<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * The main class for the Affiliate
 *
 */
class SliceWP_Affiliate extends SliceWP_Base_Object {

	/**
	 * The Id of the booking
	 *
	 * @access protected
	 * @var    int
	 *
	 */
	protected $id;

	/**
	 * The id of the user to which this affiliate is assigned
	 *
	 * @access protected
	 * @var    int
	 *
	 */
	protected $user_id;

	/**
	 * The date when the affiliate was created
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $date_created;

	/**
	 * The date when the affiliate was last modified
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $date_modified;

	/**
	 * The status of the affiliate
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $status;

	/**
	 * The payment email of the affiliate
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $payment_email;

	/**
	 * The website of the affiliate
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $website;
	
}