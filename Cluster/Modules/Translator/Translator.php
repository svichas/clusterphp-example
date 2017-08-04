<?php

namespace Cluster\Modules\Translator;

class Translator {

	protected $languages = [];
	protected $language = [];

	public function __construct() {
		$transBase = dirname(__FILE__). SEP."..".SEP."..".SEP."..".SEP."App".SEP."Views".SEP."Translator" . SEP;
		$language_conf_source = $transBase . "config.json";
		if (file_exists($language_conf_source)) {
			
			$this->languages = json_decode(file_get_contents($language_conf_source));
			
			$lang = $this->languages[0]->language_code;

			if (isset($_GET['language'])&&$this->languageExists($_GET['language'])) {
				$lang = $_GET['language'];
			} else if (isset($_GET['lang'])&&$this->languageExists($_GET['lang'])) {
				$lang = $_GET['lang'];
			} else if (isset($_GET['l'])&&$this->languageExists($_GET['l'])) {
				$lang = $_GET['l'];
			}

			if (file_exists($transBase . $lang . ".json")) {
				$this->language = json_decode(file_get_contents($transBase . $lang . ".json"), true);
			}


		}


	}

	public function translate($word_code = "") {
		return isset($this->language[$word_code]) ? $this->language[$word_code] : $word_code;
	}

	protected function languageExists($language_code = "") {
		foreach ($this->languages as $lcode) {
			if (strtolower($lcode->language_code) == strtolower($language_code)) return true;
		}
		return false;
	}

}