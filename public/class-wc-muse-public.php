<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       support@dinkuminteractive.com
 * @since      1.0.0
 *
 * @package    Wc_Muse
 * @subpackage Wc_Muse/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Wc_Muse
 * @subpackage Wc_Muse/public
 * @author     Dinkum Interactive <support@dinkuminteractive.com>
 */
class Wc_Muse_Public {

	public $dir = array(
		'css_dir_uri'         => '',
		'js_dir_uri'          => '',
		'styles_to_override'  => '',
		'scripts_to_override' => '',
	);

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
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		// Setup style settings
		$this->dir['css_dir_uri']        = WC_MUSE_PUBLIC_CSS_URI;
		$this->dir['styles_to_override'] = array( 'wp-style' );

		// Setup scripts settings
		$this->dir['js_dir_uri']          = WC_MUSE_PUBLIC_JS_URI;
		$this->dir['scripts_to_override'] = array( 'global' );
	}

	public function register_scripts() {

		// Styles
		$css_dir_uri      = $this->dir['css_dir_uri'];
		$css_dependencies = $this->dir['styles_to_override'];

		wp_register_style( 'woo-account-pages-style', "$css_dir_uri/woo-account-style.css", $css_dependencies, '1.0.1', 'all' );

		// Scripts
		$js_dir_uri      = $this->dir['js_dir_uri'];
		$js_dependencies = $this->dir['scripts_to_override'];

		wp_register_script( 'woo-account-pages-scripts', "$js_dir_uri/woo-account-scripts.js", $js_dependencies, '1.0.0', true );

	}

	public function enqueue_scripts() {

		if ( ! is_account_page() ) {
			return false;
		}

		wp_enqueue_style( 'woo-account-pages-style' );
		wp_enqueue_script( 'woo-account-pages-scripts' );

		wp_localize_script(
			'woo-account-pages-scripts',
			'woo_account',
			apply_filters( 'woo-account-localize-script', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) )
		);

	}

	/**
	 * Use template in this plugin.
	 *
	 * @since    1.0.0
	 */
	public function woocommerce_locate_template( $template, $template_name, $template_path ) {

		global $woocommerce;

		$_template = $template;

		if ( ! $template_path ) {
			$template_path = $woocommerce->template_url;
		}

		$plugin_path = WC_MUSE_TEMPLATE_DIR;

		$template = locate_template(
			array(
				$template_path . $template_name,
				$template_name,
			)
		);

		if ( ! $template && file_exists( $plugin_path . $template_name ) ) {
			$template = $plugin_path . $template_name;
		}

		if ( ! $template ) {
			$template = $_template;
		}

		return $template;
	}
}
