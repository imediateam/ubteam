<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Add commission table reference column edit screen link
add_filter( 'slicewp_list_table_commissions_row_data', 'slicewp_list_table_commissions_add_reference_edit_link_edd' );
add_filter( 'slicewp_list_table_payout_commissions_row_data', 'slicewp_list_table_commissions_add_reference_edit_link_edd' );

// Insert a new pending commission
add_action( 'edd_insert_payment', 'slicewp_insert_pending_commission_edd', 10, 2 );

// Update the status of the commission to "unpaid", thus marking it as complete
add_action( 'edd_complete_purchase', 'slicewp_accept_pending_commission_edd', 10, 1 );

// Update the status of the commission to "rejected" when the originating purchase is refunded
add_action( 'edd_update_payment_status', 'slicewp_reject_commission_on_refund_edd', 10, 3 );

// Update the status of the commission to "rejected" when the originating purchase is deleted
add_action( 'edd_payment_delete', 'slicewp_reject_commission_on_delete_edd', 10 );

// Add the commission settings in download page
add_action( 'add_meta_boxes', 'slicewp_add_commission_settings_metabox_edd', 10, 2 );

// Saves the commissions settings in download meta
add_action( 'edd_save_download', 'slicewp_save_product_commission_settings_edd', 10, 2 );

// Add the commission settings in category page
add_action( 'download_category_add_form_fields', 'slicewp_add_category_commision_settings_edd', 10 );
add_action( 'download_category_edit_form_fields', 'slicewp_edit_category_commision_settings_edd', 10, 1 );

// Save the product category commission settings
add_action( 'create_download_category', 'slicewp_save_category_commission_settings_edd', 10, 3 );
add_action( 'edited_download_category', 'slicewp_save_category_commission_settings_edd', 10, 3 );


/**
 * Adds the edit screen link to the reference column value from the commissions list table
 *
 * @param array $row_data
 *
 * @return array
 *
 */
function slicewp_list_table_commissions_add_reference_edit_link_edd( $row_data ) {

	if( empty( $row_data['reference'] ) )
		return $row_data;

	if( empty( $row_data['origin'] ) || $row_data['origin'] != 'edd' )
		return $row_data;

    // Get the payment
    $payment = edd_get_payment( $row_data['reference'] );

	// Create link to payment only if the payment exists
    if( ! empty( $payment->ID ) )
		$row_data['reference'] = '<a href="' . add_query_arg( array( 'post_type' => 'download', 'page' => 'edd-payment-history', 'view' => 'view-order-details', 'id' => $row_data['reference'] ), admin_url( 'edit.php' ) ) . '">' . $row_data['reference'] . '</a>';

	return $row_data;

}


/**
 * Inserts a new pending commission when a new pending payment is registered
 *
 * @param int   $payment_id
 * @param array $payment_data
 *
 */
function slicewp_insert_pending_commission_edd( $payment_id, $payment_data ) {

	// Get and check to see if referrer exists
	$affiliate_id = slicewp_get_referrer_affiliate_id();
	$visit_id	  = slicewp_get_referrer_visit_id();

	/**
	 * Filters the referrer affiliate ID for Easy Digital Downloads.
	 * This is mainly used by add-ons for different functionality.
	 *
	 * @param int $affiliate_id
	 * @param int $payment_id
	 *
	 */
	$affiliate_id = apply_filters( 'slicewp_referrer_affiliate_id_edd', $affiliate_id, $payment_id );

	if( empty( $affiliate_id ) )
		return;

	// Verify if the affiliate is valid
	if ( ! slicewp_is_affiliate_valid( $affiliate_id ) ){

		slicewp_add_log( 'EDD: Pending commission was not created because the affiliate is not valid.' );
		return;

	}

	// Check to see if the payment is a renewal or not
	$is_renewal = get_post_meta( $payment_id, '_edd_sl_is_renewal', true );

	if( ! empty( $is_renewal ) ) {

		slicewp_add_log( 'EDD: Pending commission was not created because the payment is a renewal.' );
		return;

	}

	// Check to see if a commission for this payment has been registered
	$commissions = slicewp_get_commissions( array( 'reference' => $payment_id, 'origin' => 'edd' ) );

	if( ! empty( $commissions ) ) {

		slicewp_add_log( 'EDD: Pending commission was not created because another commission for the reference and origin already exists.' );
		return;

	}

	// Check to see if the affiliate made the purchase
	if( empty( slicewp_get_setting( 'affiliate_own_commissions' ) ) ) {

		$affiliate = slicewp_get_affiliate( $affiliate_id );

		if( is_user_logged_in() && get_current_user_id() == $affiliate->get('user_id') ) {

			slicewp_add_log( 'EDD: Pending commission was not created because the customer is also the affiliate.' );
			return;

		}
		
		if( slicewp_affiliate_has_email( $affiliate_id, $payment_data['user_email'] ) ) {

			slicewp_add_log( 'EDD: Pending commission was not created because the customer is also the affiliate.' );
			return;

		}

	}

	// Get all cart items
	$cart_items = edd_get_payment_meta_cart_details( $payment_id );

	if( ! is_array( $cart_items ) ) {

		slicewp_add_log( 'EDD: Pending commission was not created because the cart details were not valid.' );
		return;

	}


	// Calculate the commission amount for each item in the cart
	if( ! slicewp_is_commission_basis_per_order() ) {

		$commission_amount = 0;

		foreach( $cart_items as $cart_item ) {

            // Get the product categories
            $categories = get_the_terms( $cart_item['id'], 'download_category' );

            // Verify if commissions are disabled for this product category
            if ( ! empty( $categories[0]->term_id ) && get_term_meta( $categories[0]->term_id, 'slicewp_disable_commissions', true ) )
                continue;

            // Verify if commissions are disabled for this product
            if ( get_post_meta( $cart_item['id'], 'slicewp_disable_commissions', true ) )
                continue;

			$amount = $cart_item['price'];

			// Exclude tax
			if( slicewp_get_setting( 'exclude_tax', false ) )
				$amount = $amount - $cart_item['tax'];

			// Add shipping fees if they exist
			if( ! slicewp_get_setting( 'exclude_shipping', false ) ) {

				if( ! empty( $cart_item['fees'] ) ) {

					foreach( $cart_item['fees'] as $key => $fee ) {

						if( empty( $fee['amount'] ) )
							continue;

						if( false === strpos( $key, 'shipping' ) )
							continue;
							
						$amount = $amount + $fee['amount'];

					}

				}

			}

			// Calculate commission amount
			$args = array(
				'origin'	   => 'edd',
				'type' 		   => ! empty( $cart_item['item_number']['options']['recurring'] ) ? 'subscription' : 'sale',
				'affiliate_id' => $affiliate_id,
				'product_id'   => $cart_item['id']
			);

			$commission_amount += slicewp_calculate_commission_amount( $amount, $args );

            // Save the order commission types for future use
            $order_commission_types[] = $args['type'];

		}

	// Calculate the commission amount for the entire order
	} else {

		$args = array(
			'origin'	   => 'edd',
			'type' 		   => 'sale',
			'affiliate_id' => $affiliate_id
		);

        $commission_amount = slicewp_calculate_commission_amount( 0, $args );
        
        // Save the order commission types for future use
        $order_commission_types[] = $args['type'];

	}

    // Check that the commission amount is not zero
    if ( ( $commission_amount == 0 ) && empty( slicewp_get_setting( 'zero_amount_commissions' ) ) ) {

        slicewp_add_log( 'EDD: Commission was not inserted because the commission amount is zero. Payment: ' . absint( $payment_id ) );
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
		'reference'     => $payment_id,
		'origin' 	    => 'edd',
		'amount'		=> slicewp_sanitize_amount( $commission_amount ),
		'currency'		=> slicewp_get_setting( 'active_currency', 'USD' )
	);

	// Insert the commission
	$commission_id = slicewp_insert_commission( $commission_data );

	if( ! empty( $commission_id ) ) {

		// Update the visit with the newly inserted commission_id
		if ( ! is_null( $visit_id ) ){

			slicewp_update_visit( $visit_id, array( 'commission_id' => $commission_id ) );
			slicewp_add_log( sprintf( 'EDD: Pending commission #%s has been successfully inserted.', $commission_id ) );
		
		}

	} else {

		slicewp_add_log( 'EDD: Pending commission could not be inserted due to an unexpected error.' );
		
	}

}


/**
 * Updates the status of the commission attached to a payment to "unpaid", thus marking it as complete
 *
 * @param int $payment_id
 *
 */
function slicewp_accept_pending_commission_edd( $payment_id ) {

	// Check to see if a commission for this payment has been registered
	$commissions = slicewp_get_commissions( array( 'reference' => $payment_id, 'origin' => 'edd' ) );

	if( empty( $commissions ) )
		return;

	// Set commission
	$commission = $commissions[0];

	// Return if the commission has already been paid
	if( $commission->get('status') == 'paid' )
		return;

	// Prepare commission data
	$commission_data = array(
		'date_modified' => slicewp_mysql_gmdate(),
		'status' => 'unpaid'
	);

	// Update the commission
	$updated = slicewp_update_commission( $commission->get('id'), $commission_data );

	if( false !== $updated )
		slicewp_add_log( sprintf( 'EDD: Pending commission #%s successfully marked as completed.', $commission->get('id') ) );

	else
		slicewp_add_log( 'EDD: Pending commission could not be completed due to an unexpected error.' );

}


/**
 * Update the status of the commission to "rejected" when the originating purchase is refunded
 *
 * @param int    $payment_id
 * @param string $new_status
 * @param string $old_status
 *
 */
function slicewp_reject_commission_on_refund_edd( $payment_id, $new_status, $old_status ) {

	if( ! slicewp_get_setting( 'reject_commissions_on_refund', false ) )
		return;

	if( $new_status != 'refunded' )
		return;

	// Check to see if a commission for this payment has been registered
	$commissions = slicewp_get_commissions( array( 'reference' => $payment_id, 'origin' => 'edd' ) );

	if( empty( $commissions ) )
		return;

	// Set commission
	$commission = $commissions[0];

	if( $commission->get('status') == 'paid' ) {

		slicewp_add_log( 'EDD: Commission could not be rejected because it was already paid.' );
		return;

	}

	// Prepare commission data
	$commission_data = array(
		'date_modified' => slicewp_mysql_gmdate(),
		'status' => 'rejected'
	);

	// Update the commission
	$updated = slicewp_update_commission( $commission->get('id'), $commission_data );

	if( false !== $updated )
		slicewp_add_log( sprintf( 'EDD: Commission #%s successfully marked as rejected, after payment #%s was refunded.', $commission->get('id'), $payment_id ) );

	else
		slicewp_add_log( sprintf( 'EDD: Commission #%s could not be rejected due to an unexpected error.', $commission->get('id') ) );

}


/**
 * Update the status of the commission to "rejected" when the originating purchase is deleted
 *
 * @param int $payment_id
 *
 */
function slicewp_reject_commission_on_delete_edd( $payment_id ) {

	// Check to see if a commission for this payment has been registered
	$commissions = slicewp_get_commissions( array( 'reference' => $payment_id, 'origin' => 'edd' ) );

	if( empty( $commissions ) )
		return;

	// Set commission
	$commission = $commissions[0];

	if( $commission->get('status') == 'paid' ) {

		slicewp_add_log( 'EDD: Commission could not be rejected because it was already paid.' );
		return;

	}

	// Prepare commission data
	$commission_data = array(
		'date_modified' => slicewp_mysql_gmdate(),
		'status' => 'rejected'
	);

	// Update the commission
	$updated = slicewp_update_commission( $commission->get('id'), $commission_data );

	if( false !== $updated )
		slicewp_add_log( sprintf( 'EDD: Commission #%s successfully marked as rejected, after payment #%s was deleted.', $commission->get('id'), $payment_id ) );

	else
		slicewp_add_log( sprintf( 'EDD: Commission #%s could not be rejected due to an unexpected error.', $commission->get('id') ) );

}


/**
 * Adds the commissions settings metabox
 * 
 * @param string $post_type
 * @param WP_Post $post
 * 
 */
function slicewp_add_commission_settings_metabox_edd( $post_type, $post ) {

    // Check that post type is 'download'
    if ( $post_type != 'download' )
        return;

    // Add the meta box
    add_meta_box( 'slicewp_metabox_commission_settings_edd', sprintf( __( '%1$s Commission Settings', 'slicewp' ), edd_get_label_singular(), edd_get_label_plural() ),  'slicewp_add_product_commission_settings_edd', $post_type, 'side', 'default' );

}

/**
 * Adds the product commission settings fields in EDD add/edit download page
 * 
 * 
 */
function slicewp_add_product_commission_settings_edd() {

    global $post;

    // Get the disable commissions value
    $disable_commissions = get_post_meta( $post->ID, 'slicewp_disable_commissions', true );

?>

    <div id="slicewp_product_settings" class="slicewp-options-groups-wrapper">

        <?php

            /**
             * Hook to add option groups before the core one
             * 
             */
            do_action( 'slicewp_edd_metabox_commission_settings_top' );

        ?>

        <div class="slicewp-options-group">

			<?php

				// Get the product categories
				$categories = get_the_terms( $post->ID, 'download_category' );

			?>

			<?php if ( ! empty( $categories[0]->term_id ) && get_term_meta( $categories[0]->term_id, 'slicewp_disable_commissions', true ) ): ?>

				<p class="slicewp-product-commissions-disabled"><?php echo __( 'The product commission rate settings are not available because the commissions for this product category are disabled.', 'slicewp' ); ?></p>

			<?php else: ?>

				<?php
					
					/**
					 * Hook to add settings before the core ones
					 * 
					 */
					do_action( 'slicewp_edd_metabox_commission_settings_core_top' );

				?>

				<p class="slicewp-option-field-wrapper">
					<label for="slicewp-disable-commissions">
						<input type="checkbox" class="slicewp-option-field-disable-commissions" name="slicewp_disable_commissions" id="slicewp-disable-commissions" value="1" <?php checked( $disable_commissions, true ); ?> />
						<?php echo sprintf( __( 'Disable commissions for this %s', 'slicewp' ), strtolower( edd_get_label_singular() ) ); ?>
					</label>
				</p>

				<?php

					/**
					 * Hook to add settings after the core ones
					 * 
					 */
					do_action( 'slicewp_edd_metabox_commission_settings_core_bottom' );
				?>

			<?php endif; ?>

        </div>

        <?php

            /**
             * Hook to add option groups after the core one
             * 
             */
            do_action( 'slicewp_edd_metabox_commission_settings_bottom' );
        
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
function slicewp_save_product_commission_settings_edd( $post_id, $post ) {

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
 * Adds the Category Rate and Type fields in the add new product category page
 * 
 */
function slicewp_add_category_commision_settings_edd() {

    /**
     * Hook to add fields before the core ones
     * 
     */
    do_action( 'slicewp_edd_add_category_form_fields_top' );
    
	?>

    <div class="slicewp-option-field-wrapper form-field">

        <label for="slicewp-disable-commissions">
            <input type="checkbox" class="slicewp-option-field-disable-commissions checkbox" name="slicewp_disable_commissions" id="slicewp-disable-commissions"/><?php echo __( 'Disable Commissions', 'slicewp' ); ?>
        </label>
        <p><?php echo __( 'When checked, commissions will not be generated for this product category.', 'slicewp' ); ?></p>

    </div>

	<?php

    /**
     * Hook to add fields after the core ones
     * 
     */
    do_action( 'slicewp_edd_add_category_form_fields_bottom' );

}


/**
 * Adds the disable commissions checkbox in the edit product category page
 * 
 * @param WP_Term $category
 * 
 */
function slicewp_edit_category_commision_settings_edd( $category ) {

    // Get the product category commission settings
    $current_category_disable_commissions = get_term_meta( $category->term_id, 'slicewp_disable_commissions', true );

    /**
     * Hook to add fields before the core ones
     * 
     */
    do_action( 'slicewp_edd_edit_category_form_fields_top', $category );
    
?>

    <tr class="slicewp-option-field-wrapper form-field">
        <th scope="row">
            <label for="slicewp-disable-commissions"><?php echo __( 'Disable Commissions', 'slicewp' ); ?></label>
        </th>
        <td>
            <input type="checkbox" class="slicewp-option-field-disable-commissions checkbox" name="slicewp_disable_commissions" id="slicewp-disable-commissions" <?php checked( $current_category_disable_commissions, true ); ?>/>
            <p class="description"><?php echo __( 'When checked, commissions will not be generated for this product category.', 'slicewp' ); ?></p>
        </td>
    </tr>

<?php

    /**
     * Hook to add fields after the core ones
     * 
     */
    do_action( 'slicewp_edd_edit_category_form_fields_bottom', $category );


}


/**
 * Saves the product category commission settings into the category meta
 * 
 * @param int $category_id
 * 
 */
function slicewp_save_category_commission_settings_edd( $category_id ) {

    // Update the disable commissions settings
    if ( ! empty( $_POST['slicewp_disable_commissions'] ) ) {

        update_term_meta( $category_id, 'slicewp_disable_commissions', 1 );
    
    } else {

        delete_term_meta( $category_id, 'slicewp_disable_commissions' );
    
    }
}