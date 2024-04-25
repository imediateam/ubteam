<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Class that handles database queries for the Payment
 *
 */
Class SliceWP_Object_DB_Payments extends SliceWP_Object_DB {

	/**
	 * Construct
	 *
	 */
	public function __construct() {

		global $wpdb;

		$this->table_name 		 = $wpdb->prefix . 'slicewp_payments';
		$this->primary_key 		 = 'id';
		$this->context 	  		 = 'payment';
		$this->query_object_type = 'SliceWP_Payment';

	}


	/**
	 * Return the table columns 
	 *
	 */
	public function get_columns() {

		return array(
			'id' 		        => '%d',
			'affiliate_id'      => '%d',
			'commission_ids' 	=> '%s',
			'amount'	    	=> '%s',
			'currency'			=> '%s',
			'date_created' 	    => '%s',
            'date_modified'     => '%s',
            'admin_id'      	=> '%d',
			'payout_method'		=> '%s',
            'status'            => '%s',
            'payout_id'			=> '%d'
		);

	}


	/**
	 * Returns an array of SliceWP_Payments objects from the database
	 *
	 * @param array $args
	 * @param bool  $count - whether to return just the count for the query or not
	 *
	 * @return mixed array|int
	 *
	 */
	public function get_payments( $args = array(), $count = false ) {

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
		$args = apply_filters( 'slicewp_get_payments_args', $args );

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

		// Include affiliate_id filter in where clause
		if( ! empty( $args['affiliate_id'] )) {

			$affiliate_id =  absint( $args['affiliate_id'] );
			$where .= " AND affiliate_id = '{$affiliate_id}' ";

		}		

		// Include payout filter in where clause
		if( ! empty( $args['payout_id'] )) {

			$payout_id =  absint( $args['payout_id'] );
			$where .= " AND payout_id = '{$payout_id}' ";

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

		// Include where clause
		if( ! empty( $args['include'] ) ) {

			$include = implode( ',', $args['include'] );
			$where  .= " AND id IN({$include})";

		}
		
		// Search
		if( ! empty( $args['search'] ) ) {

			$search = sanitize_text_field( $args['search'] );

			$user_ids   = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->users} WHERE user_login LIKE %s OR user_email LIKE %s OR display_name LIKE %s", "%{$search}%", "%{$search}%", "%{$search}%" ) );
			$affiliates = ( ! empty( $user_ids ) ? slicewp_get_affiliates( array( 'user_id' => $user_ids ) ) : array() );

			$affiliate_ids = array();

			foreach( $affiliates as $affiliate ) {

				$affiliate_ids[] = $affiliate->get('id');

			}

			$affiliate_ids = ( ! empty( $affiliate_ids ) ? implode( ',', array_map( 'absint', $affiliate_ids ) ) : 0 );

			$where .= " AND affiliate_id IN({$affiliate_ids})";

		}

		// Orderby
		$orderby = sanitize_text_field( $args['orderby'] );

		if( $args['orderby'] == 'amount' ) {
			$orderby = 'amount+0';
		}

		// Order
		$order = ( 'DESC' === strtoupper( $args['order'] ) ? 'DESC' : 'ASC' );

		$clauses = compact( 'where', 'join', 'orderby', 'order', 'count');

		$results = $this->get_results( $clauses, $args, 'slicewp_get_payment' );

		return $results;

	}


	/**
	 * Creates and updates the database table for the payments
	 *
	 */
	public function create_table() {

		global $wpdb;

		$table_name 	 = $this->table_name;
		$charset_collate = $wpdb->get_charset_collate();

		$query = "CREATE TABLE {$table_name} (
			id bigint(20) NOT NULL AUTO_INCREMENT,
            affiliate_id bigint(20) NOT NULL,
			commission_ids longtext NOT NULL,
			amount mediumtext NOT NULL,
			currency char(3) NOT NULL,
            admin_id bigint(20) NOT NULL,
			date_created datetime NOT NULL,
            date_modified datetime NOT NULL,
			payout_method tinytext NOT NULL,
			status tinytext NOT NULL,
            payout_id bigint(20) NOT NULL,
			PRIMARY KEY id (id)
		) {$charset_collate};";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $query );

	}

}