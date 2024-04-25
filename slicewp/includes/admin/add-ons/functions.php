<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Includes the files needed for the Add-ons admin area
 *
 */
function slicewp_include_files_admin_add_ons() {

	// Get affiliate admin dir path
	$dir_path = plugin_dir_path( __FILE__ );

	// Include submenu page
	if( file_exists( $dir_path . 'class-submenu-page-add-ons.php' ) )
		include $dir_path . 'class-submenu-page-add-ons.php';

}
add_action( 'slicewp_include_files', 'slicewp_include_files_admin_add_ons' );


/**
 * Register the Add-ons admin submenu page
 *
 */
function slicewp_register_submenu_page_add_ons( $submenu_pages ) {

	if( ! is_array( $submenu_pages ) )
		return $submenu_pages;

	$submenu_pages['add_ons'] = array(
		'class_name' => 'SliceWP_Submenu_Page_Add_Ons',
		'data' 		 => array(
			'page_title' => __( 'Add-ons', 'slicewp' ),
			'menu_title' => ( ! slicewp_add_ons_exist() ? '<span style="color: #00b9eb">' . __( 'Add-ons', 'slicewp' ) . '</span>' : __( 'Add-ons', 'slicewp' ) ),
			'capability' => apply_filters( 'slicewp_submenu_page_capability_add_ons', 'manage_options' ),
			'menu_slug'  => 'slicewp-add-ons'
		)
	);

	return $submenu_pages;

}
add_filter( 'slicewp_register_submenu_page', 'slicewp_register_submenu_page_add_ons', 50 );