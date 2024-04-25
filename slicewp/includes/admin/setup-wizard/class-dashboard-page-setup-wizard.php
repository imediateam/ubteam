<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


class SliceWP_Dashboard_Page_Setup_Wizard {

	/**
	 * All setup wizard steps
	 *
	 * @access private
	 * @var    array
	 *
	 */
	private $steps;

	/**
	 * The current step the user is viewing
	 *
	 * @access private
	 * @var    string
	 *
	 */
	private $current_step;

	/**
	 * The numerical index for the current step
	 *
	 * @access private
	 * @var    int
	 *
	 */
	private $current_step_index;


	/**
	 * Constructor
	 *
	 */
	public function __construct() {

		$this->steps 			  = $this->get_steps();
		$this->current_step 	  = ( ! empty( $_GET['current_step'] ) ? sanitize_text_field( $_GET['current_step'] ) : key( $this->steps ) );
		$this->current_step_index = array_search( $this->current_step, array_keys( $this->steps ) );

		add_action( 'admin_menu', array( $this, 'add_dashboard_page' ) );
		add_action( 'admin_init', array( $this, 'output' ) );

	}


	/**
	 * Returns an array with all setup wizard steps
	 *
	 * @return array
	 *
	 */
	protected function get_steps() {

		$steps = array(
			'integrations' => __( 'Integrations', 'slicewp' ),
			'setup' 	   => __( 'Program Basics', 'slicewp' ),
			'pages' 	   => __( 'Affiliate Pages', 'slicewp' ),
			'emails' 	   => __( 'Emails', 'slicewp' ),
			'finished' 	   => __( 'Ready!', 'slicewp' )
		);

		return $steps;

	}


	/**
	 * Callback to add the dashboard page
	 *
	 */
	public function add_dashboard_page() {

		add_dashboard_page( '', '', 'manage_options', 'slicewp-setup', '' );

	}


	/**
	 * Callback for the HTML output for the page
	 *
	 */
	public function output() {

		if( empty( $_GET['page'] ) || $_GET['page'] != 'slicewp-setup' )
			return;

		set_current_screen();

		// Mark the setup wizard as visited
		update_option( 'slicewp_setup_wizard_visited', 1 );

		ob_start();
		include 'views/view-page-setup-wizard.php';
		exit;

	}

}