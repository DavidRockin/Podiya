<?php

namespace DavidRockin;

/**
 * An example Poydia listener
 *
 * This is an example listener/plugin, which will override
 * previously called listeners. This example listener enhances
 * a post's message
 *
 * @author		David Tkachuk
 * @package		Poydia-Example
 * @subpackage	Poydia
 * @version		0.1
 */
class FancyExamplePlugin implements \Podiya\Listener {

	private $podiya;

	public function registerEvents(\Podiya\Podiya $podiya) {
		$this->podiya = $podiya;
		$podiya->registerEvent("format_message", [$this, "formatMessage"]);
	}
	
	public function unregisterEvents(\Podiya\Podiya $podiya) {
		$podiya->unregisterEvent("format_message", [$this, "formatMessage"]);
	}
	
	public function formatMessage($message) {
		$message = strip_tags($message);
		$message = preg_replace("/\[b\](.+?)\[\/b\]/is", "<span style='font-weight:bold'>$1</span>", $message);
		$message = preg_replace("/\[u\](.+?)\[\/u\]/is", "<span style='text-decoration:underline'>$1</span>", $message);
		$message = preg_replace("/\[url=([^\[\]]+)\](.+?)\[\/url\]/is", "<a href='$1'>$2</a>", $message);
		$message = preg_replace("/\[url\](.+?)\[\/url\]/is", "<a href='$1'>$1</a>", $message);
		return nl2br($message);
	}
	
}

