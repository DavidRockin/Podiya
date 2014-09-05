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
     * A boolean that indicates if the event is cancelled
     *
     * @access  private
     * @since   2.0
     */
    private $cancelled = false;
    
    /**
     * An array that contains the results of previous event handlers
     *
     * @access  private
     * @since   1.0
     */
    private $previousResults = [];
    
    /**
     * An instance of the main Podiya class
     *
     * @access  private
     * @since   1.0
     */
    private $podiya = null;
    
    /**
     * An array containing the event's data
     *
     * @access  private
     * @since   2.0
     */
    private $data = [];
    
    /**
     * Constructor method of Event
     *
     * @access  public
     * @param   string $eventName The name of the event
     * @since   0.3
     */
    public function __construct($name, $data, Podiya $podiya = null) {
        $this->name	  = $name;
        $this->data   = $data;
        $this->podiya = $podiya;
    }
    
    /**
     * Returns the event's name
     *
     * @access  public
     * @return  string Event name
     * @since   2.0
     */
    public function getName() {
        return $this->name;
    }
    
    /**
     * Specifies if the event should be cancelled or not
     *
     * @access  public
     * @param   bool $cancelled Cancel the event or not
     * @since   0.3
     */
    public function cancel($cancel = true) {
        return ($this->cancelled = (bool) $cancel);
    }
    
    /**
     * Determine if the event is cancelled or not
     *
     * @access  public
     * @return  bool Return true if event cancelled, otherwise false
     * @since   0.3
     */
    public function isCancelled() {
        return $this->cancelled;
    }
    
    /**
     * Gets the result of the previous event handler
     *
     * @access  public
     * @return  mixed Result of previous event handler
     * @since   1.0
     */
    public function getPreviousResult() {
        return $this->previousResults[count($this->previousResults)-1];
    }
    
    /**
     * Gets an array of all previous event handlers' results
     *
     * @access  public
     * @return  array Array of previous event handlers results
     * @since   1.0
     */
    public function getPreviousResults() {
        return $this->previousResults;
    }
    
    /**
     * Adds the previous event handler's result
     *
     * @access  public
     * @param   mixed $result The result of the previous event handler
     * @since   1.0
     */
    public function addPreviousResult($result) {
        $this->previousResults[] = $result;
        return $result;
    }
    
    /**
     * Returns our instance of the Podiya class
     *
     * @access  public
     * @return  \DavidRockin\Podiya\Podiya Podiya object reference
     * @since   1.0
     */
    public function getPodiya() {
        return $this->podiya;
    }
    
    /**
     * Returns the event's data
     * 
     * @access  public
     * @return  mixed The entire data array if no params, otherwise a specific key
     * @since   2.0
     */
    public function getData($key = null) {
        $return = $this->data;
        if ($key !== null) {
            $return = (isset($return[$key]) ? $return[$key] : null);
        }
        return $return;
    }
}
