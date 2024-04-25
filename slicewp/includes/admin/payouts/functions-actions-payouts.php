<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Preview the Payout
 *
 */
function slicewp_admin_action_preview_payout() {

	// Verify for Date Fields
	if( empty( $_POST['date_min'] ) || empty( $_POST['date_max'] ) ) {

		slicewp_admin_notices()->register_notice( 'payouts_date_empty', '<p>' . __( 'Please fill in the Start Date and End Date fields.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'payouts_date_empty' );

		return;

	}

	// Verify for End Date to be greater than Start Date
	if( $_POST['date_min'] > $_POST['date_max'] ) {

		slicewp_admin_notices()->register_notice( 'payouts_date_inversed', '<p>' . __( 'Please fill in an End Date greater than Start Date.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'payouts_date_inversed' );

		return;

	}

	// Verify for Payment Minimum Amount
	if( ! is_numeric( slicewp_sanitize_amount( $_POST['payments_minimum_amount'] ) ) || ! isset( $_POST['payments_minimum_amount'] ) || $_POST['payments_minimum_amount'] < 0 ) {

		slicewp_admin_notices()->register_notice( 'payments_minimum_amount_error', '<p>' . __( 'Please fill in a Payments Minimum Amount equal or greater than 0.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'payments_minimum_amount_error' );

		return;

	}

	// Get the start date from the filter, or set it to the default value if not present
	$date_min = ( ! empty( $_POST['date_min'] ) ? new DateTime( $_POST['date_min'] . ' 00:00:00' ) : '' );

	// Get the end date from the filter, or set it to the default value if not present
	$date_max = ( ! empty( $_POST['date_max'] ) ? new DateTime( $_POST['date_max'] . ' 23:59:59') : '' );

	// Prepare the arguments to read the commissions
	$commission_args = array(
		'number'		=> -1,
		'date_min'		=> ( ! empty( $date_min ) ? get_gmt_from_date( $date_min->format( 'Y-m-d H:i:s' ) ) : '' ),
		'date_max'		=> ( ! empty( $date_max ) ? get_gmt_from_date( $date_max->format( 'Y-m-d H:i:s' ) ) : '' ),
		'affiliate_id'	=> ( ! empty( $_POST['affiliate_id'] ) ? $_POST['affiliate_id'] : '' ),
		'status'		=> 'unpaid'
	);

	// Get the affiliate ids that generated the commissions
	$affiliate_ids = slicewp_get_commissions_column( 'affiliate_id', $commission_args );

	// Display an error if no affiliate_ids (therefore commissions) are found
	if ( empty( $affiliate_ids ) ) {

		slicewp_admin_notices()->register_notice( 'no_commissions_found', '<p>' . __( 'No commissions found.', 'slicewp' ) . '</p>', 'notice-warning' );
		slicewp_admin_notices()->display_notice( 'no_commissions_found' );
		
		return;

	}

	// Keep only the unique affiliate_ids
	$affiliate_ids = array_unique( $affiliate_ids );
	$affiliate_ids = array_map( 'absint', $affiliate_ids );
	$affiliate_ids = array_values( $affiliate_ids );

	// Get the Payments Minimum Amount setting
	$minimum_payment_amount = slicewp_sanitize_amount( isset( $_POST['payments_minimum_amount'] ) ? esc_attr( $_POST['payments_minimum_amount'] ) : slicewp_get_setting( 'payments_minimum_amount' ) );

	// Get the Currency setting
	$currency = slicewp_get_setting( 'active_currency', 'USD' );

	// We will save here the Payout amount
	$payout_amount = 0;
	
	// We will count here how many Payments we have
	$payments_count = 0;

	// Get the commissions of each affiliate
	foreach ( $affiliate_ids as $i => $affiliate_id ) {

		$commission_args['affiliate_id'] = $affiliate_id;
		$commissions = slicewp_get_commissions( $commission_args );

		$payment_amount = 0;
		$commission_ids = array();

		// Save the Payment amount
		foreach ( $commissions as $j => $commission ) {

			$payment_amount += $commission->get('amount');
			
		}

		// Skip the Payment if is less than the Payments Minimum Amount setting
		if ( $payment_amount < $minimum_payment_amount )
			continue;

		// Save the Payout amount
		$payout_amount += $payment_amount;

		// Increment the Payment counter
		$payments_count++;

	}

	// Check that we have payments
	if ( empty( $payments_count ) ){

		slicewp_admin_notices()->register_notice( 'no_payment_generated', '<p>' . __( 'Not enough commissions to generate a payment.', 'slicewp' ) . '</p>', 'notice-warning' );
		slicewp_admin_notices()->display_notice( 'no_payment_generated' );
		
		return;

	}

	// Redirect to the Preview Payouts page
	wp_redirect( add_query_arg( array( 'page' => 'slicewp-payouts', 'subpage' => 'preview-payout', 'date_min' => $_POST['date_min'], 'date_max' => $_POST['date_max'], 'payments_minimum_amount' => $minimum_payment_amount, 'payout_amount' => $payout_amount, 'payments_count' => $payments_count, 'affiliate_id' => ! empty( $_POST['affiliate_id'] ) ? esc_attr( $_POST['affiliate_id'] ) : '' ) , admin_url( 'admin.php' ) ) );
	exit;

}
add_action( 'slicewp_admin_action_preview_payout', 'slicewp_admin_action_preview_payout', 50 );


/**
 * Create the Payout
 *
 */
function slicewp_admin_action_create_payout() {

	// Verify for nonce
	if( empty( $_POST['slicewp_token'] ) || ! wp_verify_nonce( $_POST['slicewp_token'], 'slicewp_create_payout' ) )
		return;

	// Verify for Date Fields
	if( empty( $_POST['date_min'] ) || empty( $_POST['date_max'] ) ) {

		slicewp_admin_notices()->register_notice( 'payouts_date_empty', '<p>' . __( 'Please fill in the "Start Date" and "End Date" fields.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'payouts_date_empty' );

		return;

	}

	// Get the start date from the filter, or set it to the default value if not present
	$date_min = ( ! empty( $_POST['date_min'] ) ? new DateTime( $_POST['date_min'] . ' 00:00:00' ) : '' );

	// Get the end date from the filter, or set it to the default value if not present
	$date_max = ( ! empty( $_POST['date_max'] ) ? new DateTime( $_POST['date_max'] . ' 23:59:59') : '' );

	// Prepare the arguments to read the commissions
	$commission_args = array(
		'number'		=> -1,
		'date_min'		=> ( ! empty( $date_min ) ? get_gmt_from_date( $date_min->format( 'Y-m-d H:i:s' ) ) : '' ),
		'date_max'		=> ( ! empty( $date_max ) ? get_gmt_from_date( $date_max->format( 'Y-m-d H:i:s' ) ) : '' ),
		'affiliate_id'	=> ( ! empty( $_POST['affiliate_id'] ) ? $_POST['affiliate_id'] : '' ),
		'status'		=> 'unpaid'
	);

	// Get the affiliate ids that generated the commissions
	$affiliate_ids = slicewp_get_commissions_column( 'affiliate_id', $commission_args );

	// Display an error if no affiliate_ids (therefore commissions) are found
	if ( empty( $affiliate_ids ) ) {

		slicewp_admin_notices()->register_notice( 'no_commissions_found', '<p>' . __( 'No commissions found.', 'slicewp' ) . '</p>', 'notice-warning' );
		slicewp_admin_notices()->display_notice( 'no_commissions_found' );
		
		return;

	}

	// Keep only the unique affiliate_ids
	$affiliate_ids = array_unique( $affiliate_ids );
	$affiliate_ids = array_map( 'absint', $affiliate_ids );
	$affiliate_ids = array_values( $affiliate_ids );

	// The Payout amount will be saved here
	$payout_amount = 0;

	// Get the Payments Minimum Amount setting
	$minimum_payment_amount = slicewp_sanitize_amount( isset( $_POST['payments_minimum_amount'] ) ? esc_attr( $_POST['payments_minimum_amount'] ) : slicewp_get_setting( 'payments_minimum_amount' ) );

	// Get the Currency setting
	$currency = slicewp_get_setting( 'active_currency', 'USD' );

	// We will save here all the Payments data
	$all_payment_data = array();

	// Get the commissions of each affiliate
	foreach ( $affiliate_ids as $i => $affiliate_id ) {

		$commission_args['affiliate_id'] = $affiliate_id;
		$commissions = slicewp_get_commissions( $commission_args );

		$payment_amount = 0;
		$commission_ids = array();

		// Save the Payment amount and the Commission IDs
		foreach ( $commissions as $j => $commission ) {

			$payment_amount += $commission->get('amount');
			$commission_ids[$j] = $commission->get('id');
			
		}

		// Skip the Payment if is less than the Payments Minimum Amount setting
		if ( $payment_amount < $minimum_payment_amount )
			continue;

		// Save the Commission IDs in a string
		$commission_ids = implode( ',', $commission_ids );

		// Prepare the Payout data
		$payment_data = array(
			'affiliate_id'	 => $affiliate_id,
			'amount'		 => $payment_amount,
			'currency'		 => $currency,
			'payout_method'	 => 'manual',
			'date_created'   => slicewp_mysql_gmdate(),
			'status'		 => 'unpaid',
			'commission_ids' => $commission_ids,
		);

		// Save the Payment data
		$all_payment_data[] = $payment_data;

		// Save the Payout amount
		$payout_amount += $payment_amount;
	
	}

	// Check that we have payments
	if ( empty( $all_payment_data ) ){

		slicewp_admin_notices()->register_notice( 'no_payment_generated', '<p>' . __( 'Not enough commissions to generate a payment.', 'slicewp' ) . '</p>', 'notice-warning' );
		slicewp_admin_notices()->display_notice( 'no_payment_generated' );
		
		return;

	}

	// Create the Payout
	$payout_data = array(
		'date_created'  => slicewp_mysql_gmdate(),
		'date_modified' => slicewp_mysql_gmdate(),
		'admin_id'		=> get_current_user_id(),
		'amount'		=> $payout_amount
	);

	$payout_id = slicewp_insert_payout( $payout_data );

	// If the payout could not be inserted show a message to the user
	if( ! $payout_id ) {

		slicewp_admin_notices()->register_notice( 'payout_insert_false', '<p>' . __( 'Something went wrong. Could not add the payout. Please try again.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'payout_insert_false' );

		return;

	}

	// Create the Payments
	foreach ( $all_payment_data as $payment_data ){

		$payment_data['payout_id'] = $payout_id;

		$payment_id = slicewp_insert_payment( $payment_data );

	}

	// Redirect to the generated payout page
	wp_redirect( add_query_arg( array( 'page' => 'slicewp-payouts', 'subpage' => 'view-payout', 'payout_id' => $payout_id, 'slicewp_message' => 'payout_insert_success', 'payments_count' => count( $all_payment_data ) ), admin_url( 'admin.php' ) ) );
	exit;

}
add_action( 'slicewp_admin_action_create_payout', 'slicewp_admin_action_create_payout', 50 );


/**
 * Generates the Payment CSV
 *
 */
function slicewp_admin_action_generate_payouts_csv(){

	// Verify for nonce
	if( empty( $_GET['slicewp_token'] ) || ! wp_verify_nonce( $_GET['slicewp_token'], 'slicewp_generate_payouts_csv' ) )
		return;

	// Verify for Payout ID
	if( empty( $_GET['payout_id'] ) )
		return;

	// Verify for Payout existance
	$payout_id = absint( $_GET['payout_id'] );
	$payout = slicewp_get_payout( $payout_id );
	
	if( is_null( $payout_id ) )
		return;

	// Get the payments contained in the payout
	$payments_args = array(
		'number'	=> -1,
		'payout_id'	=> $payout_id,
	);

	$payments = slicewp_get_payments( $payments_args );
	
	if( is_null( $payments ) )
		return;

	// Prepare the CSV file header
	$csv_header = array(
		'ID',
		'Name',
		'Email',
		'Amount',
		'Currency'
	);
	
	// Prepare the CSV data
	foreach ( $payments as $key => $payment ){

		$data[$key][] = $payment->get('id');
		$data[$key][] = slicewp_get_affiliate_name( $payment->get('affiliate_id') );

		$affiliate = slicewp_get_affiliate( $payment->get('affiliate_id') );
		
		$data[$key][] = $affiliate->get('payment_email');
		$data[$key][] = $payment->get('amount');
		$data[$key][] = $payment->get('currency');

	}

	$filename = 'slicewp-payout-' . $payout_id . '.csv';
	slicewp_generate_csv( $csv_header, $data, $filename );

}
add_action( 'slicewp_admin_action_generate_payouts_csv', 'slicewp_admin_action_generate_payouts_csv', 50 );


/**
 * Handles the bulk payments action, which should pay all payments of a payout
 *
 */
function slicewp_admin_action_do_bulk_payments() {

	// Verify for nonce
	if( empty( $_POST['slicewp_token'] ) || ! wp_verify_nonce( $_POST['slicewp_token'], 'slicewp_do_bulk_payments' ) )
		return;

	// Verify for Payout ID
	if( empty( $_POST['payout_method'] ) )
		return;

	if( empty( $_POST['payout_id'] ) )
		return;

	$payout_method = sanitize_text_field( $_POST['payout_method'] );
	$payout_id 	   = absint( $_POST['payout_id'] );
	$payout 	   = slicewp_get_payout( $payout_id );

	if( is_null( $payout ) )
		return;

	// Get payments
	$payments = slicewp_get_payments( array( 'number' => -1, 'payout_id' => $payout_id ) );

	// Check if all payments are paid
	$payments_fully_paid = true;

	foreach( $payments as $payment ) {

		if( $payment->get( 'status' ) == 'paid' )
			continue;

		$payments_fully_paid = false;
		break;

	}

	// Return early if all payments are paid
	if( $payments_fully_paid ) {

		slicewp_add_log( 'Bulk payments was not processed. All payout payments are marked as paid.' );
		return;

	}


	// Remove all paid and processing payments
	foreach( $payments as $key => $payment ) {

		if( in_array( $payment->get( 'status' ), array( 'paid', 'processing' ) ) )
			unset( $payments[$key] );

	}

	$payments = array_values( $payments );


	/**
	 * Action hook for each payout method to hook into and handle the payments
	 *
	 * @param int   $payout_id
	 * @param array $payments
	 *
	 */
	do_action( 'slicewp_do_bulk_payments_' . $payout_method, $payout_id, $payments );

}
add_action( 'slicewp_admin_action_do_bulk_payments', 'slicewp_admin_action_do_bulk_payments' );


/**
 * Deletes a payout and the contained payments
 *
 */
function slicewp_admin_action_delete_payout() {

	// Verify for nonce
	if( empty( $_GET['slicewp_token'] ) || ! wp_verify_nonce( $_GET['slicewp_token'], 'slicewp_delete_payout' ) )
		return;

	// Verify for Payout ID
	if( empty( $_GET['payout_id'] ) )
		return;

	$payout_id = absint( $_GET['payout_id'] );
	$payout	   = slicewp_get_payout( $payout_id );

	if( is_null( $payout ) )
		return;

	// Check if current user is the one that generated the payout
	if( $payout->get('admin_id') != get_current_user_id() ) {

		slicewp_admin_notices()->register_notice( 'payout_delete_different_admin', '<p>' . __( 'You are not allowed to delete this payout because it was generated by another administrator.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'payout_delete_different_admin' );

		return;

	}

	// Get payments
	$payments = slicewp_get_payments( array( 'number' => -1, 'payout_id' => $payout_id, 'status' => 'paid' ) );

	// Return early if any payments are paid
	if( ! empty ( $payments ) ) {

		slicewp_admin_notices()->register_notice( 'payout_payments_paid', '<p>' . __( 'The payout was not deleted because it contains paid payments.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'payout_payments_paid' );

		return;

	}

	// Delete the payments
	foreach( $payments as $payment ) {

		$deleted = slicewp_delete_payment( $payment->get('id') );

		if( ! $deleted ) {

			slicewp_add_log( sprintf( 'Payout #%s was not deleted because the contained payment #%s could not be deleted.', $payout_id, $payment->get('id') ) );

			slicewp_admin_notices()->register_notice( 'payment_delete_false', '<p>' . sprintf( __( 'Payout #%s was not deleted because the contained payment #%s could not be deleted.', 'slicewp') , $payment_id, $payment->get('id') ) . '</p>', 'error' );
			slicewp_admin_notices()->display_notice( 'payment_delete_false' );
	
			return;

		}

		// Delete the payment's metadata
		$payment_meta = slicewp_get_payment_meta( $payment->get('id') );

		if( ! empty( $payment_meta ) ) {

			foreach( $payment_meta as $key => $value ) {

				slicewp_delete_payment_meta( $payment->get('id'), $key );

			}

		}
		
	}


	// Delete the payout
	$deleted = slicewp_delete_payout( $payout_id );
	
	if( ! $deleted ) {

		slicewp_admin_notices()->register_notice( 'payout_delete_false', '<p>' . __( 'Something went wrong. Could not delete the payout. Please try again.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'payout_delete_false' );
	
		return;

	}

	// Delete the payout's metadata
	$payout_meta = slicewp_get_payout_meta( $payout_id );

	if( ! empty( $payout_meta ) ) {

		foreach( $payout_meta as $key => $value ) {

			slicewp_delete_payout_meta( $payment->get('id'), $key );

		}

	}
	
	// Redirect to the current page
	wp_redirect( add_query_arg( array( 'page' => 'slicewp-payouts', 'slicewp_message' => 'payout_delete_success' ), admin_url( 'admin.php' ) ) );
	exit;

}
add_action( 'slicewp_admin_action_delete_payout', 'slicewp_admin_action_delete_payout', 50 );