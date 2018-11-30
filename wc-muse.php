<?php

/**
 * @link              support@dinkuminteractive.com
 * @since             1.0.0
 * @package           Wc_Muse
 *
 * @wordpress-plugin
 * Plugin Name:       WooCommerce Muse
 * Plugin URI:        #
 * Description:       Woocommerce Muse Integration.
 * Version:           1.0.0
 * Author:            Dinkum Interactive
 * Author URI:        support@dinkuminteractive.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wc-muse
 * Domain Path:       /languages
 * WC requires at least: 	3.0
 * WC tested up to: 		3.2.3
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) die;

/**
 * Current plugin version.
 */
define( 'WC_MUSE_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 */
function activate_wc_muse() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wc-muse-activator.php';
	Wc_Muse_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_wc_muse() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wc-muse-deactivator.php';
	Wc_Muse_Deactivator::deactivate();
}

function wc_muse_woocommerce_missing_notice() {

	$class = 'notice notice-error';

	$text = '<strong>'. __( 'Warning', 'wc-muse' ) .'</strong>' . __( ': WooCommerce Muse needs at least Woocommerce 3.0.0 to function properly.', 'wc-muse' );

	$notice = "<div class='$class'><p>$text</p></div>";

	echo $notice;

}

function wc_muse_woocommerce_missing() {

	//	WooCommerce is missing.
	if ( 
		// 	Require WooCommerce
		is_admin() && in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) &&
		// 	Require WooCommerce 3+
		defined( 'WOOCOMMERCE_VERSION' ) && version_compare( WOOCOMMERCE_VERSION, '3.0.0', '>=' )
	) {

		update_option( 'wc_muse_active', true );

		return false;

	} else {
		
		update_option( 'wc_muse_active', false );

		add_action( 'admin_notices', 'wc_muse_woocommerce_missing_notice' );

		return true;

	}

}

register_activation_hook( __FILE__, 'activate_wc_muse' );

register_deactivation_hook( __FILE__, 'deactivate_wc_muse' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wc-muse.php';

/**
 * Register constants
 *
 * @since    1.0.0
 */
function wc_muse_define_constants() {

	define( 'WC_MUSE_ADMIN_TEMPLATE_DIR', plugin_dir_path( __FILE__ ) . 'admin/partials/' );

}

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function run_wc_muse() {

	if ( wc_muse_woocommerce_missing() ) return false;
	
	if ( ! get_option( 'wc_muse_active' ) ) return false;

	wc_muse_define_constants();

	$plugin = new Wc_Muse();

	$plugin->init();

	$plugin->run();

}

add_action( 'plugins_loaded', 'run_wc_muse' );

/**
 * Add plugin cron interval.
 *
 * @since    1.0.0
 */
if ( 'yes' === get_option( 'wc-muse-enable_cron' ) ) {

	require_once plugin_dir_path( __FILE__ ) . 'includes/core/class-wc-muse-core.php';
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wc-muse-cron.php';

	$cron = new Wc_Muse_Cron();
	$core = new Wc_Muse_Core();

	add_filter( 'cron_schedules', array( $cron, 'add_cron_schedules' ) );
	add_action( 'wc_muse_order_export_success', array( $core, 'change_order_status' ), 10, 2 );
  add_action( 'wc_muse_order_export_success', array( $core, 'update_success_meta' ), 10, 2 );
  add_action( 'wc_muse_order_export_failed', array( $core, 'update_failed_meta' ), 10, 2 );

	//	Add cron event to queue.
	$cron->add_cron_schedule();
	
}

add_action( 'wc_muse_cron_events', 'run_wc_muse_cron_events' );

function run_wc_muse_cron_events() {
	$plugin = new Wc_Muse();
	$plugin->load_dependencies();
	Wc_Muse_Core::export_orders();
}
