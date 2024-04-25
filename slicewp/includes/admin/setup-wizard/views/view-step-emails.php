<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="slicewp-card">

	<div class="slicewp-card-inner">

		<div class="slicewp-setup-emails">

			<h2><?php echo __( "Email notifications. Which ones do you want activated to start with?", 'slicewp' ); ?></h2>

			<p><?php echo __( "You'll be able to customize each email to your needs later on in the settings page of SliceWP.", 'slicewp' ) ?></p>

			<br />

			<!-- Email Notifications -->
			<?php
				$email_notifications 	   = slicewp_get_available_email_notifications();
				$email_notifications_count = 0;
			?>

			<?php foreach( $email_notifications as $email_notification_slug => $email_notification ): $email_notifications_count++; ?>

				<?php if( ! empty( $email_notification['sending'] ) && $email_notification['sending'] == 'manual' ) continue; ?>

				<div class="slicewp-field-wrapper slicewp-field-wrapper-email-notification <?php echo ( $email_notifications_count == count( $email_notifications ) ? 'slicewp-last' : '' ); ?>">

					<div class="slicewp-field-label-wrapper">
						<label for="slicewp-<?php echo esc_attr( $email_notification_slug ); ?>">
							<?php echo sprintf( __( '%s Notification', 'slicewp' ), ucfirst( $email_notification['recipient'] ) ) . ' - ' . $email_notification['name']; ?>
						</label>
					</div>

					<div class="slicewp-switch">

						<input id="slicewp-<?php echo esc_attr( $email_notification_slug ); ?>" class="slicewp-toggle slicewp-toggle-round" name="<?php echo esc_attr( $email_notification_slug ); ?>" type="checkbox" checked="checked" value="1" />
						<label for="slicewp-<?php echo esc_attr( $email_notification_slug ); ?>"></label>

					</div>

					<label for="slicewp-<?php echo esc_attr( $email_notification_slug ); ?>"><?php echo $email_notification['description']; ?></label>

				</div>

			<?php endforeach; ?><!-- / Email Notifications -->

		</div>

	</div>

	<div class="slicewp-card-footer">

		<div class="slicewp-submit-wrapper-setup-wizard">

			<input type="submit" class="slicewp-form-submit slicewp-button-primary" value="<?php echo __( 'Continue', 'slicewp' ); ?>" />
			
		</div>

	</div>

</div>