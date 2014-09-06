<?php

namespace DavidRockin\PodiyaExample;
use DavidRockin\Podiya\Podiya,
    DavidRockin\Podiya\Event,
    DavidRockin\Podiya\Listener;

/**
 * An example Podiya listener
 *
 * This is an example listener/plugin, which will modify
 * previously called listeners. This example listener enhances
 * the display of posts
 *
 * @author      David Tkachuk
 * @package     Podiya
 * @subpackage  PodiyaExample
 * @version     2.0
 */
class Fancify implements Listener
{
    private $podiya;
    private $events;

    public function __construct(Podiya $podiya) {
        $this->podiya = $podiya;
        $this->events = [['create_post', [$this, 'fancyPost']]];
        $this->podiya->subscribe_array($this->events);
    }
    
    public function destroy() {
        $this->podiya->unsubscribe_array($this->events);
    }
    
    public function fancyPost(Event $event) {
        return str_replace('border:1px solid #EEE;',
            'border:1px solid #DADADA;background:#F1F1F1;font-family:Arial;font-size:15px;',
            $event->getPreviousResult());
    }
}
