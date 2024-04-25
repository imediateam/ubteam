<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


Class SliceWP_Submenu_Page_Add_Ons extends SliceWP_Submenu_Page {

	/**
	 * Callback for the HTML output for the Add-ons page
	 *
	 */
	public function output() {

		// Get cached add-ons
		$remote_add_ons = get_option( 'slicewp_remote_add_ons', array() );

		// Check if there are any values set. If there aren't pull from the server
		if( empty( $remote_add_ons['add_ons'] ) || $remote_add_ons['time_updated'] < time() - 2 * DAY_IN_SECONDS ) {

			$add_ons = $this->remote_get_add_ons();

			if( ! empty( $add_ons ) )
				update_option( 'slicewp_remote_add_ons', array( 'add_ons' => $add_ons, 'time_updated' => time() ) );

		} else {

			$add_ons = $remote_add_ons['add_ons'];

		}

		// Display add-ons page
		if( empty( $this->current_subpage ) )
			include 'views/view-add-ons.php';

	}


	/**
	 * Connects to the server to pull new information regarding add-ons
	 *
	 * @return array
	 *
	 */
	protected function remote_get_add_ons() {

		$add_ons  = array();
		$response = wp_remote_get( 'https://slicewp.com/wp-content/uploads/add-ons.json', array( 'timeout' => 15 ) );

		if( ! is_wp_error( $response ) ) {

			$add_ons = json_decode( wp_remote_retrieve_body( $response ), true );

		}

		return $add_ons;

	}

}