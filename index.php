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


$files = scandir('extraction/json');
foreach($files as $file) {
  	if ($file == "test_EMA.json") { // just for test
    	$json = file_get_contents('extraction/json/' . $file, true);
    	$datas = json_decode($json);
    	$referentiel = array();
		$elementsReferentiel = array(
			'match' => 'code_match',
			'club' => 'name',
			'team' => 'name',
		);

		foreach ($elementsReferentiel as $table => $column) {
			$sql = sprintf("SELECT * FROM `%s`", $table);
			$result = $database->query($sql);
			foreach ($result as $value) {
				$referentiel[$table][] = $value[$column];
			}
		}
		var_dump($referentiel);die;
  	}
}
?>