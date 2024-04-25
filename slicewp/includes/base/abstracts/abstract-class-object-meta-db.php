<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Base class for all core database objects.
 *
 */
abstract class SliceWP_Object_Meta_DB extends SliceWP_DB {

	/**
	 * The temporary value of the $wpdb table name property if a table name switch is needed
	 *
	 * @access protected
	 * @var    null|string
	 *
	 */
	protected $temp_wpdb_table_name = null;


	/**
	 * Constructor
	 *
	 * Subclasses should set the $table_name, $primary_key, $context
	 *
	 * @access public
	 *
	 */
	public function __construct() {

		add_action( 'plugins_loaded', array( $this, 'register_wpdb_table' ) );

	}


	/**
	 * Register the meta table with the $wpdb global
	 *
	 */
	public function register_wpdb_table() {

		global $wpdb;

		$wpdb->{'slicewp_' . $this->context . 'meta'} = $this->table_name;

	}


	/**
	 * Inserts a new meta entry for the object
	 *
	 * @param int    $object_id
	 * @param string $meta_key
	 * @param string $meta_value
	 * @param bool   $unique
	 *
	 * @return mixed int|false
	 *
	 */
	public function add( $object_id, $meta_key, $meta_value, $unique = false ) {

		$return = add_metadata( 'slicewp_' . $this->context, $object_id, $meta_key, $meta_value, $unique );

		return $return;

	}


	/**
	 * Updates a meta entry for the object
	 *
	 * @param int    $object_id
	 * @param string $meta_key
	 * @param string $meta_value
	 * @param bool   $prev_value
	 *
	 * @return bool
	 *
	 */
	public function update( $object_id, $meta_key, $meta_value, $prev_value = '' ) {

		$return = update_metadata( 'slicewp_' . $this->context, $object_id, $meta_key, $meta_value, $prev_value );

		return $return;

	}


	/**
	 * Returns a meta entry for the object
	 *
	 * @param int    $object_id
	 * @param string $meta_key
	 * @param bool   $single
	 *
	 * @return mixed
	 *
	 */
	public function get( $object_id, $meta_key = '', $single = false ) {

		$return = get_metadata( 'slicewp_' . $this->context, $object_id, $meta_key, $single );

		return $return;

	}


	/**
	 * Removes a meta entry for the object
	 *
	 * @param int    $object_id
	 * @param string $meta_key
	 * @param string $meta_value
	 * @param bool   $delete_all
	 *
	 * @return bool
	 *
	 */
	public function delete( $object_id, $meta_key, $meta_value = '', $delete_all = '' ) {

		$return = delete_metadata( 'slicewp_' . $this->context, $object_id, $meta_key, $meta_value, $delete_all );

		return $return;

	}

}