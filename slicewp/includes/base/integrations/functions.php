<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Verifies if an integration is active or not
 *
 * @param string $integration_slug
 *
 * @return bool
 *
 */
function slicewp_is_integration_active( $integration_slug ) {

	$active_integrations = slicewp_get_setting( 'active_integrations', array() );

	return in_array( $integration_slug, $active_integrations );

}


/**
 * Verifies if the plugin for an integration is active.
 * The function by default returns false. Each integration needs to hook to the available filter to
 * change the default value.
 *
 * @param string $integration_slug
 *
 * @return bool
 *
 */
function slicewp_is_integration_plugin_active( $integration_slug ) {

	/**
	 * Filter that checks to see if the plugin integration is active or not
	 *
	 * @param bool
	 *
	 */
	$integration_plugin_active = apply_filters( 'slicewp_is_integration_plugin_active_' . $integration_slug, false );

	return $integration_plugin_active;

}


/**
 * Returns the supports array for an integration
 *
 * @param string $integration_slug
 *
 * @return array|null
 *
 */
function slicewp_get_integration_supports( $integration_slug ) {

    $supports = slicewp()->integrations[$integration_slug]->get('supports');

    if ( ! empty( $supports ) )
        return $supports;

    return null;

}