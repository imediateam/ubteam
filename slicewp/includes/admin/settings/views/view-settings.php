<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$tabs = array(
	'general' => array(
		'label' => __( 'General', 'slicewp' ),
		'icon'  => 'dashicons-admin-generic'
	),
	'integrations' => array(
		'label' => __( 'Integrations', 'slicewp' ),
		'icon'  => 'dashicons-admin-plugins'
	),
	'emails' => array(
		'label' => __( 'Email Notifications', 'slicewp' ),
		'icon'  => 'dashicons-email-alt'
	),
	'tools' => array(
		'label' => __( 'Tools', 'slicewp' ),
		'icon'  => 'dashicons-admin-tools'
	)
);

/**
 * Filter the tabs for the settings edit screen
 *
 * @param array $tabs
 *
 */
$tabs = apply_filters( 'slicewp_submenu_page_settings_tabs', $tabs );

$active_tab = ( ! empty( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'general' );

/**
 * Prepare the Email Notification Settings section
 *
 * @param array $tabs
 *
 */
$email_notifications = slicewp_get_available_email_notifications();
$first_email_notification_slug = array_keys( $email_notifications )[0];

$selected_email_notification = ( ! empty( $_GET['email_notification'] ) ? sanitize_text_field( $_GET['email_notification'] ) : $first_email_notification_slug );

/**
 * Prepare the needed variables
 *
 */
$user = wp_get_current_user();
$user_id = $user->ID;
$affiliate = slicewp_get_affiliate_by_user_id( $user_id );
$affiliate_id = ( empty( $affiliate ) ? $user_id : $affiliate->get( 'id' ) );

?>

<div class="wrap slicewp-wrap slicewp-wrap-settings">

	<form action="" method="POST">

		<!-- Page Heading -->
		<h1 class="wp-heading-inline"><?php echo __( 'Settings', 'slicewp' ); ?></h1>
		<hr class="wp-header-end" />

		<!-- Tab Navigation -->
		<div class="slicewp-card">

			<!-- Navigation Tab Links -->
			<ul class="slicewp-nav-tab-wrapper">
				<?php 
					foreach( $tabs as $tab_slug => $tab ) {
						echo '<li class="slicewp-nav-tab ' . ( $tab_slug == $active_tab ? 'slicewp-active' : '' ) . '" data-tab="' . esc_attr( $tab_slug ) . '"><a href="#"><span class="dashicons ' . esc_attr( $tab['icon'] ) . '"></span>' . esc_attr( $tab['label'] ) . '</a></li>';
					}
				?>
			</ul>

			<!-- Hidden active tab -->
			<input type="hidden" name="active_tab" value="<?php echo esc_attr( $active_tab ); ?>" />

		</div>

		<!-- Tab: General Settings -->
		<div class="slicewp-tab <?php echo ( $active_tab == 'general' ? 'slicewp-active' : '' ); ?>" data-tab="general">

			<!-- Register website -->
			<?php if( slicewp_add_ons_exist() ): ?>

				<div class="slicewp-card">

					<div class="slicewp-card-header">
						<?php echo __( 'Register Website', 'slicewp' ); ?>
					</div>

					<div class="slicewp-card-inner">

						<!-- License Key -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-field-wrapper-license-key slicewp-last">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-license-key">
									<?php echo __( 'License Key', 'slicewp' ); ?>
								</label>
							</div>

							<div class="slicewp-flex-wrapper">

								<input id="slicewp-license-key" name="license_key" type="text" value="<?php echo esc_attr( get_option( 'slicewp_license_key', '' ) ); ?>">
								<a id="slicewp-register-license-key" class="slicewp-button-secondary" href="#">
									<span class="slicewp-register" <?php echo ( slicewp_is_website_registered() ? 'style="display: none;"' : '' ); ?>><?php echo __( 'Register', 'slicewp' ); ?></span>
									<span class="slicewp-deregister" <?php echo ( ! slicewp_is_website_registered() ? 'style="display: none;"' : '' ); ?>><?php echo __( 'Deregister', 'slicewp' ); ?></span>
								</a>
								
							</div>

							<input id="slicewp-is-website-registered" type="hidden" value="<?php echo ( slicewp_is_website_registered() ? 'true' : 'false' ); ?>" />

						</div><!-- / License Key -->

					</div>

				</div>

			<?php endif; ?>
			<!-- / Register website -->


			<!-- General Settings -->
			<div class="slicewp-card">

				<div class="slicewp-card-header">
					<?php echo __( 'General Settings', 'slicewp' ); ?>
				</div>

				<div class="slicewp-card-inner">

					<!-- Allow Affiliate Registration -->
					<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

						<div class="slicewp-field-label-wrapper">
							<label for="slicewp-allow-affiliate-registration">
								<?php echo __( 'Allow Affiliate Registration', 'slicewp' ); ?>
							</label>
						</div>

						<div class="slicewp-switch">

							<input id="slicewp-allow-affiliate-registration" class="slicewp-toggle slicewp-toggle-round" name="settings[allow_affiliate_registration]" type="checkbox" value="1" <?php checked( ! empty( $_POST['settings']['allow_affiliate_registration'] ) ? '1' : ( empty( $_POST ) ? slicewp_get_setting( 'allow_affiliate_registration' ) : '' ), '1' ); ?> />
							<label for="slicewp-allow-affiliate-registration"></label>

						</div>

						<label for="slicewp-allow-affiliate-registration"><?php echo __( 'Allow visitors to register as affiliates.', 'slicewp' ); ?></label>

					</div><!-- / Allow Affiliates Registration -->
					
					<!-- Register new Affiliates with Active status -->
					<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

						<div class="slicewp-field-label-wrapper">
							<label for="slicewp-affiliate-register-status-active">
								<?php echo __( 'Register Affiliates as Active', 'slicewp' ); ?>
							</label>
						</div>

						<div class="slicewp-switch">

							<input id="slicewp-affiliate-register-status-active" class="slicewp-toggle slicewp-toggle-round" name="settings[affiliate_register_status_active]" type="checkbox" value="1" <?php checked( ! empty( $_POST['settings']['affiliate_register_status_active'] ) ? '1' : ( empty( $_POST ) ? slicewp_get_setting( 'affiliate_register_status_active' ) : '' ), '1' ); ?> />
							<label for="slicewp-affiliate-register-status-active"></label>

						</div>

						<label for="slicewp-affiliate-register-status-active"><?php echo __( 'New affiliate accounts will be created with Active status.', 'slicewp' ); ?></label>

					</div><!-- / Register new Affiliates with Active status -->

					<!-- Auto Register Affiliates -->
					<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

						<div class="slicewp-field-label-wrapper">
							<label for="slicewp-auto-register-affiliates">
								<?php echo __( 'Auto Register Affiliates', 'slicewp' ); ?>
							</label>
						</div>

						<div class="slicewp-switch">

							<input id="slicewp-auto-register-affiliates" class="slicewp-toggle slicewp-toggle-round" name="settings[affiliate_auto_register]" type="checkbox" value="1" <?php checked( ! empty( $_POST['settings']['affiliate_auto_register'] ) ? '1' : ( empty( $_POST ) ? slicewp_get_setting( 'affiliate_auto_register' ) : '' ), '1' ); ?> />
							<label for="slicewp-auto-register-affiliates"></label>

						</div>

						<label for="slicewp-auto-register-affiliates"><?php echo __( 'Automatically register new user accounts as affiliates.', 'slicewp' ); ?></label>

					</div>
					<!-- / Auto Register Affiliates -->

					<!-- Cookie Duration -->
					<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

						<div class="slicewp-field-label-wrapper">
							<label for="slicewp-cookie-duration">
								<?php echo __( 'Cookie Duration', 'slicewp' ); ?>
								<?php echo slicewp_output_tooltip( __( 'The number of days a referral is valid.' , 'slicewp' ) . '<hr />' . '<a href="https://slicewp.com/docs/cookie-duration/" target="_blank">' . __( 'Click here to learn more', 'slicewp' ) . '</a>' ); ?>
							</label>
						</div>

						<input id="slicewp-cookie-duration" name="settings[cookie_duration]" type="text" value="<?php echo ( ! empty( $_POST['settings']['cookie_duration'] ) ? esc_attr( $_POST['settings']['cookie_duration'] ) : ( slicewp_get_setting( 'cookie_duration' ) ) ); ?>">

					</div><!-- / Cookie Duration -->

					<!-- Payments Minimum Amount -->
					<div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-last">

						<div class="slicewp-field-label-wrapper">
							<label for="slicewp-payments-minimum-amount">
								<?php echo __( 'Payments Minimum Amount', 'slicewp' ); ?>
								<?php echo slicewp_output_tooltip( sprintf( __( 'The payment will be generated only if its commissions sum is greater than this value. If is set to 0, the payments will be generated for all the commissions.', 'slicewp' ) ) ); ?>
							</label>
						</div>

						<input id="slicewp-payments-minimum-amount" name="settings[payments_minimum_amount]" type="text" value="<?php echo( ! empty( $_POST['settings']['payments_minimum_amount'] ) ? esc_attr( $_POST['settings']['payments_minimum_amount'] ) : ( ! empty( slicewp_get_setting( 'payments_minimum_amount' ) ) ? slicewp_get_setting( 'payments_minimum_amount' ) : 0 ) ); ?>">

					</div><!-- / Payments Minimum Amount -->

				</div>

			</div><!-- / General Settings -->
			
			
			<!-- Commissions Settings -->
			<div id="slicewp-card-settings-commissions-settings" class="slicewp-card">

				<div class="slicewp-card-header">
					<?php echo __( 'Commissions Settings', 'slicewp' ); ?>
				</div>

				<div class="slicewp-card-inner">

					<?php 
						$commission_types = slicewp_get_available_commission_types();
					?>

					<!-- Commission Rates -->
					<?php foreach( $commission_types as $type => $details ): ?>

						<?php if( $type == 'recurring' ) continue; ?>

						<?php 
							$rate 	   = slicewp_get_setting( 'commission_rate_' . $type );
							$rate_type = slicewp_get_setting( 'commission_rate_type_' . $type );
						?>

						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-field-wrapper-commission-rate" style="display: none;">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-commission-rate-<?php echo str_replace( '_', '-', $type ); ?>">
									<?php echo sprintf( __( '%s Rate', 'slicewp' ), $details['label'] ); ?>
								</label>
							</div>
							
							<input id="slicewp-commission-rate-<?php echo str_replace( '_', '-', $type ); ?>" name="settings[commission_rate_<?php echo $type; ?>]" type="text" value="<?php echo ( ! empty( $_POST['settings']['commission_rate_' . $type] ) ? esc_attr( $_POST['settings']['commission_rate_' . $type] ) : $rate) ?>" />					

							<select name="settings[commission_rate_type_<?php echo $type; ?>]" class="slicewp-select2" <?php echo ( count( $details['rate_types'] ) == 1 ? 'disabled' : '' ); ?>>
								<?php $currency_symbol = slicewp_get_currency_symbol( slicewp_get_setting( 'active_currency', 'USD' ) ); ?>
								<?php foreach( $details['rate_types'] as $details_rate_type ): ?>
									<option value="<?php echo esc_attr( $details_rate_type ); ?>" <?php selected( $rate_type, $details_rate_type ); ?>><?php echo ( $details_rate_type == 'percentage' ? __( 'Percentage (%)', 'slicewp' ) : __( 'Fixed Amount', 'slicewp' ) . ' (' . esc_attr( $currency_symbol ) . ')' ); ?></option>
								<?php endforeach; ?>
							</select>

						</div>

					<?php endforeach; ?>
					<!-- / Commission Rates -->

					<!-- Sale Fixed Amount Commission Basis -->
					<div class="slicewp-field-wrapper slicewp-field-wrapper-inline" style="display: none;">

						<div class="slicewp-field-label-wrapper">
							<label>
								<?php echo __( 'Sale Commission Basis', 'slicewp' ); ?>
							</label>
						</div>

						<select id="slicewp-fixed-amount-commission-basis" name="settings[commission_fixed_amount_rate_basis]" class="slicewp-select2">
							<option value="product" <?php echo ( slicewp_get_setting( 'commission_fixed_amount_rate_basis' ) == 'product' ? 'selected="selected"' : '' ); ?>><?php echo __( 'Fixed amount per product', 'slicewp' ); ?></option>
							<option value="order" <?php echo ( slicewp_get_setting( 'commission_fixed_amount_rate_basis' ) == 'order' ? 'selected="selected"' : '' ); ?>><?php echo __( 'Fixed amount per order', 'slicewp' ); ?></option>
						</select>

					</div>
					<!-- / Sale Fixed Amount Commission Basis -->

					<!-- Exclude Shipping -->
					<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

						<div class="slicewp-field-label-wrapper">
							<label for="slicewp-exclude-shipping">
								<?php echo __( 'Exclude Shipping', 'slicewp' ); ?>
							</label>
						</div>

						<div class="slicewp-switch">

							<input id="slicewp-exclude-shipping" class="slicewp-toggle slicewp-toggle-round" name="settings[exclude_shipping]" type="checkbox" value="1" <?php checked( ! empty( $_POST['settings']['exclude_shipping'] ) ? '1' : ( empty( $_POST ) ? slicewp_get_setting( 'exclude_shipping' ) : '' ), '1' ); ?> />
							<label for="slicewp-exclude-shipping"></label>

						</div>

						<label for="slicewp-exclude-shipping"><?php echo __( 'Exclude shipping costs from commission calculations.', 'slicewp' ); ?></label>

					</div><!-- / Exclude Shipping -->

					<!-- Exclude Tax -->
					<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

						<div class="slicewp-field-label-wrapper">
							<label for="slicewp-exclude-tax">
								<?php echo __( 'Exclude Taxes', 'slicewp' ); ?>
							</label>
						</div>

						<div class="slicewp-switch">

							<input id="slicewp-exclude-tax" class="slicewp-toggle slicewp-toggle-round" name="settings[exclude_tax]" type="checkbox" value="1" <?php checked( ! empty( $_POST['settings']['exclude_tax'] ) ? '1' : ( empty( $_POST ) ? slicewp_get_setting( 'exclude_tax' ) : '' ), '1' ); ?> />
							<label for="slicewp-exclude-tax"></label>

						</div>

						<label for="slicewp-exclude-tax"><?php echo __( 'Exclude taxes from commission calculations.', 'slicewp' ); ?></label>

					</div><!-- / Exclude Tax -->

					<!-- Reject Unpaid Commissions on Refund -->
					<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

						<div class="slicewp-field-label-wrapper">
							<label for="slicewp-reject-commissions-on-refund">
								<?php echo __( 'Reject Commissions on Refund', 'slicewp' ); ?>
							</label>
						</div>

						<div class="slicewp-switch">

							<input id="slicewp-reject-commissions-on-refund" class="slicewp-toggle slicewp-toggle-round" name="settings[reject_commissions_on_refund]" type="checkbox" value="1" <?php checked( ! empty( $_POST['settings']['reject_commissions_on_refund'] ) ? '1' : ( empty( $_POST ) ? slicewp_get_setting( 'reject_commissions_on_refund' ) : '' ), '1' ); ?> />
							<label for="slicewp-reject-commissions-on-refund"></label>

						</div>

						<label for="slicewp-reject-commissions-on-refund"><?php echo __( 'Mark unpaid commissions as rejected if the originating purchase is refunded.', 'slicewp' ); ?></label>

					</div><!-- / Reject Unpaid Commissions on Refund -->

					<!-- Zero Amount Commissions -->
					<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

						<div class="slicewp-field-label-wrapper">
							<label for="slicewp-zero-amount-commissions">
								<?php echo __( 'Zero Amount Commissions', 'slicewp' ); ?>
								<?php echo slicewp_output_tooltip( __( 'Enable the registration of commisions that have the total amount equal to zero. This is useful if you want to track conversions for fully discounted products.', 'slicewp' ) ); ?>
							</label>
						</div>

						<div class="slicewp-switch">

							<input id="slicewp-zero-amount-commissions" class="slicewp-toggle slicewp-toggle-round" name="settings[zero_amount_commissions]" type="checkbox" value="1" <?php checked( ! empty( $_POST['settings']['zero_amount_commissions'] ) ? '1' : ( empty( $_POST ) ? slicewp_get_setting( 'zero_amount_commissions' ) : '' ), '1' ); ?> />
							<label for="slicewp-zero-amount-commissions"></label>

						</div>

						<label for="slicewp-zero-amount-commissions"><?php echo __( 'Enable the registration of zero sum commisions.', 'slicewp' ); ?></label>

					</div><!-- Zero Amount Commissions -->
                    
                    <!-- Affiliate Own Commissions -->
					<div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-last">

						<div class="slicewp-field-label-wrapper">
							<label for="slicewp-affiliates-own-commissions">
								<?php echo __( 'Affiliate Own Commissions', 'slicewp' ); ?>
							</label>
						</div>

						<div class="slicewp-switch">

							<input id="slicewp-affiliates-own-commissions" class="slicewp-toggle slicewp-toggle-round" name="settings[affiliate_own_commissions]" type="checkbox" value="1" <?php checked( ! empty( $_POST['settings']['affiliate_own_commissions'] ) ? '1' : ( empty( $_POST ) ? slicewp_get_setting( 'affiliate_own_commissions' ) : '' ), '1' ); ?> />
							<label for="slicewp-affiliates-own-commissions"></label>

						</div>

						<label for="slicewp-affiliates-own-commissions"><?php echo __( 'Allow affiliates to earn commissions for their own orders.', 'slicewp' ); ?></label>

					</div><!-- / Affiliate Own Commissions -->
					
				</div>

			</div><!-- / Commisions Settings -->
			

			<!-- Affiliate Settings -->
			<div class="slicewp-card">

				<div class="slicewp-card-header">
					<?php echo __( 'Affiliate URL Settings', 'slicewp' ); ?>
				</div>

				<div class="slicewp-card-inner">

					<!-- Affiliate Keyword -->
					<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

						<div class="slicewp-field-label-wrapper">
							<label for="slicewp-affiliate-keyword">
								<?php echo __( 'Affiliate Keyword', 'slicewp' ); ?>
								<?php echo slicewp_output_tooltip( sprintf( __( 'The URL keyword for affiliate identification: <b>%s/?%s=%s</b>', 'slicewp' ), site_url(), slicewp_get_setting( 'affiliate_keyword' ), $affiliate_id ) . '<hr />' . '<a href="https://slicewp.com/docs/affiliate-links/" target="_blank">' . __( 'Click here to learn more', 'slicewp' ) . '</a>' ); ?>
							</label>
						</div>

						<input id="slicewp-affiliate-keyword" name="settings[affiliate_keyword]" type="text" value="<?php echo esc_attr( ! empty( $_POST['settings']['affiliate_keyword'] ) ? $_POST['settings']['affiliate_keyword'] : ( empty( $_POST ) ? slicewp_get_setting( 'affiliate_keyword' ) : '' ) ); ?>">

					</div><!-- / Affiliate Keyword -->

					<!-- Credit First/Last Affiliate -->
					<div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-last">

						<div class="slicewp-field-label-wrapper">
							<label>
								<?php echo __( 'Credit First/Last Affiliate', 'slicewp' ); ?>
							</label>
						</div>

						<select id="slicewp-affiliate-credit" name="settings[affiliate_credit]" class="slicewp-select2">
							<option value="first" <?php echo selected( ( ! empty( $_POST['settings']['affiliate_credit'] ) ? $_POST['settings']['affiliate_credit'] : ( empty( $_POST ) ? slicewp_get_setting( 'affiliate_credit' ) : '' ) ) , 'first' ); ?>><?php echo __( 'First Affiliate', 'slicewp' ); ?></option>
							<option value="last" <?php echo selected( ( ! empty( $_POST['settings']['affiliate_credit'] ) ? $_POST['settings']['affiliate_credit'] : ( empty( $_POST ) ? slicewp_get_setting( 'affiliate_credit' ) : '' ) ) , 'last' ); ?>><?php echo __( 'Last Affiliate', 'slicewp' ); ?></option>
						</select>

					</div><!-- / Credit First/Last Affiliate -->

				</div>

			</div><!-- / Affiliate Settings -->


			<!-- Currency Settings -->
			<div class="slicewp-card">

				<div class="slicewp-card-header">
					<?php echo __( 'Currency Settings', 'slicewp' ); ?>
				</div>

				<div class="slicewp-card-inner">

					<!-- Currency -->
					<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

						<div class="slicewp-field-label-wrapper">
							<label for="slicewp-active-currency">
								<?php echo __( 'Currency', 'slicewp' ); ?>
							</label>
						</div>

						<select id="slicewp-active-currency" name="settings[active_currency]" class="slicewp-select2">
							<?php foreach( slicewp_get_currencies() as $currency_code => $currency_name ): ?>
								<?php $currency_symbol = slicewp_get_currency_symbol( $currency_code ); ?>
								<option value="<?php echo esc_attr( $currency_code ); ?>" <?php echo selected( ! empty( $_POST['settings']['active_currency'] ) ? $_POST['settings']['active_currency'] : ( empty( $_POST ) ? slicewp_get_setting( 'active_currency' ) : '' ), $currency_code ); ?>><?php echo esc_attr( $currency_name ) . ( ! empty( $currency_symbol ) ? ( ' (' . $currency_symbol . ')' ) : '' ); ?></option>
							<?php endforeach; ?>
						</select>

					</div><!-- / Currency -->

					<!-- Currency Symbol Position -->
					<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

						<div class="slicewp-field-label-wrapper">
							<label for="slicewp-currency-symbol-position">
								<?php echo __( 'Currency Symbol Position', 'slicewp' ); ?>
								<?php echo slicewp_output_tooltip( __( 'The position of the currency symbol in relation with the amount value, when displaying amounts.', 'slicewp' ) ); ?>
							</label>
						</div>
						
						<select id="slicewp-currency-symbol-position" name="settings[currency_symbol_position]" class="slicewp-select2">
							<option value="before" <?php echo selected( ( ! empty( $_POST['settings']['currency_symbol_position'] ) ? $_POST['settings']['currency_symbol_position'] : ( empty( $_POST ) ? slicewp_get_setting( 'currency_symbol_position' ) : '' ) ) , 'before' ); ?>><?php echo __( 'Before amount', 'slicewp' ); ?></option>
							<option value="after" <?php echo selected( ( ! empty( $_POST['settings']['currency_symbol_position'] ) ? $_POST['settings']['currency_symbol_position'] : ( empty( $_POST ) ? slicewp_get_setting( 'currency_symbol_position' ) : '' ) ) , 'after' ); ?>><?php echo __( 'After amount', 'slicewp' ); ?></option>
							<option value="before_space" <?php echo selected( ( ! empty( $_POST['settings']['currency_symbol_position'] ) ? $_POST['settings']['currency_symbol_position'] : ( empty( $_POST ) ? slicewp_get_setting( 'currency_symbol_position' ) : '' ) ) , 'before_space' ); ?>><?php echo __( 'Before amount with space', 'slicewp' ); ?></option>
							<option value="after_space" <?php echo selected( ( ! empty( $_POST['settings']['currency_symbol_position'] ) ? $_POST['settings']['currency_symbol_position'] : ( empty( $_POST ) ? slicewp_get_setting( 'currency_symbol_position' ) : '' ) ) , 'after_space' ); ?>><?php echo __( 'After amount with space', 'slicewp' ); ?></option>
						</select>

					</div><!-- / Currency Symbol Position -->

					<!-- Thousands Separator -->
					<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

						<div class="slicewp-field-label-wrapper">
							<label for="slicewp-currency-thousands-separator">
								<?php echo __( 'Thousands Separator', 'slicewp' ); ?>
								<?php echo slicewp_output_tooltip( __( 'The symbol to separate thousands. This is usually a , (comma) or a . (dot).', 'slicewp' ) ); ?>
							</label>
						</div>

						<input id="slicewp-currency-thousands-separator" name="settings[currency_thousands_separator]" type="text" value="<?php echo esc_attr( ! empty( $_POST['settings']['currency_thousands_separator'] ) ? $_POST['settings']['currency_thousands_separator'] : ( empty( $_POST ) ? slicewp_get_setting( 'currency_thousands_separator' ) : '' ) ); ?>">

					</div><!-- / Thousands Separator -->

					<!-- Decimal Separator -->
					<div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-last">

						<div class="slicewp-field-label-wrapper">
							<label for="slicewp-currency-decimal-separator">
								<?php echo __( 'Decimal Separator', 'slicewp' ); ?>
								<?php echo slicewp_output_tooltip( __( 'The symbol to separate decimal points. This is usually a , (comma) or a . (dot).', 'slicewp' ) ); ?>
							</label>
						</div>

						<input id="slicewp-currency-decimal-separator" name="settings[currency_decimal_separator]" type="text" value="<?php echo esc_attr( ! empty( $_POST['settings']['currency_decimal_separator'] ) ? $_POST['settings']['currency_decimal_separator'] : ( empty( $_POST ) ? slicewp_get_setting( 'currency_decimal_separator' ) : '' ) ); ?>">

					</div><!-- / Decimal Separator -->

				</div>

			</div><!-- / Currency Settings -->


			<!-- Pages Settings -->
			<div class="slicewp-card">

				<div class="slicewp-card-header">
					<?php echo __( 'Pages Settings', 'slicewp' ); ?>
				</div>

				<div class="slicewp-card-inner">

					<!-- Affiliate Account Page -->
					<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

						<div class="slicewp-field-label-wrapper">
							<label for="slicewp-affiliate-account-page">
								<?php echo __( 'Affiliate Account Page', 'slicewp' ); ?>
								<?php echo slicewp_output_tooltip( __( "Select the page you wish to be your affiliates' private area.", 'slicewp' ) . '<hr />' . '<a href="https://slicewp.com/docs/adding-an-affiliate-account-page/" target="_blank">' . __( 'Click here to learn more', 'slicewp' ) . '</a>' ); ?>
							</label>
						</div>

						<select id="slicewp-affiliate-account-page" name="settings[page_affiliate_account]" class="slicewp-select2">
							<option value=""><?php echo( __( 'Select...', 'slicewp' ) ); ?></option>
						<?php

							$pages = get_pages();
							foreach( $pages as $page )
								echo '<option value="' . $page->ID . '"' . selected( ! empty( $_POST['settings']['page_affiliate_account'] ) ? absint( $_POST['settings']['page_affiliate_account'] ) : ( empty( $_POST ) ? slicewp_get_setting( 'page_affiliate_account' ) : '' ), $page->ID ) . '>' . $page->post_title . '</option>';

						?>
						</select>

					</div><!-- / Affiliate Account Page -->

					<!-- Terms and Conditions Page -->
					<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

						<div class="slicewp-field-label-wrapper">
							<label for="slicewp-terms-conditions-page">
								<?php echo __( 'Terms and Conditions Page', 'slicewp' ); ?>
							</label>
						</div>

						<select id="slicewp-terms-conditions-page" name="settings[page_terms_conditions]" class="slicewp-select2">
							<option value=""><?php echo( __( 'Select...', 'slicewp' ) ); ?></option>
						<?php

							$pages = get_pages();
							foreach( $pages as $page )
								echo '<option value="' . absint( $page->ID ) . '"' . selected( ! empty( $_POST['settings']['page_terms_conditions'] ) ? absint( $_POST['settings']['page_terms_conditions'] ) : ( empty( $_POST ) ? slicewp_get_setting( 'page_terms_conditions' ) : '' ), $page->ID ) . '>' . $page->post_title . '</option>';

						?>
						</select>

					</div><!-- / Terms and Conditions Page -->

					<!-- Terms and Conditions Checkbox -->
					<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

						<div class="slicewp-field-label-wrapper">
							<label for="slicewp-terms-label">
								<?php echo __( 'Terms and Conditions Label', 'slicewp' ); ?>
								<?php echo slicewp_output_tooltip( __( 'This label will acompanion the Terms and Conditions checkbox.', 'slicewp' ) ); ?>
							</label>
						</div>

						<input id="slicewp-terms-label" name="settings[terms_label]" type="text" value="<?php echo esc_attr( ! empty( $_POST['settings']['terms_label'] ) ? $_POST['settings']['terms_label'] : ( empty( $_POST ) ? slicewp_get_setting( 'terms_label' ) : '' ) ); ?>">
						
					</div><!-- / Terms and Conditions Checkbox -->

					<!-- Required registration fields -->
					<div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-last">

						<div class="slicewp-field-label-wrapper">
							<label>
								<?php echo __( 'Required Affiliate Fields', 'slicewp' ); ?>
								<?php echo slicewp_output_tooltip( __( 'Set which fields should be required for the affiliate to complete on the registration and affiliate account pages.', 'slicewp' ) ); ?>
							</label>
						</div>

						<div style="margin-bottom: 10px;">

							<div class="slicewp-switch">

								<input id="slicewp-required-field-payment-email" class="slicewp-toggle slicewp-toggle-round" name="settings[required_field_payment_email]" type="checkbox" value="1" <?php checked( ! empty( $_POST['settings']['required_field_payment_email'] ) ? '1' : ( empty( $_POST ) ? slicewp_get_setting( 'required_field_payment_email' ) : '' ), '1' ); ?> />
								<label for="slicewp-required-field-payment-email"></label>

							</div>

							<label for="slicewp-required-field-payment-email"><?php echo __( 'Payment Email', 'slicewp' ); ?></label>							

						</div>

						<div style="margin-bottom: 10px;">

							<div class="slicewp-switch">

								<input id="slicewp-required-field-website" class="slicewp-toggle slicewp-toggle-round" name="settings[required_field_website]" type="checkbox" value="1" <?php checked( ! empty( $_POST['settings']['required_field_website'] ) ? '1' : ( empty( $_POST ) ? slicewp_get_setting( 'required_field_website' ) : '' ), '1' ); ?> />
								<label for="slicewp-required-field-website"></label>

							</div>

							<label for="slicewp-required-field-website"><?php echo __( 'Website', 'slicewp' ); ?></label>							
						
						</div>

						<div style="margin-bottom: 10px;">

							<div class="slicewp-switch">

								<input id="slicewp-required-field-promotional-methods" class="slicewp-toggle slicewp-toggle-round" name="settings[required_field_promotional_methods]" type="checkbox" value="1" <?php checked( ! empty( $_POST['settings']['required_field_promotional_methods'] ) ? '1' : ( empty( $_POST ) ? slicewp_get_setting( 'required_field_promotional_methods' ) : '' ), '1' ); ?> />
								<label for="slicewp-required-field-promotional-methods"></label>

							</div>

							<label for="slicewp-required-field-promotional-methods"><?php echo __( 'How will you promote us?', 'slicewp' ); ?></label>							
						
						</div>

					</div>
					<!-- / Required registration fields -->

				</div>

			</div><!-- / Pages Settings -->

			<!-- reCAPTCHA -->
			<div class="slicewp-card">

				<div class="slicewp-card-header">
					<?php echo __( 'reCAPTCHA', 'slicewp' ); ?>
				</div>

				<div class="slicewp-card-inner">

					<!-- Enable reCAPTCHA -->
					<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

						<div class="slicewp-field-label-wrapper">
							<label for="slicewp-enable-recaptcha">
								<?php echo __( 'Enable reCAPTCHA', 'slicewp' ); ?>
							</label>
						</div>

						<div class="slicewp-switch">

							<input id="slicewp-enable-recaptcha" class="slicewp-toggle slicewp-toggle-round" name="settings[enable_recaptcha]" type="checkbox" value="1" <?php checked( ! empty( $_POST['settings']['enable_recaptcha'] ) ? esc_attr( $_POST['settings']['enable_recaptcha'] ) : ( empty( $_POST ) ? slicewp_get_setting( 'enable_recaptcha' ) : '' ), '1' ); ?> />
							<label for="slicewp-enable-recaptcha"></label>

						</div>

						<label for="slicewp-enable-recaptcha"><?php echo __( 'Enable Google reCAPTCHA on the affiliate registration form.', 'slicewp' ); ?></label>

					</div><!-- / Enable reCAPTCHA -->

					<!-- Site Key -->
					<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

						<div class="slicewp-field-label-wrapper">
							<label for="slicewp-recaptcha-site-key">
								<?php echo __( 'Site Key', 'slicewp' ); ?>
							</label>
						</div>

						<input id="slicewp-recaptcha-site-key" name="settings[recaptcha_site_key]" type="text" value="<?php echo esc_attr( ! empty( $_POST['settings']['recaptcha_site_key'] ) ? $_POST['settings']['recaptcha_site_key'] : ( empty( $_POST ) ? slicewp_get_setting( 'recaptcha_site_key' ) : '' ) ); ?>">

					</div><!-- / Site Key -->

					<!-- Secret Key -->
					<div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-last">

						<div class="slicewp-field-label-wrapper">
							<label for="slicewp-recaptcha-secret-key">
								<?php echo __( 'Secret Key', 'slicewp' ); ?>
							</label>
						</div>

						<input id="slicewp-recaptcha-secret-key" name="settings[recaptcha_secret_key]" type="text" value="<?php echo esc_attr( ! empty( $_POST['settings']['recaptcha_secret_key'] ) ? $_POST['settings']['recaptcha_secret_key'] : ( empty( $_POST ) ? slicewp_get_setting( 'recaptcha_secret_key' ) : '' ) ); ?>">

					</div><!-- / Secret Key -->

				</div>

			</div><!-- / reCAPTCHA -->

			<?php 

				/**
				 * Hook to add extra cards if needed to the General Settings tab
				 *
				 */
				do_action( 'slicewp_view_settings_tab_general_bottom' );

				/**
				 * Hook to add extra cards if needed to the General Settings tab
				 *
				 * @deprecated 1.0.12 - No longer used in core and not recommended for external usage.
				 * 					    Replaced by "slicewp_view_settings_tab_general_bottom" action.
				 *					    Slated for removal in version 2.0.0
				 *
				 */
				do_action( 'slicewp_view_settings_tab_bottom_general' );

			?>

			<!-- Save Settings Button -->
			<input type="submit" class="slicewp-form-submit slicewp-button-primary" value="<?php echo __( 'Save Settings', 'slicewp' ); ?>" />

		</div><!-- / Tab: General Settings -->

		<!-- Tab: Integrations -->
		<div class="slicewp-tab <?php echo ( $active_tab == 'integrations' ? 'slicewp-active' : '' ); ?>" data-tab="integrations">

			<div class="slicewp-card">

				<?php foreach( slicewp()->integrations as $integration_slug => $integration ): ?>

					<div class="slicewp-card-integration-row">

						<!-- Integration Activation Switch -->
						<div class="slicewp-card-integration-switch">
							
							<div class="slicewp-switch">

								<input id="slicewp-integration-switch-<?php echo $integration_slug; ?>" class="slicewp-toggle slicewp-toggle-round" name="settings[active_integrations][]" type="checkbox" value="<?php echo $integration_slug; ?>" <?php checked( ! empty( $_POST['settings']['active_integrations'] ) && in_array( $integration_slug, $_POST['settings']['active_integrations'] ) ? '1' : ( empty( $_POST ) ? ( slicewp_is_integration_active( $integration_slug ) ? '1' : '' ) : '' ), '1' ); ?> data-supports="<?php echo htmlspecialchars( json_encode( $integration->get( 'supports' ) ), ENT_QUOTES, 'UTF-8' ); ?>" />
								<label for="slicewp-integration-switch-<?php echo $integration_slug; ?>"></label>

							</div>

						</div>

						<!-- Integration Name -->
						<div class="slicewp-card-integration-name">
							<?php echo $integration->get('name'); ?>
						</div>

					</div>

				<?php endforeach; ?>

			</div>

			<!-- Save Settings Button -->
			<input type="submit" class="slicewp-form-submit slicewp-button-primary" value="<?php echo __( 'Save Settings', 'slicewp' ); ?>" />

		</div><!-- / Tab: Integrations -->


		<!-- Tab: Email Notifications -->
		<div class="slicewp-tab <?php echo ( $active_tab == 'emails' ? 'slicewp-active' : '' ); ?>" data-tab="emails">

			<!-- General Settings -->
			<div class="slicewp-card">

				<div class="slicewp-card-header">
					<?php echo __( 'General Settings', 'slicewp' ); ?>
				</div>

				<div class="slicewp-card-inner">

					<!-- From Email -->
					<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

						<div class="slicewp-field-label-wrapper">
							<label for="slicewp-from-email">
								<?php echo __( 'From Email', 'slicewp' ); ?>
								<?php echo slicewp_output_tooltip( __( 'The email address from which the emails will be sent.' , 'slicewp' ) ); ?>
							</label>
						</div>

						<input id="slicewp-from-email" name="settings[from_email]" type="text" value="<?php echo esc_attr( ! empty( $_POST['settings']['from_email'] ) ? $_POST['settings']['from_email'] : ( slicewp_get_setting( 'from_email' ) ) ); ?>">

					</div><!-- / From Email -->

					<!-- From Name -->
					<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

						<div class="slicewp-field-label-wrapper">
							<label for="slicewp-from-name">
								<?php echo __( 'From Name', 'slicewp' ); ?>
								<?php echo slicewp_output_tooltip( __( 'The name of the email sender.' , 'slicewp' ) ); ?>
							</label>
						</div>

						<input id="slicewp-from-name" name="settings[from_name]" type="text" value="<?php echo esc_attr( ! empty( $_POST['settings']['from_name'] ) ? $_POST['settings']['from_name'] : ( slicewp_get_setting( 'from_name' ) ) ); ?>">

					</div><!-- / From Name -->

					<!-- Email Template -->
					<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

						<div class="slicewp-field-label-wrapper">
							<label for="slicewp-email-template">
								<?php echo __( 'Email Template', 'slicewp' ); ?>
								<?php echo slicewp_output_tooltip( __( 'The template will be used to format the email.' , 'slicewp' ) ); ?>
							</label>
						</div>

						<select id="slicewp-email-template" name="settings[email_template]" class="slicewp-select2">
							<option value=""><?php echo __('Plain Text', 'slicewp'); ?></option>
						<?php

							$templates = slicewp_get_email_templates();
							foreach( $templates as $key => $template )
								echo '<option value="' . esc_attr( $key ) . '"' . ( slicewp_get_setting( 'email_template' ) == $key ? 'selected="selected"' : '' ) . '>' . $template['name'] . '</option>';

						?>
						</select>

					</div><!-- / Email Template -->

					<!-- Email Logo -->
					<div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-field-wrapper-email-logo" style="display: none;">

						<div class="slicewp-field-label-wrapper">
							<label for="slicewp-email-logo"><?php echo __( 'Logo', 'slicewp' ); ?></label>
							<?php echo slicewp_output_tooltip( __( "Select an image logo if you'd like to place it in the header of your email notifications.", 'slicewp' ) ); ?>
						</div>
						
						<input id="slicewp-email-logo" name="settings[email_logo]" type="text" value="<?php echo esc_attr( ! empty( $_POST['settings']['email_logo'] ) ? $_POST['settings']['email_logo'] : slicewp_get_setting( 'email_logo' ) ); ?>" />
						<input class="slicewp-button-secondary slicewp-image-select" type="button" value="<?php echo (__( 'Browse', 'slicewp' ) ); ?>" />

					</div>
					<!-- / Email Logo -->

					<!-- Admin Emails -->
					<div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-last">

						<div class="slicewp-field-label-wrapper">
							<label for="slicewp-admin-emails">
								<?php echo __( 'Admin Emails', 'slicewp' ); ?>
								<?php echo slicewp_output_tooltip( __( 'The Admin Email Notifications will be sent to these email addresses.' , 'slicewp' ) ); ?>
							</label>
						</div>

						<input id="slicewp-admin-emails" name="settings[admin_emails]" type="text" value="<?php echo esc_attr( ! empty( $_POST['settings']['admin_emails'] ) ? $_POST['settings']['admin_emails'] : ( slicewp_get_setting( 'admin_emails' ) ) ); ?>">

					</div><!-- / Admin Emails -->
					
				</div>

			</div><!-- / General Settings -->
			
			<!-- Email Notifications -->
			<div class="slicewp-card" id="slicewp-email-notifications-settings">

				<div class="slicewp-card-header">
					<?php echo __( 'Email Notifications', 'slicewp' ); ?>
				</div>

				<div class="slicewp-card-inner">

					<!-- Email Notification -->
					<div class="slicewp-field-wrapper slicewp-email-label-wrapper">

						<!-- Hidden Email Notification tab -->
						<input type="hidden" name="email_notification" value="<?php echo esc_attr( $selected_email_notification ); ?>" />

						<div class="slicewp-field-label-wrapper">
							<label for="slicewp-email-notification">
								<?php echo __( 'Email Notification', 'slicewp' ); ?>
								<?php echo slicewp_output_tooltip( __( 'Select the email notification you want to modify' , 'slicewp' ) ); ?>
							</label>
						</div>

						<select id="slicewp-email-notification" name="email_notification" class="slicewp-select2">
						<?php
							
							$email_notifications = slicewp_get_available_email_notifications();
							
							echo '<optgroup label="' . __( 'Admin', 'slicewp' ) . '">';

								foreach( $email_notifications as $email_notification_slug => $email ) {

									if( $email['recipient'] != 'admin' )
										continue;

									echo '<option value="' . esc_attr( $email_notification_slug ) . '"' . selected( $selected_email_notification, $email_notification_slug, false ) . '>' . esc_attr( $email['name'] ) . '</option>';
								
								}

							echo '</optgroup>';

							echo '<optgroup label="' . __( 'Affiliate', 'slicewp' ) . '">';

								foreach( $email_notifications as $email_notification_slug => $email ){

									if( $email['recipient'] != 'affiliate' )
										continue;

									echo '<option value="' . esc_attr( $email_notification_slug ) . '"' . selected( $selected_email_notification, $email_notification_slug, false ) . '>' . esc_attr( $email['name'] ) . '</option>';
								
								}
								
							echo '</optgroup>';
						?>
						</select>

					</div><!-- / Email Notification -->
					
					<?php

						$email_notifications_settings = slicewp_get_setting( 'email_notifications' );
						
					?>
				<?php foreach ( $email_notifications as $email_notification_slug => $email ): ?>
					
					<?php

						$email_notification_enabled = ! empty( $email_notifications_settings[$email_notification_slug]['enabled'] ) ? $email_notifications_settings[$email_notification_slug]['enabled'] : '';
						$email_notification_subject = ! empty( $email_notifications_settings[$email_notification_slug]['subject'] ) ? $email_notifications_settings[$email_notification_slug]['subject'] : '';
						$email_notification_content = ! empty( $email_notifications_settings[$email_notification_slug]['content'] ) ? $email_notifications_settings[$email_notification_slug]['content'] : '';
						$email_notification_sending = ! empty( $email_notifications[$email_notification_slug]['sending'] ) ? $email_notifications[$email_notification_slug]['sending'] : '';
					
					?>

					<div class="slicewp-settings-email-wrapper <?php echo ( $email_notification_slug == $selected_email_notification ? 'slicewp-active' : '' ); ?>" id="slicewp-settings-email-wrapper-<?php echo str_replace( '_','-', esc_attr( $email_notification_slug ) ); ?>" >
					
						<p class="description slicewp-settings-email-description"><?php echo $email['description']; ?></p>

						<?php if ( $email_notification_sending != 'manual' ): ?>

							<!-- Email Enable/Disable -->
							<div class="slicewp-field-wrapper slicewp-email-label-wrapper">

								<div class="slicewp-field-label-wrapper">
									<label>
										<?php echo __( 'Send the email?', 'slicewp' ); ?>
									</label>
								</div>

								<div class="slicewp-switch">
									<input id="slicewp-<?php echo str_replace( '_','-', $email_notification_slug ); ?>-enabled" class="slicewp-toggle slicewp-toggle-round" name="settings[email_notifications][<?php echo esc_attr( $email_notification_slug ); ?>][enabled]" type="checkbox" value="1" <?php checked( ! empty( $_POST['settings']['email_notifications'][$email_notification_slug]['enabled'] ) ? '1' : ( empty( $_POST ) ? $email_notification_enabled : '' ), '1' ); ?> />
									<label for="slicewp-<?php echo str_replace( '_','-', $email_notification_slug ); ?>-enabled"></label>
								</div>

							</div><!-- / Email Enable/Disable -->

						<?php endif; ?>

						<!-- Email Subject -->
						<div class="slicewp-field-wrapper slicewp-email-label-wrapper">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-<?php echo str_replace( '_','-', $email_notification_slug); ?>-subject">
									<?php echo __( 'Email Subject', 'slicewp' ); ?>
								</label>
							</div>

							<input id="slicewp-<?php echo str_replace( '_','-', $email_notification_slug ); ?>-subject" name="settings[email_notifications][<?php echo $email_notification_slug; ?>][subject]" type="text" value="<?php echo ( ! empty( $_POST['settings']['email_notifications'][$email_notification_slug]['subject'] ) ? esc_attr( $_POST['settings']['email_notifications'][$email_notification_slug]['subject'] ) : ( $email_notification_subject ) ); ?>">

						</div><!-- / Email Subject -->

						<!-- Email Content -->
						<div class="slicewp-field-wrapper slicewp-email-label-wrapper">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp_<?php echo $email_notification_slug ?>_content">
									<?php echo __( 'Email Content', 'slicewp' ); ?>
								</label>
							</div>
							
							<?php 

								$content   = ! empty( $_POST['settings']['email_notifications'][$email_notification_slug]['content'] ) ? esc_attr( $_POST['settings']['email_notifications'][$email_notification_slug]['content'] ) : $email_notification_content;
								$editor_id = 'slicewp_' . $email_notification_slug . '_content';
								$settings  = array(
									'textarea_name' => 'settings[email_notifications][' . $email_notification_slug . '][content]',
									'editor_height' => 250
								);

								wp_editor( $content, $editor_id, $settings );
							
								// Add explanation about the tags the user can use in the emails
								$tags_explanation = '<div>';
									$tags_explanation .= '<ul>';
										$tags_explanation .= '<p>' . __( 'You can use the following tags in the email subject and email content to personalise your emails:', 'slicewp' ) . '</p>';

										$merge_tags = new SliceWP_Merge_Tags();
										$tags = $merge_tags->get_tags();
										
										foreach ( $tags as $tag_slug => $tag ) {

											$tags_explanation .= '<li>{{' . $tag_slug . '}} - ' . $tag['description'] . '</li>';

										}
								
									$tags_explanation .= '</ul>';
								$tags_explanation .= '</div>';

								echo $tags_explanation;

							?>

						</div><!-- / Email Content -->


						<!-- Preview Email / Send Test Email Buttons -->
						<div class="slicewp-field-wrapper slicewp-email-label-wrapper slicewp-last">
						
							<a class="slicewp-button-secondary" href="<?php echo( wp_nonce_url( add_query_arg( array( 'slicewp_action' => 'preview_email' , 'email_notification' => $email_notification_slug ) , site_url() ), 'slicewp_preview_email', 'slicewp_token' ) ); ?>" target="_blank"><?php echo __( 'Preview Email', 'slicewp' ); ?></a>
							<a class="slicewp-button-secondary" href="<?php echo( wp_nonce_url( add_query_arg( array( 'page' => 'slicewp-settings', 'tab' => 'emails', 'slicewp_action' => 'send_test_email' , 'email_notification' => $email_notification_slug ) , admin_url( 'admin.php' ) ), 'slicewp_admin_send_test_email', 'slicewp_token' ) ); ?>"><?php echo __( 'Send Test Email', 'slicewp' ); ?></a>
						
						</div><!-- / Preview Email / Send Test Email Buttons -->
					
					</div>

				<?php endforeach; ?>

				</div>

			</div><!-- / General Settings -->

			<!-- Save Settings Button -->
			<input type="submit" class="slicewp-form-submit slicewp-button-primary" value="<?php echo __( 'Save Settings', 'slicewp' ); ?>" />

		</div><!-- / Tab: Emails -->


		<!-- Tab: Tools -->
		<div class="slicewp-tab <?php echo ( $active_tab == 'tools' ? 'slicewp-active' : '' ); ?>" data-tab="tools">

			<?php 

				/**
				 * Hook to add extra cards if needed to the Tools tab
				 *
				 */
				do_action( 'slicewp_view_settings_tab_tools_top' );

			?>

			<!-- Affiliate User Role -->
			<div class="slicewp-card">

				<div class="slicewp-card-header">
					<?php echo __( 'Users Affiliate User Role', 'slicewp' ); ?>
				</div>

				<div class="slicewp-card-inner">

					<p style="margin-top: 0;"><?php echo __( 'If you want to add or remove the Affiliate user role in bulk, to or from all affiliates, please click the corresponding button below.', 'slicewp' ); ?></p>

					<a href="<?php echo wp_nonce_url( add_query_arg( array( 'slicewp_action' => 'bulk_add_affiliate_user_role' ) ), 'slicewp_bulk_add_affiliate_user_role', 'slicewp_token' ); ?>" onclick="return confirm( '<?php echo __( 'Are you sure you want to add the user role Affiliate to all users that are also affiliates in SliceWP?', 'slicewp' ); ?>')" class="slicewp-button-secondary"><?php echo __( 'Bulk Add Affiliate User Role', 'slicewp' ); ?></a>
					<a href="<?php echo wp_nonce_url( add_query_arg( array( 'slicewp_action' => 'bulk_remove_affiliate_user_role' ) ), 'slicewp_bulk_remove_affiliate_user_role', 'slicewp_token' ); ?>" onclick="return confirm( '<?php echo __( 'Are you sure you want to remove the user role Affiliate from all users?', 'slicewp' ); ?>')" class="slicewp-button-secondary"><?php echo __( 'Bulk Remove Affiliate User Role', 'slicewp' ); ?></a>

				</div>

			</div>
			<!-- / Affiliate User Role -->

			<!-- Debug Log -->
			<div class="slicewp-card">

				<div class="slicewp-card-header">
					<?php echo __( 'Debug Log', 'slicewp' ); ?>
				</div>

				<div class="slicewp-card-inner">

					<!-- Enable Logging -->
					<div class="slicewp-field-wrapper slicewp-field-wrapper-inline <?php echo ( slicewp_get_setting( 'enable_logging' ) != '1' ? 'slicewp-last' : '' ); ?>">

						<div class="slicewp-field-label-wrapper">
							<label for="slicewp-enable-logging">
								<?php echo __( 'Enable Logging', 'slicewp' ); ?>
							</label>
						</div>

						<div class="slicewp-switch">

							<input id="slicewp-enable-logging" class="slicewp-toggle slicewp-toggle-round" name="settings[enable_logging]" type="checkbox" value="1" <?php checked( ! empty( $_POST['settings']['enable_logging'] ) ? esc_attr( $_POST['settings']['enable_logging'] ) : ( empty( $_POST ) ? slicewp_get_setting( 'enable_logging' ) : '' ), '1' ); ?> />
							<label for="slicewp-enable-logging"></label>

						</div>

						<label for="slicewp-enable-logging"><?php echo __( 'Enable system logging for debugging purposes.', 'slicewp' ); ?></label>

					</div><!-- / Enable Logging -->

					<!-- Debug Log Textarea -->
					<?php if( slicewp_get_setting( 'enable_logging' ) == '1' ): ?>

						<div class="slicewp-field-wrapper slicewp-last">
							<textarea disabled style="min-height: 300px;"><?php echo esc_attr( slicewp_get_log() ); ?></textarea>
						</div>

					<?php endif; ?><!-- / Debug Textarea -->

				</div>

				<!-- Card Footer -->
				<?php if( slicewp_get_setting( 'enable_logging' ) == '1' ): ?>

					<div class="slicewp-card-footer">
						<a href="<?php echo wp_nonce_url( add_query_arg( array( 'slicewp_action' => 'download_debug_log' ) ), 'slicewp_download_debug_log', 'slicewp_token' ); ?>" class="slicewp-button-primary"><?php echo __( 'Download Debug Log', 'slicewp' ); ?></a>
						<a href="<?php echo wp_nonce_url( add_query_arg( array( 'slicewp_action' => 'clear_debug_log' ) ), 'slicewp_clear_debug_log', 'slicewp_token' ); ?>" onclick="return confirm( '<?php echo __( 'Are you sure you want to clear the debug log?', 'slicewp' ); ?>')" class="slicewp-button-secondary"><?php echo __( 'Clear Debug Log', 'slicewp' ); ?></a>
					</div>

				<?php endif; ?><!-- / Card Footer -->

			</div><!-- / System Status -->

			<!-- System Status -->
			<div class="slicewp-card">

				<div class="slicewp-card-header">
					<?php echo __( 'System Status', 'slicewp' ); ?>
				</div>

				<div class="slicewp-card-inner">

					<!-- Activate System Status -->
					<div class="slicewp-field-wrapper slicewp-field-wrapper-inline <?php echo ( slicewp_get_setting( 'enable_system_status' ) != '1' ? 'slicewp-last' : '' ); ?>">

						<div class="slicewp-field-label-wrapper">
							<label for="slicewp-enable-system-status">
								<?php echo __( 'Enable System Status', 'slicewp' ); ?>
							</label>
						</div>
						
						<div class="slicewp-switch">

							<input id="slicewp-enable-system-status" class="slicewp-toggle slicewp-toggle-round" name="settings[enable_system_status]" type="checkbox" value="1" <?php checked( ! empty( $_POST['settings']['enable_system_status'] ) ? esc_attr( $_POST['settings']['enable_system_status'] ) : ( empty( $_POST ) ? slicewp_get_setting( 'enable_system_status' ) : '' ), '1' ); ?> />
							<label for="slicewp-enable-system-status"></label>

						</div>

						<label for="slicewp-enable-system-status"><?php echo __( 'Click to enable the System Status Report.', 'slicewp' ); ?></label>

					</div><!-- / Activate System Status -->

					<!-- System Status Textarea -->
					<?php if( slicewp_get_setting( 'enable_system_status' ) == '1' ): ?>

						<div class="slicewp-field-wrapper slicewp-last">
							<textarea disabled style="min-height: 300px;"><?php echo esc_attr( slicewp_system_status() ); ?></textarea>
						</div>

					<?php endif; ?><!-- / System Status Textarea -->

				</div>

			</div><!-- / System Status -->

			<!-- Setup Wizard -->
			<div class="slicewp-card">

				<div class="slicewp-card-header">
					<?php echo __( 'Setup Wizard', 'slicewp' ); ?>
				</div>

				<div class="slicewp-card-inner">

					<p style="margin-top: 0;"><?php echo __( 'If you need to run the setup wizard again, please click the button below.', 'slicewp' ); ?></p>

					<a href="<?php echo add_query_arg( array( 'page' => 'slicewp-setup' ), admin_url('index.php') ); ?>" class="slicewp-button-secondary"><?php echo __( 'Setup Wizard', 'slicewp' ); ?></a>

				</div>

			</div><!-- / Setup Wizard -->

			<!-- Plugin Usage Tracking -->
			<div class="slicewp-card">

				<div class="slicewp-card-header">
					<?php echo __( 'Usage Tracking', 'slicewp' ); ?>
				</div>

				<div class="slicewp-card-inner">

					<!-- Activate Plugin Usage Tracking -->
					<div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-last">

						<div class="slicewp-field-label-wrapper">
							<label for="slicewp-allow-tracking">
								<?php echo __( 'Allow Tracking', 'slicewp' ); ?>
							</label>
						</div>
						
						<div class="slicewp-switch">

							<input id="slicewp-allow-tracking" class="slicewp-toggle slicewp-toggle-round" name="settings[allow_tracking]" type="checkbox" value="1" <?php checked( ! empty( $_POST['settings']['allow_tracking'] ) ? esc_attr( $_POST['settings']['allow_tracking'] ) : ( empty( $_POST ) ? slicewp_get_setting( 'allow_tracking' ) : '' ), '1' ); ?> />
							<label for="slicewp-allow-tracking"></label>

						</div>

						<label for="slicewp-allow-tracking"><?php echo __( "Allow SliceWP to anonymously track the plugin's usage. The collected data can help us improve the plugin and provide better features. Sensitive data will not be tracked.", 'slicewp' ); ?></label>

						<p style="margin-bottom: 0;"><a href="https://slicewp.com/docs/usage-tracking/" target="_blank"><?php echo __( "Learn more about what we track and what we don't.", 'slicewp' ); ?></a></p>

					</div><!-- / Activate Plugin Usage Tracking -->

				</div>

			</div><!-- / Plugin Usage Tracking -->

			<?php 

				/**
				 * Hook to add extra cards if needed to the Tools tab
				 *
				 */
				do_action( 'slicewp_view_settings_tab_tools_bottom' );

			?>

			<!-- Save Settings Button -->
			<input type="submit" class="slicewp-form-submit slicewp-button-primary" value="<?php echo __( 'Save Settings', 'slicewp' ); ?>" />

		</div><!-- / Tab: Tools -->

		<!-- Action and nonce -->
		<input type="hidden" name="slicewp_action" value="save_settings" />
		<?php wp_nonce_field( 'slicewp_save_settings', 'slicewp_token', false ); ?>

	</form>

</div>