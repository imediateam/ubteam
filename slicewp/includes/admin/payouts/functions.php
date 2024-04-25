<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Includes the files needed for the Payout admin area
 *
 */
function slicewp_include_files_admin_payout() {

	// Get creative admin dir path
	$dir_path = plugin_dir_path( __FILE__ );

	// Include submenu page
	if( file_exists( $dir_path . 'class-submenu-page-payouts.php' ) )
		include $dir_path . 'class-submenu-page-payouts.php';

	// Include actions
	if( file_exists( $dir_path . 'functions-actions-payouts.php' ) )
		include $dir_path . 'functions-actions-payouts.php';

	// Include actions
	if( file_exists( $dir_path . 'functions-actions-payments.php' ) )
		include $dir_path . 'functions-actions-payments.php';

	// Include payout payments preview list table
	if( file_exists( $dir_path . 'class-list-table-payout-payments-preview.php' ) )
		include $dir_path . 'class-list-table-payout-payments-preview.php';

	// Include payouts list table
	if( file_exists( $dir_path . 'class-list-table-payouts.php' ) )
		include $dir_path . 'class-list-table-payouts.php';

	// Include payout payments list table
	if( file_exists( $dir_path . 'class-list-table-payout-payments.php' ) )
		include $dir_path . 'class-list-table-payout-payments.php';

	// Include payments list table
	if( file_exists( $dir_path . 'class-list-table-payments.php' ) )
		include $dir_path . 'class-list-table-payments.php';

	// Include payments commissions list table
	if( file_exists( $dir_path . 'class-list-table-payment-commissions.php' ) )
		include $dir_path . 'class-list-table-payment-commissions.php';

}
add_action( 'slicewp_include_files', 'slicewp_include_files_admin_payout' );


/**
 * Register the Payouts admin submenu page
 *
 */
function slicewp_register_submenu_page_payouts( $submenu_pages ) {

	if( ! is_array( $submenu_pages ) )
		return $submenu_pages;

	$submenu_pages['payouts'] = array(
		'class_name' => 'SliceWP_Submenu_Page_Payouts',
		'data' 		 => array(
			'page_title' => __( 'Payouts', 'slicewp' ),
			'menu_title' => __( 'Payouts', 'slicewp' ),
			'capability' => apply_filters( 'slicewp_submenu_page_capability_payouts', 'manage_options' ),
			'menu_slug'  => 'slicewp-payouts'
		)
	);

	return $submenu_pages;

}
add_filter( 'slicewp_register_submenu_page', 'slicewp_register_submenu_page_payouts', 45 );


/**
 * Localizes the payout methods custom messages before the plugin's admin script
 *
 * These messages are used for admin interaction purposes
 *
 */
function slicewp_enqueue_admin_scripts_payout_methods_messages() {

	$payout_methods = slicewp_get_payout_methods();
	$messages 		= array();

	foreach( $payout_methods as $payout_method_slug => $payout_method ) {

		if( ! empty( $payout_method['messages'] ) )
			$messages[$payout_method_slug] = $payout_method['messages'];

	}

	wp_localize_script( 'slicewp-script', 'slicewp_payout_methods_messages', $messages );
	
}
add_action( 'slicewp_enqueue_admin_scripts', 'slicewp_enqueue_admin_scripts_payout_methods_messages' );


/**
 * Generates a csv with the provided data
 *
 */
function slicewp_generate_csv( $header, $data, $filename = 'data.csv' ){

	header("Content-Type: text/csv; charset=utf-8");
	header("Content-Disposition: attachment; filename=" . $filename);
	header("Pragma: no-cache");
	header("Expires: 0");

	$output = fopen('php://output', 'w');
	fputcsv( $output, $header );

	foreach( $data as $row ) {

		unset( $csv_line );

		foreach( $header as $key => $value ) {
			
			if( isset( $row[$key] ) ) {

		 		$csv_line[] = $row[$key];

			}
		}

		fputcsv( $output, $csv_line );

	}
	
	die();

}