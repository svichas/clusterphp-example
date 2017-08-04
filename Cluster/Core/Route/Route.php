<?php
namespace Cluster\Core\Route;

use Cluster\Core\Request\Request;


class Route {

	public static function get($route="", $function) {

		if (is_string($route)) {
			foreach ($GLOBALS['ROUTE_OVERWRITE'] as $route_path => $route_data) {

				if (self::formatRoute($route) == self::formatRoute($route_path)) {

					$GLOBALS['ROUTE_OVERWRITE'][$route_path] = [
						"call" => $route_data['call'],
						"method" => $function
					];

				}

			}
		}

		//check if request match
		if (Request::Method() != "GET" && Request::Method() != "POST") return false;

		$route = self::match($route);
		if ($route) {
			self::setPageFound();
			self::callFunction($function, $route);
		}

	}

	public static function post($route="", $function) {

		//check if request match
		if (Request::Method() != "POST") return false;

		$route = self::match($route);
		if ($route) {
			self::setPageFound();
			self::callFunction($function, $route);
		}

	}

	public static function delete($route="", $function) {

		//check if request match
		if (Request::Method() != "DELETE") return false;

		$route = self::match($route);
		if ($route) {
			self::setPageFound();
			self::callFunction($function, $route);
		}

	}

	public static function put($route="", $function) {

		//check if request match
		if (Request::Method() != "PUT") return false;

		$route = self::match($route);
		if ($route) {
			self::setPageFound();
			self::callFunction($function, $route);
		}

	}

	public static function request($route="", $function, $requests=[]) {

		//upper case every element is array for case insentivity
		$requests = array_map("strtoupper", $requests);

		//check if request matches with request list
		if (!in_array(Request::Method(), $requests)) return false;

		$route = self::match($route);
		if ($route) {
			self::setPageFound();
			self::callFunction($function, $route);
		}
		
	}


	public static function setPageFound() {
		$GLOBALS['ROUTE_OVERWRITE']["/404"] = [
			"call" => false,
			"method" => $GLOBALS['ROUTE_OVERWRITE']["/404"]['method']
		];
	}

	public static function callFunction($function, $route) {
		echo call_user_func_array($function, self::getVariablesFromUri($route));
		return true;
	}

	private static function formatRoute($route="") {

		//removing slashes for start and end.
		$route = ltrim($route, "/");
		$route = rtrim($route, "/");

		$route = strtolower($route);

		return $route;
	}

	private static function getVariablesFromUri($route="") {

		$uri = Request::geturiArray();
		$route_array = explode("/",$route);
		$data = [];

		$i = 0;
		foreach ($route_array as $route_path) {

			if (substr($route_path, 0,1)=="{"&&substr($route_path, -1)=="}") {
				$key = $route_path;
				$key = str_replace("{", "", $route_path);
				$key = str_replace("}", "", $route_path);
				$data[$key] = $uri[$i];
			}

			$i += 1;
		}

		return $data;

	}


	private static function match($route="") {

		$uri = Request::geturiArray();

		if (is_array($route)) {

			foreach ($route as $route_element) {

				$returnValue = self::matchWithUrl($route_element, $uri);

				if ($returnValue) return $returnValue;

			}

			return false;

		} else {
			return self::matchWithUrl($route, $uri);
		}

	}

	private static function matchWithUrl($route = "", $uri=[]) {

		$route = self::formatRoute($route);
		$route_array = explode("/",$route);

		if (count($uri) != count($route_array)) return false;
		$i = 0;
		foreach($route_array as $route_path) {

			if (substr($route_path, 0,1)!="{" && substr($route_path, -1)!="}") {

				if (strtolower($uri[$i])!=strtolower($route_path)) return false;

			}

			$i += 1;

		}

		if ($route == "") $route = true;

		return $route;

	}


}
