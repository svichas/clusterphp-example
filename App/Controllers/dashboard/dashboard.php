<?php
use Cluster\Core\Route\Route;
use Cluster\Core\View\View;
use Cluster\Core\ErrorHandler\ErrorHandler;
use Cluster\Core\Request\Request;
use Cluster\Core\Model\Model;
use Cluster\Modules\phpform\phpform;
use Cluster\Modules\Translator\Translator;
use Cluster\Modules\Token\Token;
use Cluster\Modules\Link\Link;
use Cluster\Modules\String\Random;

Route::get("/dashboard", function() {
	
	$auth_model = Model::load("Auth");

	//return to index if user is not logged
	if (!$auth_model->isLoggedIn()) {
		Link::go("/index");
		exit;
	}


	$host_model = Model::load("Host");

	$hosts = $host_model->getUserHosts($_SESSION['user_id']);

	return View::render("dashboard/index", [
		"hosts" => $hosts
	]);

});
