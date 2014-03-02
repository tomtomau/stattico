<?php
include("strava.php");
/*
function syncActivities($club_id, $db_params, $access_token){
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
}*/

function syncArchives($club_id, $db_params, $access_token){
	$page = 8;
	$page_limit = 20;
	$date_limit = strtotime("2013-12-01");
	$this_date = strtotime(date("Y-m-d"));
	$log = "";
	$db = createConnection($db_params);
	while($this_date > $date_limit){
		// still within date limit
		// interact
		$url = "https://www.strava.com/api/v3/clubs/$club_id/activities?access_token=$access_token&page=10";
		$activities_json = interact($url);
		var_dump($activities_json);
		foreach($activities_json as $activity_json){
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
					//$count++;
				}catch(PDOException $e){
					$log = $e;
					break;
				}
			}
		}
		if(sizeof($activities_json)>0){
			$first_activity = $activities_json[0];
			$this_date = strtotime($first_activity->{"start_date"});
		}
		$page++;
		if($page > $page_limit){
			break;
		}
	}
	echo($log);
	echo("<br> $page pages");
}
//syncArchives(165, $db_params, $access_token);

function interactNew($url, $params){
	$url .= '?' . http_build_query($params);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_HTTPGET, true);
	$headers = array('Content-Type:application/json', 'Expect:');
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	$result = curl_exec($ch);
	curl_getinfo($ch, CURLINFO_HTTP_CODE);
	echo(curl_error($ch));
	$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	echo("Status $status <br>");
	//var_dump($result);
    curl_close($ch);
    return json_decode($result);
}

$club_id = 25148;
$params = array("access_token"=>$access_token, "page" => 7);
$url = "https://www.strava.com/api/v3/clubs/$club_id/activities";
$activities_json = interactNew($url, $params);
var_dump($activities_json);



?>