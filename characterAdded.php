<?php

//Turn on error reporting
ini_set('display_errors', 'On');
//Connects to the database
$mysqli = new mysqli("oniddb.cws.oregonstate.edu","cawleys-db","N8zPsvQQLymkGPe8","cawleys-db");


	//insert first, last, birthplace, starting and ending rank
	if(!($stmt = $mysqli->prepare("INSERT INTO star_character(first_name, last_name, birth_place) 
									VALUES (?,?,?)"))){
		echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
	}



	//header("Location: test.php");

?>


