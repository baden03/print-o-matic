=== Print-O-Matic ===
Contributors: twinpictures, baden03
Donate link: https://pluginoven.com/panares-fund/
Tags: print, print element, print shortcode, send to print, print button, print me, jQuery, print page, javascript, twinpictures, plugin oven
Requires at least: 5.0
Tested up to: 7.1
Stable tag: 2.1.12
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Adds the ability to target print elements using a simple [print-me] shortcode. Extra jQuery Jedi love given to filled out forms.

== Description ==
Print-O-Matic adds the ability to print any post, page or page element by using a simple [print-me] shortcode. A <a href='https://pluginoven.com/plugins/print-o-matic/documentation/shortcode/'>complete listing of shortcode options</a> are available. Bugs and feature requests are tracked in <a href='https://github.com/baden03/print-o-matic/issues'>GitHub issues</a>.

== Installation ==

1. Download the latest `print-o-matic-x.x.x.zip` from <a href="https://github.com/baden03/print-o-matic/releases">GitHub Releases</a> and upload it via Plugins → Add New → Upload Plugin. Old-school: unzip and drop the `print-o-matic` folder into `/wp-content/plugins/` via FTP.
1. Activate the Plug-in
1. Add a the shortcode to your post like so: `[print-me target="div#id_of_element_to_print" title="Print Form"]`
1. Test that the this plug-in meets your demanding needs.
1. Tweak the CSS to match your flavor.
1. Report bugs, feature requests, and cocktail recipes at https://github.com/baden03/print-o-matic/issues

== Frequently Asked Questions ==

= Where can I fork this plugin, report a bug, or contribute changes? =
<a href='https://github.com/baden03/print-o-matic'>github</a>, and bugs or feature requests belong in <a href='https://github.com/baden03/print-o-matic/issues'>github issues</a>.

= I am a Social Netwookiee, might Twinpictures have a Facebook page? =
Nope.

= Does Twinpictures do the Twitter? =
Nope.

= How does one use the shortcode, exactly? =
A <a href='https://pluginoven.com/plugins/print-o-matic/documentation/'>complete listing of shortcode options</a> has been provided to answer this exact question.

= Where can I translate this plugin into my favorite language? =
A .pot file has been included in the languages folder, along with a German (de_DE) translation. Pull requests with new translations are welcome.

= Who likes to rock the party? =
We like to rock the party.

== Screenshots ==
1. See the printer icon? Guess what happens when it's clicked?
1. Print-O-Matic Options screen for Ultimate Flexibility

== Changelog ==

= 2.1.12 =
* distribution: GitHub Actions now deprecates Node.js 20; the 2.1.11 release used `actions/checkout@v4` and could not be re-run with a newer script, because tag builds use the workflow file from that tag
* distribution: updated the release workflow to `actions/checkout@v5` and `softprops/action-gh-release@v3` so GitHub Releases build on Node.js 24

= 2.1.11 =
* security: restricted the shortcode's `tag` attribute to a safe allow-list of HTML tags
* security: sanitized the shortcode `id` used as an inline JS variable name, to prevent script breakout via a crafted `id` attribute
* security: removed `eval()` use in printomat.js in favor of a direct property lookup
* security: escaped the printer icon, print target, print title, do-not-print and pause-time values on the options page (they were being passed through the translation functions instead of being escaped)
* security: added a direct-file-access guard
* fix: "Only load scripts with shortcode" checkbox rendered a duplicate `checked` attribute
* fix: per-shortcode print data is now keyed on `window`, so shortcode ids containing a hyphen no longer emit invalid JavaScript
* fix: no longer emits an empty per-shortcode print data object when no overrides are set
* fix: `%ID%` target replacement and the shortcode return value no longer warn when used outside the loop (PHP 8)
* fix: corrected the `print-o-mat` text domain typo in the "Level Up!" box
* i18n: removed hard-coded links from translatable strings; link markup is now passed as printf arguments
* i18n: added translator comments and numbered placeholders (%1$s/%2$s) to every string containing dynamic markup
* i18n: added the missing 'print-o-matic' text domain to the options page and menu titles, "Save Changes", "About" and "Click to toggle"
* i18n: added a `languages` directory with a generated `print-o-matic.pot` template, and a `Domain Path` header
* i18n: added a complete German (de_DE) translation, shipped as both .mo and the faster .l10n.php format WordPress 6.5+ prefers
* i18n: text domain is now registered on `init` rather than `plugins_loaded`, per the WordPress 6.7+ translation loading guidance
* support has moved to GitHub issues; removed all wordpress.org references from the plugin and documentation
* removed the "please review this plugin" line from the options page, along with the random compliment list that fed it
* distribution: pushing a version tag now publishes a GitHub Release with a clean `print-o-matic-x.x.x.zip`
* distribution: blueprint.json installs from the GitHub repository
* tested up to WordPress 7.1

= 2.1.10 =
* removed pause before print
* fixed issue of not printing correctly in safari

= 2.1.9 =
* now escapes only the title attribute value, not the entire attribute string

= 2.1.8 =
* security update. Now the plugin is escaping all shortcode attributes before output.

= 2.1.7 =
* re-added method of passing default and print-trigger specific data to js script using wp_add_inline_script
* added a bit of pause to allow for top and bot html to fully load
* pause before print now is for adjustingg the amount of time to let the print preview render before reverting back to display layout

= 2.1.6 =
* pause before print is now pause time to allow the browser to render the print-preview before resetting back to display layout

= 2.1.5 =
* reverted back to wp_localize_script to pass print data to js script
* moved pause before print to the post print cleanup
* fully tested with version 6.0

= 2.1.4 =
* try and force lazy load images to load before print

= 2.1.3 =
* improved method of passing default and print-trigger specific data to js script using wp_add_inline_script
* added line numbers back to CodeMirror, with admin css

= 2.1.2 =
* custom html settings in the plugin options also use CodeMirror
* no longer escaping print css before being passed to wp_add_inline_style
* removed line numbers from CodeMirror elements
* fixed issue with only first element being printed

= 2.1.1 =
* added back code to printomat.js

= 2.1.0 =
* prevent default on print triggers
* rolled back tested tag to 5.8.1 as only RC1 versions are apparently accepted
* more escaping for security
* tightened up the code a bit
* custom css sections of plugin options page now use CodeMirror
* improved method of adding custom css using wp_add_inline_style

= 2.0.3 =
* updated link to documentation
* textarea input fields now escaped using esc_textarea() on plugin options page

= 2.0.2 =
* targets are validated before print
* field values in the plugin options page are now escaped using esc_attr()  

= 2.0.1 =
* added ability to define [print target by class](https://spacedonkey.de/4188/print-o-matic-external-print-trigger-target-by-class/)
* patched xss security issue

= 2.0 =
* complete re-write using new print-elements method by [@szepeshazi](https://github.com/szepeshazi/print-elements)
* fully tested with WordPress 5.8.1

= 1.7.12 =
* Fully tested with WordPress 5.2.1
* print window opens in new tab
* uses wp_localize to pass variables to script
* added option to include print-me scripts in admin dashboard
* added reviver.lt's Edge fix

= 1.7.11 =
* Fully tested with WordPress 5.1
* Fixed typos

= 1.7.10 =
* Fully tested with WordPress 4.9.1
* Added default print title
* IE bug fix: select elements now print selected values

= 1.7.9 =
* Fully tested with WordPress 4.8

= 1.7.8 =
* Safari on iOS will now rendering print preview correctly when close after print is used
* fixed link to twinpictures author website

= 1.7.7 =
* tested with WordPress 4.7.3
* replaced http links with https

= 1.7.6 =
* top and bottom print page html now use do_shortcode instead of the_content filter to avoid conflicts with social sharing plugins
* fixed error of extra character in the title tag.

= 1.7.5 =
* added missing alt tag
* fully tested with WordPress 4.6

= 1.7.4 =
* added missing alt tag
* reworked script to build new window more efficiently
* working (not fully resolved) on Edge. Some issues will are related to a bug in Edge.

= 1.7.3 =
* top and bottom html will process shortcakes
* work around to clone IE element values that don’t have ID attributes… ugh
* added option to close the print window after print dialogue box is closed
* work around IE syntax errors when NO form elements are present… double ugh

= 1.7.2 =
* added IE hack to fill in missing input text values for IE browsers.

= 1.7.1 =
* checks for older IE MSIE, IE 11 Trident & IE 12 Edge properly

= 1.7 =
* checks for IE (MSIE, Trident & Edge)
* plugin fully tested with WordPress 4.5
* added external printstyle for external triggers
* print window no longer auto-closes
* removed language files in favour of WordPress Language Packs

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
* security hardening: tag allow-list, sanitized inline JS variable ids, removed eval(), escaped options-page output
* tested up to WordPress 7.1

