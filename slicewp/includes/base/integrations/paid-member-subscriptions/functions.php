<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Includes the Paid Member Subscriptions files
 *
 */
function slicewp_include_files_pms() {

	// Get legend dir path
	$dir_path = plugin_dir_path( __FILE__ );

	// Include main class
	if( file_exists( $dir_path . 'class-integration-paid-member-subscriptions.php' ) )
		include $dir_path . 'class-integration-paid-member-subscriptions.php';

	// Include hooks functions
	if( slicewp_is_integration_active( 'pms' ) && slicewp_is_integration_plugin_active( 'pms' ) ) {

		if( file_exists( $dir_path . 'functions-hooks-integration-paid-member-subscriptions.php' ) )
			include $dir_path . 'functions-hooks-integration-paid-member-subscriptions.php';
		
	}

}
add_action( 'slicewp_include_files_late', 'slicewp_include_files_pms' );


/**
 * Register the class that handles PMS related actions
 *
 * @param array $integrations
 *
 * @return array
 *
 */
function slicewp_register_integration_pms( $integrations ) {

	$integrations['pms'] = 'SliceWP_Integration_Paid_Member_Subscriptions';

	return $integrations;

}
add_filter( 'slicewp_register_integration', 'slicewp_register_integration_pms', 30 );


/**
 * Verifies if Paid Member Subscriptions is active
 *
 * @param bool $is_active
 *
 * @return bool
 *
 */
function slicewp_is_integration_plugin_active_pms( $is_active = false ) {

	if( class_exists( 'Paid_Member_Subscriptions' ) )
		$is_active = true;

	return $is_active;

}
add_filter( 'slicewp_is_integration_plugin_active_pms', 'slicewp_is_integration_plugin_active_pms' );