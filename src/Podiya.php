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
     * An array holding any published events to which no handler has subscribed yet
     */
    private $pending = [];
    
    /**
     * An array that contains callbacks and their time interval to be executed on
     * 
     * @access  private
     * @since   2.0
     */
    private $timers = [];

    /**
     * Registers an event handler to an event
     * 
     * @access  public
     * @param   string      $eventName  The published event's name
     * @param   callable    $callback   A callback that will handle the event
     * @param   int         $priority   Priority of the event (0-5)
     * @param   bool        $force      Whether to ignore event cancellation
     * @return  mixed   False if $eventName isn't published, array of first two params otherwise
     * @since   2.0
     */
    public function subscribe($eventName, callable $callback, 
                              $priority = self::PRIORITY_NORMAL, $force = false)
    {
        if ($eventName{0} == '+') {
            // this is a timer!
            $this->timers[$priority][] = [
                'interval' => (int) substr($eventName, 1), // milliseconds
                'lastcalltime' => self::currentTimeMillis(), // milliseconds
                'callback' => $callback,
                'force'    => (bool) $force,
            ];
            return [$eventName, $callback, null];
        }
        
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
        
        $this->events[$eventName]['subscribers']++;
        $this->events[$eventName][$priority][] = [
            'callback' => $callback,
            'force'    => (bool) $force,
        ];
        
        // now re-publish any pending events for this subscriber
        $result = null;
        $pcount = count($this->pending);
        for ($i = 0; $i < $pcount; $i++) {
            if ($this->pending[$i]->getName() == $eventName) {
                $result[] = $this->publish(array_splice($this->pending, $i, 1), $priority);
            }
        }
        return [$eventName, $callback, $result];
    }
    
    /**
     * Subscribes multiple handlers at once
     * 
     * @access  public
     * @param   array   $arr    The list of handlers
     * @return  void
     * @since   2.0
     */
    public function subscribe_array(array $arr)
    {
        foreach ($arr as $info) {
            if (isset($info[2])) {
                if (isset($info[3])) {
                    $this->subscribe($info[0], $info[1], $info[2], $info[3]);
                } else {
                    $this->subscribe($info[0], $info[1], $info[2]);
                }
            } else {
                $this->subscribe($info[0], $info[1]);
            }
        }
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
    public function unsubscribe($eventName, callable $callback)
    {
        if ($this->hasSubscribers($eventName)) {
            // unsubscribing a normal event
            foreach ($this->events[$eventName] as $priority => $subscribers) {
                if ($priority != 'subscribers') {
                    $index = $this->array_search_deep($callback, $this->events[$eventName][$priority]);
                    if ($index !== false) {
                        unset($this->events[$eventName][$priority][$index]);
                        $this->events[$eventName]['subscribers']--;
                    }
                }
            }
            
            if ($this->events[$eventName]['subscribers'] == 0) {
                unset($this->events[$eventName]);
            }
        } else if ($eventName{0} == '+') {
            // unsubscribing a timer
            foreach ($this->timers as $priority => $subscribers) {
                $index = $this->array_search_deep(
                    ['interval' => (int) substr($eventName, 1), 'callback' => $callback],
                    $this->timers[$priority]);
                if ($index !== false) {
                    unset($this->timers[$priority][$index]);
                }
            }
        }
        return $this;
    }
    
    /**
     * Unsubscribes multiple handlers at once
     * 
     * @access  public
     * @param   array   $arr    The list of handlers
     * @return  void
     * @since   2.0
     */
    public function unsubscribe_array(array $arr)
    {
        foreach ($arr as $info) {
            $this->unsubscribe($info[0], $info[1]);
        }
    }
    
    /**
     * Remove all subscribers from an event
     * 
     * @access  public
     * @param   string  $eventName  The desired event's name
     * @return  void
     * @since   2.0
     */
    public function unsubscribeAll($eventName)
    {
        unset($this->events[$eventName]);
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
        if ($eventName{0} == '+') {
            // looking for a timer
            return self::array_search_deep(
                ['interval' => (int) substr($eventName, 1), 'callback' => $callback],
                $this->timers);
        }
        
        return self::array_search_deep($callback, $this->events[$eventName]);
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
    public function publish(Event $event, $priority = false)
    {
        if (!$this->hasSubscribers($event->getName())) {
            array_unshift($this->pending, $event);
            return;
        }
        
        $result = null;
        
        if ($priority === false) {
            // Loop through all the priority levels
            foreach ($this->events[$event->getName()] as $plevel => $subscribers) {
                if ($plevel != 'subscribers') {
                    // Loop through the subscribers of this priority level
                    foreach ($subscribers as $subscriber) {
                        if (!$event->isCancelled() || $subscriber['force']) {
                            $event->addPreviousResult($result);
                            $result = call_user_func($subscriber['callback'], $event);
                        }
                    }
                }
            }
        } else {
            // Loop through the subscribers of the given priority
            foreach ($this->events[$event->getName()][$priority] as $subscriber) {
                if (!$event->isCancelled() || $subscriber['force']) {
                    $event->addPreviousResult($result);
                    $result = call_user_func($subscriber['callback'], $event);
                }
            }
        }
        return $result;
    }
    
    /**
     * Check all timers to see if any of them are ready to be called
     * 
     * Typically this will be called like:
     * $podiya->tick(new Event('timer'));
     * 
     * @access  public
     * @param   DavidRockin\Podiya\Event    $event  An event object
     * @return  array   Result of the events
     * @since   2.0
     */
    public function tick(Event $event)
    {
        $result = [];
        foreach ($this->timers as $priority => $subscribers) {
            foreach ($subscribers as $subscriber) {
                if (self::currentTimeMillis() - $subscriber['lastcalltime']
                    > $subscriber['interval']
                    && (!$event->isCancelled() || $subscriber['force'])
                ) {
                    $event->addPreviousResult($result);
                    $result[] = call_user_func($subscriber['callback'], $event);
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
            && count(array_diff_assoc($needle, $haystack)) == 0
        ) {
            return true;
        }
        
        foreach ($haystack as $key => $value) {
            if ($needle === $value
                || (is_array($value)
                    && self::array_search_deep($needle, $value) !== false
                )
            ) {
                return $key;
            }
        }
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
        return (int) (microtime(true) * 1000);
    }
}
