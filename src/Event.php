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
     * Returns the event's name
     * 	
     * @access  public
     * @return  string  Event name
     * @since   2.0
     */
    public function getName()
    { 	
        return $this->name; 	
    }
    
    /** 	
     * Returns the event's data
     * 	
     * @access  public
     * @param   mixed   $key    An array key (optional)
     * @return  mixed   The entire data array if no params, otherwise a specific key
     * @since   2.0
     */
    public function getData($key = null)
    {
		return ($key === null ? $this->data : 
					(isset($this->data[$key]) ? $this->data[$key] : null));
    }
    
    /** 	
     * Returns the event's calling object or class name
     * 	
     * @access  public
     * @return  mixed  Calling object or class name
     * @since   2.0
     */ 	
    public function getCaller()
    { 	
        return $this->caller;
    }
    
    /**
     * Returns our Podiya instance
     * 
     * @access  public
     * @return  \DavidRockin\Podiya\Podiya  Podiya object reference
     * @since   1.0
     */
    public function getPodiya()
    {
        return $this->podiya;
    }
    
    /**
     * Gets an array of all previous event handlers' results
     * 
     * @access  public
     * @return  array   Array of previous event handlers results
     * @since   1.0
     */
    public function getPreviousResults()
    {
        return $this->previousResults;
    }
    
    /**
     * Gets the result of the previous event handler
     * 
     * @access  public
     * @return  mixed   Result of previous event handler
     * @since   1.0
     */
    public function getPreviousResult()
    {
        return $this->previousResults[count($this->previousResults)-1];
    }
    
    /**
     * Adds the previous event handler's result
     * 
     * @access  public
     * @param   mixed   $result The result of the previous event handler
     * @since   1.0
     */
    public function addPreviousResult($result) {
        $this->previousResults[] = $result;
        return $result;
    }
    
    /**
     * Determine whether further subscriber calls for this event will be stopped
     * 
     * @access  public
     * @param   bool    $cancel Cancel the event or not
     * @return  bool    Returns the new value we've set it to
     * @since   0.3
     */
    public function setCancelled($cancel = true)
    {
        return ($this->cancelled = (bool) $cancel);
    }
    
    /**
     * Return whether the event is cancelled
     * 
     * @access  public
     * @return  bool    True if event is cancelled, otherwise false
     * @since   0.3
     */
    public function isCancelled()
    {
        return $this->cancelled;
    }
}
