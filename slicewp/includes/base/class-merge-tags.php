<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Base class for the different merge tags used throughout the plugin
 *
 */
class SliceWP_Merge_Tags {

	/**
	 * Affiliate object
	 *
	 * @var SliceWP_Affiliate
	 * @access protected
	 *
	 */
	protected $affiliate;

	/**
	 * Commission object
	 *
	 * @var SliceWP_Commission
	 * @access protected
	 *
	 */
	protected $commission;

	/**
	 * Payout object
	 *
	 * @var SliceWP_Payout
	 * @access protected
	 *
	 */
	protected $payout;

	/**
	 * Constructor
	 *
	 * @param array $tags_data
	 *
	 */
	public function __construct( ) {

	}

	/**
	 * Setter
	 *
	 * @param string $property
	 * @param string $value
	 *
	 */
	public function set( $property = '', $value ) {

		$this->$property = $value;

	}

	/**
	 * Defines the tags that will be replaced with content
	 *
	 */	
	public function get_tags() {

		$tags = array(
			'affiliate_id' => array(
				'description'	=> __( "Replaces the tag with affiliate's id", 'slicewp'),
				'callback'		=> 'do_tag_affiliate_id'
			),
			'affiliate_username' => array(
				'description'	=> __( "Replaces the tag with the affiliate's username", 'slicewp' ),
				'callback'		=> 'do_tag_affiliate_username'
			),
			'affiliate_email' => array(
				'description'	=> __( "Replaces the tag with the affiliate's email", 'slicewp' ),
				'callback'		=> 'do_tag_affiliate_email'
			),
			'affiliate_first_name' => array(
				'description'	=> __( "replaces the tag with the affiliate's first name", 'slicewp' ),
				'callback'		=> 'do_tag_affiliate_first_name'
			),
			'affiliate_last_name' => array(
				'description'	=> __( "Replaces the tag with the affiliate's last name", 'slicewp' ),
				'callback'		=> 'do_tag_affiliate_last_name'
			),
			'affiliate_website' => array(
				'description'	=> __( "Replaces the tag with the affiliate's website", 'slicewp' ),
				'callback'		=> 'do_tag_affiliate_website'
			),
			'affiliate_status'  => array(
				'description'   => __( "Replaces the tag with the affiliate's status", 'slicewp' ),
				'callback'		=> 'do_tag_affiliate_status'
			),
			'promotional_methods' => array(
				'description'   => __( "Replaces the tag with the affiliate application promotional methods", 'slicewp' ),
				'callback'		=> 'do_tag_promotional_methods'
			),
			'reject_reason' => array(
				'description'	=> __( "Replaces the tag with the affiliate application reject reason", 'slicewp' ),
				'callback'		=> 'do_tag_reject_reason'
			),
			'commission_amount' => array(
				'description'	=> __( "Replaces the tag with the commission amount", 'slicewp' ),
				'callback'		=> 'do_tag_commission_amount'
			),
			'site_name' => array(
				'description'	=> __( "Replaces the tag with your site name", 'slicewp' ),
				'callback'		=> 'do_tag_site_name'
			),
			'page_affiliate_account' => array(
				'description'	=> __( "Replaces the tag with the link to the Affiliate Account Page", 'slicewp' ),
				'callback'		=> 'do_tag_page_affiliate_account'
			)
		);

		// Filter to add more tags
		return apply_filters( 'slicewp_merge_tags', $tags, $this );

	}

	/**
	 * The affiliate's id
	 * 
	 * @return string id
	 * 
	 */
	public function do_tag_affiliate_id() {

		if ( is_null( $this->affiliate ) )
			return '';

		return $this->affiliate->get( 'id' );

	}

	/**
	 * The affiliate's username
	 * 
	 * @return string username
	 * 
	 */
	public function do_tag_affiliate_username() {

		if ( is_null( $this->affiliate ) )
			return '';

		$user = get_userdata( $this->affiliate->get( 'user_id' ) );

		return $user->user_login;

	}

	/**
	 * The affiliate's email address
	 * 
	 * @return string email
	 * 
	 */
	public function do_tag_affiliate_email() {

		if ( is_null( $this->affiliate ) )
			return '';

		$user = get_userdata( $this->affiliate->get( 'user_id' ) );

		return $user->user_email;

	}
	
	/**
	 * The affiliate's first name
	 * 
	 * @return string first_name
	 * 
	 */
	public function do_tag_affiliate_first_name() {

		if ( is_null( $this->affiliate ) )
			return '';

		$user = get_userdata( $this->affiliate->get( 'user_id' ) );

		return $user->first_name;

	}

	/**
	 * The affiliate's last name
	 * 
	 * @return string last_name
	 * 
	 */	
	public function do_tag_affiliate_last_name() {

		if ( is_null( $this->affiliate ) )
			return '';
		
		$user = get_userdata( $this->affiliate->get( 'user_id' ) );

		return $user->last_name;

	}

	/**
	 * The affiliate's website
	 * 
	 * @return string website
	 * 
	 */	
	public function do_tag_affiliate_website() {

		if ( is_null( $this->affiliate ) )
			return '';

		return $this->affiliate->get( 'website' );

	}

	/**
	 * The affiliate's status
	 * 
	 * @return string
	 * 
	 */	
	public function do_tag_affiliate_status() {

		if( is_null( $this->affiliate ) )
			return '';

		$affiliate_statuses = slicewp_get_affiliate_available_statuses();

		if( empty( $affiliate_statuses[$this->affiliate->get( 'status' )] ) )
			return '';

		return $affiliate_statuses[$this->affiliate->get( 'status' )];

	}

	/**
	 * The promotional methods added by the affiliate on registration
	 *
	 * @return string
	 *
	 */
	public function do_tag_promotional_methods() {

		if ( is_null( $this->affiliate ) )
			return '';

		return slicewp_get_affiliate_meta( $this->affiliate->get( 'id' ), 'promotional_methods', true );

	}

	/**
	 * The reject reason
	 * 
	 * @return string reject_reason
	 * 
	 */	
	public function do_tag_reject_reason() {

		if ( is_null( $this->affiliate ) )
			return '';

		return slicewp_get_affiliate_meta( $this->affiliate->get( 'id' ), 'reject_reason', true );

	}

	/**
	 * The commission amount
	 * 
	 * @return string commission_amount
	 * 
	 */
	public function do_tag_commission_amount() {

		if ( is_null( $this->commission ) )
			return '';
		
		return slicewp_format_amount( $this->commission->get( 'amount' ), $this->commission->get( 'currency' ) );		

	}

	/**
	 * The site name
	 * 
	 * @return string site_name
	 * 
	 */
	public function do_tag_site_name() {
		
		return wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );

	}

	/**
	 * The site name
	 * 
	 * @return string site_name
	 * 
	 */
	public function do_tag_page_affiliate_account() {
		
		$page = slicewp_get_setting( 'page_affiliate_account', 0 );

		if ( empty( $page ) )
			return '';
		else
			return get_permalink( $page );

	}

	/**
	 * Replaces the merge tags with the corresponding data in the given content
	 *
	 * @param string $content
	 *
	 * @return string
	 *
	 */
	public function replace_tags( $content ) {

		$tags = $this->get_tags();

		foreach ( $tags as $tag_slug => $tag ) {

			if ( method_exists( $this, $tag['callback'] ) ) {

				$callback  = $tag['callback'];
				$tag_value = $this->$callback();

				$content = str_replace( '{{'. $tag_slug . '}}', $tag_value, $content );
			
			}
		}

		return $content;

	}

}