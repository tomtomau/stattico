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

	public function in_database($db){
		try{
			$query = "SELECT COUNT(*) FROM activities WHERE id = ?";
			$statement = $db->prepare($query);
			$statement->execute(array($this->id));
			$count = $statement->fetch(PDO::FETCH_NUM)[0];
			return $count;
		}catch(PDOException $e){
			var_dump($e);
		}
	}
	public function addToDatabase($db){
		try{
			$query = "INSERT INTO activities (id, club_id, athlete_id, distance, datetime) VALUES (?, ?, ?, ?, ?)";
			$statement = $db->prepare($query);
			$statement->execute(array($this->id, $this->club_id, $this->athlete_id, $this->distance, $this->datetime));
			return true;
		}catch(PDOException $e){
			var_dump($e);
		}
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
		if($activity->in_database($db)){
			continue;
		}else{
			try{
				$activity->addToDatabase($db);
				$log .= "New Activity <a href='http://www.strava.com/activities/".$activity->id."'>here</a><br>";
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
		// New activities added
		// Update fact tables
		// Email Log Notifying Update
		// function notify()
	}else{
		echo("nothing");
	}
}
syncMonth(165, $db_params, $access_token);

?>