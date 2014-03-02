<?php
// This file must be included on every page

// Begin global variables:
// Database credentials
$db_params = array(
	"user"=>"",
	"pass"=>"",
	"name"=>"",
	"host"=>""
	);

// You need to curl and get an access token
$access_token = "";

// Begin global functions

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


?>