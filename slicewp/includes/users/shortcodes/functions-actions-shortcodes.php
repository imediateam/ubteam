<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Registers new affiliate account
 *
 */
function slicewp_user_action_register_affiliate() {

    // Verify if affiliate registration is allowed
    $register_affiliate = slicewp_get_setting( 'allow_affiliate_registration' );
    
    if ( empty( $register_affiliate ) )
        return;
        
    // Verify for nonce
	if ( empty( $_POST['slicewp_token'] ) || ! wp_verify_nonce( $_POST['slicewp_token'], 'slicewp_register_affiliate' ) )
		return;

    if ( ! is_user_logged_in() ) {

        // Verify for Username
        if ( empty( $_POST['username'] ) ) {

            slicewp_user_notices()->register_notice( 'username_empty_error', '<p>' . __( 'Please fill in your Username.', 'slicewp' ) . '</p>', 'error' );
            slicewp_user_notices()->display_notice( 'username_empty_error' );

            return;
        
        }
        
        // Verify for First Name
        if ( empty( $_POST['first_name'] ) ) {

            slicewp_user_notices()->register_notice( 'first_name_empty_error', '<p>' . __( 'Please fill in your First Name.', 'slicewp' ) . '</p>', 'error' );
            slicewp_user_notices()->display_notice( 'first_name_empty_error' );

            return;
        
        }

        // Verify for Last Name
        if ( empty( $_POST['last_name'] ) ) {

            slicewp_user_notices()->register_notice( 'last_name_empty_error', '<p>' . __( 'Please fill in your Last Name.', 'slicewp' ) . '</p>', 'error' );
            slicewp_user_notices()->display_notice( 'last_name_empty_error' );

            return;
        
        }

        // Verify for Email
        if ( empty( $_POST['email'] ) ) {

            slicewp_user_notices()->register_notice( 'email_empty_error', '<p>' . __( 'Please fill in your Email.', 'slicewp' ) . '</p>', 'error' );
            slicewp_user_notices()->display_notice( 'email_empty_error' );

            return;
        
        }

        // Verify for valid Email
        if ( ! is_email( $_POST['email'] ) ) {

            slicewp_user_notices()->register_notice( 'email_invalid_error', '<p>' . __( 'Please fill in a valid Email.', 'slicewp' ) . '</p>', 'error' );
            slicewp_user_notices()->display_notice( 'email_invalid_error' );

            return;
        
        }
        
        // Verify for Password
        if ( empty( $_POST['password'] ) ) {

            slicewp_user_notices()->register_notice( 'password_empty_error', '<p>' . __( 'Please choose a Password.', 'slicewp' ) . '</p>', 'error' );
            slicewp_user_notices()->display_notice( 'password_empty_error' );

            return;
        
        }

        // Verify for Passwords to match
        if ( $_POST['password'] != $_POST['password_confirmation'] ) {

            slicewp_user_notices()->register_notice( 'password_confirmation_error', '<p>' . __( 'The typed passwords do not match.', 'slicewp' ) . '</p>', 'error' );
            slicewp_user_notices()->display_notice( 'password_confirmation_error' );

            return;
    
        }

        // Verify if the Username is available
        if ( username_exists( $_POST['username'] ) ){


            slicewp_user_notices()->register_notice( 'username_exists_error', '<p>' . sprintf( __( 'The username <strong>%s</strong> is already registered.', 'slicewp' ) , $_POST['username'] ) . '</p>', 'error' );
            slicewp_user_notices()->display_notice( 'username_exists_error' );

            return;

        }

        // Verify if the email address is already used
        if ( email_exists( $_POST['email'] ) ){

            slicewp_user_notices()->register_notice( 'email_exists_error', '<p>' . sprintf( __( 'The email address <strong>%s</strong> is already registered.', 'slicewp' ), $_POST['email'] ) . '</p>', 'error' );
            slicewp_user_notices()->display_notice( 'email_exists_error' );

            return;

        }
    }

    // Verify for Payment Email
    if ( slicewp_get_setting( 'required_field_payment_email' ) && empty( $_POST['payment_email'] ) ) {

        slicewp_user_notices()->register_notice( 'payment_email_empty_error', '<p>' . __( 'Please fill in your Payment Email.', 'slicewp' ) . '</p>', 'error' );
        slicewp_user_notices()->display_notice( 'payment_email_empty_error' );

        return;
    
    }

    // Verify for valid Payment Email
    if ( ! empty( $_POST['payment_email'] ) && ! is_email( $_POST['payment_email'] ) ) {

        slicewp_user_notices()->register_notice( 'payment_email_invalid_error', '<p>' . __( 'Please fill in a valid Payment Email.', 'slicewp' ) . '</p>', 'error' );
        slicewp_user_notices()->display_notice( 'payment_email_invalid_error' );

        return;
    
    }

    // Verify for Website
    if ( slicewp_get_setting( 'required_field_website' ) && empty( $_POST['website'] ) ) {

        slicewp_user_notices()->register_notice( 'website_empty_error', '<p>' . __( 'Please fill in your Website.', 'slicewp' ) . '</p>', 'error' );
        slicewp_user_notices()->display_notice( 'website_empty_error' );

        return;
    
    }

    // Verify for valid Website
    if ( ! empty( $_POST['website'] ) && filter_var( $_POST['website'], FILTER_VALIDATE_URL ) === FALSE) {

        slicewp_user_notices()->register_notice( 'website_invalid_error', '<p>' . __( 'Please provide a valid Website URL.', 'slicewp' ) . '</p>', 'error' );
        slicewp_user_notices()->display_notice( 'website_invalid_error' );

        return;
    
    }

    // Verify for Promotional Methods
    if ( slicewp_get_setting( 'required_field_promotional_methods' ) && empty( $_POST['promotional_methods'] ) ) {

        slicewp_user_notices()->register_notice( 'promotional_methods_empty_error', '<p>' . __( 'Please fill how you will promote us.', 'slicewp' ) . '</p>', 'error' );
        slicewp_user_notices()->display_notice( 'promotional_methods_empty_error' );
    
		return;

    }

    // Verify for Terms and Conditions
    $page_terms_conditions = slicewp_get_setting( 'page_terms_conditions' );

    if ( ! empty( $page_terms_conditions ) ) {

        if( empty( $_POST['terms_conditions'] ) ) {

            slicewp_user_notices()->register_notice( 'terms_empty_error', '<p>' . __( 'You must agree with our Terms and Conditions.', 'slicewp' ) . '</p>', 'error' );
            slicewp_user_notices()->display_notice( 'terms_empty_error' );
        
            return;
    
        }

    }

    // Verify for reCAPTCHA
    $recaptcha = slicewp_get_setting( 'enable_recaptcha' );

    if( ! empty( $recaptcha ) && ! slicewp_is_recaptcha_valid( $_POST ) ) {

        slicewp_user_notices()->register_notice( 'recaptcha_invalid_error', '<p>' . __( 'Please complete the reCAPTCHA.', 'slicewp' ) . '</p>', 'error' );
        slicewp_user_notices()->display_notice( 'recaptcha_invalid_error' );
    
        return;

    }

    if ( ! is_user_logged_in() ) {

        // Prepare user data to be inserted in db
        $userdata = array(
            'user_login'    => sanitize_user( $_POST['username'] ),
            'user_pass'     => trim( $_POST['password'] ),
            'user_email'    => sanitize_email( $_POST['email'] ),
            'first_name'    => sanitize_text_field( $_POST['first_name'] ),
            'last_name'     => sanitize_text_field( $_POST['last_name'] )
        );

        // Insert user data
        $user_id = wp_insert_user( $userdata );
        
        // Verify if user was inserted successfully
        if ( is_wp_error( $user_id ) ) {

            slicewp_user_notices()->register_notice( 'username_insert_error', '<p>' . __( 'Username could not be created! Please try again later!', 'slicewp' ) . '</p>', 'error' );
            slicewp_user_notices()->display_notice( 'username_insert_error' );

            return;
        
        }

        // Prepare the credentials for login
        $credentials = array(
            'user_login'    => sanitize_user( $_POST['username'] ),
            'user_password' => trim( $_POST['password'] )
        );
        
        // Login the user
        $user = wp_signon( $credentials, '' );

    } else {

        $user_id = get_current_user_id();
    
    }

    // Verify the status to be used for the affiliate
    $affiliate_register_status_active = slicewp_get_setting( 'affiliate_register_status_active' );

    // Prepare affiliate data to be inserted in db
    $affiliate_data = array(
        'user_id' 		=> absint( $user_id ),
        'date_created'  => slicewp_mysql_gmdate(),
        'date_modified' => slicewp_mysql_gmdate(),
        'payment_email' => ( ! empty( $_POST['payment_email'] ) ? sanitize_email( $_POST['payment_email'] ) : '' ),
        'website'       => ( ! empty( $_POST['website'] ) ? esc_url( $_POST['website'] ) : '' ),
        'status'		=> ( ( $affiliate_register_status_active == 1 ) ? 'active' : 'pending' )
    );

    // Insert affiliate in db
    $affiliate_id = slicewp_insert_affiliate( $affiliate_data );
    
    // Verify if affiliate was inserted succesfully
    if ( empty( $affiliate_id ) ) {

        slicewp_user_notices()->register_notice( 'affiliate_insert_error', '<p>' . __( 'Affiliate account could not be created!', 'slicewp' ) . '</p>', 'error' );
        slicewp_user_notices()->display_notice( 'affiliate_insert_error' );

        return;
    
    }

    // Add extra meta-data
    if( ! empty( $_POST['promotional_methods'] ) )
        slicewp_add_affiliate_meta( $affiliate_id, 'promotional_methods', sanitize_textarea_field( $_POST['promotional_methods'] ), true );

    /**
     * Executes right after the user and affiliate have been added to the database
     *
     * @param int $affiliate_id
     *
     */
    do_action( 'slicewp_register_affiliate', $affiliate_id );

    // Redirect to the Affiliate Account Page
    if ( ! empty( $_POST['redirect_url'] ) ) {

        wp_redirect( $_POST['redirect_url'] );
        exit;
    
    }

	// Redirect to the Register Page with success message
    wp_redirect( add_query_arg( array( 'success' => 1 ) ) );
    exit;

}
add_action( 'slicewp_user_action_register_affiliate', 'slicewp_user_action_register_affiliate', 50 );


/**
 * Login for the affiliate
 *
 */
function slicewp_user_action_login_affiliate() {

    // Verify for nonce
	if( empty( $_POST['slicewp_token'] ) || ! wp_verify_nonce( $_POST['slicewp_token'], 'slicewp_login_affiliate' ) )
        return;

    // Verify for Login
	if( empty( $_POST['login'] ) ) {

        slicewp_user_notices()->register_notice( 'login_empty_error', '<p>' . __( 'Please fill in your Username / Email address.', 'slicewp' ) . '</p>', 'error' );
        slicewp_user_notices()->display_notice( 'login_empty_error' );
    
        return;
    
    }

    // Verify for Password
    if( empty( $_POST['password'] ) ) {

        slicewp_user_notices()->register_notice( 'password_empty_error', '<p>' . __( 'Please fill in your Password.', 'slicewp' ) . '</p>', 'error' );
        slicewp_user_notices()->display_notice( 'password_empty_error' );

        return;
    
    }

    // Verify if the field contains an email address
    if ( is_email( $_POST['login'] ) ) {

        if ( ! email_exists( $_POST['login'] ) ) {

            slicewp_user_notices()->register_notice( 'email_not_registered_error', '<p>' . sprintf( __( 'Unable to login. Please try again.', 'slicewp' ), $_POST['login'] ) . '</p>', 'error' );
            slicewp_user_notices()->display_notice( 'email_not_registered_error' );

            return;

        }

    } else {
        
        if ( ! username_exists( $_POST['login'] ) ) {

            slicewp_user_notices()->register_notice( 'username_not_registered_error', '<p>' . sprintf( __( 'Unable to login. Please try again.', 'slicewp' ), $_POST['login'] ) . '</p>', 'error' );
            slicewp_user_notices()->display_notice( 'username_not_registered_error' );

            return;

        }
    }

    // Prepare the credentials for login
    $credentials = array(
        'user_login'    => is_email( $_POST['login'] ) ? sanitize_email( $_POST['login'] ) : sanitize_user( $_POST['login'] ),
        'user_password' => trim( $_POST['password'] )
    );
    
    // Login the user
    $user = wp_signon( $credentials, '' );

    if ( is_wp_error( $user ) ){
 
        slicewp_user_notices()->register_notice( 'affiliate_login_error', '<p>' . __( 'Unable to login. Please try again.' , 'slicewp' ) . '</p>', 'error' );
        slicewp_user_notices()->display_notice( 'affiliate_login_error' );

        return;
    }
    
    // Redirect to the Affiliate Account Page
    if ( ! empty( $_POST['redirect_url'] ) ) {

        wp_redirect( $_POST['redirect_url'] );
        exit;
    
    }
    
	// Redirect to the Login Page with success message
    wp_redirect( add_query_arg( array( 'success' => 1 ) ) );
    exit;

}
add_action( 'slicewp_user_action_login_affiliate', 'slicewp_user_action_login_affiliate', 50 );


/**
 * Update the affiliate settings
 *
 */
function slicewp_user_action_update_affiliate_settings() {

    // Verify for nonce
	if ( empty( $_POST['slicewp_token'] ) || ! wp_verify_nonce( $_POST['slicewp_token'], 'slicewp_update_affiliate_settings' ) )
        return;

    // Verify for Payment Email
    if ( slicewp_get_setting( 'required_field_payment_email' ) && empty( $_POST['payment_email'] ) ) {

        slicewp_user_notices()->register_notice( 'payment_email_empty_error', '<p>' . __( 'Please fill in your Payment Email.', 'slicewp' ) . '</p>', 'error' );
        slicewp_user_notices()->display_notice( 'payment_email_empty_error' );

        return;
    
    }

    // Verify for valid Payment Email
    if ( ! empty( $_POST['payment_email'] ) && ! is_email( $_POST['payment_email'] ) ) {

        slicewp_user_notices()->register_notice( 'payment_email_invalid_error', '<p>' . __( 'Please fill in a valid Payment Email.', 'slicewp' ) . '</p>', 'error' );
        slicewp_user_notices()->display_notice( 'payment_email_invalid_error' );

        return;
    
    }

    // Verify for Website
    if ( slicewp_get_setting( 'required_field_website' ) && empty( $_POST['website'] ) ) {

        slicewp_user_notices()->register_notice( 'website_empty_error', '<p>' . __( 'Please fill in your Website.', 'slicewp' ) . '</p>', 'error' );
        slicewp_user_notices()->display_notice( 'website_empty_error' );

        return;
    
    }

    // Verify for valid Website
    if ( ! empty( $_POST['website'] ) && filter_var( $_POST['website'], FILTER_VALIDATE_URL ) === FALSE) {

        slicewp_user_notices()->register_notice( 'website_invalid_error', '<p>' . __( 'Please provide a valid Website URL.', 'slicewp' ) . '</p>', 'error' );
        slicewp_user_notices()->display_notice( 'website_invalid_error' );

        return;
    
    }

    // Get the affiliate id
    $affiliate    = slicewp_get_affiliate_by_user_id( get_current_user_id() );
    $affiliate_id = $affiliate->get('id');

	// Prepare affiliate data to be updated
	$affiliate_data = array(
		'date_modified' => slicewp_mysql_gmdate(),
        'payment_email'	=> ( ! empty( $_POST['payment_email'] ) ? sanitize_text_field( $_POST['payment_email'] ) : '' ),
        'website'       => ( ! empty( $_POST['website'] ) ? esc_url( $_POST['website'] ) : '' )
	);

	// Update affiliate into the database
	$updated = slicewp_update_affiliate( $affiliate_id, $affiliate_data );

    // If the affiliate could not be updated show a message to the user
	if( ! $updated ) {

		slicewp_user_notices()->register_notice( 'affiliate_update_false', '<p>' . __( 'Something went wrong. Could not update the settings. Please try again.', 'slicewp' ) . '</p>', 'error' );
		slicewp_user_notices()->display_notice( 'affiliate_update_false' );

		return;

    }

    // Redirect to the Affiliate Account Page with success message
    slicewp_user_notices()->register_notice( 'affiliate_settings_saved', '<p>' . __( 'Settings saved!', 'slicewp' ) . '</p>', 'updated' );
    slicewp_user_notices()->display_notice( 'affiliate_settings_saved' );
    
    return;
    
}
add_action( 'slicewp_user_action_update_affiliate_settings', 'slicewp_user_action_update_affiliate_settings', 50 );
