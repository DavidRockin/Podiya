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
 * a post's message
 *
 * @author      David Tkachuk
 * @package     Podiya
 * @subpackage  PodiyaExample
 * @version     2.0
 */
class FancyExamplePlugin extends Listener
{
    public function __construct(Podiya $podiya) {
        $this->events = [['format_message', [$this, 'formatMessage']]];
        parent::__construct($podiya);
    }
    
    public function formatMessage(Event $event) {
        $message = strip_tags($event->getData());
        $message = preg_replace('/\[b\](.+?)\[\/b\]/is', '<span style="font-weight:bold">$1</span>', $message);
        $message = preg_replace('/\[u\](.+?)\[\/u\]/is', '<span style="text-decoration:underline">$1</span>', $message);
        $message = preg_replace('/\[url=([^\[\]]+)\](.+?)\[\/url\]/is', '<a href="$1">$2</a>', $message);
        $message = preg_replace('/\[url\](.+?)\[\/url\]/is', '<a href="$1">$1</a>', $message);
        return nl2br($message);
    }
}
