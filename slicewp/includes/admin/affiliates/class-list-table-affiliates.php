<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * List table class outputter for Affiliates
 *
 */
Class SliceWP_WP_List_Table_Affiliates extends SliceWP_WP_List_Table {

	/**
	 * The number of affiliates that should appear in the table
	 *
	 * @access private
	 * @var int
	 *
	 */
	private $items_per_page;

	/**
	 * The data of the table
	 *
	 * @access public
	 * @var array
	 *
	 */
	public $data = array();


	/**
	 * Constructor
	 *
	 */
	public function __construct() {

		parent::__construct( array(
			'plural' 	=> 'slicewp_affiliates',
			'singular' 	=> 'slicewp_affiliate',
			'ajax' 		=> false
		));

		$this->items_per_page = 10;
		$this->paged 		  = ( ! empty( $_GET['paged'] ) ? (int)$_GET['paged'] : 1 );

		$this->set_pagination_args( array(
            'total_items' => slicewp_get_affiliates( array( 'number' => -1, 'status' => ( ! empty( $_GET['affiliate_status'] ) ? sanitize_text_field( $_GET['affiliate_status'] ) : '' ), 'search' => ( ! empty( $_GET['s'] ) ? $_GET['s'] : '' ) ), true ),
            'per_page'    => $this->items_per_page
        ));

		// Get and set table data
		$this->set_table_data();
		
		// Add column headers and table items
		$this->_column_headers = array( $this->get_columns(), array(), $this->get_sortable_columns() );
		$this->items 		   = $this->data;

	}


	/**
	 * Returns all the columns for the table
	 *
	 */
	public function get_columns() {

		$columns = array(
			'id' 		    	 => __( 'ID', 'slicewp' ),
			'name'		    	 => __( 'Name', 'slicewp' ),
			'earnings_paid'		 => __( 'Paid Earnings', 'slicewp' ),
			'earnings_unpaid'	 => __( 'Unpaid Earnings', 'slicewp' ),
			'commissions_paid'	 => __( 'Paid Commissions', 'slicewp' ),
			'commissions_unpaid' => __( 'Unpaid Commissions', 'slicewp' ),
			'status'			 => __( 'Status', 'slicewp' ),
			'actions'			 => ''
		);

		/**
		 * Filter the columns of the affiliates table
		 *
		 * @param array $columns
		 *
		 */
		return apply_filters( 'slicewp_list_table_affiliates_columns', $columns );

	}


	/**
	 * Returns all the sortable columns for the table
	 *
	 */
	public function get_sortable_columns() {

		$columns = array(
			'id' 			  	 => array( 'id', false ),
			'earnings_paid'   	 => array( 'earnings_paid', false ),
			'earnings_unpaid'  	 => array( 'earnings_unpaid', false ),
			'commissions_paid' 	 => array( 'commissions_paid', false ),
			'commissions_unpaid' => array( 'commissions_unpaid', false )
		);

		/**
		 * Filter the sortable columns of the affiliates table
		 *
		 * @param array $columns
		 *
		 */
		return apply_filters( 'slicewp_list_table_affiliates_sortable_columns', $columns );

	}


	/**
     * Returns the possible views for the affiliate list table
     *
     */
    protected function get_views() {

    	$statuses = slicewp_get_affiliate_available_statuses();

    	$affiliate_status = ( ! empty( $_GET['affiliate_status'] ) ? sanitize_text_field( $_GET['affiliate_status'] ) : '' );

    	// Set the view for "all" affiliates
    	$views = array(
    		'all' => '<a href="' . add_query_arg( array( 'page' => 'slicewp-affiliates', 'paged' => 1 ), admin_url( 'admin.php' ) ) . '" ' . ( empty( $affiliate_status ) ? 'class="current"' : '' ) . '>' . __( 'All', 'slicewp' ) . ' <span class="count">(' . slicewp_get_affiliates( array( 'search' => ( ! empty( $_GET['s'] ) ? $_GET['s'] : '' ) ), true ) . ')</span></a>'
    	);

    	// Set the views for each affiliate status
    	foreach( $statuses as $status_slug => $status_name ) {
    		$views[$status_slug] = '<a href="' . add_query_arg( array( 'page' => 'slicewp-affiliates', 'affiliate_status' => $status_slug, 'paged' => 1 ), admin_url( 'admin.php' ) ) . '" ' . ( $affiliate_status == $status_slug ? 'class="current"' : '' ) . '>' . $status_name . ' <span class="count">(' . slicewp_get_affiliates( array( 'status' => $status_slug, 'search' => ( ! empty( $_GET['s'] ) ? $_GET['s'] : '' ) ), true ) . ')</span></a>';
    	}

		/**
		 * Filter the views of the affiliates table
		 *
		 * @param array $views
		 *
		 */
		return apply_filters( 'slicewp_list_table_affiliates_views', $views );

    }


	/**
	 * Gets the affiliates data and sets it
	 *
	 */
	private function set_table_data() {

		$affiliate_args = array(
			'number'  => $this->items_per_page,
			'offset'  => ( $this->get_pagenum() - 1 ) * $this->items_per_page,
			'status'  => ( ! empty( $_GET['affiliate_status'] ) ? sanitize_text_field( $_GET['affiliate_status'] ) : '' ),
			'search'  => ( ! empty( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '' ),
			'orderby' => ( ! empty( $_GET['orderby'] ) ? sanitize_text_field( $_GET['orderby'] ) : 'id' ),
			'order'	  => ( ! empty( $_GET['order'] ) ? sanitize_text_field( $_GET['order'] ) : 'desc' )
		);

		$affiliates = slicewp_get_affiliates( $affiliate_args );

		if( empty( $affiliates ) )
			return;

		foreach( $affiliates as $affiliate ) {

			$row_data = $affiliate->to_array();

			/**
			 * Filter the affiliate row data
			 *
			 * @param array 		 $row_data
			 * @param slicewp_Affiliate $affiliate
			 *
			 */
			$row_data = apply_filters( 'slicewp_list_table_affiliates_row_data', $row_data, $affiliate );

			$this->data[] = $row_data;

		}
		
	}


	/**
	 * Returns the HTML that will be displayed in each columns
	 *
	 * @param array $item 			- data for the current row
	 * @param string $column_name 	- name of the current column
	 *
	 * @return string
	 *
	 */
	public function column_default( $item, $column_name ) {

		return isset( $item[ $column_name ] ) ? $item[ $column_name ] : '-';

	}


	/**
	 * Returns the HTML that will be displayed in the "name" column
	 *
	 * @param array $item - data for the current row
	 *
	 * @return string
	 *
	 */
	public function column_name( $item ) {

		/**
		 * Set user display name
		 *
		 */
		$affiliate_name = slicewp_get_affiliate_name( $item['id'] );

		if( null === $affiliate_name )
			$output = __( '(inexistent affiliate)', 'slicewp' );
		else if ( $item['status'] != 'pending' )
			$output = '<a href="' . add_query_arg( array( 'page' => 'slicewp-affiliates', 'subpage' => 'edit-affiliate', 'affiliate_id' => $item['id'] ) , admin_url( 'admin.php' ) ) . '">' . $affiliate_name . '</a>';
		else
			$output = '<a href="' . add_query_arg( array( 'page' => 'slicewp-affiliates', 'subpage' => 'review-affiliate', 'affiliate_id' => $item['id'] ) , admin_url( 'admin.php' ) ) . '">' . $affiliate_name . '</a>';

		return $output;

	}


	/**
	 * Returns the HTML that will be displayed in the "earnings_paid" column
	 *
	 * @param array $item - data for the current row
	 *
	 * @return string
	 *
	 */
	public function column_earnings_paid( $item ) {

		$output = slicewp_get_affiliate_earnings_paid( $item['id'] );

		return $output;

	}


	/**
	 * Returns the HTML that will be displayed in the "earnings_unpaid" column
	 *
	 * @param array $item - data for the current row
	 *
	 * @return string
	 *
	 */
	public function column_earnings_unpaid( $item ) {

		$output = slicewp_get_affiliate_earnings_unpaid( $item['id'] );

		return $output;

	}


	/**
	 * Returns the HTML that will be displayed in the "commissions_paid" column
	 *
	 * @param array $item - data for the current row
	 *
	 * @return string
	 *
	 */
	public function column_commissions_paid( $item ) {

		$output = slicewp_get_commissions( array( 'number' => -1, 'affiliate_id' => $item['id'], 'status' => 'paid' ), true );

		return $output;

	}


	/**
	 * Returns the HTML that will be displayed in the "commissions_unpaid" column
	 *
	 * @param array $item - data for the current row
	 *
	 * @return string
	 *
	 */
	public function column_commissions_unpaid( $item ) {

		$output = slicewp_get_commissions( array( 'number' => -1, 'affiliate_id' => $item['id'], 'status' => 'unpaid' ), true );

		return $output;

	}


	/**
	 * Returns the HTML that will be displayed in the "status" column
	 *
	 * @param array $item - data for the current row
	 *
	 * @return string
	 *
	 */
	public function column_status( $item ) {

		$statuses = slicewp_get_affiliate_available_statuses();

		$output = ( ! empty( $statuses[$item['status']] ) ? '<span class="slicewp-status-pill slicewp-status-' . esc_attr( $item['status'] ) . '">' . $statuses[$item['status']] . '</span>' : '' );

		return $output;

	}
	

	/**
	 * Returns the HTML that will be displayed in the "actions" column
	 *
	 * @param array $item - data for the current row
	 *
	 * @return string
	 *
	 */
	public function column_actions( $item ) {

		/**
		 * Set actions
		 *
		 */
		$status = $item['status'];

		$output  = '<div class="row-actions">';

		if ( $item['status'] != 'pending' )
			$output .= '<a href="' . add_query_arg( array( 'page' => 'slicewp-affiliates', 'subpage' => 'edit-affiliate', 'affiliate_id' => $item['id'] ) , admin_url( 'admin.php' ) ) . '" class="slicewp-button-secondary">' . __( 'Edit', 'slicewp' ) . '</a>';
		else
			$output .= '<a href="' . add_query_arg( array( 'page' => 'slicewp-affiliates', 'subpage' => 'review-affiliate', 'affiliate_id' => $item['id'] ) , admin_url( 'admin.php' ) ) . '" class="slicewp-button-secondary">' . __( 'Review', 'slicewp' ) . '</a>';

			$output .= '<span class="trash"><a onclick="return confirm( \'' . __( "Are you sure you want to delete this affiliate?", "slicewp" ) . ' \' )" href="' . wp_nonce_url( add_query_arg( array( 'page' => 'slicewp-affiliates', 'slicewp_action' => 'delete_affiliate', 'affiliate_id' => $item['id'] ) , admin_url( 'admin.php' ) ), 'slicewp_delete_affiliate', 'slicewp_token' ) . '" class="submitdelete">' . __( 'Delete', 'slicewp' ) . '</a></span>';
		$output .= '</div>';

		return $output;

	}


	/**
	 * HTML display when there are no items in the table
	 *
	 */
	public function no_items() {

		echo __( 'No affiliates found.', 'slicewp' );

	}

}