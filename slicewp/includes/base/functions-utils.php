<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Sanitizes the values of an array recursivelly
 *
 * @param array $array
 *
 * @return array
 *
 */
function _slicewp_array_sanitize_text_field( $array = array() ) {

    if( empty( $array ) || ! is_array( $array ) )
        return array();

    foreach( $array as $key => $value ) {

        if( is_array( $value ) )
            $array[$key] = _slicewp_array_sanitize_text_field( $value );

        else
            $array[$key] = sanitize_text_field( $value );

    }

    return $array;

}


/**
 * Sanitizes the values of an array recursivelly and allows HTML tags
 *
 * @param array $array
 *
 * @return array
 *
 */
function _slicewp_array_wp_kses_post( $array = array() ) {

    if( empty( $array ) || ! is_array( $array ) )
        return array();

    foreach( $array as $key => $value ) {

        if( is_array( $value ) )
            $array[$key] = _slicewp_array_wp_kses_post( $value );

        else
            $array[$key] = wp_kses_post( $value );

    }

    return $array;

}


/**
 * Adds an associative array value, example array( 'key' => 'value' ) into an existing array
 * after the existing array's provided $key
 *
 * @param array  $array
 * @param string $key
 * @param array  $value
 *
 * @return array
 *
 */
function _slicewp_array_assoc_push_after_key( $array = array(), $key = '', $value ) {

    if( ! isset( $value ) )
        return $array;

    if( ( $offset = array_search( $key, array_keys( $array ) ) ) === false ) {

        $offset = count( $array );

    }
    
    $offset++;

    return array_merge( array_slice( $array, 0, $offset ), $value, array_slice( $array, $offset ) );

}


/**
 * Returns a random generated string
 *
 * @param int $length
 *
 * @return string
 *
 */
function _slicewp_generate_random_string( $length = 20 ) {

    $chars         = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $chars_length  = strlen( $chars );
    $random_string = '';

    for( $i = 0; $i < $length; $i++ ) {

        $random_string .= $chars[ rand( 0, $chars_length - 1 ) ];

    }

    return $random_string;

}


/**
 * Checks to see if the provided date is a valid format
 *
 * @param string $date
 * @param string $format
 *
 * @return bool
 *
 */
function slicewp_is_date_valid( $date, $format = 'Y-m-d' ) {

    $d = DateTime::createFromFormat( $format, $date );

    return $d && $d->format($format) === $date;

}


/**
 * Returns the date and time format saved in WP's settings page
 *
 * @return string
 *
 */
function slicewp_get_datetime_format() {

    $format = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );

    /**
     * Filter the default date time format before returning
     *
     * @param string $format
     *
     */
    $format = apply_filters( 'slicewp_datetime_format', $format );

    return $format;

}


/**
 * Returns the current date and time in mysql format
 *
 * @return string
 *
 */
function slicewp_mysql_gmdate() {
    
    return current_time( 'mysql', true );

}


/**
 * Returns the date and time in user's language
 * 
 * @param string $date
 * 
 * @return string
 * 
 */
function slicewp_date_i18n( $date ) {

    return date_i18n( slicewp_get_datetime_format(), strtotime( get_date_from_gmt( $date ) ) );

}


/**
 * Function that return the IP address of the user. Checks for IPs (in order) in: 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'
 *
 * @return string
 *
 */
function slicewp_get_user_ip_address() {

    $ip_address = '';

    foreach( array( 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR' ) as $key ) {
        if( array_key_exists( $key, $_SERVER ) === true ) {
            foreach( array_map( 'trim', explode( ',', $_SERVER[$key] ) ) as $ip ) {
                if( filter_var( $ip, FILTER_VALIDATE_IP ) !== false ) {
                    return $ip;
                }
            }
        }
    }

    return $ip_address;
    
}


/**
 * Adds a new message to the debug log
 *
 * @param string $message
 *
 * @return bool
 *
 */
function slicewp_add_log( $message ) {

    return slicewp()->services['debug_logger']->add_log( $message );

}


/**
 * Returns the entire debug log
 *
 * @return string
 *
 */
function slicewp_get_log() {

    return slicewp()->services['debug_logger']->get_file_contents();

}


/**
 * Clears the debug log file
 *
 */
function slicewp_clear_log() {

    return slicewp()->services['debug_logger']->delete_file();

}


/**
 * Returns a user friendly error message for the provided API action and error code,
 * when we are connecting to SliceWP website's API
 *
 * @param string $action
 * @param string $error_code
 *
 * @return string
 *
 */
function slicewp_get_api_action_response_error( $action, $error_code ) {

    $error_messages = array(
        'register_website' => array(
            'license_is_null'          => __( "The provided license key does not exist or is invalid.", 'slicewp' ),
            'license_inactive'         => __( "The provided license key is inactive.", 'slicewp' ),
            'license_expired'          => __( "The provided license key is expired.", 'slicewp' ),
            'activation_limit_reached' => __( "Your activation limit for this license key has been reached. Please upgrade your account if you'd like to register more websites.", 'slicewp' ),
            'register_website_failed'  => __( "Something went wrong. Could not activate the website. Please try again.", 'slicewp' )
        ),
        'deregister_website' => array(
            'license_is_null'           => __( "The provided license key does not exist or is invalid.", 'slicewp' ),
            'website_is_null'           => __( "This website is not registered on our system.", 'slicewp' ),
            'deregister_website_failed' => __( "Something went wrong. Could not activate the website. Please try again.", 'slicewp' )
        )
    );

    return ( ! empty( $error_messages[$action][$error_code] ) ? $error_messages[$action][$error_code] : '' );

}


/**
 * Returns the system status for the current installation
 *
 * @return string
 *
 */
function slicewp_system_status() {

    // Get system versions
    global $wp_version;

    $curl_version   = ( function_exists( 'curl_version' ) ? curl_version() : 'Not installed' );
    $curl_version   = ( is_array( $curl_version ) ? $curl_version['version'] : $curl_version );

    // Get all plugins and active plugins
    $plugins        = get_plugins();
    $active_plugins = array();

    foreach( $plugins as $key => $plugin ) {

        if( is_plugin_active( $key ) )
            $active_plugins[$key] = $plugin;

    }


    // Prepare system status
    $status  = 'System:' . "\r\n";
    $status .= '---------------------------------------------------------------------' . "\r\n";
    $status .= 'PHP Version: ' . phpversion() . "\r\n";
    $status .= 'cUrl Version: ' . $curl_version . "\r\n";
    $status .= 'WP Version: ' . $wp_version . "\r\n";
    $status .= 'SliceWP Version: ' . SLICEWP_VERSION . "\r\n";

    $status .= "\r\n";

    // Prepare all plugins
    $status .= 'All Plugins:' . "\r\n";
    $status .= '---------------------------------------------------------------------' . "\r\n";

    if( ! empty( $plugins ) ) {

        foreach( $plugins as $key => $plugin )
            $status .= esc_attr( $plugin['Name'] ) . ' (' . esc_attr( $key ) . ')' . ' (v.' . esc_attr( $plugin['Version'] ) . ')' ."\r\n";

    } else
        $status .= 'None' . "\r\n";

    $status .= "\r\n";

    // Prepare active plugins
    $status .= 'Active Plugins:' . "\r\n";
    $status .= '---------------------------------------------------------------------' . "\r\n";

    if( ! empty( $active_plugins ) ) {

        foreach( $active_plugins as $key => $plugin )
            $status .= esc_attr( $plugin['Name'] ) . ' (' . esc_attr( $key ) . ')' . ' (v.' . esc_attr( $plugin['Version'] ) . ')' ."\r\n";

    } else
        $status .= 'None' . "\r\n";

    // Return the system info
    return $status;

}


/**
 * Prefixes all keys of an array with the given prefix and returns the result
 *
 * @param array  $array
 * @param string $prefix
 *
 * @return array
 *
 */
function _slicewp_prefix_array_keys( $array = array(), $prefix = '' ) {

    if( empty( $array ) )
        return array();

    if( empty( $prefix ) )
        return $array;

    foreach( $array as $key => $value ) {

        $array[$prefix . $key] = $value;
        unset( $array[$key] );

    }

    return $array;

}