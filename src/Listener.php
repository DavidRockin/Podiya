<?php

namespace DavidRockin\Podiya;

/**
 * Podiya listener interface
 *
 * @author  David Tkachuk
 * @package Podiya
 * @version 2.0
 */
interface Listener
{
    /**
     * Unregisters the listener's event handlers
     *
     * When the listener object needs to be destroyed, this method has to be
     * called to unsubscribe its event handles. Unfortunately, __destruct()
     * can't accomplish this, because the Podiya object maintains hidden
     * references to the listener object
     * (in $podiya->events[$eventName][$priority][(int)]['callback'][0] ).
     *
     * @access  public
     * @return  void
     * @since   0.2
     */
    public function destroy();
}
