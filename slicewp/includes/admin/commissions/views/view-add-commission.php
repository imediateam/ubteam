<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="wrap slicewp-wrap slicewp-wrap-add-commission">

	<form action="" method="POST">

		<!-- Page Heading -->
		<h1 class="wp-heading-inline"><?php echo __( 'Add a New Commission', 'slicewp' ); ?></h1>
		<hr class="wp-header-end" />

		<div id="slicewp-content-wrapper">

			<!-- Primary Content -->
			<div id="slicewp-primary">

				<!-- Postbox -->
				<div class="slicewp-card slicewp-first">

					<div class="slicewp-card-header">
						<?php echo __( 'Commission Details', 'slicewp' ); ?>
					</div>

					<!-- Form Fields -->
					<div class="slicewp-card-inner">

						<!-- Commission Affiliate ID -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-field-wrapper-users-autocomplete">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-commission-affiliate-id"><?php echo __( 'Affiliate', 'slicewp' ); ?> *</label>
							</div>
							
							<input id="slicewp-commission-affiliate-id" class="slicewp-field-users-autocomplete" data-affiliates="include" data-return-value="affiliate_id" autocomplete="off" name="user_search" type="text" placeholder="<?php echo __( "Type the affiliate's email or name...", 'slicewp' ); ?>" value="<?php echo ( ! empty( $_POST['user_search'] ) ? esc_attr( $_POST['user_search'] ) : '' ); ?>" />
							<input type="hidden" name="affiliate_id" value="<?php echo ( ! empty( $_POST['affiliate_id'] ) ? esc_attr( $_POST['affiliate_id'] ) : '' ); ?>" />

							<?php wp_nonce_field( 'slicewp_user_search', 'slicewp_user_search_token', false ); ?>
							
						</div>

						<!-- Commission Amount -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-commission-amount">
									<?php echo __( 'Amount', 'slicewp' ); ?>
									<?php
										$currency_symbol = slicewp_get_currency_symbol( slicewp_get_setting( 'active_currency', 'USD' ) );
										echo ' (' . $currency_symbol . ')';
									?>
								</label>
							</div>
							
							<input id="slicewp-commission-amount" name="amount" type="text" value="<?php echo ( ! empty( $_POST['amount'] ) ? esc_attr( $_POST['amount'] ) : '' ); ?>" />

						</div>

						<!-- Commission Reference -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-commission-reference"><?php echo __( 'Reference', 'slicewp' ); ?></label>
							</div>
							
							<input id="slicewp-commission-reference" name="reference" type="text" value="<?php echo ( ! empty( $_POST['reference'] ) ? esc_attr( $_POST['reference'] ) : '' ); ?>" />

						</div>

						<!-- Commission Origin -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-commission-origin"><?php echo __( 'Origin', 'slicewp' ); ?></label>
							</div>
							
							<select id="slicewp-commission-origin" name="origin" class="slicewp-select2">

								<?php 
									foreach( slicewp()->integrations as $integration_slug => $integration ) {
										echo '<option value="' . esc_attr( $integration_slug ) . '">' . $integration->get( 'name' ) . '</option>';
									} 
								?>

							</select>

						</div>

						<!-- Commission Date -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-commission-date-created"><?php echo __( 'Date', 'slicewp' ); ?></label>
							</div>
							
							<input id="slicewp-commission-date-created" name="date_created" type="text" class="slicewp-datetimepicker" autocomplete="off" value="<?php echo ( ! empty( $_POST['date_created'] ) ? esc_attr( $_POST['date_created'] ) : '' ); ?>" />

						</div>

						<!-- Commission Type -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-commission-type"><?php echo __( 'Type', 'slicewp' ); ?></label>
							</div>

							<select id="slicewp-commission-type" name="type" class="slicewp-select2">

								<?php 
									foreach( slicewp_get_available_commission_types() as $type_slug => $type_data ) {
										echo '<option value="' . esc_attr( $type_slug ) . '">' . $type_data['label'] . '</option>';
									} 
								?>

							</select>

						</div>

						<!-- Commission Status -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-last">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-commission-status"><?php echo __( 'Status', 'slicewp' ); ?> *</label>
							</div>
							
							<select id="slicewp-commission-status" name="status" class="slicewp-select2">

								<?php 
									foreach( slicewp_get_commission_available_statuses() as $status_slug => $status_name ) {
										echo '<option value="' . esc_attr( $status_slug ) . '">' . $status_name . '</option>';
									} 
								?>

							</select>

						</div>
					
					</div>

				</div>

				<?php 

					/**
					 * Hook to add extra cards if needed
					 *
					 */
					do_action( 'slicewp_view_commissions_add_commission_bottom' );

				?>

			</div>

			<!-- Sidebar Content -->
			<div id="slicewp-secondary">

				<?php 

					/**
					 * Hook to add extra cards if needed in the sidebar
					 *
					 */
					do_action( 'slicewp_view_commissions_add_commission_secondary' );

				?>

			</div><!-- / Sidebar Content -->

		</div>

		<!-- Action and nonce -->
		<input type="hidden" name="slicewp_action" value="add_commission" />
		<?php wp_nonce_field( 'slicewp_add_commission', 'slicewp_token', false ); ?>

		<!-- Submit -->
		<input type="submit" class="slicewp-form-submit slicewp-button-primary" value="<?php echo __( 'Add Commission', 'slicewp' ); ?>" />
		
	</form>

</div>