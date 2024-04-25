<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Includes the files needed for the user area
 *
 */
function slicewp_include_files_user() {

	// Get dir path
	$dir_path = plugin_dir_path( __FILE__ );

	// Include the db layer classes
	if( file_exists( $dir_path . 'class-user-notices.php' ) )
		include $dir_path . 'class-user-notices.php';

}
add_action( 'slicewp_include_files', 'slicewp_include_files_user' );


/**
 * Adds a central action hook on the init that the plugin and add-ons
 * can use to do certain actions, like adding a new user, editing a user, etc.
 *
 */
function slicewp_register_user_do_actions() {

	// Exit if is accessed from admin panel
	if ( is_admin() )
		return;

	if( empty( $_REQUEST['slicewp_action'] ) )
		return;

	$action = sanitize_text_field( $_REQUEST['slicewp_action'] );

	/**
	 * Hook that should be used by all processes that make a certain action
	 * withing the plugin, like adding a new user, editing an user, etc.
	 *
	 */
	do_action( 'slicewp_user_action_' . $action );

}
add_action( 'init', 'slicewp_register_user_do_actions' );
