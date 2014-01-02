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
			array( 'http://example.org/wp-content/themes/twentyfourteen/theme-relative.css' ),
		);
	}

	public static function get_css_file_links2() {
		return array(
			array( 'http://test.example.org/css/sample2.css' ),
			array( 'http://example.org/css/site-relative2.css' ),
			array( 'http://example.org/wp-content/themes/twentyfourteen/theme-relative2.css' ),
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
		$head = $this->get_action_output( 'wp_head' );

		$this->assertEmpty( strpos( $head,  $this->add_css( '' ) ) );
	}

	/**
	 * @dataProvider get_css_file_links2
	 */
	function test_css_files_added_via_filter_not_added_to_wp_head( $link ) {
		add_filter( 'c2c_add_admin_css_files', array( $this, 'add_css_files' ) );

		$head = $this->get_action_output( 'wp_head' );

		$this->assertEmpty( intval( strpos( $head, $link ) ) );
	}

	/***
	 * ALL ADMIN AREA RELATED TESTS NEED TO FOLLOW THIS FUNCTION
	 *****/

	function test_turn_on_admin() {
		define( 'WP_ADMIN', true );
		require dirname( __FILE__ ) . '/../add-admin-css.php';
		c2c_AddAdminCSS::instance()->init();
		c2c_AddAdminCSS::instance()->register_css_files();

		$this->option_name = c2c_AddAdminCSS::instance()->admin_options_name;

		$this->assertTrue( is_admin() );
	}


	/**
	 * @dataProvider get_css_file_links
	 */
	function test_css_files_are_added_to_admin_head( $link ) {
		c2c_AddAdminCSS::instance()->register_css_files();

		$head = $this->get_action_output();

		$this->assertGreaterThan( 0, intval( strpos( $head, $link ) ) );
	}

	/**
	 * @dataProvider get_css_file_links2
	 */
	function test_css_files_added_via_filter_are_added_to_admin_head( $link ) {
		add_filter( 'c2c_add_admin_css_files', array( $this, 'add_css_files' ) );
		c2c_AddAdminCSS::instance()->register_css_files();

		$head = $this->get_action_output();

		$this->assertGreaterThan( 0, intval( strpos( $head, $link ) ) );
	}

	function test_css_is_added_to_admin_head() {
		$head = $this->get_action_output();

		$this->assertGreaterThan( 0, intval( strpos( $head, $this->add_css( '', '22' ) ) ) );
	}

	function test_css_added_via_filter_is_added_to_admin_head() {
		add_filter( 'c2c_add_admin_css', array( $this, 'add_css' ) );

		$head = $this->get_action_output();

		$this->assertGreaterThan( 0, intval( strpos( $head,  $this->add_css( '' ) ) ) );
	}

}
