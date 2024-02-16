Handschrift's Discord Integration
Contributors: @Handschrift
Donate link: https://de.liberapay.com/Handschrift/donate
Tags: Discord
Requires at least: 6.0
Tested up to: 6.1
Requires PHP: 8.0
Stable tag: 0.1.2
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

A plugin which adds integrations for your discord server and your WordPress webpage.

== Description ==
This plugin provides a simple integration for your discord server.
Currently, it supports the following features:
* Being able to log in via discord to your WordPress webpage
* Map roles from your WordPress webpage to your discord server
== Configuring the plugin ==
You have to do some configuration to set up the plugin properly.
First, you have to create a new app at [Discord](https://discord.dev) to retrieve a clientid and a client secret. This is needed to enable the discord login functionality.
To enable the WordPress to discord role mapping, you also have to create a bot token and provide it in the settings of the plugin.
== Using the plugin ==
After installing the plugin and doing the required configuration, a button to login via discord will be visible on the login form.
You can also place a button with the following shortcode:
`[discord_login_button text="Your text here"]`
== Development ==
This is an open source plugin. You can feel free to contribute via [GitLab](https://gitlab.com/Schreibschrift/Wordpress-Discord-Integration)
== Support ==
If you encounter any bugs you can either send a review or open an issue on GitLab (which is the much preferred way)
