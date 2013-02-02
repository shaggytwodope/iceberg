<?php

class Twig_Extension_Variables extends Twig_Extension {

	public $data = array();

	public function getName() { 
		return "variables"; 
	}

	public function getFunctions() {
		return array(
			"set" => new Twig_Function_Method($this, "set")
		);
	}
	public function set($name, $value) {
		$this->data[$name] = $value;
	}

	public function get($name) {
		return $this->data[$name];
	}

}