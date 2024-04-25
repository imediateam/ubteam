<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Base class for all integrations
 *
 */
abstract Class SliceWP_Integration {

	/**
	 * The name of the integration
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $name = '';

	/**
	 * The supports array of the integration
	 *
	 * @access protected
	 * @var    array
	 *
	 */
	protected $supports = array();

	/**
	 * Constructor
	 *
	 * Subclasses should set the class properties
	 *
	 * @access public
	 *
	 */
	public function __construct() {}

	/**
	 * Getter
	 *
	 * @param string $property
	 *
	 */
	public function get( $property = '' ) {

		if( method_exists( $this, 'get_' . $property ) )
			return $this->{'get_' . $property}();
		else
			return $this->$property;

	}

}