<?php

namespace DavidRockin\PodiyaExample;

/**
 * A default Podiya listener
 *
 * This is the default Podiya listener, which other plugins/listeners
 * will override its functionality
 *
 * @author		David Tkachuk
 * @package		PodiyaExample
 * @subpackage	Podiya
 * @version		1.0
 */
class Formatter implements \DavidRockin\Podiya\Listener {

	private $podiya;

	public function registerEvents(\DavidRockin\Podiya\Podiya $podiya) {
		$this->podiya = $podiya;
		$podiya->registerEvent("format_username", [$this, "formatUsername"])
				->registerEvent("format_group", [$this, "formatGroup"])
				->registerEvent("format_message", [$this, "formatMessage"])
				->registerEvent("format_date", [$this, "formatDate"])
				->registerEvent("create_post", [$this, "makePost"]);
	}
	
	public function unregisterEvents(\DavidRockin\Podiya\Podiya $podiya) {
		$podiya->unregisterEvent("format_username", [$this, "formatUsername"])
				->unregisterEvent("format_group", [$this, "formatGroup"])
				->unregisterEvent("format_message", [$this, "formatMessage"])
				->unregisterEvent("format_date", [$this, "formatDate"])
				->unregisterEvent("create_post", [$this, "makePost"]);
	}
	
	public function formatUsername(\DavidRockin\Podiya\Event $event, $username) {
		return $username;
	}
	
	public function formatGroup(\DavidRockin\Podiya\Event $event, $groupName) {
		return $groupName;
	}
	
	public function formatMessage(\DavidRockin\Podiya\Event $event, $message) {
		return nl2br($message);
	}
	
	public function formatDate(\DavidRockin\Podiya\Event $event, $date) {
		return date("F j, Y h:i:s A", $date);
	}
	
	public function makePost(\DavidRockin\Podiya\Event $event, $username, $group, $message, $date) {
		$result = "<div style=\"padding: 9px 16px;border:1px solid #EEE;margin-bottom:16px;\">" .
			"<strong>Posted by</strong> " . $this->podiya->callEvent("format_username", $username) . 
				" (" . $this->podiya->callEvent("format_group", $group) . ")<br />" .
			"<strong>Posted Date</strong> " . $this->podiya->callEvent("format_date", $date) . "<br />" .
				$this->podiya->callEvent("format_message", $message);

		return $result . "</div>";
	}

}

