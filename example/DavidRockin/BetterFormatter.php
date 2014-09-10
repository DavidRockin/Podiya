<?php

namespace DavidRockin\PodiyaExample;
use DavidRockin\Podiya\Podiya,
    DavidRockin\Podiya\Event,
    DavidRockin\Podiya\Listener;

/**
 * An example Podiya listener
 *
 * This is an example listener/plugin, which will override
 * previously called listeners. This example listener enhances
 * the group and date formatting
 *
 * @author      David Tkachuk
 * @package     Podiya
 * @subpackage  PodiyaExample
 * @version     2.0
 */
class BetterFormatter implements Listener
{
    private $podiya;
    private $events;

    public function __construct(Podiya $podiya) {
        $this->podiya = $podiya;
        $this->events = [
            ['format_group', [$this, 'betterGroup']],
            ['format_date',  [$this, 'betterDate']],
        ];
        $this->podiya->subscribe_array($this->events);
    }
    
    public function destroy() {
        $this->podiya->unsubscribe_array($this->events);
    }
    
    public function betterGroup(Event $event) {
        $groupName = strtolower($event->data);
        switch ($groupName) {
            case 'admin':
            case 'administrator':
                $groupName = '<span style="color:#F00;">Administrator</span>';
                break;
                
            case 'mod':
            case 'moderator':
                $groupName = '<span style="color:#00A;">Moderator</span>';
                break;
        }
        return $groupName;
    }
    
    public function betterDate(Event $event) {
        return date('F j, Y h:i:s A T', $event->data);
    }
}
