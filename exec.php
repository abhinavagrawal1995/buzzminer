<?php
	$output = shell_exec('git pull');
	echo "<pre>".$output."</pre>";
	die('<hr>done');
?>