<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Add commission table reference column edit screen link
add_filter( 'slicewp_list_table_commissions_row_data', 'slicewp_list_table_commissions_add_reference_edit_link_woo' );
add_filter( 'slicewp_list_table_payout_commissions_row_data', 'slicewp_list_table_commissions_add_reference_edit_link_woo' );

// Insert a new pending commission
add_action( 'woocommerce_checkout_update_order_meta', 'slicewp_insert_pending_commission_woo', 10, 1 );

// Update the status of the commission to "unpaid", thus marking it as complete
add_action( 'woocommerce_order_status_completed', 'slicewp_accept_pending_commission_woo', 10, 1 );
add_action( 'woocommerce_order_status_processing', 'slicewp_accept_pending_commission_woo', 10, 1 );

// Update the status of the commission to "rejected" when the originating order is refunded
add_action( 'woocommerce_order_status_changed', 'slicewp_reject_commission_on_refund_woo', 10, 3 );

// Update the status of the commission to "rejected" when the originating order is cancelled or failed payment
add_action( 'woocommerce_order_status_changed', 'slicewp_reject_commission_on_order_fail_woo', 10, 3 );

// Update the status of the commission to "rejected" when the originating order is trashed
add_action( 'wc-on-hold_to_trash', 'slicewp_reject_commission_on_trash_woo', 10 );
add_action( 'wc-processing_to_trash', 'slicewp_reject_commission_on_trash_woo', 10 );
add_action( 'wc-completed_to_trash', 'slicewp_reject_commission_on_trash_woo', 10 );

// Update the status of the commission to "pending" when the originating order is moved from failed to any other status
add_action( 'woocommerce_order_status_changed', 'slicewp_approve_rejected_commission_woo', 10, 3 );

// Add commission settings in product page
add_filter( 'woocommerce_product_data_tabs', 'slicewp_add_product_data_tab_woo' );
add_action( 'woocommerce_product_data_panels', 'slicewp_add_product_commission_settings_woo' );

// Save the product commission settings
add_action( 'save_post_product' , 'slicewp_save_product_commission_settings_woo', 10, 2 );

// Add commission settings in product variation page
add_action( 'woocommerce_product_after_variable_attributes', 'slicewp_add_product_variation_commission_settings_woo', 10, 3 );

// Save the product variation commission settings
add_action( 'woocommerce_ajax_save_product_variations', 'slicewp_save_product_variation_commission_settings_woo', 10, 1 );

// Add commission settings in product category page
add_action( 'product_cat_add_form_fields', 'slicewp_add_product_category_commision_settings_woo', 10 );
add_action( 'product_cat_edit_form_fields', 'slicewp_edit_product_category_commision_settings_woo', 10, 1 );

// Save the product category commission settings
add_action( 'created_product_cat', 'slicewp_save_product_category_commission_settings_woo', 10, 3 );
add_action( 'edited_product_cat', 'slicewp_save_product_category_commission_settings_woo', 10, 3 );


/**
 * Adds the edit screen link to the reference column value from the commissions list table
 *
 * @param array $row_data
 *
 * @return array
 *
 */
function slicewp_list_table_commissions_add_reference_edit_link_woo( $row_data ) {

	if ( empty( $row_data['reference'] ) )
		return $row_data;

	if ( empty( $row_data['origin'] ) || $row_data['origin'] != 'woo' )
		return $row_data;

	// Get the order
	$order = wc_get_order( $row_data['reference'] );

	// Create link to order only if the order exists
    if ( ! empty( $order ) && $order->get_status() != 'trash' )
		$row_data['reference'] = '<a href="' . add_query_arg( array( 'post' => $row_data['reference'], 'action' => 'edit' ), admin_url( 'post.php' ) ) . '">' . $row_data['reference'] . '</a>';

	return $row_data;

}


/**
 * Inserts a new pending commission when a new pending order is registered
 *
 * @param int $order_id
 *
 */
function slicewp_insert_pending_commission_woo( $order_id ) {

	// Get and check to see if referrer exists
	$affiliate_id = slicewp_get_referrer_affiliate_id();
	$visit_id	  = slicewp_get_referrer_visit_id();

	/**
	 * Filters the referrer affiliate ID for WooCommerce.
	 * This is mainly used by add-ons for different functionality.
	 *
	 * @param int $affiliate_id
	 * @param int $order_id
	 *
	 */
	$affiliate_id = apply_filters( 'slicewp_referrer_affiliate_id_woo', $affiliate_id, $order_id );

	if ( empty( $affiliate_id ) ) 
		return;

	// Verify if the affiliate is valid
	if ( ! slicewp_is_affiliate_valid( $affiliate_id ) ) {

		slicewp_add_log( 'WOO: Pending commission was not created because the affiliate is not valid.' );
		return;

	}
	
	// Check to see if a commission for this order has been registered
	$commissions = slicewp_get_commissions( array( 'reference' => $order_id, 'origin' => 'woo' ) );

	if ( ! empty( $commissions ) ) {

		slicewp_add_log( 'WOO: Pending commission was not created because another commission for the reference and origin already exists.' );
		return;

	}

	// Get order
	$order = wc_get_order( $order_id );

	// Check to see if the affiliate made the purchase
	if ( empty( slicewp_get_setting( 'affiliate_own_commissions' ) ) ) {

		$affiliate = slicewp_get_affiliate( $affiliate_id );

		if ( is_user_logged_in() && get_current_user_id() == $affiliate->get('user_id') ) {

			slicewp_add_log( 'WOO: Pending commission was not created because the customer is also the affiliate.' );
			return;

		}

		// Get billing email
		$billing_email = ( true === version_compare( WC()->version, '3.0.0', '>=' ) ? $order->get_billing_email() : $order->billing_email );

		if ( slicewp_affiliate_has_email( $affiliate_id, $billing_email ) ) {

			slicewp_add_log( 'WOO: Pending commission was not created because the customer is also the affiliate.' );
			return;

		}

	}

	// Get all cart items
	$cart_items = $order->get_items();
	
	// Set cart shipping
	$cart_shipping = $order->get_shipping_total( 'edit' );

	if ( ! slicewp_get_setting( 'exclude_tax', false ) )
		$cart_shipping = $cart_shipping + $order->get_shipping_tax( 'edit' );


    // Calculate the commission amount for each item in the cart
    if ( ! slicewp_is_commission_basis_per_order() ) {

        $commission_amount = 0;

        foreach( $cart_items as $cart_item ) {

            // Get the product categories
            $categories = get_the_terms( $cart_item->get_product_id(), 'product_cat' );

            // Verify if commissions are disabled for this product category
            if ( ! empty( $categories[0]->term_id ) && get_term_meta( $categories[0]->term_id, 'slicewp_disable_commissions', true ) )
                continue;

            // Verify if commissions are disabled for this product
            if ( get_post_meta( $cart_item->get_product_id(), 'slicewp_disable_commissions', true ) )
                continue;

            // Verify if commissions are disabled for this product variation
            if ( ! empty( $cart_item->get_variation_id() ) && get_post_meta( $cart_item->get_variation_id(), 'slicewp_disable_commissions', true ) )
                continue;

            $amount = $cart_item->get_total( 'edit' );

			// Include tax
			if ( ! slicewp_get_setting( 'exclude_tax', false ) )
				$amount = $amount + $cart_item->get_total_tax( 'edit' );

			// Include shipping
			if ( ! slicewp_get_setting( 'exclude_shipping', false ) && $cart_shipping > 0 )
				$amount = $amount + $cart_shipping / count( $cart_items );

			// Set product ID
			$variation_id = $cart_item->get_variation_id( 'edit' );
			$product_id   = ( ! empty( $variation_id ) ? $variation_id : $cart_item->get_product_id( 'edit' ) );

            // Get the product
            $product = wc_get_product( $product_id );

            // Calculate commission amount
			$args = array(
				'origin'	   => 'woo',
				'type' 		   => $product->is_type( array( 'subscription', 'variable-subscription', 'subscription_variation' ) ) ? 'subscription' : 'sale',
				'affiliate_id' => $affiliate_id,
				'product_id'   => $product_id
			);

            $commission_amount += slicewp_calculate_commission_amount( $amount, $args );
            
            // Save the order commission types for future use
            $order_commission_types[] = $args['type'];

		}

	// Calculate the commission amount for the entire order
	} else {

		$args = array(
			'origin'	   => 'woo',
			'type' 		   => 'sale',
			'affiliate_id' => $affiliate_id
		);

		$commission_amount = slicewp_calculate_commission_amount( 0, $args );

        // Save the order commission types for future use
        $order_commission_types[] = $args['type'];

	}

    // Check that the commission amount is not zero
    if ( ( $commission_amount == 0 ) && empty( slicewp_get_setting( 'zero_amount_commissions' ) ) ) {

        slicewp_add_log( 'WOO: Commission was not inserted because the commission amount is zero. Order: ' . absint( $order_id ) );
        return;

    }

    // Remove duplicated order commission types
    $order_commission_types = array_unique( $order_commission_types );

	// Prepare commission data
	$commission_data = array(
		'affiliate_id'  => $affiliate_id,
		'visit_id'		=> ( ! is_null( $visit_id ) ? $visit_id : 0 ),
		'date_created'  => slicewp_mysql_gmdate(),
		'date_modified' => slicewp_mysql_gmdate(),
		'type'			=> sizeof( $order_commission_types ) == 1 ? $order_commission_types[0] : 'sale',
		'status'		=> 'pending',
		'reference'     => $order_id,
		'origin' 	    => 'woo',
		'amount'		=> slicewp_sanitize_amount( $commission_amount ),
		'currency'		=> slicewp_get_setting( 'active_currency', 'USD' )
    );

	// Insert the commission
	$commission_id = slicewp_insert_commission( $commission_data );

	if ( ! empty( $commission_id ) ) {

		// Update the visit with the newly inserted commission_id
		if ( ! is_null( $visit_id ) ) {

			slicewp_update_visit( $visit_id, array( 'commission_id' => $commission_id ) );
			slicewp_add_log( sprintf( 'WOO: Pending commission #%s has been successfully inserted.', $commission_id ) );
		
		}
		
	} else {

		slicewp_add_log( 'WOO: Pending commission could not be inserted due to an unexpected error.' );
		
	}

}


/**
 * Updates the status of the commission attached to an order to "unpaid", thus marking it as complete
 *
 * @param int $order_id
 *
 */
function slicewp_accept_pending_commission_woo( $order_id ) {

	// Return if the order is processing and the payment method is cash on delivery
	$order = wc_get_order( $order_id );

	if ( $order->get_status() == 'processing' && $order->get_payment_method() == 'cod' )
		return;

	// Check to see if a commission for this order has been registered
	$commissions = slicewp_get_commissions( array( 'reference' => $order_id, 'origin' => 'woo' ) );

	if ( empty( $commissions ) )
		return;

	// Set commission
	$commission = $commissions[0];

	// Return if the commission has already been paid
	if ( $commission->get('status') == 'paid' )
		return;

	// Prepare commission data
	$commission_data = array(
		'date_modified' => slicewp_mysql_gmdate(),
		'status' => 'unpaid'
	);

	// Update the commission
	$updated = slicewp_update_commission( $commission->get('id'), $commission_data );

	if ( false !== $updated )
		slicewp_add_log( sprintf( 'WOO: Pending commission #%s successfully marked as completed.', $commission->get('id') ) );

	else
		slicewp_add_log( 'WOO: Pending commission could not be completed due to an unexpected error.' );

}


/**
 * Update the status of the commission to "rejected" when the originating order is refunded
 *
 * @param int    $order_id
 * @param string $status_from
 * @param string $status_to
 *
 */
function slicewp_reject_commission_on_refund_woo( $order_id, $status_from, $status_to ) {

	if ( ! slicewp_get_setting( 'reject_commissions_on_refund', false ) )
		return;

	if ( $status_to != 'refunded' )
		return;

	// Check to see if a commission for this order has been registered
	$commissions = slicewp_get_commissions( array( 'reference' => $order_id, 'origin' => 'woo' ) );

	if ( empty( $commissions ) )
		return;

	// Set commission
	$commission = $commissions[0];

	if ( $commission->get('status') == 'paid' ) {

		slicewp_add_log( 'WOO: Commission could not be rejected because it was already paid.' );
		return;

	}

	// Prepare commission data
	$commission_data = array(
		'date_modified' => slicewp_mysql_gmdate(),
		'status' => 'rejected'
	);

	// Update the commission
	$updated = slicewp_update_commission( $commission->get('id'), $commission_data );

	if ( false !== $updated )
		slicewp_add_log( sprintf( 'WOO: Commission #%s successfully marked as rejected, after order #%s was refunded.', $commission->get('id'), $order_id ) );

	else
		slicewp_add_log( sprintf( 'WOO: Commission #%s could not be rejected due to an unexpected error.', $commission->get('id') ) );

}


/**
 * Update the status of the commission to "rejected" when the originating order is cancelled or failed payment
 *
 * @param int    $order_id
 * @param string $status_from
 * @param string $status_to
 *
 */
function slicewp_reject_commission_on_order_fail_woo( $order_id, $status_from, $status_to ) {

	if ( $status_to != 'failed' && $status_to != 'cancelled' )
		return;

	// Check to see if a commission for this order has been registered
	$commissions = slicewp_get_commissions( array( 'reference' => $order_id, 'origin' => 'woo' ) );

	if ( empty( $commissions ) )
		return;

	// Set commission
	$commission = $commissions[0];

	if ( $commission->get('status') == 'paid' ) {

		slicewp_add_log( 'WOO: Commission could not be rejected because it was already paid.' );
		return;

	}

	// Prepare commission data
	$commission_data = array(
		'date_modified' => slicewp_mysql_gmdate(),
		'status' => 'rejected'
	);

	// Update the commission
	$updated = slicewp_update_commission( $commission->get('id'), $commission_data );

	if ( false !== $updated )
		slicewp_add_log( sprintf( 'WOO: Commission #%s successfully marked as rejected, after order #%s failed or was cancelled.', $commission->get('id'), $order_id ) );

	else
		slicewp_add_log( sprintf( 'WOO: Commission #%s could not be rejected due to an unexpected error.', $commission->get('id') ) );

}


/**
 * Update the status of the commission to "rejected" when the originating order is trashed
 *
 * @param int $order_id
 *
 */
function slicewp_reject_commission_on_trash_woo( $order_id ) {

	if ( is_a( $order_id, 'WP_Post' ) )
		$order_id = $order_id->ID;

	if ( get_post_type( $order_id ) != 'shop_order' )
		return;

	// Check to see if a commission for this order has been registered
	$commissions = slicewp_get_commissions( array( 'reference' => $order_id, 'origin' => 'woo' ) );

	if ( empty( $commissions ) )
		return;

	// Set commission
	$commission = $commissions[0];

	if ( $commission->get('status') == 'paid' ) {

		slicewp_add_log( 'WOO: Commission could not be rejected because it was already paid.' );
		return;

	}

	// Prepare commission data
	$commission_data = array(
		'date_modified' => slicewp_mysql_gmdate(),
		'status' => 'rejected'
	);

	// Update the commission
	$updated = slicewp_update_commission( $commission->get('id'), $commission_data );

	if ( false !== $updated )
		slicewp_add_log( sprintf( 'WOO: Commission #%s successfully marked as rejected, after order #%s was trashed.', $commission->get('id'), $order_id ) );

	else
		slicewp_add_log( sprintf( 'WOO: Commission #%s could not be rejected due to an unexpected error.', $commission->get('id') ) );

}


/**
 * Update the status of the commission to "pending" when the originating order is moved from failed to any other status
 *
 * @param int    $order_id
 * @param string $status_from
 * @param string $status_to
 *
 */
function slicewp_approve_rejected_commission_woo( $order_id, $status_from, $status_to ) {

	if ( ! in_array( $status_from, array( 'failed', 'cancelled', 'refunded' ) ) )
		return;

	if ( in_array( $status_to, array( 'failed', 'cancelled', 'refunded', 'processing', 'completed' ) ) )
		return;

	// Check to see if a commission for this order has been registered
	$commissions = slicewp_get_commissions( array( 'reference' => $order_id, 'origin' => 'woo' ) );

	if ( empty( $commissions ) )
		return;

	// Set commission
	$commission = $commissions[0];

	if ( $commission->get('status') != 'rejected' )
		return;

	// Prepare commission data
	$commission_data = array(
		'date_modified' => slicewp_mysql_gmdate(),
		'status' => 'pending'
	);

	// Update the commission
	$updated = slicewp_update_commission( $commission->get('id'), $commission_data );

	if ( false !== $updated )
		slicewp_add_log( sprintf( 'WOO: Commission #%s successfully marked as pending, after order #%s was updated from %s to %s.', $commission->get('id'), $order_id, $status_from, $status_to ) );

	else
		slicewp_add_log( sprintf( 'WOO: Commission #%s could not be marked as pending due to an unexpected error.', $commission->get('id') ) );

}


/**
 * Adds the SliceWP data tab in the WooCommerce product page
 * 
 * @param array $tabs
 * 
 * @return array $tabs
 * 
 */
function slicewp_add_product_data_tab_woo( $tabs ) {

    $tabs['slicewp'] = array(
        'label'    => __( 'SliceWP', 'slicewp' ),
        'target'   => 'slicewp_product_settings',
        'class'    => array()
    );

    return $tabs;

}


/**
 * Adds the product commission settings in the product page - SliceWP data tab
 * 
 * 
 */
function slicewp_add_product_commission_settings_woo() {

    global $post;

?>

    <div id="slicewp_product_settings" class="panel woocommerce_options_panel slicewp-options-groups-wrapper">
	    
	    <?php

	    	/**
	         * Hook to add option groups before the core one
	         * 
	         */
	        do_action( 'slicewp_woo_product_data_panel_top' );

	    ?>

        <div class="slicewp-options-group options_group">

	        <p><?php echo( __( 'Here you can make commission customizations for this product. These settings will be used to calculate the commissions for this product.', 'slicewp' ) ); ?></p>

			<?php

			    // Get the product categories
			    $categories = get_the_terms( $post->ID, 'product_cat' );

			?>

			<?php if ( ! empty( $categories[0]->term_id ) && get_term_meta( $categories[0]->term_id, 'slicewp_disable_commissions', true ) ): ?>

				<p class="form-row form-row-full slicewp-product-commissions-disabled"><?php echo __( 'The product commission rate settings are not available because the commissions for this product category are disabled.', 'slicewp' ); ?></p>

			<?php else: ?>

				<?php

			        /**
			         * Hook to add settings before the core ones
			         * 
			         */
			        do_action( 'slicewp_woo_product_data_panel_options_group_core_top' );
			        
			        woocommerce_wp_checkbox( array(
			            'id'          => 'slicewp_disable_commissions',
			            'label'       => __( 'Disable commissions', 'slicewp' ),
			            'description' => __( 'When checked, commissions will not be generated for this product.', 'slicewp' ),
			            'cbvalue'     => 1,
			            'class'		  => 'slicewp-option-field-disable-commissions',
			            'wrapper_class' => 'slicewp-option-field-wrapper'
			        ) );

			        /**
			         * Hook to add settings after the core ones
			         * 
			         */
			        do_action( 'slicewp_woo_product_data_panel_options_group_core_bottom' );
			        
				?>

			<?php endif; ?>

        </div>

        <?php 

        	/**
	         * Hook to add options groups after the core one
	         * 
	         */
	        do_action( 'slicewp_woo_product_data_panel_bottom' );

        ?>

    </div>

<?php

    // Add nonce field
    wp_nonce_field( 'slicewp_save_meta', 'slicewp_token', false );

}


/**
 * Saves the product commission settings into the product meta
 * 
 * @param int $post_id
 * @param WP_Post $post
 * 
 */
function slicewp_save_product_commission_settings_woo( $post_id, $post ) {

	// Verify for nonce
    if ( empty( $_POST['slicewp_token'] ) || ! wp_verify_nonce( $_POST['slicewp_token'], 'slicewp_save_meta' ) )
        return $post_id;
    
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return $post_id;

    if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) )
        return $post_id;

    // Update the disable commissions settings
    if ( ! empty( $_POST['slicewp_disable_commissions'] ) ) {

        update_post_meta( $post_id, 'slicewp_disable_commissions', 1 );

    } else {

        delete_post_meta( $post_id, 'slicewp_disable_commissions' );

    }

}


/**
 * Adds the product variation commission settings in the variations tab
 * 
 * @param int     $loop           Position in the loop.
 * @param array   $variation_data Variation data.
 * @param WP_Post $variation      Post data.
 * 
 */
function slicewp_add_product_variation_commission_settings_woo( $loop, $variation_data, $variation ) {

    // Show the product variation commission settings
?>

    <div id="slicewp_product_variation_settings" class="slicewp-options-groups-wrapper">

    	<?php

    		/**
	         * Hook to add settings before the core ones
	         * 
	         */
	        do_action( 'slicewp_woo_variation_product_data_panel_top', $loop, $variation_data, $variation );

    	?>

    	<div class="slicewp-options-group">

	        <p class="form-row form-row-full">
	            <strong><?php echo __( 'SliceWP Commission Settings', 'slicewp' ); ?></strong>
	        </p>

			<?php

			    // Get the product categories
			    $categories = get_the_terms( $variation->post_parent, 'product_cat' );

			?>

			<?php 

				// Verify if commissions are disabled for category
				if ( ! empty( $categories[0]->term_id ) && get_term_meta( $categories[0]->term_id, 'slicewp_disable_commissions', true ) ): 

			?>

	        	<p class="form-row form-row-full slicewp-product-variation-commissions-disabled"><?php echo __( 'The product variation commission rate settings are not available because the commissions for this product category are disabled.', 'slicewp' ); ?></p>

			<?php

			    // Verify if the commissions are disabled for the parent product
			    elseif ( get_post_meta( $variation->post_parent, 'slicewp_disable_commissions', true ) ):

			?>

	        	<p class="form-row form-row-full slicewp-product-variation-commissions-disabled"><?php echo __( 'The product variation commission rate settings are not available because the commissions for this product are disabled.', 'slicewp' ); ?></p>

			<?php else: ?>

				<?php

			        $disable_commissions = get_post_meta( $variation->ID, 'slicewp_disable_commissions', true );

			        /**
			         * Hook to add settings before the core ones
			         * 
			         */
			        do_action( 'slicewp_woo_variation_product_data_panel_options_group_core_top', $loop, $variation_data, $variation );

		    	?>

		        <p class="slicewp-option-field-wrapper form-row form-row-full options slicewp_variation_disable_commissions[<?php echo $variation->ID?>]">
		            <label for="slicewp_variation_disable_commissions[<?php echo $variation->ID?>]">
		                <input type="checkbox" class="slicewp-option-field-disable-commissions checkbox" name="slicewp_variation_disable_commissions[<?php echo $variation->ID; ?>]" id="slicewp_variation_disable_commissions[<?php echo $variation->ID; ?>]" <?php checked( $disable_commissions, true ); ?> /> <?php echo __( 'Disable commissions for this product variation', 'slicewp' ); ?>
		            </label>
		        </p>


			    <?php

			        /**
			         * Hook to add settings after the core ones
			         * 
			         */
			        do_action( 'slicewp_woo_variation_product_data_panel_options_group_core_bottom', $loop, $variation_data, $variation );

				?>

			<?php endif; ?>

		</div>

		<?php

	        /**
	         * Hook to add settings after the core ones
	         * 
	         */
	        do_action( 'slicewp_woo_variation_product_data_panel_bottom', $loop, $variation_data, $variation );

		?>

    </div>

<?php

}


/**
 * Saves the product variation commission settings into the product meta
 * 
 * @param int $product_id
 * 
 */
function slicewp_save_product_variation_commission_settings_woo( $product_id = 0 ) {

    // Check for variations
    if ( empty( $_POST['variable_post_id'] ) )
        return;
    
    // Parse all the variations
    foreach( $_POST['variable_post_id'] as $variation_id ) {

        $variation_id = absint( $variation_id );

        // Update the disable commissions settings
        if ( ! empty( $_POST['slicewp_variation_disable_commissions'] ) && ! empty( $_POST['slicewp_variation_disable_commissions'][$variation_id] ) ) {

            update_post_meta( $variation_id, 'slicewp_disable_commissions', 1 );

        } else {

            delete_post_meta( $variation_id, 'slicewp_disable_commissions' );

        }
        
    }

}


/**
 * Adds the category commission settings in the add product category page
 * 
 */
function slicewp_add_product_category_commision_settings_woo() {

    /**
     * Hook to add fields before the core ones
     * 
     */
    do_action( 'slicewp_woo_add_product_category_form_fields_top' );

	?>

    <div class="slicewp-option-field-wrapper">

        <label for="slicewp-disable-commissions">
            <input type="checkbox" class="slicewp-option-field-disable-commissions checkbox" name="slicewp_disable_commissions" id="slicewp-disable-commissions" class="slicewp-option-field-disable-commissions" /><?php echo __( 'Disable commissions', 'slicewp' ); ?>
        </label>
        <p><?php echo __( 'When checked, commissions will not be generated for this product category.', 'slicewp' ); ?></p>

    </div>

	<?php

    /**
     * Hook to add fields after the core ones
     * 
     */
    do_action( 'slicewp_woo_add_product_category_form_fields_bottom' );

}


/**
 * Adds the category commission settings in the edit product category page
 * 
 * @param WP_Term $category
 * 
 */
function slicewp_edit_product_category_commision_settings_woo( $category ) {

    // Get the product category commission settings
    $category_disable_commissions = get_term_meta( $category->term_id, 'slicewp_disable_commissions', true );

    /**
     * Hook to add fields before the core ones
     * 
     */
    do_action( 'slicewp_woo_edit_product_category_form_fields_top', $category );

    ?>
    
    <tr class="slicewp-option-field-wrapper form-field">
        <th scope="row">
            <label for="slicewp-disable-commissions"><?php echo __( 'Disable commissions', 'slicewp' ); ?></label>
        </th>
        <td>
            <input type="checkbox" class="slicewp-option-field-disable-commissions checkbox" name="slicewp_disable_commissions" id="slicewp-disable-commissions" <?php checked( $category_disable_commissions, true ); ?> />
            <p class="description"><?php echo __( 'When checked, commissions will not be generated for this product category.', 'slicewp' ); ?></p>
        </td>
    </tr>

    <?php

    /**
     * Hook to add fields after the core ones
     * 
     */    
    do_action( 'slicewp_woo_edit_product_category_form_fields_bottom', $category );
       
}


/**
 * Saves the product category commission settings into the category meta
 * 
 * @param int $category_id
 * 
 */
function slicewp_save_product_category_commission_settings_woo( $category_id ) {

    // Update the disable commissions settings
    if ( ! empty( $_POST['slicewp_disable_commissions'] ) ) {

        update_term_meta( $category_id, 'slicewp_disable_commissions', 1 );
    
    } else {

        delete_term_meta( $category_id, 'slicewp_disable_commissions' );
    
    }
}