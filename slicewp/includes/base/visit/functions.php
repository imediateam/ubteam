<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Includes the files needed for the visits
 *
 */
function slicewp_include_files_visit() {

	// Get visit dir path
	$dir_path = plugin_dir_path( __FILE__ );

	// Include main visit class
	if( file_exists( $dir_path . 'class-visit.php' ) )
		include $dir_path . 'class-visit.php';

	// Include the db layer classes
	if( file_exists( $dir_path . 'class-object-db-visits.php' ) )
		include $dir_path . 'class-object-db-visits.php';

}
add_action( 'slicewp_include_files', 'slicewp_include_files_visit' );


/**
 * Register the class that handles database queries for the visits
 *
 * @param array $classes
 *
 * @return array
 *
 */
function slicewp_register_database_classes_visits( $classes ) {

	$classes['visits'] = 'SliceWP_Object_DB_Visits';
	
	return $classes;

}
add_filter( 'slicewp_register_database_classes', 'slicewp_register_database_classes_visits' );


/**
 * Returns an array with SliceWP_Visit objects from the database
 *
 * @param array $args
 * @param bool  $count
 *
 * @return array
 *
 */
function slicewp_get_visits( $args = array(), $count = false ) {

	$visits = slicewp()->db['visits']->get_visits( $args, $count );

	/**
	 * Add a filter hook just before returning
	 *
	 * @param array $visits
	 * @param array $args
	 * @param bool  $count
	 *
	 */
	return apply_filters( 'slicewp_get_visits', $visits, $args, $count );

}


/**
 * Gets a visit from the database
 *
 * @param mixed int|object      - visit id or object representing the visit
 *
 * @return SliceWP_Visits|false
 *
 */
function slicewp_get_visit( $visit ) {

	return slicewp()->db['visits']->get_object( $visit );

}


/**
 * Inserts a new visit into the database
 *
 * @param array $data
 *
 * @return mixed int|false
 *
 */
function slicewp_insert_visit( $data ) {

	return slicewp()->db['visits']->insert( $data );

}


/**
 * Updates a visit from the database
 *
 * @param int 	$visit_id
 * @param array $data
 *
 * @return bool
 *
 */
function slicewp_update_visit( $visit_id, $data ) {

	return slicewp()->db['visits']->update( $visit_id, $data );

}


/**
 * Deletes a visit from the database
 *
 * @param int $visit_id
 *
 * @return bool
 *
 */
function slicewp_delete_visit( $visit_id ) {

	return slicewp()->db['visits']->delete( $visit_id );

}


/**
 * Removes the affiliate variable from the landing URL
 *
 * @param string $url
 *
 * @return string
 *
 */
function slicewp_sanitize_visit_landing_url( $url ) {

	$affiliate_variable = slicewp_get_setting( 'affiliate_keyword' );

	if( ! empty( $affiliate_variable ) )
		$url = remove_query_arg( $affiliate_variable, $url );

	/**
	 * Filter the URL before returning
	 *
	 * @param string $url
	 *
	 */
	$url = apply_filters( 'slicewp_sanitize_visit_landing_url', $url );

	return $url;

}