<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * List table class outputter for Visits
 *
 */
Class SliceWP_WP_List_Table_Visits extends SliceWP_WP_List_Table {

	/**
	 * The number of visits that should appear in the table
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
			'plural' 	=> 'slicewp_visits',
			'singular' 	=> 'slicewp_visit',
			'ajax' 		=> false
		));

		$this->items_per_page = 10;
		$this->paged 		  = ( ! empty( $_GET['paged'] ) ? (int)$_GET['paged'] : 1 );


		//Get the start date from the filter, or set it to the default value if not present
		$date_min = ( ! empty( $_GET['date_min'] ) ? new DateTime( $_GET['date_min'] . ' 00:00:00' ) : '' );
		
		//Get the end date from the filter, or set it to the default value if not present
		$date_max = ( ! empty( $_GET['date_max'] ) ? new DateTime( $_GET['date_max'] . ' 23:59:59') : '' );


		$this->set_pagination_args( array(
            'total_items' => slicewp_get_visits( array( 'number' => -1, 'search' => ( ! empty( $_GET['s'] ) ? $_GET['s'] : '' ), 'affiliate_id' => ( ! empty( $_GET['affiliate_id'] ) ? $_GET['affiliate_id'] : '' ), 'date_min' => ( ! empty ( $date_min ) ? get_gmt_from_date( $date_min->format( 'Y-m-d H:i:s' ) ) : '' ), 'date_max' => ( ! empty( $date_max ) ? get_gmt_from_date( $date_max->format( 'Y-m-d H:i:s' ) ) : '' ), 'converted' => ( isset ( $_GET['converted'] ) ? (bool)$_GET['converted'] : '' ) ), true ),
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
			'id' 		    	=> __( 'ID', 'slicewp' ),
			'affiliate_name'	=> __( 'Affiliate', 'slicewp' ),
			'date_created'  	=> __( 'Date', 'slicewp' ),
			'ip_address'		=> __( 'IP Address', 'slicewp' ),
			'landing_url'		=> __( 'Landing URL', 'slicewp' ),
			'referrer_url'		=> __( 'Referrer URL', 'slicewp' ),
			'converted'			=> __( 'Converted', 'slicewp' )
		);

		/**
		 * Filter the columns of the affiliates table
		 *
		 * @param array $columns
		 *
		 */
		return apply_filters( 'slicewp_list_table_visits_columns', $columns );

	}


	/**
	 * Returns all the sortable columns for the table
	 *
	 */
	public function get_sortable_columns() {

		$columns = array(
			'id'				=> array( 'id', false ),
			'date_created'		=> array( 'date_created', false ),
			'landing_url'		=> array( 'landing_url', false ),
			'referrer_url'		=> array( 'referrer_url', false ),
			'converted'			=> array( 'commission_id', false)
		);

		/**
		 * Filter the sortable columns of the visits table
		 *
		 * @param array $columns
		 *
		 */
		return apply_filters( 'slicewp_list_table_visits_sortable_columns', $columns );

	}

	/**
     * Returns the possible views for the visits list table
     *
     */
    protected function get_views() {

		$converted = ( isset ( $_GET['converted'] ) ? (bool)( $_GET['converted'] ) : NULL );

		//Get the start date from the filter, or set it to the default value if not present
		$date_min = ( ! empty( $_GET['date_min'] ) ? new DateTime( $_GET['date_min'] . ' 00:00:00' ) : '' );

		//Get the end date from the filter, or set it to the default value if not present
		$date_max = ( ! empty( $_GET['date_max'] ) ? new DateTime( $_GET['date_max'] . ' 23:59:59') : '' );

		// Set the view for "all" visits
    	$views = array(
    		'all' => '<a href="' . add_query_arg( array( 'page' => 'slicewp-visits', 'user_search' => ( ! empty( $_GET['user_search'] ) ? $_GET['user_search'] : '' ), 'affiliate_id' => ( ! empty( $_GET['affiliate_id'] ) ? $_GET['affiliate_id'] : '' ), 'date_min' => ( ! empty ( $date_min ) ? $date_min->format( 'Y-m-d' ) : '' ), 'date_max' => ( ! empty ( $date_max ) ? $date_max->format( 'Y-m-d' ) : '' ), 'paged' => 1 ), admin_url( 'admin.php' ) ) . '" ' . ( is_null( $converted ) ? 'class="current"' : '' ) . '>' . __( 'All', 'slicewp' ) . ' <span class="count">(' . slicewp_get_visits( array( 'search' => ( ! empty( $_GET['s'] ) ? $_GET['s'] : '' ), 'affiliate_id' => ( ! empty( $_GET['affiliate_id'] ) ? $_GET['affiliate_id'] : '' ), 'date_min' => ( ! empty ( $date_min ) ? get_gmt_from_date( $date_min->format( 'Y-m-d H:i:s' ) ) : '' ), 'date_max' => ( ! empty ( $date_max ) ? get_gmt_from_date( $date_max->format( 'Y-m-d H:i:s' ) ) : '' ) ), true ) . ')</span></a>'
		);
		
		// Set the views for each visits status
		$views['converted'] = '<a href="' . add_query_arg( array( 'page' => 'slicewp-visits', 'user_search' => ( ! empty( $_GET['user_search'] ) ? $_GET['user_search'] : '' ), 'affiliate_id' => ( ! empty( $_GET['affiliate_id'] ) ? $_GET['affiliate_id'] : '' ), 'date_min' => ( ! empty ( $date_min ) ? $date_min->format( 'Y-m-d' ) : '' ), 'date_max' => ( ! empty ( $date_max ) ? $date_max->format( 'Y-m-d' ) : '' ), 'converted' => 1, 'paged' => 1 ), admin_url( 'admin.php' ) ) . '" ' . ( $converted === true ? 'class="current"' : '' ) . '>' . __('Converted', 'slicewp') . ' <span class="count">(' . slicewp_get_visits( array( 'converted' => true, 'search' => ( ! empty( $_GET['s'] ) ? $_GET['s'] : '' ), 'affiliate_id' => ( ! empty( $_GET['affiliate_id'] ) ? $_GET['affiliate_id'] : '' ), 'date_min' => ( ! empty ( $date_min ) ? get_gmt_from_date( $date_min->format( 'Y-m-d H:i:s' ) ) : '' ), 'date_max' => ( ! empty ( $date_max ) ? get_gmt_from_date( $date_max->format( 'Y-m-d H:i:s' ) ) : '' ) ), true ) . ')</span></a>';
		$views['not_converted'] = '<a href="' . add_query_arg( array( 'page' => 'slicewp-visits', 'user_search' => ( ! empty( $_GET['user_search'] ) ? $_GET['user_search'] : '' ), 'affiliate_id' => ( ! empty( $_GET['affiliate_id'] ) ? $_GET['affiliate_id'] : '' ), 'date_min' => ( ! empty ( $date_min ) ? $date_min->format( 'Y-m-d' ) : '' ), 'date_max' => ( ! empty ( $date_max ) ? $date_max->format( 'Y-m-d' ) : '' ), 'converted' => 0, 'paged' => 1 ), admin_url( 'admin.php' ) ) . '" ' . ( $converted === false ? 'class="current"' : '' ) . '>' . __('Not Converted', 'slicewp') . ' <span class="count">(' . slicewp_get_visits( array( 'converted' => false, 'search' => ( ! empty( $_GET['s'] ) ? $_GET['s'] : '' ), 'affiliate_id' => ( ! empty( $_GET['affiliate_id'] ) ? $_GET['affiliate_id'] : '' ), 'date_min' => ( ! empty ( $date_min ) ? get_gmt_from_date( $date_min->format( 'Y-m-d H:i:s' ) ) : '' ), 'date_max' => ( ! empty ( $date_max ) ? get_gmt_from_date( $date_max->format( 'Y-m-d H:i:s' ) ) : '' ) ), true ) . ')</span></a>';

		/**
		 * Filter the views of the commissions table
		 *
		 * @param array $views
		 *
		 */
		return apply_filters( 'slicewp_list_table_commissions_views', $views );

	}
	
	/**
	 * Gets the visits data and sets it
	 *
	 */
	private function set_table_data() {

		//Get the start date from the filter, or set it to the default value if not present
		$date_min = ( ! empty( $_GET['date_min'] ) ? new DateTime( $_GET['date_min'] . ' 00:00:00' ) : '' );

		//Get the end date from the filter, or set it to the default value if not present
		$date_max = ( ! empty( $_GET['date_max'] ) ? new DateTime( $_GET['date_max'] . ' 23:59:59') : '' );

		$visit_args = array(
			'number'		=> $this->items_per_page,
			'offset'		=> ( $this->get_pagenum() - 1 ) * $this->items_per_page,
			'affiliate_id'	=> ( ! empty( $_GET['affiliate_id'] ) ? absint( $_GET['affiliate_id'] ) : '' ),
			'date_min'		=> ( ! empty( $date_min ) ? get_gmt_from_date( $date_min->format( 'Y-m-d H:i:s' ) ) : '' ),
			'date_max'		=> ( ! empty( $date_max ) ? get_gmt_from_date( $date_max->format( 'Y-m-d H:i:s' ) ) : '' ),
			'search'		=> ( ! empty( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '' ),
			'converted'		=> ( isset( $_GET['converted'] ) ? (bool)$_GET['converted'] : '' ),
			'orderby'		=> ( ! empty( $_GET['orderby'] ) ? sanitize_text_field( $_GET['orderby'] ) : 'id' ),
			'order'			=> ( ! empty( $_GET['order'] ) ? sanitize_text_field( $_GET['order'] ) : 'desc' )
		);

		$visits = slicewp_get_visits( $visit_args );
		
		if( empty( $visits ) )
			return;

		foreach( $visits as $visit ) {

			$row_data = $visit->to_array();

			/**
			 * Filter the visit row data
			 *
			 * @param array			$row_data
			 * @param slicewp_Visit		$visit
			 *
			 */
			$row_data = apply_filters( 'slicewp_list_table_visits_row_data', $row_data, $visit );

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
	public function column_affiliate_name( $item ) {

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
	 * Returns the HTML that will be displayed in the "converted" column
	 *
	 * @param array $item - data for the current row
	 *
	 * @return string
	 *
	 */
	public function column_converted( $item ) {

		if( empty( $item['commission_id'] ) )
			$output = __( 'no', 'slicewp' );
		else
			$output = __('yes', 'slicewp'). ' (' . '<a href="' . add_query_arg( array( 'page' => 'slicewp-commissions', 'subpage' => 'edit-commission', 'commission_id' => $item['commission_id'] ) , admin_url( 'admin.php' ) ) . '">' . '#' . $item['commission_id'] . '</a>' . ')';

		return $output;

	}


	/**
	 * HTML display when there are no items in the table
	 *
	 */
	public function no_items() {

		echo __( 'No visits found.', 'slicewp' );

	}


	/**
	 * Adds the Visits filters above the table
	 *
	 */
	protected function extra_tablenav( $which ) {

		if( $which == 'top' ) {
		?>
		<div class="slicewp-table-filters">

			<!-- Affiliate User ID -->
			<div class="slicewp-field-wrapper slicewp-field-wrapper-users-autocomplete">
				
				<input id="slicewp-affiliate-user-id" class="slicewp-field-users-autocomplete" data-affiliates="include" data-return-value="affiliate_id" autocomplete="off" name="user_search" type="text" placeholder="<?php echo __( 'Affiliate name', 'slicewp' ); ?>" value="<?php echo ( ! empty( $_GET['user_search'] ) ? esc_attr( $_GET['user_search'] ) : '' ); ?>" />
				<input type="hidden" name="affiliate_id" value="<?php echo ( ! empty( $_GET['affiliate_id'] ) ? esc_attr( $_GET['affiliate_id'] ) : '' ); ?>" />

				<?php wp_nonce_field( 'slicewp_user_search', 'slicewp_user_search_token', false ); ?>
				
			</div>

			<!-- Date Min -->
			<div class="slicewp-field-wrapper">

				<input type="text" name="date_min" class="slicewp-datepicker" autocomplete="off" placeholder="<?php echo __( 'From', 'slicewp' ); ?>" value="<?php echo ( ! empty( $_GET['date_min'] ) ? esc_attr( $_GET['date_min'] ) : '' )?>" />

			</div>

			<!-- Date Max -->
			<div class="slicewp-field-wrapper">

				<input type="text" name="date_max" class="slicewp-datepicker" autocomplete="off" placeholder="<?php echo __( 'To', 'slicewp' ); ?>" value="<?php echo ( ! empty( $_GET['date_max'] ) ? esc_attr( $_GET['date_max'] ) : '' )?>" />

			</div>
			
			<!-- Converted -->
			<?php
			if ( ! empty( $_GET['converted'] ) ): ?>
			
				<input type="hidden" name="converted" value="<?php echo ( $_GET['converted'] ); ?>" />
			
			<?php endif; ?>
			
			<!-- Filter Button -->
			<input type="submit" class="slicewp-button-secondary" value="<?php echo __( 'Filter', 'slicewp' ); ?>" />

			<!-- Clear Filter -->
			<span class="slicewp-clear-filters"><a href="<?php echo add_query_arg( array( 'page' => 'slicewp-visits' ), admin_url( 'admin.php' ) ); ?>"><?php echo __('Clear', 'slicewp') ?></a></span>

		</div>
<?php

		}
	}
}