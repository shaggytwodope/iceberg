<?php

namespace iceberg\hook;

class HookElement {

	public $enabled = true;

	public $name, $event, $path, $data = array();

	public function __construct($name, $event, $path) {
		$this->name = $name;
		$this->event = $event;
		$this->path = $path;
	}

	public function enable() { $this->enabled = true; }

	public function disable() { $this->enabled = false; }

	public function enabled() { return $this->enabled; }
}
