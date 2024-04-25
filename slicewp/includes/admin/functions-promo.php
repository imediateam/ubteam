<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Adds a call-to-action at the bottom of pages that have list tables
 *
 */
function slicewp_promo_add_upgrade_card_cta() {

	if( slicewp_is_website_registered() )
		return;

	?>

	<a id="slicewp-upgrade-card-cta" href="<?php echo add_query_arg( array( 'page' => 'slicewp-affiliates', 'subpage' => 'upgrade-to-premium' ), 'admin.php' ); ?>">

		<div class="slicewp-card">
			<div class="slicewp-card-inner">
				<p><?php echo __( 'Missing anything? Discover more powerful features in the premium version now!', 'slicewp' ); ?></p>
				<span><?php echo __( "I'm interested", 'slicewp' ); ?></span>
			</div>
		</div>

	</a>

	<?php

}
add_action( 'slicewp_view_affiliates_bottom', 'slicewp_promo_add_upgrade_card_cta' );
add_action( 'slicewp_view_commissions_bottom', 'slicewp_promo_add_upgrade_card_cta' );
add_action( 'slicewp_view_creatives_bottom', 'slicewp_promo_add_upgrade_card_cta' );
add_action( 'slicewp_view_visits_bottom', 'slicewp_promo_add_upgrade_card_cta' );
add_action( 'slicewp_view_payouts_bottom', 'slicewp_promo_add_upgrade_card_cta' );


/**
 * Adds an upgrade to premium subpage for the Affiliates page where all the perks
 * are being displayed
 *
 */
function slicewp_promo_subpage_upgrade_to_premium( $subpage ) {

	if( empty( $subpage ) || $subpage != 'upgrade-to-premium' )
		return;

	?>

	<div class="wrap slicewp-wrap slicewp-wrap-upgrade-to-premium">

		<!-- Page Heading -->
		<h1 class="wp-heading-inline"><?php echo __( 'Upgrade to Premium', 'slicewp' ); ?></h1>
		<hr class="wp-header-end" />

		<div id="slicewp-content-wrapper">

			<!-- Primary Content -->
			<div id="slicewp-primary">

				<div class="slicewp-row">

					<div class="slicewp-col-1-2">
						<div class="slicewp-card">
							<div class="slicewp-card-inner">
								<h4><span class="dashicons dashicons-yes"></span><?php echo __( 'Set custom commission rates per affiliate', 'slicewp' ) ?></h4>
								<p><?php echo __( 'Set custom percentage or fixed amount commission rates for each individual affiliate, to overwrite the default ones from general settings.', 'slicewp' ); ?></p>
							</div>
						</div>
					</div>

					<div class="slicewp-col-1-2">
						<div class="slicewp-card">
							<div class="slicewp-card-inner">
								<h4><span class="dashicons dashicons-yes"></span><?php echo __( 'Reward commissions with affiliate coupons', 'slicewp' ) ?></h4>
								<p><?php echo __( 'Associate coupon codes to affiliates and reward commissions to your affiliate partners whenever customers use the codes when purchasing.', 'slicewp' ); ?></p>
							</div>
						</div>
					</div>

				</div>

				<div class="slicewp-row">

					<div class="slicewp-col-1-2">
						<div class="slicewp-card">
							<div class="slicewp-card-inner">
								<h4><span class="dashicons dashicons-yes"></span><?php echo __( 'Set custom commission rates per individual product', 'slicewp' ) ?></h4>
								<p><?php echo __( 'Overwrite global commission rates with custom rates for each individual product or subscription.', 'slicewp' ); ?></p>
							</div>
						</div>
					</div>

					<div class="slicewp-col-1-2">
						<div class="slicewp-card">
							<div class="slicewp-card-inner">
								<h4><span class="dashicons dashicons-yes"></span><?php echo __( 'Analyze extensive reports', 'slicewp' ) ?></h4>
								<p><?php echo __( 'Track the performance of your affiliate partners and monitor key metrics to help you improve your affiliate marketing program.', 'slicewp' ); ?></p>
							</div>
						</div>
					</div>

				</div>

				<div class="slicewp-row">

					<div class="slicewp-col-1-2">
						<div class="slicewp-card">
							<div class="slicewp-card-inner">
								<h4><span class="dashicons dashicons-yes"></span><?php echo __( 'Pay your affiliates with PayPal Payouts', 'slicewp' ) ?></h4>
								<p><?php echo __( "Pay affiliates in bulk directly from your WordPress administrator interface through PayPal's Payouts feature.", 'slicewp' ); ?></p>
							</div>
						</div>
					</div>

					<div class="slicewp-col-1-2">
						<div class="slicewp-card">
							<div class="slicewp-card-inner">
								<h4><span class="dashicons dashicons-yes"></span><?php echo __( 'Set recurring commission rates for subscriptions', 'slicewp' ) ?></h4>
								<p><?php echo __( "Set custom commission rates for recurring payments and reward your affiliates for any new subscription payments.", 'slicewp' ); ?></p>
							</div>
						</div>
					</div>

				</div>

				<div class="slicewp-row">

					<div class="slicewp-col-1-2">
						<div class="slicewp-card">
							<div class="slicewp-card-inner">
								<h4><span class="dashicons dashicons-yes"></span><?php echo __( 'Get immediate assistance', 'slicewp' ) ?></h4>
								<p><?php echo __( "Whenever you need help, we're here for you. Our support response times are between 6 and 24 hours, so you can rest assured any issues will be resolved quickly.", 'slicewp' ); ?></p>
							</div>
						</div>
					</div>

				</div>

			</div>

			<!-- Sidebar Content -->
			<div id="slicewp-secondary">

				<div id="slicewp-upgrade-to-premium-main-cta" class="slicewp-card" style="background: #feaa19;">
					<div class="slicewp-card-inner">
						<h2><?php echo __( 'Ready to take full advantage of the premium version?', 'slicewp' ); ?></h2>
						<a href="https://slicewp.com/pricing/" target="_blank"><?php echo __( 'Get started', 'slicewp' ); ?></a>
						<span><?php echo __( 'Prices starting from $79', 'slicewp' ); ?></span>
					</div>
				</div>

				<div id="slicewp-upgrade-to-premium-discount-code" class="slicewp-card">
					<div class="slicewp-card-inner">
						<h3><?php echo __( 'Use this coupon code for a 20% discount.', 'slicewp' ); ?></h3>
						<span>FREE-VERSION-UPGRADE</span>
					</div>
				</div>

			</div>

		</div>

	</div>

	<?php

}
add_action( 'slicewp_submenu_page_output_affiliates', 'slicewp_promo_subpage_upgrade_to_premium' );


/**
 * Include the promo commission rates view for the affiliate
 *
 */
function slicewp_promo_view_affiliates_add_affiliate_bottom_affiliate_commission_rates() {

	if( slicewp_is_website_registered() )
		return;

	if( slicewp_add_ons_exist() )
		return;

	$affiliate_id = ( ! empty( $_GET['affiliate_id'] ) ? sanitize_text_field( $_GET['affiliate_id'] ) : 0 );

	?>

	<div class="slicewp-card slicewp-card-promo">

		<div class="slicewp-card-header">
			<?php echo __( 'Affiliate Commission Rates', 'slicewp' ); ?>
			<a class="slicewp-promo-pill" href="<?php echo add_query_arg( array( 'page' => 'slicewp-affiliates', 'subpage' => 'upgrade-to-premium' ), 'admin.php' ); ?>"><?php echo __( 'Pro Feature', 'slicewp' ); ?></a>
		</div>

		<div class="slicewp-card-inner">

			<!-- Enable Custom Commission Rates -->
			<div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-last">

				<div class="slicewp-field-label-wrapper">
					<label><?php echo __( 'Commission Rates', 'slicewp' ); ?></label>
				</div>
				
				<div class="slicewp-switch">

					<input class="slicewp-toggle slicewp-toggle-round" disabled type="checkbox" value="1" checked />
					<label></label>

				</div>

				<label><?php echo __( 'Enable custom commission rates for this affiliate.', 'slicewp' ); ?></label>

			</div>
			<!-- / Enable Custom Commission Rates -->

			<!-- Commissions Rates -->
			<?php 
				$commission_types = slicewp_get_available_commission_types( true );
				$count = 0;
			?>

			<?php foreach( $commission_types as $type => $details ): ?>

				<?php if( $type == 'recurring' ) continue; ?>

				<?php
					$rate 	   = slicewp_get_affiliate_meta( $affiliate_id, 'commission_rate_' . $type, true );
					$rate_type = slicewp_get_affiliate_meta( $affiliate_id, 'commission_rate_type_' . $type, true );
				?>

				<div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-field-wrapper-commission-rate">

					<div class="slicewp-field-label-wrapper">
						<label for="slicewp-commission-rate-<?php echo str_replace( '_', '-', $type ); ?>">
							<?php echo sprintf( __( '%s rate', 'slicewp' ), $details['label'] ); ?>
						</label>
					</div>
					
					<input id="slicewp-commission-rate-<?php echo str_replace( '_', '-', $type ); ?>" type="text" value="25" disabled />					

					<select name="commission_rate_type_<?php echo $type; ?>" class="slicewp-select2" disabled>
						<?php foreach( $details['rate_types'] as $details_rate_type ): ?>
							<option value="<?php echo esc_attr( $details_rate_type ); ?>" <?php selected( $rate_type, $details_rate_type ); ?>><?php echo ( $details_rate_type == 'percentage' ? __( 'Percentage (%)', 'slicewp' ) : __( 'Fixed Amount', 'slicewp' ) ); ?></option>
						<?php endforeach; ?>
					</select>

				</div>

				<?php $count++; ?>

			<?php endforeach; ?>
			<!-- / Commisions Rates -->

		</div>

	</div>

	<?php

}
add_action( 'slicewp_view_affiliates_add_affiliate_bottom', 'slicewp_promo_view_affiliates_add_affiliate_bottom_affiliate_commission_rates' );
add_action( 'slicewp_view_affiliates_edit_affiliate_bottom', 'slicewp_promo_view_affiliates_add_affiliate_bottom_affiliate_commission_rates' );