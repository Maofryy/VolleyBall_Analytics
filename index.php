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
);
$elementsReferentiel = array(
	'match' => 'code_match',
	'club' => 'name',
	'team' => 'name',
	'player' => 'licence',
	'division' => 'div_name',
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
  	if ($file == "test_EMA.json") { // just for test
		// get datas
    	$json = file_get_contents('extraction/json/' . $file, true);
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
			$players[$licence] = $teams['Players']['Team A']['Nom Prénom'][$key];
		}
		foreach ($teams['Players']['Team B']['Licence'] as $key => $licence) {
			$players[$licence] = $teams['Players']['Team A']['Nom Prénom'][$key];
		}
		foreach ($players as $licence => $fullName) {
			if (isset($referentiel['player'][$licence])) {
				continue;
			}
			$names = explode(' ', $fullName);
			$firstName = array_pop($names);
			$lastName = implode(' ', $names);
			$sql = sprintf("INSERT INTO sport_analytics.player (licence, first_name, last_name)
			VALUES('%s', '%s', '%s')", $licence, $firstName, $lastName);
			$stmt = $database->prepare($sql);
			$stmt->execute();
			$referentiel['players'][$licence] = $licence;
		}

		/*$sql = sprintf("INSERT INTO sport_analytics.`match`
		(team_home_id, team_out_id, div_code, div_name, div_pool, match_number, match_day, city, gym, category, ligue, date_match, created_at, updated_at)
		VALUES('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s');
		", $title['']);*/

		var_dump($title);die;
		var_dump($referentiel);die;
  	}
}
?>