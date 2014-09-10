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
class Formatter extends Listener
{
    public function __construct(Podiya $podiya) {
        // events we will handle
        $this->events = [
            ['format_username', [$this, 'formatUsername']],
            ['format_group',    [$this, 'formatGroup']],
            ['format_date',     [$this, 'formatDate']],
            ['format_message',  [$this, 'formatMessage']],
            ['create_post',     [$this, 'makePost']],
        ];
        parent::__construct($podiya);
    }
    
    public function formatUsername(Event $event) {
        return $event->data;
    }
    
    public function formatGroup(Event $event) {
        return $event->data;
    }
    
    public function formatMessage(Event $event) {
        return nl2br($event->data);
    }
    
    public function formatDate(Event $event) {
        return date('F j, Y h:i:s A', $event->data);
    }
    
    public function makePost(Event $event) {
        $result = '<div style="padding: 9px 16px;border:1px solid #EEE;margin-bottom:16px;">'
                 .'<strong>Posted by</strong> '
                 .$this->podiya->publish(new Event('format_username', $event->data['username'], $this))
                 .' ('
                 .$this->podiya->publish(new Event('format_group', $event->data['group'], $this))
                 .')<br /><strong>Posted Date</strong> '
                 .$this->podiya->publish(new Event('format_date', $event->data['date'], $this))
                 .'<br />'
                 .$this->podiya->publish(new Event('format_message', $event->data['message'], $this))
                 .'</div>';
        
        return $result;
    }
}
