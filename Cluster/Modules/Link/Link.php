<?php
namespace Cluster\Modules\Link;

use Cluster\Core\Request\Request;

class Link {

	public static function create($string = "") {
		$string = ltrim($string, "/");
		return Request::getBaseUrl()."/".$string;
	}

	public static function go($string = "") {
		header("location: ".self::create($string));
	}

}
