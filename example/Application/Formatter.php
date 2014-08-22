<?php

namespace Application;

/**
 * A default Poydia listener
 *
 * This is the default Poydia listener, which other plugins/listeners
 * will override its functionality
 *
 * @author		David Tkachuk
 * @package		Poydia-Example
 * @subpackage	Poydia
 * @version		0.1
 */
class Formatter implements \Podiya\Listener {

	private $podiya;

	public function registerEvents(\Podiya\Podiya $podiya) {
		$this->podiya = $podiya;
		$podiya->registerEvent("format_username", [$this, "formatUsername"])
				->registerEvent("format_group", [$this, "formatGroup"])
				->registerEvent("format_message", [$this, "formatMessage"])
				->registerEvent("format_date", [$this, "formatDate"])
				->registerEvent("create_post", [$this, "makePost"]);
	}
	
	public function formatUsername($username) {
		return $username;
	}
	
	public function formatGroup($groupName) {
		return $groupName;
	}
	
	public function formatMessage($message) {
		return nl2br($message);
	}
	
	public function formatDate($date) {
		return date("F j, Y h:i:s A", $date);
	}
	
	public function makePost($username, $group, $message, $date) {
		$result = "<div style=\"padding: 9px 16px;border:1px solid #EEE;margin-bottom:16px;\">" .
			"<strong>Posted by</strong> " . $this->podiya->callEvent("format_username", $username) . 
				" (" . $this->podiya->callEvent("format_group", $group) . ")<br />" .
			"<strong>Posted Date</strong> " . $this->podiya->callEvent("format_date", $date) . "<br />" .
				$this->podiya->callEvent("format_message", $message);
		
		return $result . "</div>";
	}

}

