<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Register the email notification sent to affiliate when the account is rejected
 *
 * @param array $email_notifications
 *
 * @return array
 *
 */
function slicewp_email_notification_affiliate_account_rejected( $email_notifications = array() ) {

    $email_notifications['affiliate_account_rejected'] = array(
        'name'          => __( 'Account Rejected', 'slicewp' ),
        'description'   => __( 'The affiliate will receive an email when an administrator rejects their registration pending request.', 'slicewp' ),
        'recipient'     => 'affiliate',
        'sending'       => 'manual'
    );

    return $email_notifications;

}
add_filter( 'slicewp_available_email_notification', 'slicewp_email_notification_affiliate_account_rejected', 40 );


/**
 * Send an email notification to the affiliate when the account is rejected
 *
 * @param int   $affiliate_id
 * @param array $affiliate_data
 *
 */
function slicewp_send_email_notification_affiliate_account_rejected( $affiliate_id = 0, $affiliate_data = array() ) {
    
    // Verify received arguments not to be empty
    if( empty( $affiliate_id ) )
		return;

    if( empty( $affiliate_data ) )
        return;

    // Verify if the send email option is enabled
    if ( empty( $_POST['send_email_notification'] ) )
        return;

    // Verify if the affiliate account status was changed to Rejected
    if( $affiliate_data['status'] != 'rejected' )
        return;

    // Verify if the email notification subject and content are filled in
	$notification_settings = slicewp_get_email_notification_settings( 'affiliate_account_rejected' );

    if( empty( $notification_settings['subject'] ) || empty( $notification_settings['content'] ) )
        return;

    // Verify if Reject Reason is provided
    if( empty( $_POST['affiliate_reject_reason'] ) )
        return;

    // Save the Reject Reason in affiliate's meta
    $affiliate_meta = slicewp_update_affiliate_meta( $affiliate_id, 'reject_reason', sanitize_text_field( $_POST['affiliate_reject_reason'] ) );

    // Get the affiliate email address
	$affiliate = slicewp_get_affiliate( $affiliate_id );
	$user      = get_user_by( 'id', $affiliate->get('user_id') );

	if( empty( $user->user_email ) )
		return;

    // Prepare the email subject and content
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
add_action( 'slicewp_update_affiliate', 'slicewp_send_email_notification_affiliate_account_rejected', 20, 2 );
