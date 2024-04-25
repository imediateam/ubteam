<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="wrap slicewp-wrap slicewp-wrap-add-affiliate">

	<form action="" method="POST">

		<!-- Page Heading -->
		<h1 class="wp-heading-inline"><?php echo __( 'Add a New Affiliate', 'slicewp' ); ?></h1>
		<hr class="wp-header-end" />

		<div id="slicewp-content-wrapper">

			<!-- Primary Content -->
			<div id="slicewp-primary">

				<!-- Postbox -->
				<div class="slicewp-card slicewp-first">

					<div class="slicewp-card-header">
						<?php echo __( 'Affiliate Details', 'slicewp' ); ?>
					</div>

					<!-- Form Fields -->
					<div class="slicewp-card-inner">

						<!-- Affiliate User ID -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-field-wrapper-users-autocomplete">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-affiliate-user-id"><?php echo __( 'User', 'slicewp' ); ?> *</label>
							</div>
							
							<input id="slicewp-affiliate-user-id" class="slicewp-field-users-autocomplete" data-affiliates="exclude" autocomplete="off" name="user_search" type="text" placeholder="<?php echo __( "Type the user's email or name...", 'slicewp' ); ?>" value="<?php echo ( ! empty( $_POST['user_search'] ) ? esc_attr( $_POST['user_search'] ) : '' ); ?>" />
							<input type="hidden" name="user_id" value="<?php echo ( ! empty( $_POST['user_id'] ) ? esc_attr( $_POST['user_id'] ) : '' ); ?>" />

							<?php wp_nonce_field( 'slicewp_user_search', 'slicewp_user_search_token', false ); ?>

						</div>

						<!-- Website -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-affiliate-website"><?php echo __( 'Website', 'slicewp' ); ?></label>
							</div>
							
							<input id="slicewp-affiliate-website" name="website" type="text" value="<?php echo ( ! empty( $_POST['website'] ) ? esc_attr( $_POST['website'] ) : '' ); ?>" />

						</div>

						<!-- Payment Email -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-affiliate-payment-email"><?php echo __( 'Payment Email', 'slicewp' ); ?> *</label>
							</div>
							
							<input id="slicewp-affiliate-payment-email" name="payment_email" type="text" value="<?php echo ( ! empty( $_POST['payment_email'] ) ? esc_attr( $_POST['payment_email'] ) : '' ); ?>" />

						</div>

						<!-- Affiliate Status -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-affiliate-status"><?php echo __( 'Status', 'slicewp' ); ?> *</label>
							</div>
							
							<select id="slicewp-affiliate-status" name="status" class="slicewp-select2">

								<?php 
									foreach( slicewp_get_affiliate_available_statuses() as $status_slug => $status_name ) {
										echo '<option value="' . esc_attr( $status_slug ) . '">' . $status_name . '</option>';
									} 
								?>

							</select>

						</div>

						<!-- Send Welcome Email -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-last">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-affiliate-welcome-email"><?php echo __( 'Send Welcome Email', 'slicewp' ); ?></label>
							</div>
							
							<div class="slicewp-switch">

								<input id="slicewp-affiliate-welcome-email" class="slicewp-toggle slicewp-toggle-round" name="welcome_email" type="checkbox" value="1" <?php echo ( ! empty( $_POST['welcome_email'] ) ? 'checked="checked"' : '' ); ?> />
								<label for="slicewp-affiliate-welcome-email"></label>

							</div>

							<label for="slicewp-affiliate-welcome-email"><?php echo __( 'Send a welcome email to your new affiliate after registration.', 'slicewp' ); ?></label>

						</div>
					
					</div>

				</div>

				<?php 

					/**
					 * Hook to add extra cards if needed
					 *
					 */
					do_action( 'slicewp_view_affiliates_add_affiliate_bottom' );

				?>

			</div><!-- / Primary Content -->

			<!-- Sidebar Content -->
			<div id="slicewp-secondary">

				<?php

					/**
					 * Hook to add extra cards if needed in the sidebar
					 *
					 */
					do_action( 'slicewp_view_affiliates_add_affiliate_secondary' );

				?>

			</div><!-- / Sidebar Content -->

		</div>

		<!-- Action and nonce -->
		<input type="hidden" name="slicewp_action" value="add_affiliate" />
		<?php wp_nonce_field( 'slicewp_add_affiliate', 'slicewp_token', false ); ?>

		<!-- Submit -->
		<input type="submit" class="slicewp-form-submit slicewp-button-primary" value="<?php echo __( 'Add Affiliate', 'slicewp' ); ?>" />
		
	</form>

</div>