=== Add Admin CSS ===
Contributors: coffee2code
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6ARCFJ9TX3522
Tags: admin, css, style, stylesheets, admin theme, customize, coffee2code
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 4.9
Tested up to: 5.7
Stable tag: 2.0.1

Easily define additional CSS (inline and/or by URL) to be added to all administration pages.


== Description ==

Ever want to tweak the appearance of the WordPress admin pages by hiding stuff, moving stuff around, changing fonts, colors, sizes, etc? Any modification you may want to do with CSS can easily be done via this plugin.

Using this plugin you'll easily be able to define additional CSS (inline and/or files by URL) to be added to all administration pages. You can define CSS to appear inline in the admin head (within style tags), or reference CSS files to be linked (via "link rel='stylesheet'" tags). The referenced CSS files will appear in the admin head first, listed in the order defined in the plugin's settings. Then any inline CSS are added to the admin head. Both values can be filtered for advanced customization (see Advanced section).

Links: [Plugin Homepage](https://coffee2code.com/wp-plugins/add-admin-css/) | [Plugin Directory Page](https://wordpress.org/plugins/add-admin-css/) | [GitHub](https://github.com/coffee2code/add-admin-css/) | [Author Homepage](https://coffee2code.com)


== Installation ==

1. Install via the built-in WordPress plugin installer. Or download and unzip `add-admin-css.zip` inside the plugins directory for your site (typically `wp-content/plugins/`)
1. Activate the plugin through the 'Plugins' admin menu in WordPress
1. Go to "Appearance" -> "Admin CSS" and specify some CSS to be added into all admin pages. (You can also use the "Settings" link in the plugin's entry on the admin "Plugins" page).


== Frequently Asked Questions ==

= Can I add CSS I defined via a file, or one that is hosted elsewhere? =

Yes, via the "Admin CSS Files" input field on the plugin's settings page.

= Can I limit what admin pages the CSS gets output on? =

No, not presently. At least not directly. By default, the CSS is added for every admin page on the site.

However, you can preface your selectors with admin page specific class(es) on the 'body' tag to ensure CSS only applies on certain admin pages. (e.g. `body.index-php h2, #icon-index { display: none; }`).

Or, you can hook the 'c2c_add_admin_css' and 'c2c_add_admin_css_files' filters and determine the current admin page context to decide whether the respective hook argument should be returned (and thus output) or not.

= Can I limit what users the CSS applies to? =

No, not presently. At least not directly. By default, the CSS is added for any user that can enter the admin section of the site.

You can hook the 'c2c_add_admin_css' and 'c2c_add_admin_css_files' filters and determine the current user to decide whether the respective hook argument should be returned (and thus output) for the user or not.

= How can I edit the plugin's settings in the event I supplied CSS that prevents the admin pages from properly functioning or being seen? =

It is certainly possible that you can put yourself in an unfortunate position by supplying CSS that could hide critical parts of admin pages, making it seeminly impossible to fix or revert your changes. Fortunately, there are a number of approaches you can take to correct the problem.

The recommended approach is to visit the URL for the plugin's settings page, but appended with a special query parameter to disable the output of its CSS. The plugin's settings page would typically be at a URL like `https://example.com/wp-admin/themes.php?page=add-admin-css%2Fadd-admin-css.php`. Append `&c2c-no-css=1` to that, so that the URL is `https://example.com/wp-admin/themes.php?page=add-admin-css%2Fadd-admin-css.php&c2c-no-css=1` (obviously change example.com with the domain name for your site).

There are other approaches you can use, though they require direct database or server filesystem access:

* Some browsers (such as Firefox, via View -> Page Style -> No Style) allow you to disable styles for sites loaded in that tab. Other browsers may also support such functionality natively or through an extension. Chrome has an extension called [Web Developer](https://chrome.google.com/webstore/detail/web-developer/bfbameneiokkgbdmiekhjnmfkcnldhhm?hl=en-US) that adds the functionality.
* If you're familiar with doing so and have an idea of what CSS style you added that is causing problems, you can use your browser's developer tools to inspect the page, find the element in question, and disable the offending style.
* In the site's `wp-config.php` file, define a constant to disable output of the plugin-defined CSS: `define( 'C2C_ADD_ADMIN_CSS_DISABLED', true );`. You can then visit the site's admin. Just remember to remove that line after you've fixed the CSS (or at least change "true" to "false"). This is an alternative to the query parameter approach described above, though it persists while the constant remains defined. There will be an admin notice on the plugin's setting page to alert you to the fact that the constant is defined and effectively disabling the plugin from adding any CSS.
* Presuming you know how to directly access the database: within the site's database, find the row with the option_name field value of `c2c_add_admin_css` and delete that row. The settings you saved for the plugin will be deleted and it will be like you've installed the plugin for the first time.
* If your server has WP-CLI installed, you can delete the plugin's setting from the commandline: `wp option delete c2c_add_admin_css`

The initial reaction by some might be to remove the plugin from the server's filesystem. This will certainly disable the plugin and prevent the CSS you configured through it from taking effect, restoring the access and functionality to the backend. However, reinstalling the plugin will put you back into the original predicament because the plugin will use the previously-configured settings, which wouldn't have changed.

= How do I disable syntax highlighting? =

The plugin's syntax highlighting of CSS (available as of WP 4.9) honors the built-in setting for whether syntax highlighting should be enabled or not.

To disable syntax highlighting, go to your profile page. Next to "Syntax Highlighting", click the checkbox labeled "Disable syntax highlighting when editing code". Note that this checkbox disables syntax highlighting throughout the admin interface and not just specifically for the plugin's settings page.

= Does this plugin include unit tests? =

Yes.


== Screenshots ==

1. A screenshot of the plugin's admin settings page.


== Hooks ==

The plugin exposes two filters for hooking. Typically, code making use of filters should ideally be put into a mu-plugin or site-specific plugin (which is beyond the scope of this readme to explain). Bear in mind that the features controlled by these filters are also configurable via the plugin's settings page. These filters are likely only of interest to advanced users able to code.

**c2c_add_admin_css (filter)**

The 'c2c_add_admin_css' filter allows customization of CSS that should be added directly to the admin page head.

Arguments:

* $css (string): CSS styles.

Example:

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

**c2c_add_admin_css_files (filter)**

The 'c2c_add_admin_css_files' filter allows programmatic modification of the list of CSS files to enqueue in the admin.

Arguments:

* $files (array): Array of CSS files.

Example:

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

= 2.0.1 (2021-05-30) =
Highlights:

This recommended bugfix release addresses a potential conflict with other plugins that prevented the plugin settings page main content from being displayed.

Details:

* Change: Update plugin framework to 063
    * Fix: Simplify settings initialization to prevent conflicts with other plugins
    * Change: Remove ability to detect plugin settings page before current screen is set, as it is no longer needed
    * Change: Enqueue thickbox during `'admin_enqueue_scripts'` action instead of during `'init'`
    * Change: Use `is_plugin_admin_page()` in `help_tabs()` instead of reproducing its functionality
    * Change: Trigger a debugging warning if `is_plugin_admin_page()` is used before `'admin_init'` action is fired
* New: Add new string (from plugin framework) for translation

= 2.0 (2021-05-12) =

Highlights:

This recommended minor release updates the plugin framework, restructures unit test files, notes compatibility through 5.7+, and incorporates numerous minor behind-the-scenes tweaks.

Details:

* Change: Outright support HTML5 rather than check for theme support of HTML5, since that isn't relevant to admin
* Change: Update plugin framework to 062
    * 062:
    * Change: Update `is_plugin_admin_page()` to use `get_current_screen()` when available
    * Change: Actually prevent object cloning and unserialization by throwing an error
    * Change: Check that there is a current screen before attempting to access its property
    * Change: Remove 'type' attribute from `style` tag
    * Change: Incorporate commonly defined styling for inline_textarea
    * 061:
    * Fix bug preventing settings from getting saved
    * 060:
    * Rename class from `c2c_{PluginName}_Plugin_051` to `c2c_Plugin_060`
    * Move string translation handling into inheriting class making the plugin framework code plugin-agnostic
        * Add abstract function `get_c2c_string()` as a getter for translated strings
        * Replace all existing string usage with calls to `get_c2c_string()`
    * Handle WordPress's deprecation of the use of the term "whitelist"
        * Change: Rename `whitelist_options()` to `allowed_options()`
        * Change: Use `add_allowed_options()` instead of deprecated `add_option_whitelist()` for WP 5.5+
        * Change: Hook `allowed_options` filter instead of deprecated `whitelist_options` for WP 5.5+
    * New: Add initial unit tests (currently just covering `is_wp_version_cmp()` and `get_c2c_string()`)
    * Add `is_wp_version_cmp()` as a utility to compare current WP version against a given WP version
    * Refactor `contextual_help()` to be easier to read, and correct function docblocks
    * Don't translate urlencoded donation email body text
    * Add inline comments for translators to clarify purpose of placeholders
    * Change PHP package name (make it singular)
    * Tweak inline function description
    * Note compatibility through WP 5.7+
    * Update copyright date (2021)
    * 051:
    * Allow setting integer input value to include commas
    * Use `number_format_i18n()` to format integer value within input field
    * Update link to coffee2code.com to be HTTPS
    * Update `readme_url()` to refer to plugin's readme.txt on plugins.svn.wordpress.org
    * Remove defunct line of code
* Change: Use plugin framework's `is_plugin_admin_page()` instead of reinventing it
* New: Add a recommendation for Add Admin JavaScript plugin to settings page
* Change: Output the multiple tips on the settings page as a list instead of multiple paragraphs
* Change: Prevent appending newline to value of setting passed to filter unless an actual value was configured
* Change: Move translation of all parent class strings into main plugin file
* Change: Tweak conditional checks to be more succinct
* Change: Ensure there's a current screen before attempting to get one of its properties
* Change: Omit inline styles for settings now that plugin framework defines them
* Change: Output newlines after paragraph tags in settings page
* Change: Note compatibility through WP 5.7+
* Change: Update copyright date (2021)
* Change: Change plugin's short description
* Change: Tweak some readme.txt documentation
* Change: Tweak some inline function and parameter documentation
* Unit tests:
    * New: Add tests for JS files getting registered and enqueued
    * New: Add tests for `add_codemirror()`
    * New: Add help function `get_css_files()`
    * Change: Restructure unit test directories and files into `tests/` top-level directory
    * Change: Remove 'test-' prefix from unit test files
    * Change: In bootstrap, store path to plugin file constant so its value can be used within that file and in test file

= 1.9.1 (2020-09-25) =
* Change: Update plugin framework to 051
    * Allow setting integer input value to include commas
    * Use `number_format_i18n()` to format integer value within input field
    * Update link to coffee2code.com to be HTTPS
    * Update `readme_url()` to refer to plugin's readme.txt on plugins.svn.wordpress.org
    * Remove defunct line of code
* Change: Note compatibility through WP 5.5+
* Change: Restructure unit test file structure
    * New: Create new subdirectory `phpunit/` to house all files related to unit testing
    * Change: Move `bin/` to `phpunit/bin/`
    * Change: Move `tests/bootstrap.php` to `phpunit/`
    * Change: Move `tests/` to `phpunit/tests/`
    * Change: Rename `phpunit.xml` to `phpunit.xml.dist` per best practices

_Full changelog is available in [CHANGELOG.md](https://github.com/coffee2code/add-admin-css/blob/master/CHANGELOG.md)._


== Upgrade Notice ==

= 2.0.1 =
Recommended bugfix release: Addressed potential conflict with other plugins that prevented plugin settings page main content from being displayed.

= 2.0 =
Recommended minor update: Updated plugin framework, restructured unit test files, noted compatibility through 5.7+, and incorporated numerous minor behind-the-scenes tweaks.

= 1.9.1 =
Trivial update: Updated plugin framework to version 051, restructured unit test file structure, and noted compatibility through WP 5.5+.

= 1.9 =
Minor update: updated plugin framework, added a TODO.md file, updated a few URLs to be HTTPS, expanded unit testing, updated compatibility to be WP 4.9 through 5.4+, and minor behind-the-scenes tweaks.

= 1.8 =
Minor update: added HTML5 compliance when supported by the theme, modernized and fixed unit tests, noted compatibility through WP 5.3+, and updated copyright date (2020)

= 1.7 =
Recommended update: added recovery mode, tweaked plugin initialization process, updated plugin framework, compatibility is now WP 4.7 through WP 5.1+, updated copyright date (2019), and more documentation and code improvements.

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
