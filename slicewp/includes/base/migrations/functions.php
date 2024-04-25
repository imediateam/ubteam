<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Includes the Migrations files
 *
 */
function slicewp_include_files_migrations() {

	// Get legend dir path
	$dir_path = plugin_dir_path( __FILE__ );

	// Include migrations manager class
	if( file_exists( $dir_path . 'class-migration-manager.php' ) )
		include $dir_path . 'class-migration-manager.php';

}
add_action( 'slicewp_include_files', 'slicewp_include_files_migrations' );