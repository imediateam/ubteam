<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * List table class outputter for Payouts
 *
 */
Class SliceWP_WP_List_Table_Payouts extends SliceWP_WP_List_Table {

	/**
	 * The number of payouts that should appear in the table
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
			'plural' 	=> 'slicewp_payouts',
			'singular' 	=> 'slicewp_payout',
			'ajax' 		=> false
		));

		$this->items_per_page = 10;
		$this->paged 		  = ( ! empty( $_GET['paged'] ) ? (int)$_GET['paged'] : 1 );

		$this->set_pagination_args( array(
            'total_items' => slicewp_get_payouts( array( 'number' => -1 ), true ),
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
            'id' 		   		=> __( 'ID', 'slicewp' ),
            'amount'			=> __( 'Amount', 'slicewp '),
            'payments_count'	=> __( 'Payments', 'slicewp' ),
			'date_created'		=> __( 'Date', 'slicewp' ),
			'progress'			=> __( 'Progress', 'slicewp' ),
			'actions'			=> ''
		);

		/**
		 * Filter the columns of the payouts table
		 *
		 * @param array $columns
		 *
		 */
		return apply_filters( 'slicewp_list_table_payouts_columns', $columns );

	}

    
	/**
	 * Returns all the sortable columns for the table
	 *
	 */
	public function get_sortable_columns() {

		$columns = array(
			'id'		=> array( 'id', false ),
			'amount'	=> array( 'amount', false)
        );

		/**
		 * Filter the sortable columns of the visits table
		 *
		 * @param array $columns
		 *
		 */
		return apply_filters( 'slicewp_list_table_payouts_sortable_columns', $columns );

	}


	/**
	 * Gets the payouts data and sets it
	 *
	 */
	private function set_table_data() {

		$payout_args = array(
			'number'  => $this->items_per_page,
			'offset'  => ( $this->get_pagenum() - 1 ) * $this->items_per_page,
			'orderby' => ( ! empty( $_GET['orderby'] ) ? sanitize_text_field( $_GET['orderby'] ) : 'id' ),
			'order'	  => ( ! empty( $_GET['order'] ) ? sanitize_text_field( $_GET['order'] ) : 'desc' )
		);

		$payouts = slicewp_get_payouts( $payout_args );
		
		if( empty( $payouts ) )
			return;

		foreach( $payouts as $payout ) {
			
			$row_data = $payout->to_array();
			
			/**
			 * Filter the payout row data
			 *
			 * @param array				$row_data
			 * @param SliceWP_Payout	$payout
			 *
			 */
			$row_data = apply_filters( 'slicewp_list_table_payouts_row_data', $row_data, $payout );

			$this->data[] = $row_data;

		}
		
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
	 * Returns the HTML that will be displayed in the "amount" column
	 *
	 * @param array $item - data for the current row
	 *
	 * @return string
	 *
	 */
	public function column_payments_count( $item ) {

		$args = array(
			'payout_id' => $item['id']
		);

		$output = slicewp_get_payments( $args, true );

		return $output;

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

		$currency = slicewp_get_setting( 'active_currency', 'USD' );
		$output = slicewp_format_amount( $item['amount'], $currency );

		return $output;

	}


	/**
	 * Returns the HTML that will be displayed in the "progress" column
	 *
	 * @param array $item - data for the current row
	 *
	 * @return string
	 *
	 */
	public function column_progress( $item ) {

		// Get the Payout Payments Count
		$args = array(
			'payout_id' => $item['id']
		);

		$payments_count = slicewp_get_payments( $args, true );

		// Get the Payout Payments Count for paid Payments
		$args = array(
			'payout_id' => $item['id'],
			'status'	=> 'paid'
		);

		$payments_paid_count = slicewp_get_payments( $args, true );

		// Compute the Paid percentage
		$paid_percentage = round( $payments_paid_count / $payments_count * 100 );

		return slicewp_output_progressbar( $paid_percentage, true );

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
		$output  = '<div class="row-actions">';
			$output .= '<a href="' . add_query_arg( array( 'page' => 'slicewp-payouts', 'subpage'=>'view-payout', 'payout_id' => $item['id'] ) , admin_url( 'admin.php' ) ) . '" class="slicewp-button-secondary">' . __( 'View', 'slicewp' ) . '</a>';

		// Allow CSV Generation only if the Payout has an amount greater than zero
		if ( $item['amount'] != 0 )
			$output .= '<a href="' . wp_nonce_url( add_query_arg( array( 'page' => 'slicewp-payouts', 'subpage' => 'payouts-history', 'slicewp_action' => 'generate_payouts_csv', 'payout_id' => $item['id'] ) , admin_url( 'admin.php' ) ), 'slicewp_generate_payouts_csv', 'slicewp_token' ) . '" class="slicewp-button-primary">' . __( 'Generate CSV', 'slicewp' ) . '</a>';
		else
			$output .= '<a href="#" class="slicewp-button-primary slicewp-disabled" onclick="return false;">' . __( 'Generate CSV', 'slicewp' ) . '</a>';

		// Get paid payments
		$payments_count = slicewp_get_payments( array( 'number' => -1, 'payout_id' => $item['id'], 'status' => 'paid' ), true );

		// Block deletion in certain circumstances
		if ( $item['admin_id'] != get_current_user_id() || $payments_count != 0 ) {

			$title = ( $payments_count != 0 ? __( 'You cannot delete this payout because it contains paid payments.', 'slicewp' ) : __( 'You cannot delete this payout because you are not the one that created it.', 'slicewp' ) );

			$output .= '<span class="disabled" title="' . esc_attr( $title ) . '">' . __( 'Delete', 'slicewp' ) . '</span>';

		// Add the delete payout link
		} else {

			$output .= '<span class="trash"><a onclick="return confirm( \'' . __( "Are you sure you want to delete this payout? All the contained payments will be also deleted!", "slicewp" ) . ' \' )" href="' . wp_nonce_url( add_query_arg( array( 'page' => 'slicewp-payouts', 'slicewp_action' => 'delete_payout', 'payout_id' => $item['id'] ) , admin_url( 'admin.php' ) ), 'slicewp_delete_payout', 'slicewp_token' ) . '" class="submitdelete">' . __( 'Delete', 'slicewp' ) . '</a></span>';

		}

		$output .= '</div>';

		return $output;

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
	 * HTML display when there are no items in the table
	 *
	 */
	public function no_items() {

		echo __( 'No payouts found.', 'slicewp' );

	}
}