<?php
class Activity
{
	var $id = null;
	var $athlete_id = null;
	var $name = null;
	var $distance = null;
	var $moving_time = null;
	var $elapsed_time = null;
	var $total_elevation_gain = null;
	var $start_date = null;
	var $start_date_local = null;
	var $commute = null;
	var $average_speed = null;
	var $max_speed = null;

	function __construct($object){
		if(isset($object["id"])){
			$this->id = $object["id"];
		}
		if(isset($object["athlete"]["id"])){
			$this->athlete_id = $object["athlete"]["id"];
		}
		if(isset($object["name"])){
			$this->name = $object["name"];
		}
		if(isset($object["distance"])){
			$this->distance = $object["distance"];
		}
		if(isset($object["moving_time"])){
			$this->moving_time = $object["moving_time"];
		}
		if(isset($object["elapsed_time"])){
			$this->elapsed_time = $object["elapsed_time"];
		}
		if(isset($object["total_elevation_gain"])){
			$this->total_elevation_gain = $object["total_elevation_gain"];
		}
		if(isset($object["start_date"])){
			$this->start_date = strtotime($object["start_date"]);
		}
		if(isset($object["start_date_local"])){
			$this->start_date_local = strtotime($object["start_date_local"]);
		}
		if(isset($object["commute"])){
			$this->commute = $object["commute"];
		}
		if(isset($object["average_speed"])){
			$this->average_speed = $object["average_speed"];
		}
		if(isset($object["max_speed"])){
			$this->max_speed = $object["max_speed"];
		}
	}
	/**
	*	Check whether this activity is valid
	*/
	function is_valid(){
		if(is_null($this->id)||is_null($this->athlete_id)){
			return false;
		}
		return true;
	}

	function printme(){
		var_dump($this);
		echo "<br><hr>";
	}

	function in_database($db){
		try{
			$query = "SELECT COUNT(*) FROM activities WHERE id = ?";
			$statement = $db->prepare($query);
			$statement->execute(array($this->id));
			$count = $statement->fetch(PDO::FETCH_NUM);
			return reset($count);
		}catch(PDOException $e){
			var_dump($e);
		}
	}
	function addToDatabase($db){
		try{
			$query = "INSERT INTO activities (id, athlete_id, name, distance, moving_time, elapsed_time, total_elevation_gain, start_date, start_date_local, commute, average_speed, max_speed, added) VALUES (?,?,?,?,?,?,?,FROM_UNIXTIME(?),FROM_UNIXTIME(?),?,?,?,NOW())";
			$statement = $db->prepare($query);
			$statement->execute(array($this->id, 
				$this->athlete_id, $this->name, $this->distance, 
				$this->moving_time, $this->elapsed_time, 
				$this->total_elevation_gain, $this->start_date, 
				$this->start_date_local, $this->commute, 
				$this->average_speed, $this->max_speed));
			return true;
		}catch(PDOException $e){
			var_dump($e);
		}
	}
}
?>