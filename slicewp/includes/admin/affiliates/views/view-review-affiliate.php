<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$affiliate_id = ( ! empty( $_GET['affiliate_id'] ) ? sanitize_text_field( $_GET['affiliate_id'] ) : 0 );

// Verify for affiliate id
if( empty( $affiliate_id ) )
	return;

// Verify if affiliate exits
$affiliate = slicewp_get_affiliate( $affiliate_id );

if( is_null( $affiliate ) )
	return;

// Verify if affiliate status is 'pending'
if ( $affiliate->get( 'status' ) != 'pending' )
	return;

// Get the affiliate information
$user = get_user_by( 'id', $affiliate->get('user_id') );
$affiliate_username = $user->user_login;
$affiliate_email = $user->user_email;
$affiliate_message = slicewp_get_affiliate_meta( $affiliate_id, 'promotional_methods', true );

if ( empty( $affiliate_message ) )
	$affiliate_message = __( 'No message provided.' , 'slicewp' );

// Prepare the warnings in case the Approve/Reject Account email notifications are empty
$email_notifications = slicewp_get_available_email_notifications();

$approve_notification_settings = slicewp_get_email_notification_settings( 'affiliate_account_approved' );
if ( empty( $approve_notification_settings['subject'] ) || empty( $approve_notification_settings['content'] ) )
	$approve_email_notification_warning = sprintf( __('The "%s" email notification is not complete. Please update it %shere%s.', 'slicewp'), $email_notifications['affiliate_account_approved']['name'], '<a href="' . add_query_arg( array( 'page' => 'slicewp-settings', 'tab' => 'emails', 'email_notification' => 'affiliate_account_approved' ), admin_url( 'admin.php' ) ) . '#slicewp-email-notifications-settings' . '">', '</a>' );

$reject_notification_settings = slicewp_get_email_notification_settings( 'affiliate_account_rejected' );
if ( empty( $reject_notification_settings['subject'] ) || empty( $reject_notification_settings['content'] ) )
	$reject_email_notification_warning = sprintf( __('The "%s" email notification is not complete. Please update it %shere%s.','slicewp'), $email_notifications['affiliate_account_rejected']['name'] , '<a href="' . add_query_arg( array( 'page' => 'slicewp-settings', 'tab' => 'emails', 'email_notification' => 'affiliate_account_rejected' ), admin_url( 'admin.php' ) ) . '#slicewp-email-notifications-settings' . '">' ,'</a>' );

?>

<div class="wrap slicewp-wrap slicewp-wrap-review-affiliate">

	<form action="" method="POST">

		<!-- Page Heading -->
		<h1 class="wp-heading-inline"><?php echo __( 'Review Affiliate Registration', 'slicewp' ); ?></h1>
		<hr class="wp-header-end" />

		<!-- Postbox -->
		<div class="slicewp-card slicewp-first">

			<!-- Email Notifications Warnings -->
			<?php

				if ( ! empty( $approve_email_notification_warning ) ) {

					echo '<div class="notice notice-warning">';
					echo '<p>' . $approve_email_notification_warning . '</p>';
					echo '</div>';
				
				}

				if ( ! empty( $reject_email_notification_warning ) ) {

					echo '<div class="notice notice-warning">';
					echo '<p>' . $reject_email_notification_warning . '</p>';
					echo '</div>';
				
				}
			?>

			<!-- Form Fields -->
			<div class="slicewp-card-inner">

				<!-- Affiliate Username -->
				<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

					<div class="slicewp-field-label-wrapper">
						<label for="slicewp-affiliate-affiliate-name"><?php echo __( 'Affiliate Username', 'slicewp' ); ?></label>
					</div>
					
					<input id="slicewp-affiliate-affiliate-name" name="affiliate_name" disabled type="text" value="<?php echo esc_attr( $affiliate_username ); ?>" />

				</div>

				<!-- Affiliate Name -->
				<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

					<div class="slicewp-field-label-wrapper">
						<label for="slicewp-affiliate-affiliate-name"><?php echo __( 'Affiliate Name', 'slicewp' ); ?></label>
						<?php echo slicewp_output_tooltip( sprintf( __( 'This is the display name of the user attached to this affiliate. You can change this value from the %suser edit page%s.', 'slicewp' ), '<a href="' . add_query_arg( array( 'user_id' => $affiliate->get('user_id') ), admin_url( 'user-edit.php' ) ) . '">', '</a>' ) ); ?>
					</div>
					
					<input id="slicewp-affiliate-affiliate-name" name="affiliate_name" disabled type="text" value="<?php echo esc_attr( slicewp_get_affiliate_name( $affiliate ) ); ?>" />

				</div>

				<!-- Affiliate Email -->
				<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

					<div class="slicewp-field-label-wrapper">
						<label for="slicewp-affiliate-affiliate-email"><?php echo __( 'Affiliate Email', 'slicewp' ); ?></label>
					</div>
					
					<input id="slicewp-affiliate-affiliate-email" name="affiliate_email" disabled type="text" value="<?php echo esc_attr( $affiliate_email ); ?>" />

				</div>
				
				<!-- Affiliate ID -->
				<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

					<div class="slicewp-field-label-wrapper">
						<label for="slicewp-affiliate-affiliate-id"><?php echo __( 'Affiliate ID', 'slicewp' ); ?></label>
					</div>
					
					<input id="slicewp-affiliate-affiliate-id" name="affiliate_id" disabled type="text" value="<?php echo esc_attr( $affiliate->get('id') ); ?>" />

				</div>

				<!-- User ID -->
				<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

					<div class="slicewp-field-label-wrapper">
						<label for="slicewp-affiliate-user-id"><?php echo __( 'User ID', 'slicewp' ); ?></label>
					</div>
					
					<input id="slicewp-affiliate-user-id" name="user_id" disabled type="text" value="<?php echo esc_attr( $affiliate->get('user_id') ); ?>" />

				</div>

				<!-- Registration Date -->
				<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

					<div class="slicewp-field-label-wrapper">
						<label for="slicewp-affiliate-registration-date"><?php echo __( 'Registration Date', 'slicewp' ); ?></label>
					</div>
					
					<input id="slicewp-affiliate-registration-date" name="registration_date" disabled type="text" value="<?php echo slicewp_date_i18n( esc_attr( $affiliate->get('date_created') ) ); ?>" />

				</div>
				
				<!-- Website -->
				<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

					<div class="slicewp-field-label-wrapper">
						<label for="slicewp-affiliate-website"><?php echo __( 'Website', 'slicewp' ); ?></label>
					</div>
					
					<input id="slicewp-affiliate-website" name="website" disabled type="text" value="<?php echo ( esc_url( $affiliate->get( 'website' ) ) ); ?>" />

				</div>
				
				<!-- Payment Email -->
				<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

					<div class="slicewp-field-label-wrapper">
						<label for="slicewp-affiliate-payment-email"><?php echo __( 'Payment Email', 'slicewp' ); ?></label>
					</div>
					
					<input id="slicewp-affiliate-payment-email" name="payment_email" disabled type="text" value="<?php echo ( esc_attr( $affiliate->get('payment_email') ) ); ?>" />

				</div>
				
				<!-- Affiliate Message -->
				<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

					<div class="slicewp-field-label-wrapper">
						<label for="slicewp-affiliate-affiliate-message"><?php echo __( 'Affiliate Message', 'slicewp' ); ?></label>
					</div>
					
					<textarea id="slicewp-affiliate-affiliate-message" name="affiliate_message" disabled><?php echo esc_attr( $affiliate_message ); ?></textarea>

				</div>

				<!-- Application Status -->
				<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

					<div class="slicewp-field-label-wrapper">
						<label for="slicewp-affiliate-application-status"><?php echo __( 'Application Status', 'slicewp' ); ?></label>
					</div>
					
					<select id="slicewp-affiliate-application-status" name="application_status" class="slicewp-select2">
						<option value="application_approved" <?php echo ( ! empty( $_POST['application_status'] ) ? selected( $_POST['application_status'], 'application_approved', false ) : '' ); ?>><?php echo __('Approved', 'slicewp'); ?></option>
						<option value="application_rejected" <?php echo ( ! empty( $_POST['application_status'] ) ? selected( $_POST['application_status'], 'application_rejected', false ) : '' ); ?>><?php echo __('Rejected', 'slicewp'); ?></option>
					</select>

				</div>

				<!-- Notify with email? -->
				<div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-last">

					<div class="slicewp-field-label-wrapper">
						<label for="slicewp-send-email-notification"><?php echo __( 'Email Notification', 'slicewp' ); ?></label>
						<?php echo slicewp_output_tooltip( __( 'When enabled, an approval/rejection email notification will be sent to the affiliate.', 'slicewp' ) ); ?>
					</div>

					<div class="slicewp-switch">
						<input id="slicewp-send-email-notification" class="slicewp-toggle slicewp-toggle-round" name="send_email_notification" type="checkbox" value="1" checked />
						<label for="slicewp-send-email-notification"></label>
					</div>

					<?php echo ( __( '<a href="' . add_query_arg( array( 'page' => 'slicewp-settings', 'tab' => 'emails', 'email_notification' => 'affiliate_account_approved' ), admin_url( 'admin.php' ) ) . '#slicewp-email-notifications-settings' . '" id="slicewp-link-approve-email-notification">[edit email notification]</a>', 'slicewp' ) ); ?>
					<?php echo ( __( '<a href="' . add_query_arg( array( 'page' => 'slicewp-settings', 'tab' => 'emails', 'email_notification' => 'affiliate_account_rejected' ), admin_url( 'admin.php' ) ) . '#slicewp-email-notifications-settings' . '" id="slicewp-link-reject-email-notification" style="display:none;">[edit email notification]</a>', 'slicewp' ) ); ?>

				</div>

				<!-- Reject Reason -->
				<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

					<div class="slicewp-field-label-wrapper">
						<label for="slicewp-affiliate-reject-reason"><?php echo __( 'Reject Reason', 'slicewp' ); ?></label>
						<?php echo slicewp_output_tooltip( __( 'The Reject Reason will be replaced with the tag {{reject_reason}} in the Rejection email notification template.', 'slicewp' ) ); ?>
					</div>

					<textarea id="slicewp-affiliate-reject-reason" name="affiliate_reject_reason"></textarea>

				</div>

			</div>

		</div>

		<!-- Hidden Affiliate ID field -->
		<input type="hidden" name="affiliate_id" value="<?php echo $affiliate_id; ?>" />

		<!-- Action and nonce -->
		<input type="hidden" name="slicewp_action" value="review_affiliate" />
		<?php wp_nonce_field( 'slicewp_review_affiliate', 'slicewp_token', false ); ?>

		<!-- Submit -->
		<input type="submit" class="slicewp-form-submit slicewp-button-primary" id="slicewp-approve-affiliate" name="slicewp_approve_affiliate" value="<?php echo __( 'Approve', 'slicewp' ); ?>" data-confirmation-message="<?php echo __( 'Are you sure you want to Approve the affiliate submission?', 'slicewp' ); ?>" />
		<input type="submit" class="slicewp-form-submit slicewp-button-primary" id="slicewp-reject-affiliate" name="slicewp_reject_affiliate" value="<?php echo __( 'Reject', 'slicewp' ); ?>" data-confirmation-message="<?php echo __( 'Are you sure you want to Reject the affiliate submission?', 'slicewp' ); ?>" />

	</form>

</div>