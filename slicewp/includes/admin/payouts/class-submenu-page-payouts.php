<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


Class SliceWP_Submenu_Page_Payouts extends SliceWP_Submenu_Page {

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

		// Commission removed from Payment successfully
		slicewp_admin_notices()->register_notice( 'commission_remove_success', '<p>' . __( 'Commission removed successfully from payment.', 'slicewp' ) . '</p>' );

		// Payout added successfully
		slicewp_admin_notices()->register_notice( 'payout_insert_success', '<p>' . sprintf( __( 'Payments for %d affiliates have been generated succesfully.', 'slicewp' ), ! empty( $_GET['payments_count'] ) ? $_GET['payments_count'] : '' ) . '</p>' );

		// Payment reviewed successfully
		slicewp_admin_notices()->register_notice( 'payment_update_success', '<p>' . __( 'Payment successfully updated.', 'slicewp' ) . '</p>' );

		// Payment deleted successfully
		slicewp_admin_notices()->register_notice( 'payment_delete_success', '<p>' . __( 'Payment deleted succesfully.', 'slicewp' ) . '</p>' );

		// Payout deleted successfully
		slicewp_admin_notices()->register_notice( 'payout_delete_success', '<p>' . __( 'Payout deleted succesfully.', 'slicewp' ) . '</p>' );
	}


	/**
	 * Callback for the HTML output for the Payouts page
	 *
	 */
	public function output() {

		if( empty( $this->current_subpage ) )
			include 'views/view-payouts.php';

		else {

			if( $this->current_subpage == 'view-payout' )
				include 'views/view-payout.php';

			if( $this->current_subpage == 'create-payout' )
				include 'views/view-create-payout.php';

			if( $this->current_subpage == 'preview-payout' )
				include 'views/view-preview-payout.php';

			if( $this->current_subpage == 'review-payment' )
				include 'views/view-review-payment.php';

			if( $this->current_subpage == 'view-payments' )
				include 'views/view-payments.php';

		}

	}

}