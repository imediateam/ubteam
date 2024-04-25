<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


Class SliceWP_Submenu_Page_Visits extends SliceWP_Submenu_Page {

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

	}


	/**
	 * Callback for the HTML output for the Affiliate page
	 *
	 */
	public function output() {

		if( empty( $this->current_subpage ) )
			include 'views/view-visits.php';

	}

}