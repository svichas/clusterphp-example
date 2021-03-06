<?php 

/*
Require Core files.
*/
require_once 'Core/Lexer.php';
require_once 'Core/Parser.php';
require_once 'Core/Translator.php';
require_once 'Helpers/Evaluator.php';

use Transfiguration\Core\Lexer;
use Transfiguration\Core\Translator;
use Transfiguration\Core\Parser;


class Transfiguration {
	
	public $requirebase = "";
	public $parser;

	public function __construct($html, $data = [], $path="") {
		$lexer = new Lexer($html);
		$this->parser = new Parser($lexer->exportTokens(), $data, $path);
	}

	public function export() {
		$translator = new Translator($this->parser->exportTokens());
		return $translator->translate();
	}

	public function render() {
		$translator = new Translator($this->parser->exportTokens());
		echo $translator->translate();	
		return true;
	}
	
}