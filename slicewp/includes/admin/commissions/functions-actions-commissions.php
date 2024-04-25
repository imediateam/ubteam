<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Validates and handles the adding of the new commission in the database
 *
 */
function slicewp_admin_action_add_commission() {

	// Verify for nonce
	if( empty( $_POST['slicewp_token'] ) || ! wp_verify_nonce( $_POST['slicewp_token'], 'slicewp_add_commission' ) )
		return;

	// Verify for affiliate ID
	if( empty( $_POST['affiliate_id'] ) ) {

		slicewp_admin_notices()->register_notice( 'commission_affiliate_id_missing', '<p>' . __( 'Please select the affiliate you wish to assign this commission to.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'commission_affiliate_id_missing' );

		return;

	}

	// Verify for creation date
	if( empty( $_POST['date_created'] ) ) {

		slicewp_admin_notices()->register_notice( 'commission_date_created_missing', '<p>' . __( 'Please set the date for the commission.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'commission_date_created_missing' );

		return;

	}

	// Verify that the date is valid
	if( ! slicewp_is_date_valid( $_POST['date_created'], 'Y-m-d H:i:s' ) ) {

		slicewp_admin_notices()->register_notice( 'commission_date_created_invalid', '<p>' . __( 'The selected date and time for the commission are invalid.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'commission_date_created_invalid' );

		return;

	}

	// Verify for commission type
	if( empty( $_POST['type'] ) ) {

		slicewp_admin_notices()->register_notice( 'commission_type_missing', '<p>' . __( 'Please select the type of the new commission.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'commission_type_missing' );

		return;

	}

	// Verify for commission status
	if( empty( $_POST['status'] ) ) {

		slicewp_admin_notices()->register_notice( 'commission_status_missing', '<p>' . __( 'Please select the status of the new commission.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'commission_status_missing' );

		return;

	}

	$statuses = slicewp_get_commission_available_statuses();

	// Verify if the commission status is valid
	if( ! in_array( $_POST['status'], array_keys( $statuses ) ) ) {

		slicewp_admin_notices()->register_notice( 'commission_status_invalid', '<p>' . __( 'The selected status in not allowed.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'commission_status_invalid' );

		return;

	}

	$_POST = stripslashes_deep( $_POST );

	// Prepare commission data to be inserted
	$commission_data = array(
		'affiliate_id'  => absint( $_POST['affiliate_id'] ),
		'date_created'  => get_gmt_from_date( sanitize_text_field( $_POST['date_created'] ) ),
		'date_modified' => slicewp_mysql_gmdate(),
		'amount'		=> slicewp_sanitize_amount( ( ! empty( $_POST['amount'] ) ? sanitize_text_field( $_POST['amount'] ) : '0' ) ),
		'reference'		=> ( ! empty( $_POST['reference'] ) ? sanitize_text_field( $_POST['reference'] ) : '' ),
		'origin'		=> ( ! empty( $_POST['origin'] ) ? sanitize_text_field( $_POST['origin'] ) : '' ),
		'type'			=> sanitize_text_field( $_POST['type'] ),
		'status'		=> sanitize_text_field( $_POST['status'] ),
		'currency'		=> slicewp_get_setting( 'active_currency', 'USD' )
	);

	// Insert commission into the database
	$commission_id = slicewp_insert_commission( $commission_data );

	// If the commission could not be inserted show a message to the user
	if( ! $commission_id ) {

		slicewp_admin_notices()->register_notice( 'commission_insert_false', '<p>' . __( 'Something went wrong. Could not add the commission. Please try again.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'commission_insert_false' );

		return;

	}

	// Redirect to the edit page of the commission with a success message
	wp_redirect( add_query_arg( array( 'page' => 'slicewp-commissions', 'subpage' => 'edit-commission', 'commission_id' => $commission_id, 'slicewp_message' => 'commission_insert_success' ), admin_url( 'admin.php' ) ) );
	exit;

}
add_action( 'slicewp_admin_action_add_commission', 'slicewp_admin_action_add_commission', 50 );


/**
 * Validates and handles the updating of an commission in the database
 *
 */
function slicewp_admin_action_update_commission() {

	// Verify for nonce
	if( empty( $_POST['slicewp_token'] ) || ! wp_verify_nonce( $_POST['slicewp_token'], 'slicewp_update_commission' ) )
		return;

	// Verify for commission ID
	if( empty( $_POST['commission_id'] ) ) {

		slicewp_admin_notices()->register_notice( 'commission_id_missing', '<p>' . __( 'Something went wrong. Could not update the commission. Please try again.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'commission_id_missing' );

		return;

	}

	// Verify for commission's existance
	$commission_id = absint( $_POST['commission_id'] );
	$commission    = slicewp_get_commission( $commission_id );

	if( is_null( $commission ) ) {

		slicewp_admin_notices()->register_notice( 'commission_not_exists', '<p>' . __( 'Something went wrong. Could not update the commission. Please try again.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'commission_not_exists' );

		return;

	}

	// Verify for commission status
	if( empty( $_POST['status'] ) ) {

		slicewp_admin_notices()->register_notice( 'commission_status_missing', '<p>' . __( 'Please select the status of your new commission.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'commission_status_missing' );

		return;

	}

	$statuses = slicewp_get_commission_available_statuses();

	// Verify if the commission status is valid
	if( ! in_array( $_POST['status'], array_keys( $statuses ) ) ) {

		slicewp_admin_notices()->register_notice( 'commission_status_invalid', '<p>' . __( 'The selected status in not allowed.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'commission_status_invalid' );

		return;

	}

	$_POST = stripslashes_deep( $_POST );

	// Prepare commission data to be updated
	$commission_data = array(
		'date_modified' => slicewp_mysql_gmdate(),
		'amount'		=> slicewp_sanitize_amount( ( ! empty( $_POST['amount'] ) ? sanitize_text_field( $_POST['amount'] ) : '0' ) ),
		'reference'		=> ( ! empty( $_POST['reference'] ) ? sanitize_text_field( $_POST['reference'] ) : '' ),
		'origin'		=> ( ! empty( $_POST['origin'] ) ? sanitize_text_field( $_POST['origin'] ) : $commission->get( 'origin' ) ),
		'type'			=> sanitize_text_field( $_POST['type'] ),
		'status'		=> sanitize_text_field( $_POST['status'] ),
	);

	// Update commission into the database
	$updated = slicewp_update_commission( $commission_id, $commission_data );

	// If the commission could not be inserted show a message to the user
	if( ! $updated ) {

		slicewp_admin_notices()->register_notice( 'commission_update_false', '<p>' . __( 'Something went wrong. Could not update the commission. Please try again.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'commission_update_false' );

		return;

	}

	// Redirect to the edit page of the commission with a success message
	wp_redirect( add_query_arg( array( 'page' => 'slicewp-commissions', 'subpage' => 'edit-commission', 'commission_id' => $commission_id, 'slicewp_message' => 'commission_update_success', 'updated' => '1' ), admin_url( 'admin.php' ) ) );
	exit;

}
add_action( 'slicewp_admin_action_update_commission', 'slicewp_admin_action_update_commission', 50 );


/**
 * Validates and handles the deleting of an commission from the database
 *
 */
function slicewp_admin_action_delete_commission() {

	// Verify for nonce
	if( empty( $_GET['slicewp_token'] ) || ! wp_verify_nonce( $_GET['slicewp_token'], 'slicewp_delete_commission' ) )
		return;

	// Verify for commission ID
	if( empty( $_GET['commission_id'] ) )
		return;

	// Verify for commission's existance
	$commission_id = absint( $_GET['commission_id'] );
	$commission    = slicewp_get_commission( $commission_id );

	if( is_null( $commission ) )
		return;

	// Delete the commission
	$deleted = slicewp_delete_commission( $commission_id );

	if( ! $deleted ) {

		slicewp_admin_notices()->register_notice( 'commission_delete_false', '<p>' . __( 'Something went wrong. Could not delete the commission. Please try again.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'commission_delete_false' );

		return;

	}

	// Delete the commission's metadata
	$commission_meta = slicewp_get_commission_meta( $commission_id );

	if( ! empty( $commission_meta ) ) {

		foreach( $commission_meta as $key => $value ) {

			slicewp_delete_commission_meta( $commission_id, $key );

		}

	}

	// Redirect to the current page
	wp_redirect( add_query_arg( array( 'page' => 'slicewp-commissions', 'slicewp_message' => 'commission_delete_success' ), admin_url( 'admin.php' ) ) );
	exit;

}
add_action( 'slicewp_admin_action_delete_commission', 'slicewp_admin_action_delete_commission', 50 );