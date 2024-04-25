<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Returns the commision types that are available, based on what integration is available
 *
 * @deprecated 1.0.7 - No longer used in core and not recommended for external usage.
 * 					   Replaced by slicewp_get_available_commission_types().
 *					   Slated for removal in version 2.0.0
 *
 * @return array
 *
 */
function slicewp_get_active_commission_types() {

	return slicewp_get_available_commission_types();

}