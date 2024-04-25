<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Class that handles database queries for the Note
 *
 */
Class SliceWP_Object_DB_Notes extends SliceWP_Object_DB {

	/**
	 * Construct
	 *
	 */
	public function __construct() {

		global $wpdb;

		$this->table_name 		 = $wpdb->prefix . 'slicewp_notes';
		$this->primary_key 		 = 'id';
		$this->context 	  		 = 'note';
		$this->query_object_type = 'SliceWP_Note';

	}


	/**
	 * Return the table columns 
	 *
	 */
	public function get_columns() {

		return array(
			'id' 		     => '%d',
			'object_context' => '%s',
			'object_id'		 => '%d',
			'user_id'		 => '%d',
			'date_created' 	 => '%s',
			'note_content'   => '%s'
		);

	}


	/**
	 * Returns an array of SliceWP_Note objects from the database
	 *
	 * @param array $args
	 * @param bool  $count - whether to return just the count for the query or not
	 *
	 * @return mixed array|int
	 *
	 */
	public function get_notes( $args = array(), $count = false ) {

		global $wpdb;

		$defaults = array(
			'number'    		=> 20,
			'offset'    		=> 0,
			'orderby'   		=> 'id',
			'order'     		=> 'DESC',
			'include'   		=> array(),
			'search'			=> ''
		);

		$args = wp_parse_args( $args, $defaults );

		/**
		 * Filter the query arguments just before making the db call
		 *
		 * @param array $args
		 *
		 */
		$args = apply_filters( 'slicewp_get_notes_args', $args );

		// Number args
		if( $args['number'] < 1 )
			$args['number'] = 999999;

		// Join clause
		$join = '';

		// Where clause
		$where = "WHERE 1=1";

		// Include where clause
		if( ! empty( $args['include'] ) ) {

			$include = implode( ',', $args['include'] );
			$where  .= " AND id IN({$include})";

		}

		// Include object_context filter in where clause
		if( ! empty( $args['object_context'] )) {

			$object_context =  sanitize_text_field( $args['object_context'] );
			$where    	   .= " AND object_context = '{$object_context}' ";

		}
		
		// Include object_id filter in where clause
		if( isset( $args['object_id'] )) {

			$object_id =  absint( $args['object_id'] );
			$where    .= " AND object_id = '{$object_id}' ";

		}

		// Include user_id filter in where clause
		if( isset( $args['user_id'] )) {

			$user_id =  absint( $args['user_id'] );
			$where  .= " AND user_id = '{$user_id}' ";

		}

		// Include date_min filter in where clause
		if( ! empty( $args['date_min'] )) {

			$date_min =  sanitize_text_field( $args['date_min'] );
			$where  .= " AND date_created >= '{$date_min}' ";

		}

		// Include date_max filter in where clause
		if( ! empty( $args['date_max'] )) {

			$date_max =  sanitize_text_field( $args['date_max'] );
			$where  .= " AND date_created <= '{$date_max}' ";

		}
		
		// Default orderby
		$orderby = sanitize_text_field( $args['orderby'] );

		// Order
		$order = ( 'DESC' === strtoupper( $args['order'] ) ? 'DESC' : 'ASC' );

		$clauses = compact( 'where', 'join', 'orderby', 'order', 'count' );

		$results = $this->get_results( $clauses, $args, 'slicewp_get_note' );

		return $results;

	}


	/**
	 * Creates and updates the database table for the notes
	 *
	 */
	public function create_table() {

		global $wpdb;

		$table_name 	 = $this->table_name;
		$charset_collate = $wpdb->get_charset_collate();

		$query = "CREATE TABLE {$table_name} (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			object_context mediumtext NOT NULL,
			object_id bigint(20),
			user_id bigint(20) NOT NULL,
			date_created datetime NOT NULL,
			note_content longtext NOT NULL,
			PRIMARY KEY  id (id)
		) {$charset_collate};";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $query );

	}

}