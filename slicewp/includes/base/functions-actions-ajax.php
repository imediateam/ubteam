<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * AJAX callback that returns an array of data from WP_User objects
 *
 */
function slicewp_action_ajax_get_users() {

	if( empty( $_REQUEST['slicewp_token'] ) || ! wp_verify_nonce( $_REQUEST['slicewp_token'], 'slicewp_user_search' ) )
		wp_die(0);

	if( ! current_user_can( 'manage_options' ) )
		wp_die( 0 );

	$affiliates   = ( ! empty( $_REQUEST['affiliates'] ) ? $_REQUEST['affiliates'] : '' );
	$return_value = ( ! empty( $_REQUEST['return_value'] ) ? $_REQUEST['return_value'] : 'user_id' );
	$users 		  = array();
	$return 	  = array();

	// Prepare users arguments
	$args = array(
		'number' 		 => -1,
		'search_columns' => array( 'user_login', 'user_email', 'display_name' ),
		'search' 		 => ( ! empty( $_REQUEST['term'] ) ? '*' . trim( $_REQUEST['term'] ) . '*' : '' )
	);

	// Get all users if an include or explode is not specified
	if( $affiliates == '' ) {

		$users = get_users( $args );

	}
	

	// Get users that are also affiliates
	if( $affiliates == 'include' ) {

		$_affiliates 	= slicewp_get_affiliates( array( 'number' => -1 ) );
		$affiliates_ids = array();

		foreach( $_affiliates as $affiliate )
			$affiliates_ids[$affiliate->get('id')] = $affiliate->get('user_id');

		$args['include'] = $affiliates_ids;

		if( ! empty( $affiliates_ids ) )
			$users = get_users( $args );

	}

	// Get users that are not affiliates
	if( $affiliates == 'exclude' ) {

		$_affiliates 	= slicewp_get_affiliates( array( 'number' => -1 ) );
		$affiliates_ids = array();

		foreach( $_affiliates as $affiliate )
			$affiliates_ids[$affiliate->get('id')] = $affiliate->get('user_id');

		$args['exclude'] = $affiliates_ids;

		$users = get_users( $args );

	}

	// Filter the results before returning
	foreach( $users as $user ) {

		$_affiliates_ids = ( ! empty( $affiliates_ids ) ? array_flip( $affiliates_ids ) : array() );

		$return[] = array(
			'label' => $user->display_name . ' (' . $user->user_email . ')',
			'value' => ( ( $return_value == 'user_id' || empty( $_affiliates_ids ) ) ? $user->ID : ( $_affiliates_ids[$user->ID] ) )
		);

	}

	echo json_encode( $return );
	wp_die();

}
add_action( 'wp_ajax_slicewp_action_ajax_get_users', 'slicewp_action_ajax_get_users' );


/**
 * Attempts to register a website with our server
 *
 */
function slicewp_action_ajax_register_website() {

	if( empty( $_POST['slicewp_token'] ) || ! wp_verify_nonce( $_POST['slicewp_token'], 'slicewp_save_settings' ) )
		wp_die(0);

	if( empty( $_POST['license_key'] ) )
		wp_die(0);

	$license_key = sanitize_text_field( $_POST['license_key'] );
	$website_url = get_site_url();

	// Call the API link
	$response = wp_remote_get( add_query_arg( array( 'edde_api_action' => 'register_website', 'license_key' => $license_key, 'url' => $website_url ), 'https://slicewp.com/' ), array( 'timeout' => 30, 'sslverify' => false ) );

	// If the connection isn't successfull, return
	if( is_wp_error( $response ) ) {

		// Log the error
		slicewp_add_log( 'System: Website could not be registered. WP Error: ' . $response->get_error_message() );

		wp_send_json( array( 'success' => false, 'data' => array( 'message' => __( 'Something went wrong. Could not activate the website. Please try again.', 'slicewp' ) ) ) );

	}

	// Get the response's body
	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	// If the website could not be registered, return the error
	if( ! empty( $body['error'] ) ) {

		// Log the error
		slicewp_add_log( 'System: Website could not be registered. API return error: ' . $body['error'] );

		wp_send_json( array( 'success' => false, 'data' => array( 'message' => slicewp_get_api_action_response_error( 'register_website', $body['error'] ) ) ) );

	}

	// Log the success
	slicewp_add_log( 'System: Website was successfully registered.' );

	// Save the license key
	update_option( 'slicewp_license_key', $license_key );

	// Save license key data
	update_option( 'slicewp_license_key_data', $body );

	// Set the website as registered
	update_option( 'slicewp_website_registered', true );

	// Return with a success message
	wp_send_json( array( 'success' => true, 'data' => array( 'message' => __( 'Your website has been successfully registered.', 'slicewp' ) ) ) );

}
add_action( 'wp_ajax_slicewp_action_ajax_register_website', 'slicewp_action_ajax_register_website' );


/**
 * Attempts to deregister a website from our server
 *
 */
function slicewp_action_ajax_deregister_website() {

	if( empty( $_POST['slicewp_token'] ) || ! wp_verify_nonce( $_POST['slicewp_token'], 'slicewp_save_settings' ) )
		wp_die(0);

	if( empty( $_POST['license_key'] ) )
		wp_die(0);

	$license_key = sanitize_text_field( $_POST['license_key'] );
	$website_url = get_site_url();

	// Call the API link
	$response = wp_remote_get( add_query_arg( array( 'edde_api_action' => 'deregister_website', 'license_key' => $license_key, 'url' => $website_url ), 'https://slicewp.com/' ), array( 'timeout' => 30, 'sslverify' => false ) );

	// If the connection isn't successfull, return
	if( is_wp_error( $response ) ) {

		// Log the error
		slicewp_add_log( 'System: Website could not be deregistered. WP Error: ' . $response->get_error_message() );

		wp_send_json( array( 'success' => false, 'data' => array( 'message' => __( 'Something went wrong. Could not activate the website. Please try again.', 'slicewp' ) ) ) );

	}

	// Get the response's body
	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	// If the website could not be registered, return the error
	if( ! empty( $body['error'] ) ) {

		// Log the error
		slicewp_add_log( 'System: Website could not be deregistered. API return error: ' . $body['error'] );

		wp_send_json( array( 'success' => false, 'data' => array( 'message' => slicewp_get_api_action_response_error( 'deregister_website', $body['error'] ) ) ) );

	}

	// Log the success
	slicewp_add_log( 'System: Website was successfully deregistered.' );

	// Save the license key
	delete_option( 'slicewp_license_key' );

	// Save license key data
	delete_option( 'slicewp_license_key_data' );

	// Set the website as registered
	delete_option( 'slicewp_website_registered' );

	// Return with a success message
	wp_send_json( array( 'success' => true, 'data' => array( 'message' => __( 'Your website has been successfully deregistered.', 'slicewp' ) ) ) );

}
add_action( 'wp_ajax_slicewp_action_ajax_deregister_website', 'slicewp_action_ajax_deregister_website' );

