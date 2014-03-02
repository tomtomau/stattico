<?php
class Facts
{
	var $club_id = null;
	var $before = null;
	var $after = null;
	var $total_distance = null;
	var $total_athletes = null;
	var $total_rides = null;
	var $average_distance = null;
	var $average_daily_rides = null;
	var $average_distance_athletes = null;


function updateFacts($db, $before, $after, $club_id){
		$this->club_id = $club_id;
		$this->before = $before;
		$this->after = $after;

		// Generate facts
		$this->generateFacts($db);
		try{
			// Put facts into the database
			$query = "REPLACE INTO facts SET date = ?, club_id = ?, total_distance = ?, 
			total_rides = ?, total_athletes = ?, average_distance = ?, 
			average_daily_rides = ?, average_distance_athletes = ?";
			$statement = $db->prepare($query);
			$statement->execute(array($this->before, $this->club_id, 
			$this->total_distance, $this->total_rides, $this->total_athletes, 
			$this->average_distance, $this->average_daily_rides, $this->average_distance_athletes));
			echo("Updated table for $club_id!");
		}catch(PDOException $e){
			var_dump($e);
		}
	}
	/**
	*	Generate the data to then go into a facts table
	*/
	function generateFacts($db){
		$this->calculateTotals($db);
		$this->calculateAggregates($db);
		$this->calculateDerived();
	}

	function calculateTotals($db){
		try{
			$query = "SELECT SUM(distance) as total_distance, 
						COUNT(*) as total_rides, 
						AVG(distance) as average_distance 
						FROM activities WHERE club_id = ? AND
						datetime > ? AND datetime < ?
						GROUP BY club_id";
			$statement = $db->prepare($query);
			$statement->execute(array($this->club_id, $this->after, $this->before));
			$query_results = $statement->fetch(PDO::FETCH_ASSOC);
			$this->total_distance = $query_results{"total_distance"};
			$this->total_rides = $query_results{"total_rides"};
			$this->average_distance = $query_results{"average_distance"};
		}catch(PDOException $e){
			var_dump($e);
		}
	}

	function calculateAggregates($db){
		try{
			// Calculate total athletes
			$query = "SELECT COUNT(DISTINCT athlete_id) as total_athletes FROM activities 
			WHERE club_id = ? AND datetime > ? AND datetime < ?";
			$statement = $db->prepare($query);
			$statement->execute(array($this->club_id, $this->after, $this->before));
			$results = $statement->fetch(PDO::FETCH_NUM);
			$this->total_athletes = $results[0];

		}catch(PDOException $e){
			var_dump($e);
		}
	}

	function calculateDerived(){
		$this->average_distance_athletes = $this->total_distance / $this->total_athletes;
		$days = 1+(strtotime($this->before) - strtotime($this->after))/60/60/24;
		$this->average_daily_rides = floor($this->total_rides / $days);
	}
}
?>