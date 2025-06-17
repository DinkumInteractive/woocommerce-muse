<?php

/**
 * The core plugin class.
 *
 * @since      1.0.0
 * @package    Wc_Muse
 * @subpackage Wc_Muse/includes
 * @author     Dinkum Interactive <support@dinkuminteractive.com>
 */
class Wc_Muse {

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
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		$this->version = WC_MUSE_VERSION;
		$this->plugin_name = 'wc-muse';
	}

	/**
	 * Define the core functionality of the plugin.
	 *
	 * @since    1.0.0
	 */
	public function init() {
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	public function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wc-muse-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wc-muse-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wc-muse-admin.php';

		/**
		 * The class responsible for defining all woocommerce settings.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wc-muse-settings.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wc-muse-public.php';

		/**
		 * The class responsible for managing connection to muse.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/core/class-wc-muse-core.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/core/class-wc-muse-connector.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/core/class-wc-muse-orders.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/core/class-wc-muse-customers.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/core/class-wc-muse-tickets.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/core/class-wc-muse-helper.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/core/class-wc-muse-payment.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/core/class-wc-muse-handler-creditcard.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/core/class-wc-muse-mail.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/core/class-wc-muse-account-function.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/core/class-wc-account-eticket-detail.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/core/class-wc-muse-eticket.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/core/class-wc-muse-wp-profile.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wc-muse-cron.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wc-muse-acf-folder-manager.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wc-muse-products-hooks.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wc-muse-admin-user-hooks.php';

		$this->loader = new Wc_Muse_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Wc_Muse_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Wc_Muse_Admin( $this->get_plugin_name(), $this->get_version() );
		$wc_muse = new Wc_Muse_Core();

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		//	WooCommerce admin settings
		$this->loader->add_filter( 'woocommerce_settings_tabs_array', $plugin_admin, 'add_settings_tab', 25 );
		$this->loader->add_action( 'woocommerce_settings_tabs_wc_muse_settings', $plugin_admin, 'settings_tab' );
		$this->loader->add_action( 'woocommerce_update_options_wc_muse_settings', $plugin_admin, 'update_settings' );

		// 	woocommerce product hooks
		$plugin_admin->define_products_hook();
		$plugin_admin->define_user_hooks();

		//	WooCommerce Muse
		// $this->loader->add_filter( 'wc_muse_validate_token', $wc_muse, 'validate_token' );

		// 	Test Page
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_test_menu' );

		//	ACF folder manager
		$acf_folder_path = WC_MUSE_CUSTOM_FIELDS_DIR;
		$field_group_ids = array(

			//	PCMS Options – Eticket
			// 73288,

			//	Muse Order
			// 73313,

			//	Muse User
			// 73315,
		);
		// $acf_folder_manager = new WC_Muse_ACF_Folder_Manager( $acf_folder_path, $field_group_ids );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Wc_Muse_Public( $this->get_plugin_name(), $this->get_version() );

		// Account template function
		$this->loader->add_filter( 'woocommerce_locate_template', $plugin_public, 'woocommerce_locate_template', 1, 3 );

		// Cron
		$cron = new Wc_Muse_Cron();

		// if cron interval was saved
		$this->loader->add_action( 'update_option_wc-muse-enable_cron', $cron, 'on_settings_saved', 99, 2 );
		$this->loader->add_action( 'update_option_wc-muse-cron_in_minute', $cron, 'on_settings_saved', 10, 2 );

		// Credit Card
		// $Handler_Credit_Card = new Wc_Muse_Handler_Credit_Card();
		// $this->loader->add_action( 'wc_braintree_credit_card_api_request_performed', $Handler_Credit_Card, 'maybe_export_cc', 10, 3 );
		//

		//	Add account pages style.
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'register_scripts' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		//	WC MyAccount custom functions
		$account_function = new Wc_Muse_Account_Function();
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Wc_Muse_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Compare the version number of WooCommerce.
	 *
	 * @since     1.0.0     method to compare currently used WooCommerce version.
	 * @return    string    The version number of the plugin.
	 */
	public static function woocommerce_version_compare( $version, $compare = '>=' ) {
		return ( defined( 'WOOCOMMERCE_VERSION' ) && version_compare( WOOCOMMERCE_VERSION, $version, $compare ) );
	}

}
