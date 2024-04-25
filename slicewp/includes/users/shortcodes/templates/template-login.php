<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<h1>Affiliate Login</h1>

<form id="slicewp-affiliate-login-form" action="" method="POST">

	<!-- Notices -->
	<?php do_action( 'slicewp_user_notices' ); ?>

	<!-- Login -->
	<div class="slicewp-field-wrapper">

		<div class="slicewp-field-label-wrapper">
			<label for="slicewp-user-login"><?php echo __( 'Username / Email', 'slicewp' ); ?> *</label>
		</div>
		
		<input id="slicewp-user-login" name="login" type="text" value="<?php echo ( ! empty( $_POST['login'] ) ? esc_attr( $_POST['login'] ) : '' ); ?>" />

	</div>

	<!-- Password -->
	<div class="slicewp-field-wrapper">

		<div class="slicewp-field-label-wrapper">
			<label for="slicewp-user-password"><?php echo __( 'Password', 'slicewp' ); ?> *</label>
		</div>
		
		<input id="slicewp-user-password" name="password" type="password" value="" />
	</div>

	<!-- Action and nonce -->
	<input type="hidden" name="slicewp_action" value="login_affiliate" />
	<?php wp_nonce_field( 'slicewp_login_affiliate', 'slicewp_token', false ); ?>

	<!-- Redirect URL -->
	<input type="hidden" name="redirect_url" value="<?php echo ( ! empty($atts['redirect_url']) ? esc_url( $atts['redirect_url'] ) : '' ); ?>" />

	<!-- Submit -->
	<input type="submit" class="slicewp-button-primary" value="<?php echo __( 'Login', 'slicewp' ); ?>" />
	
</form>
