<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Verify if a payment id is received
$payment_id = ( ! empty( $_GET['payment_id'] ) ? absint( $_GET['payment_id'] ) : 0 );

if( empty( $payment_id ) )
	return;

// Get the payment information
$payment = slicewp_get_payment( $payment_id );

if( is_null( $payment ) )
	return;

$payout_methods = slicewp_get_payout_methods();

?>

<div class="wrap slicewp-wrap slicewp-wrap-review-payment">

	<form action="" method="POST">

		<!-- Page Heading -->
		<h1 class="wp-heading-inline"><?php echo __( 'Review Payment', 'slicewp' ); ?></h1>
		<hr class="wp-header-end" />

		<div id="slicewp-content-wrapper">
			
			<!-- Primary Content -->
			<div id="slicewp-primary">

				<!-- Postbox -->
				<div class="slicewp-card slicewp-first">

					<div class="slicewp-card-header">
						<?php echo __( 'Payment Details', 'slicewp' ); ?>
					</div>

					<!-- Form Fields -->
					<div class="slicewp-card-inner">

						<!-- Payment ID -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-payment-id"><?php echo __( 'Payment ID', 'slicewp' ); ?></label>
							</div>
							
							<input id="slicewp-payment-id" name="payment_id" disabled type="text" value="<?php echo esc_attr( $payment_id ); ?>" />

						</div>
		                
		                <!-- Affiliate Name -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-affiliate-name"><?php echo __( 'Affiliate', 'slicewp' ); ?></label>
							</div>

							<div class="slicewp-field-link-disabled">
								<?php $affiliate_name = slicewp_get_affiliate_name( $payment->get('affiliate_id') ); ?>
								<?php if( null === $affiliate_name ): ?>
									<span><?php echo __( '(inexistent affiliate)', 'slicewp' ); ?></span>
								<?php else: ?>
									<a href="<?php echo add_query_arg( array( 'page' => 'slicewp-affiliates', 'subpage' => 'edit-affiliate', 'affiliate_id' => $payment->get('affiliate_id') ) , admin_url( 'admin.php' ) ); ?>"><?php echo $affiliate_name; ?></a>
								<?php endif; ?>
							</div>

						</div>
						
		                <!-- Amount -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-payment-amount"><?php echo __( 'Amount', 'slicewp' ); ?></label>
							</div>
							
							<input id="slicewp-payment-amount" name="amount" disabled type="text" value="<?php echo esc_attr( slicewp_format_amount( $payment->get('amount'), $payment->get('currency') ) ); ?>" />

						</div>
						
		                <!-- Payout Method -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-payment-payout-method"><?php echo __( 'Payout Method', 'slicewp' ); ?></label>
							</div>
							
							<input id="slicewp-payment-payout-method" name="payout_method" disabled type="text" value="<?php echo esc_attr( ! empty( $payout_methods[$payment->get('payout_method')]['label'] ) ? $payout_methods[$payment->get('payout_method')]['label'] : $payment->get('payout_method') ); ?>" />

						</div>

		                <!-- Payment Date -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-payment-date"><?php echo __( 'Date Created', 'slicewp' ); ?></label>
							</div>
							
							<input id="slicewp-payment-date" name="date_created" disabled type="text" value="<?php echo slicewp_date_i18n( esc_attr( $payment->get('date_created') ) ); ?>" />

						</div>

						<!-- Payment Status -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-last">

							<div class="slicewp-field-label-wrapper">
								<?php echo slicewp_output_tooltip( __( 'Changing the Payment status will also update the attached commissions statuses.', 'slicewp' ) ); ?>
								<label for="slicewp-payment-status"><?php echo __( 'Status', 'slicewp' ); ?></label>
							</div>
							
							<select id="slicewp-payment-status" name="status" class="slicewp-select2" <?php echo ( $payment->get( 'payout_method' ) != 'manual' && $payment->get( 'status' ) == 'paid' ? 'disabled' : '' ); ?>>

								<?php 
									foreach( slicewp_get_payment_available_statuses() as $status_slug => $status_name ) {

										echo '<option value="' . esc_attr( $status_slug ) . '" ' . selected( $payment->get('status'), $status_slug, false ) . '>' . $status_name . '</option>';
									
										if ( $payment->get('status') == $status_slug ) {

											$status = $status_name;

										}
									}
								?>

							</select>

						</div>

					</div>

				</div>

				<!-- Payment Commissions List Table -->
				<?php 
					$table = new SliceWP_WP_List_Table_Payment_Commissions();
					$table->display();
				?>

			</div><!-- / Primary Content -->

			<!-- Sidebar Content -->
			<div id="slicewp-secondary">

				<?php 

					/**
					 * Hook to add extra cards if needed in the sidebar
					 *
					 */
					do_action( 'slicewp_view_payouts_review_payment_secondary' );

				?>

			</div><!-- / Sidebar Content -->

		</div>

		<!-- Hidden Payment ID field -->
		<input type="hidden" name="payment_id" value="<?php echo $payment_id; ?>" />

		<!-- Action and nonce -->
		<input type="hidden" name="slicewp_action" value="review_payment" />
		<?php wp_nonce_field( 'slicewp_review_payment', 'slicewp_token', false ); ?>

		<!-- Submit -->
		<input type="submit" id="slicewp-review-payment-button" class="slicewp-form-submit slicewp-button-primary" name="slicewp_review_payment" value="<?php echo __( 'Save Payment', 'slicewp' ); ?>" data-confirmation-message="<?php echo sprintf( __( 'Are you sure you want to mark the payment as %s?', 'slicewp' ), $status ); ?>" />

	</form>

</div>