<?php
namespace Cluster\Core\View;

class View {

	public static function render($viewname="", $data=[]) {

		//Require Transfiguration library from libs
		require_once __BASE__ . SEP . "Cluster" . SEP ."Libs".SEP."Transfiguration".SEP."transfiguration.php";

		$path = __BASE__."App".SEP."Views".SEP;

		//check if viewname ends with php
		if (strlen($viewname) > 4 && strtolower(substr($viewname, -4)) == ".php") {
			extract($data);
			ob_start(); // begin collecting output
			require $path.$viewname;
			return ob_get_clean();
		}

		$file = "";
		if (file_exists($path.$viewname)) {
			$file = $path.$viewname;
		} else if (file_exists($path.$viewname.".html.transfiguration")) {
			$file = $path.$viewname.".html.transfiguration";
		} else if (file_exists($path.$viewname.".html")) {
			$file = $path.$viewname.".html";
		}

		$transfiguration = new \Transfiguration(file_get_contents($file), $data, $path);
		return $transfiguration->export();

	}

	public static function json($array=[]) {
		return json_encode($array);
	}

}
