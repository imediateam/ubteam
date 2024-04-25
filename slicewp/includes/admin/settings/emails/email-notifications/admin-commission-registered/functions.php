<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Register the email notification sent to administrators when a new commission is registered
 *
 * @param array $email_notifications
 *
 * @return array
 *
 */
function slicewp_email_notification_admin_new_commission_registered( $email_notifications = array() ) {

	$email_notifications['admin_new_commission_registered'] = array(
		'name' 		  => __( 'Commission Registered', 'slicewp' ),
		'description' => __( 'The administrator will receive an email when a commission is generated.', 'slicewp' ),
		'recipient'   => 'admin'
    );
	
	return $email_notifications;

}
add_filter( 'slicewp_available_email_notification', 'slicewp_email_notification_admin_new_commission_registered', 20 );


/**
 * Send an email notification to the admininstrators when a new commision is registered
 *
 * @param int   $commission_id
 * @param array $commission_data
 *
 */
function slicewp_send_email_notification_admin_new_commission_registered( $commission_id = 0, $commission_data = array() ) {

	// Verify if the notification request comes from backend
	if ( is_admin() )
		return;

	// Verify received arguments not to be empty
	if( empty( $commission_id ) )
		return;

	if( empty( $commission_data ) )
		return;

	// Get the affiliate ID that registered the commission
	$affiliate_id = $commission_data['affiliate_id'];

	// Verify if email notification sending is enabled
	$notification_settings = slicewp_get_email_notification_settings( 'admin_new_commission_registered' );

	if( empty( $notification_settings['enabled'] ) )
		return;
	
	// Verify if the email notification subject and content are filled in
	if( empty( $notification_settings['subject'] ) || empty( $notification_settings['content'] ) )
		return;
	
	// Verify that admin emails are not empty
	$admin_emails = slicewp_get_setting( 'admin_emails' );

	if( empty( $admin_emails ) )
		return;

	// Put the admin emails in an array
	$admin_emails = array_filter( array_map( 'trim', explode( ',', $admin_emails ) ) );

	// Remove array items that are not email addresses
	if( ! empty( $admin_emails ) ) {

		foreach( $admin_emails as $key => $value ) {
			if( ! is_email( $value ) )
				unset( $admin_emails[$key] );
		}

		$admin_emails = array_values( $admin_emails );

	}

	if( empty( $admin_emails ) )
		return;

	// Prepare the email subject and content
	$email_subject = ( ! empty( $notification_settings['subject'] ) ? sanitize_text_field( $notification_settings['subject'] ) : '' );
	$email_content = ( ! empty( $notification_settings['content'] ) ? $notification_settings['content'] : '' );

	// Get the Affiliate and Commission Objects
	$affiliate = slicewp_get_affiliate( $affiliate_id );
	$commission = slicewp_get_commission( $commission_id );

	// Replace the tags with data
	$merge_tags = new SliceWP_Merge_Tags();
	$merge_tags->set( 'affiliate', $affiliate );
	$merge_tags->set( 'commission', $commission );

	$email_subject = $merge_tags->replace_tags( $email_subject );
	$email_content = $merge_tags->replace_tags( $email_content );

	// Send the email
	slicewp_wp_email( $admin_emails, $email_subject, $email_content );

}
add_action( 'slicewp_insert_commission', 'slicewp_send_email_notification_admin_new_commission_registered', 10, 2 );

