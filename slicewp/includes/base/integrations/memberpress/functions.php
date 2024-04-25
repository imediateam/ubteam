<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Includes the MemberPress files
 *
 */
function slicewp_include_files_mepr() {

	// Get legend dir path
	$dir_path = plugin_dir_path( __FILE__ );

	// Include main class
	if( file_exists( $dir_path . 'class-integration-memberpress.php' ) )
		include $dir_path . 'class-integration-memberpress.php';

	// Include hooks functions
	if( slicewp_is_integration_active( 'mepr' ) && slicewp_is_integration_plugin_active( 'mepr' ) ) {

		if( file_exists( $dir_path . 'functions-hooks-integration-memberpress.php' ) )
			include $dir_path . 'functions-hooks-integration-memberpress.php';
		
	}

}
add_action( 'slicewp_include_files_late', 'slicewp_include_files_mepr' );


/**
 * Register the class that handles MemberPress related actions
 *
 * @param array $integrations
 *
 * @return array
 *
 */
function slicewp_register_integration_mepr( $integrations ) {

	$integrations['mepr'] = 'SliceWP_Integration_MemberPress';

	return $integrations;

}
add_filter( 'slicewp_register_integration', 'slicewp_register_integration_mepr', 30 );


/**
 * Verifies if MemberPress is active
 *
 * @param bool $is_active
 *
 * @return bool
 *
 */
function slicewp_is_integration_plugin_active_mepr( $is_active = false ) {

	if( defined( 'MEPR_VERSION' ) )
		$is_active = true;

	return $is_active;

}
add_filter( 'slicewp_is_integration_plugin_active_mepr', 'slicewp_is_integration_plugin_active_mepr' );