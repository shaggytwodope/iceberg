<?php

namespace iceberg\hook;

class HookElement {

	public $enabled = true;

	public function __construct($name, $event, $path, $data = array()) {

		$this->name = $name;
		$this->event = $event;
		$this->path = $path;
		$this->data = $data;
	}

	public function enable() {
		$this->enabled = true;
	}

	public function disable() {
		$this->enabled = false;
	}

	public function enabled() {
		return $this->enabled;
	}

}
