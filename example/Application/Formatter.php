<?php

namespace DavidRockin\PodiyaExample;
use DavidRockin\Podiya\Podiya,
    DavidRockin\Podiya\Event,
    DavidRockin\Podiya\Listener;

/**
 * A default Podiya listener
 *
 * This is the default Podiya listener, which other plugins/listeners
 * will override its functionality
 *
 * @author      David Tkachuk
 * @package     Podiya
 * @subpackage  PodiyaExample
 * @version     2.0
 */
class Formatter implements Listener
{
    private $podiya;
    private $events;
    
    public function __construct(Podiya $podiya) {
        $this->podiya = $podiya;
        
        // events we will handle
        $this->events = [
            ['format_username', [$this, 'formatUsername']],
            ['format_group',    [$this, 'formatGroup']],
            ['format_date',     [$this, 'formatDate']],
            ['format_message',  [$this, 'formatMessage']],
            ['create_post',     [$this, 'makePost']],
        ];
        $this->podiya->subscribe_array($this->events);
    }
    
    public function destroy()
    {
        $this->podiya->unsubscribe_array($this->events);
    }
    
    public function formatUsername(Event $event) {
        return $event->getData();
    }
    
    public function formatGroup(Event $event) {
        return $event->getData();
    }
    
    public function formatMessage(Event $event) {
        return nl2br($event->getData());
    }
    
    public function formatDate(Event $event) {
        return date('F j, Y h:i:s A', $event->getData());
    }
    
    public function makePost(Event $event) {
        $result = '<div style="padding: 9px 16px;border:1px solid #EEE;margin-bottom:16px;">'
                 .'<strong>Posted by</strong> '
                 .$this->podiya->publish(new Event('format_username', $this, $event->getData('username')))
                 .' ('
                 .$this->podiya->publish(new Event('format_group', $this, $event->getData('group')))
                 .')<br /><strong>Posted Date</strong> '
                 .$this->podiya->publish(new Event('format_date', $this, $event->getData('date')))
                 .'<br />'
                 .$this->podiya->publish(new Event('format_message', $this, $event->getData('message')))
                 .'</div>';
        
        return $result;
    }
}
