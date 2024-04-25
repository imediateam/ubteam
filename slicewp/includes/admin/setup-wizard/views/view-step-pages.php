<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="slicewp-card">

	<div class="slicewp-card-inner">

		<div class="slicewp-setup-pages">

			<h2><?php echo __( "Welcome your affiliates", 'slicewp' ); ?></h2>

			<p><?php echo __( "To offer your affiliates a welcoming experience and have them interact with the website, SliceWP can automatically create a few pages designed precisely for them.", 'slicewp' ) ?></p>

			<p><?php echo __( "You can select just one or all of them below.", 'slicewp' ); ?></p>

			<br />

			<!-- Affiliate Register Page -->
			<div class="slicewp-field-wrapper">

				<div class="slicewp-field-label-wrapper">
					<label for="slicewp-affiliate-register-page">
						<?php echo __( 'Affiliate Register Page', 'slicewp' ); ?>
					</label>
				</div>

				<div class="slicewp-switch">

					<input id="slicewp-affiliate-register-page" class="slicewp-toggle slicewp-toggle-round" name="page_affiliate_register" type="checkbox" checked="checked" value="1" />
					<label for="slicewp-affiliate-register-page"></label>

				</div>

				<label for="slicewp-affiliate-register-page"><?php echo __( "This page will contain a form where users will have the ability to register as affiliates.", 'slicewp' ); ?></label>

			</div><!-- / Affiliate Register Page -->

			<!-- Affiliate Login Page -->
			<div class="slicewp-field-wrapper">

				<div class="slicewp-field-label-wrapper">
					<label for="slicewp-affiliate-login-page">
						<?php echo __( 'Affiliate Login Page', 'slicewp' ); ?>
					</label>
				</div>

				<div class="slicewp-switch">

					<input id="slicewp-affiliate-login-page" class="slicewp-toggle slicewp-toggle-round" name="page_affiliate_login" type="checkbox" checked="checked" value="1" />
					<label for="slicewp-affiliate-login-page"></label>

				</div>

				<label for="slicewp-affiliate-login-page"><?php echo __( 'This page will contain a form where affiliates will be able to log into their affiliate account.', 'slicewp' ); ?></label>

			</div><!-- / Affiliate Login Page -->

			<!-- Affiliate Account Page -->
			<div class="slicewp-field-wrapper slicewp-last">

				<div class="slicewp-field-label-wrapper">
					<label for="slicewp-affiliate-account-page">
						<?php echo __( 'Affiliate Account Page', 'slicewp' ); ?>
					</label>
				</div>

				<div class="slicewp-switch">

					<input id="slicewp-affiliate-account-page" class="slicewp-toggle slicewp-toggle-round" name="page_affiliate_account" type="checkbox" checked="checked" value="1" />
					<label for="slicewp-affiliate-account-page"></label>

				</div>

				<label for="slicewp-affiliate-account-page"><?php echo __( "This page will be your affiliates' personal dashboard, where they'll be able to generate referral links and view the visits and commissions they generated.", 'slicewp' ); ?></label>

			</div><!-- / Affiliate Account Page -->

		</div>

	</div>

	<div class="slicewp-card-footer">

		<div class="slicewp-submit-wrapper-setup-wizard">
			
			<input type="submit" class="slicewp-form-submit slicewp-button-primary" value="<?php echo __( 'Continue', 'slicewp' ); ?>" />

		</div>

	</div>

</div>