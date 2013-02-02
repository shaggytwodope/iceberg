<?php

class Twig_Extension_Variables extends Twig_Extension {

	public $data;

	public function getName() { 
		return "variables"; 
	}

	public function getFunctions() {
		return array(
			"_set" => new Twig_Function_Method($this, "_set")
		);
	}
	public function _set($name, $value) { 
		$this->data[$name] = $value;
	}

	public function _get($name) {
		return $this->data[$name];
	}

}