<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Includes the files needed for the Creative admin area
 *
 */
function slicewp_include_files_admin_creative() {

	// Get creative admin dir path
	$dir_path = plugin_dir_path( __FILE__ );

	// Include submenu page
	if( file_exists( $dir_path . 'class-submenu-page-creatives.php' ) )
		include $dir_path . 'class-submenu-page-creatives.php';

	// Include actions
	if( file_exists( $dir_path . 'functions-actions-creatives.php' ) )
		include $dir_path . 'functions-actions-creatives.php';

	// Include creatives list table
	if( file_exists( $dir_path . 'class-list-table-creatives.php' ) )
		include $dir_path . 'class-list-table-creatives.php';

}
add_action( 'slicewp_include_files', 'slicewp_include_files_admin_creative' );


/**
 * Register the Creatives admin submenu page
 *
 */
function slicewp_register_submenu_page_creatives( $submenu_pages ) {

	if( ! is_array( $submenu_pages ) )
		return $submenu_pages;

	$submenu_pages['creatives'] = array(
		'class_name' => 'SliceWP_Submenu_Page_Creatives',
		'data' 		 => array(
			'page_title' => __( 'Creatives', 'slicewp' ),
			'menu_title' => __( 'Creatives', 'slicewp' ),
			'capability' => apply_filters( 'slicewp_submenu_page_capability_creatives', 'manage_options' ),
			'menu_slug'  => 'slicewp-creatives'
		)
	);

	return $submenu_pages;

}
add_filter( 'slicewp_register_submenu_page', 'slicewp_register_submenu_page_creatives', 30 );