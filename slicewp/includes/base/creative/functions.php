<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Includes the files needed for the creatives
 *
 */
function slicewp_include_files_creative() {

	// Get creative dir path
	$dir_path = plugin_dir_path( __FILE__ );

	// Include main creative class
	if( file_exists( $dir_path . 'class-creative.php' ) )
		include $dir_path . 'class-creative.php';

	// Include the db layer classes
	if( file_exists( $dir_path . 'class-object-db-creatives.php' ) )
		include $dir_path . 'class-object-db-creatives.php';

}
add_action( 'slicewp_include_files', 'slicewp_include_files_creative' );


/**
 * Register the class that handles database queries for the creatives
 *
 * @param array $classes
 *
 * @return array
 *
 */
function slicewp_register_database_classes_creatives( $classes ) {

	$classes['creatives'] = 'SliceWP_Object_DB_Creatives';
	
	return $classes;

}
add_filter( 'slicewp_register_database_classes', 'slicewp_register_database_classes_creatives' );


/**
 * Returns an array with SliceWP_Creative objects from the database
 *
 * @param array $args
 * @param bool  $count
 *
 * @return array
 *
 */
function slicewp_get_creatives( $args = array(), $count = false ) {

	$creatives = slicewp()->db['creatives']->get_creatives( $args, $count );

	/**
	 * Add a filter hook just before returning
	 *
	 * @param array $creatives
	 * @param array $args
	 * @param bool  $count
	 *
	 */
	return apply_filters( 'slicewp_get_creatives', $creatives, $args, $count );

}


/**
 * Gets a creative from the database
 *
 * @param mixed int|object      - creative id or object representing the creative
 *
 * @return SliceWP_Creative|false
 *
 */
function slicewp_get_creative( $creative ) {

	return slicewp()->db['creatives']->get_object( $creative );

}


/**
 * Inserts a new creative into the database
 *
 * @param array $data
 *
 * @return mixed int|false
 *
 */
function slicewp_insert_creative( $data ) {

	return slicewp()->db['creatives']->insert( $data );

}

/**
 * Updates a creative from the database
 *
 * @param int 	$creative_id
 * @param array $data
 *
 * @return bool
 *
 */
function slicewp_update_creative( $creative_id, $data ) {

	return slicewp()->db['creatives']->update( $creative_id, $data );

}

/**
 * Deletes a creative from the database
 *
 * @param int $creative_id
 *
 * @return bool
 *
 */
function slicewp_delete_creative( $creative_id ) {

	return slicewp()->db['creatives']->delete( $creative_id );

}


/**
 * Returns an array with the possible statuses the Creative can have
 *
 * @return array
 *
 */
function slicewp_get_creative_available_statuses() {

	$statuses = array(
		'active'   => __( 'Active', 'slicewp' ),
		'inactive' => __( 'Inactive', 'slicewp' )
	);

	/**
	 * Filter the available statuses just before returning
	 *
	 * @param array $statuses
	 *
	 */
	$statuses = apply_filters( 'slicewp_creative_available_statuses', $statuses );

	return $statuses;

}

/**
 * Returns an array with the possible statuses the Creative can have
 *
 * @return array
 *
 */
function slicewp_get_creative_available_types() {

	$types = array(
		'image'		=> __( 'Image', 'slicewp' ),
		'text' 		=> __( 'Text', 'slicewp' ),
		'long_text' => __( 'Long Text', 'slicewp' )
	);

	/**
	 * Filter the available types just before returning
	 *
	 * @param array $types
	 *
	 */
	$types = apply_filters( 'slicewp_creative_available_types', $types );

	return $types;

}