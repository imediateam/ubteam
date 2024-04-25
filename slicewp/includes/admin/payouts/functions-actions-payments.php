<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Validates and handles the removal of a commission from the payments table
 *
 */
function slicewp_admin_action_remove_commission() {

	// Verify for nonce
	if( empty( $_GET['slicewp_token'] ) || ! wp_verify_nonce( $_GET['slicewp_token'], 'slicewp_remove_commission' ) )
		return;

  	// Verify for Payment ID
	if( empty( $_GET['payment_id'] ) )
        return;
    
	// Verify for Commission ID
	if( empty( $_GET['commission_id'] ) )
		return;


    // Remove the Commission from Payment
    $payment_id = absint( $_GET['payment_id'] );
    $remove_id = absint( $_GET['commission_id'] );

    // Get the Payment data
    $payment = slicewp_get_payment( $payment_id );

    if( is_null( $payment ) )
        return;

    // Prepare the Commission IDs
    $commission_ids = array_map( 'trim', explode( ',', $payment->get('commission_ids') ) );
    $payment_amount = $payment->get('amount');

    // Remove the Commission ID from Payment
    foreach ( $commission_ids as $key => $commission ){
    
        if ( $commission == $remove_id ){
            
            //Compute the new Payment amount
            $commission_data = slicewp_get_commission( $commission_ids[$key] );
            $commission_amount = $commission_data->get('amount');
            $payment_amount -= $commission_amount;

            unset( $commission_ids[$key] );

        }

    }

    $commission_ids = implode( ',', $commission_ids );

    // Prepare Payment data to be updated
    $payment_data = array(
        'date_modified'     => slicewp_mysql_gmdate(),
        'commission_ids'	=> $commission_ids,
        'amount'            => $payment_amount
    );

    // Remove the Commission from Payment
	$updated = slicewp_update_payment( $payment_id, $payment_data );

	if( ! $updated ) {

		slicewp_admin_notices()->register_notice( 'payment_update_false', '<p>' . __( 'Something went wrong. Could not update the payment. Please try again.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'payment_update_false' );

		return;

	}

	// Redirect to the current page
	wp_redirect( add_query_arg( array( 'page' => 'slicewp-payouts', 'subpage' => 'review-payment', 'payment_id' => $payment_id, 'slicewp_message' => 'commission_remove_success' ), admin_url( 'admin.php' ) ) );
	exit;

}
add_action( 'slicewp_admin_action_remove_commission', 'slicewp_admin_action_remove_commission', 50 );


/**
 * Validates and handles the updating of a payment in the database
 *
 */
function slicewp_admin_action_review_payment() {
    
	// Verify for nonce
	if( empty( $_POST['slicewp_token'] ) || ! wp_verify_nonce( $_POST['slicewp_token'], 'slicewp_review_payment' ) )
		return;

    // Verify for Payment ID
	if( empty( $_POST['payment_id'] ) ) {

		slicewp_admin_notices()->register_notice( 'payment_id_missing', '<p>' . __( 'Something went wrong. Could not update the payment. Please try again.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'payment_id_missing' );

		return;

    }
    
	// Verify for payment's existance
	$payment_id = absint( $_POST['payment_id'] );
	$payment 	= slicewp_get_payment( $payment_id );

	if( is_null( $payment ) ) {

		slicewp_admin_notices()->register_notice( 'payment_not_exists', '<p>' . __( 'Something went wrong. Could not update the payment. Please try again.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'payment_not_exists' );

		return;

	}

    // Verify for payment status
	if( empty( $_POST['status'] ) ) {

		slicewp_admin_notices()->register_notice( 'payment_status_missing', '<p>' . __( 'Please select the status of the payment.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'payment_status_missing' );

		return;

    }
    
    $statuses = slicewp_get_payment_available_statuses();
    
	// Verify if the payment status is valid
	if( ! in_array( $_POST['status'], array_keys( $statuses ) ) ) {

		slicewp_admin_notices()->register_notice( 'payment_status_invalid', '<p>' . __( 'The selected status in not allowed.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'payment_status_invalid' );

		return;

    }
    
    $_POST = stripslashes_deep( $_POST );


    // Prepare payment data to be updated
	$payment_data = array(
		'date_modified' => slicewp_mysql_gmdate(),
		'status'		=> sanitize_text_field( $_POST['status'] )
	);

	// Update payment into the database
	$updated = slicewp_update_payment( $payment_id, $payment_data );

	// If the payment could not be updated show a message to the user
	if( ! $updated ) {

		slicewp_admin_notices()->register_notice( 'payment_update_false', '<p>' . __( 'Something went wrong. Could not update the payment. Please try again.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'payment_update_false' );

		return;

	}

	// Update commissions status only if the payment's new status is different
	if( $payment->get( 'status' ) != $_POST['status'] ) {

		// Get the commissions from the updated payment
		$commission_ids = array_map( 'trim', explode( ',', $payment->get('commission_ids') ) );

		// Prepare the data for the commission update
		$commission_data = array(
			'date_modified' => slicewp_mysql_gmdate(),
			'status'		=> ( $_POST['status'] == 'paid' ? 'paid'  : 'unpaid' ),
		);
		
		// Change the status of the commissions
		foreach ( $commission_ids as $commission_id ) {

			$updated = slicewp_update_commission( $commission_id, $commission_data );

			if( ! $updated ) {

				slicewp_admin_notices()->register_notice( 'commission_update_false', '<p>' . __( 'Something went wrong. Could not update the commission. Please try again.', 'slicewp' ) . '</p>', 'error' );
				slicewp_admin_notices()->display_notice( 'commission_update_false' );
		
				return;
		
			}
		
		}

	}

	// Redirect to the review page of the payment with a success message
	wp_redirect( add_query_arg( array( 'page' => 'slicewp-payouts', 'subpage' => 'review-payment', 'payment_id' => $payment_id, 'slicewp_message' => 'payment_update_success', 'updated' => '1' ), admin_url( 'admin.php' ) ) );
	exit;

}
add_action( 'slicewp_admin_action_review_payment', 'slicewp_admin_action_review_payment', 50 );


/**
 * Deletes the payment
 *
 */
function slicewp_admin_action_delete_payment(){

	// Verify for nonce
	if( empty( $_GET['slicewp_token'] ) || ! wp_verify_nonce( $_GET['slicewp_token'], 'slicewp_delete_payment' ) )
		return;

	// Verify for payment ID
	if( empty( $_GET['payment_id'] ) )
		return;
	
	// Verify for payment's existance
	$payment_id = absint( $_GET['payment_id'] );
	$payment = slicewp_get_payment( $payment_id );

	if( is_null( $payment ) )
		return;

	// Delete the payment
	$deleted = slicewp_delete_payment( $payment_id );

	if( ! $deleted ) {

		slicewp_admin_notices()->register_notice( 'payment_delete_false', '<p>' . __( 'Something went wrong. Could not delete the payment. Please try again.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'payment_delete_false' );

		return;

	}

	// Substract the deleted Payment amount from the Payout
	$payout_id = $payment->get( 'payout_id' );
	$payout = slicewp_get_payout( $payout_id );

	if ( empty( $payout ) ){

		slicewp_admin_notices()->register_notice( 'payout_update_false', '<p>' . __( 'Something went wrong. Could not update the payout. Please try again.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'payout_update_false' );

		return;

	}

	$payout_args = array(
		'amount' => $payout->get( 'amount' ) - $payment->get( 'amount' )
	);

	$updated = slicewp_update_payout( $payout_id, $payout_args );

	if( ! $updated ) {

		slicewp_admin_notices()->register_notice( 'payout_update_false', '<p>' . __( 'Something went wrong. Could not update the payout. Please try again.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'payout_update_false' );

		return;

	}

	// Redirect to the current page
	wp_redirect( add_query_arg( array( 'page' => 'slicewp-payouts', 'subpage' => isset( $_GET['subpage'] ) ? esc_attr( $_GET['subpage'] ) : '' , 'slicewp_message' => 'payment_delete_success', 'payout_id' => isset( $_GET['payout_id'] ) ? esc_attr( $_GET['payout_id'] ) : '' ), admin_url( 'admin.php' ) ) );
	exit;
	
}
add_action( 'slicewp_admin_action_delete_payment', 'slicewp_admin_action_delete_payment', 50 );
