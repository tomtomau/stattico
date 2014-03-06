<?php
include_once("includes.php");
secure_session_start();
class Athletes{
	function verifyAthlete($db, $athlete_id, $access_token){
		try{
			$query = "SELECT * FROM athletes WHERE athlete_id = ? AND access_token = ?";
			$statement = $db->prepare($query);
			$statement->execute(array($athlete_id, $access_token));
			$results = $statement->fetchAll(PDO::FETCH_ASSOC);
			return reset($results);
		}catch(PDOException $e){
			var_dump($e);
		}	
	}
	function checkSessionAuth($db){
		// Return false if the session variables are shit
		if(!isset($_SESSION['athlete_id']) || !isset($_SESSION["access_token"])){
			return false;
		}
		$athlete_id = $_SESSION['athlete_id'].".";
		$access_token = ltrim(base64_decode($_SESSION["access_token"]), $athlete_id);
		// Verify and see if access token & athlete_id match
		if($Athlete = $this->verifyAthlete($db, $athlete_id, $access_token)){
			return true;
		}else{
			return false;
		}
	}
	function getAthlete($db, $athlete_id){
		try{
			$query = "SELECT * FROM athletes WHERE athlete_id = ?";
			$statement = $db->prepare($query);
			$statement->execute(array($athlete_id));
			$results = $statement->fetchAll(PDO::FETCH_ASSOC);
			return reset($results);
		}catch(PDOException $e){
			var_dump($e);
		}	
	}
	function getSubscribedAthletes($db){
		try{
			$query = "SELECT athletes.*, IFNULL(UNIX_TIMESTAMP(max(activities.start_date)),0) as newest FROM athletes 
			LEFT JOIN activities on activities.athlete_id = athletes.athlete_id
			GROUP BY athletes.athlete_id";
			$statement = $db->prepare($query);
			$statement->execute();
			$results = $statement->fetchAll(PDO::FETCH_ASSOC);
			return $results;
		}catch(PDOException $e){
			var_dump($e);
		}	
	}

}

$db = createConnection($db_params);
$a = new Athletes();
var_dump($a->checkSessionAuth($db));

?>