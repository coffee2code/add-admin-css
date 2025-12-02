=== Add Admin CSS ===
Contributors: coffee2code
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6ARCFJ9TX3522
Tags: admin, css, style, stylesheets, admin theme
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 5.5
Tested up to: 6.9
Stable tag: 2.5.1

Easily define additional CSS (inline and/or by URL) to be added to all administration pages.


== Description ==

Ever want to tweak the appearance of the WordPress admin pages by hiding stuff, moving stuff around, changing fonts, colors, sizes, etc? Any modification you may want to do with CSS can easily be done via this plugin.

Using this plugin you'll easily be able to define additional CSS (inline and/or files by URL) to be added to all administration pages. Hooks are provided to customize the output of the CSS, the CSS files, and if/when the CSS should even be output (see Hooks section).

Links: [Plugin Homepage](https://coffee2code.com/wp-plugins/add-admin-css/) | [Plugin Directory Page](https://wordpress.org/plugins/add-admin-css/) | [GitHub](https://github.com/coffee2code/add-admin-css/) | [Author Homepage](https://coffee2code.com)


== Installation ==

1. Install via the built-in WordPress plugin installer. Or install the plugin code inside the plugins directory for your site (typically `/wp-content/plugins/`).
1. Activate the plugin through the 'Plugins' admin menu in WordPress
1. Go to "Appearance" -> "Admin CSS" and specify some CSS to be added into all admin pages. (You can also use the "Settings" link in the plugin's entry on the admin "Plugins" page).


== Frequently Asked Questions ==

= Can I add CSS I defined via a file, or one that is hosted elsewhere? =

Yes, via the "Admin CSS Files" input field on the plugin's settings page.

= Can I limit what admin pages the CSS gets output on? =

By default, the CSS is added for every admin page on the site and for every user.

One option, if you wish to only use CSS and you want to limit use of CSS to certain admin pages, is to preface your selectors with admin page specific class(es) on the 'body' tag to ensure CSS only applies on certain admin pages. (e.g. `body.edit-php h1 { color: purple; }`).

Otherwise, programmatically you have full control over that behavior via the `'c2c_add_admin_css_disable_css'` filter (see Hooks section). You'd hook that filter, determine the context, and decide if the CSS should be output or not. You could check what page is being loaded and/or who is the current user.

= Can I limit what users the CSS applies to? =

By default, the CSS is added for every admin page on the site and for every user.

Programmatically you have full control over that behavior via the `'c2c_add_admin_css_disable_css'` filter (see Hooks section). You'd hook that filter, determine the context, and decide if the CSS should be output or not. You could check who is the current user and/or what page is being loaded.

There is currently no way to do this purely with CSS or through any other setting provided by the plugin.

= How can I edit the plugin's settings in the event I supplied CSS that prevents the admin pages from properly functioning or being seen? =

It is certainly possible that you can put yourself in an unfortunate position by supplying CSS that could hide critical parts of admin pages, making it seemingly impossible to fix or revert your changes. Fortunately, there are a number of approaches you can take to correct the problem.

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

= Does this plugin have unit tests? =

Yes. The tests are not packaged in the release .zip file or included in plugins.svn.wordpress.org, but can be found in the [plugin's GitHub repository](https://github.com/coffee2code/add-admin-css/).


== Screenshots ==

1. A screenshot of the plugin's admin settings page.


== Developer Documentation ==

Developer documentation can be found in [DEVELOPER-DOCS.md](https://github.com/coffee2code/add-admin-css/blob/master/DEVELOPER-DOCS.md). That documentation covers the hooks provided by the plugin.

As an overview, these are the hooks provided by the plugin:

* `c2c_add_admin_css`             : Filter to customize the CSS that should be added directly to the admin page head.
* `c2c_add_admin_css_files`       : Filter to customize the list of CSS files to enqueue in the admin.
* `c2c_add_admin_css_disable_css` : Filter to customize if the CSS defined via this plugin should be output or not.


== Changelog ==

= 2.5.1 (2025-12-02) =
Highlights:

A bugfix release to address the overzealous encoding of some valid CSS characters introduced in v2.5.

Details:

* Fix: Escape only the minimum of characters so that valid CSS characters don't get escaped. Props kevinvanrijn.
* Change: Note compatibility through WP 6.9+

= 2.5 (2025-03-29) =
Highlights:

This recommended long overdue release adds a new filter for fine-grained control of whether CSS should be output or not, updates the plugin framework to the most current version (for hardening and miscellaneous improvements), prevents translations from containing unintended markup, notes compatibility through WP 6.8+ and PHP 8.3+, drops compatibility with versions of WP older than 5.5, adds DEVELOPER-DOCS.md, and removes unit tests from release packaging, and more.

Details:

* New: Add `c2c_add_admin_css_disable_css` filter to override if CSS defined via this plugin should be output or not
* New: Add `is_recovery_mode_enabled()` to determine is recovery mode is enabled
* Change: Only support query parameter method of enabling recovery mode if current user can configure plugin settings
* Change: Display the files setting help text as a list rather than a paragraph
* Change: Check specifically if recovery mode is enabled before displaying admin notice that recovery mode is enabled
* Change: Explicitly state the plugin name in the recovery mode admin notice to avoid ambiguity
* Change: Switch use of `parse_url()` to `wp_parse_url()`
* Change: Escape output of all translated strings
* Change: Use instance method invocation instead of a deprecated static method invocation
* Change: Convert 'input_attributes' value of config items from a string to an array
* Change: Add translator comments for a pair of strings with placeholders that didn't have one
* New: Add DEVELOPER-DOCS.md and move hooks documentation into it
* Change: Update plugin framework to 068
    * 068:
    * Change: Discontinue unnecessary explicit loading of textdomain
    * Change: Ignore a PHPCS warning that doesn't apply
    * Change: Minor code reformatting
    * Change: Note compatibility through WP 6.8+
    * Change: Update copyright date (2025)
    * Unit tests:
        * Change: Generify unit tests to centralize per-plugin configuration to the top of the test class
        * Change: Define method return types for PHP 8+ compatibility
        * New: Add some header documentation
    * 067:
    * Breaking: Require config attribute 'input_attributes' to be an array
    * Hardening: Treat input attributes as array and escape each element before output
    * Change: Ensure config attribute values are of the same datatype as their defaults
    * Change: Simplify `form_action_url()` to avoid using a server global
    * Change: Use `form_action_url()` in `plugin_action_links()` rather than duplicating its functionality
    * Change: Escape output of all translated strings
    * Change: Make `get_hook()` public rather than protected
    * Change: Explicitly declare object variables rather than doing so dynamically
    * Change: Convert `register_filters()` to an abstract declaration
    * Change: Use double quotes for attribute of paragraph for setting description
    * Change: Prevent unwarranted PHPCS complaints about nonces
    * Change: Improve function documentation
    * Change: Adjust function documentation formatting to align with WP core
    * Change: Note compatibility through WP 6.5+
    * Change: Drop compatibility with version of WP older than 5.5
    * Change: Update copyright date (2024)
    * 066:
    * New: Add customization of capability needed to manage plugin settings (via new filter {plugin_prefix}_manage_options_capability)
    * Change: Add styles for nested lists within settings descriptions
    * Change: Note compatibility through WP 6.3+
    * 065:
    * New: Add support for 'inline_help' setting configuration option
    * New: Add support for 'raw_help' setting configuration option
    * New: Add support for use of lists within settings descriptions
    * Change: Add an 'id' attribute to settings form
    * Change: Add styles for disabled input text fields and inline setting help notices
    * Change: Support 'number' input by assigning 'small-text' class
    * Change: Tweak styling for settings page footer
    * Change: Note compatibility through WP 6.2+
    * Change: Update copyright date (2023)
    * 064:
    * New: For checkbox settings, support a 'more_help' config option for defining help text to appear below checkbox and its label
    * Fix: Fix URL for plugin listing donate link
    * Change: Store donation URL as object variable
    * Change: Update strings used for settings page donation link
* Change: Ignore some PHPCS checks that don't apply
* Change: Update and improve long description and numerous FAQ entries
* Change: Note compatibility through WP 6.8+
* Change: Note compatibility through PHP 8.3+
* Change: Drop compatibility with version of WP older than 5.5
* Change: Tweak installation instruction
* Change: Update copyright date (2025)
* Change: Reduce number of tags defined in readme.txt
* Change: Remove development and testing related files from release packaging

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

_Full changelog is available in [CHANGELOG.md](https://github.com/coffee2code/add-admin-css/blob/master/CHANGELOG.md)._


== Upgrade Notice ==

= 2.5.1 =
Recommended bugfix update: Addressed the overzealous encoding of some valid CSS characters introduced in v2.5 and noted compatibility through WP 6.9+.

= 2.5 =
Recommended update: added filter to control if CSS is output, updated plugin framework (hardening & improvements), prevented unintended markup in translations, noted compatibility through WP 6.8+, dropped compatibility with WP older than 5.5, and removed unit tests from release packaging.

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
Recommended update: added code highlighting and syntax checking as introduced in WP 4.9; show admin notifications for settings page; updated plugin framework to version 046; noted compatibility through WP 4.9; dropped compatibility with versions of WP older than 4.6; updated copyright date (2018).

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
