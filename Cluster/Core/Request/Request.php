<?php 
namespace Cluster\Core\Request;

class Request {

	public static function Method() {
		return strtoupper($_SERVER['REQUEST_METHOD']);
	}

	public static function post($name="") {
		return isset($_POST[$name]) ? $_POST[$name] : "";
	}

	public static function get($name="") {
		return isset($_GET[$name]) ? $_GET[$name] : "";
	}

	public static function getBaseUrl() {
		$url_base = $_SERVER['PHP_SELF'];
		$url_base_array = explode("/", $url_base);
		array_pop($url_base_array);
		return implode("/", $url_base_array);
	}

	public static function geturiArray() {
		$url_base_array = explode("/", self::getBaseUrl());
		$url_full = rtrim($_SERVER["REQUEST_URI"],"/");
    	$url_full = filter_var($url_full, FILTER_SANITIZE_URL);
		if (strpos($url_full, "?")) {
			$url_full_arr = explode("?", $url_full);
			$url_full = $url_full_arr[0];
		}
		$url_array = explode("/", $url_full);
		for ($i=0;$i<count($url_base_array);$i++) {
			array_shift($url_array);
		}
		if (!isset($url_array[0])) $url_array[0] = "";
		
		return $url_array;
	}

	public static function getUri() {
		return rtrim($_SERVER["REQUEST_URI"],"/");
	}

}