<?php
	include_once('funcs.php');
	
	$html = file_get_contents('structure.html');

	$jobList = getJobListFromHTML($html);

	print_r($jobList);
?>