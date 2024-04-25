<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Exit if parameters are not given
if ( empty( $_GET['date_min'] ) || empty ( $_GET['date_max'] ) || ! isset ( $_GET['payments_minimum_amount'] ) || ! isset ( $_GET['payout_amount'] ) || ! isset ( $_GET['payments_count'] ) )
	exit;

// Get the affiliate
if ( ! empty( $_GET['affiliate_id'] ) ){

	$affiliate = slicewp_get_affiliate( absint( $_GET['affiliate_id'] ) );
	
	if ( isset( $affiliate ) )
		$user = get_user_by( 'id', $affiliate->get('user_id') );

}

?>

<div class="wrap slicewp-wrap slicewp-wrap-preview-payout">

	<form method="POST">

		<!-- Page Heading -->
		<h1 class="wp-heading-inline"><?php echo __( 'Preview Payout', 'slicewp' ); ?></h1>
		<hr class="wp-header-end" />
        
		<!-- Postbox -->
		<div class="slicewp-card slicewp-first">

			<!-- Form Fields -->
			<div class="slicewp-card-inner">

<?php if ( isset( $affiliate ) ): ?>

				<!-- Affiliate Name -->
            	<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

                    <div class="slicewp-field-label-wrapper">
                        <label for="slicewp-affiliate-user-id"><?php echo __( 'Affiliate Name', 'slicewp' ); ?></label>
        			</div>

					<input id="slicewp-affiliate-user-id" type="text" disabled value="<?php echo ( $user->display_name . ' (' . $user->user_email . ')' ); ?>" />
					<input type="hidden" name="affiliate_id" value="<?php echo ( ! empty( $_GET['affiliate_id'] ) ? esc_attr( $_GET['affiliate_id'] ) : '' ); ?>" />
               	
                </div>

<?php endif;?>

				<!-- Date Min -->
            	<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

                    <div class="slicewp-field-label-wrapper">
                        <label for="slicewp-date-min"><?php echo __( 'Start Date', 'slicewp' ); ?> *</label>
                    </div>

                    <input id="slicewp-date-min" type="text" disabled value="<?php echo ( ! empty( $_GET['date_min'] ) ? esc_attr( $_GET['date_min'] ) : '' ); ?>" />
                    <input type="hidden" name="date_min" value="<?php echo ( ! empty( $_GET['date_min'] ) ? esc_attr( $_GET['date_min'] ) : '' ); ?>" />
                
                </div>

				<!-- Date max -->
            	<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

                    <div class="slicewp-field-label-wrapper">
                        <label for="slicewp-date-max"><?php echo __( 'End Date', 'slicewp' ); ?> *</label>
                    </div>

                    <input id="slicewp-date-max" type="text" disabled value="<?php echo ( ! empty( $_GET['date_max'] ) ? esc_attr( $_GET['date_max'] ) : '' ); ?>" />
                    <input type="hidden" name="date_max" value="<?php echo ( ! empty( $_GET['date_max'] ) ? esc_attr( $_GET['date_max'] ) : '' ); ?>" />
                
                </div>

				<!-- Minimum amount -->
            	<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

                    <div class="slicewp-field-label-wrapper">
                        <label for="slicewp-payments-minimum-amount"><?php echo __( 'Minimum Amount', 'slicewp' ); ?></label>
                    </div>

					<input id="slicewp-payments-minimum-amount" type="text" disabled value="<?php echo slicewp_format_amount( isset( $_GET['payments_minimum_amount'] ) ? esc_attr( $_GET['payments_minimum_amount'] ) : slicewp_get_setting( 'payments_minimum_amount' ), slicewp_get_setting( 'active_currency', 'USD' ) ); ?>" />
					<input type="hidden" name="payments_minimum_amount" value="<?php echo( isset( $_GET['payments_minimum_amount'] ) ? esc_attr( $_GET['payments_minimum_amount'] ) : slicewp_get_setting( 'payments_minimum_amount' ) ); ?>" />
                
				</div>
				
				<!-- Payout amount -->
            	<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

                    <div class="slicewp-field-label-wrapper">
                        <label for="slicewp-payout-amount"><?php echo __( 'Payout Amount', 'slicewp' ); ?></label>
                    </div>

					<input id="slicewp-payout-amount" type="text" disabled value="<?php echo( isset( $_GET['payout_amount'] ) ? esc_attr( slicewp_format_amount( $_GET['payout_amount'], slicewp_get_setting( 'active_currency', 'USD' ) ) ) : '' ); ?>" />
					<input type="hidden" name="payout_amount" value="<?php echo( isset( $_GET['payout_amount'] ) ? esc_attr( slicewp_format_amount( $_GET['payout_amount'], slicewp_get_setting( 'active_currency', 'USD' ) ) ) : '' ); ?>" />
                
				</div>

				<!-- Payments count -->
            	<div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-last">

                    <div class="slicewp-field-label-wrapper">
                        <label for="slicewp-payments-count"><?php echo __( 'Payments Count', 'slicewp' ); ?></label>
                    </div>

					<input id="slicewp-payments-count" type="text" disabled value="<?php echo( isset( $_GET['payments_count'] ) ? esc_attr( $_GET['payments_count'] ) : '' ); ?>" />
					<input type="hidden" name="payments_count" value="<?php echo( isset( $_GET['payments_count'] ) ? esc_attr( $_GET['payments_count'] ) : '' ); ?>" />
                
				</div>

			</div>

		</div>
		
		<!-- Payment List Table -->
		<?php 
			$table = new SliceWP_WP_List_Table_Payout_Payments_Preview();
			$table->display();
		?>        

		<!-- Hidden fields needed for the search query -->
		<input type="hidden" name="page" value="slicewp-payments">

		<!-- Action and nonce -->
		<input type="hidden" name="slicewp_action" value="create_payout" />
		<?php wp_nonce_field( 'slicewp_create_payout', 'slicewp_token', false ); ?>

		<!-- Submit -->
		<input type="submit" class="slicewp-form-submit slicewp-button-primary" name="slicewp_create_payout" value="<?php echo __( 'Create Payout', 'slicewp' ); ?>" />

	</form>

</div>