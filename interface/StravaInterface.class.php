<?php

class StravaInterface{
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
}

?>