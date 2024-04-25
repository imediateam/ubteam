<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<form id="slicewp-affiliate-register-form" action="" method="POST">

	<!-- Notices -->
	<?php do_action( 'slicewp_user_notices' ); ?>

	<?php if ( ! is_user_logged_in() ): ?>

		<!-- Username -->
		<div class="slicewp-field-wrapper">

			<div class="slicewp-field-label-wrapper">
				<label for="slicewp-user-username"><?php echo __( 'Username', 'slicewp' ); ?> <span class="slicewp-field-required-marker">*</span></label>
			</div>
			
			<input id="slicewp-user-username" name="username" type="text" value="<?php echo ( ! empty( $_POST['username'] ) ? esc_attr( $_POST['username'] ) : '' ); ?>" />

		</div>

		<!-- First Name -->
		<div class="slicewp-field-wrapper">

			<div class="slicewp-field-label-wrapper">
				<label for="slicewp-user-first-name"><?php echo __( 'First Name', 'slicewp' ); ?> <span class="slicewp-field-required-marker">*</span></label>
			</div>
			
			<input id="slicewp-user-first-name" name="first_name" type="text" value="<?php echo ( ! empty( $_POST['first_name'] ) ? esc_attr( $_POST['first_name'] ) : '' ); ?>" />

		</div>

		<!-- Last Name -->
		<div class="slicewp-field-wrapper">

			<div class="slicewp-field-label-wrapper">
				<label for="slicewp-user-last-name"><?php echo __( 'Last Name', 'slicewp' ); ?> <span class="slicewp-field-required-marker">*</span></label>
			</div>
			
			<input id="slicewp-user-last-name" name="last_name" type="text" value="<?php echo ( ! empty( $_POST['last_name'] ) ? esc_attr( $_POST['last_name'] ) : '' ); ?>" />

		</div>
	
		<!-- Email -->
		<div class="slicewp-field-wrapper">

			<div class="slicewp-field-label-wrapper">
				<label for="slicewp-user-email"><?php echo __( 'Email', 'slicewp' ); ?> <span class="slicewp-field-required-marker">*</span></label>
			</div>
			
			<input id="slicewp-user-email" name="email" type="email" value="<?php echo ( ! empty( $_POST['email'] ) ? esc_attr( $_POST['email'] ) : '' ); ?>" />

		</div>
		
		<!-- Password -->
		<div class="slicewp-field-wrapper">

			<div class="slicewp-field-label-wrapper">
				<label for="slicewp-user-password"><?php echo __( 'Password', 'slicewp' ); ?> <span class="slicewp-field-required-marker">*</span></label>
			</div>
			
			<input id="slicewp-user-password" name="password" type="password" value="" />

		</div>

		<!-- Password Confirmation -->
		<div class="slicewp-field-wrapper">

			<div class="slicewp-field-label-wrapper">
				<label for="slicewp-user-password-confirm"><?php echo __( 'Password Confirmation', 'slicewp' ); ?> <span class="slicewp-field-required-marker">*</span></label>
			</div>
			
			<input id="slicewp-user-password-confirm" name="password_confirmation" type="password" value="" />

		</div>

	<?php endif; ?>

	<!-- Payment Email -->
	<div class="slicewp-field-wrapper">

		<div class="slicewp-field-label-wrapper">
			<label for="slicewp-payment-email"><?php echo __( 'Payment Email', 'slicewp' ); ?> <?php echo ( slicewp_get_setting( 'required_field_payment_email' ) ? '<span class="slicewp-field-required-marker">*</span>' : '' ); ?></label>
		</div>
		
		<input id="slicewp-payment-email" name="payment_email" type="email" value="<?php echo ( ! empty( $_POST['payment_email'] ) ? esc_attr( $_POST['payment_email'] ) : '' ); ?>" />

	</div>

	<!-- Website -->
	<div class="slicewp-field-wrapper">

		<div class="slicewp-field-label-wrapper">
			<label for="slicewp-affiliate-website"><?php echo __( 'Website', 'slicewp' ); ?> <?php echo ( slicewp_get_setting( 'required_field_website' ) ? '<span class="slicewp-field-required-marker">*</span>' : '' ); ?></label>
		</div>
		
		<input id="slicewp-affiliate-website" name="website" type="url" value="<?php echo ( ! empty( $_POST['website'] ) ? esc_attr( $_POST['website'] ) : '' ); ?>" />

	</div>

	<!-- Promotional Method -->
	<div class="slicewp-field-wrapper">

		<div class="slicewp-field-label-wrapper">
			<label for="slicewp-user-promotional-methods"><?php echo __( 'How will you promote us?', 'slicewp' ); ?> <?php echo ( slicewp_get_setting( 'required_field_promotional_methods' ) ? '<span class="slicewp-field-required-marker">*</span>' : '' ); ?></label>
		</div>
		
		<textarea id="slicewp-user-promotional-methods" name="promotional_methods"><?php echo ( ! empty( $_POST['promotional_methods'] ) ? esc_attr( $_POST['promotional_methods'] ) : '' ); ?></textarea>
	</div>

	<?php $page_terms_conditions = slicewp_get_setting( 'page_terms_conditions' ); ?>

	<?php if ( ! empty( $page_terms_conditions ) ): ?>

		<!-- Terms and Conditions -->
		<div class="slicewp-field-wrapper slicewp-field-wrapper-terms-and-conditions">

			<div class="slicewp-field-label-wrapper">

				<input id="slicewp-terms-and-conditions" name="terms_conditions" type="checkbox" value="1" <?php checked( ! empty( $_POST['terms_conditions'] ), '1' ) ?>/>
				<label for="slicewp-terms-and-conditions"><a href="<?php echo get_permalink( slicewp_get_setting( 'page_terms_conditions' ) ); ?>" target="_blank"><?php echo ( ! empty( slicewp_get_setting( 'terms_label' ) ) ? slicewp_get_setting( 'terms_label' ) :  __( 'Agree to Our Terms and Conditions', 'slicewp' ) ); ?></a></label>

			</div>

		</div>

	<?php endif; ?>

	<?php $recaptcha = slicewp_get_setting( 'enable_recaptcha' ); ?>

	<?php if( ! empty( $recaptcha ) ): ?>

		<!-- reCAPTCHA -->
		<div class="slicewp-field-wrapper slicewp-field-wrapper-recaptcha">

			<?php wp_enqueue_script( 'slicewp-recaptcha-async-defer', 'https://www.google.com/recaptcha/api.js' ); ?>
			<div class="g-recaptcha" data-sitekey="<?php echo esc_attr( slicewp_get_setting( 'recaptcha_site_key' ) ); ?>"></div>
			<input type="hidden" name="g-recaptcha-remoteip" value="<?php echo esc_attr( slicewp_get_user_ip_address() ); ?>" />

		</div>

	<?php endif; ?>

	<!-- Action and nonce -->
	<input type="hidden" name="slicewp_action" value="register_affiliate" />
	<?php wp_nonce_field( 'slicewp_register_affiliate', 'slicewp_token', false ); ?>

	<!-- Redirect URL -->
	<input type="hidden" name="redirect_url" value="<?php echo ( ! empty( $atts['redirect_url'] ) ? esc_url( $atts['redirect_url'] ) : '' ); ?>" />

	<!-- Submit -->
	<input type="submit" class="slicewp-button-primary" value="<?php echo __( 'Register', 'slicewp' ); ?>" />
	
</form>