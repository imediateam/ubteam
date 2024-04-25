<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class that handles user notice registration and output
 *
 */
Class SliceWP_User_Notices {

	/**
	 * The current instance of the object
	 *
	 * @access private
	 * @var    SliceWP_User_Notices
	 *
	 */
	private static $instance;

	/**
	 * List of notices that have been registered
	 *
	 * @access protected
	 * @var    array
	 *
	 */
	protected $notices = array();

	/**
	 * List of notices that will be printed in the page
	 *
	 * @access protected
	 * @var    array
	 *
	 */
	protected $display_notices = array();


	/**
	 * Constructor
	 *
	 */
	public function __construct() {

		add_action( 'slicewp_user_init', array( $this, 'catch_url_user_notice' ), 100 );
		add_action( 'slicewp_user_notices', array( $this, 'print_notices' ) );

	}


	/**
	 * Returns an instance of the object
	 *
	 * @return SliceWP_User_Notices
	 *
	 */
	public static function instance() {

		if( ! isset( self::$instance ) && ! ( self::$instance instanceof SliceWP_User_Notices ) )
			self::$instance = new SliceWP_User_Notices;

		return self::$instance;

	}


	/**
	 * Adds a new user notice to the $notices property
	 *
	 * @param string $slug
	 * @param string $message
	 * @param string $class
	 *
	 */
	public function register_notice( $slug, $message, $class = 'updated' ) {

		$this->notices[$slug] = array(
			'message' => $message,
			'class'   => $class
		);

	}


	/**
	 * Prepares a registered user notice for printing on user_notices action
	 *
	 * @param string $slug
	 *
	 */
	public function display_notice( $slug ) {

		$this->display_notices[] = $slug;

	}


	/**
	 * Outputs or returns the HTML of a registered user notice
	 *
	 * @param string $notice_slug
	 * @param bool   $return
	 *
	 * @return void|string
	 *
	 */
	public function output_notice( $notice_slug, $return = false ) {

		$output = '<div class="slicewp-' . $this->notices[$notice_slug]['class'] . ' slicewp-user-notice">';
            $output .= $this->notices[$notice_slug]['message'];
        $output .= '</div>';

        if( $return )
        	return $output;
        else
        	echo $output;

	}


	/**
	 * Callback function to print user notices
	 *
	 */
	public function print_notices() {

		if( ! isset( $this->display_notices ) )
            return;
        
        foreach( $this->display_notices as $notice_slug ) {

        	if( empty( $this->notices[$notice_slug] ) )
        		continue;

        	$this->output_notice( $notice_slug );

        }

	}


	/**
     * Catches messages sent through the URL
     *
     */
    public function catch_url_user_notice() {

        if( empty( $_POST['slicewp_message'] ) )
            return;

        $message_slug = sanitize_text_field( $_POST['slicewp_message'] );

        if( ! empty( $this->notices[$message_slug] ) )
            $this->display_notice( $message_slug );

    }


}


/**
 * Returns the instance of SliceWP_User_Notices
 *
 * @return SliceWP_User_Notices
 *
 */
function slicewp_user_notices() {

	return SliceWP_User_Notices::instance();

}

slicewp_user_notices();