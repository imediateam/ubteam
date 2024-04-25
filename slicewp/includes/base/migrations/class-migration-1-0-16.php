<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Class SliceWP_Migration_1_0_16
 *
 * Migration that runs when updating to version 1.0.16
 *
 */
class SliceWP_Migration_1_0_16 extends SliceWP_Abstract_Migration {

	/**
	 * Constructor
	 *
	 */
	public function __construct() {

		$this->id          = 'slicewp-update-1-0-16';
		$this->notice_type = 'none';

		parent::__construct();

	}


	/**
	 * Actually run the migration
	 *
	 */
	public function migrate() {

		// Get the general settings
		$settings = slicewp_get_option( 'settings' );

		if( ! empty( $settings ) && is_array( $settings ) ) {

			$settings['required_field_payment_email'] = 1;
			$settings['required_field_website'] = 1;
			$settings['required_field_promotional_methods'] = 1;

			// Update the general settings with the new value
			slicewp_update_option( 'settings', $settings );
			
		}

		return true;

	}

}