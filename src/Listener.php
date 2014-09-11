<?php

namespace DavidRockin\Podiya;

/**
 * Podiya listener class -- to be extended
 *
 * @author  David Tkachuk
 * @package Podiya
 * @version 2.0
 */
abstract class Listener
{
    /**
     * Our instance of Podiya
     * 
     * @access  protected
     * @since   2.0
     */
    protected $podiya;
    
    /**
     * The array of events we'll be subscribing to. This is to be set by child
     * classes before they call parent::__construct($podiya);
     * 
     * @access  protected
     * @since   2.0
     */
    protected $events = [];
    
    /**
     * Sets up the Listener object
     * 
     * Before this is called, $this->events needs to be set by the child class.
     * Child class constructors will likely use this same method signature.
     * 
     * @access  protected
     * @since   2.0
     */
    protected function __construct(Podiya $podiya)
    {
        $this->podiya = $podiya;
        $this->podiya->subscribe($this->events);
    }
    
    /**
     * Unregisters the listener's event handlers
     *
     * When the listener object needs to be destroyed, this method has to be
     * called to unsubscribe its event handles. Unfortunately, __destruct()
     * can't accomplish this, because the Podiya object maintains hidden
     * references to the listener object
     * (in $this->podiya->events[$eventName][$priority][(int)]['callback'][0] ).
     *
     * @access  public
     * @return  void
     * @since   2.0
     */
    public function destroy()
    {
        $this->podiya->unsubscribe($this->events);
    }
}
