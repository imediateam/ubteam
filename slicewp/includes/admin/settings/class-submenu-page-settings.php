<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


Class SliceWP_Submenu_Page_Settings extends SliceWP_Submenu_Page {

	/**
	 * Helper init method that runs on parent __construct
	 *
	 */
	protected function init() {

		add_action( 'admin_init', array( $this, 'register_admin_notices' ), 10 );

	}


	/**
	 * Callback method to register admin notices that are sent via URL parameters
	 *
	 */
	public function register_admin_notices() {

		if( empty( $_GET['slicewp_message'] ) )
			return;

		// Save settings succesfully
		slicewp_admin_notices()->register_notice( 'save_settings_success', '<p>' . __( 'Settings saved successfully.', 'slicewp' ) . '</p>' );

		// Debug log cleared successfully
		slicewp_admin_notices()->register_notice( 'debug_log_clear_success', '<p>' . __( 'Debug log cleared successfully.', 'slicewp' ) . '</p>' );

		// Send test email success
		slicewp_admin_notices()->register_notice( 'send_test_email_success', '<p>' . __( 'Test email has been successfully sent.', 'slicewp' ) . '</p>' );

		// Send test email fail
		slicewp_admin_notices()->register_notice( 'send_test_email_fail', '<p>' . __( 'Something went wrong. Could not send test email. Please try again.', 'slicewp' ) . '</p>', 'error' );

		if( ! empty( $_GET['updated'] ) ) {

			// User role added successfully
			slicewp_admin_notices()->register_notice( 'bulk_add_affiliate_user_role_success', '<p>' . sprintf( __( 'The Affiliate user role has been added to %d users.', 'slicewp' ), absint( $_GET['updated'] ) ) . '</p>' );

			// User role removed successfully
			slicewp_admin_notices()->register_notice( 'bulk_remove_affiliate_user_role_success', '<p>' . sprintf( __( 'The Affiliate user role has been removed from %d users.', 'slicewp' ), absint( $_GET['updated'] ) ) . '</p>' );
			
		}

	}


	/**
	 * Callback for the HTML output for the Settings page
	 *
	 */
	public function output() {

		include 'views/view-settings.php';

	}

}