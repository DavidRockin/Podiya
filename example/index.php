<?php

define("BASEDIR", dirname(__FILE__) . "/");
define("SRCDIR", dirname(BASEDIR) . "/src/");

// Include Podiya files
include SRCDIR . "/Podiya.php";
include SRCDIR . "/Listener.php";
include SRCDIR . "/Event.php";

// Setup Podiya
$podiya = new \DavidRockin\Podiya\Podiya();

// Include the listeners
include BASEDIR . "/Application/Formatter.php";
include BASEDIR . "/DavidRockin/FancyExamplePlugin.php";
include BASEDIR . "/DavidRockin/BetterFormatter.php";
include BASEDIR . "/DavidRockin/Fancy.php";

// Initialize the listeners
$defaultFormatter = new \DavidRockin\PodiyaExample\Formatter();
$fancyExamplePlugin = new \DavidRockin\PodiyaExample\FancyExamplePlugin();
$betterFormatter = new \DavidRockin\PodiyaExample\BetterFormatter();
$fancy = new \DavidRockin\PodiyaExample\Fancy();

// Register the default application listeners 
$podiya->registerListener($defaultFormatter)
// Register plugin listeners
	->registerListener($fancyExamplePlugin)
	->registerListener($betterFormatter)
	->registerListener($fancy);

$sampleMessage = <<<HTML
Lorem [b]ipsum dolor sit amet[/b], consectetur adipiscing elit. Fusce dignissim neque vitae velit mollis, ac volutpat mauris consequat. Morbi sed arcu leo. Vestibulum dignissim, est at blandit suscipit, sapien leo [u]iaculis massa, mollis faucibus[/u] odio mauris sed risus. Integer mollis, ipsum ut efficitur lobortis, ex enim dictum felis, in mattis purus orci [b]in nulla. Nunc [u]semper mauris[/u] enim[/b], quis faucibus massa luctus quis. Sed ut malesuada magna, cursus ullamcorper augue. Curabitur orci nisl, mattis quis elementum eu, condimentum at lorem. Interdum et malesuada fames ac ante ipsum primis in faucibus. Aliquam ultricies tristique urna in maximus. Praesent facilisis, [url=http://github.com/DavidRockin]diam ac euismod sollicitudin[/url], eros diam consectetur est, quis egestas nisl orci vel nisl. Aenean consectetur justo non felis varius, eu fermentum mi fermentum. Ut ac dui ligula.
For more information please visit [url]http://github.com/DavidRockin[/url]
HTML;


// With better formatting
echo $podiya->callEvent("create_post", "David", "Administrator", $sampleMessage, time()) . 
	$podiya->callEvent("create_post", "John Doe", "Moderator", $sampleMessage, strtotime("-3 days"));


// Without the better formatting
$podiya->unregisterEvent("format_group", [$betterFormatter, "betterGroup"]);
$podiya->unregisterEvent("create_post", [$fancy, "fancyPost"]);
echo $podiya->callEvent("create_post", "AppleJuice", "Member", $sampleMessage, strtotime("-3 weeks")) .
	$podiya->callEvent("create_post", "Anonymous", "Donator", $sampleMessage, strtotime("-3 years"));


// After unregistering the fancyExamplePlugin listener
$podiya->unregisterListener($fancyExamplePlugin);
echo $podiya->callEvent("create_post", "AppleJuice", "Member", $sampleMessage, strtotime("-3 weeks"));

