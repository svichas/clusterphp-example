<?php

namespace Cluster\Kernel\App;

use Cluster\Core\Model\Model;
use Cluster\Core\View\View;
use Cluster\Core\ErrorHandler\ErrorHandler;
use Cluster\Kernel\DevelopmentMode\DevelopmentMode;
use Cluster\Core\Route\Route;

//global variables

$GLOBALS['ROUTE_OVERWRITE'] = [
	"/404" => [
		"call" => true,
		"method" => function() {
			return View::render("http_error/404");
		}
	],
	"/500" => [
		"call" => false,
		"method" => function() {
			return View::render("http_error/500");
		}
	]
];


class Cluster {

	function __construct() {

		/*
		* Displaying development errors...
		*/
		DevelopmentMode::KernelCheck();

		/*
		* Run all controllers
		*/

		//Controllers base path
		$base_path = __BASE__ . "App".SEP."Controllers".SEP;

		//getting list of controllers with scandir
		$dirs = scandir($base_path);
		$dirs = array_splice($dirs, 2);
		foreach ($dirs as $dir) {
			foreach (glob($base_path. $dir . SEP. "*.php") as $controller_name) {
				//requiring controllers
				require_once $controller_name;
			}
		}
		foreach (glob($base_path."*.php") as $controller_name) {
 			require_once $controller_name;
		}



		// calling overwrite routes
		foreach ($GLOBALS['ROUTE_OVERWRITE'] as $route_path => $route_data) {

			if ($route_data['call']) {
				
				if ($route_path == "/500") {
					ob_end_clean();
				}

				// calling method
				Route::callFunction($route_data['method'], $route_path);
			}

		}

	
	}


	public static function setup() {

		if (!DEVELOPMENT_MODE) {
			echo "Setup() method requires DEVELOPMENT_MODE.";
			exit;
		}

		$models_path = __BASE__ . "App" . SEP . "Models" . SEP;

		foreach (glob($models_path."*.php") as $model_name_path) {

			$model_name = explode(SEP, $model_name_path);
			$model_name = end($model_name);
			$model_name = explode(".php", $model_name);
			$model_name = $model_name[0];

			$model = Model::load($model_name);

			if (method_exists($model, "setup")) {
				$model->setup();
			}

		}

		echo "Setup() method execution was successfull. Please comment <b>Cluster::setup();</b> on Public/index.php.";
		exit;
	}


}
