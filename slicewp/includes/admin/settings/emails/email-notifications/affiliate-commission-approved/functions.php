<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Register the email notification sent to affiliates when a commission is approved
 *
 * @param array $email_notifications
 *
 * @return array
 *
 */
function slicewp_email_notification_affiliate_commission_approved( $email_notifications = array() ) {

	$email_notifications['affiliate_commission_approved'] = array(
		'name'			=> __( 'Commission Approved', 'slicewp' ),
		'description'	=> __( 'The affiliate will receive an email when an order that generated a commission is completed.', 'slicewp' ),
		'recipient'		=> 'affiliate'
	);

	return $email_notifications;

}
add_filter( 'slicewp_available_email_notification', 'slicewp_email_notification_affiliate_commission_approved', 45 );


/**
 * Capture the Commission Status before it gets updated
 *
 * @param int $commission_id
 *
 */
function slicewp_get_previous_commission_status( $commission_id = 0 ){

	// Verify received arguments not to be empty
	if( empty( $commission_id ) )
		return;

	// Get the Commission that will be updated
	$commission = slicewp_get_commission( $commission_id );

	if( empty( $commission ) )
		return;

	// Save the previous Commission Status
	$GLOBALS['slicewp_pre_update_commission_status_' . $commission_id] = $commission->get( 'status' );

}
add_action( 'slicewp_pre_update_commission', 'slicewp_get_previous_commission_status' );


/**
 * Send an email notification to the affiliate when a commision is approved
 *
 * @param int	$commission_id
 * @param array	$commission_data
 *
 */
function slicewp_send_email_notification_affiliate_commission_approved( $commission_id = 0, $commission_data = array() ) {

	// Verify received arguments not to be empty
	if( empty( $commission_id ) )
		return;

	if( empty( $commission_data ) )
		return;

	// Verify previus Commission status to be Pending
	if( $GLOBALS['slicewp_pre_update_commission_status_' . $commission_id ] != 'pending' )
		return;

	// Verify if the Commission status was changed to Unpaid
	if( $commission_data['status'] != 'unpaid' )
		return;

	// Get the Affiliate ID that registered the commission
	$commission = slicewp_get_commission( $commission_id );
	$affiliate_id = $commission->get('affiliate_id');

	// Verify if Email Notification sending is Enabled
	$notification_settings = slicewp_get_email_notification_settings( 'affiliate_commission_approved' );

	if( empty( $notification_settings['enabled'] ) )
		return;

	// Verify if the Email Notification Subject and Content are filled in
	if( empty( $notification_settings['subject'] ) || empty( $notification_settings['content'] ) )
		return;
	
	// Get the affiliate email address
	$affiliate = slicewp_get_affiliate( $affiliate_id );
	$user      = get_user_by( 'id', $affiliate->get('user_id') );

	if( empty( $user->user_email ) )
		return;

	// Prepare the email Content
	$email_subject = ( ! empty( $notification_settings['subject'] ) ? sanitize_text_field( $notification_settings['subject'] ) : '' );
	$email_content = ( ! empty( $notification_settings['content'] ) ? $notification_settings['content'] : '' );

	// Replace the tags with data
	$merge_tags = new SliceWP_Merge_Tags();
	$merge_tags->set( 'affiliate', $affiliate );
	$merge_tags->set( 'commission', $commission );

	$email_subject = $merge_tags->replace_tags( $email_subject );
	$email_content = $merge_tags->replace_tags( $email_content );

	slicewp_wp_email( $user->user_email, $email_subject, $email_content );

}
add_action( 'slicewp_update_commission', 'slicewp_send_email_notification_affiliate_commission_approved', 10, 2 );
