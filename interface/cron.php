<?php
include_once("includes.php");

include_once("Athletes.class.php");
include_once("StravaInterface.class.php");
include_once("Activity.class.php");

$a = new Athletes();

function syncAthletes($db){
	$AthleteCtn = new Athletes();
	$StravaInterface = new StravaInterface();
	$Athletes = $AthleteCtn->getSubscribedAthletes($db);
	foreach($Athletes as $athlete){
		// Foreach athlete in the list of athletes
		if(!isset($athlete["athlete_id"]) && is_null($athlete["athlete_id"])){
			echo "Not an athlete in this system.";
			echo "<br>";
			continue;
		}
		// Athlete is valid, get newest activity;
		$Activities = $StravaInterface->getAthleteActivities($athlete['access_token'], $athlete['newest']);
		foreach($Activities as $activity){
			var_dump($activity);
			$ActivityObj = new Activity($activity);
			if($ActivityObj->in_database($db)){
				echo "already in database";
			}else{
				// Not in database, now add it
				$ActivityObj->addToDatabase($db);
				// Sweet - this is a new activity, lets check if any challenges have been completed
				//checkChallenges();
			}
		}
	}
	
}

$db = createConnection();
syncAthletes($db);

?>