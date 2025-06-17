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
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Wc_Muse_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

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
		$this->loader = new Wc_Muse_Loader();

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
	 * Manages all product hooks.
	 *
	 * @since    1.0.0
	 */
	public function define_products_hook() {

		$products_hook = new Wc_Muse_Products_Hook();
		
		// 	woocommerce product extra metabox
		$this->loader->add_filter( 'woocommerce_product_data_tabs', $products_hook, 'add_tab' );
		$this->loader->add_action( 'woocommerce_product_data_panels', $products_hook, 'add_tab_content' );
		$this->loader->add_action( 'woocommerce_product_after_variable_attributes', $products_hook, 'add_variation_meta_info', 10, 3 );
		$this->loader->add_action( 'woocommerce_ajax_save_product_variations', $products_hook, 'save_product_variations_meta', 10, 1 );
		$this->loader->add_action( 'save_post_product', $products_hook, 'save_post_meta', 0, 3 );

		// 	run all hooks
		$this->loader->run();

	}

	/**
	 * Render test page.
	 *
	 * @since    1.0.0
	 */
	public function the_test_template() {

		include WC_MUSE_ADMIN_TEMPLATE_DIR . 'wc-muse-admin-test.php';

	}

	/**
	 * Manages admin user hook.
	 * 
	 */
	public function define_user_hooks() {


		global $pagenow;


		$admin_user_hook_handler = new Wc_Muse_Admin_User_Hooks();


		$this->loader->add_action( 'restrict_manage_users', $admin_user_hook_handler, 'add_muse_id_section_filter', 10 );
		$this->loader->add_filter( 'pre_get_users', $admin_user_hook_handler, 'filter_users_by_muse_id_section', 10 );


		// 	run all hooks
		if ( is_admin() && 'users.php' === $pagenow )
			$this->loader->run();
	}

}
