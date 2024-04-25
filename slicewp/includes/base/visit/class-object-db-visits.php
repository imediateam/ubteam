<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Class that handles database queries for the Visits
 *
 */
Class SliceWP_Object_DB_Visits extends SliceWP_Object_DB {

	/**
	 * Construct
	 *
	 */
	public function __construct() {

		global $wpdb;

		$this->table_name 		 = $wpdb->prefix . 'slicewp_visits';
		$this->primary_key 		 = 'id';
		$this->context 	  		 = 'visit';
		$this->query_object_type = 'SliceWP_Visit';

	}


	/**
	 * Return the table columns 
	 *
	 */
	public function get_columns() {

		return array(
			'id' 		    => '%d',
			'affiliate_id'  => '%d',
			'date_created' 	=> '%s',
			'ip_address'	=> '%s',
			'landing_url'	=> '%s',
			'referrer_url'	=> '%s',
			'commission_id' => '%d'
		);

	}


	/**
	 * Returns an array of SliceWP_Visits objects from the database
	 *
	 * @param array $args
	 * @param bool  $count - whether to return just the count for the query or not
	 *
	 * @return mixed array|int
	 *
	 */
	public function get_visits( $args = array(), $count = false ) {

		global $wpdb;
		
		$defaults = array(
			'number'    	=> 20,
			'offset'    	=> 0,
			'orderby'   	=> 'id',
			'order'     	=> 'DESC',
			'include'   	=> array(),
			'commision_id'	=> '',
			'search'		=> '',
			'converted'		=> ''
		);

		$args = wp_parse_args( $args, $defaults );

		/**
		 * Filter the query arguments just before making the db call
		 *
		 * @param array $args
		 *
		 */
		$args = apply_filters( 'slicewp_get_visits_args', $args );

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

		// Include affiliate_id filter in where clause
		if( ! empty( $args['affiliate_id'] )) {

			$affiliate_id =  absint( $args['affiliate_id'] );
			$where  .= " AND affiliate_id = '{$affiliate_id}' ";

		}		

		// Include date_min filter in where clause
		if( ! empty( $args['date_min'] ) ) {

			$date_min =  sanitize_text_field( $args['date_min']);
			$where  .= " AND date_created >= '{$date_min}' ";

		}

		// Include date_max filter in where clause
		if( ! empty( $args['date_max'] ) ) {

			$date_max =  sanitize_text_field( $args['date_max']);
			$where  .= " AND date_created <= '{$date_max}' ";

		}

		// Include Converted filter in where clause
		if ( $args['converted'] !== '' ) {

			if( $args['converted'] === true ) {

				$where  .= " AND commission_id != 0 ";

			}

			if( $args['converted'] === false ) {
				
				$where  .= " AND commission_id = 0 ";

			}

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

			$where  .= " AND (affiliate_id IN({$affiliate_ids}) OR landing_url LIKE '%%{$search}%%' OR referrer_url LIKE '%%{$search}%%')";

		}

		// Orderby
		$orderby = sanitize_text_field( $args['orderby'] );

		// Order
		$order = ( 'DESC' === strtoupper( $args['order'] ) ? 'DESC' : 'ASC' );

		$clauses = compact( 'where', 'join', 'orderby', 'order', 'count');

		$results = $this->get_results( $clauses, $args, 'slicewp_get_visit' );

		return $results;

	}


	/**
	 * Creates and updates the database table for the visits
	 *
	 */
	public function create_table() {

		global $wpdb;

		$table_name 	 = $this->table_name;
		$charset_collate = $wpdb->get_charset_collate();

		$query = "CREATE TABLE {$table_name} (
			id bigint(20) NOT NULL AUTO_INCREMENT,
            affiliate_id bigint(20) NOT NULL,
			date_created datetime NOT NULL,
			ip_address mediumtext NOT NULL,
			landing_url mediumtext NOT NULL,
			referrer_url mediumtext NOT NULL,
			commission_id bigint(20) NOT NULL,
			PRIMARY KEY id (id)
		) {$charset_collate};";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $query );

	}

}