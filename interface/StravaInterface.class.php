<?php

class StravaInterface{
	function getNewActivities(){
		$AthletesContainer = new Athletes();
		$Athletes = $AthletesContainer->getSubscribedAthlete();
		foreach($Athletes as $Athlete){
			$this->getAthleteActivities($Athlete["id"]);
		}
	}

	function getAthleteActivities($access_token, $after=0){
		// Set URL
		$url = "https://www.strava.com/api/v3/athlete/activities";
		//@TODO: Get athletes token
		$params = array("access_token"=>$access_token, "after"=>$after);
		print_r($url);
		$response = $this->interact($url, $params);
		return $response;
	}

	function syncAthleteActivities($Activities){
		foreach($Activities as $Activity){
			$ActivityObj = new Activity($Activity);
		}
		
	}

	/**
	*	Interacts with a pin payments URL and returns an array
	*		of information, decoded from the JSON return
	*		@param string page
	*			Page name (customers, charges, etc)
	*		@param array params
	*			Optional associative array with the POST parameters
	*			for the request
	*				(Default empty array)
	*		@param boolean post
	*			does special things if you need to use a POST method
	*		@return
	*			Returns json_decode array of JSON reply from Pin
	*/

	function interact($url, $params=array(), $post=false){
		$ch = curl_init();
		if($post){
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		}else{
			$url = $url."?".http_build_query($params);
		}
		echo($url);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		$result = curl_exec($ch);
		curl_close($ch);
		return json_decode($result, true);
	}
}

?>