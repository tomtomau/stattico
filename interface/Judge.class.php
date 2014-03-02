<?php

include_once("Challenge.class.php");
include_once("DistanceChallenge.class.php");
include_once("SegmentChallenge.class.php");
include_once("SegmentEffortChallenge.class.php");
include_once("SpeedChallenge.class.php");

class Judge{
	public $Challenges = array();

	// Constructor
	function __construct(){
		array_push($this->Challenges, 
			new DistanceChallenge(), 
			new SegmentChallenge(),
			new SegmentEffortChallenge(),
			new SpeedChallenge()
			);
	}

	function doStuff(){
		var_dump($this->Challenges);
	}

	function findCompletedChallenges($ActivityJson){
		foreach($this->Challenges as $Challenge){
			var_dump($Challenge->completed($ActivityJson));
		}
	}
}

$a = new Judge();


?>