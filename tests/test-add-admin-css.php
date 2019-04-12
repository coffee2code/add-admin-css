<?php

defined( 'ABSPATH' ) or die();

class Add_Admin_CSS_Test extends WP_UnitTestCase {

	public function setUp() {
		$theme = wp_get_theme( 'twentyseventeen' );
		switch_theme( $theme->get_stylesheet() );
	}

	public function tearDown() {
		parent::tearDown();

		remove_filter( 'c2c_add_admin_css_files', array( $this, 'add_css_files' ) );
		remove_filter( 'c2c_add_admin_css',       array( $this, 'add_css' ) );

		unset( $GLOBALS['wp_styles']);
		$GLOBALS['wp_styles'] = new WP_Styles;

		if ( class_exists( 'c2c_AddAdminCSS' ) ) {
			c2c_AddAdminCSS::instance()->reset();
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
		$obj = c2c_AddAdminCSS::instance();

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

	/***
	 * ALL ADMIN AREA RELATED TESTS NEED TO FOLLOW THIS FUNCTION
	 *****/

	public function test_turn_on_admin() {
		if ( ! defined( 'WP_ADMIN' ) ) {
			define( 'WP_ADMIN', true );
		}
		require( dirname( dirname( __FILE__ ) ) . '/add-admin-css.php' );
		c2c_AddAdminCSS::instance()->init();
		c2c_AddAdminCSS::instance()->register_css_files();

		$this->assertTrue( is_admin() );
	}

	public function test_class_name() {
		$this->assertTrue( class_exists( 'c2c_AddAdminCSS' ) );
	}

	public function test_plugin_framework_class_name() {
		$this->assertTrue( class_exists( 'c2c_AddAdminCSS_Plugin_049' ) );
	}

	public function test_plugin_framework_version() {
		$this->assertEquals( '049', c2c_AddAdminCSS::instance()->c2c_plugin_version() );
	}

	public function test_version() {
		$this->assertEquals( '1.6', c2c_AddAdminCSS::instance()->version() );
	}

	/**
	 * @dataProvider get_settings_and_defaults
	 */
	public function test_default_settings( $setting ) {
		$options = c2c_AddAdminCSS::instance()->get_options();

		$this->assertEmpty( $options[ $setting ] );
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

		$this->assertContains( 'http://test.example.org/css/sample.css?ver=' . c2c_AddAdminCSS::instance()->version(), $this->get_action_output() );
	}

	public function test_ver_query_arg_added_for_relative_links() {
		$this->set_option();
		$this->test_turn_on_admin();

		$this->assertContains( '/css/site-relative.css?ver=' . c2c_AddAdminCSS::instance()->version(), $this->get_action_output() );
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
		$options     = c2c_AddAdminCSS::instance()->get_options();

		$this->assertFalse( get_option( $option_name ) );
	}
	*/

	public function test_uninstall_deletes_option() {
		$option_name = c2c_AddAdminCSS::SETTING_NAME;
		$options     = c2c_AddAdminCSS::instance()->get_options();

		// Explicitly set an option to ensure options get saved to the database.
		$this->set_option( array( 'css' => 'p { margin-top: 1.5em; }' ) );

		$this->assertNotEmpty( $options );
		$this->assertNotFalse( get_option( $option_name ) );

		c2c_AddAdminCSS::uninstall();

		$this->assertFalse( get_option( $option_name ) );
	}

}
