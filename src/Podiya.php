<?php

namespace DavidRockin\Podiya;

/**
 * Podiya main class
 *
 * @author  David Tkachuk
 * @package Podiya
 * @version 2.0
 */
class Podiya
{
    const PRIORITY_URGENT	= 0;
    const PRIORITY_HIGHEST	= 1;
    const PRIORITY_HIGH		= 2;
    const PRIORITY_NORMAL	= 3;
    const PRIORITY_LOW		= 4;
    const PRIORITY_LOWEST	= 5;
    
    /**
     * An array that contains registered events and their handlers by priority
     *
     * @access  private
     * @since   0.1
     */
    private $events = [];
    
    /**
     * An array holding any published events to which no handler has yet subscribed
     * 
     * @access  private
     * @since   2.0
     */
    private $pending = [];
    
    /**
     * Whether we should put published events for which there are no subscribers
     * onto the $pending list.
     * 
     * @access  private
     * @since   2.0
     */
    private $holdUnheardEvents = false;

    /**
     * Determine if events may be held if there are no subscribers for them
     *
     * @access  public
     * @return  bool    Return true if events may be held, otherwise false
     * @since   2.0
     */
    public function willHoldUnheardEvents()
    {
        return $this->holdUnheardEvents;
    }
    
    /**
     * Specifies if the event should be held if there are no subscribers for it
     *
     * @access  public
     * @param   bool    $hold   Hold the event in the pending list or not
     * @return  bool    Returns the new value we've set it to
     * @since   2.0
     */
    public function holdUnheardEvents($hold = true)
    {
        // if we're turning it off
        if (!$hold) {
            // make sure the pending list is wiped clean
            $this->pending = [];
        }
        return ($this->holdUnheardEvents = (bool) $hold);
    }
    
    /**
     * Registers an event handler to an event
     * 
     * @access  public
     * @param   string      $eventName  The published event's name
     * @param   callable    $callback   A callback that will handle the event
     * @param   int         $priority   Priority of the handler  (0-5)
     * @param   bool        $force      Whether to ignore event cancellation
     * @return  mixed       False if $eventName isn't published, array of first two params otherwise
     * @since   2.0
     */
    public function subscribe($eventName, callable $callback = null, 
                              $priority = self::PRIORITY_NORMAL, $force = false)
    {
        // handle an array of subscribers recursively if that's what we're given
        if (is_array($eventName) && is_array($eventName[0])) {
            $results = [];
            foreach ($eventName as $newsub) {
                $results[$newsub[0]] = $this->subscribe($newsub[0], $newsub[1],
                    (isset($newsub[2]) ? $newsub[2] : $priority),
                    (isset($newsub[3]) ? $newsub[3] : $force));
            }
            return $results;
        }
        
        // otherwise, we're not processing an array, so $callback better not be null
        if ($callback === null) {
            return false;
        }
        
        $interval = false;
        // if this is a timer subscriber
        if (strpos($eventName, 'timer:') === 0) {
            // extract the desired firing interval from the name
            $interval = (int) substr($eventName, 6);
            $eventName = 'timer';
        }
        
        // If the event was never registered, create it
        if (!$this->hasSubscribers($eventName)) {
            $this->events[$eventName] = [
                'subscribers'          => 0,
                self::PRIORITY_URGENT  => [],
                self::PRIORITY_HIGHEST => [],
                self::PRIORITY_HIGH    => [],
                self::PRIORITY_NORMAL  => [],
                self::PRIORITY_LOW     => [],
                self::PRIORITY_LOWEST  => [],
            ];
        }
        
        // Our new subscriber will have these properties, at least
        $newsub = [
            'callback' => $callback,
            'force'    => (bool) $force,
        ];
        // and if it's a timer, it will have a few more
        if ($interval) {
            $newsub['interval'] = $interval; // milliseconds
            $newsub['nextcalltime'] = self::currentTimeMillis() + $interval;
        }
        // ok, now we've composed our subscriber, so throw it on the queue
        $this->events[$eventName][$priority][] = $newsub;
        // and increment the counter for this event name
        $this->events[$eventName]['subscribers']++;
        
        $result = null;
        
        // there will never be pending timer events, so skip straight to the return
        if (!$interval) {
            $pcount = count($this->pending); // will be 0 if functionality is disabled
            
            // loop through the pending events
            for ($i = 0; $i < $pcount; $i++) {
                
                // if this pending event's name matches our new subscriber
                if ($this->pending[$i]->getName() == $eventName) {
                    // re-publish that matching pending event
                    $result[] = $this->publish(array_splice($this->pending, $i, 1), $priority);
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Detach a handler from its event
     * 
     * @access  public
     * @param   string      $eventName  The event we want to unsubscribe from
     * @param   callable    $callback   The callback we want to remove from the event
     * @return  \DavidRockin\Podiya\Podiya  This object
     * @since   2.0
     */
    public function unsubscribe($eventName, callable $callback = null)
    {
        // handle an array of subscribers recursively if that's what we're given
        if (is_array($eventName) && is_array($eventName[0])) {
            foreach ($eventName as $subscriber) {
                $this->unsubscribe($subscriber[0], $subscriber[1]);
            }
            return $this;
        }
        
        // otherwise, we're not processing an array, so $callback better not be null
        if ($callback === null) {
            return false;
        }
        
        // if this is a timer subscriber
        if (strpos($eventName, 'timer:') === 0) {
            // then we'll need to match not only the callback but also the interval
            $callback = [
                'interval' => (int) substr($eventName, 6),
                'callback' => $callback
            ];
            $eventName = 'timer';
        }
        
        // If the event has been subscribed to by this callback
        if (($priority = $this->isSubscribed($eventName, $callback)) !== false) {
            
            // Loop through the subscribers for the matching priority level
            foreach ($this->events[$eventName][$priority] as $key => $subscriber) {
                
                // if this subscriber matches what we're looking for
                if (self::array_search_deep($callback, $subscriber) !== false) {
                    
                    // delete that subscriber and decrement the event name's counter
                    unset($this->events[$eventName][$priority][$key]);
                    $this->events[$eventName]['subscribers']--;
                }
            }
            
            // If there are no more events, remove the event
            if ($this->events[$eventName]['subscribers'] == 0) {
                unset($this->events[$eventName]);
            }
        }
        
        return $this;
    }
    
    /**
     * Remove all subscribers from an event
     * 
     * @access  public
     * @param   string  $eventName  The desired event's name
     * @return  \DavidRockin\Podiya\Podiya  This object
     * @since   2.0
     */
    public function unsubscribeAll($eventName)
    {
        unset($this->events[$eventName]);
        return $this;
    }
    
    /**
     * Get the array of subscribers by priority for a given event name
     * 
     * @access  public
     * @param   string  $eventName  The desired event's name
     * @return  mixed   Array of subscribers by priority if found, false otherwise
     * @since   2.0
     */
    public function getSubscribers($eventName)
    {
        return ($this->hasSubscribers($eventName)) ? $this->events[$eventName] : false;
    }
    
    /**
     * Determine if the event name has any subscribers
     * 
     * @access  public
     * @param   string  $eventName  The desired event's name
     * @return  bool    Whether or not the event was published
     * @since   2.0
     */
    public function hasSubscribers($eventName)
    {
        return isset($this->events[$eventName]);
    }
    
    /**
     * Determine if the described event has been subscribed to or not by the callback
     * 
     * @access  public
     * @param   string      $eventName  The desired event's name
     * @param   callable    $callback   The specific callback we're looking for
     * @return  mixed   Priority it's subscribed to if found, false otherwise; use ===
     * @since   2.0
     */
    public function isSubscribed($eventName, callable $callback)
    {
        if ($this->hasSubscribers($eventName)) {
            // try to find the index of $callback in the list for this event name
            return self::array_search_deep($callback, $this->events[$eventName]);
        }
        return false;
    }
    
    /**
     * Let any relevant subscribers know an event needs to be handled
     *
     * Note: The event object can be used to share information to other similar
     * event handlers.
     *
     * @access  public
     * @param   DavidRockin\Podiya\Event    $event  An event object
     * @param   mixed   $priority   Notify only subscribers of a certain priority level
     * @return  mixed   Result of the event
     * @since   2.0
     */
    public function publish(Event $event, $priority = null)
    {
        $eventName = $event->getName();
        
        // If no subscribers are listening to this event...
        if (!$this->hasSubscribers($eventName)) {
            
            // Then if holding events is enabled and it's not a timer, hold it
            if ($this->holdUnheardEvents && $eventName != 'timer') {
                array_unshift($this->pending, $event);
            }
            
            // Either way, we don't need to do anything else here
            return;
        }
        
        $result = null;
        $eventNames = [$eventName];
        
        // Make sure event is fired to any subscribers that listen to all events
        if (isset($this->events['*'])) {
            array_unshift($eventNames, '*');
        }
        
        // First handle '*' subscribers if any, then the ones for this event name
        foreach ($eventNames as $eventName) {
            
            // Loop through all the subscriber priority levels
            foreach ($this->events[$eventName] as $plevel => &$subscribers) {
                
                // If a priority was passed and this isn't it,
                // or if this isn't a subscriber array
                if (($priority !== null && $plevel != $priority)
                    || !is_array($subscribers)
                ) {
                    // then move on to the next priority level
                    continue;
                }
                
                // Loop through the subscribers of this priority level
                foreach ($subscribers as &$subscriber) {
                    
                    // As long as the event's not cancelled, or if the subscriber is forced..
                    if (!$event->isCancelled() || $subscriber['force']) {
                        
                        // If the subscriber is a timer...
                        if (isset($subscriber['interval'])) {
                            // Then if the current time is equal to or after when the sub needs to be called
                            if (self::currentTimeMillis() >= $subscriber['nextcalltime']) {
                                // Mark down the next call time as another interval away
                                $subscriber['nextcalltime'] += $subscriber['interval'];
                            } else {
                                // It's not time yet
                                continue;
                            }
                        }
                        
                        // Fire it and save the result for passing to any further subscribers
                        $event->addPreviousResult($result);
                        $result = call_user_func($subscriber['callback'], $event);
                    }
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Searches a multi-dimensional array for a value in any dimension.
     * Named similar to the built-in PHP array_search() function.
     *
     * @access  public
     * @param   mixed   $needle     The value to be searched for
     * @param   array   $haystack   The array
     * @return  mixed   The top-level key containing the needle if found, false otherwise
     * @since   2.0
     */
    public static function array_search_deep($needle, array $haystack)
    {
        if (is_array($needle)
            && !is_callable($needle)
            // and if all key/value pairs in $needle have exact matches in $haystack
            && count(array_diff_assoc($needle, $haystack)) == 0
        ) {
            // we found what we're looking for, so bubble back up with 'true'
            return true;
        }
        
        foreach ($haystack as $key => $value) {
            if ($needle === $value
                || (is_array($value)
                    && self::array_search_deep($needle, $value) !== false
                )
            ) {
                // return top-level key of $haystack that contains $needle as a value somewhere
                return $key;
            }
        }
        // 404 $needle not found
        return false;
    }
    
    /**
     * Returns the current timestamp in milliseconds.
     * Named for the similar function in Java.
     * 
     * @access  public
     * @return  int Current timestamp in milliseconds
     * @since   2.0
     */
    public static function currentTimeMillis()
    {
        // microtime(true) returns a float where there's 4 digits after the
        // decimal and if you add 00 on the end, those 6 digits are microseconds.
        // But we want milliseconds, so bump that decimal point over 3 places.
        return (int) (microtime(true) * 1000);
    }
}
