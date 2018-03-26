<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    Wc_Muse
 * @subpackage Wc_Muse/admin
 * @author     Dinkum Interactive <support@dinkuminteractive.com>
 */
class Wc_Muse_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wc-muse-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wc-muse-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Add settings tab to WooCommerce admin page.
	 *
	 * @since    1.0.0
	 */
	public function add_settings_tab( $settings_tabs ) {

		$settings_tabs['wc_muse_settings'] = __( 'Muse', 'wc-muse' );

		return $settings_tabs;

	}

	/**
	 * Print settings tab.
	 *
	 * @since    1.0.0
	 */
	public function settings_tab() {

		$settings = new Wc_Muse_Settings();

		woocommerce_admin_fields( $settings->get_settings() );

	}

	/**
	 * Update settings tab.
	 *
	 * @since    1.0.0
	 */
	public function update_settings() {

		$settings = new Wc_Muse_Settings();

		$settings->validate_input();

		woocommerce_update_options( $settings->get_settings() );

	}

	/**
	 * Add test main menu.
	 *
	 * @since    1.0.0
	 */
	public function add_test_menu() {

		$page_title = __( 'WC Muse Test', 'wc-muse' );
		$menu_title = __( 'WC Muse Test', 'wc-muse' );
		$capability = 'manage_options';
		$menu_slug = 'wc-muse-test';
		$callback = array( $this, 'the_test_template' );
		$icon = false;
		$position = false;

		add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $callback, $icon, $position );

	}

	/**
	 * Render test page.
	 *
	 * @since    1.0.0
	 */
	public function the_test_template() {

		include WC_MUSE_ADMIN_TEMPLATE_DIR . 'wc-muse-admin-test.php';

	}

}
