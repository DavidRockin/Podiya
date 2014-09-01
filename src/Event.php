<?php

namespace DavidRockin\Podiya;

/**
 * Event Class
 *
 * This class will be used whenever an event is called.
 * This class will be passed to all of the handlers of
 * a registered event along with their results. This class
 * allows event handlers to easily share information 
 * with other event handlers.
 *
 * @author		David Tkachuk
 * @package		Podiya
 * @version		1.0
 * @TODO		Store passed vars in an array using magic methods?
 */
class Event {

	/**
	 * The name of the event
	 *
	 * @access		private
	 * @since		0.3
	 */
	private $eventName;

	/**
	 * A boolean that indicates if the event is cancelled
	 *
	 * @access		private
	 * @since		0.3
	 */
	private $isCancelled = false;
	
	/**
	 * An array that contains the results of previous event handlers
	 *
	 * @access		private
	 * @since		1.0
	 */
	private $previousResults = array();
	
	/**
	 * Constructor method of Event
	 *
	 * @access		public
	 * @param		string $eventName The name of the event
	 * @since		0.3
	 */
	public function __construct($eventName) {
		$this->eventName = $eventName;
	}
	
	/**
	 * Returns the event's name
	 *
	 * @access		public
	 * @return		string Event name
	 * @since		0.3
	 */
	public function getEventName() {
		return $this->eventName;
	}
	
	/**
	 * Specifies if the event is cancelled or not
	 *
	 * @access		public
	 * @param		bool $cancelled Cancel the event or not
	 * @since		0.3
	 */
	public function setCancelled($cancel) {
		$this->isCancelled = $cancel;
	}
	
	/**
	 * Determine if the event is cancelled or not
	 *
	 * @access		public
	 * @return		bool Return true if event cancelled, otherwise false
	 * @since		0.3
	 */
	public function isCancelled() {
		return $this->isCancelled;
	}
	
	/**
	 * Gets the result of the previous event handler
	 *
	 * @access		public
	 * @return		mixed Result of previous event handler
	 * @since		1.0
	 */
	public function getPreviousResult() {
		$key = count($this->previousResults)-1;
		return $this->previousResults[$key];
	}
	
	/**
	 * Gets an array of all previous event handlers' results
	 *
	 * @access		public
	 * @return		array Array of previous event handlers results
	 * @since		1.0
	 */
	public function getPreviousResults() {
		return $this->previousResults;
	}
	
	/**
	 * Adds the previous event handler's result
	 *
	 * @access		public
	 * @param		mixed $result The result of the previous event handler
	 * @since		1.0
	 */
	public function addPreviousResult($result) {
		$this->previousResults[] = $result;
	}

}

