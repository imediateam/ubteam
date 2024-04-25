<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Includes the files needed for the payout
 *
 */
function slicewp_include_files_payout() {

	// Get payout dir path
	$dir_path = plugin_dir_path( __FILE__ );

	// Include main payout class
	if( file_exists( $dir_path . 'class-payout.php' ) )
		include $dir_path . 'class-payout.php';

	// Include the db layer classes
	if( file_exists( $dir_path . 'class-object-db-payouts.php' ) )
		include $dir_path . 'class-object-db-payouts.php';

	if( file_exists( $dir_path . 'class-object-meta-db-payouts.php' ) )
		include $dir_path . 'class-object-meta-db-payouts.php';

}
add_action( 'slicewp_include_files', 'slicewp_include_files_payout' );


/**
 * Register the class that handles database queries for the payout
 *
 * @param array $classes
 *
 * @return array
 *
 */
function slicewp_register_database_classes_payout( $classes ) {

	$classes['payouts'] = 'SliceWP_Object_DB_Payouts';
	$classes['payoutmeta'] = 'SliceWP_Object_Meta_DB_Payouts';

	return $classes;

}
add_filter( 'slicewp_register_database_classes', 'slicewp_register_database_classes_payout' );


/**
 * Returns an array with SliceWP_Payout objects from the database
 *
 * @param array $args
 * @param bool  $count
 *
 * @return array
 *
 */
function slicewp_get_payouts( $args = array(), $count = false ) {

	$payouts = slicewp()->db['payouts']->get_payouts( $args, $count );

	/**
	 * Add a filter hook just before returning
	 *
	 * @param array $payouts
	 * @param array $args
	 * @param bool  $count
	 *
	 */
	return apply_filters( 'slicewp_get_payouts', $payouts, $args, $count );

}

/**
 * Gets a payouts group from the database
 *
 * @param mixed int|object      - payout group id or object representing the payout
 *
 * @return SliceWP_Payout_Group|false
 *
 */
function slicewp_get_payout( $payout ) {

	return slicewp()->db['payouts']->get_object( $payout );

}


/**
 * Inserts a new payouts group into the database
 *
 * @param array $data
 *
 * @return mixed int|false
 *
 */
function slicewp_insert_payout( $data ) {

	return slicewp()->db['payouts']->insert( $data );

}


/**
 * Updates a payout from the database
 *
 * @param int 	$payout_id
 * @param array $data
 *
 * @return bool
 *
 */
function slicewp_update_payout( $payout_id, $data ) {

	return slicewp()->db['payouts']->update( $payout_id, $data );

}


/**
 * Deletes a payout from the database
 *
 * @param int $payout_id
 *
 * @return bool
 *
 */
function slicewp_delete_payout( $payout_id ) {

	return slicewp()->db['payouts']->delete( $payout_id );

}

/**
 * Inserts a new meta entry for payout
 *
 * @param int    $payout_id
 * @param string $meta_key
 * @param string $meta_value
 * @param bool   $unique
 *
 * @return mixed int|false
 *
 */
function slicewp_add_payout_meta( $payout_id, $meta_key, $meta_value, $unique = false ) {

	return slicewp()->db['payoutmeta']->add( $payout_id, $meta_key, $meta_value, $unique );

}

/**
 * Updates a meta entry for payout
 *
 * @param int    $payout_id
 * @param string $meta_key
 * @param string $meta_value
 * @param bool   $prev_value
 *
 * @return bool
 *
 */
function slicewp_update_payout_meta( $payout_id, $meta_key, $meta_value, $prev_value = '' ) {

	return slicewp()->db['payoutmeta']->update( $payout_id, $meta_key, $meta_value, $prev_value );

}

/**
 * Returns a meta entry for payout
 *
 * @param int    $payout_id
 * @param string $meta_key
 * @param bool   $single
 *
 * @return mixed
 *
 */
function slicewp_get_payout_meta( $payout_id, $meta_key = '', $single = false ) {

	return slicewp()->db['payoutmeta']->get( $payout_id, $meta_key, $single );

}

/**
 * Removes a meta entry for payout
 *
 * @param int    $payout_id
 * @param string $meta_key
 * @param string $meta_value
 * @param bool   $delete_all
 *
 * @return bool
 *
 */
function slicewp_delete_payout_meta( $payout_id, $meta_key, $meta_value = '', $delete_all = '' ) {

	return slicewp()->db['payoutmeta']->delete( $payout_id, $meta_key, $meta_value, $delete_all );

}


/**
 * Returns all available payout methods
 *
 * @return array
 *
 */
function slicewp_get_payout_methods() {

	$payout_methods = array();

	/**
	 * Filter to register more payout methods
	 *
	 * @param array $payout_methods
	 *
	 */
	$payout_methods = apply_filters( 'slicewp_register_payout_methods', $payout_methods );

	return $payout_methods;

}


/**
 * Checks to see whether a payout can performs bulk payments
 *
 * @param int $payout_id
 *
 * @return bool
 *
 */
function slicewp_can_do_bulk_payments( $payout_id ) {

	$args = array(
		'number'	=> -1,
		'payout_id' => $payout_id
	);

	$payments = slicewp_get_payments( $args );

	$payments_count_paid 	   = 0;
	$payments_count_unpaid	   = 0;
	$payments_count_processing = 0;
	$payments_count_failed	   = 0;

	foreach( $payments as $payment ) {

		if( $payment->get( 'status' ) == 'paid' )
			$payments_count_paid++;

		if( $payment->get( 'status' ) == 'unpaid' )
			$payments_count_unpaid++;

		if( $payment->get( 'status' ) == 'processing' )
			$payments_count_processing++;

		if( $payment->get( 'status' ) == 'failed' )
			$payments_count_failed++;

	}

	$return = ( $payments_count_unpaid > 0 || $payments_count_failed > 0 ? true : false );
	$return = ( $payments_count_paid == count( $payments ) || $payments_count_processing != 0 ? false : $return );

	/**
	 * Filter the returned value before returning
	 *
	 * @param bool  $return
	 * @param int   $payout_id
	 * @param array $payments
	 *
	 */
	return apply_filters( 'slicewp_can_do_bulk_payments', $return, $payout_id, $payments );

}