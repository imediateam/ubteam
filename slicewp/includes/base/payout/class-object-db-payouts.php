<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Class that handles database queries for the Payout
 *
 */
Class SliceWP_Object_DB_Payouts extends SliceWP_Object_DB {

	/**
	 * Construct
	 *
	 */
	public function __construct() {

		global $wpdb;

		$this->table_name 		 = $wpdb->prefix . 'slicewp_payouts';
		$this->primary_key 		 = 'id';
		$this->context 	  		 = 'payout';
		$this->query_object_type = 'SliceWP_Payout';

	}


	/**
	 * Return the table columns 
	 *
	 */
	public function get_columns() {

		return array(
			'id' 		    => '%d',
            'admin_id'    	=> '%d',
			'date_created'	=> '%s',
			'date_modified'	=> '%s',
			'amount'		=> '%s'
		);

	}


	/**
	 * Returns an array of SliceWP_Payout objects from the database
	 *
	 * @param array $args
	 * @param bool  $count - whether to return just the count for the query or not
	 *
	 * @return mixed array|int
	 *
	 */
	public function get_payouts( $args = array(), $count = false ) {

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
		$args = apply_filters( 'slicewp_get_payouts_args', $args );

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

		// Include date_min filter in where clause
		if( ! empty( $args['date_min'] )) {

			$date_min =  sanitize_text_field( $args['date_min']);
			$where  .= " AND date_created >= '{$date_min}' ";

		}

		// Include date_max filter in where clause
		if( ! empty( $args['date_max'] )) {

			$date_max =  sanitize_text_field( $args['date_max']);
			$where  .= " AND date_created <= '{$date_max}' ";

		}

		// Orderby
		$orderby = sanitize_text_field( $args['orderby'] );
		
		if( $args['orderby'] == 'amount' ) {
			$orderby = 'amount+0';
		}
		
		// Order
		$order = ( 'DESC' === strtoupper( $args['order'] ) ? 'DESC' : 'ASC' );

		$clauses = compact( 'where', 'join', 'orderby', 'order', 'count');

		$results = $this->get_results( $clauses, $args, 'slicewp_get_payout' );

		return $results;

	}


	/**
	 * Creates and updates the database table for the payout
	 *
	 */
	public function create_table() {

		global $wpdb;

		$table_name 	 = $this->table_name;
		$charset_collate = $wpdb->get_charset_collate();

		$query = "CREATE TABLE {$table_name} (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			date_created datetime NOT NULL,
            date_modified datetime NOT NULL,
            admin_id bigint(20) NOT NULL,
			amount mediumtext NOT NULL,
			PRIMARY KEY id (id)
		) {$charset_collate};";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $query );

	}

}