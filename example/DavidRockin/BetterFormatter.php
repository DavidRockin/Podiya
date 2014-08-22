<?php

namespace DavidRockin;

/**
 * An example Poydia listener
 *
 * This is an example listener/plugin, which will override
 * previously called listeners. This example listener enhances
 * the group and date formatting
 *
 * @author		David Tkachuk
 * @package		Poydia-Example
 * @subpackage	Poydia
 * @version		0.1
 */
class BetterFormatter implements \Podiya\Listener {

	private $podiya;

	public function registerEvents(\Podiya\Podiya $podiya) {
		$this->podiya = $podiya;
		$podiya->registerEvent("format_group", [$this, "betterGroup"])
				->registerEvent("format_date", [$this, "betterDate"]);
	}

	public function betterGroup($groupName) {
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
	
	public function betterDate($date) {
		return date("F j, Y h:i:s A T", $date);
	}
	
}

