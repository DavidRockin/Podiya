<?php

namespace Podiya;

/**
 * Podiya listener interface
 *
 * @author		David Tkachuk
 * @package		Podiya
 * @version		0.1
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
	 */
	public function registerEvents(\Podiya\Podiya $podiya);

}

