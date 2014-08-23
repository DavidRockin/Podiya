<?php

define("BASEDIR", dirname(__FILE__) . "/");
define("SRCDIR", dirname(BASEDIR) . "/src/");

// Include Poydia files
include SRCDIR . "/Podiya/Podiya.php";
include SRCDIR . "/Podiya/Listener.php";
include SRCDIR . "/Podiya/Priority.php";

// Setup Poydia
$podiya = new \Podiya\Podiya();

// Include the listeners
include BASEDIR . "/Application/Formatter.php";
include BASEDIR . "/DavidRockin/FancyExamplePlugin.php";
include BASEDIR . "/DavidRockin/BetterFormatter.php";
include BASEDIR . "/DavidRockin/Fancy.php";

// Register the default application listeners 
$podiya->registerListener(new \Application\Formatter())
// Register plugin listeners
	->registerListener(new \DavidRockin\FancyExamplePlugin())
	->registerListener(new \DavidRockin\BetterFormatter())
	->registerListener(new \DavidRockin\Fancy());

$sampleMessage = <<<HTML
Lorem [b]ipsum dolor sit amet[/b], consectetur adipiscing elit. Fusce dignissim neque vitae velit mollis, ac volutpat mauris consequat. Morbi sed arcu leo. Vestibulum dignissim, est at blandit suscipit, sapien leo [u]iaculis massa, mollis faucibus[/u] odio mauris sed risus. Integer mollis, ipsum ut efficitur lobortis, ex enim dictum felis, in mattis purus orci [b]in nulla. Nunc [u]semper mauris[/u] enim[/b], quis faucibus massa luctus quis. Sed ut malesuada magna, cursus ullamcorper augue. Curabitur orci nisl, mattis quis elementum eu, condimentum at lorem. Interdum et malesuada fames ac ante ipsum primis in faucibus. Aliquam ultricies tristique urna in maximus. Praesent facilisis, [url=http://github.com/DavidRockin]diam ac euismod sollicitudin[/url], eros diam consectetur est, quis egestas nisl orci vel nisl. Aenean consectetur justo non felis varius, eu fermentum mi fermentum. Ut ac dui ligula.
For more information please visit [url]http://github.com/DavidRockin[/url]
HTML;

echo $podiya->callEvent("create_post", "David", "Administrator", $sampleMessage, time()) . 
	$podiya->callEvent("create_post", "John Doe", "Moderator", $sampleMessage, strtotime("-3 days")) .
	$podiya->callEvent("create_post", "AppleJuice", "Member", $sampleMessage, strtotime("-3 weeks")) .
	$podiya->callEvent("create_post", "Anonymous", "Donator", $sampleMessage, strtotime("-3 years"));
