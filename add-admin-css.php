<?php
/**
 * @package Add_Admin_CSS
 * @author Scott Reilly
 * @version 1.1
 */
/*
Plugin Name: Add Admin CSS
Version: 1.1
Plugin URI: http://coffee2code.com/wp-plugins/add-admin-css/
Author: Scott Reilly
Author URI: http://coffee2code.com
Text Domain: add-admin-css
Domain Path: /lang/
Description: Interface for easily defining additional CSS (inline and/or by URL) to be added to all administration pages.

Compatible with WordPress 3.0+, 3.1+, 3.2+, 3.3+

=>> Read the accompanying readme.txt file for instructions and documentation.
=>> Also, visit the plugin's homepage for additional information and updates.
=>> Or visit: http://wordpress.org/extend/plugins/add-admin-css/

TODO:
	* Move 'Advanced Tips' section to contextual help

*/

/*
Copyright (c) 2010-2011 by Scott Reilly (aka coffee2code)

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation
files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy,
modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the
Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR
IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

if ( is_admin() && ! class_exists( 'c2c_AddAdminCSS' ) ) :

require_once( 'c2c-plugin.php' );

class c2c_AddAdminCSS extends C2C_Plugin_029 {

	public static $instance;

	protected $css_file_handles = array();

	/**
	 * Handles installation tasks, such as ensuring plugin options are instantiated and saved to options table.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->c2c_AddAdminCSS();
	}

	public function c2c_AddAdminCSS() {
		// Be a singleton
		if ( ! is_null( self::$instance ) )
			return;

		parent::__construct( '1.1', 'add-admin-css', 'c2c', __FILE__, array( 'settings_page' => 'themes' ) );
		register_activation_hook( __FILE__, array( __CLASS__, 'activation' ) );
		self::$instance = $this;
	}

	/**
	 * Handles activation tasks, such as registering the uninstall hook.
	 *
	 * @since 1.1
	 *
	 * @return void
	 */
	public function activation() {
		register_uninstall_hook( __FILE__, array( __CLASS__, 'uninstall' ) );
	}

	/**
	 * Handles uninstallation tasks, such as deleting plugin options.
	 *
	 * This can be overridden.
	 *
	 * @since 1.1
	 *
	 * @return void
	 */
	public function uninstall() {
		delete_option( 'c2c_add_admin_css' );
	}

	/**
	 * Initializes the plugin's configuration and localizable text variables.
	 *
	 * @return void
	 */
	protected function load_config() {
		$this->name      = __( 'Add Admin CSS', $this->textdomain );
		$this->menu_name = __( 'Admin CSS', $this->textdomain );

		$this->config = array(
			'files' => array( 'input' => 'textarea', 'default' => '', 'datatype' => 'array',
					'label' => __( 'Admin CSS Files', $this->textdomain ),
					'help' => __( 'List one file per line.  The reference can be relative to the root of your active theme, relative to the root of your site (by prepending file or path with "/"), or a full, absolute URL.  These will be listed in the order listed, and appear before the CSS defined below.', $this->textdomain ),
					'input_attributes' => 'style="width: 98%; font-family: \"Courier New\", Courier, mono;" rows="4" cols="40"'
			),
			'css' => array( 'input' => 'textarea', 'default' => '', 'datatype' => 'text',
					'label' => __( 'Admin CSS', $this->textdomain ),
					'help' => __( 'Note that the above CSS will be added to all admin pages and apply for all admin users.', $this->textdomain),
					'input_attributes' => 'style="width: 98%; font-family: \"Courier New\", Courier, mono;" rows="10" cols="40"'
			)
		);
	}

	/**
	 * Override the plugin framework's register_filters() to register actions and filters.
	 *
	 * @return void
	 */
	public function register_filters() {
		add_action( 'admin_init', array( &$this, 'register_css_files' ) );
		add_action( 'admin_head', array( &$this, 'add_css' ) );
		add_action( $this->get_hook( 'after_settings_form' ), array( &$this, 'advanced_tips' ) );
	}

	/**
	 * Outputs the text above the setting form
	 *
	 * @return void (Text will be echoed.)
	 */
	public function options_page_description() {
		parent::options_page_description( __( 'Add Admin CSS Settings', $this->textdomain ) );
		echo '<p>' . __( 'Add additional CSS to your admin pages, which allows you to tweak the appearance of the WordPress administration pages to your liking.', $this->textdomain ) . '</p>';
		echo '<p>' . __( 'See <a href="#advanced-tips">Advanced Tips</a> for info on how to use the plugin to programmatically customize CSS.' ) . '</p>';
		echo '<p>' .
			sprintf( __( 'TIP: If you are primarily only interested in hiding certain administration interface elements, take a look at my <a href="%s" title="Admin Trim Interface">Admin Trim Interface</a> plugin.  If you only want to hide in-page help text, check out my <a href="%s" title="">Admin Expert Mode</a> plugin.  Both plugins are geared toward their respective tasks and are very simple to use, requiring no knowledge of CSS.', $this->textdomain ),
			'http://wordpress.org/extend/plugins/admin-trim-interface/',
			'http://wordpress.org/extend/plugins/admin-expert-mode/' ) .
		'</p>';
	}

	/*
	 * Outputs advanced tips text
	 *
	 * @return void (Text will be echoed.)
	 */
	public function advanced_tips() {
		echo '<a name="advanced-tips"></a>';
		echo '<h2>Advanced Tips</h2>';
		echo '<p>' . __( 'You can also programmatically add to or customize any CSS defined in the "Admin CSS" field via the <code>c2c_add_admin_css</code> filter, like so:', $this->textdomain ) . '</p>';
		echo <<<HTML
		<pre><code>add_filter( 'c2c_add_admin_css', 'my_admin_css' );
function my_admin_css( \$css ) {
	\$css .= "
		#site-heading a span { color:blue !important; }
		#favorite-actions { display:none; }
	";
	return \$css;
}</code></pre>

HTML;
		echo '<p>' . __( 'You can also programmatically add to or customize any referenced CSS files defined in the "Admin CSS Files" field via the <code>c2c_add_admin_css_files</code> filter, like so:', $this->textdomain ) . '</p>';
		echo <<<HTML
		<pre><code>add_filter( 'c2c_add_admin_css_files', 'my_admin_css_files' );
function my_admin_css_files( \$files ) {
	\$files[] = 'http://yui.yahooapis.com/2.9.0/build/reset/reset-min.css';
	return \$files;
}</code></pre>

HTML;

	}

	/**
	 * Register CSS files
	 *
	 * @return array Array of CSS files
	 */
	public function get_css_files() {
		$options = $this->get_options();
		return apply_filters( 'c2c_add_admin_css_files', $options['files'] );
	}

	/**
	 * Register CSS files
	 *
	 * @return void
	 */
	public function register_css_files() {
		$files = $this->get_css_files();
		if ( $files ) {
			foreach ( (array) $files as $file ) {
				$handle = basename( $file, '.css' );
				// FYI: There is still the potential for duplicate handles, which preclude subsequent uses from registering
				if ( strpos( $file, '://' ) !== false ) {
					$src = $file;
					$handle .= '-remote';
				} elseif ( $file{0} == '/' ) {
					$src = get_option( 'siteurl' ) . '/' . $file;
				} else {
					$src = get_stylesheet_directory_uri() . '/' . $file;
				}
				$this->css_file_handles[] = $handle;
				wp_register_style( $handle, $src, array(), $this->version, 'all' );
			}
		}
	}

	/**
	 * Outputs CSS as header links and/or inline header styles
	 *
	 * @return void (Text will be echoed.)
	 */
	public function add_css() {
		global $wp_styles;
		$options = $this->get_options();
		if ( ! empty( $this->css_file_handles ) )
			$wp_styles->do_items( $this->css_file_handles );

		$css = trim( apply_filters( 'c2c_add_admin_css', $options['css'] . "\n" ) );
		if ( ! empty( $css ) ) {
			echo "
			<style type='text/css'>
			$css
			</style>
			";
		}
	}

} // end c2c_AddAdminCSS

// NOTICE: The 'c2c_add_admin_css' global is deprecated and will be removed in the plugin's version 1.2.
// Instead, use: c2c_AddAdminCSS::$instance
$GLOBALS['c2c_add_admin_css'] = new c2c_AddAdminCSS();

endif; // end if !class_exists()

?>