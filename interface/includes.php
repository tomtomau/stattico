<?php
// This file must be included on every page

// Begin global variables:
// Database credentials
$db_params = array(
	"user"=>"root",
	"pass"=>"",
	"name"=>"strava",
	"host"=>"localhost"
	);

// You need to curl and get an access token
$client_id = "";
$client_secret = "";
$access_token = "";

// Begin global functions

function createConnection($dummy = null){
    // Connect to database
    global $db_params;
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

function secure_session_start(){
    $session_name = "secure_session";
    $secure = false;
    $httponly = true;
    // Forces sessions to only use cookies.
    if (ini_set('session.use_only_cookies', 1) === FALSE) {
        header("Location: ../error.php?err=Could not initiate a safe session (ini_set)");
        exit();
    }
    // Gets current cookies params.
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params($cookieParams["lifetime"],
        $cookieParams["path"], 
        $cookieParams["domain"], 
        $secure,
        $httponly);
     // Sets the session name to the one set above.
    session_name($session_name);
    session_start();            // Start the PHP session 
    session_regenerate_id();    // regenerated the session, delete the old one. 

}

function secure_session_destroy(){
    secure_session_start();
    $_SESSION = array();
    // get session parameters 
    $params = session_get_cookie_params();

    // Delete the actual cookie. 
    setcookie(session_name(),
        '', time() - 42000, 
        $params["path"], 
        $params["domain"], 
        $params["secure"], 
        $params["httponly"]);

    // Destroy session 
    session_destroy();
}


?>