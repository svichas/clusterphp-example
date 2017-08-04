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

Route::get(["/", "/index"], function() {
	
	$auth_model = Model::load("Auth");

	if ($auth_model->isLoggedIn()) Link::go("/dashboard"); 

	$form = new phpform();

	$form->add(phpform::$TEXT, "username", "", ["placeholder" => "Username or Email", "class" => "text"])
	->add(phpform::$PASSWORD, "password", "", ["placeholder" => "Password", "class" => "text"])
	->add(phpform::$SUBMIT, "login", "Login", ["class" => "button button-blue"]);

	$messages = [];

	if ($form->isSubmitted() && $form->isValid()) {

		$form_data = $form->getData();

		$auth_result = $auth_model->loginRequest($form_data['username'], $form_data['password']);

		if ($auth_result['success']) {
			Link::go("/dashboard");
			return false;
		} else {
			$messages[] = [
				"type" => "error",
				"message" => "Invalid username/password."
			];
		}


	}

	return View::render("index/index", [
		"form" => $form->getView(),
		"messages" => $messages
	]);

});

Route::get("/logout", function() {
	$auth_model = Model::load("Auth");

	//return to index if user is not logged
	if (!$auth_model->isLoggedIn()) {
		Link::go("/index");
		exit;
	}

	$auth_model->sessionDestroy();

	Link::go("/index");

});



Route::get("/account/create", function() {
	
	$auth_model = Model::load("Auth");

	//return to index if user is not logged
	if ($auth_model->isLoggedIn()) {
		Link::go("/dashboard");
		exit;
	}


	$form = new phpform();

	$form->add(phpform::$TEXT, "username", "", ["placeholder" => "Username", "class" => "text", "minlength" => "4", "maxlength" => "14"])
	->add(phpform::$EMAIL, "email", "", ["placeholder" => "Email", "class" => "text"])
	->add(phpform::$PASSWORD, "password", "", ["placeholder" => "Password", "class" => "text", "minlength" => "4", "maxlength" => "155"])
	->add(phpform::$SUBMIT, "create", "Create account", ["class" => "button button-blue"]);

	$messages = [];

	if ($form->isSubmitted() && $form->isValid()) {

		$form_data = $form->getData();


		if (ctype_alnum($form_data['username'])) {

			$auth_result = $auth_model->createUser($form_data['username'], $form_data['email'], $form_data['password']);

			if (!$auth_result) {
				$messages[] = [
					"type" => "error",
					"message" => "Username already exists."
				];
			} else {
				$messages[] = [
					"type" => "success",
					"message" => "Account successfully created."
				];
			}

		} else {
			$messages[] = [
				"type" => "error",
				"message" => "Invalid username."
			];
		}


	}

	return View::render("index/accountcreate", [
		"form" => $form->getView(),
		"messages" => $messages
	]);

});

