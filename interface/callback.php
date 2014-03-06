<?php
include_once("includes.php");
include_once("StravaInterface.class.php");
$S = new StravaInterface();
$callback = $_GET;

$params = array(
	"client_id"=>$client_id,
	"client_secret"=>$client_secret,
	"code"=>$callback['code'],
	);

$response = $S->interact("https://www.strava.com/oauth/token", $params, true);
var_dump($response);
if (isset($response["message"]) && $response["message"] == "Bad Request"){
	// Ah shit
	echo("Damn, bad login, try again?");
}else{
	secure_session_destroy();
	secure_session_start();
	$_SESSION['athlete_id'] = $response["athlete"]["id"];
	$_SESSION['access_token'] = base64_encode($response["athlete"]["id"] . $response["access_token"]);
	// redirect to homepage
}



?>