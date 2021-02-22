<?php
ini_set('display_errors', 1); 
ini_set('display_startup_errors', 1); 
error_reporting(E_ALL); 

// Connexion to database
$servername = "localhost";
$username = "root";
$password = "root";

try {
  	$database = new PDO("mysql:host=$servername;dbname=sport_analytics", $username, $password);
  	// set the PDO error mode to exception
  	$database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
  	echo "Connection failed: " . $e->getMessage();
}

//set referentiel
$referentiel = array(
	'match' => array(),
	'club' => array(),
	'team' => array(),
	'player' => array(),
	'division' => array(),
	'team_player' => array(),
	'ligue' => array(),
);
$elementsReferentiel = array(
	'match' => 'code_match',
	'club' => 'name',
	'team' => 'name',
	'player' => 'licence',
	'division' => 'div_name',
	'season' => 'year',
	'ligue' => 'name',
);

foreach ($elementsReferentiel as $table => $column) {
	$sql = sprintf("SELECT * FROM `%s`", $table);
	$result = $database->query($sql);
	foreach ($result as $value) {
		if ($table == 'player') { // exception table player because primary key is licence
			$referentiel[$table][$value[$column]] = $value['licence'];
		} else {
			$referentiel[$table][$value[$column]] = $value[$table . '_id'];
		}
	}
}

$files = scandir('extraction/json');
foreach($files as $file) {
  	if ($file == 'sample_test.json') { // just for test
		// get datas
    	$json = file_get_contents('extraction/json/' . $file, true);
		var_dump(Utf8_ansi($json));die;
    	$datas = json_decode($json, true);

		$title = $datas['Match']['Title'];
		$sets = $datas['Match']['Sets'];
		$teams = $datas['Match']['Teams'];
		$results = $datas['Match']['Results'];
		$referees = $datas['Match']['Referees'];
		$penalties = $datas['Match']['Penalties'];

		if ($referentiel['match'] == $title['match_number']) {
			continue; // match already insert
		}

		$teamAName = $teams['Name']['Team A'][0];
		$teamBName = $teams['Name']['Team B'][0];

		//division
		if (!isset($referentiel['division'][$title['div_name']])) {
			$sql = sprintf("INSERT INTO sport_analytics.division (div_name, div_code)
			VALUES('%s', '%s');", $title['div_name'], $title['div_code']);
			$stmt = $database->prepare($sql);
			$stmt->execute();
			$referentiel['division'][$title['div_name']] = $database->lastInsertId();
		}

		// ligue
		if (!isset($referentiel['ligue'][$title['ligue']])) {
			$sql = sprintf("INSERT INTO sport_analytics.ligue (name)
			VALUES('%s');", $title['ligue']);
			$stmt = $database->prepare($sql);
			$stmt->execute();
			$referentiel['division'][$title['ligue']] = $database->lastInsertId();
		}

		// club
		if (!isset($referentiel['club'][$teamAName])) {
			$sql = sprintf("INSERT INTO sport_analytics.club(name)	VALUES('%s')", $teamAName);
			$stmt = $database->prepare($sql);
			$stmt->execute();
			$referentiel['club'][$teamAName] = $database->lastInsertId();
		}
		if (!isset($referentiel['club'][$teamBName])) {
			$sql = sprintf("INSERT INTO sport_analytics.club(name)	VALUES('%s')", $teamBName);
			$stmt = $database->prepare($sql);
			$stmt->execute();
			$referentiel['club'][$teamBName] = $database->lastInsertId();
		}

		// team
		if (!isset($referentiel['team'][$teamAName])) {
			$sql = sprintf("INSERT INTO sport_analytics.team (club_id, name, gender)
			VALUES('%s', '%s', '%s')", $referentiel['club'][$teamAName], $teamAName, null);
			$stmt = $database->prepare($sql);
			$stmt->execute();
			$referentiel['team'][$teamAName] = $database->lastInsertId();
		}
		if (!isset($referentiel['team'][$teamBName])) {
			$sql = sprintf("INSERT INTO sport_analytics.team (club_id, name, gender)
			VALUES('%s', '%s', '%s')", $referentiel['club'][$teamBName], $teamBName, null);
			$stmt = $database->prepare($sql);
			$stmt->execute();
			$referentiel['team'][$teamBName] = $database->lastInsertId();
		}

		//players
		$players = array();
		foreach ($teams['Players']['Team A']['Licence'] as $key => $licence) {
			$players[$licence] = array(
				"name" => $teams['Players']['Team A']['Nom Prénom'][$key],
				"team" => $teamAName,
			);
		}
		foreach ($teams['Players']['Team B']['Licence'] as $key => $licence) {
			$players[$licence] = array(
				"name" => $teams['Players']['Team B']['Nom Prénom'][$key],
				"team" => $teamBName,
			);
		}		
		$dateMatch = DateTime::createFromFormat('y-m-d H:i:s', $title['date']);
		$midSeason = DateTime::createFromFormat('Y-m-d', $dateMatch->format('Y') . '-08-15'); // 15 aout
		$year = intval($dateMatch->format('Y'));

		if ($dateMatch > $midSeason) {
			$season = $year . '/' . $year + 1; 
		} else {
			$season = $year - 1 . '/' . $year; 
		}
		$seasonId = $referentiel['season'][$season];

		foreach ($players as $licence => $player) {
			if (isset($referentiel['player'][$licence])) {
				continue;
			}
			$names = explode(' ', $player['name']);
			$firstName = array_pop($names);
			$lastName = implode(' ', $names);
			$sql = sprintf("INSERT INTO sport_analytics.player (licence, first_name, last_name)
			VALUES('%s', '%s', '%s')", $licence, $firstName, $lastName);
			$stmt = $database->prepare($sql);
			$stmt->execute();
			$referentiel['players'][$licence] = $licence;

			// team_players			
			$sql = sprintf("INSERT INTO sport_analytics.team_player (team_id, player_id, season_id)
			VALUES('%s', '%s', '%s');", $referentiel['team'][$player['team']], $licence, $seasonId);
			$stmt = $database->prepare($sql);
			$stmt->execute();
		}

		/*$sql = sprintf("INSERT INTO sport_analytics.`match`
		(team_home_id, team_out_id, div_code, div_name, div_pool, match_number, match_day, city, gym, category, ligue, date_match, created_at, updated_at)
		VALUES('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s');
		", $title['']);*/

		var_dump('done');die;
  	}
}

function Utf8_ansi($valor='') {

    $utf8_ansi2 = array(
    "\u00c0" =>"À",
    "\u00c1" =>"Á",
    "\u00c2" =>"Â",
    "\u00c3" =>"Ã",
    "\u00c4" =>"Ä",
    "\u00c5" =>"Å",
    "\u00c6" =>"Æ",
    "\u00c7" =>"Ç",
    "\u00c8" =>"È",
    "\u00c9" =>"É",
    "\u00ca" =>"Ê",
    "\u00cb" =>"Ë",
    "\u00cc" =>"Ì",
    "\u00cd" =>"Í",
    "\u00ce" =>"Î",
    "\u00cf" =>"Ï",
    "\u00d1" =>"Ñ",
    "\u00d2" =>"Ò",
    "\u00d3" =>"Ó",
    "\u00d4" =>"Ô",
    "\u00d5" =>"Õ",
    "\u00d6" =>"Ö",
    "\u00d8" =>"Ø",
    "\u00d9" =>"Ù",
    "\u00da" =>"Ú",
    "\u00db" =>"Û",
    "\u00dc" =>"Ü",
    "\u00dd" =>"Ý",
    "\u00df" =>"ß",
    "\u00e0" =>"à",
    "\u00e1" =>"á",
    "\u00e2" =>"â",
    "\u00e3" =>"ã",
    "\u00e4" =>"ä",
    "\u00e5" =>"å",
    "\u00e6" =>"æ",
    "\u00e7" =>"ç",
    "\u00e8" =>"è",
    "\u00e9" =>"é",
    "\u00ea" =>"ê",
    "\u00eb" =>"ë",
    "\u00ec" =>"ì",
    "\u00ed" =>"í",
    "\u00ee" =>"î",
    "\u00ef" =>"ï",
    "\u00f0" =>"ð",
    "\u00f1" =>"ñ",
    "\u00f2" =>"ò",
    "\u00f3" =>"ó",
    "\u00f4" =>"ô",
    "\u00f5" =>"õ",
    "\u00f6" =>"ö",
    "\u00f8" =>"ø",
    "\u00f9" =>"ù",
    "\u00fa" =>"ú",
    "\u00fb" =>"û",
    "\u00fc" =>"ü",
    "\u00fd" =>"ý",
    "\u00ff" =>"ÿ");

    return strtr($valor, $utf8_ansi2);      
}
?>