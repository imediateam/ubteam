<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Validates and handles the adding of the new creative in the database
 *
 */
function slicewp_admin_action_add_creative() {

	// Verify for nonce
	if( empty( $_POST['slicewp_token'] ) || ! wp_verify_nonce( $_POST['slicewp_token'], 'slicewp_add_creative' ) )
		return;

	// Verify for creative name
	if( empty( $_POST['name'] ) ) {

		slicewp_admin_notices()->register_notice( 'creative_name_missing', '<p>' . __( 'Please fill the name of your new creative.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'creative_name_missing' );

		return;

	}

	// Verify for creative type
	if( empty( $_POST['type'] ) ) {

		slicewp_admin_notices()->register_notice( 'creative_type_missing', '<p>' . __( 'Please select the type of your new creative.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'creative_type_missing' );

		return;

	}

	$types = slicewp_get_creative_available_types();

	// Verify if the creative type is valid
	if( ! in_array( $_POST['type'], array_keys( $types ) ) ) {

		slicewp_admin_notices()->register_notice( 'creative_type_invalid', '<p>' . __( 'The selected type in not allowed.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'creative_type_invalid' );

		return;

	}

	// Verify the fields to be filled based on the creative type
	if( $_POST['type'] == 'image' && empty( $_POST['image_url'] ) ) {

		slicewp_admin_notices()->register_notice( 'creative_image_url_missing', '<p>' . __( 'Please select an image for your creative.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'creative_image_url_missing' );

		return;

	} elseif ( ( $_POST['type'] == 'text' || $_POST['type'] == 'long_text' ) && empty( $_POST['text'] ) ) {

		slicewp_admin_notices()->register_notice( 'creative_text_missing', '<p>' . __( 'Please write the text for your creative.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'creative_text_missing' );

		return;
		
	}

	// Verify that Landing URL contains valid URL
    if ( ! empty( $_POST['landing_url'] ) && filter_var( $_POST['landing_url'], FILTER_VALIDATE_URL ) === FALSE) {

        slicewp_admin_notices()->register_notice( 'landing_url_invalid_error', '<p>' . __( 'Please provide a valid Landing URL.', 'slicewp' ) . '</p>', 'error' );
        slicewp_admin_notices()->display_notice( 'landing_url_invalid_error' );

        return;
    
    }

	// Verify for creative status
	if( empty( $_POST['status'] ) ) {

		slicewp_admin_notices()->register_notice( 'creative_status_missing', '<p>' . __( 'Please select the status of your new creative.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'creative_status_missing' );

		return;

	}

	$statuses = slicewp_get_creative_available_statuses();

	// Verify if the creative status is valid
	if( ! in_array( $_POST['status'], array_keys( $statuses ) ) ) {

		slicewp_admin_notices()->register_notice( 'creative_status_invalid', '<p>' . __( 'The selected status in not allowed.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'creative_status_invalid' );

		return;

	}

	$_POST = stripslashes_deep( $_POST );

	// Prepare creative data to be inserted
	$creative_data = array(
		'name' 			=> sanitize_text_field( $_POST['name'] ),
		'description' 	=> sanitize_text_field( $_POST['description'] ),
		'date_created'  => slicewp_mysql_gmdate(),
		'date_modified' => slicewp_mysql_gmdate(),
		'type' 			=> sanitize_text_field( $_POST['type'] ),
		'image_url'		=> ( ! empty( $_POST['image_url'] ) ? sanitize_text_field( $_POST['image_url'] ) : '' ),
		'alt_text'		=> ( ! empty( $_POST['alt_text'] ) ? sanitize_text_field( $_POST['alt_text'] ) : '' ),
		'text'			=> ( ! empty( $_POST['text'] ) ? sanitize_textarea_field( $_POST['text'] ) : '' ),
		'landing_url'	=> sanitize_text_field( $_POST['landing_url'] ),
		'status'		=> sanitize_text_field( $_POST['status'] )
	);

	// Insert creative into the database
	$creative_id = slicewp_insert_creative( $creative_data );

	// If the creative could not be inserted show a message to the user
	if( ! $creative_id ) {

		slicewp_admin_notices()->register_notice( 'creative_insert_false', '<p>' . __( 'Something went wrong. Could not add the creative. Please try again.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'creative_insert_false' );

		return;

	}

	// Redirect to the edit page of the creative with a success message
	wp_redirect( add_query_arg( array( 'page' => 'slicewp-creatives', 'subpage' => 'edit-creative', 'creative_id' => $creative_id, 'slicewp_message' => 'creative_insert_success' ), admin_url( 'admin.php' ) ) );
	exit;

}
add_action( 'slicewp_admin_action_add_creative', 'slicewp_admin_action_add_creative', 50 );


/**
 * Validates and handles the updating of an creative in the database
 *
 */
function slicewp_admin_action_update_creative() {

	// Verify for nonce
	if( empty( $_POST['slicewp_token'] ) || ! wp_verify_nonce( $_POST['slicewp_token'], 'slicewp_update_creative' ) )
		return;

	// Verify for creative ID
	if( empty( $_POST['creative_id'] ) ) {

		slicewp_admin_notices()->register_notice( 'creative_id_missing', '<p>' . __( 'Something went wrong. Could not update the creative. Please try again.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'creative_id_missing' );

		return;

	}

	// Verify for creative's existance
	$creative_id = absint( $_POST['creative_id'] );
	$creative = slicewp_get_creative( $creative_id );

	if( is_null( $creative ) ) {

		slicewp_admin_notices()->register_notice( 'creative_not_exists', '<p>' . __( 'Something went wrong. Could not update the creative. Please try again.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'creative_not_exists' );

		return;

	}

	// Verify for creative name
	if( empty( $_POST['name'] ) ) {

		slicewp_admin_notices()->register_notice( 'creative_name_missing', '<p>' . __( 'Please fill the name of your creative.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'creative_name_missing' );

		return;

	}

	// Verify for creative type
	if( empty( $_POST['type'] ) ) {

		slicewp_admin_notices()->register_notice( 'creative_type_missing', '<p>' . __( 'Please select the type of your creative.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'creative_type_missing' );

		return;

	}

	$types = slicewp_get_creative_available_types();

	// Verify if the creative type is valid
	if( ! in_array( $_POST['type'], array_keys( $types ) ) ) {

		slicewp_admin_notices()->register_notice( 'creative_type_invalid', '<p>' . __( 'The selected type in not allowed.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'creative_type_invalid' );

		return;

	}

	// Verify the fields to be filled based on the creative type
	if( $_POST['type'] == 'image' && empty( $_POST['image_url'] ) ) {

		slicewp_admin_notices()->register_notice( 'creative_image_url_missing', '<p>' . __( 'Please select an image for your creative.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'creative_image_url_missing' );

		return;

	} elseif ( ( $_POST['type'] == 'text' || $_POST['type'] == 'long_text' ) && empty( $_POST['text'] ) ) {

		slicewp_admin_notices()->register_notice( 'creative_text_missing', '<p>' . __( 'Please write the text for your creative.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'creative_text_missing' );

		return;
		
	}

	// Verify that Landing URL contains valid URL
    if ( ! empty( $_POST['landing_url'] ) && filter_var( $_POST['landing_url'], FILTER_VALIDATE_URL ) === FALSE) {

        slicewp_admin_notices()->register_notice( 'landing_url_invalid_error', '<p>' . __( 'Please provide a valid Landing URL.', 'slicewp' ) . '</p>', 'error' );
        slicewp_admin_notices()->display_notice( 'landing_url_invalid_error' );

        return;
    
	}
	
	// Verify for creative status
	if( empty( $_POST['status'] ) ) {

		slicewp_admin_notices()->register_notice( 'creative_status_missing', '<p>' . __( 'Please select the status of your creative.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'creative_status_missing' );

		return;

	}

	$statuses = slicewp_get_creative_available_statuses();

	// Verify if the creative status is valid
	if( ! in_array( $_POST['status'], array_keys( $statuses ) ) ) {

		slicewp_admin_notices()->register_notice( 'creative_status_invalid', '<p>' . __( 'The selected status in not allowed.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'creative_status_invalid' );

		return;

	}

	$_POST = stripslashes_deep( $_POST );

	// Prepare creative data to be updated
	$creative_data = array(
		'name'			=> sanitize_text_field( $_POST['name'] ), 
		'description'	=> sanitize_text_field( $_POST['description'] ), 
		'date_modified' => slicewp_mysql_gmdate(),
		'type' 			=> sanitize_text_field( $_POST['type'] ),
		'image_url'		=> ( ! empty( $_POST['image_url'] ) ? sanitize_text_field( $_POST['image_url'] ) : '' ),
		'alt_text'		=> ( ! empty( $_POST['alt_text'] ) ? sanitize_text_field( $_POST['alt_text'] ) : '' ),
		'text'			=> ( ! empty( $_POST['text'] ) ? sanitize_textarea_field( $_POST['text'] ) : '' ),
		'landing_url'	=> sanitize_text_field( $_POST['landing_url'] ),
		'status'		=> sanitize_text_field( $_POST['status'] )
	);

	// Update creative into the database
	$updated = slicewp_update_creative( $creative_id, $creative_data );

	// If the creative could not be inserted show a message to the user
	if( ! $updated ) {

		slicewp_admin_notices()->register_notice( 'creative_update_false', '<p>' . __( 'Something went wrong. Could not update the creative. Please try again.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'creative_update_false' );

		return;

	}

	// Redirect to the edit page of the creative with a success message
	wp_redirect( add_query_arg( array( 'page' => 'slicewp-creatives', 'subpage' => 'edit-creative', 'creative_id' => $creative_id, 'slicewp_message' => 'creative_update_success' ), admin_url( 'admin.php' ) ) );
	exit;

}
add_action( 'slicewp_admin_action_update_creative', 'slicewp_admin_action_update_creative', 50 );


/**
 * Validates and handles the deleting of a creative from the database
 *
 */
function slicewp_admin_action_delete_creative() {

	// Verify for nonce
	if( empty( $_GET['slicewp_token'] ) || ! wp_verify_nonce( $_GET['slicewp_token'], 'slicewp_delete_creative' ) )
		return;

	// Verify for creative ID
	if( empty( $_GET['creative_id'] ) )
		return;

	// Verify for creative's existance
	$creative_id = absint( $_GET['creative_id'] );
	$creative 	 = slicewp_get_creative( $creative_id );

	if( is_null( $creative ) )
		return;

	// Delete the creative
	$deleted = slicewp_delete_creative( $creative_id );

	if( ! $deleted ) {

		slicewp_admin_notices()->register_notice( 'creative_delete_false', '<p>' . __( 'Something went wrong. Could not delete the creative. Please try again.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'creative_delete_false' );

		return;

	}


	// Redirect to the current page
	wp_redirect( add_query_arg( array( 'page' => 'slicewp-creatives', 'slicewp_message' => 'creative_delete_success' ), admin_url( 'admin.php' ) ) );
	exit;

}

add_action( 'slicewp_admin_action_delete_creative', 'slicewp_admin_action_delete_creative', 50 );