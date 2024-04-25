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

	if( empty( $_GET['page'] ) )
		return;

	// Bail if we're already on the setup page
	if( $_GET['page'] == 'slicewp-setup' )
		return;

	// Make sure we only redirect from our pages
	if( false === strpos( $_GET['page'], 'slicewp' ) )
		return;

	// Bail if the setup wizard was already visited
	if( false !== get_option( 'slicewp_setup_wizard_visited' ) )
		return;

	if( wp_doing_ajax() )
		return;

	if( is_network_admin() )
		return;

	// Redirect to setup wizard
	wp_redirect( add_query_arg( array( 'page' => 'slicewp-setup' ), admin_url( 'index.php' ) ) );
	die();

}
add_action( 'admin_init', 'slicewp_activated_redirect_to_setup_wizard' );