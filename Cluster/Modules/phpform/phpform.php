<?php 
namespace Cluster\Modules\phpform;

class phpform {

	protected $token = "";
	protected $elements = [];
	protected $method = "POST";
	protected $formPrefix = "phpform";
	
	public static $FILE     = ["<div class=\"form-group\"><input type=\"file\" value=\"{value}\" name=\"{name}\" {attributes}></div>", "FILE"];
	public static $TEXT     = ["<div class=\"form-group\"><label><div>{placeholder}</div><input type=\"text\" value=\"{value}\" name=\"{name}\" {attributes}></label></div>", "TEXT"];
	public static $EMAIL    = ["<div class=\"form-group\"><label><div>{placeholder}</div><input type=\"email\" value=\"{value}\" name=\"{name}\" {attributes}></label></div>", "EMAIL"];
	public static $HIDDEN   = ["<input type=\"hidden\" value=\"{value}\" name=\"{name}\" {attributes}>", "HIDDEN"];
	public static $PASSWORD = ["<div class=\"form-group\"><label><div>{placeholder}</div><input type=\"password\" value=\"{value}\" name=\"{name}\" {attributes}></label></div>", "PASSWORD"];
	public static $TEXTAREA = ["<div class=\"form-group\"><label><div>{placeholder}</div><textarea type=\"password\" name=\"{name}\" {attributes}>{value}</textarea></label></div>", "TEXTAREA"];
	public static $CHECKBOX = ["<div class=\"form-group\"><label><div>{placeholder}</div><input type=\"checkbox\" value=\"{value}\" name=\"{name}\" {attributes}></label></div>", "CHECKBOX"];
	public static $NUMBER   = ["<div class=\"form-group\"><label><div>{placeholder}</div><input type=\"number\" value=\"{value}\" name=\"{name}\" {attributes}></label></div>", "NUMBER"];
	public static $SUBMIT   = ["<div class=\"form-group\"><input type=\"submit\" value=\"{value}\" name=\"{name}\" {attributes}></div>", "SUBMIT"];
	public static $RADIO    = ["<div class=\"form-group\"><input type=\"radio\" value=\"{value}\" name=\"{name}\" {attributes}></div>", "RADIO"];
	public static $COLOR    = ["<div class=\"form-group\"><input type=\"color\" value=\"{value}\" name=\"{name}\" {attributes}></div>", "COLOR"];
	public static $MONTH    = ["<div class=\"form-group\"><input type=\"month\" value=\"{value}\" name=\"{name}\" {attributes}></div>", "MONTH"];
	public static $DATE     = ["<div class=\"form-group\"><input type=\"date\" value=\"{value}\" name=\"{name}\" {attributes}></div>", "DATE"];

	public function __construct($method="POST") {
		$this->method = $method;
		$session_name = md5($this->getFormString());
		//starting session for csrf token.
		if (session_status() == PHP_SESSION_NONE) session_start();
		//setting token
		$this->token = isset($_SESSION[$session_name]) ? $_SESSION[$session_name] : $this->randomString(22);
		//adding token to form
		$this->addElement(self::$HIDDEN, "csrf_token", $this->token);

		if (!isset($_SESSION[$session_name])) $_SESSION[$session_name] = $this->token;
	}

	public function add($type=[], $name="", $value="", $attributes=[]) {
		$this->addElement($type, $name, $value, $attributes);
		return $this;
	}

	public function isSubmitted() {

		$session_name = md5($this->getFormString());
		$ret_val = false;
		if (isset($_POST[$this->formPrefix])&&isset($_POST[$this->formPrefix]['csrf_token'])&&isset($_SESSION[$session_name])&&$_SESSION[$session_name] != "") {
			if ($_POST[$this->formPrefix]['csrf_token'] == $_SESSION[$session_name]) $ret_val = true;
		}
		
		//set session
		$_SESSION[$session_name] = $this->token;
		return $ret_val;
	}

	public function isValid() {

		$data = $this->getData();
		$not_post = ['CHECKBOX', "RADIO", "FILE"];

		if (strtoupper($_SERVER['REQUEST_METHOD']) != strtoupper($this->method)) return false;

		if (!empty($data)) {
			
			foreach ($this->elements as $element) {

				$type = $element['type'][1];
				$name = $element['name'];

				if (!in_array($type, $not_post)) {
					
					if ($name == "") continue;

					if (isset($element['attributes']['disabled'])) continue;
					
					//check if is posted
					if (!isset($data[$name])) return false;					

					//check if max length matches with post value
					if (isset($element['attributes']['maxlength'])) {
						if (mb_strlen($data[$name]) > $element['attributes']['maxlength']) return false;
					}
					//check if min length matches with post value
					if (isset($element['attributes']['minlength'])) {
						if (mb_strlen($data[$name]) < $element['attributes']['minlength']) return false;
					}
					//check if is empty if is required
					if (isset($element['attributes']['required'])) {
						if (empty($data[$name])) return false;
					}
					if ($type == "EMAIL" && $data[$name] != "") {
   						if(!filter_var($data[$name], FILTER_VALIDATE_EMAIL)) return false;
					}

					if ($type == "NUMBER" && $data[$name] != "") {
   						if(!ctype_digit($data[$name])) return false;
					}

				}
			}
			return true;
		}

		return false;

	}

	public function getData() {
		//get form post data
		if (isset($_POST[$this->formPrefix])) {
			return $_POST[$this->formPrefix];
		}
	}

	public function getView() {
		return $this->constructFormHtml();
	}
	public function renderView() {
		echo $this->constructFormHtml();
	}
	protected function constructFormHtml() {
		$html = "";
		foreach ($this->elements as $element) {
			$html .= $this->constructElement($element);
		}
		return $this->formWrap($html);
	}
	protected function formWrap($html = "") {
		return "<form action='' method='{$this->method}' autocomplete='off'>". $html. "</form>";
	}
	protected function constructElement($element=[]) {
		$attr_string = $this->constructAttrbutes($element['attributes']);
		$element_content = $element['type'][0];
		$element_placeholder = isset($element['attributes']['placeholder']) ? $element['attributes']['placeholder'] : "";
		$element_content = str_replace("{attributes}", $attr_string, $element_content);
		$element_content = str_replace("{value}", $element['value'], $element_content);
		$element_content = str_replace("{name}", "{$this->formPrefix}[".$element['name']."]", $element_content);
		$element_content = str_replace("{placeholder}", $element_placeholder, $element_content);
		return $element_content;
	}
	protected function constructAttrbutes($attributes=[]) {
		$attrs_string = "";
		foreach ($attributes as $key => $attr) {
			$attrs_string .= $key . "='{$attr}' ";
		}
		return $attrs_string;
	}
	protected function addElement($type="", $name="", $value="", $attributes=[]) {
		$this->elements[] = [
			"type" => $type,
			"name" => $name,
			"value" => $value,
			"attributes" => $attributes
		];
	}
	protected function getFormString() {
		$string = "";
		foreach ($this->elements as $element) {
			
			if ($element['name']=="csrf_token") continue;

			foreach ($element as $em) {
				if (is_array($em)) {
					$string .= implode("", $em);
				} else {
					$string .= $em;
				}
			}
		}
		return $string. "_form";
	}
	
	protected function randomString($length = 10) {

		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		
		return $randomString;
	}

}
