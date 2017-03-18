<?php
	//connect to db
	//die("hello");
<<<<<<< HEAD
	$dbhost = "46.101.227.78";
	//$dbhost = "localhost";
	$dbuser = "buzzminer";
	$dbpass = "Buzz1!";
	$dbname = "buzzminer";
	$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);// or die("DB conn fail");
=======
	$dbhost = "localhost";
	$dbuser = "buzzminer";
	$dbpass = "Buzz1!";
	$dbname = "buzzminer";
	$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname) or die("DB conn fail");
>>>>>>> a6a23c73ac0e1c314d4356f96876599ebab6a8f0
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