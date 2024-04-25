<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Includes the files needed for the commissions
 *
 */
function slicewp_include_files_commission() {

	// Get commission dir path
	$dir_path = plugin_dir_path( __FILE__ );

	// Include main commission class
	if( file_exists( $dir_path . 'class-commission.php' ) )
		include $dir_path . 'class-commission.php';

	// Include the db layer classes
	if( file_exists( $dir_path . 'class-object-db-commissions.php' ) )
		include $dir_path . 'class-object-db-commissions.php';

	if( file_exists( $dir_path . 'class-object-meta-db-commissions.php' ) )
		include $dir_path . 'class-object-meta-db-commissions.php';

}
add_action( 'slicewp_include_files', 'slicewp_include_files_commission' );


/**
 * Register the class that handles database queries for the commissions
 *
 * @param array $classes
 *
 * @return array
 *
 */
function slicewp_register_database_classes_commissions( $classes ) {

	$classes['commissions']    = 'SliceWP_Object_DB_Commissions';
	$classes['commissionmeta'] = 'SliceWP_Object_Meta_DB_Commissions';

	return $classes;

}
add_filter( 'slicewp_register_database_classes', 'slicewp_register_database_classes_commissions' );


/**
 * Returns an array with SliceWP_Commission objects from the database
 *
 * @param array $args
 * @param bool  $count
 *
 * @return array
 *
 */
function slicewp_get_commissions( $args = array(), $count = false ) {

	$commissions = slicewp()->db['commissions']->get_commissions( $args, $count );

	/**
	 * Add a filter hook just before returning
	 *
	 * @param array $commissions
	 * @param array $args
	 * @param bool  $count
	 *
	 */
	return apply_filters( 'slicewp_get_commissions', $commissions, $args, $count );

}


/**
 * Gets a commission from the database
 *
 * @param mixed int|object      - commission id or object representing the commission
 *
 * @return SliceWP_Commission|null
 *
 */
function slicewp_get_commission( $commission ) {

	return slicewp()->db['commissions']->get_object( $commission );

}

/**
 * Returns an array with SliceWP_Commission objects from the database
 *
 * @param string $column
 * @param array $args
 * @param bool  $count
 *
 * @return array
 *
 */
function slicewp_get_commissions_column( $column, $args = array(), $count = false ) {

	$column = slicewp()->db['commissions']->get_commissions_column( $column, $args, $count );
	
	/**
	 * Add a filter hook just before returning
	 *
	 * @param array $commissions
	 * @param array $args
	 * @param bool  $count
	 *
	 */
	return apply_filters( 'slicewp_get_commissions_column', $column, $args, $count );

}

/**
 * Inserts a new commission into the database
 *
 * @param array $data
 *
 * @return mixed int|false
 *
 */
function slicewp_insert_commission( $data ) {

	return slicewp()->db['commissions']->insert( $data );

}

/**
 * Updates a commission from the database
 *
 * @param int 	$commission_id
 * @param array $data
 *
 * @return bool
 *
 */
function slicewp_update_commission( $commission_id, $data ) {

	return slicewp()->db['commissions']->update( $commission_id, $data );

}

/**
 * Deletes a commission from the database
 *
 * @param int $commission_id
 *
 * @return bool
 *
 */
function slicewp_delete_commission( $commission_id ) {

	return slicewp()->db['commissions']->delete( $commission_id );

}

/**
 * Inserts a new meta entry for the commission
 *
 * @param int    $commission_id
 * @param string $meta_key
 * @param string $meta_value
 * @param bool   $unique
 *
 * @return mixed int|false
 *
 */
function slicewp_add_commission_meta( $commission_id, $meta_key, $meta_value, $unique = false ) {

	return slicewp()->db['commissionmeta']->add( $commission_id, $meta_key, $meta_value, $unique );

}

/**
 * Updates a meta entry for the commission
 *
 * @param int    $commission_id
 * @param string $meta_key
 * @param string $meta_value
 * @param bool   $prev_value
 *
 * @return bool
 *
 */
function slicewp_update_commission_meta( $commission_id, $meta_key, $meta_value, $prev_value = '' ) {

	return slicewp()->db['commissionmeta']->update( $commission_id, $meta_key, $meta_value, $prev_value );

}

/**
 * Returns a meta entry for the commission
 *
 * @param int    $commission_id
 * @param string $meta_key
 * @param bool   $single
 *
 * @return mixed
 *
 */
function slicewp_get_commission_meta( $commission_id, $meta_key = '', $single = false ) {

	return slicewp()->db['commissionmeta']->get( $commission_id, $meta_key, $single );

}

/**
 * Removes a meta entry for the commission
 *
 * @param int    $commission_id
 * @param string $meta_key
 * @param string $meta_value
 * @param bool   $delete_all
 *
 * @return bool
 *
 */
function slicewp_delete_commission_meta( $commission_id, $meta_key, $meta_value = '', $delete_all = '' ) {

	return slicewp()->db['commissionmeta']->delete( $commission_id, $meta_key, $meta_value, $delete_all );

}


/**
 * Returns an array with the possible statuses the Commission can have
 *
 * @return array
 *
 */
function slicewp_get_commission_available_statuses() {

	$statuses = array(
		'paid'     => __( 'Paid', 'slicewp' ),
		'unpaid'   => __( 'Unpaid', 'slicewp' ),
		'pending'  => __( 'Pending', 'slicewp' ),
		'rejected' => __( 'Rejected', 'slicewp' )
	);

	/**
	 * Filter the available statuses just before returning
	 *
	 * @param array $statuses
	 *
	 */
	$statuses = apply_filters( 'slicewp_commission_available_statuses', $statuses );

	return $statuses;

}


/**
 * Returns all available commission types
 *
 * @return array
 *
 */
function slicewp_get_commission_types() {

	$commission_types = array(
		'sale' => array(
			'label' 	 => __( 'Sale', 'slicewp' ),
			'rate_types' => array( 'percentage', 'fixed_amount' )
		),
		'subscription' => array(
			'label'		 => __( 'Subscription' , 'slicewp'),
			'rate_types' => array( 'percentage', 'fixed_amount' )
		),
		'user_signup' => array(
			'label'		 => __( 'User Signup', 'slicewp' ),
			'rate_types' => array( 'fixed_amount' )
		),
		'email_subscribe' => array(
			'label'		 => __( 'Email Subscribe', 'slicewp' ),
			'rate_types' => array( 'fixed_amount' )
		)
	);

	/**
	 * Filter to register more commission types
	 *
	 * @param array $commission_date_types
	 *
	 */
	$commission_types = apply_filters( 'slicewp_register_commission_types', $commission_types );

	return $commission_types;

}


/**
 * Returns the rate types supported by the given commission type
 *
 * @param string $commission_type
 *
 * @return array
 *
 */
function slicewp_get_commission_type_rate_types( $commission_type ) {

	$rate_types = array(
		'percentage'   => __( 'Percentage (%)', 'slicewp' ),
		'fixed_amount' => __( 'Fixed amount', 'slicewp' ) . ' (' . esc_attr( slicewp_get_currency_symbol( slicewp_get_setting( 'active_currency', 'USD' ) ) ) . ')'
	);

	$commission_types = slicewp_get_commission_types();

	// Return empty string if the commission type doesn't support rate types
	if( empty( $commission_types[$commission_type]['rate_types'] ) )
		return array();
	
	// Return only rate types that match keys
	return array_intersect_key( $rate_types, array_flip( $commission_types[$commission_type]['rate_types'] ) );
	
}


/**
 * Returns the commision types that are available, based on what integration is available or active
 *
 * @param bool   $only_active  - whether to return only for integrations that are enabled/active or for all existing integrations
 * @param string $default_type - if the returned value ends up being empty, add a default so that something is returned
 *
 * @return array
 *
 */
function slicewp_get_available_commission_types( $only_active = false, $default_type = 'sale' ) {

	$commission_types 		 	= slicewp_get_commission_types();
	$available_commistion_types = array();

	// Go through each integration and check the supports array for commission rate types
	foreach( slicewp()->integrations as $integration_slug => $integration ) {

		$supports = $integration->get( 'supports' );

		if( empty( $supports['commission_types'] ) || ! is_array( $supports['commission_types'] ) )
			continue;

		if( $only_active && ! slicewp_is_integration_active( $integration_slug ) )
			continue;

		// Go through each commission rate types from the integration's supports array
		foreach( $supports['commission_types'] as $type_slug ) {

			if( ! array_key_exists( $type_slug, $commission_types ) )
				continue;

			$available_commistion_types[$type_slug] = $commission_types[$type_slug];

		}

	}

	// Set default if nothing is active
	if( empty( $available_commistion_types ) && ! empty( $default_type ) && ! empty( $commission_types[$default_type] ) ) {

		$available_commistion_types[$default_type] = $commission_types[$default_type];

	}

	return $available_commistion_types;

}


/**
 * Checks to see if the commissions are set to be calculated on a per order basis
 *
 * @return bool
 *
 */
function slicewp_is_commission_basis_per_order() {

	$return = false;

	if( 'fixed_amount' == slicewp_get_setting( 'commission_rate_type_sale' ) ) {

		if( 'order' == slicewp_get_setting( 'commission_fixed_amount_rate_basis' ) )
			$return = true;

	}

	/**
	 * Filters the value just before returning it
	 *
	 * @param bool $return
	 *
	 */
	$return = apply_filters( 'slicewp_is_commission_basis_per_order', $return );

	return $return;

}