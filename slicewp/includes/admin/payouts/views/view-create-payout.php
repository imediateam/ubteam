<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="wrap slicewp-wrap slicewp-wrap-payments">

	<form method="POST">

		<!-- Page Heading -->
		<h1 class="wp-heading-inline"><?php echo __( 'Create Payout', 'slicewp' ); ?></h1>
		<hr class="wp-header-end" />
        
		<!-- Postbox -->
		<div class="slicewp-card slicewp-first">

			<!-- Form Fields -->
			<div class="slicewp-card-inner">

				<!-- Affiliate Name -->
            	<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

                    <div class="slicewp-field-label-wrapper">
                        <label for="slicewp-affiliate-user-id"><?php echo __( 'Affiliate Name', 'slicewp' ); ?></label>
						<?php echo slicewp_output_tooltip( __( 'Entering an affiliate name will create a payment only for the selected affiliate. Leaving it blank will create payments for all the affiliates.', 'slicewp' ) ); ?>
        			</div>

					<input id="slicewp-affiliate-user-id" class="slicewp-field-users-autocomplete" data-affiliates="include" data-return-value="affiliate_id" autocomplete="off" name="user_search" type="text" placeholder="<?php echo __( 'John Doe', 'slicewp' ); ?>" value="<?php echo ( ! empty( $_POST['user_search'] ) ? esc_attr( $_POST['user_search'] ) : '' ); ?>" />
					<input type="hidden" name="affiliate_id" value="<?php echo ( ! empty( $_POST['affiliate_id'] ) ? esc_attr( $_POST['affiliate_id'] ) : '' ); ?>" />
                	
                	<?php wp_nonce_field( 'slicewp_user_search', 'slicewp_user_search_token', false ); ?>
                	
                </div>

				<!-- Date Min -->
            	<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

                    <div class="slicewp-field-label-wrapper">
                        <label for="slicewp-date-min"><?php echo __( 'Start Date', 'slicewp' ); ?> *</label>
                    </div>

                    <input id="slicewp-date-min" type="text" name="date_min" class="slicewp-datepicker" autocomplete="off" value="<?php echo ( ! empty( $_POST['date_min'] ) ? esc_attr( $_POST['date_min'] ) : '' )?>"/>
                
                </div>

				<!-- Date max -->
            	<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

                    <div class="slicewp-field-label-wrapper">
                        <label for="slicewp-date-max"><?php echo __( 'End Date', 'slicewp' ); ?> *</label>
                    </div>

                    <input id="slicewp-date-max" type="text" name="date_max" class="slicewp-datepicker" autocomplete="off" value="<?php echo ( ! empty( $_POST['date_max'] ) ? esc_attr( $_POST['date_max'] ) : '' )?>"/>
                
                </div>

				<!-- Minimum amount -->
            	<div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-last">

                    <div class="slicewp-field-label-wrapper">
                        <label for="slicewp-payments-minimum-amount"><?php echo __( 'Payments Minimum Amount', 'slicewp' ); ?></label>
						<?php echo slicewp_output_tooltip( __( 'The payment will be generated only if its commissions sum is greater than this value. If is set to 0, the payments will be generated for all the commissions.	', 'slicewp' ) ); ?>
                    </div>

					<input id="slicewp-payments-minimum-amount" name="payments_minimum_amount" type="text" value="<?php echo ( ! empty( $_POST['payments_minimum_amount'] ) ? esc_attr( $_POST['payments_minimum_amount'] ) : ( ! empty( slicewp_get_setting( 'payments_minimum_amount' ) ) ? slicewp_get_setting( 'payments_minimum_amount' ) : 0 ) ); ?>" />
                
                </div>                
			</div>

		</div>

		<!-- Hidden fields needed at submit -->
		<input type="hidden" name="page" value="slicewp-payouts">
		<input type="hidden" name="subpage" value="preview-payout">

		<!-- Action and nonce -->
		<input type="hidden" name="slicewp_action" value="preview_payout" />

		<!-- Submit -->
		<input type="submit" class="slicewp-form-submit slicewp-button-primary" name="slicewp_preview_payout" value="<?php echo __( 'Preview Payout', 'slicewp' ); ?>" />

	</form>

</div>