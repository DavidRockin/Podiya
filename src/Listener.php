<?php

namespace DavidRockin\Podiya;

/**
 * Podiya listener interface
 *
 * @author		David Tkachuk
 * @package		Podiya
 * @version		0.2
 */
interface Listener {

	/**
	 * Register the listener's event handlers
	 *
	 * When the listener class is initialized, this method
	 * will be called to register the event handlers
	 *
	 * @access		public
	 * @param		\Podiya\Podiya $podiya The main podiya class
	 * @since		0.1
	 */
	public function registerEvents(\Podiya\Podiya $podiya);

	/**
	 * Unregisters the listener's event handlers
	 *
	 * When the listener class becomes unregistered, this method
	 * will be called to unregister its event handles
	 *
	 * @access		public
	 * @param		\Podiya\Podiya $podiya The main podiya class
	 * @since		0.2
	 */
	public function unregisterEvents(\Podiya\Podiya $podiya);

}

