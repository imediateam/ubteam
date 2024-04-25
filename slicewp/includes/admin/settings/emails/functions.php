<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Returns the available email notifications
 *
 * @return array
 *
 */
function slicewp_get_available_email_notifications() {

	/**
	 * Filter to dynamically add email notifications
	 *
	 * @param array
	 *
	 */
	$email_notifications = apply_filters( 'slicewp_available_email_notification', array() );

	$email_notifications = ( is_array( $email_notifications ) ? $email_notifications : array() );

	return $email_notifications;

}


/**
 * Returns the settings saved for the given email notification
 *
 * @param string $email_notification_slug
 *
 * @return array
 *
 */
function slicewp_get_email_notification_settings( $email_notification_slug = '' ) {

	if( empty( $email_notification_slug ) )
		return array();

	$email_notifications_settings = slicewp_get_setting( 'email_notifications' );

	if ( empty( $email_notifications_settings[$email_notification_slug] ) )
		return array();
		
	return $email_notifications_settings[$email_notification_slug];

}


/**
 * Modifies the from_name from the wp_mail_from_name filter before sending an email
 *
 * @param string $from_name
 *
 */
function slicewp_email_notification_modify_from_name( $from_name ) {

	// Get email notification settings
	$notification_settings_from_name = slicewp_get_setting( 'from_name' );

	if( empty( $notification_settings_from_name ) )
		return $from_name;

	// Set from name
	$from_name = sanitize_text_field( $notification_settings_from_name );

	return $from_name;

}


/**
 * Modifies the from_email from the wp_mail_from filter before sending an email
 *
 * @param string $from_email
 *
 */
function slicewp_email_notification_modify_from_email( $from_email ) {

	// Get email notification settings
	$notification_settings_from_email = slicewp_get_setting( 'from_email' );

	if( empty( $notification_settings_from_email ) )
		return $from_email;

	if( ! is_email( $notification_settings_from_email ) )
		return $from_email;

	// Set from name
	$from_email = sanitize_text_field( $notification_settings_from_email );

	return $from_email;

}


/**
 * Modifies the email content type from the wp_mail_content_type filter before sending an email
 *
 */
function slicewp_email_notification_modify_email_content_type() {

	return apply_filters( 'slicewp_email_content_type', 'text/html' );

}


/**
 * Send the email notification
 *
 * @param string $email_recipient
 * @param string $email_subject
 * @param string $email_content
 *
 * @return bool
 *
 */
function slicewp_wp_email( $email_recipient, $email_subject, $email_content ){

	if ( empty( $email_recipient ) )
		return false;
	
	if ( empty( $email_subject ) )
		return false;
	
	if ( empty( $email_content ) )
		return false;

	// Add paragraphs to content
	$email_content = wpautop( $email_content );

	// Get the email template
	$email_template = slicewp_get_email_template( slicewp_get_setting( 'email_template' ) );

	// Replace the content with the full template
	if( ! is_null( $email_template ) ) {

		ob_start();

		include $email_template['path'];

		$email_content = ob_get_contents();

		/**
		 * Attempts to remove code comments if true
		 *
		 * @param bool
		 *
		 */
		$strip_email_comments = apply_filters( 'slicewp_strip_email_code_comments', true );

		if( $strip_email_comments ) {

			//  Removes code comments
			$email_content = preg_replace( '/(\s+)\/\*([^\/]*)\*\/(\s+)/s', "\n", $email_content );

		}

		ob_end_clean();

	}
	
	// Temporary change the from name and from email
    add_filter( 'wp_mail_from_name', 'slicewp_email_notification_modify_from_name', 999 );
    add_filter( 'wp_mail_from', 'slicewp_email_notification_modify_from_email', 999 );
    add_filter( 'wp_mail_content_type', 'slicewp_email_notification_modify_email_content_type', 999 );

	// Send email
    $sent = wp_mail( $email_recipient, $email_subject, $email_content );

    // Reset the from name and email
    remove_filter( 'wp_mail_from_name', 'slicewp_email_notification_modify_from_name', 999 );
    remove_filter( 'wp_mail_from', 'slicewp_email_notification_modify_from_email', 999 );
    remove_filter( 'wp_mail_content_type', 'slicewp_email_notification_modify_email_content_type', 999 );

    return $sent;

}