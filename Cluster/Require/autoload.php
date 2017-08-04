<?php 

function __autoload($classname = "") {
	$path = __BASE__;

	$path_array = explode("\\", $classname);
	
	//if (strtoupper($path_array[0]) != "CLUSTER") return false;

	for ($i=0;$i<count($path_array);$i++) {
		$path.= SEP. $path_array[$i];
	}

	$path .= ".php";

	if (file_exists($path)) require_once $path;
	
}