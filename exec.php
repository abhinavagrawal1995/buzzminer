<?php
	var_dump(getcwd());
	$output = shell_exec('ls');
	var_dump($output);
	die('done');
?>