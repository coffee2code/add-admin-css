=== Add Admin CSS ===
Contributors: coffee2code
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6ARCFJ9TX3522
Tags: admin, css, style, stylesheets, admin theme, customize, coffee2code
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 3.5
Tested up to: 4.0
Stable tag: 1.3.1

Interface for easily defining additional CSS (inline and/or by URL) to be added to all administration pages.


== Description ==

Ever want to tweak the appearance of the WordPress admin pages by hiding stuff, moving stuff around, changing fonts, colors, sizes, etc?  Any modification you may want to do with CSS can easily be done via this plugin.

Using this plugin you'll easily be able to define additional CSS (inline and/or files by URL) to be added to all administration pages. You can define CSS to appear inline in the admin head (within style tags), or reference CSS files to be linked (via "link rel='stylesheet'" tags). The referenced CSS files will appear in the admin head first, listed in the order defined in the plugin's settings. Then any inline CSS are added to the admin head. Both values can be filtered for advanced customization (see Advanced section).

Links: [Plugin Homepage](http://coffee2code.com/wp-plugins/add-admin-css/) | [Plugin Directory Page](https://wordpress.org/plugins/add-admin-css/) | [Author Homepage](http://coffee2code.com)


== Advanced ==

You can also programmatically add to or customize any CSS defined in the "Admin CSS" field via the c2c_add_admin_css filter, like so:

`
add_filter( 'c2c_add_admin_css', 'my_admin_css' );
function my_admin_css( $css ) {
	$css .= "
		#site-heading a span { color:blue !important; }
		#favorite-actions { display:none; }
	";
	return $css;
}
`

You can also programmatically add to or customize any referenced CSS files defined in the "Admin CSS Files" field via the c2c_add_admin_css_files filter, like so:

`
add_filter( 'c2c_add_admin_css_files', 'my_admin_css_files' );
function my_admin_css_files( $files ) {
	$files[] = 'http://yui.yahooapis.com/2.9.0/build/reset/reset-min.css';
	return $files;
}
`


== Installation ==

1. Unzip `add-admin-css.zip` inside the `/wp-content/plugins/` directory for your site (or install via the built-in WordPress plugin installer)
1. Activate the plugin through the 'Plugins' admin menu in WordPress
1. Go to "Appearance" -> "Admin CSS" and add some CSS to be added into all admin pages.


== Frequently Asked Questions ==

= Can I add CSS I defined via a file, or one that is hosted elsewhere? =

Yes, via the "Admin CSS Files" input field on the plugin's settings page.

= Can I limit what admin pages the CSS gets output on? =

No, not presently. The CSS The CSS is added for every admin page on the site.

However, you can preface your selectors with admin page specific class(es) on 'body' tag to ensure CSS only applies on certain admin pages. (e.g. `body.index-php h2, #icon-index { display: none; }`).

= Can I limit what users the CSS applies to? =

No, not presently. The CSS is added for any user that can enter the admin section of the site.

= Does this plugin include unit tests? =

Yes.


== Screenshots ==

1. A screenshot of the plugin's admin settings page.


== Changelog ==

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

= 1.3.1 =
Trivial update: update plugin framework to version 038; noted compatibility through WP 4.0+; added plugin icon.

= 1.3 =
Minor update: added unit tests; minor improvements; noted compatibility through WP 3.8+.

= 1.2 =
Recommended update. Highlights: stopped wrapping long input field text; updated plugin framework; updated WP compatibility as 3.1 - 3.5+; explicitly stated license; and more.

= 1.1 =
Recommended update: renamed class and filters by prefixing 'c2c_'; noted compatibility through WP 3.3; dropped support for versions of WP older than 3.0; updated plugin framework; deprecate global variable.

= 1.0 =
Initial public release!
