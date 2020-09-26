=== Add Admin CSS ===
Contributors: coffee2code
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6ARCFJ9TX3522
Tags: admin, css, style, stylesheets, admin theme, customize, coffee2code
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 4.9
Tested up to: 5.5
Stable tag: 1.9.1

Interface for easily defining additional CSS (inline and/or by URL) to be added to all administration pages.


== Description ==

Ever want to tweak the appearance of the WordPress admin pages by hiding stuff, moving stuff around, changing fonts, colors, sizes, etc?  Any modification you may want to do with CSS can easily be done via this plugin.

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

However, you can preface your selectors with admin page specific class(es) on 'body' tag to ensure CSS only applies on certain admin pages. (e.g. `body.index-php h2, #icon-index { display: none; }`).

Or, you can hook the 'c2c_add_admin_css' and 'c2c_add_admin_css_files' filters and determine the current admin page content to decide whether the respective hook argument should be returned (and thus output) or not.

= Can I limit what users the CSS applies to? =

No, not presently. At least not directly. By default, the CSS is added for any user that can enter the admin section of the site.

You can hook the 'c2c_add_admin_css' and 'c2c_add_admin_css_files' filters and determine the current user to decide whether the respective hook argument should be returned (and thus output) for the user or not.

= How can I edit the plugin's settings in the event I supplied CSS that prevents the admin pages from properly functioning or being seen? =

It is certainly possible that you can put yourself in an unfortunate position by supplying CSS that could hide critical parts of admin pages, making it seeminly impossible to fix or revert your changes. Fortunately, there are a number of approaches you can take to correct the problem.

The recommended approach is to visit the URL for the plugin's settings page, but appended with a special query parameter to disable the output of its JavaScript. The plugin's settings page would typically be at a URL like `https://example.com/wp-admin/themes.php?page=add-admin-css%2Fadd-admin-css.php`. Append `&c2c-no-css=1` to that, so that the URL is `https://example.com/wp-admin/themes.php?page=add-admin-css%2Fadd-admin-css.php&c2c-no-css=1` (obviously change example.com with the domain name for your site).

There are other approaches you can use, though they require direct database or server filesystem access:

* Some browsers (such as Firefox, via View -> Page Style -> No Style) allow you to disable styles for sites loaded in that tab. Other browsers may also support such functionality natively or through an extension. Chrome has an extension called [Web Developer](https://chrome.google.com/webstore/detail/web-developer/bfbameneiokkgbdmiekhjnmfkcnldhhm?hl=en-US) that adds the functionality.
* If you're familiar with doing so and have an idea of what CSS style you added that is causing problems, you can use your browser's developer tools to inspect the page, find the element in question, and disable the offending style.
* In the site's `wp-config.php` file, define a constant to disable output of the plugin-defined JavaScript: `define( 'C2C_ADD_ADMIN_CSS_DISABLED', true );`. You can then visit the site's admin. Just remember to remove that line after you've fixed the CSS (or at least change "true" to "false"). This is an alternative to the query parameter approach described above, though it persists while the constant remains defined. There will be an admin notice on the plugin's setting page to alert you to the fact that the constant is defined and effectively disabling the plugin from adding any CSS.
* Presuming you know how to directly access the database: within the site's database, find the row with the option_name field value of `c2c_add_admin_css` and delete that row. The settings you saved for the plugin will be deleted and it will be like you've installed the plugin for the first time.
* If your server has WP-CLI installed, you can delete the plugin's setting from the commandline: `wp option delete c2c_add_admin_css`

The initial reaction by some might be to remove the plugin from the server's filesystem. This will certainly disable the plugin and prevent the CSS you configured through it from taking effect, restoring the access and functionality to the backend. However, reinstalling the plugin will put you back into the original predicament because the plugin will use the previously-configured settings, which wouldn't have changed.

= How do I disable syntax highlighting? =

The plugin's syntax highlighting of CSS (available on WP 4.9+) honors the built-in setting for whether syntax highlighting should be enabled or not.

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

= 1.9 (2020-06-26) =

Highlights:

* This minor release updates its plugin framework, adds a TODO.md file, updates a few URLs to be HTTPS, expands unit testing, updates compatibility to be WP 4.9 through 5.4+, and minor behind-the-scenes tweaks.

Details:

* Change: Allow class to always be instantiated, but add check to only register hooks when in the admin
* Change: Change class names used for admin notice to match current WP convention
* Change: Update plugin framework to 050
    * Allow a hash entry to literally have '0' as a value without being entirely omitted when saved
    * Output donation markup using `printf()` rather than using string concatenation
    * Update copyright date (2020)
    * Note compatibility through WP 5.4+
    * Drop compatibility with version of WP older than 4.9
* New: Add TODO.md and move existing TODO list from top of main plugin file into it (and add to it)
* Change: Tweak help text for 'files' setting for better phrasing and to remove extra sentence spaces
* Change: Note compatibility through WP 5.4+
* Change: Drop compatibility for version of WP older than 4.9
* Change: Update links to coffee2code.com to be HTTPS
* Change: Add translator comment for string with multiple placeholders
* CHange: Minor code reformatting
* Unit tests:
    * New: Add test for `options_page_description()`
    * New: Add tests for default hooks
    * New: Add tests for setting and query param names
    * New: Label groupings of tests
    * Fix: Adjust tests to properly account for theme support or non-support of html5 when checking expected markup output
    * Fix: Ensure admin-related tests call `test_turn_on_admin()` so admin init actions are called
    * Fix: Invoke parent class's `setUp()` during `setUp()`
    * Change: Remove unnecessary unregistering of hooks in `tearDown()`
    * Change: Move `test_turn_on_admin()` until just before first needed now that other tests can run before it
    * Change: Store plugin instance in class variable to simplify referencing it
    * Change: Use HTTPS for link to WP SVN repository in bin script for configuring unit tests (and delete commented-out code)

= 1.8 (2019-12-04) =
Highlights:

* This minor release adds HTML5 compliance when supported by the theme, modernizes and fixes unit tests, and notes compatibility through WP 5.3+.

Details:

* New: Add HTML5 compliance by omitting `type` attribute when the theme supports 'html5'
* Unit tests:
    * New: Add unit test to ensure plugin is hooked to initialize on `plugins_loaded`
    * Fix: Don't pass argument to plugin object's `add_css()`
    * Fix: Don't expect `type` attribute in `link` tags since they're not HTML5-compliant
    * Fix: Prevent WP from attempting to print the emoji detection script (which isn't built in the develop.svn repo)
    * Change: Update unit test install script and bootstrap to use latest WP unit test repo
* Change: Note compatibility through WP 5.3+
* Change: Update copyright date (2020)

_Full changelog is available in [CHANGELOG.md](https://github.com/coffee2code/add-admin-css/blob/master/CHANGELOG.md)._


== Upgrade Notice ==

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
