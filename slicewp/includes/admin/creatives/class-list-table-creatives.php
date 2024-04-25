<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * List table class outputter for Creatives
 *
 */
Class SliceWP_WP_List_Table_Creatives extends SliceWP_WP_List_Table {

	/**
	 * The number of creatives that should appear in the table
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
			'plural' 	=> 'slicewp_creatives',
			'singular' 	=> 'slicewp_creative',
			'ajax' 		=> false
		));

		$this->items_per_page = 10;
		$this->paged 		  = ( ! empty( $_GET['paged'] ) ? (int)$_GET['paged'] : 1 );

		$this->set_pagination_args( array(
            'total_items' => slicewp_get_creatives( array( 'number' => -1, 'status' => ( ! empty( $_GET['creative_status'] ) ? sanitize_text_field( $_GET['creative_status'] ) : '' ), 'search' => ( ! empty( $_GET['s'] ) ? $_GET['s'] : '' ) ), true ),
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
			'name'				=> __( 'Name', 'slicewp' ),
			'type'			  	=> __( 'Type', 'slicewp' ),
			'text'  			=> __( 'Text', 'slicewp' ),
			'image_preview'		=> __( 'Image Preview', 'slicewp' ),
			'landing_url'		=> __( 'Landing URL', 'slicewp' ),
			'status'			=> __( 'Status', 'slicewp' ),
			'actions'			=> ''
		);

		/**
		 * Filter the columns of the creatives table
		 *
		 * @param array $columns
		 *
		 */
		return apply_filters( 'slicewp_list_table_creatives_columns', $columns );

	}

	/**
	 * Returns all the sortable columns for the table
	 *
	 */
	public function get_sortable_columns() {

		$columns = array(
			'id'			=> array( 'id', false ),
			'name'			=> array( 'name', false ),
			'type'			=> array( 'type', false ),
			'landing_url'	=> array( 'landing_url', false ),
			'status'		=> array( 'status', false ),
		);

		/**
		 * Filter the sortable columns of the visits table
		 *
		 * @param array $columns
		 *
		 */
		return apply_filters( 'slicewp_list_table_creatives_sortable_columns', $columns );

	}

	/**
     * Returns the possible views for the creative list table
     *
     */
    protected function get_views() {

    	$statuses = slicewp_get_creative_available_statuses();

    	$creative_status = ( ! empty( $_GET['creative_status'] ) ? sanitize_text_field( $_GET['creative_status'] ) : '' );

    	// Set the view for "all" creatives
    	$views = array(
    		'all' => '<a href="' . add_query_arg( array( 'page' => 'slicewp-creatives', 'paged' => 1 ), admin_url( 'admin.php' ) ) . '" ' . ( empty( $creative_status ) ? 'class="current"' : '' ) . '>' . __( 'All', 'slicewp' ) . ' <span class="count">(' . slicewp_get_creatives( array( 'search' => ( ! empty( $_GET['s'] ) ? $_GET['s'] : '' ) ), true ) . ')</span></a>'
    	);

    	// Set the views for each creatives status
    	foreach( $statuses as $status_slug => $status_name ) {
    		$views[$status_slug] = '<a href="' . add_query_arg( array( 'page' => 'slicewp-creatives', 'creative_status' => $status_slug, 'paged' => 1 ), admin_url( 'admin.php' ) ) . '" ' . ( $creative_status == $status_slug ? 'class="current"' : '' ) . '>' . $status_name . ' <span class="count">(' . slicewp_get_creatives( array( 'status' => $status_slug, 'search' => ( ! empty( $_GET['s'] ) ? $_GET['s'] : '' ) ), true ) . ')</span></a>';
    	}

		/**
		 * Filter the views of the creatives table
		 *
		 * @param array $views
		 *
		 */
		return apply_filters( 'slicewp_list_table_creatives_views', $views );

    }


	/**
	 * Gets the creatives data and sets it
	 *
	 */
	private function set_table_data() {

		$creative_args = array(
			'number'  => $this->items_per_page,
			'offset'  => ( $this->get_pagenum() - 1 ) * $this->items_per_page,
			'status'  => ( ! empty( $_GET['creative_status'] ) ? sanitize_text_field( $_GET['creative_status'] ) : '' ),
			'search'  => ( ! empty( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '' ),
			'orderby' => ( ! empty( $_GET['orderby'] ) ? sanitize_text_field( $_GET['orderby'] ) : 'id' ),
			'order'	  => ( ! empty( $_GET['order'] ) ? sanitize_text_field( $_GET['order'] ) : 'desc' )
		);

		$creatives = slicewp_get_creatives( $creative_args );
		
		if( empty( $creative_args ) )
			return;

		foreach( $creatives as $creative ) {
			
			$row_data = $creative->to_array();
			
			/**
			 * Filter the creative row data
			 *
			 * @param array			$row_data
			 * @param slicewp_Creative	$creative
			 *
			 */
			$row_data = apply_filters( 'slicewp_list_table_creatives_row_data', $row_data, $creative );

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

		$output = isset( $item[ 'name' ] ) ? $item[ 'name' ] : '-';

		return $output;

	}


	/**
	 * Returns the HTML that will be displayed in the "type" column
	 *
	 * @param array $item - data for the current row
	 *
	 * @return string
	 *
	 */
	public function column_type( $item ) {

		$creative_types = slicewp_get_creative_available_types();

		return ( ! empty( $creative_types[$item['type']] ) ? $creative_types[$item['type']] : $item['type'] );

	}


	/**
	 * Returns the HTML that will be displayed in the "Image Preview" column
	 *
	 * @param array $item - data for the current row
	 *
	 * @return string
	 *
	 */
	public function column_image_preview( $item ) {

		return ( ! empty ( $item[ 'image_url' ] ) ) ? '<img class="slicewp-preview-image" src="' . esc_url( $item['image_url'] ) . '" alt="' . esc_attr( $item['alt_text'] ) . '">' : '';

	}


	/**
	 * Returns the HTML that will be displayed in the "Landing URL" column
	 *
	 * @param array $item - data for the current row
	 *
	 * @return string
	 *
	 */
	public function column_landing_url( $item ) {

		if( $item['type'] == 'long_text' )
			return '-';

		return ( ! empty ( $item['landing_url'] ) ) ? esc_url ( $item['landing_url'] ) : site_url();

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

		$statuses = slicewp_get_creative_available_statuses();

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
			$output .= '<a href="' . add_query_arg( array( 'page' => 'slicewp-creatives', 'subpage' => 'edit-creative', 'creative_id' => $item['id'] ) , admin_url( 'admin.php' ) ) . '" class="slicewp-button-secondary">' . __( 'Edit', 'slicewp' ) . '</a>';
			$output .= '<span class="trash"><a onclick="return confirm( \'' . __( "Are you sure you want to delete this creative?", "slicewp" ) . ' \' )" href="' . wp_nonce_url( add_query_arg( array( 'page' => 'slicewp-creatives', 'slicewp_action' => 'delete_creative', 'creative_id' => $item['id'] ) , admin_url( 'admin.php' ) ), 'slicewp_delete_creative', 'slicewp_token' ) . '" class="submitdelete">' . __( 'Delete', 'slicewp' ) . '</a></span>';
		$output .= '</div>';

		return $output;

	}


	/**
	 * HTML display when there are no items in the table
	 *
	 */
	public function no_items() {

		echo __( 'No creatives found.', 'slicewp' );

	}

}