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

Route::get("/host/{host_url}", function($host_url="") {

	$host_model = Model::load("Host");

	return $host_model->getHostHtml($host_url);

});
