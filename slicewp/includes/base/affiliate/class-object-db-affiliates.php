<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Class that handles database queries for the Affiliate
 *
 */
Class SliceWP_Object_DB_Affiliates extends SliceWP_Object_DB {

	/**
	 * Construct
	 *
	 */
	public function __construct() {

		global $wpdb;

		$this->table_name 		 = $wpdb->prefix . 'slicewp_affiliates';
		$this->primary_key 		 = 'id';
		$this->context 	  		 = 'affiliate';
		$this->query_object_type = 'SliceWP_Affiliate';

	}


	/**
	 * Return the table columns 
	 *
	 */
	public function get_columns() {

		return array(
			'id' 		    => '%d',
			'user_id'		=> '%d',
			'date_created' 	=> '%s',
			'date_modified' => '%s',
			'payment_email'	=> '%s',
			'website'		=> '%s',
			'status'		=> '%s'
		);

	}


	/**
	 * Returns an array of SliceWP_Affiliate objects from the database
	 *
	 * @param array $args
	 * @param bool  $count - whether to return just the count for the query or not
	 *
	 * @return mixed array|int
	 *
	 */
	public function get_affiliates( $args = array(), $count = false ) {

		global $wpdb;

		$defaults = array(
			'number'    		=> 20,
			'offset'    		=> 0,
			'orderby'   		=> 'id',
			'order'     		=> 'DESC',
			'user_id'			=> '',
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
		$args = apply_filters( 'slicewp_get_affiliates_args', $args );

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

		// User ID where clause
		if( ! empty( $args['user_id'] ) ) {

			if( is_array( $args['user_id'] ) ) {

				$user_ids = implode( ',', array_map( 'absint', $args['user_id'] ) );
				$where   .= " AND user_id IN({$user_ids})";

			} else {

				$user_id = absint( $args['user_id'] );
				$where  .= " AND user_id = '{$user_id}'";

			}

		}

		// Include where clause
		if( ! empty( $args['include'] ) ) {

			$include = implode( ',', $args['include'] );
			$where  .= " AND id IN({$include})";

		}

		// Search
		if( ! empty( $args['search'] ) ) {

			$search = sanitize_text_field( $args['search'] );

			$user_ids = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->users} WHERE user_login LIKE %s OR user_email LIKE %s OR display_name LIKE %s", "%{$search}%", "%{$search}%", "%{$search}%" ) );
			$user_ids = ( ! empty( $user_ids ) ? implode( ',', array_map( 'absint', $user_ids ) ) : 0 );

			$where .= " AND user_id IN($user_ids)";

		}
		
		// Include affiliate_id filter in where clause
		if( ! empty( $args['affiliate_id'] )) {

			$affiliate_id =  absint( $args['affiliate_id'] );
			$where  .= " AND affiliate_id = '{$affiliate_id}' ";

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

		$commissions_table = slicewp()->db['commissions']->table_name;
		$visits_table = slicewp()->db['visits']->table_name;

		// Orderby paid commissions
		if( $args['orderby'] == 'commissions_paid' ) {

			$commissions = slicewp()->db['commissions']->table_name;

			$orderby  = "( SELECT COUNT(*) FROM {$commissions_table}";
			$orderby .= " WHERE ( {$this->table_name}.id = {$commissions_table}.affiliate_id";
			$orderby .= " AND {$commissions_table}.status = 'paid' ) )";

		}

		// Orderby unpaid commissions
		if( $args['orderby'] == 'commissions_unpaid' ) {

			$orderby  = "( SELECT COUNT(*) FROM {$commissions_table}";
			$orderby .= " WHERE ( {$this->table_name}.id = {$commissions_table}.affiliate_id";
			$orderby .= " AND {$commissions_table}.status = 'unpaid' ) )";
		
		}

		// Orderby paid earnings
		if( $args['orderby'] == 'earnings_paid' ) {

			$orderby  = "( SELECT SUM({$commissions_table}.amount) FROM {$commissions_table}";
			$orderby .= " WHERE ( {$this->table_name}.id = {$commissions_table}.affiliate_id";
			$orderby .= " AND {$commissions_table}.status = 'paid' ) )";

		}

		// Orderby unpaid earnings
		if( $args['orderby'] == 'earnings_unpaid' ) {

			$orderby  = "( SELECT SUM({$commissions_table}.amount) FROM {$commissions_table}";
			$orderby .= " WHERE ( {$this->table_name}.id = {$commissions_table}.affiliate_id";
			$orderby .= " AND {$commissions_table}.status = 'unpaid' ) )";
		}
		
		// Orderby paid earnings and date
		if( $args['orderby'] == 'earnings_paid_period' ) {

			$orderby  = "( SELECT SUM({$commissions_table}.amount) FROM {$commissions_table}";
			$orderby .= " WHERE ( {$this->table_name}.id = {$commissions_table}.affiliate_id";
			$orderby .= " AND {$commissions_table}.status = 'paid'";
			$orderby .= " AND {$commissions_table}.date_created >= '{$args['commission_date_min']}'";
			$orderby .= " AND {$commissions_table}.date_created <= '{$args['commission_date_max']}' ) )";

		}
				
		// Orderby conversion rate and date
		if( $args['orderby'] == 'conversions_period' ) {

			$orderby  = "( ( SELECT COUNT({$visits_table}.affiliate_id ) FROM {$visits_table}";
			$orderby .= " WHERE ( {$this->table_name}.id = {$visits_table}.affiliate_id";
			$orderby .= " AND {$visits_table}.commission_id != 0";
			$orderby .= " AND {$visits_table}.date_created >= '{$args['commission_date_min']}'";
			$orderby .= " AND {$visits_table}.date_created <= '{$args['commission_date_max']}' ) )";
			$orderby .= " / ";
			$orderby .= "( SELECT COUNT({$visits_table}.affiliate_id ) FROM {$visits_table}";
			$orderby .= " WHERE ( {$this->table_name}.id = {$visits_table}.affiliate_id";
			$orderby .= " AND {$visits_table}.date_created >= '{$args['commission_date_min']}'";
			$orderby .= " AND {$visits_table}.date_created <= '{$args['commission_date_max']}' ) ) )";

		}

		// Order
		$order = ( 'DESC' === strtoupper( $args['order'] ) ? 'DESC' : 'ASC' );

		$clauses = compact( 'where', 'join', 'orderby', 'order', 'count' );

		$results = $this->get_results( $clauses, $args, 'slicewp_get_affiliate' );

		return $results;

	}


	/**
	 * Creates and updates the database table for the affiliates
	 *
	 */
	public function create_table() {

		global $wpdb;

		$table_name 	 = $this->table_name;
		$charset_collate = $wpdb->get_charset_collate();

		$query = "CREATE TABLE {$table_name} (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			user_id bigint(20) NOT NULL,
			date_created datetime NOT NULL,
			date_modified datetime NOT NULL,
			payment_email mediumtext NOT NULL,
			website mediumtext NOT NULL,
			status tinytext NOT NULL,
			PRIMARY KEY  id (id)
		) {$charset_collate};";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $query );

	}

}