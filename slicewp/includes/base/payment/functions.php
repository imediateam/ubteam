<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Includes the files needed for the payments
 *
 */
function slicewp_include_files_payment() {

	// Get payment dir path
	$dir_path = plugin_dir_path( __FILE__ );

	// Include main payment class
	if( file_exists( $dir_path . 'class-payment.php' ) )
		include $dir_path . 'class-payment.php';

	// Include the db layer classes
	if( file_exists( $dir_path . 'class-object-db-payments.php' ) )
		include $dir_path . 'class-object-db-payments.php';

	if( file_exists( $dir_path . 'class-object-meta-db-payments.php' ) )
		include $dir_path . 'class-object-meta-db-payments.php';

}
add_action( 'slicewp_include_files', 'slicewp_include_files_payment' );


/**
 * Register the class that handles database queries for the payments
 *
 * @param array $classes
 *
 * @return array
 *
 */
function slicewp_register_database_classes_payments( $classes ) {

	$classes['payments'] = 'SliceWP_Object_DB_Payments';
	$classes['paymentmeta'] = 'SliceWP_Object_Meta_DB_Payments';

	return $classes;

}
add_filter( 'slicewp_register_database_classes', 'slicewp_register_database_classes_payments' );


/**
 * Returns an array with SliceWP_Payments objects from the database
 *
 * @param array $args
 * @param bool  $count
 *
 * @return array
 *
 */
function slicewp_get_payments( $args = array(), $count = false ) {

	$payments = slicewp()->db['payments']->get_payments( $args, $count );

	/**
	 * Add a filter hook just before returning
	 *
	 * @param array $payments
	 * @param array $args
	 * @param bool  $count
	 *
	 */
	return apply_filters( 'slicewp_get_payments', $payments, $args, $count );

}


/**
 * Gets a payment from the database
 *
 * @param mixed int|object      - payment id or object representing the payment
 *
 * @return SliceWP_Payment|false
 *
 */
function slicewp_get_payment( $payment ) {

	return slicewp()->db['payments']->get_object( $payment );

}


/**
 * Inserts a new payment into the database
 *
 * @param array $data
 *
 * @return mixed int|false
 *
 */
function slicewp_insert_payment( $data ) {

	return slicewp()->db['payments']->insert( $data );

}


/**
 * Updates a payment from the database
 *
 * @param int 	$payment_id
 * @param array $data
 *
 * @return bool
 *
 */
function slicewp_update_payment( $payment_id, $data ) {

	return slicewp()->db['payments']->update( $payment_id, $data );

}


/**
 * Deletes a payment from the database
 *
 * @param int $payment_id
 *
 * @return bool
 *
 */
function slicewp_delete_payment( $payment_id ) {

	return slicewp()->db['payments']->delete( $payment_id );

}

/**
 * Inserts a new meta entry for payment
 *
 * @param int    $payment_id
 * @param string $meta_key
 * @param string $meta_value
 * @param bool   $unique
 *
 * @return mixed int|false
 *
 */
function slicewp_add_payment_meta( $payment_id, $meta_key, $meta_value, $unique = false ) {

	return slicewp()->db['paymentmeta']->add( $payment_id, $meta_key, $meta_value, $unique );

}

/**
 * Updates a meta entry for payment
 *
 * @param int    $payment_id
 * @param string $meta_key
 * @param string $meta_value
 * @param bool   $prev_value
 *
 * @return bool
 *
 */
function slicewp_update_payment_meta( $payment_id, $meta_key, $meta_value, $prev_value = '' ) {

	return slicewp()->db['paymentmeta']->update( $payment_id, $meta_key, $meta_value, $prev_value );

}

/**
 * Returns a meta entry for payment
 *
 * @param int    $payment_id
 * @param string $meta_key
 * @param bool   $single
 *
 * @return mixed
 *
 */
function slicewp_get_payment_meta( $payment_id, $meta_key = '', $single = false ) {

	return slicewp()->db['paymentmeta']->get( $payment_id, $meta_key, $single );

}

/**
 * Removes a meta entry for payment
 *
 * @param int    $payment_id
 * @param string $meta_key
 * @param string $meta_value
 * @param bool   $delete_all
 *
 * @return bool
 *
 */
function slicewp_delete_payment_meta( $payment_id, $meta_key, $meta_value = '', $delete_all = '' ) {

	return slicewp()->db['paymentmeta']->delete( $payment_id, $meta_key, $meta_value, $delete_all );

}

/**
 * Returns an array with the possible statuses the Payment can have
 *
 * @return array
 *
 */
function slicewp_get_payment_available_statuses() {

	$statuses = array(
		'paid'       => __( 'Paid', 'slicewp' ),
		'unpaid'     => __( 'Unpaid', 'slicewp' ),
		'processing' => __( 'Processing', 'slicewp' ),
		'failed'     => __( 'Failed', 'slicewp' )
	);

	/**
	 * Filter the available statuses just before returning
	 *
	 * @param array $statuses
	 *
	 */
	$statuses = apply_filters( 'slicewp_payment_available_statuses', $statuses );

	return $statuses;

}