<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * AJAX callback that inserts a new note into the DB and returns the HTML for the newly added note
 *
 */
function slicewp_action_ajax_insert_note() {

	// Verify for token
	if( empty( $_POST['slicewp_token'] ) || ! wp_verify_nonce( $_POST['slicewp_token'], 'slicewp_action_note' ) )
		wp_die( 0 );

	// Bail if the user doesn't have proper capabilities
	if( ! current_user_can( 'manage_options' ) )
		wp_die( 0 );

	// Make sure object context and id are provided
	if( empty( $_POST['object_context'] ) || ! isset( $_POST['object_id'] ) )
		wp_die( 0 );

	// Make sure the content is sent
	if( empty( $_POST['note_content'] ) )
		wp_die( 0 );

	$_POST = stripslashes_deep( $_POST );

	// Sanitize data
	$object_context = sanitize_text_field( $_POST['object_context'] );
	$object_id 		= absint( $_POST['object_id'] );
	$note_content   = wp_kses_post( $_POST['note_content'] );

	// Prepare note data
	$note_data = array(
		'object_context' => $object_context,
		'object_id'		 => $object_id,
		'user_id'		 => get_current_user_id(),
		'date_created'	 => slicewp_mysql_gmdate(),
		'note_content'	 => $note_content
	);

	// Insert note
	$note_id = slicewp_insert_note( $note_data );

	// Bail if something went wrong
	if( ! $note_id )
		wp_die( 0 );

	$notes_outputter = new SliceWP_Outputter_Notes( $object_context, $object_id );
	$notes_outputter->output_note( slicewp_get_note( $note_id ) );

	wp_die();

}
add_action( 'wp_ajax_slicewp_action_ajax_insert_note', 'slicewp_action_ajax_insert_note' );


/**
 * AJAX callback that deletes a note from the DB
 *
 */
function slicewp_action_ajax_delete_note() {

	// Verify for token
	if( empty( $_POST['slicewp_token'] ) || ! wp_verify_nonce( $_POST['slicewp_token'], 'slicewp_action_note' ) )
		wp_die( 0 );

	// Bail if the user doesn't have proper capabilities
	if( ! current_user_can( 'manage_options' ) )
		wp_die( 0 );

	// Make sure the note_id is present
	if( empty( $_POST['note_id'] ) )
		wp_die( 0 );

	$_POST = stripslashes_deep( $_POST );

	// Delete note
	$deleted = slicewp_delete_note( absint( $_POST['note_id'] ) );

	// Bail if something went wrong
	if( ! $deleted )
		wp_die( 0 );

	wp_die( 1 );

}
add_action( 'wp_ajax_slicewp_action_ajax_delete_note', 'slicewp_action_ajax_delete_note' );