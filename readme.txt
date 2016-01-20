=== Print-O-Matic ===
Contributors: twinpictures, baden03
Donate link: http://plugins.twinpictures.de/flying-houseboat/
Tags: print, print element, print shortcode, send to print, print button, print me, jQuery, print page, javascript, twinpictures, plugin oven
Requires at least: 4.0
Tested up to: 4.4.1
Stable tag: 1.6.7b
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds the ability to print any post or post element using a simple [print-me] shortcode. Extra jQuery Jedi love given to filled out forms.

== Description ==
Print-O-Matic adds the ability to print any post, page or page element by using a simple [print-me] shortcode.  Bonus feature: filled out form elements are also printed using a kind of jQuery Jedi magic.  A <a href='http://plugins.twinpictures.de/plugins/print-o-matic/documentation/'>complete listing of shortcode options</a> are available, as well as <a href='http://wordpress.org/support/plugin/print-o-matic'>free community support</a>.

== Installation ==

1. Old-school: upload the `print-o-matic` folder to the `/wp-content/plug-ins/` directory via FTP.  Hipster: Ironically add Print-O-Matic via the WordPress Plug-ins menu.
1. Activate the Plug-in
1. Add a the shortcode to your post like so: `[print-me target="div#id_of_element_to_print" title="Print Form"]`
1. Test that the this plug-in meets your demanding needs.
1. Tweak the CSS to match your flavor.
1. Rate the plug-in and verify if it works at wordpress.org.
1. Leave a comment regarding bugs, feature request, cocktail recipes at http://wordpress.org/tags/print-o-matic/

== Frequently Asked Questions ==

= Where can I fork this plugin and contribute changes? =
<a href='https://github.com/baden03/print-o-matic'>github</a>

= I am a Social Netwookiee, might Twinpictures have a Facebook page? =
Yes, yes... <a href='http://www.facebook.com/twinpictures'>Twinpictures is on Facebook</a>.

= Does Twinpictures do the Twitter? =
Ah yes! <a href='http://twitter.com/#!/twinpictures'>@Twinpictures</a> does the twitter tweeting around here.

= How does one use the shortcode, exactly? =
A <a href='http://plugins.twinpictures.de/plugins/print-o-matic/documentation/'>complete listing of shortcode options</a> has been provided to answer this exact question.

= Where can I translate this plugin into my favorite language? =
<a href='http://translate.twinpictures.de/projects/printomat/'>Community translation for Print-O-Matic</a> has been set up. You are <a href='http://translate.twinpictures.de/wordpress/wp-login.php?action=register'>welcome to join</a>.

= Who likes to rock the party? =
We like to rock the party.

== Screenshots ==
1. See the printer icon? Guess what happens when it's clicked?
1. Print-O-Matic Options screen for Ultimate Flexibility

== Changelog ==

= 1.6.7 =
* tweaks that address Microsoft Edge issues (no surprise there, really)
* plugin fully tested with WordPress 4.4.1

= 1.6.6 =
* plugin fully tested with WordPress 4.4.0

= 1.6.5 =
* adjusted method of determining if the print_data object exists and has property
* adjusted the language domain to work with WordPress’ new language translation system

= 1.6.4 =
* addressed move lovely IE issues
* mega hack-o-riffic workaround for IE input text elements loosing user input values

= 1.6.3 =
* fixed bug with title not passing target correctly when displaying both icon and title

= 1.6.2 =
* fixed issue with printstyle default value not saving
* deactivating plugin no longer clears all settings

= 1.6.1 =
* corrected typo in printstyle attribute

= 1.6.0 =
* added tag and class attributes
* target now passed using data attribute rather than hidden input field

= 1.5.7 =
* added pause_before_print attribute and option to manually pause the print dialogue box to let the page fully load.

= 1.5.6 =
* typo correction
* replaced redundant inline scripts with smarter print_data js object placed in footer
* added icons for WordPress 4.0

= 1.5.5 =
* Added some Internet Explorer workarounds
* Added optional jQuery clone.fix to address issues with cloning textarea elements
* Added %prev% and %next% target placeholders to print elements immediately preceding or following the print button
* Added 3 second delay if iframe is detected in print page
* Added Russian language translation

= 1.5.4 =
* load scripts option now defaults to always
* improvements for roll-your-own print-o-matic elements
* added german and hungarian translations

= 1.5.3 =
* works also in IE 11
* print command now waits for page to fully load
* added option to load scrips only on pages where shortcode is used

= 1.5.2 =
* fixed bug with do_not_print
* added alt attribute
* works again in horrid IE browsers

= 1.5.1 =
* added the do_not_print attribute 

= 1.5 =
* added print page top and bottom HTML section (special thanks to Daniel Kevin Johansen @ 555Haxor.dk & Game-Site.dk haxor5552@hotmail.com)
* added option to select alternate print icons
* can now show icon, text and icon & text print button
* form values now include radio and checkbox selections
* removed php4 constructors
* added I18n localization support
* fixed printicon attribute bug in shortcode
* added custom css for display page as well as print page

= 1.4 =
* target may now use %ID% as a placeholder for the post ID

= 1.3 =
* Fixed so the print dialog box will display in IE (buggy, buggy IE)

= 1.2 =
* Added Printicon Attribute to insert text-only print link

= 1.1 =
* Added Options page with default target attribute and css style settings

= 1.0.1 =
* Removed space from title of new window to prevent the wonderful IE8 from throwing errors.

= 1.0 =
* The plug-in was forked and completely rewritten from Print Button Shortcode by MyWebsiteAdvisor.

== Upgrade Notice ==
plugin is fully tested with WordPress 4.4.0. Notice of print-pro-magic price increase for 2016.
