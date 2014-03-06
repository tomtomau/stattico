<?php
include_once("StravaInterface.class.php");
include_once("Activity.class.php");
include_once("Facts.class.php");
include_once("ClubActivity.class.php");



function syncActivities($club_id, $db_params, $access_token){
	$StravaInterface = new StravaInterface();
	// Work out beginning of this month
	$date_string = date("Y-m-01");
	// get epoch time
	$after = strtotime($date_string);
	// Strava API url
	$url = "https://www.strava.com/api/v3/clubs/$club_id/activities?access_token=$access_token&after=$after&per_page=200";
	$activities_json = $StravaInterface->interact($url);
	// setup database connection
	$db = createConnection($db_params);
	// count number of activities added
	$count = 0;
	// create a log of what's been added
	$log = "";
	foreach ($activities_json as $activity_json){
		$activity = new ClubActivity($activity_json, $club_id);
		// check activity is valid (proper ids, distances)
		if(!$activity->is_valid()){
			continue;
		}
		// if id already in db, pass
		if($activity->in_database($db, $club_id)){
			continue;
		}else{
			try{
				// add this activity to the activity database
				$activity->addToDatabase($db);
				// add to the log
				$log .= "New Activity <a href='http://www.strava.com/activities/".$activity->id."'>here</a> for club $club_id<br>";
				$count++;
			}catch(PDOException $e){
				$log = $e;
				break;
			}
		}
	}
	$return_array = array("count"=>$count, "log"=>$log);
	return $return_array;
}
function syncMonth($club_id, $db_params, $access_token){
	$sync_output = syncActivities($club_id, $db_params, $access_token);
	if($sync_output{"count"}){
		echo($sync_output{"log"});
		// New activities were added
		$db = createConnection($db_params);
		// Update fact tables
		$fact_obj = new Facts();

		$after = date("Y-m-01");
		$before= date("Y-m-t");
		$fact_obj->updateFacts($db, $before, $after, $club_id);
		//$fact_obj->updateFacts($db, $club_id);
		// Email Log Notifying Update
		// function notify()
	}else{
		// nothing happened
		return 0;
	}
}


function listTally($club_id, $db_params){
	try{
		$db = createConnection($db_params);
		$query = "SELECT date, total_distance, total_rides, timestamp FROM facts WHERE club_id = ?";
		$statement = $db->prepare($query);
		$statement->execute(array($club_id));
		$results = $statement->fetchAll(PDO::FETCH_ASSOC);
		return $results;
	}catch(PDOException $e){
		var_dump($e);
	}
}

function monthData($club_id, $db_params, $date){
	try{
		$db = createConnection($db_params);
		$query = "SELECT * FROM facts WHERE club_id = ? and date = ?";
		$statement = $db->prepare($query);
		$statement->execute(array($club_id, $date));
		$results = $statement->fetch(PDO::FETCH_ASSOC);
		return $results;
	}catch(PDOException $e){
		var_dump($e);
	}
}


?>