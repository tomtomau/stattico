<?php
class Activity
{
	var $id = null;
	var $distance = null;
	var $club_id = null;
	var $athlete_id = null;
	var $datetime = null;

	function __construct($object, $club_id){
		if(isset($object->{"id"})){
			$this->id = $object->{"id"};
		}
		if(isset($object->{"distance"})){
			$this->distance = $object->{"distance"};
		}
		if(isset($object->{"athlete"}->{"id"})){
			$this->athlete_id = $object->{"athlete"}->{"id"};
		}
		if(isset($object->{"start_date"})){
			$date_string = $object->{"start_date"};
			$epoch = strtotime($date_string);
			$dt = new DateTime("@$epoch");
			$this->datetime = $dt->format("Y-m-d H:i:s");
		}
		if(isset($club_id)){
			$this->club_id = $club_id;
		}
	}
	/**
	*	Check whether this activity is valid
	*/
	function is_valid(){
		if(is_null($this->id)||is_null($this->distance)||is_null($this->club_id)||is_null($this->athlete_id)||is_null($this->datetime)){
			return false;
		}
		return true;
	}

	function printme(){
		echo($this->id . " - " . $this->distance." at ". $this->datetime . "<br>");
	}

	function in_database($db, $club_id){
		try{
			$query = "SELECT COUNT(*) FROM activities WHERE activity_id = ? AND club_id = ?";
			$statement = $db->prepare($query);
			$statement->execute(array($this->id, $club_id));
			$count = $statement->fetch(PDO::FETCH_NUM);
			return $count[0];
		}catch(PDOException $e){
			var_dump($e);
		}
	}
	function addToDatabase($db){
		try{
			$query = "INSERT INTO activities (activity_id, club_id, athlete_id, distance, datetime) VALUES (?, ?, ?, ?, ?)";
			$statement = $db->prepare($query);
			$statement->execute(array($this->id, $this->club_id, $this->athlete_id, $this->distance, $this->datetime));
			return true;
		}catch(PDOException $e){
			var_dump($e);
		}
	}
}
?>