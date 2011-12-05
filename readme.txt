=== Add Admin CSS ===
Contributors: coffee2code
Donate link: http://coffee2code.com/donate
Tags: admin, css, style, stylesheets, admin theme, customize, coffee2code
Requires at least: 3.0
Tested up to: 3.3
Stable tag: 1.1
Version: 1.1

Interface for easily defining additional CSS (inline and/or by URL) to be added to all administration pages.


== Description ==

Interface for easily defining additional CSS (inline and/or by URL) to be added to all administration pages.

Ever want to tweak the appearance of the WordPress admin pages, by hiding stuff, moving stuff around, changing fonts, colors, sizes, etc?  Any modification you may want to do with CSS can easily be done via this plugin.

Using this plugin you'll easily be able to define additional CSS (inline and/or files by URL) to be added to all administration pages.  You can define CSS to appear inline in the admin head (within style tags), or reference CSS files to be linked (via "link rel='stylesheet'" tags).  The referenced CSS files will appear in the admin head first, listed in the order defined in the plugin's settings.  Then any inline CSS are added to the admin head.  Both values can be filtered for advanced customization (see Advanced section).

Links: [Plugin Homepage](http://coffee2code.com/wp-plugins/add-admin-css/) | [Author Homepage](http://coffee2code.com)


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

= Can I limit what admin pages the CSS applies to? =

No, not presently.  The CSS is added for every admin page on the site.

= Can I limit what users the CSS applies to? =

No, not presently.  The CSS is added for any user that can enter the admin section of the site.


== Screenshots ==

1. A screenshot of the plugin's admin settings page.


== Changelog ==

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

= 1.1 =
Recommended update: renamed class and filters by prefixing 'c2c_'; noted compatibility through WP 3.3; dropped support for versions of WP older than 3.0; updated plugin framework; deprecate global variable.

= 1.0 =
Initial public release!