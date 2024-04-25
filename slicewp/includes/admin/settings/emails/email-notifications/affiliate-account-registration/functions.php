<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Register the email notification sent to affiliates when they register a new account
 *
 * @param array $email_notifications
 *
 * @return array
 *
 */
function slicewp_email_notification_affiliate_account_registration( $email_notifications = array() ) {

	$email_notifications['affiliate_account_registration'] = array(
		'name'			=> __( 'Account Registration', 'slicewp' ),
		'description'	=> __( 'The affiliate will receive an email after the registration form is successfully submitted.', 'slicewp' ),
		'recipient'		=> 'affiliate'
	);
	
	return $email_notifications;

}
add_filter( 'slicewp_available_email_notification', 'slicewp_email_notification_affiliate_account_registration', 30 );


/**
 * Send an email notification to the affiliate when they register manually from the affiliate register page
 *
 * @param int $affiliate_id
 *
 */
function slicewp_send_email_notification_affiliate_succesfull_registration( $affiliate_id ) {

	if( empty( $affiliate_id ) )
		return;

	if( is_admin() )
		return;

	// Verify if email notification sending is enabled
	$notification_settings = slicewp_get_email_notification_settings( 'affiliate_account_registration' );

	if( empty( $notification_settings['enabled'] ) )
		return;
	
    if( empty( $notification_settings['subject'] ) || empty( $notification_settings['content'] ) )
        return;
        
    // Get the affiliate email address
    $affiliate = slicewp_get_affiliate( $affiliate_id );
    $user 	   = get_user_by( 'id', $affiliate->get( 'user_id' ) );

	if( empty( $user->user_email ) )
		return;

	// Prepare the email content
	$email_subject = ( ! empty( $notification_settings['subject'] ) ? sanitize_text_field( $notification_settings['subject'] ) : '' );
	$email_content = ( ! empty( $notification_settings['content'] ) ? $notification_settings['content'] : '' );

	// Replace the tags with data
	$merge_tags = new SliceWP_Merge_Tags();
	$merge_tags->set( 'affiliate', $affiliate );

	$email_subject = $merge_tags->replace_tags( $email_subject );
	$email_content = $merge_tags->replace_tags( $email_content );

    // Send the email
	slicewp_wp_email( $user->user_email, $email_subject, $email_content );

}
add_action( 'slicewp_register_affiliate', 'slicewp_send_email_notification_affiliate_succesfull_registration' );