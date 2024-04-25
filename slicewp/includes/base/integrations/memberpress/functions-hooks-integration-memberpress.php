<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Add commission table reference column edit screen link
add_filter( 'slicewp_list_table_commissions_row_data', 'slicewp_list_table_commissions_add_reference_edit_link_mepr' );
add_filter( 'slicewp_list_table_payout_commissions_row_data', 'slicewp_list_table_commissions_add_reference_edit_link_mepr' );

// Insert a new pending commission
add_action( 'mepr-txn-status-pending', 'slicewp_insert_pending_commission_mepr', 10, 1 );

// Update the status of the commission to "unpaid", thus marking it as complete
add_action( 'mepr-txn-status-complete', 'slicewp_accept_pending_commission_mepr', 10, 1 );

// Update the status of the commission to "rejected" when the originating transaction is failed
add_action( 'mepr-txn-status-failed', 'slicewp_reject_commission_on_fail_mepr', 10, 1 );

// Update the status of the commission to "rejected" when the originating transaction is refunded
add_action( 'mepr-txn-status-refunded', 'slicewp_reject_commission_on_refund_mepr', 10, 1 );

// Update the status of the commission to "rejected" when the originating transaction is deleted
add_action( 'mepr_pre_delete_transaction', 'slicewp_reject_commission_on_delete_mepr', 10, 1 );

// Add the commission settings in download page
add_action( 'add_meta_boxes', 'slicewp_add_commission_settings_metabox_mepr', 10, 2 );

// Save the affiliate id in the product meta
add_action( 'save_post_memberpressproduct', 'slicewp_save_product_commission_settings_mepr', 10, 2 );


/**
 * Adds the edit screen link to the reference column value from the commissions list table
 *
 * @param array $row_data
 *
 * @return array
 *
 */
function slicewp_list_table_commissions_add_reference_edit_link_mepr( $row_data ) {

	if( empty( $row_data['reference'] ) )
		return $row_data;

	if( empty( $row_data['origin'] ) || $row_data['origin'] != 'mepr' )
		return $row_data;

    // Get the transaction
    $transaction = new MeprTransaction( $row_data['reference'] );

    // Create link to payment only if the payment exists
    if( ! empty( $transaction->id ) )
        $row_data['reference'] = '<a href="' . add_query_arg( array( 'page' => 'memberpress-trans', 'action' => 'edit', 'id' => $row_data['reference'] ), admin_url( 'admin.php' ) ) . '">' . $row_data['reference'] . '</a>';
    
    return $row_data;
    
}


/**
 * Inserts a new pending commission when a new transaction is registered
 *
 * @param MeprTransaction $transaction
 *
 */
function slicewp_insert_pending_commission_mepr( $transaction ) {

    // Verify if commissions are disabled for the purchased product
    if ( get_post_meta( $transaction->product_id, 'slicewp_disable_commissions', true ) )
        return;

    // Get and check to see if referrer exists
	$affiliate_id = slicewp_get_referrer_affiliate_id();
	$visit_id	  = slicewp_get_referrer_visit_id();

	/**
	 * Filters the referrer affiliate ID for MemberPress.
	 * This is mainly used by add-ons for different functionality.
	 *
	 * @param int $affiliate_id
	 * @param int $transaction->id
	 *
	 */
	$affiliate_id = apply_filters( 'slicewp_referrer_affiliate_id_mepr', $affiliate_id, $transaction->id );

	if( empty( $affiliate_id ) ) {

		slicewp_add_log( 'MEPR: Pending commission was not created because the affiliate is not valid.' );
		return;

	}

	// Check to see if a commission for this transaction has been registered
	$commissions = slicewp_get_commissions( array( 'reference' => $transaction->id, 'origin' => 'mepr' ) );

	if( ! empty( $commissions ) )
		return;

	// Check to see if the affiliate made the purchase
	if( empty( slicewp_get_setting( 'affiliate_own_commissions' ) ) ) {

		$affiliate = slicewp_get_affiliate( $affiliate_id );

		if( is_user_logged_in() && get_current_user_id() == $affiliate->get('user_id') ) {

			slicewp_add_log( 'MEPR: Pending commission was not created because the customer is also the affiliate.' );
			return;

		}

		// Get the user
		$user = get_userdata( $transaction->user_id );

		// Check to see if the affiliate made the purchase, as we don't want this
		if( slicewp_affiliate_has_email( $affiliate_id, $user->user_email ) ) {

			slicewp_add_log( 'MEPR: Pending commission was not created because the customer is also the affiliate.' );
			return;

		}

	}

    // Get the order amount. Exclude tax
    if( slicewp_get_setting( 'exclude_tax', false ) )
		$amount = $transaction->amount;
	else
		$amount = $transaction->total;

    // Calculate the commission amount for the entire transaction
    $args = array(
        'origin'	   => 'mepr',
        'type' 		   => 'subscription',
        'affiliate_id' => $affiliate_id,
        'product_id'   => $transaction->product_id
    );

    $commission_amount = slicewp_calculate_commission_amount( $amount, $args );

    // Check that the commission amount is not zero
    if ( ( $commission_amount == 0 ) && empty( slicewp_get_setting( 'zero_amount_commissions' ) ) ) {

        slicewp_add_log( 'MEPR: Commission was not inserted because the commission amount is zero. Transaction: ' . absint( $transaction->id ) );
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
		'reference'     => $transaction->id,
		'origin' 	    => 'mepr',
		'amount'		=> slicewp_sanitize_amount( $commission_amount ),
		'currency'		=> slicewp_get_setting( 'active_currency', 'USD' )
	);

	// Insert the commission
	$commission_id = slicewp_insert_commission( $commission_data );

	if( ! empty( $commission_id ) ) {

		// Update the visit with the newly inserted commission_id
		if ( ! is_null( $visit_id ) ){

			slicewp_update_visit( $visit_id, array( 'commission_id' => $commission_id ) );
			
		}
		
		slicewp_add_log( sprintf( 'MEPR: Pending commission #%s has been successfully inserted.', $commission_id ) );
		
	} else {

		slicewp_add_log( 'MEPR: Pending commission could not be inserted due to an unexpected error.' );
		
	}

}


/**
 * Updates the status of the commission attached to a transaction to "unpaid", thus marking it as complete
 *
 * @param MeprTransaction $transaction
 *
 */
function slicewp_accept_pending_commission_mepr( $transaction ) {

	// Check to see if a commission for this transaction has been registered
	$commissions = slicewp_get_commissions( array( 'reference' => $transaction->id, 'origin' => 'mepr' ) );

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
		slicewp_add_log( sprintf( 'MEPR: Pending commission #%s successfully marked as completed.', $commission->get('id') ) );

	else
		slicewp_add_log( 'MEPR: Pending commission could not be completed due to an unexpected error.' );

}


/**
 * Update the status of the commission to "rejected" when the originating transaction is failed
 *
 * @param MeprTransaction $transaction
 *
 */
function slicewp_reject_commission_on_fail_mepr( $transaction ) {

	// Check to see if a commission for this transaction has been registered
	$commissions = slicewp_get_commissions( array( 'reference' => $transaction->id, 'origin' => 'mepr' ) );

	if( empty( $commissions ) )
		return;

	// Set commission
	$commission = $commissions[0];

	if( $commission->get('status') == 'paid' ) {

		slicewp_add_log( 'MEPR: Commission could not be rejected because it was already paid.' );
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
		slicewp_add_log( sprintf( 'MEPR: Commission #%s successfully marked as rejected, after transaction #%s failed.', $commission->get('id'), $transaction->id ) );

	else
		slicewp_add_log( sprintf( 'MEPR: Commission #%s could not be rejected due to an unexpected error.', $commission->get('id') ) );

}


/**
 * Update the status of the commission to "rejected" when the originating transaction is refunded
 *
 * @param MeprTransaction $transaction
 *
 */
function slicewp_reject_commission_on_refund_mepr( $transaction ) {

	if( ! slicewp_get_setting( 'reject_commissions_on_refund', false ) )
		return;

	// Check to see if a commission for this transaction has been registered
	$commissions = slicewp_get_commissions( array( 'reference' => $transaction->id, 'origin' => 'mepr' ) );

	if( empty( $commissions ) )
		return;

	// Set commission
	$commission = $commissions[0];

	if( $commission->get('status') == 'paid' ) {

		slicewp_add_log( 'MEPR: Commission could not be rejected because it was already paid.' );
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
		slicewp_add_log( sprintf( 'MEPR: Commission #%s successfully marked as rejected, after transaction #%s was refunded.', $commission->get('id'), $transaction->id ) );

	else
		slicewp_add_log( sprintf( 'MEPR: Commission #%s could not be rejected due to an unexpected error.', $commission->get('id') ) );

}


/**
 * Update the status of the commission to "rejected" when the originating transaction is deleted
 *
 * @param MeprTransaction $transaction
 *
 */
function slicewp_reject_commission_on_delete_mepr( $transaction ) {

	// Check to see if a commission for this payment has been registered
	$commissions = slicewp_get_commissions( array( 'reference' => $transaction->id, 'origin' => 'mepr' ) );

	if( empty( $commissions ) )
		return;

	// Set commission
	$commission = $commissions[0];

	if( $commission->get('status') == 'paid' ) {

		slicewp_add_log( 'MEPR: Commission could not be rejected because it was already paid.' );
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
		slicewp_add_log( sprintf( 'MEPR: Commission #%s successfully marked as rejected, after payment #%s was deleted.', $commission->get('id'), $transaction->id ) );

	else
		slicewp_add_log( sprintf( 'MEPR: Commission #%s could not be rejected due to an unexpected error.', $commission->get('id') ) );

}


/**
 * Adds the commissions settings metabox
 * 
 * @param string $post_type
 * @param WP_Post $post
 * 
 */
function slicewp_add_commission_settings_metabox_mepr( $post_type, $post ) {
	
    // Check that post type is 'memberpressproduct'
    if ( $post_type != 'memberpressproduct' )
        return;

    // Add the meta box
    add_meta_box( 'slicewp_metabox_commission_settings_mepr', __( 'Subscription Commission Settings', 'slicewp' ), 'slicewp_add_product_commission_settings_mepr', $post_type, 'side', 'default' );

}


/**
 * Adds the product commission settings fields in MemberPress add/edit subscription page
 * 
 * 
 */
function slicewp_add_product_commission_settings_mepr() {

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
            do_action( 'slicewp_mepr_metabox_commission_settings_top' );

        ?>

        <div class="slicewp-options-group">

            <?php
                
                /**
                 * Hook to add settings before the core ones
                 * 
                 */
                do_action( 'slicewp_mepr_metabox_commission_settings_core_top' );

            ?>

            <p class="slicewp-option-field-wrapper">
                <label for="slicewp-disable-commissions">
                    <input type="checkbox" class="slicewp-option-field-disable-commissions" name="slicewp_disable_commissions" id="slicewp-disable-commissions" value="1"<?php checked( $disable_commissions, true ); ?> />
                    <?php echo __( 'Disable commissions for this subscription', 'slicewp' ); ?>
                </label>
            </p>

            <?php

                /**
                 * Hook to add settings after the core ones
                 * 
                 */
                do_action( 'slicewp_mepr_metabox_commission_settings_core_bottom' );
            ?>

        </div>

        <?php

            /**
             * Hook to add option groups after the core one
             * 
             */
            do_action( 'slicewp_mepr_metabox_commission_settings_bottom' );
        
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
function slicewp_save_product_commission_settings_mepr( $post_id, $post ) {

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