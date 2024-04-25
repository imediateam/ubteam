<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Add commission table reference column edit screen link
add_filter( 'slicewp_list_table_commissions_row_data', 'slicewp_list_table_commissions_add_reference_edit_link_pms' );
add_filter( 'slicewp_list_table_payout_commissions_row_data', 'slicewp_list_table_commissions_add_reference_edit_link_pms' );

// Insert a new pending commission
add_action( 'pms_register_payment', 'slicewp_insert_pending_commission_pms', 10, 1 );

// Update the status of the commission to "unpaid", thus marking it as complete
add_action( 'pms_payment_update', 'slicewp_accept_pending_commission_pms', 10, 3 );

// Update the status of the commission to "rejected" when the originating payment is failed
add_action( 'pms_payment_update', 'slicewp_reject_commission_on_fail_pms', 10, 3 );

// Update the status of the commission to "rejected" when the originating payment is refunded
add_action( 'pms_payment_update', 'slicewp_reject_commission_on_refund_pms', 10, 3 );

// Update the status of the commission to "rejected" when the originating payment is deleted
add_action( 'pms_payment_delete', 'slicewp_reject_commission_on_delete_pms', 10, 1 );

// Add the commission settings in download page
add_action( 'add_meta_boxes', 'slicewp_add_commission_settings_metabox_pms', 10, 2 );

// Saves the commissions settings in download meta
add_action( 'pms_save_meta_box_pms-subscription', 'slicewp_save_product_commission_settings_pms', 10, 2 );


/**
 * Adds the edit screen link to the reference column value from the commissions list table
 *
 * @param array $row_data
 *
 * @return array
 *
 */
function slicewp_list_table_commissions_add_reference_edit_link_pms( $row_data ) {

	if( empty( $row_data['reference'] ) )
		return $row_data;

	if( empty( $row_data['origin'] ) || $row_data['origin'] != 'pms' )
		return $row_data;

    // Get the payment
    $payment = pms_get_payment( $row_data['reference'] );

    // Create link to payment only if the payment exists
    if( ! empty( $payment->id ) )
        $row_data['reference'] = '<a href="' . add_query_arg( array( 'page' => 'pms-payments-page', 'pms-action' => 'edit_payment', 'payment_id' => $row_data['reference'] ), admin_url( 'admin.php' ) ) . '">' . $row_data['reference'] . '</a>';
    
    return $row_data;
    
}


/**
 * Inserts a new pending commission when a new payment is registered
 *
 * @param array $payment_data
 *
 */
function slicewp_insert_pending_commission_pms( $payment_data ) {

    // Verify if commissions are disabled for the purchased subscription
    if ( get_post_meta( $payment_data['subscription_plan_id'], 'slicewp_disable_commissions', true ) )
        return;

	// Get and check to see if referrer exists
	$affiliate_id = slicewp_get_referrer_affiliate_id();
	$visit_id	  = slicewp_get_referrer_visit_id();

	/**
	 * Filters the referrer affiliate ID for Paid Member Subscriptions.
	 * This is mainly used by add-ons for different functionality.
	 *
	 * @param int $affiliate_id
	 * @param int $payment_id
	 *
	 */
	$affiliate_id = apply_filters( 'slicewp_referrer_affiliate_id_pms', $affiliate_id, $payment_data['payment_id'] );

	if( empty( $affiliate_id ) ) 
		return;

	// Verify if the affiliate is valid
	if ( ! slicewp_is_affiliate_valid( $affiliate_id ) ){

		slicewp_add_log( 'PMS: Pending commission was not created because the affiliate is not valid.' );
		return;

	}

	// Check to see if the payment is a renewal or not
    $is_renewal = ( in_array( $payment_data['type'] , array( 'manual_payment', 'web_accept_paypal_standard', 'subscription_initial_payment') ) ? '' : 'is_renewal' );

	if( ! empty( $is_renewal ) ) {

        slicewp_add_log( 'PMS: Pending commission was not created because the payment is a renewal.' );
		return;

	}

	// Check to see if a commission for this payment has been registered
	$commissions = slicewp_get_commissions( array( 'reference' => $payment_data['payment_id'], 'origin' => 'pms' ) );

	if( ! empty( $commissions ) ) {

		slicewp_add_log( 'PMS: Pending commission was not created because another commission for the reference and origin already exists.' );
		return;

	}

	// Check to see if the affiliate made the purchase
	if( empty( slicewp_get_setting( 'affiliate_own_commissions' ) ) ) {

		$affiliate = slicewp_get_affiliate( $affiliate_id );

		if( is_user_logged_in() && get_current_user_id() == $affiliate->get('user_id') ) {

			slicewp_add_log( 'PMS: Pending commission was not created because the customer is also the affiliate.' );
			return;

		}

		// Get the user
		$user = get_userdata( $payment_data['user_id'] );

		// Check to see if the affiliate made the purchase, as we don't want this
		if( slicewp_affiliate_has_email( $affiliate_id, $user->user_email ) ) {

			slicewp_add_log( 'PMS: Pending commission was not created because the customer is also the affiliate.' );
			return;

		}

	}

	// Get the order amount. Exclude tax
	$amount = $payment_data['amount'];
	
	if ( defined('PMS_TAX_VERSION') && slicewp_get_setting( 'exclude_tax', false ) ){

		$tax 	= pms_tax_determine_tax_breakdown( $payment_data['payment_id'] );	
		$amount = empty( $tax ) ? $amount : $tax['subtotal'];
		
	}

	// Calculate the commission amount for the entire payment
    $args = array(
        'origin'	   => 'pms',
        'type' 		   => 'subscription',
        'affiliate_id' => $affiliate_id,
        'product_id'   => $payment_data['subscription_plan_id']
    );

    $commission_amount = slicewp_calculate_commission_amount( $amount, $args );

    // Check that the commission amount is not zero
    if ( ( $commission_amount == 0 ) && empty( slicewp_get_setting( 'zero_amount_commissions' ) ) ) {

        slicewp_add_log( 'PMS: Commission was not inserted because the commission amount is zero. Payment: ' . absint( $payment_data['payment_id'] ) );
        return;

    }


    // Prepare commission data
	$commission_data = array(
		'affiliate_id'  => $affiliate_id,
		'visit_id'		=> ( ! is_null( $visit_id ) ? $visit_id : 0 ),
		'date_created'  => slicewp_mysql_gmdate(),
		'date_modified' => slicewp_mysql_gmdate(),
		'type'			=> 'subscription',
		'status'		=> 'pending',
		'reference'     => $payment_data['payment_id'],
		'origin' 	    => 'pms',
		'amount'		=> slicewp_sanitize_amount( $commission_amount ),
		'currency'		=> slicewp_get_setting( 'active_currency', 'USD' )
	);

	// Insert the commission
	$commission_id = slicewp_insert_commission( $commission_data );

	if( ! empty( $commission_id ) ) {

		// Update the visit with the newly inserted commission_id
		if ( ! is_null( $visit_id ) ){

			slicewp_update_visit( $visit_id, array( 'commission_id' => $commission_id ) );
			slicewp_add_log( sprintf( 'PMS: Pending commission #%s has been successfully inserted.', $commission_id ) );
		
		}
		
	} else {

		slicewp_add_log( 'PMS: Pending commission could not be inserted due to an unexpected error.' );
		
	}

}


/**
 * Updates the status of the commission attached to a payment to "unpaid", thus marking it as complete
 *
 * @param int $payment_id
 * @param array $new_data
 * @param array $old_data
 *
 */
function slicewp_accept_pending_commission_pms( $payment_id, $new_data, $old_data ) {

    // Check if the new payment status is 'completed'
    if ( ! isset( $new_data['status'] ) || $new_data['status'] != 'completed' )
        return;

	// Check to see if a commission for this payment has been registered
	$commissions = slicewp_get_commissions( array( 'reference' => $payment_id, 'origin' => 'pms' ) );

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
		slicewp_add_log( sprintf( 'PMS: Pending commission #%s successfully marked as completed.', $commission->get('id') ) );

	else
		slicewp_add_log( 'PMS: Pending commission could not be completed due to an unexpected error.' );

}


/**
 * Update the status of the commission to "rejected" when the originating payment is failed
 *
 * @param int   $payment_id
 * @param array $new_data
 * @param array $old_data
 *
 */
function slicewp_reject_commission_on_fail_pms( $payment_id, $new_data, $old_data ) {

    if ( ! isset( $new_data['status'] ) || $new_data['status'] != 'failed' )
		return;

	// Check to see if a commission for this payment has been registered
	$commissions = slicewp_get_commissions( array( 'reference' => $payment_id, 'origin' => 'pms' ) );

	if( empty( $commissions ) )
		return;

	// Set commission
	$commission = $commissions[0];

	if( $commission->get('status') == 'paid' ) {

		slicewp_add_log( 'PMS: Commission could not be rejected because it was already paid.' );
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
		slicewp_add_log( sprintf( 'PMS: Commission #%s successfully marked as rejected, after payment #%s failed.', $commission->get('id'), $payment_id ) );

	else
		slicewp_add_log( sprintf( 'PMS: Commission #%s could not be rejected due to an unexpected error.', $commission->get('id') ) );

}


/**
 * Update the status of the commission to "rejected" when the originating payment is refunded
 *
 * @param int   $payment_id
 * @param array $new_data
 * @param array $old_data
 *
 */
function slicewp_reject_commission_on_refund_pms( $payment_id, $new_data, $old_data ) {

	if( ! slicewp_get_setting( 'reject_commissions_on_refund', false ) )
		return;

	if ( ! isset( $new_data['status'] ) || $new_data['status'] != 'refunded' )
		return;

	// Check to see if a commission for this payment has been registered
	$commissions = slicewp_get_commissions( array( 'reference' => $payment_id, 'origin' => 'pms' ) );

	if( empty( $commissions ) )
		return;

	// Set commission
	$commission = $commissions[0];

	if( $commission->get('status') == 'paid' ) {

		slicewp_add_log( 'PMS: Commission could not be rejected because it was already paid.' );
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
		slicewp_add_log( sprintf( 'PMS: Commission #%s successfully marked as rejected, after payment #%s was refunded.', $commission->get('id'), $payment_id ) );

	else
		slicewp_add_log( sprintf( 'PMS: Commission #%s could not be rejected due to an unexpected error.', $commission->get('id') ) );

}


/**
 * Update the status of the commission to "rejected" when the originating payment is deleted
 *
 * @param int $payment_id
 *
 */
function slicewp_reject_commission_on_delete_pms( $payment_id ) {

	// Check to see if a commission for this payment has been registered
	$commissions = slicewp_get_commissions( array( 'reference' => $payment_id, 'origin' => 'pms' ) );

	if( empty( $commissions ) )
		return;

	// Set commission
	$commission = $commissions[0];

	if( $commission->get('status') == 'paid' ) {

		slicewp_add_log( 'PMS: Commission could not be rejected because it was already paid.' );
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
		slicewp_add_log( sprintf( 'PMS: Commission #%s successfully marked as rejected, after payment #%s was deleted.', $commission->get('id'), $payment_id ) );

	else
		slicewp_add_log( sprintf( 'PMS: Commission #%s could not be rejected due to an unexpected error.', $commission->get('id') ) );

}


/**
 * Adds the commissions settings metabox
 * 
 * @param string $post_type
 * @param WP_Post $post
 * 
 */
function slicewp_add_commission_settings_metabox_pms( $post_type, $post ) {

    // Check that post type is 'pms-subscription'
    if ( $post_type != 'pms-subscription' )
        return;

    // Add the meta box
    add_meta_box( 'slicewp_metabox_commission_settings_pms', __( 'Subscription Commission Settings', 'slicewp' ), 'slicewp_add_product_commission_settings_pms', $post_type, 'normal', 'default' );

}


/**
 * Adds the product commission settings fields in PMS add/edit subscription page
 *
 */
function slicewp_add_product_commission_settings_pms() {

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
            do_action( 'slicewp_pms_metabox_commission_settings_top' );

        ?>

        <div class="slicewp-options-group">

            <?php
                
                /**
                 * Hook to add settings before the core ones
                 * 
                 */
                do_action( 'slicewp_pms_metabox_commission_settings_core_top' );

            ?>

            <p class="slicewp-option-field-wrapper pms-meta-box-field-wrapper">
            	<label for="slicewp-disable-commissions" class="pms-meta-box-field-label"><?php echo __( 'Disable Commissions', 'slicewp' ); ?></label>
                <label for="slicewp-disable-commissions">
                    <input type="checkbox" class="slicewp-option-field-disable-commissions" name="slicewp_disable_commissions" id="slicewp-disable-commissions" value="1" <?php checked( $disable_commissions, true ); ?> />
                    <?php echo __( 'Disable all commissions for this subscription plan.', 'slicewp' ); ?>
                </label>
            </p>

            <?php

                /**
                 * Hook to add settings after the core ones
                 * 
                 */
                do_action( 'slicewp_pms_metabox_commission_settings_core_bottom' );
            ?>

        </div>

        <?php

            /**
             * Hook to add option groups after the core one
             * 
             */
            do_action( 'slicewp_pms_metabox_commission_settings_bottom' );
        
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
function slicewp_save_product_commission_settings_pms( $post_id, $post ) {

    // Update the disable commissions settings
    if ( ! empty( $_POST['slicewp_disable_commissions'] ) ) {

        update_post_meta( $post_id, 'slicewp_disable_commissions', 1 );

    } else {

        delete_post_meta( $post_id, 'slicewp_disable_commissions' );

    }

}