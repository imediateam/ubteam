<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Includes the Easy Digital Downloads files
 *
 */
function slicewp_include_files_edd() {

	// Get legend dir path
	$dir_path = plugin_dir_path( __FILE__ );

	// Include main class
	if( file_exists( $dir_path . 'class-integration-easy-digital-downloads.php' ) )
		include $dir_path . 'class-integration-easy-digital-downloads.php';

	// Include hooks functions
	if( slicewp_is_integration_active( 'edd' ) && slicewp_is_integration_plugin_active( 'edd' ) ) {

		if( file_exists( $dir_path . 'functions-hooks-integration-easy-digital-downloads.php' ) )
			include $dir_path . 'functions-hooks-integration-easy-digital-downloads.php';
		
	}

}
add_action( 'slicewp_include_files_late', 'slicewp_include_files_edd' );


/**
 * Register the class that handles EDD related actions
 *
 * @param array $integrations
 *
 * @return array
 *
 */
function slicewp_register_integration_edd( $integrations ) {

	$integrations['edd'] = 'SliceWP_Integration_Easy_Digital_Downloads';

	return $integrations;

}
add_filter( 'slicewp_register_integration', 'slicewp_register_integration_edd', 20 );


/**
 * Verifies if Easy Digital Downloads is active
 *
 * @param bool $is_active
 *
 * @return bool
 *
 */
function slicewp_is_integration_plugin_active_edd( $is_active = false ) {

	if( class_exists( 'Easy_Digital_Downloads' ) )
		$is_active = true;

	return $is_active;

}
add_filter( 'slicewp_is_integration_plugin_active_edd', 'slicewp_is_integration_plugin_active_edd' );


/**
 * Adds additional commission types for Easy Digital Downloads
 *
 * @param array $supports
 *
 * @return array
 *
 */
function slicewp_integration_supports_edd( $supports ) {

    // Add subscription commission type if Easy Digital Downloads - Recurring Payments is active
	if( defined( 'EDD_RECURRING_VERSION' ) )
        $supports['commission_types'][] = 'subscription';

    return $supports;

}
add_filter( 'slicewp_integration_supports_edd', 'slicewp_integration_supports_edd' );