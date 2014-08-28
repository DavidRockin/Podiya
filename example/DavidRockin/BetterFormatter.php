<?php

namespace DavidRockin\PodiyaExample;

/**
 * An example Poydia listener
 *
 * This is an example listener/plugin, which will override
 * previously called listeners. This example listener enhances
 * the group and date formatting
 *
 * @author		David Tkachuk
 * @package		PoydiaExample
 * @subpackage	Poydia
 * @version		0.3
 */
class BetterFormatter implements \DavidRockin\Podiya\Listener {

	private $podiya;

	public function registerEvents(\DavidRockin\Podiya\Podiya $podiya) {
		$this->podiya = $podiya;
		$podiya->registerEvent("format_group", [$this, "betterGroup"])
				->registerEvent("format_date", [$this, "betterDate"]);
	}
	
	public function unregisterEvents(\DavidRockin\Podiya\Podiya $podiya) {
		$podiya->unregisterEvent("format_group", [$this, "betterGroup"])
				->unregisterEvent("format_date", [$this, "betterDate"]);
	}
	
	public function betterGroup(\DavidRockin\Podiya\Event $event, $groupName) {
		switch (strtolower($groupName)) {
			case "admin":
			case "administrator":
				$groupName = "<span style='color:#F00;'>Administrator</span>";
				break;
				
			case "mod":
			case "moderator":
				$groupName = "<span style='color:#00A;'>Moderator</span>";
				break;
		}
	
		return $groupName;
	}
	
	public function betterDate(\DavidRockin\Podiya\Event $event, $date) {
		return date("F j, Y h:i:s A T", $date);
	}
	
}

