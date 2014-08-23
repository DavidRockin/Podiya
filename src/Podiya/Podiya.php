<?php

namespace Podiya;

/**
 * Podiya main class
 *
 * @author		David Tkachuk
 * @package		Podiya
 * @version		0.1
 */
class Podiya {

	/**
	 * An array that contains registered events
	 *
	 * @access		protected
	 */
	protected $events = array();

	/**
	 * Registers an event handler for an event
	 *
	 * @access		public
	 * @param		string $eventName The registered event's name
	 * @param		callable $callback A callback that will handle the event
	 * @return		\Podiya\Podiya Returns the class
	 */
	public function registerEvent($eventName, callable $callback, $priority = \Podiya\Priority::NORMAL) {
		$this->events[$eventName][$priority][] = $callback;
		return $this;
	}

	/**
	 * Unregister the event handlers for an event
	 *
	 * @access		public
	 * @param		string $eventName The registered event's name
	 * @return		\Podiya\Podiya Returns the class
	 */
	public function unregisterEvent($eventName) {
		if ($this->eventRegistered($eventName)) {
			foreach ($this->events[$eventName] as $key => $value) {
				$this->events[$eventName][$key] = array_splice($this->events[$eventName][$key], 0, 1);
			}
		}

		return $this;
	}

	/**
	 * Remove a registered event
	 *
	 * @access		public
	 * @param		string $eventName The registered event's name
	 * @return		\Podiya\Podiya Returns the class
	 */
	public function removeEvent($eventName) {
		if ($this->eventRegistered($eventName)) {
			unset($this->events[$eventName]);
		}

		return $this;
	}
	
	/**
	 * Registers a listener class
	 *
	 * @access		public
	 * @param		\Podiya\Listener $listener The listener class to be registered
	 * @return		\Podiya\Podiya Returns the class
	 */
	public function registerListener(\Podiya\Listener $listener) {
		$listener->registerEvents($this);
		return $this;
	}
	
	/**
	 * Call an event to be handled by a listener
	 *
	 * The arguments passed to callEvent() will be passed to the
	 * event handler, the last argument that is passed to the event
	 * handler is the result of the previous event handler.
	 *
	 * @access		public
	 * @param		string $eventName The targeted event's name
	 * @param		mixed $variable,... The option and unlimited options passed to the event
	 * @return		mixed Result of the event
	 */
	public function callEvent($eventName) {
		if (!$this->eventRegistered($eventName))
			return;

		// Get the passed arguments
		$args = func_get_args();
		array_shift($args);
		$result = null;

		for ($i = 0; $i < 6; $i++) {
			if (!isset($this->events[$eventName][$i])) continue;
			$events = $this->events[$eventName][$i];

			foreach ($events as $callback) {
				$arguments = array_merge($args, array($result));
				$result = call_user_func_array($callback, $arguments);
			}
		}
		
		return $result;
	}
	
	/**
	 * Determine if the event has been registered
	 *
	 * @access		public
	 * @param		string $eventName The targeted event's name
	 * @return		bool Whether or not the event was registered
	 */
	public function eventRegistered($eventName) {
		return (isset($this->events[$eventName]) && !empty($this->events[$eventName]));
	}

}

