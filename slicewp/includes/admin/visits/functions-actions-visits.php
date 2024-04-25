<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Validates and handles the deleting of a visit from the database
 *
 */
function slicewp_admin_action_delete_visit() {

	// Verify for nonce
	if( empty( $_GET['slicewp_token'] ) || ! wp_verify_nonce( $_GET['slicewp_token'], 'slicewp_delete_visit' ) )
		return;

	// Verify for visit ID
	if( empty( $_GET['visit_id'] ) )
		return;

	// Verify for visit's existance
	$visit_id = absint( $_GET['visit_id'] );
	$visit 	  = slicewp_get_visit( $visit_id );

	if( is_null( $visit ) )
		return;

	// Delete the visit
	$deleted = slicewp_delete_visit( $visit_id );

	if( ! $deleted ) {

		slicewp_admin_notices()->register_notice( 'visit_delete_false', '<p>' . __( 'Something went wrong. Could not delete the visit. Please try again.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'visit_delete_false' );

		return;

	}

	// Redirect to the current page
	wp_redirect( add_query_arg( array( 'page' => 'slicewp-visits', 'slicewp_message' => 'visit_delete_success' ), admin_url( 'admin.php' ) ) );
	exit;

}
add_action( 'slicewp_admin_action_delete_visit', 'slicewp_admin_action_delete_visit', 50 );