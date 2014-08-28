<?php

namespace DavidRockin\Podiya;

class Event {

	private $eventName;
	private $isCancelled = false;
	
	public function __construct($eventName) {
		$this->eventName = $eventName;
	}
	
	public function getEventName() {
		return $this->eventName;
	}
	
	public function setCancelled($cancel) {
		$this->isCancelled = $cancel;
	}
	
	public function isCancelled() {
		return $this->isCancelled;
	}

}

