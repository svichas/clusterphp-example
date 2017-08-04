<?php 
namespace Cluster\Modules\Token;

class Token {
	
	/*
	@param $name token name
	*/
	public static function set($name="",$content="",$time=0) {
		$name = md5($name);

		$domain = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : "localhost";

		$https = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on");

		$cookie = setcookie($name, $content, time()+$time, "/", $domain, $https, true);
	}

	/*
	@param $name token name
	*/
	public static function get($name="") {
		$name = md5($name);
		if (isset($_COOKIE[$name])) {
			return $_COOKIE[$name];
		} else {
			return false;
		}
	}

	/*
	@param $name token name
	*/
	public static function destroy($name="") {
		$name = md5($name);
		setcookie($name, "", time()-5, "/");
	}

}