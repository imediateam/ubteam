<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * The main class for the Payment
 *
 */
class SliceWP_Payment extends SliceWP_Base_Object {

	/**
	 * The id of the payment
	 *
	 * @access protected
	 * @var    int
	 *
	 */
	protected $id;

	/**
	 * The id of the affiliate receiving the payment
	 *
	 * @access protected
	 * @var    int
	 *
	 */
	protected $affiliate_id;

	/**
	 * The id of the commissions that are included in the payment
	 *
	 * @access protected
	 * @var    int
	 *
	 */
	protected $commission_ids;

	/**
	 * The amount of the payment
	 *
	 * @access protected
	 * @var    int
	 *
	 */
	protected $amount;
	
  	/**
	 * The currency of the payment
	 *
	 * @access protected
	 * @var    int
	 *
	 */
	protected $currency;
	
    /**
	 * The admin that generated the payment
	 *
	 * @access protected
	 * @var    int
	 *
	 */
    protected $admin_id;
    
    /**
	 * The date when the payment was created
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $date_created;

	/**
	 * The date when the payment was last modified
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $date_modified;

	/**
	 * The payout method of the payment
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $payout_method;

	/**
	 * The status of the payment
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $status;

	/**
	 * The payout id the payment belongs to
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $payout_id;

}