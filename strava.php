<?php
include("includes.php");

function createConnection($db_params){
    // Connect to database
    try {
        /* 
         * Config:
         *  - persistent connection (e.g. no need to re-establish connection
         *  - use native PDO mysql prepared statements
         *  - show exception errors
         */
        $db = new PDO("mysql:host=".$db_params{"host"}.";dbname=".$db_params{"name"}.";charset=utf8", 
            $db_params{"user"}, $db_params{"pass"}, array(
                PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        return $db;
    } catch (PDOException $e) {
        echo "Error connecting to database " . $e->getMessage() . "<br />";
    }
}

class Activity
{
	public $id = null;
	public $distance = null;
	public $club_id = null;
	public $athlete_id = null;
	public $datetime = null;

	public function __construct($object, $club_id){
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
	public function is_valid(){
		if(is_null($this->id)||is_null($this->distance)||is_null($this->club_id)||is_null($this->athlete_id)||is_null($this->datetime)){
			return false;
		}
		return true;
	}

	public function printme(){
		echo($this->id . " - " . $this->distance." at ". $this->datetime . "<br>");
	}

	public function in_database($db, $club_id){
		try{
			$query = "SELECT COUNT(*) FROM activities WHERE activity_id = ? AND club_id = ?";
			$statement = $db->prepare($query);
			$statement->execute(array($this->id, $club_id));
			$count = $statement->fetch(PDO::FETCH_NUM)[0];
			return $count;
		}catch(PDOException $e){
			var_dump($e);
		}
	}
	public function addToDatabase($db){
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

class Facts
{
	public $club_id = null;
	public $before = null;
	public $after = null;
	public $total_distance = null;
	public $total_athletes = null;
	public $total_rides = null;
	public $average_distance = null;
	public $average_daily_rides = null;
	public $average_distance_athletes = null;


	public function updateFacts($db, $before, $after, $club_id){
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
	private function generateFacts($db){
		$this->calculateTotals($db);
		$this->calculateAggregates($db);
		$this->calculateDerived();
	}

	private function calculateTotals($db){
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

	private function calculateAggregates($db){
		try{
			// Calculate total athletes
			$query = "SELECT COUNT(DISTINCT athlete_id) as total_athletes FROM activities 
			WHERE club_id = ? AND datetime > ? AND datetime < ?";
			$statement = $db->prepare($query);
			$statement->execute(array($this->club_id, $this->after, $this->before));
			$this->total_athletes = $statement->fetch(PDO::FETCH_NUM)[0];

		}catch(PDOException $e){
			var_dump($e);
		}
	}

	private function calculateDerived(){
		$this->average_distance_athletes = $this->total_distance / $this->total_athletes;
		$days = 1+(strtotime($this->before) - strtotime($this->after))/60/60/24;
		$this->average_daily_rides = floor($this->total_rides / $days);
	}
}


/**
*	Interacts with a pin payments URL and returns an array
*		of information, decoded from the JSON return
*		@param string page
*			Page name (customers, charges, etc)
*		@param boolean post
*			Optional boolean flag to specify if the interaction
*			is a POST method as opposed to GET.
*				(Default false, set to GET)
*		@param array params
*			Optional associative array with the POST parameters
*			for the request
*				(Default NULL)
*		@return
*			Returns json_decode array of JSON reply from Pin
*/
function interact($url){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	$result = curl_exec($ch);
    curl_close($ch);
    return json_decode($result);
}

function syncActivities($club_id, $db_params, $access_token){
	// Work out beginning of this month
	$date_string = date("Y-m-01");
	// get epoch time
	$after = strtotime($date_string);
	// Strava API url
	$url = "https://www.strava.com/api/v3/clubs/$club_id/activities?access_token=$access_token&after=$after&per_page=200";
	$activities_json = interact($url);

	// setup database connection
	$db = createConnection($db_params);
	// count number of activities added
	$count = 0;
	// create a log of what's been added
	$log = "";
	foreach ($activities_json as $activity_json){
		$activity = new Activity($activity_json, $club_id);
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
$db = createConnection($db_params);
		// Update fact tables
		$fact_obj = new Facts();

		$after = date("Y-m-01");
		$before= date("Y-m-t");
		$fact_obj->updateFacts($db, $before, $after, $club_id);
		return 0;
	}
}

syncMonth(165, $db_params, $access_token);
syncMonth(25148, $db_params, $access_token);


?>