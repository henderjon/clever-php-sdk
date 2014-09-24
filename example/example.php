<?php

require "vendor/autoload.php";

$logger = new \Chevron\Loggers\UserFuncLogger(function($level, $message, array $context = []){
	$context = ["level" => strtoupper($level), "message" => $message, "timestamp" => date("c (e)")] + $context;

	$len = 0;
	foreach($context as $key => $value){
		if( ($l = strlen($key)) > $len){ $len = $l; }
	}

	$output = "\n\n--------------------------------------------------\n\n";

	foreach($context as $key => $value){
		$output .= sprintf("%{$len}s => (%s)%s\n", $key, gettype($value), json_encode($value));
	}

	$output .= "\n\n\n";

	echo $output;
});

$clever = new \Clever\CleverApi("DEMO_TOKEN");
$clever->setLogger($logger);

$district = $clever->district("4fd43cc56d11340000000005");
$section = $clever->section("530e597a049e75a9262d0baf");

drop($section->getEvents());


$schools = $district->getSchools();

$school = $schools[0];

drop($school->getEvents());

// try{
// 	$district = $clever->getDistrict("4fd43cc56d11340000000005");
// }catch(Exception $e){
// 	drop($e);
// }

// drop($district->getSchools(["limit" => "1"]));
exit();