<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Includes the Base files
 *
 */
function slicewp_include_files_base() {

	// Get legend dir path
	$dir_path = plugin_dir_path( __FILE__ );

	// Include deprecated functions
	if( file_exists( $dir_path . 'functions-deprecated.php' ) )
		include $dir_path . 'functions-deprecated.php';

	// Include utils functions
	if( file_exists( $dir_path . 'functions-utils.php' ) )
		include $dir_path . 'functions-utils.php';

	// Include currency functions
	if( file_exists( $dir_path . 'functions-currency.php' ) )
		include $dir_path . 'functions-currency.php';

	// Include ajax actions
	if( file_exists( $dir_path . 'functions-actions-ajax.php' ) )
		include $dir_path . 'functions-actions-ajax.php';

	// Include tracking class
	if( file_exists( $dir_path . 'class-tracking.php' ) )
		include $dir_path . 'class-tracking.php';

	// Include debug logger class
	if( file_exists( $dir_path . 'class-debug-logger.php' ) )
		include $dir_path . 'class-debug-logger.php';

	// Include update checker
	if( file_exists( $dir_path . 'class-update-checker.php' ) )
		include $dir_path . 'class-update-checker.php';

	// Include debug logger class
	if( file_exists( $dir_path . 'class-merge-tags.php' ) )
		include $dir_path . 'class-merge-tags.php';

	// Include plugin usage tracker class
	if( file_exists( $dir_path . 'class-plugin-usage-tracker.php' ) )
		include $dir_path . 'class-plugin-usage-tracker.php';

}
add_action( 'slicewp_include_files', 'slicewp_include_files_base' );


/**
 * Returns a plugin option
 *
 * @param string $option
 * @param mixed  $default
 *
 * @return mixed
 *
 */
function slicewp_get_option( $option, $default = '' ) {

	return get_option( 'slicewp_' . $option, $default );

}


/**
 * Updates a plugin option by the given value
 *
 * @param string $option
 * @param mixed  $value
 *
 */
function slicewp_update_option( $option, $value ) {

	return update_option( 'slicewp_' . $option, $value );

}


/**
 * Returns a plugin setting from the settings plugin option
 *
 * @param string $option
 * @param mixed  $default
 *
 * @return mixed
 *
 */
function slicewp_get_setting( $setting, $default = '' ) {

	$settings = slicewp_get_option( 'settings', array() );

	return ( isset( $settings[$setting] ) ? $settings[$setting] : $default );

}


/**
 * Returns the affiliate_id of the referrer saved in the cookie
 *
 * @return null|int
 *
 */
function slicewp_get_referrer_affiliate_id() {

	return slicewp()->services['tracking']->get_referrer_affiliate_id();

}


/**
 * Returns the visit_id of the referrer saved in the cookie
 *
 * @return null|int
 *
 */
function slicewp_get_referrer_visit_id() {

	return slicewp()->services['tracking']->get_referrer_visit_id();

}


/**
 * Calculates the commission amount for a given base amount taking into account
 * the passed arguments
 *
 * @param float $amount
 * @param array $args
 *
 * @return float
 *
 */
function slicewp_calculate_commission_amount( $amount, $args = array() ) {

	if( empty( $args['origin'] ) )
		return 0;

	if( empty( $args['type'] ) )
		return 0;

	$rate 	   = slicewp_get_setting( 'commission_rate_' . $args['type'] );
	$rate_type = slicewp_get_setting( 'commission_rate_type_' . $args['type'] );

	$commission_amount = ( $rate_type == 'percentage' ? round( ( $amount * $rate / 100 ), 2 ) : $rate );

	/**
	 * Filter the commission amount before returning it
	 *
	 * @param float $commission_amount
	 * @param float $amount
	 * @param array $args
	 *
	 */
	$commission_amount = apply_filters( 'slicewp_calculate_commission_amount', $commission_amount, $amount, $args );

	return $commission_amount;
	
}


/**
 * Get the URL of the current page
 *
 * @return string
 *
 */
function slicewp_get_current_page_url() {

	global $wp;

	if ( get_option( 'permalink_structure' ) ) {
		$base = trailingslashit( home_url( $wp->request ) );
	} else {
		$base = add_query_arg( $wp->query_string, '', trailingslashit( home_url( $wp->request ) ) );
		$base = remove_query_arg( array( 'post_type', 'name' ), $base );
	}

	$scheme      = is_ssl() ? 'https' : 'http';
	$current_url = set_url_scheme( $base, $scheme );

	if ( is_front_page() ) {
		$current_url = home_url( '/' );
	}

	/**
	 * Filter the current page URL
	 *
	 * @param string $current_url
	 *
	 */
	return apply_filters( 'slicewp_get_current_page_url', $current_url );

}


/**
 * Verifies if the provided reCAPTCHA response is valid
 *
 * @param array $data
 *
 * @return bool
 *
 */
function slicewp_is_recaptcha_valid( $data ) {

	$site_key   = slicewp_get_setting( 'recaptcha_site_key' );
	$secret_key = slicewp_get_setting( 'recaptcha_secret_key' );

	if( empty( $site_key ) || empty( $secret_key ) )
		return false;

	if( empty( $data['g-recaptcha-response'] ) || empty( $data['g-recaptcha-remoteip'] ) )
		return false;

	// Send post to verify the response with Google
	$response = wp_safe_remote_post( 'https://www.google.com/recaptcha/api/siteverify', array(
		'timeout' => 30,
		'body'    => array(
			'secret'   => $secret_key,
			'response' => $data['g-recaptcha-response'],
			'remoteip' => $data['g-recaptcha-remoteip']
		)
	));

	if( is_wp_error( $response ) )
		return false;

	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	if( ! isset( $body['success'] ) || ! $body['success'] )
		return false;

	return true;

}


/**
 * Adds the "async" and "defer" attributes to scripts that have the tag
 * explicitly added to the handle
 *
 * @param string $tag
 * @param string $handle
 *
 */
function slicewp_script_async_defer_attribute( $tag, $handle ) {

	if ( is_admin() )
		return $tag;

	if ( false === strpos( $handle, 'slicewp' ) )
		return $tag;

	// Return tag with both async and defer
	if( false !== strpos( $handle, 'async-defer' ) )
		return str_replace( '<script ', '<script async defer ', $tag );

    // Return the tag with the async attribute
    if ( false !== strpos( $handle, 'async' ) )
        return str_replace( '<script ', '<script async ', $tag );

    // Return the tag with the defer attribute
    if ( false !== strpos( $handle, 'defer' ) )
        return str_replace( '<script ', '<script defer ', $tag );
    
    return $tag;

}
add_filter( 'script_loader_tag', 'slicewp_script_async_defer_attribute', 10, 2 );