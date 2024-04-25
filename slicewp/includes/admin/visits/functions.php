<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Includes the files needed for the Visits admin area
 *
 */
function slicewp_include_files_admin_visit() {

	// Get visit admin dir path
	$dir_path = plugin_dir_path( __FILE__ );

	// Include submenu page
	if( file_exists( $dir_path . 'class-submenu-page-visits.php' ) )
		include $dir_path . 'class-submenu-page-visits.php';

	// Include actions
	if( file_exists( $dir_path . 'functions-actions-visits.php' ) )
		include $dir_path . 'functions-actions-visits.php';

	// Include visits list table
	if( file_exists( $dir_path . 'class-list-table-visits.php' ) )
		include $dir_path . 'class-list-table-visits.php';

}
add_action( 'slicewp_include_files', 'slicewp_include_files_admin_visit' );


/**
 * Register the Visits admin submenu page
 *
 */
function slicewp_register_submenu_page_visits( $submenu_pages ) {

	if( ! is_array( $submenu_pages ) )
		return $submenu_pages;

	$submenu_pages['visits'] = array(
		'class_name' => 'SliceWP_Submenu_Page_Visits',
		'data' 		 => array(
			'page_title' => __( 'Visits', 'slicewp' ),
			'menu_title' => __( 'Visits', 'slicewp' ),
			'capability' => apply_filters( 'slicewp_submenu_page_capability_visits', 'manage_options' ),
			'menu_slug'  => 'slicewp-visits'
		)
	);

	return $submenu_pages;

}
add_filter( 'slicewp_register_submenu_page', 'slicewp_register_submenu_page_visits', 40 );