<?php
	//connect to db
	//die("hello");
	$dbhost = "localhost";
	$dbuser = "buzzminer";
	$dbpass = "Buzz1!";
	$dbname = "buzzminer";
	$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname) or die("DB conn fail");
	//var_dump($connection);
	// die("done");
	// //test if connection occurred.
	// if(mysqli_connect_errno()) {
	// 	die("Database connection failed: " .
	// 		mysqli_connect_error() .
	// 		" (" . mysqli_connect_errno() . ")"
	// 		);
	// }
?>