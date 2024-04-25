<?php
/**
 * Plugin Name: SliceWP
 * Plugin URI: https://slicewp.com/
 * Description: The fastest and easiest way to set up an affiliate program for your store or membership site
 * Version: 1.0.27
 * Author: SliceWP
 * Author URI: https://slicewp.com/
 * Text Domain: slicewp
 * License: GPL2
 *
 * == Copyright ==
 * Copyright 2020 SliceWP
 *	
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Main plugin class
 *
 */
Class SliceWP {

	/**
	 * The current object instance
	 *
	 * @access private
	 * @var    SliceWP
	 *
	 */
	private static $instance;

	/**
	 * A list with the objects that handle database requests
	 *
	 * @access public
	 * @var    array
	 *
	 */
	public $db = array();

	/**
	 * A list with the objects that handle submenu pages
	 *
	 * @access public
	 * @var    array
	 *
	 */
	public $submenu_pages = array();

	/**
	 * A list of plugin integrations
	 *
	 * @access public
	 * @var    array
	 *
	 */
	public $integrations = array();

	/**
	 * A list of services used for different operations
	 *
	 * @access public
	 * @var    array
	 *
	 */
	public $services;


	/**
	 * Constructor
	 *
	 */
	public function __construct() {

		// Defining constants
		define( 'SLICEWP_VERSION', 		   '1.0.27' );
		define( 'SLICEWP_BASENAME',  	   plugin_basename( __FILE__ ) );
		define( 'SLICEWP_PLUGIN_DIR', 	   plugin_dir_path( __FILE__ ) );
		define( 'SLICEWP_PLUGIN_DIR_URL',  plugin_dir_url( __FILE__ ) );

		$this->include_files();
		$this->load_db_layer();
		$this->load_services();

		// Add a hook where different systems can include files at a later time
		add_action( 'plugins_loaded', array( $this, 'include_files_late' ), 15 );

		// Load integrations late to ensure the plugins are loaded
		add_action( 'plugins_loaded', array( $this, 'load_integrations' ), 15 );

		// Load plugin textdomain
        add_action( 'plugins_loaded', array( $this, 'load_textdomain' ), 15 );

		// Check if just updated
		add_action( 'plugins_loaded', array( $this, 'update_check' ), 20 );

		// Update the database tables
		add_action( 'slicewp_update_check', array( $this, 'update_database_tables' ) );

		// Add and remove main plugin page
		add_action( 'admin_menu', array( $this, 'add_main_menu_page' ), 10 );
        add_action( 'admin_menu', array( $this, 'remove_main_menu_page' ), 11 );

        // Add submenu pages
        add_action( 'admin_menu', array( $this, 'load_admin_submenu_pages' ), 9 );

        // Admin scripts
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

        // Front-end scripts
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_front_end_scripts' ) );

        // Remove plugin query args from the URL
        add_filter( 'removable_query_args', array( $this, 'removable_query_args' ) );

        // Add a class to the admin body to tell plugin pages apart
        add_filter( 'admin_body_class', array( $this, 'admin_body_class' ) );

        // Add extra action links to the plugin in Plugins list table
        add_filter( 'plugin_action_links_' . SLICEWP_BASENAME, array( $this, 'add_plugin_action_links' ) );

        // Set and unset cron jobs
        register_activation_hook( __FILE__, array( $this, 'set_cron_jobs' ) );
        register_deactivation_hook( __FILE__, array( $this, 'unset_cron_jobs' ) );

        // Set general settings
        register_activation_hook( __FILE__, array( $this, 'set_general_settings' ) );

        // Set fresh install transient
        register_activation_hook( __FILE__, array( $this, 'set_activation_transient' ) );

        /**
         * Plugin initialized
         *
         */
        do_action( 'slicewp_initialized' );

	}


	/**
	 * Returns an instance of the plugin object
	 *
	 * @return SliceWP
	 *
	 */
	public static function instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof SliceWP ) )
			self::$instance = new SliceWP;

		return self::$instance;

	}


	/**
	 * Add the main menu page
	 *
	 */
	public function add_main_menu_page() {

		add_menu_page( 'SliceWP', 'SliceWP', apply_filters( 'slicewp_menu_page_capability', 'manage_options' ), 'slicewp-page', '', 'dashicons-chart-pie' );

	}
    
    /**
	 * Remove the main menu page as we will rely only on submenu pages
	 *
	 */
	public function remove_main_menu_page() {

		remove_submenu_page( 'slicewp-page', 'slicewp-page' );

	}


	/**
	 * Checks to see if the current version of the plugin matches the version
	 * saved in the database
	 *
	 * @return void 
	 *
	 */
	public function update_check() {

		$db_version = get_option( 'slicewp_version', '' );
		$do_update 	= false;

		// If current version number differs from saved version number
		if( $db_version != SLICEWP_VERSION ) {

			$do_update = true;

			// Update the version number in the db
			update_option( 'slicewp_version', SLICEWP_VERSION );

			// Add first activation time
			if( get_option( 'slicewp_first_activation', '' ) == '' ) {

				update_option( 'slicewp_first_activation', time() );

				/**
				 * Hook for first time activation
				 *
				 */
				do_action( 'slicewp_first_activation', $db_version );

			}

		}


		if( $do_update ) {

			/**
			 * Hook for fresh update
			 *
			 */
			do_action( 'slicewp_update_check', $db_version );

			// Trigger set cron jobs
			$this->set_cron_jobs();

		}

	}


	/**
	 * Creates and updates the database tables 
	 *
	 * @return void
	 *
	 */
	public function update_database_tables() {

		foreach( $this->db as $db_class ) {

			$db_class->create_table();

		}

	}


	/**
	 * Sets an action hook for modules to add custom schedules
	 *
	 */
	public function set_cron_jobs() {

		do_action( 'slicewp_set_cron_jobs' );

	}


	/**
	 * Sets an action hook for modules to remove custom schedules
	 *
	 */
	public function unset_cron_jobs() {

		do_action( 'slicewp_unset_cron_jobs' );

	}


	/**
	 * Sets the default general settings on plugin activation
	 *
	 */
	public function set_general_settings() {

		$settings = get_option( 'slicewp_settings' );

		if( ! empty( $settings ) && is_array( $settings ) )
			return;

		$settings = array(
			'commission_rate_sale'   	   		=> 20,
			'commission_rate_type_sale'    		=> 'percentage',
			'commission_rate_subscription' 		=> 20,
			'commission_rate_type_subscription' => 'percentage',
			'cookie_duration' 		 	   => 30,
			'payments_minimum_amount' 	   => 0,
			'active_currency'		 	   => 'USD',
			'currency_thousands_separator' => ',',
			'currency_decimal_separator'   => '.',
			'currency_symbol_position' 	   => 'before',
			'affiliate_keyword'			   => 'aff',
			'affiliate_credit'			   => 'last',
			'required_field_payment_email' 		 => 1,
			'required_field_website'	   		 => 1,
			'required_field_promotional_methods' => 1,
			'from_email'				   => get_bloginfo( 'admin_email' ),
			'from_name'					   => get_bloginfo( 'name' ),
			'email_template'			   => 'default',
			'admin_emails'				   => get_bloginfo( 'admin_email' ),
			'email_notifications'		   => array(
				'admin_new_affiliate_registration' => array(
					'enabled' => 1,
					'subject' => __( 'New Affiliate Registration', 'slicewp' ),
					'content' => __( 'The user {{affiliate_username}} ({{affiliate_email}}) just signed up for the affiliate program.', 'slicewp' )
				),
				'admin_new_commission_registered' => array(
					'enabled' => 1,
					'subject' => __( 'New Commission Registered', 'slicewp' ),
					'content' => __( 'A new commission was generated for your affiliate partner {{affiliate_first_name}} {{affiliate_last_name}} ({{affiliate_email}}).', 'slicewp' )
				),
				'affiliate_account_registration' => array(
					'enabled' => 1,
					'subject' => __( 'Affiliate Account Registration', 'slicewp' ),
					'content' => __( 'Hey {{affiliate_first_name}},', 'slicewp' ) . "\n\n" . __( "We've received your affiliate registration request. Our team will review it shortly and we'll get back to you with details.", 'slicewp' )
				),
				'affiliate_account_approved' => array(
					'subject' => __( 'Affiliate Account Approved', 'slicewp' ),
					'content' => __( 'Hey {{affiliate_first_name}},', 'slicewp' ) . "\n\n" . __( 'Your application for our affiliate account has been approved.', 'slicewp' ) . "\n\n" . __( 'Welcome to the team!', 'slicewp' )
				),
				'affiliate_account_rejected' => array(
					'subject' => __( 'Affiliate Account Rejected', 'slicewp' ),
					'content' => __( 'Hey {{affiliate_first_name}},', 'slicewp' ) . "\n\n" . __( 'Unfortunately, your application for an affiliate account was rejected.', 'slicewp' ) . "\n\n" . "{{reject_reason}}"
				),
				'affiliate_commission_approved' => array(
					'enabled' => 1,
					'subject' => __( 'Commission Approved', 'slicewp' ),
					'content' => __( 'Hey {{affiliate_first_name}},', 'slicewp' ) . "\n\n" . __( "You have been rewarded a new commission of {{commission_amount}}.", 'slicewp' )
				)
			)
		);

		update_option( 'slicewp_settings', $settings );

	}


	/**
	 * Sets a transient right at activation time
	 *
	 */
	public function set_activation_transient() {

		set_transient( '_slicewp_activated', 1, 60 );

	}


	/**
	 * Include files
	 *
	 * @return void
	 *
	 */
	public function include_files() {

		/**
		 * Include abstract classes
		 *
		 */
		$abstracts = scandir( SLICEWP_PLUGIN_DIR . 'includes/base/abstracts' );

		foreach( $abstracts as $abstract ) {

			if( false === strpos( $abstract, '.php' ) )
				continue;

			include SLICEWP_PLUGIN_DIR . 'includes/base/abstracts/' . $abstract;

		}

		/**
		 * Include all functions.php files from all plugin folders
		 *
		 */
		$this->_recursively_include_files( SLICEWP_PLUGIN_DIR . 'includes' );

		/**
		 * Helper hook to include files early
		 *
		 */
		do_action( 'slicewp_include_files' );

	}


	/**
	 * Include files on plugins_loaded
	 *
	 * @return void
	 *
	 */
	public function include_files_late() {

		/**
		 * Helper hook to include files late
		 *
		 */
		do_action( 'slicewp_include_files_late' );

	}


	/**
	 * Recursively includes all functions.php files from the given directory path
	 *
	 * @param string $dir_path
	 *
	 */
	protected function _recursively_include_files( $dir_path ) {

		$folders = array_filter( glob( $dir_path . '/*' ), 'is_dir' );

		foreach( $folders as $folder_path ) {

			if( file_exists( $folder_path . '/functions.php' ) )
				include $folder_path . '/functions.php';

			$this->_recursively_include_files( $folder_path );

		}

	}


	/**
	 * Sets up all objects that handle database related requests and adds them to the
	 * $db property of the app
	 *
	 */
	public function load_db_layer() {

		/**
		 * Hook to register db class handlers
		 * The array element should be 'class_slug' => 'class_name'
		 *
		 * @param array
		 *
		 */
		$db_classes = apply_filters( 'slicewp_register_database_classes', array() );

		if( empty( $db_classes ) )
			return;

		foreach( $db_classes as $db_class_slug => $db_class_name ) {

			$this->db[$db_class_slug] = new $db_class_name;

		}

	}


	/**
	 * Sets up all objects that handle submenu pages and adds them to the
	 * $submenu_pages property of the app
	 *
	 */
	public function load_admin_submenu_pages() {

		/**
		 * Hook to register submenu_pages class handlers
		 * The array element should be 'submenu_page_slug' => array( 'class_name' => array(), 'data' => array() )
		 *
		 * @param array
		 *
		 */
		$submenu_pages = apply_filters( 'slicewp_register_submenu_page', array() );

		if( empty( $submenu_pages ) )
			return;

		foreach( $submenu_pages as $submenu_page_slug => $submenu_page ) {

			if( empty( $submenu_page['data'] ) )
				continue;

			if( empty( $submenu_page['data']['page_title'] ) || empty( $submenu_page['data']['menu_title'] ) || empty( $submenu_page['data']['capability'] ) || empty( $submenu_page['data']['menu_slug'] ) )
				continue;

			$this->submenu_pages[$submenu_page['data']['menu_slug']] = new $submenu_page['class_name']( $submenu_page['data']['page_title'], $submenu_page['data']['menu_title'], $submenu_page['data']['capability'], $submenu_page['data']['menu_slug'] );

		}

	}


	/**
	 * Sets up all objects that handle integration related requests and adds them to the
	 * $integrations property of the app
	 *
	 */
	public function load_integrations() {

		/**
		 * Hook to register integration class handlers
		 * The array element should be 'class_slug' => 'class_name'
		 *
		 * @param array
		 *
		 */
		$integration_classes = apply_filters( 'slicewp_register_integration', array() );

		if( empty( $integration_classes ) )
			return;

		foreach( $integration_classes as $integration_class_slug => $integration_class_name ) {

			$this->integrations[$integration_class_slug] = new $integration_class_name;

		}

	}


	/**
	 * Sets up all objects that handle different services that can be used within the plugin
	 *
	 */
	public function load_services() {

		$this->services['tracking'] 		 = new SliceWP_Tracking;
		$this->services['debug_logger'] 	 = new SliceWP_Debug_Logger;
		$this->services['migration_manager'] = new SliceWP_Migration_Manager;

	}


	/**
     * Loads plugin text domain
     *
     */
    public function load_textdomain() {

        $current_theme = wp_get_theme();

        if( ! empty( $current_theme->stylesheet ) && file_exists( get_theme_root() . '/' . $current_theme->stylesheet . '/slicewp-translations' ) )
            load_plugin_textdomain( 'slicewp', false, plugin_basename( dirname( __FILE__ ) ) . '/../../themes/' . $current_theme->stylesheet . '/slicewp-translations/' );
        else
            load_plugin_textdomain( 'slicewp', false, plugin_basename( dirname( __FILE__ ) ) . '/translations/' );

    }


	/**
	 * Enqueue the scripts and style for the admin area
	 *
	 */
	public function enqueue_admin_scripts( $hook ) {

		if( strpos( $hook, 'slicewp' ) !== false || ( isset( $_GET['page'] ) && strpos( $_GET['page'], 'slicewp' ) === 0 ) ) {

			// Color picker
    		wp_enqueue_style( 'jquery-style', SLICEWP_PLUGIN_DIR_URL . 'assets/css/jquery-ui.css', array(), SLICEWP_VERSION );
			wp_enqueue_style( 'wp-color-picker' );

			// Datepicker Custom
			wp_enqueue_style( 'slicewp-datepicker', SLICEWP_PLUGIN_DIR_URL . 'assets/css/datepicker.css', array(), SLICEWP_VERSION );

			// Select2
			wp_register_script( 'select2-js', SLICEWP_PLUGIN_DIR_URL . 'assets/libs/select2/select2.min.js', array( 'jquery' ) );
			wp_enqueue_script( 'select2-js' );

			wp_register_style( 'select2-css', SLICEWP_PLUGIN_DIR_URL . 'assets/libs/select2/select2.min.css' );
			wp_enqueue_style( 'select2-css' );

			// Datetime picker
			wp_register_script( 'slicewp-timepicker-js', SLICEWP_PLUGIN_DIR_URL . 'assets/libs/timepicker/jquery-ui-timepicker-addon.js', array( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-slider' ) );
			wp_enqueue_script( 'slicewp-timepicker-js' );

			wp_register_style( 'slicewp-timepicker-css', SLICEWP_PLUGIN_DIR_URL . 'assets/libs/timepicker/jquery-ui-timepicker-addon.css' );
			wp_enqueue_style( 'slicewp-timepicker-css' );

			// Media Library
			wp_enqueue_media();

		}

		// Plugin styles
		wp_register_style( 'slicewp-style', SLICEWP_PLUGIN_DIR_URL . 'assets/css/style-admin.css', array(), SLICEWP_VERSION );
		wp_enqueue_style( 'slicewp-style' );

		// Plugin script
		wp_register_script( 'slicewp-script', SLICEWP_PLUGIN_DIR_URL . 'assets/js/script-admin.js', array( 'jquery', 'wp-color-picker', 'jquery-ui-datepicker', 'jquery-ui-autocomplete' ), SLICEWP_VERSION );
		wp_enqueue_script( 'slicewp-script' );

		/**
		 * Hook to enqueue scripts immediately after the plugin's scripts
		 *
		 */
		do_action( 'slicewp_enqueue_admin_scripts' );

	}


	/**
	 * Registers and enqueues the scripts and style for the front-end part
	 *
	 */
	public function enqueue_front_end_scripts() {

		// Register and enqueue plugin styles
		wp_register_style( 'slicewp-style', SLICEWP_PLUGIN_DIR_URL . 'assets/css/style-front-end.css', array( 'dashicons' ), SLICEWP_VERSION );
		wp_enqueue_style( 'slicewp-style' );

		// Register plugin scripts. Enqueing is done in the shortcodes callbacks for performance.
		wp_register_script( 'slicewp-script', SLICEWP_PLUGIN_DIR_URL . 'assets/js/script-front-end.js', array( 'jquery' ), SLICEWP_VERSION, true );

		/**
		 * Hook to enqueue scripts immediately after the plugin's scripts
		 *
		 */
		do_action( 'slicewp_enqueue_front_end_scripts' );

	}


	/**
	 * Removes the query variables from the URL upon page load
	 *
	 */
	public function removable_query_args( $args = array() ) {

		$args[] = 'slicewp_message';

		return $args;

	}


	/**
	 * Add custom plugin CSS classes to the admin body classes
	 *
	 */
	public function admin_body_class( $classes ) {

		if( empty( $_GET['page'] ) )
			return $classes;

		if( false === strpos( $_GET['page'], 'slicewp-' ) )
			return $classes;

		return $classes . ' slicewp-pagestyles';

	}


	/**
	 * Add extra action links in the plugins page
	 *
	 * @param array $links
	 *
	 * @return array
	 *
	 */
	public function add_plugin_action_links( $links ) {

	  $links[] = '<a href="' . esc_url( add_query_arg( array( 'page' => 'slicewp-settings' ), admin_url( 'admin.php' ) ) ) . '">' . __( 'Settings', 'slicewp' ) . '</a>';
	  $links[] = '<a href="https://slicewp.com/docs/" target="_blank">' . __( 'Docs', 'slicewp' ) . '</a>';

	  return $links;

	}

}

function slicewp() {

	return SliceWP::instance();

}

slicewp();