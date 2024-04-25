<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * The main class for the Visit
 *
 */
class SliceWP_Visit extends SliceWP_Base_Object {

	/**
	 * The id of the visit
	 *
	 * @access protected
	 * @var    int
	 *
	 */
	protected $id;

	/**
	 * The id of the affiliate that reffered the visit
	 *
	 * @access protected
	 * @var    int
	 *
	 */
	protected $affiliate_id;

	/**
	 * The date when the visit was created
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $date_created;

	/**
	 * The IP address of the visit
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $ip_address;

	/**
	 * The landing url of the visit
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $landing_url;

	/**
	 * The referrer url that lead to the visit
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $referrer_url;

	/**
	 * The id of the commission resulted from the visit
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $commission_id;
}