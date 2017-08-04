<?php 
namespace Cluster\Kernel\DevelopmentMode;
use Cluster\Core\Route\Route;
use Cluster\Core\View\View;
use Cluster\Core\ErrorHandler\ErrorHandler;

class DevelopmentMode {

	public static function KernelCheck() {
		if (DEVELOPMENT_MODE) {
			//if development mode show php errors in screen
			ini_set('display_errors', 1);
			ini_set('display_startup_errors', 1);
			error_reporting(E_ALL);

			//creating route for cluster errorhandler
			Route::get("/cluster_error/{ERROR_ID}", function($ERROR_ID) {

				//getting the error
				$error_handler = new ErrorHandler;
				$error = $error_handler->getError($ERROR_ID);

				return View::render("http_error/ErrorHandler", [
					"error" => $error
				]);

			});

			set_error_handler(function($errno, $errstr, $errfile, $errline) {
				
				$error = [
					"errno" => $errno,
					"errstr" => $errstr,
					"errfile" => $errfile,
					"errline" => $errline
				];

				
				echo View::render("http_error/500_DEVELOPMENT_MODE", $error);
				

				exit;
			});

		} else {

			set_error_handler(function() {
				ob_start();
				$GLOBALS['ROUTE_OVERWRITE']["/500"]['call'] = true;
			});

			ini_set('display_errors', 0);
			ini_set('display_startup_errors', 0);
		}
	}


}