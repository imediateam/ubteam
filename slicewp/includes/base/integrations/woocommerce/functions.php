<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Includes the WooCommerce files
 *
 */
function slicewp_include_files_woo() {

	// Get legend dir path
	$dir_path = plugin_dir_path( __FILE__ );

	// Include main class
	if( file_exists( $dir_path . 'class-integration-woocommerce.php' ) )
		include $dir_path . 'class-integration-woocommerce.php';

	// Include hooks functions
	if( slicewp_is_integration_active( 'woo' ) && slicewp_is_integration_plugin_active( 'woo' ) ) {

		if( file_exists( $dir_path . 'functions-hooks-integration-woocommerce.php' ) )
			include $dir_path . 'functions-hooks-integration-woocommerce.php';
		
	}

}
add_action( 'slicewp_include_files_late', 'slicewp_include_files_woo' );


/**
 * Register the WooCommerce class
 *
 * @param array $integrations
 *
 * @return array
 *
 */
function slicewp_register_integration_woo( $integrations ) {

	$integrations['woo'] = 'SliceWP_Integration_WooCommerce';

	return $integrations;

}
add_filter( 'slicewp_register_integration', 'slicewp_register_integration_woo', 10 );


/**
 * Verifies if WooCommerce is active
 *
 * @param bool $is_active
 *
 * @return bool
 *
 */
function slicewp_is_integration_plugin_active_woo( $is_active = false ) {

	if( class_exists( 'WooCommerce' ) )
		$is_active = true;

	return $is_active;

}
add_filter( 'slicewp_is_integration_plugin_active_woo', 'slicewp_is_integration_plugin_active_woo' );


/**
 * Adds additional commission types for WooCommerce
 *
 * @param array $supports
 *
 * @return array
 *
 */
function slicewp_integration_supports_woo( $supports ) {

    // Add subscription commission type if WooCommerce Subscription is active
    if( class_exists( 'WC_Subscriptions' ) )
        $supports['commission_types'][] = 'subscription';

    return $supports;

}
add_filter( 'slicewp_integration_supports_woo', 'slicewp_integration_supports_woo' );
