<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


Class SliceWP_Submenu_Page_Creatives extends SliceWP_Submenu_Page {

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

		// Creative insert success
		slicewp_admin_notices()->register_notice( 'creative_insert_success', '<p>' . __( 'Creative added successfully.', 'slicewp' ) . '</p>' );

		// Creative updated successfully
		slicewp_admin_notices()->register_notice( 'creative_update_success', '<p>' . __( 'Creative updated successfully.', 'slicewp' ) . '</p>' );

		// Creative updated fail
		slicewp_admin_notices()->register_notice( 'creative_update_fail', '<p>' . __( 'Something went wrong. Could not update the creative.', 'slicewp' ) . '</p>', 'error' );

		// Creative delete success
		slicewp_admin_notices()->register_notice( 'creative_delete_success', '<p>' . __( 'Creative deleted successfully.', 'slicewp' ) . '</p>' );

	}


	/**
	 * Callback for the HTML output for the Creative page
	 *
	 */
	public function output() {

		if( empty( $this->current_subpage ) )
			include 'views/view-creatives.php';

		else {

			if( $this->current_subpage == 'add-creative' )
				include 'views/view-add-creative.php';

			if( $this->current_subpage == 'edit-creative' )
				include 'views/view-edit-creative.php';

		}

	}

}