<?php

class Challenge{

	/**
	*	Function to verify whether a particular activity (represented by a JSON associated array)
	*		has completed the challenge.
	*	
	**/
	function completed($ActivityJson){
		return false;
	}

	/**
	*	Function to retrieve all challenges for the athlete
	*/
	function getAthleteChallenges($AthleteId){
		return array();
	}

}



?>