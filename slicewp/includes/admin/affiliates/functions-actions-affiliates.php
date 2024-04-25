<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Validates and handles the adding of the new affiliate in the database
 *
 */
function slicewp_admin_action_add_affiliate() {

	// Verify for nonce
	if( empty( $_POST['slicewp_token'] ) || ! wp_verify_nonce( $_POST['slicewp_token'], 'slicewp_add_affiliate' ) )
		return;

	// Verify for user ID
	if( empty( $_POST['user_id'] ) ) {

		slicewp_admin_notices()->register_notice( 'affiliate_user_id_missing', '<p>' . __( 'Please select the user you wish to add as an affiliate.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'affiliate_user_id_missing' );

		return;

	}

	// Verify if affiliate isn't already added
	$affiliates = slicewp_get_affiliates( array( 'user_id' => absint( $_POST['user_id'] ) ) );

	if( ! empty( $affiliates ) ) {

		slicewp_admin_notices()->register_notice( 'affiliate_already_exists', '<p>' . __( 'An affiliate attached to the selected user already exists.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'affiliate_already_exists' );

		return;

	}

	// Verify for Payment Email
    if( empty( $_POST['payment_email'] ) ) {

        slicewp_admin_notices()->register_notice( 'payment_email_empty_error', '<p>' . __( 'Please fill in the Payment Email.', 'slicewp' ) . '</p>', 'error' );
        slicewp_admin_notices()->display_notice( 'payment_email_empty_error' );

        return;
    
    }

    // Verify for valid Payment Email
    if ( ! is_email( $_POST['payment_email'] ) ) {

        slicewp_admin_notices()->register_notice( 'payment_email_invalid_error', '<p>' . __( 'Please fill in a valid Payment Email.', 'slicewp' ) . '</p>', 'error' );
        slicewp_admin_notices()->display_notice( 'payment_email_invalid_error' );

        return;
    
    }

	// Verify for affiliate status
	if( empty( $_POST['status'] ) ) {

		slicewp_admin_notices()->register_notice( 'affiliate_status_missing', '<p>' . __( 'Please select the status of your new affiliate.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'affiliate_status_missing' );

		return;

	}

	$statuses = slicewp_get_affiliate_available_statuses();

	// Verify if the affiliate status is valid
	if( ! in_array( $_POST['status'], array_keys( $statuses ) ) ) {

		slicewp_admin_notices()->register_notice( 'affiliate_status_invalid', '<p>' . __( 'The selected status in not allowed.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'affiliate_status_invalid' );

		return;

	}

	$_POST = stripslashes_deep( $_POST );

	// Prepare affiliate data to be inserted
	$affiliate_data = array(
		'user_id' 		=> absint( $_POST['user_id'] ),
		'website'		=> ( ! empty( $_POST['website'] ) ? sanitize_text_field( $_POST['website'] ) : '' ),
		'payment_email' => sanitize_email( $_POST['payment_email'] ),
		'date_created'  => slicewp_mysql_gmdate(),
		'date_modified' => slicewp_mysql_gmdate(),
		'status'		=> sanitize_text_field( $_POST['status'] )
	);

	// Insert affiliate into the database
	$affiliate_id = slicewp_insert_affiliate( $affiliate_data );

	// If the affiliate could not be inserted show a message to the user
	if( ! $affiliate_id ) {

		slicewp_admin_notices()->register_notice( 'affiliate_insert_false', '<p>' . __( 'Something went wrong. Could not add the affiliate. Please try again.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'affiliate_insert_false' );

		return;

	}

	// Redirect to the edit page of the affiliate with a success message
	wp_redirect( add_query_arg( array( 'page' => 'slicewp-affiliates', 'subpage' => 'edit-affiliate', 'affiliate_id' => $affiliate_id, 'slicewp_message' => 'affiliate_insert_success' ), admin_url( 'admin.php' ) ) );
	exit;

}
add_action( 'slicewp_admin_action_add_affiliate', 'slicewp_admin_action_add_affiliate', 50 );


/**
 * Validates and handles the updating of an affiliate in the database
 *
 */
function slicewp_admin_action_update_affiliate() {

	// Verify for nonce
	if( empty( $_POST['slicewp_token'] ) || ! wp_verify_nonce( $_POST['slicewp_token'], 'slicewp_update_affiliate' ) )
		return;

	// Verify for affiliate ID
	if( empty( $_POST['affiliate_id'] ) ) {

		slicewp_admin_notices()->register_notice( 'affiliate_id_missing', '<p>' . __( 'Something went wrong. Could not update the affiliate. Please try again.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'affiliate_id_missing' );

		return;

	}

    // Verify for Payment Email
    if( empty( $_POST['payment_email'] ) ) {

        slicewp_admin_notices()->register_notice( 'payment_email_empty_error', '<p>' . __( 'Please fill in the Payment Email.', 'slicewp' ) . '</p>', 'error' );
        slicewp_admin_notices()->display_notice( 'payment_email_empty_error' );

        return;
    
    }

    // Verify for valid Payment Email
    if ( ! is_email( $_POST['payment_email'] ) ) {

        slicewp_admin_notices()->register_notice( 'payment_email_invalid_error', '<p>' . __( 'Please fill in a valid Payment Email.', 'slicewp' ) . '</p>', 'error' );
        slicewp_admin_notices()->display_notice( 'payment_email_invalid_error' );

        return;
    
    }

	// Verify for affiliate's existance
	$affiliate_id = absint( $_POST['affiliate_id'] );
	$affiliate 	  = slicewp_get_affiliate( $affiliate_id );

	if( is_null( $affiliate ) ) {

		slicewp_admin_notices()->register_notice( 'affiliate_not_exists', '<p>' . __( 'Something went wrong. Could not update the affiliate. Please try again.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'affiliate_not_exists' );

		return;

	}

	// Verify for affiliate status
	if( empty( $_POST['status'] ) ) {

		slicewp_admin_notices()->register_notice( 'affiliate_status_missing', '<p>' . __( 'Please select the status of your new affiliate.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'affiliate_status_missing' );

		return;

	}

	$statuses = slicewp_get_affiliate_available_statuses();

	// Verify if the affiliate status is valid
	if( ! in_array( $_POST['status'], array_keys( $statuses ) ) ) {

		slicewp_admin_notices()->register_notice( 'affiliate_status_invalid', '<p>' . __( 'The selected status in not allowed.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'affiliate_status_invalid' );

		return;

	}

	$_POST = stripslashes_deep( $_POST );

	// Prepare affiliate data to be updated
	$affiliate_data = array(
		'date_modified' => slicewp_mysql_gmdate(),
		'website'		=> ( ! empty( $_POST['website'] ) ? sanitize_text_field( $_POST['website'] ) : '' ),
		'payment_email'	=> sanitize_email( $_POST['payment_email'] ),
		'status'		=> sanitize_text_field( $_POST['status'] )
	);

	// Update affiliate into the database
	$updated = slicewp_update_affiliate( $affiliate_id, $affiliate_data );

	// If the affiliate could not be inserted show a message to the user
	if( ! $updated ) {

		slicewp_admin_notices()->register_notice( 'affiliate_update_false', '<p>' . __( 'Something went wrong. Could not update the affiliate. Please try again.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'affiliate_update_false' );

		return;

	}

	// Redirect to the edit page of the affiliate with a success message
	wp_redirect( add_query_arg( array( 'page' => 'slicewp-affiliates', 'subpage' => 'edit-affiliate', 'affiliate_id' => $affiliate_id, 'slicewp_message' => 'affiliate_update_success', 'updated' => '1' ), admin_url( 'admin.php' ) ) );
	exit;

}
add_action( 'slicewp_admin_action_update_affiliate', 'slicewp_admin_action_update_affiliate', 50 );


/**
 * Validates and handles the deleting of an affiliate from the database
 *
 */
function slicewp_admin_action_delete_affiliate() {

	// Verify for nonce
	if( empty( $_GET['slicewp_token'] ) || ! wp_verify_nonce( $_GET['slicewp_token'], 'slicewp_delete_affiliate' ) )
		return;

	// Verify for affiliate ID
	if( empty( $_GET['affiliate_id'] ) )
		return;

	// Verify for affiliate's existance
	$affiliate_id = absint( $_GET['affiliate_id'] );
	$affiliate 	  = slicewp_get_affiliate( $affiliate_id );

	if( is_null( $affiliate ) )
		return;

	// Delete the affiliate
	$deleted = slicewp_delete_affiliate( $affiliate_id );

	if( ! $deleted ) {

		slicewp_admin_notices()->register_notice( 'affiliate_delete_false', '<p>' . __( 'Something went wrong. Could not delete the affiliate. Please try again.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'affiliate_delete_false' );

		return;

	}

	// Delete the affiliate's metadata
	$affiliate_meta = slicewp_get_affiliate_meta( $affiliate_id );

	if( ! empty( $affiliate_meta ) ) {

		foreach( $affiliate_meta as $key => $value ) {

			slicewp_delete_affiliate_meta( $affiliate_id, $key );

		}

	}

	// Redirect to the current page
	wp_redirect( add_query_arg( array( 'page' => 'slicewp-affiliates', 'slicewp_message' => 'affiliate_delete_success' ), admin_url( 'admin.php' ) ) );
	exit;

}
add_action( 'slicewp_admin_action_delete_affiliate', 'slicewp_admin_action_delete_affiliate', 50 );


/**
 * Validates and handles the review process of an affiliate
 *
 */
function slicewp_admin_action_review_affiliate() {

	// Verify for nonce
	if( empty( $_POST['slicewp_token'] ) || ! wp_verify_nonce( $_POST['slicewp_token'], 'slicewp_review_affiliate' ) )
		return;

	// Verify for affiliate ID
	if( empty( $_POST['affiliate_id'] ) ) {

		slicewp_admin_notices()->register_notice( 'affiliate_id_missing', '<p>' . __( 'Something went wrong. Could not update the affiliate application. Please try again.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'affiliate_id_missing' );

		return;

	}

	// Verify for affiliate's existance
	$affiliate_id = absint( $_POST['affiliate_id'] );
	$affiliate 	  = slicewp_get_affiliate( $affiliate_id );

	if( is_null( $affiliate ) ) {

		slicewp_admin_notices()->register_notice( 'affiliate_not_exists', '<p>' . __( 'Something went wrong. Could not update the affiliate application. Please try again.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'affiliate_not_exists' );

		return;

	}

	// Verify if Reject Reason is filled
    if ( isset( $_POST['slicewp_reject_affiliate'] ) && isset( $_POST['send_email_notification'] ) && empty( $_POST['affiliate_reject_reason'] ) ) {

        slicewp_admin_notices()->register_notice( 'affiliate_reject_reason', '<p>' . __( 'Please fill in the Reject Reason field.', 'slicewp' ) . '</p>', 'error' );
        slicewp_admin_notices()->display_notice( 'affiliate_reject_reason' );

        return;

	}
	
	// Prepare affiliate data to be updated
	$_POST = stripslashes_deep( $_POST );
	if ( isset( $_POST['slicewp_approve_affiliate'] ) ) {

		$affiliate_data = array(
			'date_modified' => slicewp_mysql_gmdate(),
			'status'		=> 'active'
		);

	} else {

		$affiliate_data = array(
			'date_modified' => slicewp_mysql_gmdate(),
			'status'		=> 'rejected'
		);

	}

	// Update affiliate into the database
	$updated = slicewp_update_affiliate( $affiliate_id, $affiliate_data );

	// If the affiliate could not be inserted show a message to the user
	if( ! $updated && isset( $_POST['slicewp_approve_affiliate'] ) ) {

		slicewp_admin_notices()->register_notice( 'affiliate_review_approve_false', '<p>' . __( 'Something went wrong. Could not Approve the affiliate application. Please try again.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'affiliate_review_approve_false' );

		return;

	}

	if( ! $updated && isset( $_POST['slicewp_reject_affiliate'] ) ) {

		slicewp_admin_notices()->register_notice( 'affiliate_review_reject_false', '<p>' . __( 'Something went wrong. Could not Reject the affiliate application. Please try again.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'affiliate_review_reject_false' );

		return;

	}

	// Redirect to the edit page of the affiliate with a success message
	if( isset( $_POST['slicewp_approve_affiliate'] ) ) {

		wp_redirect( add_query_arg( array( 'page' => 'slicewp-affiliates', 'affiliate_id' => $affiliate_id, 'slicewp_message' => 'affiliate_review_approve_success' ), admin_url( 'admin.php' ) ) );
		exit;
	
	} else {

		wp_redirect( add_query_arg( array( 'page' => 'slicewp-affiliates', 'affiliate_id' => $affiliate_id, 'slicewp_message' => 'affiliate_review_reject_success' ), admin_url( 'admin.php' ) ) );
		exit;
	
	}
	
}
add_action( 'slicewp_admin_action_review_affiliate', 'slicewp_admin_action_review_affiliate', 50 );
