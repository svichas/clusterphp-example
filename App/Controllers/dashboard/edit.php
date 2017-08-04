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

Route::get("/host/edit/{host_url}", function($host_url="") {
		
	$auth_model = Model::load("Auth");
	
	if (!$auth_model->isLoggedIn()) {
		Link::go("/index");
		exit;
	}

	$host_model = Model::load("Host");
	$host = $host_model->getLoggedInUserHost($host_url, $_SESSION['user_id']);

	if ($host) {

		$messages = [];
		$form = new phpform();

		$form->add(phpform::$TEXT, "title", $host['host_title'], ["disabled"=>"disabled","placeholder" => "Host url", "class" => "text", "maxlength" => "23", "minlength" => "5"])
		->add(phpform::$TEXTAREA, "content", $host['host_content'], ["placeholder" => "Host content", "class" => "text", "style" => "resize: vertical;", "maxlength" => "400", "required" => "required", "minlength" => "2"])
		->add(phpform::$SUBMIT, "host", "Edit host", ["class" => "button button-blue"]);



		if ($form->isSubmitted() && $form->isValid()) {
			
			$form_data = $form->getData();

			//if ($host['host_title'] != $form_data['title']) return false;

			$host_model->updateHost($host['host_title'], $form_data['content']);

			$messages[] = [
				"type" => "success",
				"message" => "Host successfully editted."
			];


			Link::go("/index");
		
		}

		return View::render("dashboard/edit", [
			"messages" => $messages,
			"edit_form" => $form->getView()
		]);

	} else {
		
		Link::go("/index");
		exit;
	
	}


});



Route::get("/host/delete/{host_url}", function($host_url="") {
		
	$auth_model = Model::load("Auth");
	
	if (!$auth_model->isLoggedIn()) {
		Link::go("/index");
		exit;
	}

	$host_model = Model::load("Host");
	$host_model->deleteHost($host_url, $_SESSION['user_id']);

	Link::go("/dashboard");

});
