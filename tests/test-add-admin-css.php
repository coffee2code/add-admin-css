<?php

defined( 'ABSPATH' ) or die();

class Add_Admin_CSS_Test extends WP_UnitTestCase {

	private $option_name = 'c2c_add_admin_css';

	public function setUp() {
		parent::setUp();

		$this->set_option();
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


	public static function get_css_file_links() {
		return array(
			array( 'https://maxcdn.example.com/font-awesome/4.4.0/css/font-awesome.min.css?ver=4.4.0' ),
			array( 'http://test.example.org/css/sample.css' ),
			array( 'http://example.org/css/site-relative.css' ),
			array( get_stylesheet_directory_uri() . '/theme-relative.css' ),
		);
	}

	public static function get_css_file_links2() {
		return array(
			array( 'https://maxcdn.example.com/font-awesome/4.4.0/css/font-awesome2.min.css?ver=4.4.0' ),
			array( 'http://test.example.org/css/sample2.css' ),
			array( 'http://example.org/css/site-relative2.css' ),
			array( get_stylesheet_directory_uri() . '/theme-relative2.css' ),
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

	public function set_option() {
		update_option( $this->option_name, array(
			'files' => array(
				'https://maxcdn.example.com/font-awesome/4.4.0/css/font-awesome.min.css?ver=4.4.0',
				'http://test.example.org/css/sample.css',
				'/css/site-relative.css',
				'theme-relative.css',
			),
			'css' => $this->add_css( '', '22' ),
		) );
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
		require( dirname( __FILE__ ) . '/../add-admin-css.php' );
		c2c_AddAdminCSS::instance()->init();
		c2c_AddAdminCSS::instance()->register_css_files();

		$this->option_name = c2c_AddAdminCSS::instance()->admin_options_name;

		$this->assertTrue( is_admin() );
	}

	public function test_class_name() {
		$this->assertTrue( class_exists( 'c2c_AddAdminCSS' ) );
	}

	public function test_plugin_framework_class_name() {
		$this->assertTrue( class_exists( 'c2c_AddAdminCSS_Plugin_040' ) );
	}

	public function test_plugin_framework_version() {
		$this->assertEquals( '040', c2c_AddAdminCSS::instance()->c2c_plugin_version() );
	}

	public function test_version() {
		$this->assertEquals( '1.4', c2c_AddAdminCSS::instance()->version() );
	}

	/**
	 * @dataProvider get_css_file_links
	 */
	public function test_css_files_are_added_to_admin_head( $link ) {
		$this->test_turn_on_admin();

		$this->assertContains( $link, $this->get_action_output() );
	}

	public function test_ver_query_arg_added_for_links() {
		$this->test_turn_on_admin();

		$this->assertContains( 'http://test.example.org/css/sample.css?ver=' . c2c_AddAdminCSS::instance()->version(), $this->get_action_output() );
	}

	public function test_ver_query_arg_added_for_relative_links() {
		$this->test_turn_on_admin();

		$this->assertContains( '/css/site-relative.css?ver=' . c2c_AddAdminCSS::instance()->version(), $this->get_action_output() );
	}

	public function test_ver_query_arg_not_added_if_link_already_has_it() {
		$this->test_turn_on_admin();

		$this->assertContains( "'https://maxcdn.example.com/font-awesome/4.4.0/css/font-awesome.min.css?ver=4.4.0'", $this->get_action_output() );
	}

	/**
	 * @dataProvider get_css_file_links2
	 */
	public function test_css_files_added_via_filter_are_added_to_admin_head( $link ) {
		add_filter( 'c2c_add_admin_css_files', array( $this, 'add_css_files' ) );

		$this->test_turn_on_admin();

		$this->assertContains( $link, $this->get_action_output() );
	}

	public function test_css_is_added_to_admin_head() {
		$this->test_turn_on_admin();

		$this->assertContains( $this->add_css( '', '22' ), $this->get_action_output() );
	}

	public function test_css_added_via_filter_is_added_to_admin_head() {
		$this->test_turn_on_admin();

		add_filter( 'c2c_add_admin_css', array( $this, 'add_css' ) );

		$this->assertContains( $this->add_css( '' ), $this->get_action_output() );
	}

	public function test_uninstall_deletes_option() {
		$option = 'c2c_add_admin_css';
		c2c_AddAdminCSS::instance()->get_options();

		$this->assertNotFalse( get_option( $option ) );

		c2c_AddAdminCSS::uninstall();

		$this->assertFalse( get_option( $option ) );
	}

}
