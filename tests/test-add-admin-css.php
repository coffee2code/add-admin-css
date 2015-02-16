<?php

class Add_Admin_CSS_Test extends WP_UnitTestCase {

	private $option_name = 'c2c_add_admin_css';

	function setUp() {
		parent::setUp();

		$this->set_option();
	}

	function tearDown() {
		parent::tearDown();

		remove_filter( 'c2c_add_admin_css_files', array( $this, 'add_css_files' ) );
		remove_filter( 'c2c_add_admin_css',       array( $this, 'add_css' ) );

		unset( $GLOBALS['wp_styles']);
		$GLOBALS['wp_styles'] = new WP_Styles;

		if ( class_exists( 'c2c_AddAdminCSS' ) ) {
			c2c_AddAdminCSS::instance()->reset();
		}
	}


	/**
	 *
	 * DATA PROVIDERS
	 *
	 */


	public static function get_css_file_links() {
		return array(
			array( 'http://test.example.org/css/sample.css' ),
			array( 'http://example.org/css/site-relative.css' ),
			array( get_stylesheet_directory_uri() . '/theme-relative.css' ),
		);
	}

	public static function get_css_file_links2() {
		return array(
			array( 'http://test.example.org/css/sample2.css' ),
			array( 'http://example.org/css/site-relative2.css' ),
			array( get_stylesheet_directory_uri() . '/theme-relative2.css' ),
		);
	}


	/**
	 *
	 * HELPER FUNCTIONS
	 *
	 */


	function get_action_output( $action = 'admin_head' ) {
		ob_start();
		do_action( $action );
		$out = ob_get_contents();
		ob_end_clean();
		return $out;
	}

	function add_css_files( $files ) {
		$files = array();
		$files[] = 'http://test.example.org/css/sample2.css';
		$files[] = '/css/site-relative2.css';
		$files[] = 'theme-relative2.css';
		return $files;
	}

	function add_css( $css, $modifier = '' ) {
		$more_css = '#example li' . $modifier . ' { color: red; }';
		return $css . $more_css;
	}

	function set_option() {
		update_option( $this->option_name, array(
			'files' => array(
				'http://test.example.org/css/sample.css',
				'/css/site-relative.css',
				'theme-relative.css',
			),
			'css' => $this->add_css( '', '22' ),
		) );
	}


	/**
	 *
	 * TESTS
	 *
	 */

	function test_css_added_via_filter_not_added_to_wp_head() {
		add_filter( 'c2c_add_admin_css', array( $this, 'add_css' ) );

		$this->assertNotContains( $this->add_css( '' ), $this->get_action_output( 'wp_head' ) );
	}

	/**
	 * @dataProvider get_css_file_links2
	 */
	function test_css_files_added_via_filter_not_added_to_wp_head( $link ) {
		add_filter( 'c2c_add_admin_css_files', array( $this, 'add_css_files' ) );

		$this->assertNotContains( $link, $this->get_action_output( 'wp_head' ) );
	}

	/***
	 * ALL ADMIN AREA RELATED TESTS NEED TO FOLLOW THIS FUNCTION
	 *****/

	function test_turn_on_admin() {
		if ( ! defined( 'WP_ADMIN' ) ) {
			define( 'WP_ADMIN', true );
		}
		require( __DIR__ . '/../add-admin-css.php' );
		c2c_AddAdminCSS::instance()->init();
		c2c_AddAdminCSS::instance()->register_css_files();

		$this->option_name = c2c_AddAdminCSS::instance()->admin_options_name;

		$this->assertTrue( is_admin() );
	}


	function test_class_name() {
		$this->assertTrue( class_exists( 'c2c_AddAdminCSS' ) );
	}

	function test_plugin_framework_class_name() {
		$this->assertTrue( class_exists( 'C2C_Plugin_039' ) );
	}

	function test_version() {
		$this->assertEquals( '1.3.2', c2c_AddAdminCSS::instance()->version() );
	}

	/**
	 * @dataProvider get_css_file_links
	 */
	function test_css_files_are_added_to_admin_head( $link ) {
		$this->test_turn_on_admin();

		$this->assertContains( $link, $this->get_action_output() );
	}

	/**
	 * @dataProvider get_css_file_links2
	 */
	function test_css_files_added_via_filter_are_added_to_admin_head( $link ) {
		add_filter( 'c2c_add_admin_css_files', array( $this, 'add_css_files' ) );

		$this->test_turn_on_admin();

		$this->assertContains( $link, $this->get_action_output() );
	}

	function test_css_is_added_to_admin_head() {
		$this->test_turn_on_admin();

		$this->assertContains( $this->add_css( '', '22' ), $this->get_action_output() );
	}

	function test_css_added_via_filter_is_added_to_admin_head() {
		$this->test_turn_on_admin();

		add_filter( 'c2c_add_admin_css', array( $this, 'add_css' ) );

		$this->assertContains( $this->add_css( '' ), $this->get_action_output() );
	}

}
