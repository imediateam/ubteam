<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Includes the Paid Memberships Pro files
 *
 */
function slicewp_include_files_pmpro() {

	// Get legend dir path
	$dir_path = plugin_dir_path( __FILE__ );

	// Include main class
	if( file_exists( $dir_path . 'class-integration-paid-memberships-pro.php' ) )
		include $dir_path . 'class-integration-paid-memberships-pro.php';

	// Include hooks functions
	if( slicewp_is_integration_active( 'pmpro' ) && slicewp_is_integration_plugin_active( 'pmpro' ) ) {

		if( file_exists( $dir_path . 'functions-hooks-integration-paid-memberships-pro.php' ) )
			include $dir_path . 'functions-hooks-integration-paid-memberships-pro.php';
		
	}

}
add_action( 'slicewp_include_files', 'slicewp_include_files_pmpro' );


/**
 * Register the class that handles PMPRO related actions
 *
 * @param array $integrations
 *
 * @return array
 *
 */
function slicewp_register_integration_pmpro( $integrations ) {

	$integrations['pmpro'] = 'SliceWP_Integration_Paid_Memberships_Pro';

	return $integrations;

}
add_filter( 'slicewp_register_integration', 'slicewp_register_integration_pmpro', 20 );


/**
 * Verifies if Paid Memberships Pro is active
 *
 * @param bool $is_active
 *
 * @return bool
 *
 */
function slicewp_is_integration_plugin_active_pmpro( $is_active = false ) {

	if( defined( 'PMPRO_VERSION' ) )
		$is_active = true;

	return $is_active;

}
add_filter( 'slicewp_is_integration_plugin_active_pmpro', 'slicewp_is_integration_plugin_active_pmpro' );