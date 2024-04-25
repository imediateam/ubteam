<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * The main class for the Commission
 *
 */
class SliceWP_Commission extends SliceWP_Base_Object {

	/**
	 * The id of the commission
	 *
	 * @access protected
	 * @var    int
	 *
	 */
	protected $id;

	/**
	 * The id of the affiliate receiving the commission
	 *
	 * @access protected
	 * @var    int
	 *
	 */
	protected $affiliate_id;

	/**
	 * The id of the visit that led to the commission
	 *
	 * @access protected
	 * @var    int
	 *
	 */
	protected $visit_id;

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
	 * The type of commission
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $type;

	/**
	 * The status of the commission
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $status;

	/**
	 * The reference tied to this commission
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $reference;

	/**
	 * The slug for the entity that generated the commission
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $origin;

	/**
	 * The amount of the commission
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $amount;

	/**
	 * The ID of the parent commissions
	 *
	 * @access protected
	 * @var    int
	 *
	 */
	protected $parent_id;

	/**
	 * The currency of the commission
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $currency;

}