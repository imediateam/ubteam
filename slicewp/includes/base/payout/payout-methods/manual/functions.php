<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Register "Manual" payout method
 *
 * @param array $payout_methods
 *
 * @return array
 *
 */
function slicewp_register_payout_method_manual( $payout_methods ) {

	if( ! is_array( $payout_methods ) )
		return array();

	$payout_methods['manual'] = array(
		'label'    => __( 'Manual', 'slicewp' ),
		'supports' => array( 'single_payment', 'bulk_payments' ),
		'messages' => array(
			'payout_form_confirmation_bulk_payments' => __( 'This will mark all unpaid and failed payments for this payout as paid. All commissions associated with these payments will also be marked as paid. Are you sure you want to continue?', 'slicewp' )
		)
	);

	return $payout_methods;

}
add_filter( 'slicewp_register_payout_methods', 'slicewp_register_payout_method_manual', 10 );


/**
 * Handles "manual" bulk payments
 *
 * @param int   $payout_id
 * @param array $payments
 *
 */
function slicewp_do_bulk_payments_manual( $payout_id, $payments ) {

	// Go through each payment and mark it as paid
	foreach( $payments as $payment ) {

		// Take into account only unpaid and failed payments
		if( ! in_array( $payment->get( 'status' ), array( 'unpaid', 'failed' ) ) )
			continue;

		// Update payment
		$payment_data = array(
			'date_modified' => slicewp_mysql_gmdate(),
			'payout_method' => 'manual',
			'status' 		=> 'paid'
		);

		$updated = slicewp_update_payment( $payment->get( 'id' ), $payment_data );

		// If the payment wasn't updated, go to next payment
		if( ! $updated )
			continue;

		// If the payment was updated, update each of the generated commissions
		$commission_ids = array_map( 'trim', explode( ',', $payment->get( 'commission_ids' ) ) );

		$commission_data = array(
			'date_modified' => slicewp_mysql_gmdate(),
			'status'		=> 'paid'
		);

		foreach ( $commission_ids as $commission_id ) {

			$updated = slicewp_update_commission( $commission_id, $commission_data );
		
		}

	}

	// Redirect to the current page
	wp_redirect( add_query_arg( array( 'page' => 'slicewp-payouts', 'subpage' => 'view-payout', 'payout_id' => absint( $payout_id ), 'slicewp_message' => 'payout_bulk_payments_manual_success' ), admin_url( 'admin.php' ) ) );
	exit;

}
add_action( 'slicewp_do_bulk_payments_manual', 'slicewp_do_bulk_payments_manual', 10, 2 );