<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * List table class outputter for Payments
 *
 */
Class SliceWP_WP_List_Table_Payment_Commissions extends SliceWP_WP_List_Table {

	/**
	 * The number of commissions that should appear in the table
	 *
	 * @access private
	 * @var    int
	 *
	 */
	private $items_per_page;

	/**
	 * The current payment
	 *
	 * @access private
	 * @var    int
	 *
	 */
	private $payment;

	/**
	 * The data of the table
	 *
	 * @access public
	 * @var    array
	 *
	 */
	public $data = array();


	/**
	 * Constructor
	 *
	 */
	public function __construct() {

		parent::__construct( array(
			'plural' 	=> 'slicewp_payment_commissions',
			'singular' 	=> 'slicewp_payment_commission',
			'ajax' 		=> false
		));

		$this->items_per_page = 20;
		$this->paged 		  = ( ! empty( $_GET['paged'] ) ? (int)$_GET['paged'] : 1 );

		//Get the Payment data
		$payment_id = sanitize_text_field( $_GET['payment_id'] );
		$payment 	= slicewp_get_payment( $payment_id );

		$this->payment = $payment;
		
		// Get the Commission IDs
		if ( ! empty ( $payment->get('commission_ids') ) ) {

			$commission_ids = array_map( 'trim', explode( ',', $payment->get('commission_ids') ) );
			
			$this->set_pagination_args( array(
				'total_items' => slicewp_get_commissions( array( 'number' => -1, 'include' => $commission_ids ), true ),
				'per_page'    => $this->items_per_page
			));
		
		}
		
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
			'id'            => __( 'ID', 'slicewp' ),
			'amount'		=> __( 'Amount', 'slicewp' ),
			'reference'     => __( 'Reference', 'slicewp' ),
            'date_created'  => __( 'Date', 'slicewp' ),            
			'status'		=> __( 'Status', 'slicewp' ),
			'actions'		=> ''
		);

		/**
		 * Filter the columns of the payments table
		 *
		 * @param array $columns
		 *
		 */
		return apply_filters( 'slicewp_list_table_payment_commissions_columns', $columns );

	}

	/**
	 * Returns all the sortable columns for the table
	 *
	 */
	public function get_sortable_columns() {

		$columns = array(
			'amount'			=> array( 'amount', false ),
			'status'			=> array( 'status', false )
        );

		/**
		 * Filter the sortable columns of the visits table
		 *
		 * @param array $columns
		 *
		 */
		return apply_filters( 'slicewp_list_table_payment_commissions_sortable_columns', $columns );

	}


	/**
	 * Gets the commissions data and sets it
	 *
	 */
	private function set_table_data() {

		// Get the Payment data
		$payment_id = sanitize_text_field( $_GET['payment_id'] );
		$payment 	= slicewp_get_payment( $payment_id );
		
		// Get the Commission IDs
		if ( empty ( $payment->get('commission_ids') ) )
			return;
		
		$commission_ids = array_map( 'trim', explode( ',', $payment->get('commission_ids') ) );
					
		$commission_args = array(
			'number'	=> $this->items_per_page,
			'offset'	=> ( $this->get_pagenum() - 1 ) * $this->items_per_page,
            'include'	=> $commission_ids,
			'orderby'	=> ( ! empty( $_GET['orderby'] ) ? sanitize_text_field( $_GET['orderby'] ) : 'id' ),
			'order'		=> ( ! empty( $_GET['order'] ) ? sanitize_text_field( $_GET['order'] ) : 'desc' )
		);
		
		$payment_commissions = slicewp_get_commissions( $commission_args );

		if( empty( $payment_commissions ) )
			return;

		foreach( $payment_commissions as $payment_commission ) {
			
			$row_data = $payment_commission->to_array();
			
			/**
			 * Filter the payment row data
			 *
			 * @param array				$row_data
			 * @param SliceWP_Payment	$payment
			 *
			 */
			$row_data = apply_filters( 'slicewp_list_table_payment_commissions_row_data', $row_data, $payment_commission );

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
	 * Returns the HTML that will be displayed in the "amount" column
	 *
	 * @param array $item - data for the current row
	 *
	 * @return string
	 *
	 */
	public function column_amount( $item ) {

		$output = slicewp_format_amount( $item['amount'], $item['currency'] );

		return $output;

	}


	/**
	 * Returns the HTML that will be displayed in the "date_created" column
	 *
	 * @param array $item - data for the current row
	 *
	 * @return string
	 *
	 */
	public function column_date_created( $item ) {

		$output = slicewp_date_i18n( $item['date_created'] );

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

		$statuses = slicewp_get_commission_available_statuses();

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

		// Get the Payment
		if( is_null( $this->payment ) )
			return;

		$output  = '<div class="row-actions">';

			$output .= '<a href="' . add_query_arg( array( 'page' => 'slicewp-commissions', 'subpage' => 'edit-commission', 'commission_id' => $item['id'] ) , admin_url( 'admin.php' ) ) . '" class="slicewp-button-secondary">' . __( 'Edit', 'slicewp' ) . '</a>';
			
			if( in_array( $this->payment->get( 'status' ), array( 'unpaid', 'failed' ) ) )
				$output .= '<span class="trash"><a onclick="return confirm( \'' . __( "Are you sure you want to remove this commission?", "slicewp" ) . ' \' )" href="' . wp_nonce_url( add_query_arg( array( 'page' => 'slicewp-payouts','subpage' => 'review-payment', 'slicewp_action' => 'remove_commission', 'payment_id' => $this->payment->get( 'id' ), 'commission_id' => $item['id'] ) , admin_url( 'admin.php' ) ), 'slicewp_remove_commission', 'slicewp_token' ) . '" class="submitdelete">' . __( 'Remove', 'slicewp' ) . '</a></span>';

		$output .= '</div>';

		return $output;

	}


	/**
	 * HTML display when there are no items in the table
	 *
	 */
	public function no_items() {

		echo __( 'No commissions found.', 'slicewp' );

	}

}