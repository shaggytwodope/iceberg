<?php

class Twig_Extension_Variables extends Twig_Extension {

	public $functions = array();

	public function getName() { 
		return "variables";
	}

	public function register($function) {
		$this->functions[$function] = new Twig_Function_Method($this, $function);
	}

	public function getFunctions() {
		return $this->functions;
	}

	public function __call($function, $arguments) {
		$this->{$function} = $arguments[0];
	}

}