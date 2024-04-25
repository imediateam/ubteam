<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * List table class outputter for Payments
 *
 */
Class SliceWP_WP_List_Table_Payout_Payments_Preview extends SliceWP_WP_List_Table {

	/**
	 * The number of payments that should appear in the table
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
			'plural' 	=> 'slicewp_payout_payments_previews',
			'singular' 	=> 'slicewp_payout_payments_preview',
			'ajax' 		=> false
		));

		//Get the start date from the filter, or set it to the default value if not present
		$date_min = ( ! empty( $_GET['date_min'] ) ? new DateTime( $_GET['date_min'] . ' 00:00:00' ) : '' );
		
		//Get the end date from the filter, or set it to the default value if not present
		$date_max = ( ! empty( $_GET['date_max'] ) ? new DateTime( $_GET['date_max'] . ' 23:59:59') : '' );

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
			'affiliate'		=> __( 'Affiliate', 'slicewp' ),
            'amount'		=> __( 'Amount', 'slicewp' ),
            'commissions'   => __( 'Commissions', 'slicewp' )
		);

		/**
		 * Filter the columns of the payout payments preview table
		 *
		 * @param array $columns
		 *
		 */
		return apply_filters( 'slicewp_list_table_payout_payments_preview_columns', $columns );

	}

	/**
	 * Returns all the sortable columns for the table
	 *
	 */
	public function get_sortable_columns() {

		$columns = array(
			'amount'		=> array( 'amount', false ),
			'commissions'	=> array( 'commissions', false)
        );

		/**
		 * Filter the sortable columns of the payments table
		 *
		 * @param array $columns
		 *
		 */
		return apply_filters( 'slicewp_list_table_payout_payments_preview_sortable_columns', $columns );

	}


	/**
	 * Gets the payments data and sets it
	 *
	 */
	private function set_table_data() {

		//Get the start date from the filter, or set it to the default value if not present
		$date_min = ( ! empty( $_GET['date_min'] ) ? new DateTime( $_GET['date_min'] . ' 00:00:00' ) : '' );

		//Get the end date from the filter, or set it to the default value if not present
		$date_max = ( ! empty( $_GET['date_max'] ) ? new DateTime( $_GET['date_max'] . ' 23:59:59') : '' );

        // Prepare the arguments to read the commissions
        $commission_args = array(
            'number'		=> -1,
            'date_min'		=> ( ! empty( $date_min ) ? get_gmt_from_date( $date_min->format( 'Y-m-d H:i:s' ) ) : '' ),
            'date_max'		=> ( ! empty( $date_max ) ? get_gmt_from_date( $date_max->format( 'Y-m-d H:i:s' ) ) : '' ),
            'affiliate_id'	=> ( ! empty( $_GET['affiliate_id'] ) ? absint( $_GET['affiliate_id'] ) : '' ),
            'status'		=> 'unpaid'
        );

        // Get the affiliate ids that generated the commissions
        $affiliate_ids = slicewp_get_commissions_column( 'affiliate_id', $commission_args );

        // Keep only the unique affiliate_ids
        $affiliate_ids = array_unique( $affiliate_ids );
        $affiliate_ids = array_map( 'absint', $affiliate_ids );
        $affiliate_ids = array_values( $affiliate_ids );

        // The total amount of the Payout will be saved here
        $total_amount = 0;

        // Get the Payments Minimum Amount setting
        $minimum_payment_amount = isset( $_GET['payments_minimum_amount'] ) ? esc_attr( $_GET['payments_minimum_amount'] ) : slicewp_get_setting( 'payments_minimum_amount' );

        // Get the Currency setting
        $currency = slicewp_get_setting( 'active_currency', 'USD' );

        // We will save here all the Payments data
        $all_payment_data = array();

        // Get the commissions of each affiliate
        foreach ( $affiliate_ids as $i => $affiliate_id ) {

            $commission_args['affiliate_id'] = $affiliate_id;
            $commissions = slicewp_get_commissions( $commission_args );

            $payment_amount = 0;
            $commission_ids = array();

            // Save the Payment amount and the Commission IDs
            foreach ( $commissions as $j => $commission ) {

                $payment_amount += $commission->get('amount');
                $commission_ids[$j] = $commission->get('id');
                
            }

            // Skip the Payment if is less than the Payments Minimum Amount setting
            if ( $payment_amount < $minimum_payment_amount )
                continue;

            // Save the Commission IDs in a string
            $commission_ids = implode( ',', $commission_ids );

            // Prepare the Payout data
            $payment_data = array(
                'affiliate_id'		=> $affiliate_id,
                'amount'			=> $payment_amount,
                'currency'			=> $currency,
                'commission_ids'	=> $commission_ids
            );

            // Save the Payment data
            $all_payment_data[] = $payment_data;

            // Save the Payout total amount
            $total_amount += $payment_amount;
        
        }
		
		if( empty( $all_payment_data ) )
			return;

		// Sort the payments
		if( ! empty( $_GET['orderby'] ) && in_array( $_GET['orderby'], array( 'amount', 'commissions' ) ) && ! empty( $_GET['order'] ) && in_array( $_GET['order'], array( 'asc', 'desc' ) ) ){
			
			foreach ( $all_payment_data as $payment => $row ){

				$sort[$payment] = ( $_GET['orderby'] == 'amount' ? $row['amount'] : count( explode( ',', $row['commission_ids'] ) ) );

			}

			array_multisort( $sort, ( $_GET['order'] == 'desc' ? SORT_DESC : SORT_ASC ), $all_payment_data );

		}

		// Set the table data
		foreach( $all_payment_data as $payment ) {
			
			$row_data = $payment;
			
			/**
			 * Filter the payment row data
			 *
			 * @param array				$row_data
			 * @param SliceWP_Payment	$payment
			 *
			 */
			$row_data = apply_filters( 'slicewp_list_table_payout_payments_preview_row_data', $row_data, $payment );

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
	 * Returns the HTML that will be displayed in the "affiliate" column
	 *
	 * @param array $item - data for the current row
	 *
	 * @return string
	 *
	 */
	public function column_affiliate( $item ) {

		/**
		 * Set user display name
		 *
		 */
		$affiliate_name = slicewp_get_affiliate_name( $item['affiliate_id'] );

		if( null === $affiliate_name )
			$output = __( '(inexistent affiliate)', 'slicewp' );
		else
			$output = '<a href="' . add_query_arg( array( 'page' => 'slicewp-affiliates', 'subpage' => 'edit-affiliate', 'affiliate_id' => $item['affiliate_id'] ) , admin_url( 'admin.php' ) ) . '">' . $affiliate_name . '</a>';


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

		$output = slicewp_format_amount( $item['amount'], $item['currency'] );

		return $output;

	}


    /**
	 * Returns the HTML that will be displayed in the "commissions" column
	 *
	 * @param array $item - data for the current row
	 *
	 * @return string
	 *
	 */
	public function column_commissions( $item ) {

        $output = count( explode( ',', $item['commission_ids'] ) );

        return $output;

	}


	/**
	 * HTML display when there are no items in the table
	 *
	 */
	public function no_items() {

		echo __( 'No payments found.', 'slicewp' );

	}

}