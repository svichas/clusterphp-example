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

Route::get("/create", function() {
	
	$auth_model = Model::load("Auth");

	//return to index if user is not logged
	if (!$auth_model->isLoggedIn()) {
		Link::go("/index");
		exit;
	}

	$messages = [];
	$host_model = Model::load("Host");

	$form = new phpform();

	$form->add(phpform::$TEXT, "title", "", ["placeholder" => "Host url", "class" => "text", "maxlength" => "23", "minlength" => "5"])
	->add(phpform::$TEXTAREA, "content", "", ["placeholder" => "Host content", "class" => "text", "style" => "resize: vertical;", "maxlength" => "400", "required" => "required", "minlength" => "2"])
	->add(phpform::$SUBMIT, "host", "Create host", ["class" => "button button-blue"]);


	if ($form->isSubmitted() && $form->isValid()) {
		$form_data = $form->getData();

		if (!ctype_alnum($form_data['title'])) {
			$messages[] = [
				"type" => "error",
				"message" => "HOST URL is not valid."
			];
		} else {
			$result = $host_model->createHost($form_data['title'], $form_data['content'], $_SESSION['user_id']);
			if ($result) {
				$messages[] = [
					"type" => "success",
					"message" => "Host successfully created!"
				];
			} else {
				$messages[] = [
					"type" => "error",
					"message" => "Host url already exists."
				];
			}
		}

	}

	return View::render("dashboard/create", [
		"messages" => $messages,
		"create_form" => $form->getView()
	]);

});
