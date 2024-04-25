<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Generates the shortcode for affiliate registration
 *
 */
function slicewp_shortcode_affiliate_registration( $atts ) {

    if( is_admin() )
        return;

    if( defined( 'REST_REQUEST' ) && REST_REQUEST )
        return;

    // Enqueue scripts
    wp_enqueue_script( 'slicewp-script' );
    
    // Prepare the default attributes
    $atts = shortcode_atts( array(
        'redirect_url' => ''
    ), $atts );

    // Verify if affiliate registration is allowed
    $register_affiliate = slicewp_get_setting( 'allow_affiliate_registration' );
    
    if ( empty( $register_affiliate ) && current_user_can( 'administrator' ) )
        return __( 'You see this message because you\'re an administrator of this website. The affiliate registrations are disabled! ', 'slicewp' );

    if ( empty( $register_affiliate ) )
        return;

    // Verify if the Affiliate Account Page is set in Settings
    if ( empty( $atts['redirect_url'] ) ) {

        $page_id = slicewp_get_setting( 'page_affiliate_account' , 0 );
        
        if ( ! empty( $page_id ) ) {

            $atts['redirect_url'] = get_permalink( $page_id );
        
        }

    }
    
    // Verify if the user is logged in after registration with success and show a notification
    if ( is_user_logged_in() && ! empty( $_GET['success'] ) ) {

        slicewp_user_notices()->register_notice( 'user_registered_success', '<p>' . __( 'Your account was registered successfully!', 'slicewp' ) . '</p>', 'updated' );

        return slicewp_user_notices()->output_notice( 'user_registered_success', true );

    }

    // Verify if the user is logged in
    if ( is_user_logged_in() ) {

        $user = wp_get_current_user();
        $affiliate = slicewp_get_affiliate_by_user_id( $user->ID );

    }

    // Show the registration form
    if ( empty( $affiliate ) ) {

        // Include the register template
        $dir_path = plugin_dir_path( __FILE__ );

        ob_start();

        if ( file_exists( $dir_path . 'templates/template-register.php' ) )
            include $dir_path . 'templates/template-register.php';

        $return = ob_get_contents();

        ob_end_clean();

        // Show the registration form
        return $return;

    } else {

        slicewp_user_notices()->register_notice( 'user_already_registered', '<p>' . __( 'You are already registered!', 'slicewp' ) . '</p>', 'warning' );

        return slicewp_user_notices()->output_notice( 'user_already_registered', true );
        
    }

}
add_shortcode( 'slicewp_affiliate_registration', 'slicewp_shortcode_affiliate_registration' );


/**
 * Generates the shortcode for affiliate login
 *
 */
function slicewp_shortcode_affiliate_login( $atts ) {

    if( is_admin() )
        return;

    if( defined( 'REST_REQUEST' ) && REST_REQUEST )
        return;

    // Enqueue scripts
    wp_enqueue_script( 'slicewp-script' );
    
    // Prepare the default attributes
    $atts = shortcode_atts( array(
        'redirect_url' => ''
    ), $atts );


    // Verify if the user was logged in with success message and display a notification
    if ( is_user_logged_in() && ! empty( $_GET['success'] ) ){

        slicewp_user_notices()->register_notice( 'user_login_success', '<p>' . __( 'You are now logged in!', 'slicewp' ) . '</p>', 'updated' );
        
        return slicewp_user_notices()->output_notice( 'user_login_success', true );

    }

    // Verify if the user is already logged in
    if ( is_user_logged_in() ){

        slicewp_user_notices()->register_notice( 'user_already_logged_in', '<p>' . __( 'You are already logged in!', 'slicewp' ) . ' ' . sprintf( __( '%1$sClick here if you wish to logout.%2$s', 'slicewp' ), '<a href="' . wp_logout_url( slicewp_get_current_page_url() ) . '">', '</a>' ) . '</p>', 'warning' );

        return slicewp_user_notices()->output_notice( 'user_already_logged_in', true );

    }

    // Verify if the Affiliate Account Page is set in Settings
    if ( empty( $atts['redirect_url'] ) ) {

        $page_id = slicewp_get_setting( 'page_affiliate_account' , 0 );
        
        if ( ! empty( $page_id ) ){

            $atts['redirect_url'] = get_permalink( $page_id );
        
        }

    }

    // Include the login template
    $dir_path = plugin_dir_path( __FILE__ );

    ob_start();

    if ( file_exists( $dir_path . 'templates/template-login.php' ) )
        include $dir_path . 'templates/template-login.php';
   
    $return = ob_get_contents();

    ob_end_clean();

    // Show the login form
    return $return;

}
add_shortcode( 'slicewp_affiliate_login', 'slicewp_shortcode_affiliate_login' );


/**
 * Generates the shortcode for affiliate account
 *
 */
function slicewp_shortcode_affiliate_account() {

    if( is_admin() )
        return;

    if( defined( 'REST_REQUEST' ) && REST_REQUEST )
        return;

    // Enqueue scripts
    wp_enqueue_script( 'slicewp-script' );
    
    // Verify if the user is logged in
    if( ! is_user_logged_in() ) {

        slicewp_user_notices()->register_notice( 'user_not_logged_in', '<p>' . __( 'You are not logged in!', 'slicewp' ) . '</p>', 'warning' );
        
        return slicewp_user_notices()->output_notice( 'user_not_logged_in', true );

    }

    // Verify if the user is affiliate
    $user = wp_get_current_user();
    $user_id = $user->ID;
    $affiliate = slicewp_get_affiliate_by_user_id( $user_id );

    // Include the login template
    $dir_path = plugin_dir_path( __FILE__ );

    // Check if the Affiliate Registration is allowed
    $register_affiliate = slicewp_get_setting( 'allow_affiliate_registration' );

    if( empty( $affiliate ) && empty( $register_affiliate ) ) {

        slicewp_user_notices()->register_notice( 'user_not_affiliate', '<p>' . __( 'Your account does not have affiliate privileges!', 'slicewp' ) . '</p>', 'warning' );
        
        return slicewp_user_notices()->output_notice( 'user_not_affiliate', true );

    }

    ob_start();
    
    //Show the registration form if the user is registered but it's not affiliate
    if( empty( $affiliate ) ) {

        slicewp_user_notices()->register_notice( 'user_not_affiliate_warning', '<p>' . __( 'Your account is not enrolled in our affiliate program. Fill the form below if you want to apply.', 'slicewp' ) . '</p>', 'warning' );
        slicewp_user_notices()->display_notice( 'user_not_affiliate_warning' );

        if( file_exists( $dir_path . 'templates/template-register.php' ) )
            include $dir_path . 'templates/template-register.php';

    } else {

        // Check the affiliate status and show the appropiate message
        if( $affiliate->get('status') == 'pending' ) {

            slicewp_user_notices()->register_notice( 'affiliate_account_pending', '<p>' . __( 'Your affiliate account is currently being reviewed.', 'slicewp' ) . '</p>', 'warning' );
            slicewp_user_notices()->output_notice( 'affiliate_account_pending' );
           
        }

        if( $affiliate->get('status') == 'rejected' ) {

            slicewp_user_notices()->register_notice( 'affiliate_account_rejected', '<p>' . __( 'Your affiliate application has been rejected.', 'slicewp' ) . '</p>', 'warning' );
            slicewp_user_notices()->output_notice( 'affiliate_account_rejected' );

        }

        if( $affiliate->get('status') == 'inactive' ) {

            slicewp_user_notices()->register_notice( 'affiliate_account_inactive', '<p>' . __( 'Your affiliate account is not active.', 'slicewp' ) . '</p>', 'warning' );
            slicewp_user_notices()->output_notice( 'affiliate_account_inactive' );

        }

        if( $affiliate->get('status') == 'active' ) {

            if ( file_exists( $dir_path . 'templates/template-affiliate-account.php' ) )
                include $dir_path . 'templates/template-affiliate-account.php';
        
        }

    }
    
    $return = ob_get_contents();

    ob_end_clean();

    // Show the Affiliate Account page
    return $return;

}
add_shortcode( 'slicewp_affiliate_account', 'slicewp_shortcode_affiliate_account' );


/**
 * Generates the shortcode for creative
 *
 */
function slicewp_shortcode_creative( $atts ) {

    if( is_admin() )
        return;

    if( defined( 'REST_REQUEST' ) && REST_REQUEST )
        return;

    // Enqueue scripts
    wp_enqueue_script( 'slicewp-script' );
    
    // Prepare the default attributes
    $atts = shortcode_atts( array(
        'id' => ''
    ), $atts );

    if ( empty( $atts ) )
        return;

    // Verify if the user is logged in
    if ( ! is_user_logged_in() )
        return;

    // Verify if the user is affiliate
    if ( ! slicewp_is_user_affiliate() )
        return;

    $creative = slicewp_get_creative( absint( $atts['id'] ) );
    
    // Verify if the creative exists
    if ( empty( $creative ) )
        return;

    // Verify if the creative is active
    if ( $creative->get('status') == 'inactive' )
        return;

    // Include the creative template
    $dir_path = plugin_dir_path( __FILE__ );

    ob_start();
    
    if ( file_exists( $dir_path . 'templates/template-creative.php' ) )
        include $dir_path . 'templates/template-creative.php';
    
    // Show the Creative
    return ob_get_clean();

}
add_shortcode( 'slicewp_creative', 'slicewp_shortcode_creative' );