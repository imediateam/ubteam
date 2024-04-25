<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * The class that defines the Paid Member Subscriptions integration
 *
 */
Class SliceWP_Integration_Paid_Member_Subscriptions extends SliceWP_Integration {

	/**
	 * Constructor
	 *
	 */
	public function __construct() {

		/**
		 * Set the name of the integration
		 *
		 */
		$this->name = 'Paid Member Subscriptions';

		/**
		 * Set the supports values
		 *
		 */
		$supports = array(
			'commission_types' => array( 'subscription' )
		);

		/**
		 * Filter the supports array
		 *
		 * @param array $supports
		 *
		 */
		$this->supports = apply_filters( 'slicewp_integration_supports_pms', $supports );

	}

}