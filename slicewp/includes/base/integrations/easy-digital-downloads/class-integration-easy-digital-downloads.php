<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * The class that defines the WooCommerce integration
 *
 */
Class SliceWP_Integration_Easy_Digital_Downloads extends SliceWP_Integration {

	/**
	 * Constructor
	 *
	 */
	public function __construct() {

		/**
		 * Set the name of the integration
		 *
		 */
		$this->name = 'Easy Digital Downloads';

		/**
		 * Set the supports values
		 *
		 */
		$supports = array(
			'commission_types' => array( 'sale' )
		);

		/**
		 * Filter the supports array
		 *
		 * @param array $supports
		 *
		 */
		$this->supports = apply_filters( 'slicewp_integration_supports_edd', $supports );

	}

}