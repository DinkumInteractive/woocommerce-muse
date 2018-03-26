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

	$text = '<strong>'. __( 'Warning', 'wc-muse' ) .'</strong>' . __( ': WooCommerce Muse needs Woocommerce to function properly.', 'wc-muse' );

	$notice = "<div class='$class'><p>$text</p></div>";

	echo $notice;

}

function wc_muse_woocommerce_missing() {

	//	WooCommerce is missing.
	if ( is_admin() && in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

		update_option( 'wc_muse_active', true );

	} else {
		
		update_option( 'wc_muse_active', false );

		add_action( 'admin_notices', 'wc_muse_woocommerce_missing_notice' );

	}

}

register_activation_hook( __FILE__, 'activate_wc_muse' );

register_deactivation_hook( __FILE__, 'deactivate_wc_muse' );

add_action( 'admin_init', 'wc_muse_woocommerce_missing' );

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

	if ( ! get_option( 'wc_muse_active' ) ) return false;

	wc_muse_define_constants();

	$plugin = new Wc_Muse();

	$plugin->run();

}

run_wc_muse();
