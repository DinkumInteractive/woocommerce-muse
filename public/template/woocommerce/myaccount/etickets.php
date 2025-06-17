<?php 
//	Validate page availability
if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( 'Wc_Muse_Orders' ) ) exit;


//	Page helpers
global $wp;
$page_url = home_url( $wp->request );
$wc_muse_customer_manager = Wc_Muse_Customers::get_instance();


//	Page switch
$allowed_actions = array( 'list', 'view', 'print' );
$action = ( isset( $_GET['action'] ) && in_array( $_GET['action'], $allowed_actions ) ? $_GET['action'] : 'list' );

switch ($action) {

	case 'list':
		wc_get_template( 'myaccount/etickets-list.php' );
		break;

	case 'view':
		wc_get_template( 'myaccount/etickets-view.php' );
		break;

	case 'print':
		wc_get_template( 'myaccount/etickets-print.php' );
		break;

}

