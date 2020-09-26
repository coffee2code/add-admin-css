<?php
/**
 * Plugin Name: Add Admin CSS
 * Version:     1.9.1
 * Plugin URI:  https://coffee2code.com/wp-plugins/add-admin-css/
 * Author:      Scott Reilly
 * Author URI:  https://coffee2code.com/
 * Text Domain: add-admin-css
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Description: Interface for easily defining additional CSS (inline and/or by URL) to be added to all administration pages.
 *
 * Compatible with WordPress 4.9+ through 5.5+
 *
 * =>> Read the accompanying readme.txt file for instructions and documentation.
 * =>> Also, visit the plugin's homepage for additional information and updates.
 * =>> Or visit: https://wordpress.org/plugins/add-admin-css/
 *
 * @package Add_Admin_CSS
 * @author  Scott Reilly
 * @version 1.9.1
 **/

/*
	Copyright (c) 2010-2020 by Scott Reilly (aka coffee2code)

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

if ( ! class_exists( 'c2c_AddAdminCSS' ) ) :

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'c2c-plugin.php' );

final class c2c_AddAdminCSS extends c2c_AddAdminCSS_Plugin_051 {

	/**
	 * Name of plugin's setting.
	 *
	 * @since 1.7
	 * @var string
	 */
	const SETTING_NAME = 'c2c_add_admin_css';

	/**
	 * Name of query parameter for disabling CSS output.
	 *
	 * @since 1.7
	 * @var string
	 */
	const NO_CSS_QUERY_PARAM = 'c2c-no-css';

	/**
	 * The one true instance.
	 *
	 * @var c2c_AddAdminCSS
	 */
	private static $instance;

	/**
	 * CSS file handles.
	 *
	 * @var array
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
		parent::__construct( '1.9.1', 'add-admin-css', 'c2c', __FILE__, array( 'settings_page' => 'themes' ) );
		register_activation_hook( __FILE__, array( __CLASS__, 'activation' ) );

		return self::$instance = $this;
	}

	/**
	 * Handles activation tasks, such as registering the uninstall hook.
	 *
	 * @since 1.1
	 */
	public static function activation() {
		register_uninstall_hook( __FILE__, array( __CLASS__, 'uninstall' ) );
	}

	/**
	 * Handles uninstallation tasks, such as deleting plugin options.
	 *
	 * This can be overridden.
	 *
	 * @since 1.1
	 */
	public static function uninstall() {
		delete_option( self::SETTING_NAME );
	}

	/**
	 * Initializes the plugin's configuration and localizable text variables.
	 */
	protected function load_config() {
		$this->name      = __( 'Add Admin CSS', 'add-admin-css' );
		$this->menu_name = __( 'Admin CSS', 'add-admin-css' );

		$this->config = array(
			'files' => array(
				'input'            => 'inline_textarea',
				'default'          => '',
				'datatype'         => 'array',
				'label'            => __( 'Admin CSS Files', 'add-admin-css' ),
				'help'             => __( 'List one file per line. The reference can be relative to the root of your active theme, relative to the root of your site (by prepending file or path with "/"), or a full, absolute URL. These will be output in the order listed above and appear before the CSS defined below.', 'add-admin-css' ),
				'input_attributes' => 'style="width: 98%; white-space: pre; word-wrap: normal; overflow-x: scroll;" rows="4" cols="40"',
			),
			'css' => array(
				'input'            => 'inline_textarea',
				'default'          => '',
				'datatype'         => 'text',
				'label'            => __( 'Admin CSS', 'add-admin-css' ),
				'help'             => __( 'Note that the above CSS will be added to all admin pages and apply for all users able to view those pages.', 'add-admin-css' ),
				'input_attributes' => 'style="width: 98%; white-space: pre; word-wrap: normal; overflow-x: scroll;" rows="10" cols="40"',
			),
		);
	}

	/**
	 * Override the plugin framework's register_filters() to register actions and filters.
	 */
	public function register_filters() {
		if ( ! is_admin() ) {
			return;
		}

		add_action( 'admin_init',            array( $this, 'register_css_files' ) );
		add_action( 'admin_head',            array( $this, 'add_css' ) );
		add_action( 'admin_notices',         array( $this, 'recovery_mode_notice' ) );
		add_action( 'admin_notices',         array( $this, 'show_admin_notices' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'add_codemirror' ) );
		add_filter( 'wp_redirect',           array( $this, 'remove_query_param_from_redirects' ) );
	}

	/**
	 * Outputs the text above the setting form
	 *
	 * @param string $localized_heading_text (optional) Localized page heading text.
	 */
	public function options_page_description( $localized_heading_text = '' ) {
		parent::options_page_description( __( 'Add Admin CSS Settings', 'add-admin-css' ) );
		echo '<p>'
			. __( 'Add additional CSS to your admin pages, which allows you to tweak the appearance of the WordPress administration pages to your liking.', 'add-admin-css' )
			. '</p>';
		echo '<p>'
			. __( 'See the "Advanced Tips" tab in the "Help" section for info on how to use the plugin to programmatically customize CSS.', 'add-admin-css' )
			. '</p>';
		echo '<p>'
			/* translators: 1: URL for Admin Trim Interface plugin page, 2: URL for Admin Expert Mode plugin page */
			. sprintf(
				__( 'TIP: If you are primarily only interested in hiding certain administration interface elements, take a look at my <a href="%1$s" title="Admin Trim Interface">Admin Trim Interface</a> plugin. If you only want to hide in-page help text, check out my <a href="%2$s" title="">Admin Expert Mode</a> plugin. Both plugins are geared toward their respective tasks and are very simple to use, requiring no knowledge of CSS.', 'add-admin-css' ),
				'https://wordpress.org/plugins/admin-trim-interface/',
				'https://wordpress.org/plugins/admin-expert-mode/'
			)
			. '</p>';
	}

	/**
	 * Configures help tabs content.
	 *
	 * @since 1.2
	 */
	public function help_tabs_content( $screen ) {
		$screen->add_help_tab( array(
			'id'      => 'c2c-advanced-tips-' . $this->id_base,
			'title'   => __( 'Advanced Tips', 'add-admin-css' ),
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
	 */
	public function contextual_help( $contextual_help, $screen_id, $screen = null ) {
		if ( $screen_id != $this->options_page ) {
			return $contextual_help;
		}

		$help = '<h3>' . __( 'Advanced Tips', 'add-admin-css' ) . '</h3>';

		$help .= '<p>' . __( 'You can also programmatically add to or customize any CSS defined in the "Admin CSS" field via the <code>c2c_add_admin_css</code> filter, like so:', 'add-admin-css' ) . '</p>';

		$help .= <<<HTML
<pre><code>function my_admin_css( \$css ) {
	\$css .= "
		#site-heading a span { color:blue !important; }
		#favorite-actions { display:none; }
	";
	return \$css;
}
add_filter( 'c2c_add_admin_css', 'my_admin_css' );</code></pre>

HTML;

		$help .= '<p>' . __( 'You can also programmatically add to or customize any referenced CSS files defined in the "Admin CSS Files" field via the <code>c2c_add_admin_css_files</code> filter, like so:', 'add-admin-css' ) . '</p>';

		$help .= <<<HTML
<pre><code>function my_admin_css_files( \$files ) {
	\$files[] = 'http://yui.yahooapis.com/2.9.0/build/reset/reset-min.css';
	return \$files;
}
add_filter( 'c2c_add_admin_css_files', 'my_admin_css_files' );</code></pre>

HTML;

		return $help;
	}

	/**
	 * Outputs admin notice on plugin's setting page if recovery mode is active.
	 *
	 * @since 1.7
	 */
	public function recovery_mode_notice() {
		if ( get_current_screen()->id === $this->options_page && ! $this->can_show_css() ) {
			if ( defined( 'C2C_ADD_ADMIN_CSS_DISABLED' ) && C2C_ADD_ADMIN_CSS_DISABLED ) {
				$msg = sprintf(
					__( "<strong>RECOVERY MODE ENABLED:</strong> CSS output for this plugin is currently disabled for the entire admin area via use of the <code>%s</code> constant.", 'add-admin-css' ),
					'C2C_ADD_ADMIN_CSS_DISABLED'
				);
			} else {
				$msg = __( "<strong>RECOVERY MODE ENABLED:</strong> CSS output for this plugin is disabled on this page view.", 'add-admin-css' );;
			}

			echo <<<HTML
				<div class="notice notice-error">
					<p>{$msg}</p>
				</div>
HTML;
		}
	}

	/**
	 * Shows settings admin notices.
	 *
	 * Settings notices are only shown for admin pages listed under Settings.
	 * This plugin has its settings page under Appearance.
	 *
	 * @since 1.6
	 */
	public function show_admin_notices() {
		// Bail if not on the plugin setting page.
		if ( $this->options_page !== get_current_screen()->id ) {
			return;
		}

		settings_errors();
	}

	/**
	 * Register CSS files
	 *
	 * @return array Array of CSS files
	 */
	public function get_css_files() {
		$options = $this->get_options();

		/**
		 * Filters the list of CSS files to enqueue in the admin.
		 *
		 * @since 1.0
		 *
		 * @param array $files Array of CSS files.
		 */
		return (array) apply_filters( 'c2c_add_admin_css_files', $options['files'] );
	}

	/**
	 * Register CSS files
	 */
	public function register_css_files() {
		$files = $this->get_css_files();

		if ( $files ) {
			foreach ( (array) $files as $file ) {
				// Determine an adequate handle for the script.
				$file_parts = parse_url( $file );
				$handle = basename( $file_parts['path'], '.css' );

				// Determine a version for the script (the one specified, else the plugin's version).
				if ( isset( $file_parts['query'] ) ) {
					parse_str( $file_parts['query'], $file_query );
				}
				$version = ( ! empty( $file_query ) && isset( $file_query['ver'] ) ) ? $file_query['ver'] : $this->version;
				unset( $file_query );

				// FYI: There is still the potential for duplicate handles, which preclude subsequent uses from registering
				if ( strpos( $file, '://' ) !== false ) {
					$src = $file;
					$handle .= '-remote';
				} elseif ( '/' == $file[0] ) {
					$src = trailingslashit( get_option( 'siteurl' ) ) . ltrim( $file, '/' );
				} else {
					$src = trailingslashit( get_stylesheet_directory_uri() ) . $file;
				}
				$this->css_file_handles[] = $handle;
				wp_register_style( $handle, $src, array(), $version, 'all' );
			}
		}
	}

	/**
	 * Removes the query parameter to disable CSS output from redirect URLs.
	 *
	 * Needed to prevent the query parameter from propagating from page view
	 * through to form submission.
	 *
	 * @since 1.7
	 *
	 * @param string $url The redirect URL.
	 * @return string
	 */
	public function remove_query_param_from_redirects( $url ) {
		if ( is_admin() ) {
			$url = remove_query_arg( self::NO_CSS_QUERY_PARAM, $url );
		}

		return $url;
	}

	/**
	 * Determines if CSS can be output under current conditions.
	 *
	 * CSS will always be output in the admin unless:
	 * - The C2C_ADD_ADMIN_CSS_DISABLED constant is defined and true.
	 * - The 'c2c-no-css' query parameter is present with a value of '1'.
	 *
	 * @since 1.7
	 *
	 * @return bool True if CSS can be shown, otherwise false.
	 */
	public function can_show_css() {
		$can_show = true;

		// Recovery mode enabled via constant.
		if ( $can_show && defined( 'C2C_ADD_ADMIN_CSS_DISABLED' ) && C2C_ADD_ADMIN_CSS_DISABLED ) {
			$can_show = false;
		}

		// Recovery mode enabled via query parameter.
		if ( $can_show && isset( $_GET[ self::NO_CSS_QUERY_PARAM ] ) && '1' === $_GET[ self::NO_CSS_QUERY_PARAM ] ) {
			$can_show = false;
		}

		return $can_show;
	}

	/**
	 * Outputs CSS as header links and/or inline header styles
	 */
	public function add_css() {
		global $wp_styles;

		if ( ! $this->can_show_css() ) {
			return;
		}

		$options = $this->get_options();

		if ( ! empty( $this->css_file_handles ) ) {
			$wp_styles->do_items( $this->css_file_handles );
		}

		/**
		 * Filters the CSS that should be added directly to all admin pages.
		 *
		 * @since 1.0
		 *
		 * @param string $files CSS code (without `<style>` tag).
		 */
		$css = trim( apply_filters( 'c2c_add_admin_css', $options['css'] . "\n" ) );

		$type_attr = current_theme_supports( 'html5', 'style' ) ? '' : ' type="text/css"';

		if ( ! empty( $css ) ) {
			echo "
			<style{$type_attr}>
			$css
			</style>
			";
		}
	}

	/**
	 * Initializes CodeMirror for the CSS textarea.
	 *
	 * @since 1.6
	 */
	public function add_codemirror() {
		// Bail if not on the plugin setting page.
		if ( $this->options_page !== get_current_screen()->id ) {
			return;
		}

		// Bail if the code editor script hasn't been registered.
		if ( ! wp_script_is( 'code-editor', 'registered' ) ) {
			return;
		}

		// Enqueue code editor and settings for manipulating CSS.
		$settings = wp_enqueue_code_editor( array( 'type' => 'text/css' ) );

		// Bail if user disabled CodeMirror.
		if ( false === $settings ) {
			return;
		}

		// Inline the CodeMirror code.
		wp_add_inline_script(
			'code-editor',
			sprintf(
				'jQuery( function() { wp.codeEditor.initialize( "css", %s ); } );',
				wp_json_encode( $settings )
			)
		);
	}

} // end c2c_AddAdminCSS

add_action( 'plugins_loaded', array( 'c2c_AddAdminCSS', 'instance' ) );

endif; // end if !class_exists()
