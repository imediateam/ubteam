<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Class that handles database queries for the Creatives
 *
 */
Class SliceWP_Object_DB_Creatives extends SliceWP_Object_DB {

	/**
	 * Construct
	 *
	 */
	public function __construct() {

		global $wpdb;

		$this->table_name 		 = $wpdb->prefix . 'slicewp_creatives';
		$this->primary_key 		 = 'id';
		$this->context 	  		 = 'creative';
		$this->query_object_type = 'SliceWP_Creative';

	}


	/**
	 * Return the table columns 
	 *
	 */
	public function get_columns() {

		return array(
			'id' 		    => '%d',
			'name'	        => '%s',
			'description'	=> '%s',
			'date_created' 	=> '%s',
			'date_modified' => '%s',
			'type'			=> '%s',
			'image_url'		=> '%s',
			'alt_text'		=> '%s',
			'text'	    	=> '%s',
			'landing_url'	=> '%s',
			'status'		=> '%s'
		);

	}


	/**
	 * Returns an array of SliceWP_Creatives objects from the database
	 *
	 * @param array $args
	 * @param bool  $count - whether to return just the count for the query or not
	 *
	 * @return mixed array|int
	 *
	 */
	public function get_creatives( $args = array(), $count = false ) {

		$defaults = array(
			'number'    		=> 20,
			'offset'    		=> 0,
			'orderby'   		=> 'id',
			'order'     		=> 'DESC',
			'include'   		=> array()
		);

		$args = wp_parse_args( $args, $defaults );

		/**
		 * Filter the query arguments just before making the db call
		 *
		 * @param array $args
		 *
		 */
		$args = apply_filters( 'slicewp_get_creatives_args', $args );

		// Number args
		if( $args['number'] < 1 )
			$args['number'] = 999999;

		// Join clause
		$join = '';

		// Where clause
		$where = "WHERE 1=1";

		// Status where clause
		if( ! empty( $args['status'] ) ) {

			$status = sanitize_text_field( $args['status'] );
			$where .= " AND status = '{$status}'";

		}


		// Include where clause
		if( ! empty( $args['include'] ) ) {

			$include = implode( ',', $args['include'] );
			$where  .= " AND id IN({$include})";

		}


		// Include search
		if( ! empty( $args['search'] ) ) {

			$search = sanitize_text_field( $args['search'] );
			$where  .= " AND (name LIKE '%%{$search}%%' OR description LIKE '%%{$search}%%' OR text LIKE '%%{$search}%%' OR landing_url LIKE '%%{$search}%%' OR alt_text LIKE '%%{$search}%%')";

		}

		// Orderby
		$orderby = sanitize_text_field( $args['orderby'] );

		// Order
		$order = ( 'DESC' === strtoupper( $args['order'] ) ? 'DESC' : 'ASC' );

		$clauses = compact( 'where', 'join', 'orderby', 'order', 'count' );

		$results = $this->get_results( $clauses, $args, 'slicewp_get_creative' );

		return $results;

	}


	/**
	 * Creates and updates the database table for the creatives
	 *
	 */
	public function create_table() {

		global $wpdb;

		$table_name 	 = $this->table_name;
		$charset_collate = $wpdb->get_charset_collate();

		$query = "CREATE TABLE {$table_name} (
			id bigint(20) NOT NULL AUTO_INCREMENT,
            name text NOT NULL,
            description longtext NOT NULL,
			date_created datetime NOT NULL,
			date_modified datetime NOT NULL,
			type tinytext NOT NULL,
            image_url text NOT NULL,
			alt_text text NOT NULL,
			text text NOT NULL,
			landing_url text NOT NULL,
            status tinytext NOT NULL,
			PRIMARY KEY id (id)
		) {$charset_collate};";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $query );

	}

}