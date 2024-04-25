<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Validates and handles the saving of the main plugin settings
 *
 */
function slicewp_admin_action_save_settings() {

	// Verify for nonce
	if( empty( $_POST['slicewp_token'] ) || ! wp_verify_nonce( $_POST['slicewp_token'], 'slicewp_save_settings' ) )
		return;

	// Verify for Cookie Duration
	if( empty( $_POST['settings']['cookie_duration'] ) || $_POST['settings']['cookie_duration'] < 0 ) {

		slicewp_admin_notices()->register_notice( 'settings_cookie_duration_error', '<p>' . __( 'Please insert a Cookie Duration greater than 0.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'settings_cookie_duration_error' );

		return;

	}
	
	// Verify for Payments Minimum Amount
	if ( $_POST['settings']['payments_minimum_amount'] < 0 ) {

		slicewp_admin_notices()->register_notice( 'settings_payments_minimum_amount_error', '<p>' . __( 'Please fill in a Payments Minimum Amount equal or greater than 0.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'settings_payments_minimum_amount_error' );	

		return;

	}

	// Verify for Affiliate Keyword
	if ( empty( $_POST['settings']['affiliate_keyword'] ) ) {

		slicewp_admin_notices()->register_notice( 'settings_affiliate_keyword_error', '<p>' . __( 'Please fill in the Affiliate Keyword.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'settings_affiliate_keyword_error' );	

		return;
	}

	// Verify for From Email
	if ( ! is_email( $_POST['settings']['from_email'] ) ) {

		slicewp_admin_notices()->register_notice( 'from_email_error', '<p>' . __( 'Please fill in a valid email address for From Email.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'from_email_error' );	

		return;
	}

	// Verify for From Name
	if ( empty( $_POST['settings']['from_name'] ) ) {

		slicewp_admin_notices()->register_notice( 'from_name_error', '<p>' . __( 'Please fill in From Name.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'from_name_error' );	

		return;

	}

	// Verify for Admin Emails
	if ( empty( $_POST['settings']['admin_emails'] ) ) {

		slicewp_admin_notices()->register_notice( 'admin_emails_error', '<p>' . __( 'Please fill in the Admin Emails.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'admin_emails_error' );	
		
		return;

	} else {

		$admin_emails = array_map( 'trim', explode( ',', sanitize_text_field( $_POST['settings']['admin_emails'] ) ) );

		foreach ( $admin_emails as $admin_email ) {

			if ( ! is_email( $admin_email ) ) {

				slicewp_admin_notices()->register_notice( 'admin_emails_error', '<p>' . __( 'Please fill in valid Admin Emails.', 'slicewp' ) . '</p>', 'error' );
				slicewp_admin_notices()->display_notice( 'admin_emails_error' );	

				return;

			}

		}

	}

	// Verify that if Email Notifications are enabled, the Subject and Content fields are filled
	$email_notifications = slicewp_get_available_email_notifications();
	
	foreach( $email_notifications as $email_notification_slug => $email ) {

		if ( ! empty( $_POST['settings']['email_notifications'][$email_notification_slug]['enabled'] ) && empty( $_POST['settings']['email_notifications'][$email_notification_slug]['subject'] ) ) {

			slicewp_admin_notices()->register_notice( 'email_notification_error', '<p>' . sprintf( __( 'Please fill in the "%s" Email Notification subject.', 'slicewp' ), $email['name'] ) . '</p>', 'error' );
			slicewp_admin_notices()->display_notice( 'email_notification_error' );

			return;

		}

		if ( ! empty( $_POST['settings']['email_notifications'][$email_notification_slug]['enabled'] ) && empty( $_POST['settings']['email_notifications'][$email_notification_slug]['content'] ) ) {

			slicewp_admin_notices()->register_notice( 'email_notification_error', '<p>' . sprintf( __( 'Please fill in the "%s" Email Notification content.', 'slicewp' ), $email['name'] ) . '</p>', 'error' );
			slicewp_admin_notices()->display_notice( 'email_notification_error' );

			return;

		}

	}

	// Add commission rates
	$commission_types = slicewp_get_available_commission_types();
	
	foreach ( $commission_types as $type => $details ) {

		if ( ! isset( $_POST['settings']['commission_rate_' . $type] ) || floatval( $_POST['settings']['commission_rate_' . $type] ) < 0 ) {

			$_POST['settings']['commission_rate_' . $type] = 0;

		}

		// Sanitize commission rate value
		$_POST['settings']['commission_rate_' . $type] = floatval( $_POST['settings']['commission_rate_' . $type] );

	}

	// Save the settings
	$_POST = stripslashes_deep( $_POST );

	slicewp_update_option( 'settings', _slicewp_array_wp_kses_post( $_POST['settings'] ) );

	// Redirect to the edit page of the settings with a success message
	wp_redirect( add_query_arg( array( 'page' => 'slicewp-settings', 'slicewp_message' => 'save_settings_success', 'tab' => ( ! empty( $_POST['active_tab'] ) ? $_POST['active_tab'] : 'general' ), 'email_notification' => ( ! empty( $_POST['email_notification'] ) ? $_POST['email_notification'] : '' ) ), admin_url( 'admin.php' ) ) );
	exit;

}
add_action( 'slicewp_admin_action_save_settings', 'slicewp_admin_action_save_settings', 50 );


/**
 * Downloads the debug log
 *
 */
function slicewp_admin_action_download_debug_log() {

	// Verify for nonce
	if( empty( $_GET['slicewp_token'] ) || ! wp_verify_nonce( $_GET['slicewp_token'], 'slicewp_download_debug_log' ) )
		return;

	nocache_headers();

	header( 'Content-Type: text/plain' );
	header( 'Content-Disposition: attachment; filename="slicewp-debug-log.txt"' );

	echo slicewp_get_log();
	exit;

}
add_action( 'slicewp_admin_action_download_debug_log', 'slicewp_admin_action_download_debug_log', 10 );


/**
 * Clears the debug log
 *
 */
function slicewp_admin_action_clear_debug_log() {

	// Verify for nonce
	if( empty( $_GET['slicewp_token'] ) || ! wp_verify_nonce( $_GET['slicewp_token'], 'slicewp_clear_debug_log' ) )
		return;

	$cleared = slicewp_clear_log();

	if( ! $cleared ) {

		slicewp_admin_notices()->register_notice( 'debug_log_clear_fail', '<p>' . __( 'The debug log could not be cleared. Please try again.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'debug_log_clear_fail' );

	}

	// Redirect to the settings with a success message
	wp_redirect( add_query_arg( array( 'page' => 'slicewp-settings', 'slicewp_message' => 'debug_log_clear_success', 'tab' => 'tools' ), admin_url( 'admin.php' ) ) );
	exit;

}
add_action( 'slicewp_admin_action_clear_debug_log', 'slicewp_admin_action_clear_debug_log', 10 );


/**
 * Sends the Test Email
 *
 */
function slicewp_admin_action_send_test_email() {

	// Verify for nonce
	if( empty( $_GET['slicewp_token'] ) || ! wp_verify_nonce( $_GET['slicewp_token'], 'slicewp_admin_send_test_email' ) )
		return;

	// Verify for Email Type
	if( empty( $_GET['email_notification'] ) )
		return;

	// Get the Administrator email addresses
	$admin_emails = slicewp_get_setting( 'admin_emails' );

	if ( empty( $admin_emails ) ) {
		
		slicewp_admin_notices()->register_notice( 'admin_emails_empty', '<p>' . __( 'Please fill in the Admin Emails field.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'admin_emails_empty' );
		
		return;

	}

	// Get the Subject and the Body of the email
	$email_notification = slicewp_get_email_notification_settings( sanitize_text_field( $_GET['email_notification'] ) );

	if ( empty( $email_notification['subject'] ) ) {
		
		slicewp_admin_notices()->register_notice( 'email_subject_empty', '<p>' . __( 'Please fill in the Email Subject.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'email_subject_empty' );
		
		return;

	}

	if ( empty( $email_notification['content'] ) ) {
		
		slicewp_admin_notices()->register_notice( 'email_content_empty', '<p>' . __( 'Please fill in the Email Content.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'email_content_empty' );
		
		return;

	}

	// Send the Test Email
	$sent = slicewp_wp_email( $admin_emails, $email_notification['subject'], $email_notification['content'] );

	// Redirect to the settings with a message
	if( $sent )
		wp_redirect( add_query_arg( array( 'page' => 'slicewp-settings', 'slicewp_message' => 'send_test_email_success', 'tab' => 'emails', 'email_notification' => ( ! empty( $_GET['email_notification'] ) ? sanitize_text_field( $_GET['email_notification'] ) : '' ) ), admin_url( 'admin.php' ) ) );
	
	else
		wp_redirect( add_query_arg( array( 'page' => 'slicewp-settings', 'slicewp_message' => 'send_test_email_fail', 'tab' => 'emails', 'email_notification' => ( ! empty( $_GET['email_notification'] ) ? sanitize_text_field( $_GET['email_notification'] ) : '' ) ), admin_url( 'admin.php' ) ) );

	exit;

}
add_action( 'slicewp_admin_action_send_test_email', 'slicewp_admin_action_send_test_email' );


/**
 * Generates the Preview Email
 *
 */
function slicewp_user_action_preview_email(){

	$dir_path = plugin_dir_path( __FILE__ );
	
	// Verify for nonce
	if( empty( $_GET['slicewp_token'] ) || ! wp_verify_nonce( $_GET['slicewp_token'], 'slicewp_preview_email' ) )
		return;

    // Verify for Email Type
	if( empty( $_GET['email_notification'] ) )
        return;

	$email_notification = slicewp_get_email_notification_settings( $_GET['email_notification'] );
	
	$email_content = wpautop( $email_notification['content'] );
	
	$email_template = slicewp_get_setting( 'email_template' );
	$email_template = slicewp_get_email_template( $email_template );
	
	if ( ! is_null( $email_template ) )
		include $email_template['path'];
	else
		echo $email_content;

	exit;
	
}
add_action( 'slicewp_user_action_preview_email', 'slicewp_user_action_preview_email' );


/**
 * Adds the "Affiliate" user role to all users that are also affiliates
 *
 */
function slicewp_admin_action_bulk_add_affiliate_user_role() {

	// Verify for nonce
	if( empty( $_GET['slicewp_token'] ) || ! wp_verify_nonce( $_GET['slicewp_token'], 'slicewp_bulk_add_affiliate_user_role' ) )
		return;

	// Get all affiliates
	$affiliates = slicewp_get_affiliates( array( 'number' => -1 ) );

	// Add user role to each affiliate
	foreach( $affiliates as $affiliate ) {

		$user = new WP_User( $affiliate->get( 'user_id' ) );

		$user->add_role( 'slicewp_affiliate' );

	}

	// Redirect to the settings with a success message
	wp_redirect( add_query_arg( array( 'page' => 'slicewp-settings', 'slicewp_message' => 'bulk_add_affiliate_user_role_success', 'updated' => count( $affiliates ), 'tab' => 'tools' ), admin_url( 'admin.php' ) ) );
	exit;

}
add_action( 'slicewp_admin_action_bulk_add_affiliate_user_role', 'slicewp_admin_action_bulk_add_affiliate_user_role' );


/**
 * Removes the "Affiliate" user role from all users that have it
 *
 */
function slicewp_admin_action_bulk_remove_affiliate_user_role() {

	// Verify for nonce
	if( empty( $_GET['slicewp_token'] ) || ! wp_verify_nonce( $_GET['slicewp_token'], 'slicewp_bulk_remove_affiliate_user_role' ) )
		return;

	// Get all users with the affiliate role
	$users = get_users( array( 'role' => 'slicewp_affiliate' ) );

	// Add user role to each affiliate
	foreach( $users as $user ) {

		$user->remove_role( 'slicewp_affiliate' );

	}

	// Redirect to the settings with a success message
	wp_redirect( add_query_arg( array( 'page' => 'slicewp-settings', 'slicewp_message' => 'bulk_remove_affiliate_user_role_success', 'updated' => count( $users ), 'tab' => 'tools' ), admin_url( 'admin.php' ) ) );
	exit;

}
add_action( 'slicewp_admin_action_bulk_remove_affiliate_user_role', 'slicewp_admin_action_bulk_remove_affiliate_user_role' );