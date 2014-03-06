<?php

class ClubActivity extends Activity{
	
	var $club_id = null;

	function __construct($object, $clubId){
		parent::__construct($object);
		if(isset($club_id)){
			$this->club_id = $club_id;
		}
	}

	function is_valid(){
		if(parent::is_valid() && !is_null($this->club_id)){
			return true;
		}else{
			return false;
		}
	}

	function in_database($db, $club_id){
		try{
			$query = "SELECT COUNT(*) FROM club_activities WHERE activity_id = ? AND club_id = ?";
			$statement = $db->prepare($query);
			$statement->execute(array($this->id, $club_id));
			$count = $statement->fetch(PDO::FETCH_NUM);
			return reset($count);
		}catch(PDOException $e){
			var_dump($e);
		
		}	
	}

	function addToDatabase($db){
		try{
			$query = "INSERT INTO club_activities (activity_id, club_id, athlete_id, distance, datetime) VALUES (?, ?, ?, ?, ?)";
			$statement = $db->prepare($query);
			$statement->execute(array($this->id, $this->club_id, $this->athlete_id, $this->distance, $this->datetime));
			return true;
		}catch(PDOException $e){
			var_dump($e);
		}
	}

}

?>