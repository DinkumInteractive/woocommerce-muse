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

register_activation_hook( __FILE__, 'activate_wc_muse' );
register_deactivation_hook( __FILE__, 'deactivate_wc_muse' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wc-muse.php';

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function run_wc_muse() {

	$plugin = new Wc_Muse();
	$plugin->run();

}
run_wc_muse();
