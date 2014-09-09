Podiya
======

Podiya (Ukrainian for 'event') is a PHP library that provides a simple and easy to use functionality that allows you to create events (or hooks) that are handled by listeners. Podiya can be used in an application to allow other developers create plugins that can extend and/or improve the functionality of the application.
[![Latest Stable Version](https://poser.pugx.org/davidrockin/podiya/v/stable.svg)](https://packagist.org/packages/davidrockin/podiya) [![Total Downloads](https://poser.pugx.org/davidrockin/podiya/downloads.svg)](https://packagist.org/packages/davidrockin/podiya) [![Latest Unstable Version](https://poser.pugx.org/davidrockin/podiya/v/unstable.svg)](https://packagist.org/packages/davidrockin/podiya) [![License](https://poser.pugx.org/davidrockin/podiya/license.svg)](https://packagist.org/packages/davidrockin/podiya)

Events
------

Events (or hooks) are thrown by the application, in which listeners (from plugins) handle the events. For example, if the application were to display a blog post, the application would make several event calls that will be handled by listeners to display the post, comments, and author information.
