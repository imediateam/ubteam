<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Includes the files needed for the notes
 *
 */
function slicewp_include_files_note() {

	// Get note dir path
	$dir_path = plugin_dir_path( __FILE__ );

	// Include ajax actions
	if( file_exists( $dir_path . 'functions-actions-ajax.php' ) )
		include $dir_path . 'functions-actions-ajax.php';

	// Include main note class
	if( file_exists( $dir_path . 'class-note.php' ) )
		include $dir_path . 'class-note.php';

	// Include the db layer classes
	if( file_exists( $dir_path . 'class-object-db-notes.php' ) )
		include $dir_path . 'class-object-db-notes.php';

	// Include the outputter classes
	if( file_exists( $dir_path . 'class-outputter-notes.php' ) )
		include $dir_path . 'class-outputter-notes.php';

}
add_action( 'slicewp_include_files', 'slicewp_include_files_note' );


/**
 * Register the class that handles database queries for the notes
 *
 * @param array $classes
 *
 * @return array
 *
 */
function slicewp_register_database_classes_notes( $classes ) {

	$classes['notes'] = 'SliceWP_Object_DB_Notes';

	return $classes;

}
add_filter( 'slicewp_register_database_classes', 'slicewp_register_database_classes_notes' );


/**
 * Returns an array with SliceWP_Affiliate objects from the database
 *
 * @param array $args
 * @param bool  $count
 *
 * @return array
 *
 */
function slicewp_get_notes( $args = array(), $count = false ) {

	$notes = slicewp()->db['notes']->get_notes( $args, $count );

	/**
	 * Add a filter hook just before returning
	 *
	 * @param array $notes
	 * @param array $args
	 * @param bool  $count
	 *
	 */
	return apply_filters( 'slicewp_get_notes', $notes, $args, $count );

}


/**
 * Gets a note from the database
 *
 * @param mixed int|object      - note id or object representing the note
 *
 * @return SliceWP_Affiliate|null
 *
 */
function slicewp_get_note( $note ) {

	return slicewp()->db['notes']->get_object( $note );

}


/**
 * Inserts a new note into the database
 *
 * @param array $data
 *
 * @return mixed int|false
 *
 */
function slicewp_insert_note( $data ) {

	return slicewp()->db['notes']->insert( $data );

}


/**
 * Updates a note from the database
 *
 * @param int 	$note_id
 * @param array $data
 *
 * @return bool
 *
 */
function slicewp_update_note( $note_id, $data ) {

	return slicewp()->db['notes']->update( $note_id, $data );

}


/**
 * Deletes a note from the database
 *
 * @param int $note_id
 *
 * @return bool
 *
 */
function slicewp_delete_note( $note_id ) {

	return slicewp()->db['notes']->delete( $note_id );

}


/**
 * Adds the notes card to the secondary (sidebar) section in different views
 *
 */
function slicewp_views_add_notes_card() {

	if( empty( $_GET['subpage'] ) )
		return;

	$object_context = '';
	$object_id		= 0;

	switch( $_GET['subpage'] ) {

		// Affiliate pages
		case 'add-affiliate':
		case 'edit-affiliate':
			$object_context = 'affiliate';
			$object_id		= ( ! empty( $_GET['affiliate_id'] ) ? absint( $_GET['affiliate_id'] ) : 0 );
			break;

		case 'add-commission':
		case 'edit-commission':
			$object_context = 'commission';
			$object_id		= ( ! empty( $_GET['commission_id'] ) ? absint( $_GET['commission_id'] ) : 0 );
			break;

		case 'view-payout':
			$object_context = 'payout';
			$object_id 		= ( ! empty( $_GET['payout_id'] ) ? absint( $_GET['payout_id'] ) : 0 );
			break;

		case 'review-payment':
			$object_context = 'payment';
			$object_id 		= ( ! empty( $_GET['payment_id'] ) ? absint( $_GET['payment_id'] ) : 0 );
			break;

		default:
			break;

	}

	if( empty( $object_context ) )
		return;

	$notes_outputter = new SliceWP_Outputter_Notes( $object_context, $object_id );
	$notes_outputter->output_card();

}
add_action( 'slicewp_view_affiliates_add_affiliate_secondary', 'slicewp_views_add_notes_card' );
add_action( 'slicewp_view_affiliates_edit_affiliate_secondary', 'slicewp_views_add_notes_card' );
add_action( 'slicewp_view_commissions_add_commission_secondary', 'slicewp_views_add_notes_card' );
add_action( 'slicewp_view_commissions_edit_commission_secondary', 'slicewp_views_add_notes_card' );
add_action( 'slicewp_view_payouts_view_payout_secondary', 'slicewp_views_add_notes_card' );
add_action( 'slicewp_view_payouts_review_payment_secondary', 'slicewp_views_add_notes_card' );


/**
 * Updates the object_id of the notes that have been added on a add new page
 *
 * @param $object_id - the id of the affiliate, commission, payout, etc
 *
 */
function slicewp_insert_object_update_notes_object_id( $object_id ) {

	if( empty( $_POST['note_ids'] ) )
		return;

	// Sanitize notes
	$note_ids = array_map( 'absint', explode( ',', $_POST['note_ids'] ) );

	// Update each note with the object_id
	foreach( $note_ids as $note_id ) {

		if( empty( $note_id ) )
			continue;

		slicewp_update_note( $note_id, array( 'object_id' => $object_id ) );

	}

}
add_action( 'slicewp_insert_affiliate', 'slicewp_insert_object_update_notes_object_id' );
add_action( 'slicewp_insert_commission', 'slicewp_insert_object_update_notes_object_id' );


/**
 * Deletes all notes associated with an object when deleting the given object
 *
 * @param int $object_id
 *
 */
function slicewp_delete_object_delete_notes( $object_id ) {

	// Get all notes
	$notes = slicewp_get_notes( array( 'number' => -1, 'object_id' => $object_id ) );

	// Do nothing if no notes are found
	if( empty( $notes ) )
		return;

	// Delete each note
	foreach( $notes as $note ) {

		slicewp_delete_note( $note->get( 'id' ) );

	}

}
add_action( 'slicewp_delete_affiliate', 'slicewp_delete_object_delete_notes' );
add_action( 'slicewp_delete_commission', 'slicewp_delete_object_delete_notes' );
add_action( 'slicewp_delete_payment', 'slicewp_delete_object_delete_notes' );
add_action( 'slicewp_delete_payout', 'slicewp_delete_object_delete_notes' );