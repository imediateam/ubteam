<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$commission_id = ( ! empty( $_GET['commission_id'] ) ? sanitize_text_field( $_GET['commission_id'] ) : 0 );

if( empty( $commission_id ) )
	return;

$commission = slicewp_get_commission( $commission_id );

if( is_null( $commission ) )
	return;

?>

<div class="wrap slicewp-wrap slicewp-wrap-edit-commission">

	<form action="" method="POST">

		<!-- Page Heading -->
		<h1 class="wp-heading-inline"><?php echo __( 'Edit Commission', 'slicewp' ); ?></h1>
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

						<!-- Commission ID -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-commission-commission-id"><?php echo __( 'Commission ID', 'slicewp' ); ?></label>
							</div>
							
							<input id="slicewp-commission-commission-id" name="commission_id" disabled type="text" value="<?php echo esc_attr( $commission->get('id') ); ?>" />

						</div>

						<!-- Commission Affiliate -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-commission-affiliate"><?php echo __( 'Affiliate', 'slicewp' ); ?></label>
							</div>
							
							<div class="slicewp-field-link-disabled">
								<?php $affiliate_name = slicewp_get_affiliate_name( $commission->get('affiliate_id') ); ?>
								<?php if( null === $affiliate_name ): ?>
									<span><?php echo __( '(inexistent affiliate)', 'slicewp' ); ?></span>
								<?php else: ?>
									<a href="<?php echo add_query_arg( array( 'page' => 'slicewp-affiliates', 'subpage' => 'edit-affiliate', 'affiliate_id' => $commission->get('affiliate_id') ) , admin_url( 'admin.php' ) ); ?>"><?php echo $affiliate_name; ?></a>
								<?php endif; ?>
							</div>

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
							
							<input id="slicewp-commission-amount" name="amount" type="text" value="<?php echo slicewp_format_amount( esc_attr( ! empty( $_POST['amount'] ) ? $_POST['amount'] : $commission->get('amount') ), slicewp_get_setting( 'active_currency', 'USD' ), false ); ?>" />

						</div>

						<!-- Commission Reference -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-commission-reference"><?php echo __( 'Reference', 'slicewp' ); ?></label>
							</div>
							
							<input id="slicewp-commission-reference" name="reference" type="text" value="<?php echo ( ! empty( $_POST['reference'] ) ? esc_attr( $_POST['reference'] ) : $commission->get('reference') ); ?>" />

						</div>

						<!-- Commission Origin -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-commission-origin"><?php echo __( 'Origin', 'slicewp' ); ?></label>
							</div>
							
							<select id="slicewp-commission-origin" name="origin" class="slicewp-select2" disabled>

								<?php
									// Add integrations in origin
									$integrations = slicewp()->integrations;

									foreach( $integrations as $integration_slug => $integration ) {
										echo '<option value="' . esc_attr( $integration_slug ) . '" ' . selected( $commission->get( 'origin' ), $integration_slug, false ) . '>' . $integration->get( 'name' ) . '</option>';
									}

									// If the origin is different than the integrations, add it as an option
									if( ! in_array( $commission->get( 'origin' ), array_keys( $integrations ) ) ) {
										echo '<option value="' . esc_attr( $commission->get( 'origin' ) ) . '" selected="true">' . esc_html( $commission->get( 'origin' ) ) . '</option>';
									}

								?>

							</select>

						</div>

						<!-- Commission Date -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-commission-date-created"><?php echo __( 'Date', 'slicewp' ); ?></label>
							</div>
							
							<input id="slicewp-commission-date-created" type="text" disabled value="<?php echo slicewp_date_i18n( esc_attr( $commission->get( 'date_created' ) ) ); ?>" />

						</div>

						<!-- Commission Type -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-commission-type"><?php echo __( 'Type', 'slicewp' ); ?></label>
							</div>

							<select id="slicewp-commission-type" name="type" class="slicewp-select2">

								<?php 
									foreach( slicewp_get_available_commission_types() as $type_slug => $type_data ) {
										echo '<option value="' . esc_attr( $type_slug ) . '" ' . selected( $commission->get('type'), $type_slug, false ) . '>' . $type_data['label'] . '</option>';
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
										echo '<option value="' . esc_attr( $status_slug ) . '" ' . selected( $commission->get('status'), $status_slug, false ) . '>' . $status_name . '</option>';
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
					do_action( 'slicewp_view_commissions_edit_commission_bottom' );

				?>

			</div><!-- / Primary Content -->

			<!-- Sidebar Content -->
			<div id="slicewp-secondary">

				<?php 

					/**
					 * Hook to add extra cards if needed in the sidebar
					 *
					 */
					do_action( 'slicewp_view_commissions_edit_commission_secondary' );

				?>

			</div><!-- / Sidebar Content -->

		</div>

		<!-- Hidden commission id field -->
		<input type="hidden" name="commission_id" value="<?php echo $commission_id; ?>" />

		<!-- Action and nonce -->
		<input type="hidden" name="slicewp_action" value="update_commission" />
		<?php wp_nonce_field( 'slicewp_update_commission', 'slicewp_token', false ); ?>

		<!-- Submit -->
		<div id="slicewp-content-actions">
			
			<input type="submit" class="slicewp-form-submit slicewp-button-primary" value="<?php echo __( 'Update Commission', 'slicewp' ); ?>" />

			<span class="slicewp-trash"><a onclick="return confirm( '<?php echo __( "Are you sure you want to delete this commission?", "slicewp" ); ?>' )" href="<?php echo wp_nonce_url( add_query_arg( array( 'page' => 'slicewp-commissions', 'slicewp_action' => 'delete_commission', 'commission_id' => $commission->get('id') ), admin_url( 'admin.php' ) ), 'slicewp_delete_commission', 'slicewp_token' ); ?>"><?php echo __( 'Delete commission', 'slicewp' ) ?></a></span>

		</div>

	</form>

</div>