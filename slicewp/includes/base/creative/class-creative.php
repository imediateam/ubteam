<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * The main class for the Creative
 *
 */
class SliceWP_Creative extends SliceWP_Base_Object {

	/**
	 * The id of the creative
	 *
	 * @access protected
	 * @var    int
	 *
	 */
	protected $id;

	/**
	 * The name of the creative
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $name;

    /**
	 * The description of the creative
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $description;

	/**
	 * The date when the creative was created
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $date_created;

	/**
	 * The date when the creative was last modified
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $date_modified;

	/**
	 * The type of creative
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $type;

	/**
	 * The image_url of the creative
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $image_url;

	/**
	 * The alt_text of the creative
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $alt_text;

	/**
	 * The text of the creative
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $text;

	/**
	 * The landing_url of the creative
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $landing_url;
	
	/**
	 * The status of the creative
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $status;
}