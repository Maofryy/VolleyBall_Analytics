<?php
ini_set('max_execution_time', 0);
ini_set('display_errors', 1); 
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); 

// Connexion to database
$servername = "localhost";
$username = "root";
$password = "";

try {
  	$database = new PDO("mysql:host=$servername;dbname=sport_analytics", $username, $password);
  	// set the PDO error mode to exception
  	$database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	 
} catch(PDOException $e) {
  	echo "Connection failed: " . $e->getMessage();
}

resetDatabase($database);

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
	'match' => 'match_number',
	'club' => 'name',
	'team' => 'name',
	'player' => 'licence',
	'division' => 'div_name',
	'season' => 'year',
	'ligue' => 'name',
);

$setRomain = array(
	'I' => 1,
	'II' => 2,
	'III' => 3,
	'IV' => 4,
	'V' => 5,
	'VI' => 6,
);
$errors_match = array();
$countFile = 0;
$startInsert = new DateTime();

foreach ($elementsReferentiel as $table => $column) {
	$sql = sprintf("SELECT * FROM `%s`", $table);
	$result = $database->query($sql);
	foreach ($result as $value) {
		if ($table == 'player') // exception table player because primary key is licence
        { 
			$referentiel[$table][$value[$column]] = $value['licence'];
		}
        else if ($table == 'team') // exception table team because primary is concat name|gender|category
        {
            $concat_name = $value['name'] . '|' . $value['gender'] . '|' . $value['category'];
            $referentiel[$table][$value[$column]] = $concat_name;
        }
        else
        {
			$referentiel[$table][$value[$column]] = $value[$table . '_id'];
		}
	}
}

/*initHistoClub();
die;*/

//echo '<pre>' . var_export($referentiel, true) . '</pre>'; die;

$dir1 = array_slice(scandir('parsed_matches'), 2);
foreach($dir1 as $dir) {
	$folders = array_slice(scandir('parsed_matches/'. $dir), 2);
	foreach($folders as $folder) {
		$files = array_slice(scandir('parsed_matches/'. $dir . '/' . $folder), 2);
		foreach($files as $file) {
			//if ($file != "2MA023.json")continue;
			if (is_dir('parsed_matches/'. $dir . '/' . $folder . '/' . $file)) {
				$files2 = array_slice(scandir('parsed_matches/'. $dir . '/' . $folder . '/' . $file), 2);
				foreach ($files2 as $file2) {
					$json = file_get_contents('parsed_matches/'. $dir . '/' . $folder . '/' . $file . '/' . $file2, true);
					addMatch($json, $files2, $errors_match);
				}
			} else {
				$json = file_get_contents('parsed_matches/'. $dir . '/' . $folder . '/' . $file, true);
				addMatch($json, $file, $errors_match);
			}
		}
	}
}


function addMatch($json, $file, &$errors_match) {
	try {
		global $database, $referentiel, $setRomain, $countFile;
		$datas = json_decode(Utf8_ansi($json), true);
			
		$title = $datas['Match']['Title'];
		$sets = $datas['Match']['Sets'];
		$teams = $datas['Match']['Teams'];
		$results = $datas['Match']['Results'];
		$referees = $datas['Match']['Referees'];
		$penalties = $datas['Match']['Penalties'];
        $explode_category = explode("|", $title['category']);
        if (empty($explode_category))
        {
            throw new Exception(sprintf("Erreur de la donnée catégorie lors du match : %s", $file));
        }
        $category = $explode_category[0];
        $gender = $explode_category[1];

		if ($referentiel['match'] == $title['match_number']) {
			return false; // match already insert
		}

		$teamAName = trim($firstName = str_replace("'", "''", $teams['Name']['Team 1']));
		$teamBName = trim($firstName = str_replace("'", "''", $teams['Name']['Team 2']));
        $concatTeamAName = $teamAName . '|' . $gender . '|' . $category;
        $concatTeamBName = $teamBName . '|' . $gender . '|' . $category;

		$inversed = (trim($sets['Teams']['Set 1']['Name']['Team 1']) != trim(substr($teams['Name']['Team 1'], 0, 22)));
		$inversed5 = false;
		if (!empty($sets['Teams']['Set 5']))
		{
			$inversed5 = (trim($sets['Teams']['Set 5']['Name']['Team 1']) != trim(substr($teams['Name']['Team 1'], 0, 19)));
		}
		
		$database->beginTransaction();
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
		/*if (!isset($referentiel['club'][$teamAName])) {
			$sql = sprintf("INSERT INTO sport_analytics.club(name)	VALUES('%s')", $teamAName);
			$stmt = $database->prepare($sql);
			$stmt->execute();
			$referentiel['club'][$teamAName] = $database->lastInsertId();
		}
		if (!isset($referentiel['club'][$teamBName])) {
			$sql = sprintf('INSERT INTO sport_analytics.club(name)	VALUES("%s")', $teamBName);
			$stmt = $database->prepare($sql);
			$stmt->execute();
			$referentiel['club'][$teamBName] = $database->lastInsertId();
		}*/

		// team
		if (!isset($referentiel['team'][$concatTeamAName]))
        {
            $clubId = getClubId($teamAName);
			$sql = sprintf("INSERT INTO sport_analytics.team (club_id, name, gender, category)
			VALUES('%s', '%s', '%s', '%s')", $clubId, $teamAName, $gender, $category);
			$stmt = $database->prepare($sql);
			$stmt->execute();
			$referentiel['team'][$concatTeamAName] = $database->lastInsertId();
		}
		if (!isset($referentiel['team'][$concatTeamBName]))
        {
            $clubId = getClubId($teamBName);
			$sql = sprintf("INSERT INTO sport_analytics.team (club_id, name, gender, category)
			VALUES('%s', '%s', '%s', '%s')", $clubId, $teamBName, $gender, $category);
			$stmt = $database->prepare($sql);
			$stmt->execute();
			$referentiel['team'][$concatTeamBName] = $database->lastInsertId();
		}

		// set table players
		$players = array();
		$playersNumber = array();
		foreach ($teams['Players']['Team 1']['Licence'] as $key => $licence) {
			if (!intval($licence)) {
				$licence = substr($licence, 1, strlen($licence) -1);
			}
			if (empty($licence)) {
				$licence = 0;
			}
			$players[$licence] = array(
				"name" => $teams['Players']['Team 1']['Nom Prénom'][$key],
				"team" => $teamAName,
				"number" => $teams['Players']['Team 1']['N°'][$key],
			);
			$playersNumber[($inversed) ? 2 : 1][$teams['Players']['Team 1']['N°'][$key]] = $licence;
		}
		foreach ($teams['Players']['Team 2']['Licence'] as $key => $licence) {
			if (!intval($licence)) {
				$licence = substr($licence, 1, strlen($licence) -1);
			}
			if (empty($licence)) {
				$licence = 0;
			}
			$players[$licence] = array(
				"name" => $teams['Players']['Team 2']['Nom Prénom'][$key],
				"team" => $teamBName,
				"number" => $teams['Players']['Team 2']['N°'][$key],
			);
			$playersNumber[($inversed) ? 1 : 2][$teams['Players']['Team 2']['N°'][$key]] = $licence;
		}
		
		$dateMatch = DateTime::createFromFormat('Y-m-d H:i:s', $title['date']);
		$midSeason = DateTime::createFromFormat('Y-m-d', $dateMatch->format('Y') . '-08-15'); // 15 aout
		$year = intval($dateMatch->format('Y'));

		if ($dateMatch > $midSeason) {
			$season = strval($year) . '/' . strval($year + 1); 
		} else {
			$season = strval($year - 1) . '/' . strval($year); 
		}
		$seasonId = $referentiel['season'][$season];
		
		if (substr(str_replace("''", "'",$teamAName), 0, 10) == substr($results['winner'], 0, 10))
		{
			$winnerName = $teamAName . '|' . $gender . '|' . $category;;
		}
		else if(substr(str_replace("''", "'",$teamBName), 0, 10) == substr($results['winner'], 0, 10))
		{
			$winnerName =  $teamBName . '|' . $gender . '|' . $category;;
		}
		else
		{
			/*var_dump(substr($teamAName, 0, 10));
			var_dump(substr($teamBName, 0, 10));
			var_dump(substr($results['winner'], 0, 10));
			var_dump($file);
			var_dump($json);
			var_dump($winnerName);
			die;*/
			throw new Exception(sprintf("Erreur lors de la récupération du vainqueur du match, fichier : %s", $file));
		}

		// match
		$title['city'] = str_replace("'", "''", $title['city']);
		$title['city'] = str_replace("\\", "", $title['city']);
		$title['gym'] = str_replace("'", "''", $title['gym']);
		$sql = sprintf("INSERT INTO sport_analytics.`match`
		(team_home_id, team_out_id, div_code, div_pool, match_number, match_day, city, gym, category, ligue, winner_team_id, score, date_match, created_at)
		VALUES('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s', %s);
		", $referentiel['team'][$concatTeamAName], $referentiel['team'][$concatTeamBName], $title['div_code'], $title['div_pool'], $title['match_number'], $title['match_day'], $title['city'], $title['gym'], $title['category'], $title['ligue'], $referentiel['team'][$winnerName], $results['score'], $title['date'], 'NOW()');
		$stmt = $database->prepare($sql);
		$stmt->execute();
		$referentiel['match'][$title['match_number']] = $database->lastInsertId();
		$matchId = $referentiel['match'][$title['match_number']];

		// player
		foreach ($players as $licence => $player) {
			if (!isset($referentiel['player'][$licence])) {
				$names = explode(' ', $player['name']);
				$firstName = array_pop($names);
				$firstName = str_replace("'", "''", $firstName); // pour les ' dans les nom
				$lastName = implode(' ', $names);
				$lastName = str_replace("'", "''", $lastName); // pour les ' dans les nom
				$sql = sprintf("INSERT INTO sport_analytics.player (licence, first_name, last_name)
				VALUES('%s', '%s', '%s')", $licence, $firstName, $lastName);
				if ($licence) {
					$stmt = $database->prepare($sql);
					$stmt->execute();
				}
				$referentiel['player'][$licence] = $licence;
			}
			
			// team_players
			$sql = sprintf("INSERT INTO sport_analytics.team_player (match_id, team_id, player_id, season_id, `number`)
			VALUES('%s', '%s', '%s', '%s', '%s');", $matchId, $referentiel['team'][$player['team'] . '|' . $gender . '|' . $category], $licence, $seasonId, $player['number']);
			$stmt = $database->prepare($sql);
			$stmt->execute();		
		}

		// match_set_timeout
		for ($i = 1; $i < 3; $i++) {
			$timeoutsN = ($i == 1) ? 'Timeouts 1': 'Timeouts 2';
			$teamN = ($i == 1) ? $referentiel['team'][$concatTeamAName] : $referentiel['team'][$concatTeamBName];
			if (!isset($sets[$timeoutsN])) {
				var_dump($sets);die;
			}
			foreach ($sets[$timeoutsN] as $setString => $timeout) {
				$set = substr($setString, -1);
				if (isset($timeout['T'])) {
					foreach ($timeout['T'] as $score) {
						$sql = sprintf("INSERT INTO sport_analytics.match_set_timeout (match_id, `set`, score, team_id)	VALUES('%s', '%s', '%s', '%s');", $matchId, $set, $score, $teamN);
						$stmt = $database->prepare($sql);
						$stmt->execute();
					}
				}
			}
		}
		
		// match_set_position
		for ($i = 1; $i < 3; $i++) {
			if ($inversed) {
				$SubstitutionsN = ($i == 1) ? 'Substitutions 2': 'Substitutions 1';
			} else {
				$SubstitutionsN = ($i == 1) ? 'Substitutions 1': 'Substitutions 2';
			}
			$teamN = ($i == 1) ? $referentiel['team'][$concatTeamAName] : $referentiel['team'][$concatTeamBName];
			foreach ($sets[$SubstitutionsN] as $setString => $sub) {
				$set = substr($setString, -1);
				if (empty($sub)) continue;
				$positions = array();
				foreach ($sub as $position => $element) {
					$number = $element[0];
					$cpt = $i;
					if ($set != 5 ) {
						if ($inversed) {
							if ($cpt == 1) {
								$cpt = 2;
							} else {
								$cpt = 1;
							}
						}
					} else {
						if ($inversed5) {
							if($cpt == 1) {
								$cpt = 2;
							} else {
								$cpt = 1;
							}
						}
					}
					if (!isset($playersNumber[$cpt][$number])) {
						throw new Exception(sprintf("Aucun numéro %s dans l'équipe %s dans le fichier %s", $number, ($i == 1) ? $teamAName : $teamBName, $file));
					}
					
					$positions[$setRomain[$position]] = $playersNumber[$cpt][$number];
				}
				$sql = sprintf("INSERT INTO sport_analytics.match_set_position(match_id, team_id, `set`, position_1, position_2, position_3, position_4, position_5, position_6)
					VALUES('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s');", $matchId, $teamN, $set, $positions[1], $positions[2], $positions[3], $positions[4], $positions[5], $positions[6]);
				$stmt = $database->prepare($sql);
				$stmt->execute();
			}
		}

		// match_set_substitution
		for ($i = 1; $i < 3; $i++) {
			if ($inversed) {
				$SubstitutionsN = ($i == 1) ? 'Substitutions 2': 'Substitutions 1';
			} else {
				$SubstitutionsN = ($i == 1) ? 'Substitutions 1': 'Substitutions 2';
			}
			if ($inversed) {
				$teamN = ($i == 1) ? $referentiel['team'][$concatTeamAName] : $referentiel['team'][$concatTeamBName];
			} else {
				$teamN = ($i == 1) ? $referentiel['team'][$concatTeamBName] : $referentiel['team'][$concatTeamAName];
			}
			
			foreach ($sets[$SubstitutionsN] as $setString => $positions) {
				$set = substr($setString, -1);
				if (empty($positions)) continue;
				foreach ($positions as $position) {
					$cpt = $i;
					if ($set != 5 ) {
						if ($inversed) {
							if($cpt == 1) {
								$cpt = 2;
							} else {
								$cpt = 1;
							}
						}
					} else {
						if ($inversed5) {
							if($cpt == 1) {
								$cpt = 2;
							} else {
								$cpt = 1;
							}
						}
					}
					if (!empty($position[2])) {
						if (!isset($playersNumber[$cpt][$position[1]]) || !isset($playersNumber[$cpt][$position[0]])) {
							throw new Exception("Erreur de numéro");
						}
						$sql = sprintf("INSERT INTO sport_analytics.match_set_substitution(match_id, `set`, licence_in, licence_out, score, team_id)
						VALUES('%s', '%s', '%s', '%s', '%s', '%s');", $matchId, $set, $playersNumber[$cpt][$position[1]], $playersNumber[$cpt][$position[0]], $position[2], $teamN);
						$stmt = $database->prepare($sql);
						$stmt->execute();
					}
					if (isset($position[3]) && !empty($position[3])) {
						$sql = sprintf("INSERT INTO sport_analytics.match_set_substitution(match_id, `set`, licence_in, licence_out, score, team_id)
						VALUES('%s', '%s', '%s', '%s', '%s', '%s');", $matchId, $set, $playersNumber[$cpt][$position[0]], $playersNumber[$cpt][$position[1]], $position[3], $teamN);
						$stmt = $database->prepare($sql);
						$stmt->execute();
					} 
				}
			}
		}

		// match_set_rotation
		for ($i = 1; $i < 3; $i++) {
			$ServesN = ($i == 1) ? 'Serves 1': 'Serves 2';
			$teamN = ($i == 1) ? $referentiel['team'][$concatTeamAName] : $referentiel['team'][$concatTeamBName];
			foreach ($sets[$ServesN] as $setString => $positions) {
				$set = substr($setString, -1);
				if (empty($positions)) continue;
				foreach ($positions as $position) {
					foreach ($position as $element) {
						if ($element === null || $element == 'X' || strlen($element) > 5) continue;
						$sql = sprintf("INSERT INTO sport_analytics.match_set_rotation (match_id, `set`, `point`, team_id)
							VALUES('%s', '%s', '%s', '%s');", $matchId, $set, $element, $teamN);
						$stmt = $database->prepare($sql);
						$stmt->execute();
					}
				}	
			}
		}

		// match_set_detais
		foreach ($sets['Time'] as $setString => $element) {
			$set = substr($setString, -1);
			if (!isset($element['Time']['Start'])) continue;					
			$sql = sprintf("INSERT INTO sport_analytics.match_set_details (match_id, `set`, date_start, date_end)
			VALUES('%s', '%s', '%s', '%s');", $matchId, $set, $element['Time']['Start'], $element['Time']['End']);
			$stmt = $database->prepare($sql);
			$stmt->execute();
		}
		
		// Voir la table other_player_function
		// Positibilité d'ajouter une colonne clé pour modifier automatiquement
		$functions = array(
			"AR1" => "1er",
			"AR2" => "2ème",
			"MAR" => "Marqueur",
			"MAA" => "Marq.Ass",
			"RS" => "R.Salle",
		);
		// match_other_player Referees 
		foreach ($functions as $code => $function) {
			if (empty($referees['Licence'][$function]) || $referees['Licence'][$function] == 'null') continue;
			$sql = sprintf("INSERT INTO sport_analytics.match_other_player (match_id, licence, function_id)	VALUES('%s', '%s', '%s');", $matchId, $referees['Licence'][$function], $code);
			$stmt = $database->prepare($sql);
			$stmt->execute();
			if (!isset($referentiel['player'][$licence])) {
				$names = explode(' ', $referees['NOM Prénom'][$function]);
				$firstName = array_pop($names);
				$lastName = implode(' ', $names);
				$sql = sprintf("INSERT INTO sport_analytics.player (licence, first_name, last_name)
				VALUES('%s', '%s', '%s')", $referees['Licence'][$function], $firstName, $lastName);
				$stmt = $database->prepare($sql);
				$stmt->execute();
				$referentiel['players'][$$referees['Licence'][$function]] = $$referees['Licence'][$function];
			}
		}
		

		// match_other_player Liberos
		for ($i = 1; $i < 3; $i++) {
			$teamN = ($i == 1) ? 'Team 1' : 'Team 2';
			foreach ($teams['Liberos'][$teamN]['Licence'] as $licence) {
				$sql = sprintf("INSERT INTO sport_analytics.match_other_player (match_id, licence, function_id)	VALUES('%s', '%s', '%s');", $matchId, $licence, 'LIB');
				$stmt = $database->prepare($sql);
				$stmt->execute();
			}
		}

		// match_other_player Officials
		for ($i = 1; $i < 3; $i++) {
			$teamN = ($i == 1) ? 'Team 1' : 'Team 2';
			$teamNId = ($i == 1) ? $referentiel['team'][$concatTeamAName] : $referentiel['team'][$concatTeamBName];
			$nb = count($teams['Officials'][$teamN]['N°']);
			for ($cpt = 0; $cpt < $nb; $cpt++) {
				$licence = $teams['Officials'][$teamN]['Licence'][$cpt];
				$function = $teams['Officials'][$teamN]['N°'][$cpt];
				$sql = sprintf("INSERT INTO sport_analytics.match_other_player (match_id, licence, function_id)	VALUES('%s', '%s', '%s');", $matchId, $licence, $function);
				$stmt = $database->prepare($sql);
				$stmt->execute();
				if (!isset($referentiel['player'][$licence])) {
					// player
					$names = explode(' ', $teams['Officials'][$teamN]['Nom Prénom'][$cpt]);
					$firstName = array_pop($names);
					$firstName = str_replace("'", "''", $firstName); // pour les ' dans les nom
					$lastName = implode(' ', $names);
					$lastName = str_replace("'", "''", $lastName); // pour les ' dans les nom
					$sql = sprintf("INSERT INTO sport_analytics.player (licence, first_name, last_name)
					VALUES('%s', '%s', '%s')", $licence, $firstName, $lastName);
					
					$stmt = $database->prepare($sql);
					$stmt->execute();
					$referentiel['player'][$licence] = $licence;

					// team_players
					$sql = sprintf("INSERT INTO sport_analytics.team_player (match_id, team_id, player_id, season_id, function_id)
					VALUES('%s', '%s', '%s', '%s', '%s');", $matchId, $teamNId, $licence, $seasonId, $function);
					$stmt = $database->prepare($sql);
					$stmt->execute();
				} else {
					// update team_player function
					$sql = sprintf("UPDATE sport_analytics.team_player SET function_id = '%s' 
					WHERE team_id = '%s' AND player_id = '%s' AND season_id = '%s';", $function, $teamNId, $licence, $seasonId);
					$stmt = $database->prepare($sql);
					$stmt->execute();
				}
			}			
		}
		$countFile++;
		$database->commit();
	} catch (Exception $e) {
		array_push($errors_match, $file);
		$database->rollBack();
		echo "Failed: " . $e->getMessage() . " </br>";
  	}
}

$endInsert = new Datetime();
$interval = $startInsert->diff($endInsert);
$content = file_get_contents("error_files.csv");
echo $countFile . '  matchs ajoutés en ' . $interval->format('%i minutes %s secondes') . '</br>';
echo count($errors_match) . ' erreurs : </br>';
foreach ($errors_match as $error) {
	echo $error . '</br>';
	if (strpos($content, $error) === false) {
		$txt = str_replace('.json', '.pdf', $error) . ',';
		file_put_contents("error_files.csv", $txt, FILE_APPEND);
	}
};

function resetDatabase($database) {
	// just tables that are inserted automatically
	$tables = array(
		'match',
		'team',
		'player',
		'team_player',
		'division',
		'ligue',
		'match_set_position',
		'match_set_timeout',
		'match_set_substitution',
		'match_set_rotation',
		'match_set_details',
		'match_other_player',
		//'club', //On enlève les clubs car on les insert avant grâce au scrap de l'historique.
		//'club_licence_histo'
	);

	foreach ($tables as $table) {
		$sql = sprintf('TRUNCATE TABLE sport_analytics.`%s`;', $table);
		$stmt = $database->prepare($sql);
		$stmt->execute();
	}
	echo 'Les tables ont correctement été réinitialisés.<br>';
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

function initHistoClub()
{
	global $database, $referentiel;
	//$json = file_get_contents('./histo_club_2017_2018.json'); //9
	//$json = file_get_contents('./histo_club_2018_2019.json'); //10
	//$json = file_get_contents('./histo_club_2019_2020.json'); // 11
	//$json = file_get_contents('./histo_club_2020_2021.json'); // 12
	$json = file_get_contents('./histo_club_2021_2022.json'); // saison 13
	$datas = json_decode($json);
	foreach($datas as $data)
	{
		if(!isset($referentiel['club'][$data->name]))
		{
			// insert club
			$data->name = str_replace("'", "''", $data->name);
			$data->comite = str_replace("'", "''", $data->comite);
			$data->ligue = str_replace("'", "''", $data->ligue);
			$sql = sprintf("INSERT INTO sport_analytics.club(name, comite, ligue)	VALUES('%s','%s','%s')", $data->name, $data->comite, $data->ligue);
			$stmt = $database->prepare($sql);
			$stmt->execute();
			$referentiel['club'][$data->name] = $database->lastInsertId();
		}
		

		// insert histo
		$sql = sprintf("INSERT INTO sport_analytics.club_licence_histo(club_id, saison_id, total_m, total_f, jeune_m, jeune_f) VALUES('%s',13,'%s','%s','%s','%s')",
			$referentiel['club'][$data->name], $data->total_m, $data->total_f, $data->jeune_m, $data->jeune_f
		);
		$stmt = $database->prepare($sql);
		$stmt->execute();
	}
}

/**
 * Si la team n'est pas déjà enregistré, on cherche le matching
 */
function getClubId($teamName)
{
	global $database, $referentiel;

    $result = [];
    $clubs = array_keys($referentiel['club']);
	$shortest = 999;
	
    foreach ($clubs as $club)
    {    
        // On enlève les mots Volley ball car ça ruine le matching
        $temp_club = $club;
        $temp_team = $teamName;
        /*$temp_club = str_replace("Volley-ball", "", $club);
        $temp_club = str_replace("Volley", "", $club);
        $temp_team = str_replace("Volley-ball", "", $team['name']);
        $temp_team = str_replace("Volley", "", $team['name']);*/

        // calcule la distance avec le mot mis en entrée et le mot courant
        $lev = levenshtein($temp_club, $temp_team);

        // cherche une correspondance exacte
        if ($lev == 0) {
            // le mot le plus près est celui-ci (correspondance exacte)
            $closest = $club;
            $shortest = 0;
            break;
        }

        // Si la distance est plus petite que la prochaine distance trouvée
        // OU, si le prochain mot le plus près n'a pas encore été trouvé
        if ($lev <= $shortest) {
            // définition du mot le plus près ainsi que la distance
            $closest  = $club;
            $shortest = $lev;
        }
    }
    if ($shortest >= 2) // on met un debug large à 2 au début
    {
        echo sprintf("Attention l'équipe %s a été rattaché au club %s", $teamName, $closest);
		echo "<br>";
    }	
    return $referentiel['club'][$closest];
}
?>