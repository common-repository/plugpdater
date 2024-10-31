=== Plugpdater ===
Contributors: imath
Donate link: http://imath.owni.fr/
Tags: plugins, update, network, admin
Requires at least: 3.0
Tested up to: 3.1.1
Stable tag: 0.1

Plugpdater makes it easier to locally/manually update plugins.

== Description ==

If like me you run an Intranet, firewalls or proxies do not allow you to contact the WordPress.org API, so the only way to have your plugins up to date is to manually delete an old version of a plugin before uploading the new version of it. Plugpdater eases this process by uploading the plugin in a temp directory, checking for errors, and if none were found replacing the old version by the new one.
As the plugin will write and replace files in /wp-content/uploads and /wp-content/plugins, make sure apache user has sufficient rights to do it.

* <u>Important</u> : this plugin needs to be tested on a testing environment before using it on a production environment.

== Installation ==

You can download and install Plugpdater using the built in WordPress plugin installer. If you download Plugpdater manually, make sure it is uploaded to "/wp-content/plugins/plugpdater/".

Activate Plugpdater in the "Plugins" admin panel using the "Activate" link or "Network activate" if you run a network of blogs (multisite).

== Frequently Asked Questions ==

= If you have any question =

Please add a comment <a href="http://imath.owni.fr/2011/04/25/plugpdater/">here</a> or use this plugin forum.

== Screenshots ==

1. main screen of the plugin.
2. plugin upload screen.
3. updating succeeded
4. Warning screen if an error is found

== Changelog ==

= 0.1 =

* Plugin birth..

== Upgrade Notice ==

= 0.1 =
no upgrades, just a beta version.