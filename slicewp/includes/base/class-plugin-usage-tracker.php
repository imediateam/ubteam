<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


class SliceWP_Plugin_Usage_Tracker {

	/**
	 * Array containing the information we want to track
	 *
	 * @access private
	 * @var    array
	 *
	 */
	private $data;


	/**
	 * Constructor
	 *
	 */
	public function __construct() {

		add_action( 'wp', array( $this, 'schedule_event' ) );
		add_action( 'init', array( $this, 'register_admin_notice' ) );

		add_action( 'slicewp_plugin_usage_event', array( $this, 'send_usage_data' ) );

		add_action( 'slicewp_admin_action_admin_notice_allow_usage_tracking', array( $this, 'allow_usage_tracking' ) );
		add_action( 'slicewp_admin_action_admin_notice_deny_usage_tracking', array( $this, 'deny_usage_tracking' ) );

	}


	/**
	 * Schedules a daily cron event for sending plugin usage data
	 *
	 */
	public function schedule_event() {

		if( ! wp_next_scheduled ( 'slicewp_plugin_usage_event' ) )
			wp_schedule_event( time(), 'daily', 'slicewp_plugin_usage_event' );	

	}


	/**
	 * Checks if plugin usage tracking is allowed
	 *
	 * @return bool
	 *
	 */
	private function is_tracking_allowed() {

		return (bool)slicewp_get_setting( 'allow_tracking', false );

	}


	/**
	 * Sends the plugin's usage data to our server
	 *
	 */
	public function send_usage_data() {

		if( ! $this->is_tracking_allowed() )
			return;

		$last_sent = slicewp_get_option( 'plugin_usage_last_sent' );

		if( ! empty( $last_sent ) && is_numeric( $last_sent ) && $last_sent + WEEK_IN_SECONDS > time() )
			return;

		$this->setup_data();

		wp_remote_post( add_query_arg( array( 'edde_api_action' => 'register_plugin_usage_data' ), 'https://slicewp.com/' ), array( 'method' => 'POST', 'timeout' => 30, 'redirection' => 5, 'body' => $this->data ) );

		$last_sent = slicewp_update_option( 'plugin_usage_last_sent', time() );

	}


	/**
	 * Validates and handles the allow user tracking action
	 *
	 */
	public function allow_usage_tracking() {

		// Verify for nonce
		if( empty( $_GET['slicewp_token'] ) || ! wp_verify_nonce( $_GET['slicewp_token'], 'slicewp_allow_usage_tracking' ) )
			return;

		// Make sure admin notice doesn't appear again
		slicewp_update_option( 'admin_notice_usage_tracking', '1' );

		$settings = slicewp_get_option( 'settings', array() );

		$settings['allow_tracking'] = '1';

		slicewp_update_option( 'settings', $settings );

		// Redirect to the same page
		wp_redirect( remove_query_arg( array( 'slicewp_action', 'slicewp_token' ) ) );
		exit;

	}


	/**
	 * Validates and handles the deny user tracking action
	 *
	 */
	public function deny_usage_tracking() {

		// Verify for nonce
		if( empty( $_GET['slicewp_token'] ) || ! wp_verify_nonce( $_GET['slicewp_token'], 'slicewp_deny_usage_tracking' ) )
			return;

		// Make sure admin notice doesn't appear again
		slicewp_update_option( 'admin_notice_usage_tracking', '1' );

		$settings = slicewp_get_option( 'settings', array() );

		if( isset( $settings['allow_tracking'] ) ) {

			unset( $settings['allow_tracking'] );
			slicewp_update_option( 'settings', $settings );

		}

		// Redirect to the same page
		wp_redirect( remove_query_arg( array( 'slicewp_action', 'slicewp_token' ) ) );
		exit;

	}


	/**
	 * Prepares the data that we want to collect
	 *
	 */
	private function setup_data() {

		$data = array();

		$data['url']		 	 = get_site_url();
		$data['php_version'] 	 = phpversion();
		$data['wp_version']  	 = get_bloginfo( 'version' );
		$date['slicewp_version'] = SLICEWP_VERSION;
		$data['plugin_settings'] = wp_unslash( $this->get_plugin_settings() );

		// Include plugin.php file, as it's not included in the front-end
		if( ! function_exists( 'get_plugins' ) )
			include ABSPATH . '/wp-admin/includes/plugin.php';

		$all_plugins 	  = array_keys( get_plugins() );
		$active_plugins   = get_option( 'active_plugins', array() );
		$inactive_plugins = array();

		foreach( $all_plugins as $plugin_slug ) {

			if( ! in_array( $plugin_slug, $active_plugins ) )
				$inactive_plugins[] = $plugin_slug;

		}

		$data['active_plugins']   = $active_plugins;
		$data['inactive_plugins'] = $inactive_plugins;
		$data['add_ons'] 		  = $this->get_add_ons_data();

		$this->data = $data;

	}


	/**
	 * Returns the plugin's settings and filters out sensitive data
	 *
	 * @return array
	 *
	 */
	private function get_plugin_settings() {

		$settings = slicewp_get_option( 'settings', array() );

		if( ! empty( $settings['from_email'] ) )
			unset( $settings['from_email'] );

		if( ! empty( $settings['from_name'] ) )
			unset( $settings['from_name'] );

		if( ! empty( $settings['admin_emails'] ) )
			unset( $settings['admin_emails'] );

		$email_notifications = slicewp_get_available_email_notifications();

		foreach( $email_notifications as $email_notification_slug => $email_notification ) {

			if( ! empty( $settings['email_notifications'][$email_notification_slug]['subject'] ) )
				unset( $settings['email_notifications'][$email_notification_slug]['subject'] );

			if( ! empty( $settings['email_notifications'][$email_notification_slug]['content'] ) )
				unset( $settings['email_notifications'][$email_notification_slug]['content'] );

		}

		return $settings;

	}


	/**
	 * Returns an array with the SliceWP add-ons data
	 *
	 * @return array
	 *
	 */
	private function get_add_ons_data() {

		if ( ! function_exists( 'get_plugins' ) ) {
		    require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$plugins = get_plugins();
		$add_ons = array();

		foreach( $plugins as $plugin_slug => $plugin_details ) {

			if( 0 === strpos( $plugin_slug, 'slicewp-add-on' ) ) {

				$add_ons[] = array(
					'slug'    => $plugin_slug,
					'version' => get_option( 'slicewp_' . str_replace( '-', '_', str_replace( '/index.php', '', str_replace( 'slicewp-add-on-', '', $plugin_slug ) ) ) . '_version', '' ),
					'active'  => is_plugin_active( $plugin_slug )
				);
				
			}

		}

		return $add_ons;

	}


	/**
	 * Register an admin notice to ask tracking permissions
	 *
	 */
	public function register_admin_notice() {

		if( empty( $_GET['page'] ) || false === strpos( $_GET['page'], 'slicewp' ) )
			return;

		$first_activation = absint( slicewp_get_option( 'first_activation', 0 ) );

		if( empty( $first_activation ) || time() - DAY_IN_SECONDS < $first_activation )
			return;

		if( ! current_user_can( 'manage_options' ) )
			return;

		if( $this->is_tracking_allowed() )
			return;

		$notice = (bool)slicewp_get_option( 'admin_notice_usage_tracking', false );

		if( $notice )
			return;

		slicewp_admin_notices()->register_notice( 'usage_tracking', '<p><strong>' . __( 'Help us improve SliceWP', 'slicewp' ) . '</strong></p>' . '<p>' . __( "Allow SliceWP to anonymously track the plugin's usage. The collected data can help us improve the plugin and provide better features. Sensitive data will not be tracked.", 'slicewp' ) . '</p>' . '<p><a href="https://slicewp.com/docs/usage-tracking/" target="_blank">' . __( "Learn more about what we track and what we don't.", 'slicewp' ) . '</a></p>' . '<p>' . '<a href="' . wp_nonce_url( add_query_arg( array( 'slicewp_action' => 'admin_notice_allow_usage_tracking' ) ), 'slicewp_allow_usage_tracking', 'slicewp_token' ) . '" class="slicewp-button-primary">' . __( 'Allow tracking', 'slicewp' ) . '</a>' . '<a href="' . wp_nonce_url( add_query_arg( array( 'slicewp_action' => 'admin_notice_deny_usage_tracking' ) ), 'slicewp_deny_usage_tracking', 'slicewp_token' ) . '" class="slicewp-button-secondary">' . __( "Don't allow", 'slicewp' ) . '</a>' . '</p>', 'notice-info' );
		slicewp_admin_notices()->display_notice( 'usage_tracking' );

	}

}

$slicewp_plugin_usage_tracker = new SliceWP_Plugin_Usage_Tracker();