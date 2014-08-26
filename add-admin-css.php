<?php
/**
 * Plugin Name: Add Admin CSS
 * Version:     1.3.1
 * Plugin URI:  http://coffee2code.com/wp-plugins/add-admin-css/
 * Author:      Scott Reilly
 * Author URI:  http://coffee2code.com/
 * Text Domain: add-admin-css
 * Domain Path: /lang/
 * License:     GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Description: Interface for easily defining additional CSS (inline and/or by URL) to be added to all administration pages.
 *
 * Compatible with WordPress 3.5+ through 4.0+
 *
 * =>> Read the accompanying readme.txt file for instructions and documentation.
 * =>> Also, visit the plugin's homepage for additional information and updates.
 * =>> Or visit: https://wordpress.org/plugins/add-admin-css/
 *
 * @package Add_Admin_CSS
 * @author Scott Reilly
 * @version 1.3.1
 **/

/*
	Copyright (c) 2010-2014 by Scott Reilly (aka coffee2code)

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

defined( 'ABSPATH' ) or die();

if ( is_admin() && ! class_exists( 'c2c_AddAdminCSS' ) ) :

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'c2c-plugin.php' );

class c2c_AddAdminCSS extends C2C_Plugin_038 {

	/**
	 * @var c2c_AddAdminCSS The one true instance
	 */
	private static $instance;

	/**
	 * @var array CSS file handles
	 */
	protected $css_file_handles = array();

	/**
	 * Get singleton instance.
	 *
	 * @since 1.2
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Resets the object to its initial state.
	 *
	 * @since 1.3
	 */
	public function reset() {
		$this->css_file_handles = array();
	}

	/**
	 * Constructor.
	 */
	protected function __construct() {
		parent::__construct( '1.3.1', 'add-admin-css', 'c2c', __FILE__, array( 'settings_page' => 'themes' ) );
		register_activation_hook( __FILE__, array( __CLASS__, 'activation' ) );

		return self::$instance = $this;
	}

	/**
	 * Handles activation tasks, such as registering the uninstall hook.
	 *
	 * @since 1.1
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
	 */
	public function uninstall() {
		delete_option( 'c2c_add_admin_css' );
	}

	/**
	 * Initializes the plugin's configuration and localizable text variables.
	 */
	protected function load_config() {
		$this->name      = __( 'Add Admin CSS', $this->textdomain );
		$this->menu_name = __( 'Admin CSS', $this->textdomain );

		$this->config = array(
			'files' => array( 'input' => 'inline_textarea', 'default' => '', 'datatype' => 'array',
					'label' => __( 'Admin CSS Files', $this->textdomain ),
					'help'  => __( 'List one file per line.  The reference can be relative to the root of your active theme, relative to the root of your site (by prepending file or path with "/"), or a full, absolute URL.  These will be listed in the order listed, and appear before the CSS defined below.', $this->textdomain ),
					'input_attributes' => 'style="width: 98%; white-space: nowrap;" rows="4" cols="40"'
			),
			'css' => array( 'input' => 'inline_textarea', 'default' => '', 'datatype' => 'text',
					'label' => __( 'Admin CSS', $this->textdomain ),
					'help'  => __( 'Note that the above CSS will be added to all admin pages and apply for all users able to view those pages.', $this->textdomain ),
					'input_attributes' => 'style="width: 98%; white-space: nowrap;" rows="10" cols="40"'
			),
		);
	}

	/**
	 * Override the plugin framework's register_filters() to register actions and filters.
	 */
	public function register_filters() {
		add_action( 'admin_init', array( $this, 'register_css_files' ) );
		add_action( 'admin_head', array( $this, 'add_css' ) );
	}

	/**
	 * Outputs the text above the setting form
	 *
	 * @param string $localized_heading_text (optional) Localized page heading text.
	 * @return void (Text will be echoed.)
	 */
	public function options_page_description( $localized_heading_text = '' ) {
		parent::options_page_description( __( 'Add Admin CSS Settings', $this->textdomain ) );
		echo '<p>' . __( 'Add additional CSS to your admin pages, which allows you to tweak the appearance of the WordPress administration pages to your liking.', $this->textdomain ) . '</p>';
		echo '<p>' . __( 'See the "Advanced Tips" tab in the "Help" section for info on how to use the plugin to programmatically customize CSS.' ) . '</p>';
		echo '<p>' .
			sprintf( __( 'TIP: If you are primarily only interested in hiding certain administration interface elements, take a look at my <a href="%s" title="Admin Trim Interface">Admin Trim Interface</a> plugin.  If you only want to hide in-page help text, check out my <a href="%s" title="">Admin Expert Mode</a> plugin.  Both plugins are geared toward their respective tasks and are very simple to use, requiring no knowledge of CSS.', $this->textdomain ),
			'https://wordpress.org/plugins/admin-trim-interface/',
			'https://wordpress.org/plugins/admin-expert-mode/' ) .
		'</p>';
	}

	/**
	 * Configures help tabs content.
	 *
	 * @since 1.2
	 */
	public function help_tabs_content( $screen ) {
		$screen->add_help_tab( array(
			'id'      => 'c2c-advanced-tips-' . $this->id_base,
			'title'   => __( 'Advanced Tips', $this->textdomain ),
			'content' => self::contextual_help( '', $this->options_page )
		) );

		parent::help_tabs_content( $screen );
	}

	/**
	 * Outputs advanced tips text
	 *
	 * @since 1.2
	 *
	 * @param string $contextual_help The default contextual help
	 * @param int $screen_id The screen ID
	 * @param object $screen The screen object (only supplied in WP 3.0)
	 * @return void (Text is echoed)
	 */
	public function contextual_help( $contextual_help, $screen_id, $screen = null ) {
		if ( $screen_id != $this->options_page ) {
			return $contextual_help;
		}

		$help = '<h3>' . __( 'Advanced Tips', $this->textdomain ) . '</h3>';

		$help .= '<p>' . __( 'You can also programmatically add to or customize any CSS defined in the "Admin CSS" field via the <code>c2c_add_admin_css</code> filter, like so:', $this->textdomain ) . '</p>';

		$help .= <<<HTML
<pre><code>add_filter( 'c2c_add_admin_css', 'my_admin_css' );
function my_admin_css( \$css ) {
	\$css .= "
		#site-heading a span { color:blue !important; }
		#favorite-actions { display:none; }
	";
	return \$css;
}</code></pre>

HTML;

		$help .= '<p>' . __( 'You can also programmatically add to or customize any referenced CSS files defined in the "Admin CSS Files" field via the <code>c2c_add_admin_css_files</code> filter, like so:', $this->textdomain ) . '</p>';

		$help .= <<<HTML
<pre><code>add_filter( 'c2c_add_admin_css_files', 'my_admin_css_files' );
function my_admin_css_files( \$files ) {
	\$files[] = 'http://yui.yahooapis.com/2.9.0/build/reset/reset-min.css';
	return \$files;
}</code></pre>

HTML;

		return $help;
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
					$src = trailingslashit( get_option( 'siteurl' ) ) . ltrim( $file, '/' );
				} else {
					$src = trailingslashit( get_stylesheet_directory_uri() ) . $file;
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

		if ( ! empty( $this->css_file_handles ) ) {
			$wp_styles->do_items( $this->css_file_handles );
		}

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

c2c_AddAdminCSS::instance();

endif; // end if !class_exists()
