<?php

namespace Podiya;

/**
 * Podiya main class
 *
 * @author		David Tkachuk
 * @package		Podiya
 * @version		0.2
 */
class Podiya {

	/**
	 * An array that contains registered events
	 *
	 * @access		protected
	 * @since		0.1
	 */
	protected $events = array();

	/**
	 * An array that contains registered listeners
	 *
	 * @access		protected
	 * @since		0.2
	 */
	protected $listeners = array();

	/**
	 * Registers an event handler for an event
	 *
	 * @access		public
	 * @param		string $eventName The registered event's name
	 * @param		callable $callback A callback that will handle the event
	 * @return		\Podiya\Podiya Returns the class
	 * @since		0.1
	 */
	public function registerEvent($eventName, callable $callback, $priority = \Podiya\Priority::NORMAL) {
		$this->events[$eventName][$priority][] = $callback;
		return $this;
	}

	/**
	 * Unregister an event handler for an event
	 *
	 * @access		public
	 * @param		string $eventName The registered event's name
	 * @return		\Podiya\Podiya Returns the class
	 * @since		0.1
	 */
	public function unregisterEvent($eventName, callable $callback) {
		if ($this->eventRegistered($eventName)) {
			foreach ($this->events[$eventName] as $priority => $events) {
				$index = array_search($callback, $this->events[$eventName][$priority], true);
				if ($index !== false) {
					unset($this->events[$eventName][$priority][$index]);
				}
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
	 * @since		0.2
	 */
	public function removeEvent($eventName) {
		if ($this->eventRegistered($eventName)) {
			unset($this->events[$eventName]);
		}

		return $this;
	}
	
	/**
	 * Register a listener class
	 *
	 * @access		public
	 * @param		\Podiya\Listener $listener The listener class to be registered
	 * @return		\Podiya\Podiya Returns the class
	 * @since		0.1
	 */
	public function registerListener(\Podiya\Listener $listener) {
		$this->listeners[] = $listener;
		$listener->registerEvents($this);
		return $this;
	}

	/**
	 * Unregister a listener class
	 *
	 * @access		public
	 * @param		\Podiya\Listener $listener The listener class to be unregistered
	 * @return		\Podiya\Podiya Returns the class
	 * @version		0.2
	 */
	public function unregisterListener(\Podiya\Listener $listener) {
		$index = array_search($listener, $this->listeners, true);
		if ($index !== false) {
			unset($this->listeners[$index]);
		}
		
		$listener->unregisterEvents($this);
		return $this;
	}
	
	/**
	 * Call an event to be handled by an event handler
	 *
	 * The arguments passed to callEvent() will be passed to the
	 * event handler, the last argument that is passed to the event
	 * handler is the result of the previous event handler.
	 *
	 * @access		public
	 * @param		string $eventName The targeted event's name
	 * @param		mixed $variable,... The option and unlimited options passed to the event
	 * @return		mixed Result of the event
	 * @since		0.1
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
	 * @since		0.1
	 */
	public function eventRegistered($eventName) {
		return (isset($this->events[$eventName]) && !empty($this->events[$eventName]));
	}

}

