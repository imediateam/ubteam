<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Register the email notification sent to affiliate when the account is approved
 *
 * @param array $email_notifications
 *
 * @return array
 *
 */
function slicewp_email_notification_affiliate_account_approved( $email_notifications = array() ) {

    $email_notifications['affiliate_account_approved'] = array(
        'name'          => __( 'Account Approved', 'slicewp' ),
        'description'   => __( 'The affiliate will receive an email when an administrator accepts their registration pending request.', 'slicewp' ),
        'recipient'     => 'affiliate',
        'sending'       => 'manual'
    );

    return $email_notifications;

}
add_filter( 'slicewp_available_email_notification', 'slicewp_email_notification_affiliate_account_approved', 35 );


/**
 * Send an email notification to the affiliate when:
 * - the account has been approved from the admin review affiliate application page
 * - the account has been manually added by an admin from the add new affiliate page
 *
 * @param int   $affiliate_id
 * @param array $affiliate_data
 *
 */
function slicewp_send_email_notification_affiliate_account_approved( $affiliate_id = 0, $affiliate_data = array() ) {

    if( ! is_admin() )
        return;

    if( empty( $affiliate_id ) )
		return;

    if( empty( $affiliate_data ) )
        return;

    // Handle returns for when adding a new affiliate manually
    if( doing_action( 'slicewp_insert_affiliate' ) ) {

        // Verify if send welcome message is enabled
        // Should work when adding a new affiliate from SliceWP and also when adding from Add New User
        if( empty( $_POST['welcome_email'] ) && empty( $_POST['slicewp_register_affiliate_welcome_email'] ) )
            return;

        // Verify if the affiliate account status was set to Active
        if( $affiliate_data['status'] != 'active' )
            return;

    }

    // Handle returns for when approving the registration application
    if( doing_action( 'slicewp_update_affiliate' ) ) {

        // Verify if the send email option is enabled
        if ( empty( $_POST['send_email_notification'] ) )
            return;
            
        // Verify if the affiliate account status was changed to Active
        if( $affiliate_data['status'] != 'active' )
            return;
        
    }

    // Verify if the email notification subject and content are filled in
    $notification_settings = slicewp_get_email_notification_settings( 'affiliate_account_approved' );
    
    if( empty( $notification_settings['subject'] ) || empty( $notification_settings['content'] ) )
        return;

    // Get the affiliate email address
	$affiliate = slicewp_get_affiliate( $affiliate_id );
	$user      = get_user_by( 'id', $affiliate->get('user_id') );

	if( empty( $user->user_email ) )
		return;

    //Prepare the email subject and content
	$email_subject = ( ! empty( $notification_settings['subject'] ) ? sanitize_text_field( $notification_settings['subject'] ) : '' );
	$email_content = ( ! empty( $notification_settings['content'] ) ? $notification_settings['content'] : '' );

	//Replace the tags with data
	$merge_tags = new SliceWP_Merge_Tags();
	$merge_tags->set( 'affiliate', $affiliate );

	$email_subject = $merge_tags->replace_tags( $email_subject );
    $email_content = $merge_tags->replace_tags( $email_content );
    
    //Send the email
	slicewp_wp_email( $user->user_email, $email_subject, $email_content );

}
add_action( 'slicewp_insert_affiliate', 'slicewp_send_email_notification_affiliate_account_approved', 20, 2 );
add_action( 'slicewp_update_affiliate', 'slicewp_send_email_notification_affiliate_account_approved', 20, 2 );
