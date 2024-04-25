<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Includes the files needed for the User area
 *
 */
function slicewp_include_files_user_shortcodes() {

	// Get affiliate admin dir path
	$dir_path = plugin_dir_path( __FILE__ );

	// Include actions
	if( file_exists( $dir_path . 'functions-actions-shortcodes.php' ) )
		include $dir_path . 'functions-actions-shortcodes.php';

	if( file_exists( $dir_path . 'functions-shortcodes.php' ) )
		include $dir_path . 'functions-shortcodes.php';

}
add_action( 'slicewp_include_files', 'slicewp_include_files_user_shortcodes' );


/**
 * Returns the current user's affiliate id
 * 
 * @return int
 * 
 */
function slicewp_get_current_affiliate_id() {

	// Get the current User ID
	$user = wp_get_current_user();

	if( ! isset( $user->ID ) )
		return 0;
	
	// Get the user's Affiliate ID
	$affiliate = slicewp_get_affiliate_by_user_id( $user->ID );
	
	if( is_null( $affiliate ) )
		return 0;

	$affiliate_id = $affiliate->get('id');
	
	return absint( $affiliate_id );

}