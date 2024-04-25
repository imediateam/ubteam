<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Includes the files needed for the Setup Wizard admin area
 *
 */
function slicewp_include_files_admin_setup_wizard() {

	// Get setup wizard admin dir path
	$dir_path = plugin_dir_path( __FILE__ );

	// Include dashboard page
	if( file_exists( $dir_path . 'class-dashboard-page-setup-wizard.php' ) )
		include $dir_path . 'class-dashboard-page-setup-wizard.php';

	// Include actions
	if( file_exists( $dir_path . 'functions-actions-setup-wizard.php' ) )
		include $dir_path . 'functions-actions-setup-wizard.php';

}
add_action( 'slicewp_include_files', 'slicewp_include_files_admin_setup_wizard' );


/**
 * Register the Setup Wizard admin dashboard page
 *
 */
function slicewp_register_dashboard_page_setup_wizard( $submenu_pages ) {

	new SliceWP_Dashboard_Page_Setup_Wizard;

}
add_filter( 'plugins_loaded', 'slicewp_register_dashboard_page_setup_wizard', 20 );


/**
 * Redirects the admin to the setup wizard when they activate SliceWP
 *
 */
function slicewp_activated_redirect_to_setup_wizard() {

	if( false === get_transient( '_slicewp_activated' ) )
		return;

	if( false !== get_option( 'slicewp_setup_wizard_visited' ) )
		return;

	if( wp_doing_ajax() )
		return;

	if( is_network_admin() )
		return;

	if( isset( $_GET['activate-multi'] ) )
		return;

	// Remove the just activated transient
	delete_transient( '_slicewp_activated' );

	// Redirect to setup wizard
	wp_redirect( add_query_arg( array( 'page' => 'slicewp-setup' ), admin_url( 'index.php' ) ) );
	die();

}
add_action( 'admin_init', 'slicewp_activated_redirect_to_setup_wizard' );