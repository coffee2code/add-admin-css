<?php

defined( 'ABSPATH' ) or die();

class Add_Admin_CSS_Test extends WP_UnitTestCase {

	protected $obj;

	public function setUp() {
		parent::setUp();

		$theme = wp_get_theme( 'twentyseventeen' );
		switch_theme( $theme->get_stylesheet() );

		add_theme_support( 'html5', array( 'script', 'style' ) );

		$this->obj = c2c_AddAdminCSS::instance();
	}

	public function tearDown() {
		parent::tearDown();

		unset( $GLOBALS['current_screen'] );
		unset( $GLOBALS['wp_styles']);
		$GLOBALS['wp_styles'] = new WP_Styles;

		if ( class_exists( 'c2c_AddAdminCSS' ) ) {
			$this->obj->reset();
			unset( $_GET[ c2c_AddAdminCSS::NO_CSS_QUERY_PARAM ] );
		}
	}


	//
	//
	// DATA PROVIDERS
	//
	//


	public static function get_settings_and_defaults() {
		return array(
			array( 'css' ),
			array( 'files' ),
		);
	}

	public static function get_default_hooks() {
		return array(
			array( 'action', 'admin_init',            'register_css_files' ),
			array( 'action', 'admin_head',            'add_css' ),
			array( 'action', 'admin_notices',         'recovery_mode_notice' ),
			array( 'action', 'admin_notices',         'show_admin_notices' ),
			array( 'action', 'admin_enqueue_scripts', 'add_codemirror' ),
			array( 'filter', 'wp_redirect',           'remove_query_param_from_redirects' ),
		);
	}

	public static function get_css_file_links() {
		return array(
			array( 'https://maxcdn.example.com/font-awesome/4.4.0/css/font-awesome.min.css?ver=4.4.0' ),
			array( 'http://test.example.org/css/sample.css' ),
			array( 'http://example.org/css/site-relative.css' ),
			array( 'http://example.org/wp-content/themes/twentyseventeen/theme-relative.css' ),
		);
	}

	public static function get_css_file_links2() {
		return array(
			array( 'https://maxcdn.example.com/font-awesome/4.4.0/css/font-awesome2.min.css?ver=4.4.0' ),
			array( 'http://test.example.org/css/sample2.css' ),
			array( 'http://example.org/css/site-relative2.css' ),
			array( 'http://example.org/wp-content/themes/twentyseventeen/theme-relative2.css' ),
		);
	}


	//
	//
	// HELPER FUNCTIONS
	//
	///


	public function get_action_output( $action = 'admin_head' ) {
		if ( 'wp_head' === $action ) {
			// This enqueues a script that doesn't exist in the develop.svn repo.
			remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		}

		ob_start();
		do_action( $action );
		$out = ob_get_contents();
		ob_end_clean();
		return $out;
	}

	public function add_css_files( $files ) {
		$files = array();
		$files[] = 'https://maxcdn.example.com/font-awesome/4.4.0/css/font-awesome2.min.css?ver=4.4.0';
		$files[] = 'http://test.example.org/css/sample2.css';
		$files[] = '/css/site-relative2.css';
		$files[] = 'theme-relative2.css';
		return $files;
	}

	public function add_css( $css, $modifier = '' ) {
		$more_css = '#example li' . $modifier . ' { color: red; }';
		return $css . $more_css;
	}

	// Use true for $settings for force use of defaults
	public function set_option( $settings = true ) {
		$obj = $this->obj;

		if ( true === $settings ) {
			$defaults = array(
				'files' => array(
					'https://maxcdn.example.com/font-awesome/4.4.0/css/font-awesome.min.css?ver=4.4.0',
					'http://test.example.org/css/sample.css',
					'/css/site-relative.css',
					'theme-relative.css',
				),
				'css' => $this->add_css( '', '22' ),
			);
		} else {
			$defaults = $obj->get_options();
		}

		$settings = wp_parse_args( (array) $settings, $defaults );
		$obj->update_option( $settings, true );
	}

	protected function fake_current_screen( $screen_id = 'hacky' ) {
		$this->test_turn_on_admin();
		set_current_screen( $screen_id );
		$this->obj->options_page = $screen_id;
		return $screen_id;
	}


	//
	//
	// TESTS
	//
	///

	public function test_css_added_via_filter_not_added_to_wp_head() {
		add_filter( 'c2c_add_admin_css', array( $this, 'add_css' ) );

		$this->assertNotContains( $this->add_css( '' ), $this->get_action_output( 'wp_head' ) );
	}

	/**
	 * @dataProvider get_css_file_links2
	 */
	public function test_css_files_added_via_filter_not_added_to_wp_head( $link ) {
		add_filter( 'c2c_add_admin_css_files', array( $this, 'add_css_files' ) );

		$this->assertNotContains( $link, $this->get_action_output( 'wp_head' ) );
	}

	public function test_class_name() {
		$this->assertTrue( class_exists( 'c2c_AddAdminCSS' ) );
	}

	public function test_plugin_framework_class_name() {
		$this->assertTrue( class_exists( 'c2c_AddAdminCSS_Plugin_051' ) );
	}

	public function test_plugin_framework_version() {
		$this->assertEquals( '051', $this->obj->c2c_plugin_version() );
	}

	public function test_version() {
		$this->assertEquals( '1.9.1', $this->obj->version() );
	}

	public function test_hooks_plugins_loaded() {
		$this->assertEquals( 10, has_action( 'plugins_loaded', array( 'c2c_AddAdminCSS', 'instance' ) ) );
	}

	public function test_setting_name() {
		$this->assertEquals( 'c2c_add_admin_css', $this->obj::SETTING_NAME );
	}

	public function test_query_param_name() {
		$this->assertEquals( 'c2c-no-css', $this->obj::NO_CSS_QUERY_PARAM );
	}

	/**
	 * @dataProvider get_settings_and_defaults
	 */
	public function test_default_settings( $setting ) {
		$options = $this->obj->get_options();

		$this->assertEmpty( $options[ $setting ] );
	}

	/***
	 * ALL ADMIN AREA RELATED TESTS NEED TO FOLLOW THIS FUNCTION
	 *****/

	public function test_turn_on_admin() {
		if ( ! defined( 'WP_ADMIN' ) ) {
			define( 'WP_ADMIN', true );
		}
		require( dirname( dirname( dirname( __FILE__ ) ) ) . '/add-admin-css.php' );
		$this->obj->init();
		$this->obj->register_css_files();

		$this->assertTrue( is_admin() );
	}

	/**
	 * @dataProvider get_default_hooks
	 */
	public function test_default_hooks( $hook_type, $hook, $function, $priority = 10, $class_method = true ) {
		$this->test_turn_on_admin();

		$callback = $class_method ? array( $this->obj, $function ) : $function;

		$prio = $hook_type === 'action' ?
			has_action( $hook, $callback ) :
			has_filter( $hook, $callback );

		$this->assertNotFalse( $prio );
		if ( $priority ) {
			$this->assertEquals( $priority, $prio );
		}
	}

	/*
	 * options_page_description()
	 */

	public function test_options_page_description() {
		$expected = '<h1>Add Admin CSS Settings</h1>' . "\n";
		$expected .= '<p class="see-help">See the "Help" link to the top-right of the page for more help.</p>' . "\n";
		$expected .= '<p>Add additional CSS to your admin pages, which allows you to tweak the appearance of the WordPress administration pages to your liking.</p>';
		$expected .= '<p>See the "Advanced Tips" tab in the "Help" section for info on how to use the plugin to programmatically customize CSS.</p>';
		$expected .= '<p>TIP: If you are primarily only interested in hiding certain administration interface elements, take a look at my <a href="https://wordpress.org/plugins/admin-trim-interface/" title="Admin Trim Interface">Admin Trim Interface</a> plugin. If you only want to hide in-page help text, check out my <a href="https://wordpress.org/plugins/admin-expert-mode/" title="">Admin Expert Mode</a> plugin. Both plugins are geared toward their respective tasks and are very simple to use, requiring no knowledge of CSS.</p>';

		$this->expectOutputRegex( '~' . preg_quote( $expected ) . '~', $this->obj->options_page_description() );
	}

	/**
	 * @dataProvider get_css_file_links
	 */
	public function test_css_files_are_added_to_admin_head( $link ) {
		$this->set_option();
		$this->test_turn_on_admin();

		$this->assertContains( $link, $this->get_action_output() );
	}

	public function test_ver_query_arg_added_for_links() {
		$this->set_option();
		$this->test_turn_on_admin();

		$this->assertContains( 'http://test.example.org/css/sample.css?ver=' . $this->obj->version(), $this->get_action_output() );
	}

	public function test_ver_query_arg_added_for_relative_links() {
		$this->set_option();
		$this->test_turn_on_admin();

		$this->assertContains( '/css/site-relative.css?ver=' . $this->obj->version(), $this->get_action_output() );
	}

	public function test_ver_query_arg_not_added_if_link_already_has_it() {
		$this->set_option();
		$this->test_turn_on_admin();

		$this->assertContains( "'https://maxcdn.example.com/font-awesome/4.4.0/css/font-awesome.min.css?ver=4.4.0'", $this->get_action_output() );
	}

	/**
	 * @dataProvider get_css_file_links2
	 */
	public function test_css_files_added_via_filter_are_added_to_admin_head( $link ) {
		$this->set_option();
		add_filter( 'c2c_add_admin_css_files', array( $this, 'add_css_files' ) );

		$this->test_turn_on_admin();

		$this->assertContains( $link, $this->get_action_output() );
	}

	public function test_css_is_added_to_admin_head() {
		$this->set_option();
		$this->test_turn_on_admin();

		$this->assertContains( $this->add_css( '', '22' ), $this->get_action_output() );
	}

	public function test_css_added_via_filter_is_added_to_admin_head() {
		$this->set_option();
		$this->test_turn_on_admin();

		add_filter( 'c2c_add_admin_css', array( $this, 'add_css' ) );

		$this->assertContains( $this->add_css( '' ), $this->get_action_output() );
	}

	public function test_add_css_to_head_with_just_css_no_html5_support( $expected = false ) {
		remove_theme_support( 'html5', 'script' );

		return $this->test_add_css_to_head_with_just_css( $expected, ' type="text/css"' );
	}

	public function test_add_css_to_head_with_just_css( $expected = false, $attr = '' ) {
		$css = $this->add_css( 'p { margin-top: 1.5em; }', 'settingfooter' );

		$this->set_option( array( 'css' => $css, 'files' => array() ) );
		$this->test_turn_on_admin();

		ob_start();
		$this->obj->add_css();
		$out = ob_get_contents();
		ob_end_clean();

		if ( false === $expected ) {
			$expected = "
			<style{$attr}>
			{$css}
			</style>
			";
		}

		$this->assertEquals( $expected, $out );

		return $out;
	}

	public function test_add_css_to_head_with_just_files( $expected = false ) {
		// Examples of different types of references.
		$files = array(
			'https://maxcdn.example.com/font-awesome/4.4.0/css/font-awesome.min.css?ver=4.4.0',
			'http://example.org/css/sample.css',
			'/css/site-relative.css',
			'theme-relative.css',
		);

		$this->set_option( array( 'css' => '', 'files' => $files ) );
		$this->test_turn_on_admin();

		ob_start();
		$this->obj->add_css();
		$out = ob_get_contents();
		ob_end_clean();

		$ver = $this->obj->version();

		if ( false === $expected ) {
			$expected = "<link rel='stylesheet' id='font-awesome.min-remote-css'  href='https://maxcdn.example.com/font-awesome/4.4.0/css/font-awesome.min.css?ver=4.4.0' media='all' />
<link rel='stylesheet' id='sample-remote-css'  href='http://example.org/css/sample.css?ver={$ver}' media='all' />
<link rel='stylesheet' id='site-relative-css'  href='http://example.org/css/site-relative.css?ver={$ver}' media='all' />
<link rel='stylesheet' id='theme-relative-css'  href='http://example.org/wp-content/themes/twentyseventeen/theme-relative.css?ver={$ver}' media='all' />
";
		}

		$this->assertEquals( $expected, $out );

		return $out;
	}

	/*
	 * remove_query_param_from_redirects()
	 */

	public function test_remove_query_param_from_redirects() {
		$url = 'https://example.com/wp-admin/themes.php?page=add-admin-css%2Fadd-admin.css.php';

		$this->test_turn_on_admin();

		$this->assertEquals(
			$url,
			$this->obj->remove_query_param_from_redirects( $url . '&' . c2c_AddAdminCSS::NO_CSS_QUERY_PARAM . '=1' )
		);
	}

	/*
	 * can_show_css()
	 */

	public function test_can_show_css() {
		$this->test_turn_on_admin();

		$this->assertTrue( $this->obj->can_show_css() );

		$_GET[ c2c_AddAdminCSS::NO_CSS_QUERY_PARAM ] = '0';

		$this->assertTrue( $this->obj->can_show_css() );
	}

	public function test_can_show_css_with_true_query_param() {
		$this->test_turn_on_admin();

		$_GET[ c2c_AddAdminCSS::NO_CSS_QUERY_PARAM ] = '1';

		$this->assertFalse( $this->obj->can_show_css() );
	}

	/*
	 * recovery_mode_notice()
	 */

	public function test_recovery_mode_via_query_param_disables_add_css() {
		$this->test_can_show_css_with_true_query_param();

		$out = $this->test_add_css_to_head_with_just_css( '' );

		$this->assertEmpty( $out );
	}

	public function test_recovery_mode_notice_when_css_not_disabled() {
		$this->fake_current_screen();

		$this->assertEmpty( $this->get_action_output( 'admin_notices' ) );
	}

	public function test_recovery_mode_notice_when_css_disabled_by_query_param() {
		$this->fake_current_screen();

		$this->test_can_show_css_with_true_query_param();

		$expected = "				<div class=\"notice notice-error\">
					<p><strong>RECOVERY MODE ENABLED:</strong> CSS output for this plugin is disabled on this page view.</p>
				</div>";

		$this->assertEquals( $expected, $this->get_action_output( 'admin_notices' ) );
	}

	/****************************************
	 * NOTE: Anything beyond this point will run with the
	 * C2C_ADD_ADMIN_CSS_DISABLED defined and true.
	 ****************************************/

	public function test_can_show_css_with_true_constant() {
		$this->test_turn_on_admin();

		define( 'C2C_ADD_ADMIN_CSS_DISABLED', true );

		$this->assertFalse( $this->obj->can_show_css() );
	}

	public function test_recovery_mode_via_constant_disables_add_css() {
		$out = $this->test_add_css_to_head_with_just_css( '' );

		$this->assertEmpty( $out );
	}

	public function test_recovery_mode_notice_when_css_disabled_by_constant() {
		$this->fake_current_screen();

		$expected = "				<div class=\"notice notice-error\">
					<p><strong>RECOVERY MODE ENABLED:</strong> CSS output for this plugin is currently disabled for the entire admin area via use of the <code>C2C_ADD_ADMIN_CSS_DISABLED</code> constant.</p>
				</div>";

		$this->assertEquals( $expected, $this->get_action_output( 'admin_notices' ) );
	}

	/*
	 * Setting handling
	 */

	/*
	// This is normally the case, but the unit tests save the setting to db via
	// setUp(), so until the unit tests are restructured somewhat, this test
	// would fail.
	public function test_does_not_immediately_store_default_settings_in_db() {
		$option_name = c2c_AddAdminCSS::SETTING_NAME;
		// Get the options just to see if they may get saved.
		$options     = $this->obj->get_options();

		$this->assertFalse( get_option( $option_name ) );
	}
	*/

	public function test_uninstall_deletes_option() {
		$option_name = c2c_AddAdminCSS::SETTING_NAME;
		$options     = $this->obj->get_options();

		// Explicitly set an option to ensure options get saved to the database.
		$this->set_option( array( 'css' => 'p { margin-top: 1.5em; }' ) );

		$this->assertNotEmpty( $options );
		$this->assertNotFalse( get_option( $option_name ) );

		c2c_AddAdminCSS::uninstall();

		$this->assertFalse( get_option( $option_name ) );
	}

}
