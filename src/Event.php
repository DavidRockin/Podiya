<?php

namespace DavidRockin\Podiya;

/**
 * Event Class
 *
 * This class will be used whenever an event is called.
 * This class will be passed to all of the handlers of
 * a registered event.
 *
 * @author		David Tkachuk
 * @package		Podiya
 * @version		0.3
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

}

