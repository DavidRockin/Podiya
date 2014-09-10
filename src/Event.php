<?php

namespace DavidRockin\Podiya;

/**
 * Event Class
 *
 * Objects of this class will be passed, whenever an event is fired, to all
 * handlers of said event along with their results. This class also allows
 * event handlers to easily share information with other event handlers.
 *
 * @author  David Tkachuk
 * @package Podiya
 * @version 2.0
 */
class Event
{
    /**
     * The name of the event
     *
     * @access  private
     * @since   0.3
     */
    private $name;
    
    /**
     * Who fired this event
     *
     * @access  private
     * @since   2.0
     */
    private $caller;
    
    /**
     * A boolean that indicates if the event is cancelled
     *
     * @access  private
     * @since   2.0
     */
    private $cancelled = false;
    
    /**
     * An array containing the event's data
     *
     * @access  private
     * @since   2.0
     */
    private $data = [];
    
    /**
     * An instance of the main Podiya class
     *
     * @access  private
     * @since   1.0
     */
    private $podiya = null;
    
    /**
     * An array that contains the results of previous event handlers
     *
     * @access  private
     * @since   1.0
     */
    private $previousResults = [];
    
    /**
     * Constructor method of Event
     * 
     * All of these properties' usage details are left up to the event handler,
     * so see your event handler to know what to pass here.
     *
     * @access  public
     * @param   string  $name   The name of the event
     * @param   mixed   $caller The calling object or class name (optional)
     * @param   mixed   $data   Data to be used by the event's handler (optional)
     * @param   \DavidRockin\Podiya\Podiya  $podiya A reference back to a Podiya instance (optional)
     * @since   0.3
     */
    public function __construct($name, $data = null, $caller = null, Podiya $podiya = null)
    {
        $this->name     = $name;
        $this->data     = $data;
        $this->caller   = $caller;
        $this->podiya   = $podiya;
    }
    
    /**
     * Getter method
     * 
     * Has special functionality for $this->previousResult
     * 
     * @access  public
     * @param   string  $name   Name of the property to retrieve
     * @return  mixed   The property's value
     * @since   2.0
     */
    public function __get($name)
    {
        switch ($name) {
            case 'previousResult':
                return $this->previousResults[count($this->previousResults)-1];
            
            default:
                return $this->$name;
        }
    }
    
    /**
     * Setter method
     * 
     * Only allows setting $this->previousResult and $this->cancelled
     * 
     * @access  public
     * @param   string  $name   Name of the property to set
     * @param   mixed   $val    Value to set it to
     * @since   2.0
     */
    public function __set($name, $val)
    {
        switch ($name) {
            case 'previousResult':
                $this->previousResults[] = $val;
                break;
            
            case 'cancelled':
                $this->cancelled = (bool) $val;
            
            default:
                break;
        }
    }
}
