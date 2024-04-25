<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="slicewp-card">

	<div class="slicewp-card-inner">

		<div class="slicewp-setup-setup">

			<h2><?php echo __( "A few essential things we need to set up", 'slicewp' ); ?></h2>

			<p><?php echo __( "Please set up the following options that are at the core of the affiliate program. If you're not quite sure how you want these options set don't worry, you can change them later.", 'slicewp' ); ?></p>

			<br />

			<!-- Commission Rates -->
			<?php $commission_types = slicewp_get_available_commission_types( true ); ?>
			<?php foreach( $commission_types as $type => $details ): ?>

				<?php 
					$rate 	   = slicewp_get_setting( 'commission_rate_' . $type );
					$rate_type = slicewp_get_setting( 'commission_rate_type_' . $type );
				?>

				<div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-field-wrapper-commission-rate">

					<div class="slicewp-field-label-wrapper">
						<label for="slicewp-commission-rate-<?php echo str_replace( '_', '-', $type ); ?>">
							<?php echo sprintf( __( '%s Commission Rate', 'slicewp' ), $details['label'] ); ?>
						</label>
					</div>
					
					<input id="slicewp-commission-rate-<?php echo str_replace( '_', '-', $type ); ?>" name="commission_rate_<?php echo $type; ?>" type="text" value="<?php echo ( ! empty( $_POST['settings']['commission_rate_' . $type] ) ? esc_attr( $_POST['settings']['commission_rate_' . $type] ) : $rate) ?>" />					

					<select name="commission_rate_type_<?php echo $type; ?>" class="slicewp-select2" <?php echo ( count( $details['rate_types'] ) == 1 ? 'disabled' : '' ); ?>>
						<?php $currency_symbol = slicewp_get_currency_symbol( slicewp_get_setting( 'active_currency', 'USD' ) ); ?>
						<?php foreach( $details['rate_types'] as $details_rate_type ): ?>
							<option value="<?php echo esc_attr( $details_rate_type ); ?>" <?php selected( $rate_type, $details_rate_type ); ?>><?php echo ( $details_rate_type == 'percentage' ? __( 'Percentage (%)', 'slicewp' ) : __( 'Fixed Amount', 'slicewp' ) . ' (' . esc_attr( $currency_symbol ) . ')' ); ?></option>
						<?php endforeach; ?>
					</select>

				</div>

			<?php endforeach; ?>
			<!-- / Commission Rates -->

			<!-- Currency -->
			<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

				<div class="slicewp-field-label-wrapper">
					<label for="slicewp-active-currency">
						<?php echo __( 'Currency', 'slicewp' ); ?>
					</label>
				</div>

				<select id="slicewp-active-currency" name="active_currency" class="slicewp-select2">
					<?php foreach( slicewp_get_currencies() as $currency_code => $currency_name ): ?>
						<?php $currency_symbol = slicewp_get_currency_symbol( $currency_code ); ?>
						<option value="<?php echo esc_attr( $currency_code ); ?>"><?php echo esc_attr( $currency_name ) . ( ! empty( $currency_symbol ) ? ( ' (' . $currency_symbol . ')' ) : '' ); ?></option>
					<?php endforeach; ?>
				</select>

			</div><!-- / Currency -->

			<!-- Cookie Duration -->
			<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

				<div class="slicewp-field-label-wrapper">
					<label for="slicewp-cookie-duration">
						<?php echo __( 'Cookie Duration', 'slicewp' ); ?>
						<?php echo slicewp_output_tooltip( __( 'The number of days a referral is valid.' , 'slicewp' ) ); ?>
					</label>
				</div>

				<input id="slicewp-cookie-duration" name="cookie_duration" type="text" value="30" />

			</div><!-- / Cookie Duration -->

			<!-- Allow Affiliate Registration -->
			<div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-last">

				<div class="slicewp-field-label-wrapper">
					<label for="slicewp-allow-affiliate-registration">
						<?php echo __( 'Allow Affiliate Registration', 'slicewp' ); ?>
					</label>
				</div>

				<div class="slicewp-switch">

					<input id="slicewp-allow-affiliate-registration" class="slicewp-toggle slicewp-toggle-round" name="allow_affiliate_registration" type="checkbox" value="1" />
					<label for="slicewp-allow-affiliate-registration"></label>

				</div>

				<label for="slicewp-allow-affiliate-registration"><?php echo __( 'Allow visitors to register as affiliates.', 'slicewp' ); ?></label>

			</div><!-- / Allow Affiliates Registration -->

		</div>

	</div>

	<div class="slicewp-card-footer">

		<div class="slicewp-submit-wrapper-setup-wizard">

			<input type="submit" class="slicewp-form-submit slicewp-button-primary" value="<?php echo __( 'Continue', 'slicewp' ); ?>" />

		</div>

	</div>

</div>