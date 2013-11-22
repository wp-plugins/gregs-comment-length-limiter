=== Greg's Comment Length Limiter ===
Contributors: GregMulhauser
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=2799661
Tags: comments, comment length, length limit, comment size, size limit, comments.php, greg mulhauser, seo, paged comments, javascript, performance, loading time, AJAX, spam, comment spam, anti-spam
Requires at least: 2.7
Tested up to: 3.8-beta-1
Stable tag: 1.5.9
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Provides a configurable limit on the length of comments left in the comment form, with a dynamically updated character count displayed for the user.

== Description ==

**NOTE:** Please do NOT use the WordPress forums to seek support for this plugin. Support for GCLL is handled on [our own site](http://gregsplugins.com/lib/faq/).

This plugin provides a configurable limit on the length of comments left in the comment form, with a character countdown displayed for the user and dynamically updated with each keypress.

= New in This Version =

* Dropped compatibility with very old versions of PHP no longer supported by WordPress.

= Background =

Most of us welcome comments on our blog posts, but that doesn't necessarily mean that we -- or our readers -- like to see comments reaching into thousands of words. Readers may be discouraged from commenting themselves (or reading comments at all) when they see very long entries left by others, and during times of high load, the significant performance overheads associated with retrieving and displaying long comments can slow even the speediest dedicated servers.

From the perspective of SEO (search engine optimization), very long comments also dilute the impact of the author's original post by relegating it to a small proportion of the overall content available on the page. For advanced SEO capabilities, see [Greg's High Performance SEO Plugin](http://gregsplugins.com/lib/plugin-details/gregs-high-performance-seo/).

The primary approach to limiting the total volume of comment material displayed on a page has long been to break up comments across several pages -- either via a plugin or now with the built-in paged comment feature introduced in WordPress 2.7.

This plugin provides one more tool by directly limiting the length of any one comment. A lightweight JavaScript counter -- just 5 lines of inline JavaScript, with no gigantic external AJAX libraries to load -- lets the user know how many characters they have left to complete their entry. Any additional text which might be inserted beyond the configured limit via cutting and pasting is automatically trimmed to length.

For users without JavaScript, the counter degrades gracefully, providing a visual indication of the preferred (but unenforced) length limit, without the countdown feature.

For coders, the plugin provides additional configuration options via direct calls.

== Installation ==

**NOTE:** Please do NOT use the WordPress forums to seek support for this plugin. Support for GCLL is handled on [our own site](http://gregsplugins.com/lib/faq/).

1. Unzip the plugin archive
2. Upload the entire folder `gregs-comment-length-limiter` to `/wp-content/plugins/`
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Go to Settings -> Comment Length Limiter to configure your preferences
5. Update your template's `comments.php` with one or two function calls, as described below

= Usage =

With just a single line of code -- or none at all -- most themes which support WordPress 2.7 will also support the comment length limit provided by this plugin. (Note that themes must include the `wp_footer()` call in order for this plugin -- and many others out there -- to work correctly.)

*WordPress 3.0 and Themes Using the New Comment Form Function*

Under WordPress 3.0 and later, the entire comment form can be generated via a single call to the function `comment_form()`. If your theme uses the new `comment_form`, this plugin can automatically tweak the form so comments can be counted. The plugin filters the `comment_field` default and inserts the countdown box just after the `label` tag for the comment area. If you'd rather position the countdown box yourself, you can do so by tapping into any of the standard comment form filters and inserting the value returned by `gcll_show_limit_box_for_filtering()`.

If your theme does not use the new 3.0 functionality, please use the instructions in the following section.

*Wordpress 2.9.2 and Earlier, And 3.0+ Themes Without the New Comment Form Functionality*

Preparing your comment form for length limiting is still very straightforward:

* Add the following function call within the textarea tag for your comment area, preferably wrapped in a conditional that tests whether the function exists: `<?php gcll_tweak_textarea(); ?>`

For example, here is how the WordPress 2.7 default theme's comment textarea looks after updating the tag to support Greg's Comment Length Limiter plugin:

`<p><textarea name="comment" id="comment" cols="100%" rows="10" tabindex="4" <?php if (function_exists('gcll_tweak_textarea')) gcll_tweak_textarea(); ?>></textarea></p>`

Optionally, if you would like to position the countdown box yourself, add the following function call wherever you would like the box to appear within your comment form, again preferably wrapped in a conditional that tests whether the function exists:

`<?php gcll_show_limit_box_manually(); ?>`

The plugin settings page allows you to specify whether you would like the plugin to place the countdown box automatically, or whether you would prefer to do that yourself. If you specify that the plugin should place the countdown box automatically, that setting will apply even if you also include manual placement code within your theme -- in other words, the plugin is intelligent enough not to attempt to do it twice. So, if you'd like to compare your placement with the default placement, you can flip the manual placement setting on and off and view the results, without repeatedly adding and deleting the call from your theme.

*Manual Positioning Via Direct Calls*

For styling purposes, manual calls to display the countdown box, using `<?php gcll_show_limit_box_manually(); ?>` or `<?php gcll_show_limit_box_for_filtering(); ?>`, can also take one parameter specifying your preferred class for the `<span>` which encloses the box: `<?php gcll_show_limit_box_manually('mypreferredclass'); ?>`.

The default `<span>` class is `countdownbox`.

*Safe Wrapping of Plugin-Dependent Function Calls*

I cover safe wrapping of plugin-dependent function calls on the [Greg's Plugins FAQ](http://gregsplugins.com/lib/faq/).

= Deactivating and Uninstalling =

You can deactivate Greg's Comment Length Limiter plugin via the plugins administration page, and your preferences will be saved for the next time you enable it.

However, if you would like to remove the plugin completely, just disable it via the plugins administration page, then select it from the list of recently deactivated plugins and choose "Delete" from the admin menu. This will not only delete the plugin, but it will also run a special routine included with the plugin which will completely remove its preferences from the database.

== Frequently Asked Questions ==

**NOTE:** Please do NOT use the WordPress forums to seek support for this plugin. Support for GCLL is handled on [our own site](http://gregsplugins.com/lib/faq/).

== Screenshots ==

1. Basic comment length limiter configuration options
2. Comment length limiter plugin in use with the default theme

== Upgrade Notice ==

= 1.5.9, 22 November 2013 =
* Dropped compatibility with very old versions of PHP no longer supported by WordPress.

== Changelog ==

= 1.5.9, 22 November 2013 =
* Dropped compatibility with very old versions of PHP no longer supported by WordPress.

= 1.5.8, 21 September 2013 =
* Updated WordPress version compatibility.

= 1.5.7, 15 December 2012 =
* Replaced some ancient admin page code to enable loading the plugin through a symbolic link.
* Confirmed 3.5 compatibility.

= 1.5.6, 26 November 2011 =
* Removed PluginSponsors.com code following threats that the plugin would be expelled from the plugin repository for using the code to display sponsorship messages

= 1.5.5, 27 October 2011 =
* Documentation updates

= 1.5.4, 3 October 2011 =
* Minor code cleanups
* Tweak to accommodate automatic placement within Twenty Eleven's pixel-level hard-coded comment form styling. (Remember, the countdown box is available for styling yourself, so please do modify how you would like to appear with your own theme: it is not possible to include universal CSS that works with every theme available.)

= 1.5.3, 5 Feburary 2011 =
* Better customisation options for WordPress 3.0+ `comment_form` filters

= 1.5.2, 29 January 2011 =
* Minor code cleanup
* Testing with WP 3.1 Release Candidate 3

= 1.5.1, 20 January 2011 =
* Minor code cleanup
* Testing with WP 3.1 Release Candidate 2

= 1.5, 12 September 2010 =
* Improved counter box placement options for WordPress 3.0's new comment_form function
* Improved counter removes dependence on an explicit 'name' attribute for the comment form, relying on 'id' instead
* Improved comment length accuracy for multi-byte character sets when using anti-spam comment trimming

= 1.4.2, 24 June 2010 =
* Better workaround for WordPress 3.0's problems initialising plugins properly under multisite

= 1.4.1, 24 June 2010 =
* Workaround for rare problem where WordPress interferes with a newly activated plugin's ability to add options when using multisite/network mode

= 1.4, 1 June 2010 =
* Major reduction in database footprint in preparation for WordPress 3.0

= 1.3, 24 May 2010
* Comment counter JavaScript is now added only to those pages which would normally load a comment form

= 1.2.9, 20 April 2010 =
* Minor code cleanups

= 1.2.8, 6 April 2010 =
* Enhanced admin pages now support user-configurable section boxes which can be re-ordered or closed

= 1.2.7, 10 February 2010 =
* Greatly improved spam handling: we now check the comment length after most other comment processes have had their crack at it, and we send an over-length comment to moderation queue only if no earlier process has marked it as spam

= 1.2.6, 12 January 2010 =
* Fully tested with 2.9.1 (no changes)

= 1.2.5, 10 November 2009 =
* Minor update to configuration pages
* Fully tested with 2.8.5 (no changes)

= 1.2.4, 17 August 2009 =
* Options page bugfix for users on old PHP4 installations

= 1.2.3, 12 August 2009 =
* Documentation tweaks
* Added support for [Plugin Sponsorship](http://pluginsponsors.com/)
* Fully tested with 2.8.4 (no changes)

= 1.2.2, 11 June 2009 =
* Fully tested with final release of WordPress 2.8

= 1.2.1, 15 April 2009 =
* Fixed a minor typo which would have interfered with translations for this plugin -- thanks to Nikolay

= 1.2, 1 April 2009 =
* This version brings higher performance, several minor enhancements, and a revamped administrative interface; it is recommended for all users.

= 1.1.1, 11 February 2009 =
* Now prevents Spam Karma 2 from overriding our choice to mark over-sized comments for moderation or to treat as spam

= 1.1, 10 February 2009 =
* As an anti-spam measure, if a user (or spambot) bypasses the JavaScript length limit, oversized comments can now be forcibly truncated, marked for moderation, marked as spam, or just passed through without modification.

= 1.0, 4 February 2009 =
* Initial public release

== Fine Print ==

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License version 3 as published by the Free Software Foundation.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.