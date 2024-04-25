<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Validates and handles the "Integrations" step from the setup wizard
 *
 */
function slicewp_admin_action_process_setup_wizard_step_integrations() {

	// Verify for nonce
	if( empty( $_POST['slicewp_token'] ) || ! wp_verify_nonce( $_POST['slicewp_token'], 'slicewp_process_setup_wizard_step' ) )
		return;

	if( ! empty( $_POST['integrations'] ) && is_array( $_POST['integrations'] ) ) {

		$settings 	  = slicewp_get_option( 'settings', array() );
		$integrations = array();

		foreach( slicewp()->integrations as $integration_slug => $integration ) {

			if( in_array( $integration_slug, $_POST['integrations'] ) )
				$integrations[] = $integration_slug;

		}

		$settings['active_integrations'] = $integrations;

		slicewp_update_option( 'settings', $settings );

	}

	// Redirect to the edit page of the affiliate with a success message
	wp_redirect( add_query_arg( array( 'page' => 'slicewp-setup', 'current_step' => sanitize_text_field( $_POST['next_step'] ) ), admin_url( 'index.php' ) ) );
	exit;

}
add_action( 'slicewp_admin_action_process_setup_wizard_step_integrations', 'slicewp_admin_action_process_setup_wizard_step_integrations', 50 );


/**
 * Validates and handles the "Setup" step from the setup wizard
 *
 */
function slicewp_admin_action_process_setup_wizard_step_setup() {

	// Verify for nonce
	if( empty( $_POST['slicewp_token'] ) || ! wp_verify_nonce( $_POST['slicewp_token'], 'slicewp_process_setup_wizard_step' ) )
		return;

	// Get general settings
	$settings = slicewp_get_option( 'settings', array() );

	// Set commission types
	$commission_types = slicewp_get_available_commission_types();

	foreach( $commission_types as $type => $details ) {

		if( isset( $_POST['commission_rate_' . $type] ) )
			$settings['commission_rate_' . $type] = sanitize_text_field( $_POST['commission_rate_' . $type] );

		if( isset( $_POST['commission_rate_type_' . $type] ) )
			$settings['commission_rate_type_' . $type] = sanitize_text_field( $_POST['commission_rate_type_' . $type] );

	}

	// Set currency
	if( isset( $_POST['active_currency'] ) ) {

		// Set active currency
		$settings['active_currency'] = sanitize_text_field( $_POST['active_currency'] );

		// Set currency separators and symbol position
		$thousands_separators = slicewp_get_currencies( 'thousands_separator' );
		$decimal_separators   = slicewp_get_currencies( 'decimal_separator' );
		$symbol_position 	  = slicewp_get_currencies( 'symbol_position' );

		$settings['currency_thousands_separator'] = ( ! empty( $thousands_separators[$settings['active_currency']] ) ? $thousands_separators[$settings['active_currency']] : ',' );
		$settings['currency_decimal_separator']   = ( ! empty( $decimal_separators[$settings['active_currency']] ) ? $decimal_separators[$settings['active_currency']] : '.' );
		$settings['currency_symbol_position']     = ( ! empty( $symbol_position[$settings['active_currency']] ) ? $symbol_position[$settings['active_currency']] : 'before' );

	}
	
	// Set cookie duration
	if( isset( $_POST['cookie_duration'] ) )
		$settings['cookie_duration'] = absint( $_POST['cookie_duration'] );

	// Set allow affiliate registration
	if( isset( $_POST['allow_affiliate_registration'] ) )
		$settings['allow_affiliate_registration'] = 1;

	// Update general settings
	slicewp_update_option( 'settings', $settings );

	// Redirect to the edit page of the affiliate with a success message
	wp_redirect( add_query_arg( array( 'page' => 'slicewp-setup', 'current_step' => sanitize_text_field( $_POST['next_step'] ) ), admin_url( 'index.php' ) ) );
	exit;

}
add_action( 'slicewp_admin_action_process_setup_wizard_step_setup', 'slicewp_admin_action_process_setup_wizard_step_setup', 50 );


/**
 * Validates and handles the "Pages" step from the setup wizard
 *
 */
function slicewp_admin_action_process_setup_wizard_step_pages() {

	// Verify for nonce
	if( empty( $_POST['slicewp_token'] ) || ! wp_verify_nonce( $_POST['slicewp_token'], 'slicewp_process_setup_wizard_step' ) )
		return;

	// Set the pages that can be created
	$pages = array(
		'affiliate_account'  => array(
			'post_title'   => __( 'Affiliate Account', 'slicewp' ),
			'post_content' => '[slicewp_affiliate_account]'
		),
		'affiliate_register' => array(
			'post_title'   => __( 'Affiliate Registration', 'slicewp' ),
			'post_content' => '[slicewp_affiliate_registration]'
		),
		'affiliate_login' => array(
			'post_title'   => __( 'Affiliate Login', 'slicewp' ),
			'post_content' => '[slicewp_affiliate_login]'
		)
	);

	global $wpdb;

	// Save the page ids
	$page_ids = array();

	foreach( $pages as $page_slug => $page_data ) {

		// Continue if the admin did not select the page
		if( empty( $_POST['page_' . $page_slug] ) )
			continue;

		// Try to check if the page already exists
		$shortcode = str_replace( array( '<!-- wp:shortcode -->', '<!-- /wp:shortcode -->' ), '', $page_data['post_content'] );
		$page_id   = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' ) AND post_content LIKE %s LIMIT 1", '%' . $shortcode . '%' ) );

		// If page exists save the ID for later
		if( ! is_null( $page_id ) ) {

			$page_ids[$page_slug] = absint( $page_id );

		// If the page doesn't exist, insert it
		} else {

			$page_array = array_merge( $page_data, array(
				'post_type'   => 'page',
				'post_status' => 'publish'
			));

			$page_ids[$page_slug] = wp_insert_post( $page_array );

		}

	}

	// If the account page exists, save it in the general settings
	if( ! empty( $_POST['page_affiliate_account'] ) && ! empty( $page_ids['affiliate_account'] ) ) {

		$settings = slicewp_get_option( 'settings', array() );

		$settings['page_affiliate_account'] = $page_ids['affiliate_account'];

		slicewp_update_option( 'settings', $settings );

	}

	// Redirect to the edit page of the affiliate with a success message
	wp_redirect( add_query_arg( array( 'page' => 'slicewp-setup', 'current_step' => sanitize_text_field( $_POST['next_step'] ) ), admin_url( 'index.php' ) ) );
	exit;

}
add_action( 'slicewp_admin_action_process_setup_wizard_step_pages', 'slicewp_admin_action_process_setup_wizard_step_pages', 50 );


/**
 * Validates and handles the "Emails" step from the setup wizard
 *
 */
function slicewp_admin_action_process_setup_wizard_step_emails() {

	// Verify for nonce
	if( empty( $_POST['slicewp_token'] ) || ! wp_verify_nonce( $_POST['slicewp_token'], 'slicewp_process_setup_wizard_step' ) )
		return;

	// Get general settings
	$settings = slicewp_get_option( 'settings', array() );
	
	$email_notifications = slicewp_get_available_email_notifications();

	foreach( $email_notifications as $email_notification_slug => $email_notification ) {

		if( ! empty( $email_notification['sending'] ) && $email_notification['sending'] == 'manual' )
			continue;

		if( ! empty( $_POST[$email_notification_slug] ) )
			$settings['email_notifications'][$email_notification_slug]['enabled'] = 1;
		else
			$settings['email_notifications'][$email_notification_slug]['enabled'] = '';

	}

	// Update general settings
	slicewp_update_option( 'settings', $settings );

	// Redirect to the edit page of the affiliate with a success message
	wp_redirect( add_query_arg( array( 'page' => 'slicewp-setup', 'current_step' => sanitize_text_field( $_POST['next_step'] ) ), admin_url( 'index.php' ) ) );
	exit;

}
add_action( 'slicewp_admin_action_process_setup_wizard_step_emails', 'slicewp_admin_action_process_setup_wizard_step_emails', 50 );