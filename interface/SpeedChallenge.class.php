<?php

class SpeedChallenge extends Challenge{

	// Constructor
	function __construct(){

	}

	/**
	*	Function to verify whether a particular activity (represented by a JSON associated array)
	*		has completed the challenge.
	*	
	**/
	function completed($ActivityJson){
		// Search for athletes challenges

		$completed_challenges = array();
		// For each challenge
			// If activity.distance > challenge.distance && activity.avg_speed > challenge.avg_speed
			// $complete_challenges[] = array(athlete_id, activity_id, challenge_id, datetime_completed)

		return (sizeof($completed_challenges) > 0 ? $completed_challenges : array());
	}
}

?>