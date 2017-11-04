=== Add Admin CSS ===
Contributors: coffee2code
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6ARCFJ9TX3522
Tags: admin, css, style, stylesheets, admin theme, customize, coffee2code
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 4.6
Tested up to: 4.9
Stable tag: 1.6

Interface for easily defining additional CSS (inline and/or by URL) to be added to all administration pages.


== Description ==

Ever want to tweak the appearance of the WordPress admin pages by hiding stuff, moving stuff around, changing fonts, colors, sizes, etc?  Any modification you may want to do with CSS can easily be done via this plugin.

Using this plugin you'll easily be able to define additional CSS (inline and/or files by URL) to be added to all administration pages. You can define CSS to appear inline in the admin head (within style tags), or reference CSS files to be linked (via "link rel='stylesheet'" tags). The referenced CSS files will appear in the admin head first, listed in the order defined in the plugin's settings. Then any inline CSS are added to the admin head. Both values can be filtered for advanced customization (see Advanced section).

Links: [Plugin Homepage](http://coffee2code.com/wp-plugins/add-admin-css/) | [Plugin Directory Page](https://wordpress.org/plugins/add-admin-css/) | [Author Homepage](http://coffee2code.com)


== Installation ==

1. Unzip `add-admin-css.zip` inside the `/wp-content/plugins/` directory for your site (or install via the built-in WordPress plugin installer)
1. Activate the plugin through the 'Plugins' admin menu in WordPress
1. Go to "Appearance" -> "Admin CSS" and add some CSS to be added into all admin pages.


== Frequently Asked Questions ==

= Can I add CSS I defined via a file, or one that is hosted elsewhere? =

Yes, via the "Admin CSS Files" input field on the plugin's settings page.

= Can I limit what admin pages the CSS gets output on? =

No, not presently. At least not directly. By default, the CSS is added for every admin page on the site.

However, you can preface your selectors with admin page specific class(es) on 'body' tag to ensure CSS only applies on certain admin pages. (e.g. `body.index-php h2, #icon-index { display: none; }`).

Or, you can hook the 'c2c_add_admin_css' and 'c2c_add_admin_css_files' filters and determine the current admin page content to decide whether the respective hook argument should be returned (and thus output) or not.

= Can I limit what users the CSS applies to? =

No, not presently. At least not directly. By default, the CSS is added for any user that can enter the admin section of the site.

You can hook the 'c2c_add_admin_css' and 'c2c_add_admin_css_files' filters and determine the current user to decide whether the respective hook argument should be returned (and thus output) for the user or not.

= How do I disable syntax highlighting? =

The plugin's syntax highlighting of CSS (available on WP 4.9+) honors the built-in setting for whether syntax highlighting should be enabled or not.

To disable syntax highlighting, go to your profile page. Next to "Syntax Highlighting", click the checkbox labeled "Disable syntax highlighting when editing code". Note that this checkbox disables syntax highlighting throughout the admin interface and not just specifically for the plugin's settings page.

= Does this plugin include unit tests? =

Yes.


== Screenshots ==

1. A screenshot of the plugin's admin settings page.


== Advanced ==

You can also programmatically add to or customize any CSS defined in the "Admin CSS" field via the c2c_add_admin_css filter, like so:

`
/**
 * Add CSS to admin pages.
 *
 * @param string $css String to be added to admin pages.
 * @return string
 */
function my_admin_css( $css ) {
	$css .= "
		#site-heading a span { color:blue !important; }
		#favorite-actions { display:none; }
	";
	return $css;
}
add_filter( 'c2c_add_admin_css', 'my_admin_css' );
`

You can also programmatically add to or customize any referenced CSS files defined in the "Admin CSS Files" field via the c2c_add_admin_css_files filter, like so:

`
/**
 * Add CSS file(s) to admin pages.
 *
 * @param array $files CSS files to be added to admin pages.
 * @return array
 */
function my_admin_css_files( $files ) {
	$files[] = 'http://yui.yahooapis.com/2.9.0/build/reset/reset-min.css';
	return $files;
}
add_filter( 'c2c_add_admin_css_files', 'my_admin_css_files' );
`


== Changelog ==

= 1.6 (2017-11-03) =
* New: Add support for CodeMirror (as packaged with WP 4.9)
    * Adds code highlighting, syntax checking, and other features
* Fix: Show admin notifications for settings page
* Change: Update plugin framework to 046
    * 046:
    * Fix `reset_options()` to reference instance variable `$options`.
	* Note compatibility through WP 4.7+.
	* Update copyright date (2017)
    * 045:
    * Ensure `reset_options()` resets values saved in the database.
    * 044:
    * Add `reset_caches()` to clear caches and memoized data. Use it in `reset_options()` and `verify_config()`.
    * Add `verify_options()` with logic extracted from `verify_config()` for initializing default option attributes.
    * Add `add_option()` to add a new option to the plugin's configuration.
    * Add filter 'sanitized_option_names' to allow modifying the list of whitelisted option names.
    * Change: Refactor `get_option_names()`.
    * 043:
    * Disregard invalid lines supplied as part of hash option value.
    * 042:
    * Update `disable_update_check()` to check for HTTP and HTTPS for plugin update check API URL.
    * Translate "Donate" in footer message.
* Change: Update unit test bootstrap
    * Default `WP_TESTS_DIR` to `/tmp/wordpress-tests-lib` rather than erroring out if not defined via environment variable
    * Enable more error output for unit tests
* Change: Note compatibility through WP 4.9+
* Change: Remove support for WordPress older than 4.6
* Change: Update copyright date (2018)

= 1.5 (2016-04-21) =
* Change: Declare class as final.
* Change: Update plugin framework to 041:
    * For a setting that is of datatype array, ensure its default value is an array.
    * Make `verify_config()` public.
    * Use `<p class="description">` for input field help text instead of custom styled span.
    * Remove output of markup for adding icon to setting page header.
    * Remove styling for .c2c-input-help.
    * Add braces around the few remaining single line conditionals.
* Change: Note compatibility through WP 4.5+.
* Change: Remove 'Domain Path' from plugin header.
* New: Add LICENSE file.

= 1.4 (2016-01-10) =
Highlights:
* This release fixes a couple of bugs, adds support for language packs, and has many minor behind-the-scenes changes.

Details:
* Bugfix: Allow CSS links/files with query args to be enqueued.
* Bugfix: If specified as part of the link, 'ver' query arg takes precedence as script version.
* Add: More unit tests.
* Add: Amend a couple of the FAQ answers to indicate things are possible via hooks rather than suggesting they aren't possible.
* Change: Update plugin framework to 040:
    * Change class name to c2c_AddAdminCSS_Plugin_040 to be plugin-specific.
    * Set textdomain using a string instead of a variable.
    * Don't load textdomain from file.
    * Change admin page header from 'h2' to 'h1' tag.
    * Add `c2c_plugin_version()`.
    * Formatting improvements to inline docs.
* Change: Add support for language packs:
    * Set textdomain using a string instead of a variable.
    * Remove .pot file and /lang subdirectory.
* Change: Declare class as final.
* Change: Explicitly declare methods in unit tests as public or protected.
* Change: Minor tweak to description.
* Change: Minor improvements to inline docs and test docs.
* Add: Create empty index.php to prevent files from being listed if web server has enabled directory listings.
* Change: Note compatibility through WP 4.4+.
* Change: Remove support for versions of WordPress older than 4.1.
* Change: Update copyright date (2016).

= 1.3.4 (2015-04-30) =
* Bugfix: Fix line-wrapping display for Firefox and Safari
* Enhancement: Add an additional unit test
* Update: Move 'Advanced' section lower in readme; add inline docs to example code
* Update: Note compatibility through WP 4.2+

= 1.3.3 (2015-02-21) =
* Bugfix: Revert back to using `dirname(__FILE__)`; `__DIR__` is only PHP 5.3+

= 1.3.2 (2015-02-16) =
* Update plugin framework to 039
* Add to and improve unit tests
* Explicitly declare class method `activation()` and `uninstall()` static
* Use __DIR__ instead of `dirname(__FILE__)`
* Various inline code documentation improvements (spacing, punctuation)
* Note compatibility through WP 4.1+
* Update copyright date (2015)
* Regenerate .pot

= 1.3.1 (2014-08-23) =
* Update plugin framework to 038
* Minor plugin header reformatting
* Minor code reformatting (spacing, bracing)
* Change documentation links to wp.org to be https
* Localize an additional string
* Note compatibility through WP 4.0+
* Regenerate .pot
* Add plugin icon

= 1.3 (2014-01-03) =
* Add unit tests
* Update plugin framework to 036
* Improve URL path construction
* Use explicit path for require_once()
* Add reset() to reset object to its initial state
* Remove __clone() and __wake() since they are part of framework
* For options_page_description(), match method signature of parent class
* Note compatibility through WP 3.8+
* Drop compatibility with versions of WP older than 3.5
* Update copyright date (2014)
* Change donate link
* Minor readme.txt tweaks (mostly spacing)
* Add banner
* Update screenshot

= 1.2 =
* Move 'Advanced Tips' section from bottom of settings page into contextual help section
* Add `help_tabs_content()` and `contextual_help()`
* Prevent textareas from wrapping lines
* Display fonts properly in textareas
* Change input fields to be displayed as inline_textarea instead of textarea
* Add `instance()` static method for returning/creating singleton instance
* Made static variable 'instance' private
* Add dummy `__clone()` and `__wakeup()`
* Remove support for previously deprecated 'c2c_add_admin_css' global
* Remove `c2c_AddAdminCSS()`; only PHP5 constructor is supported now
* Update plugin framework to 035
* Discontinue use of explicit pass-by-reference for objects
* Add check to prevent execution of code if file is directly accessed
* Regenerate .pot
* Re-license as GPLv2 or later (from X11)
* Add 'License' and 'License URI' header tags to readme.txt and plugin file
* Minor documentation improvements
* Note compatibility through WP 3.5+
* Drop compatibility versions of WP older than 3.1
* Update copyright date (2013)
* Minor code reformatting (spacing)
* Remove ending PHP close tag
* Create repo's WP.org assets directory
* Move screenshot into repo's assets directory

= 1.1 =
* Rename class from 'AddAdminCSS' to 'c2c_AddAdminCSS'
* Rename filter from 'add_admin_css' to 'c2c_add_admin_css'
* Rename filter from 'add_admin_css_files' to 'c2c_add_admin_css_files'
* Update plugin framework to 029
* Save a static version of itself in class variable $instance
* Deprecate use of global variable $c2c_add_admin_css to store instance
* Explicitly declare all functions as public
* Add __construct(), activation(), and uninstall()
* Note compatibility through WP 3.3+
* Drop compatibility with versions of WP older than 3.0
* Add .pot
* Add 'Domain Path' plugin header
* Minor code formatting changes (spacing)
* Update copyright date (2011)
* Add plugin homepage and author links in description in readme.txt

= 1.0 =
* Initial release (not publicly released)


== Upgrade Notice ==

= 1.6 =
Recommended update: added code highlighting, syntax checking, etc as introduced elsewhere in WP 4.9; show admin notifications for settings page; updated plugin framework to version 046; verified compatibility through WP 4.9; dropped compatibility with versions of WordPress older than 4.6; updated copyright date (2018).

= 1.5 =
Minor update: updated plugin framework to version 041; verified compatibility through WP 4.5.

= 1.4 =
Recommended update: bugfixes for CSS file links containing query arguments; improved support for localization; verified compatibility through WP 4.4; removed compatibility with WP earlier than 4.1; updated copyright date (2016)

= 1.3.4 =
Bugfix release: fixed line-wrapping display for Firefox and Safari; noted compatibility through WP 4.2+.

= 1.3.3 =
Bugfix release: reverted use of __DIR__ constant since it isn't supported on older installations (PHP 5.2).

= 1.3.2 =
Trivial update: improvements to unit tests; updated plugin framework to version 039; noted compatibility through WP 4.1+; updated copyright date (2015).

= 1.3.1 =
Trivial update: updated plugin framework to version 038; noted compatibility through WP 4.0+; added plugin icon.

= 1.3 =
Minor update: added unit tests; minor improvements; noted compatibility through WP 3.8+.

= 1.2 =
Recommended update. Highlights: stopped wrapping long input field text; updated plugin framework; updated WP compatibility as 3.1 - 3.5+; explicitly stated license; and more.

= 1.1 =
Recommended update: renamed class and filters by prefixing 'c2c_'; noted compatibility through WP 3.3; dropped support for versions of WP older than 3.0; updated plugin framework; deprecate global variable.

= 1.0 =
Initial public release!
