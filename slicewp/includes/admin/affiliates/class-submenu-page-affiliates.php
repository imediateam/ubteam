<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


Class SliceWP_Submenu_Page_Affiliates extends SliceWP_Submenu_Page {

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

		// Affiliate insert success
		slicewp_admin_notices()->register_notice( 'affiliate_insert_success', '<p>' . __( 'Affiliate added successfully.', 'slicewp' ) . '</p>' );

		// Affiliate updated successfully
		slicewp_admin_notices()->register_notice( 'affiliate_update_success', '<p>' . __( 'Affiliate updated successfully.', 'slicewp' ) . '</p>' );

		// Affiliate updated fail
		slicewp_admin_notices()->register_notice( 'affiliate_update_fail', '<p>' . __( 'Something went wrong. Could not update the affiliate.', 'slicewp' ) . '</p>', 'error' );

		// Affiliate delete success
		slicewp_admin_notices()->register_notice( 'affiliate_delete_success', '<p>' . __( 'Affiliate deleted successfully.', 'slicewp' ) . '</p>' );

		// Affiliate review approved successfully
		slicewp_admin_notices()->register_notice( 'affiliate_review_approve_success', '<p>' . __( 'Affiliate application approved successfully.', 'slicewp' ) . '</p>' );
		
		// Affiliate review rejected successfully
		slicewp_admin_notices()->register_notice( 'affiliate_review_reject_success', '<p>' . __( 'Affiliate application rejected successfully.', 'slicewp' ) . '</p>' );
	}


	/**
	 * Callback for the HTML output for the Affiliate page
	 *
	 */
	public function output() {

		if( empty( $this->current_subpage ) )
			include 'views/view-affiliates.php';

		else {

			if( $this->current_subpage == 'add-affiliate' )
				include 'views/view-add-affiliate.php';

			if( $this->current_subpage == 'edit-affiliate' )
				include 'views/view-edit-affiliate.php';

			if( $this->current_subpage == 'review-affiliate' )
				include 'views/view-review-affiliate.php';


		}


		/**
		 * Action to add extra subpages to the affiliates main page
		 *
		 * @param string $current_subpage
		 *
		 */
		do_action( 'slicewp_submenu_page_output_affiliates', $this->current_subpage );

	}

}