<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$tabs = array(
	'dashboard' => array(
		'label' => __( 'Dashboard', 'slicewp' ),
		'icon'  => 'dashicons-welcome-widgets-menus'
	),
	'affiliate_links' => array(
		'label' => __( 'Affiliate Links', 'slicewp' ),
		'icon'  => 'dashicons-admin-links'
	),
	'commissions' => array(
		'label' => __( 'Commissions', 'slicewp' ),
		'icon'  => 'dashicons-chart-pie'
	),
	'visits' => array(
		'label' => __( 'Visits', 'slicewp' ),
		'icon'  => 'dashicons-chart-bar'
    ),
    'creatives' => array(
		'label' => __( 'Creatives', 'slicewp' ),
		'icon'  => 'dashicons-layout'
	),
	'payments' => array(
		'label' => __( 'Payouts', 'slicewp' ),
		'icon'  => 'dashicons-money'
	),
	'settings' => array(
		'label' => __( 'Settings', 'slicewp' ),
		'icon'  => 'dashicons-admin-tools'
	)
);


/**
 * Filter the tabs for the settings edit screen
 *
 * @param array $tabs
 *
 */
$tabs = apply_filters( 'slicewp_affiliate_account_page', $tabs );

$active_tab = ( ! empty( $_GET['affiliate-account-tab'] ) ? sanitize_text_field( $_GET['affiliate-account-tab'] ) : 'dashboard' );


/**
 * Get the visits page number
 *
 */
$page_visits = ( ! empty( $_GET['page_visits'] ) ? absint( $_GET['page_visits'] ) : 1 );


/**
 * Get the commissions page number
 *
 */
$page_commissions = ( ! empty( $_GET['page_commissions'] ) ? absint( $_GET['page_commissions'] ) : 1 );


/**
 * Get the creatives page number
 *
 */
$page_creatives = ( ! empty( $_GET['page_creatives'] ) ? absint( $_GET['page_creatives'] ) : 1 );


/**
 * Get the payments page number
 *
 */
$page_payments = ( ! empty( $_GET['page_payments'] ) ? absint( $_GET['page_payments'] ) : 1 );


/**
 * Get the Affiliate ID
 *
 */
$affiliate_id = slicewp_get_current_affiliate_id();
$affiliate = slicewp_get_affiliate( $affiliate_id );
$affiliate_keyword = slicewp_get_setting( 'affiliate_keyword', 'aff' );

if ( ! empty( $affiliate ) ){

	$affiliate_payment_email = $affiliate->get('payment_email');
	$affiliate_website = $affiliate->get('website');

}

/**
 * Get the Dashboard dates
 * 
 */
$date_min = ( new DateTime() )->sub( new DateInterval( 'P30D' ) );
$date_min->setTime( 00, 00, 00 );

$date_max = new DateTime();
$date_max->setTime( 23, 59, 59 );

?>

<h1>Affiliate Area</h1>

<div id="slicewp-affiliate-account" data-affiliate-id="<?php echo absint( $affiliate_id ); ?>" data-affiliate-keyword="<?php echo esc_attr( $affiliate_keyword ); ?>">

	<!-- Tab Navigation -->
	<div id="slicewp-affiliate-account-nav-tab">

		<!-- Navigation Tab Links -->
		<ul class="slicewp-nav-tab-wrapper">
			<?php 
				foreach( $tabs as $tab_slug => $tab ) {
					echo '<li class="slicewp-nav-tab ' . ( $tab_slug == $active_tab ? 'slicewp-active' : '' ) . '" data-slicewp-tab="' . esc_attr( $tab_slug ) . '"><a href="#"><span class="dashicons ' . esc_attr( $tab['icon'] ) . '"></span><span>' . esc_attr( $tab['label'] ) . '</span></a></li>';
				}
			?>
		</ul>

		<!-- Hidden active tab -->
		<input type="hidden" name="active_tab" value="<?php echo esc_attr( $active_tab ); ?>" />

	</div>
	
	<!-- Tab: Dashboard -->
	<div class="slicewp-tab <?php echo ( $active_tab == 'dashboard' ? 'slicewp-active' : '' ); ?>" data-slicewp-tab="dashboard">

		<div class="slicewp-row">

			<div class="slicewp-col-1-2">

				<!-- Commission Rates -->
				<div class="slicewp-card slicewp-card-affiliate-dashboard">

					<div class="slicewp-card-header">
						<?php
						
							// Get the supported commissions
							$available_commission_types = slicewp_get_available_commission_types();
							$affiliate_commission_rates = slicewp_get_affiliate_commission_rates( $affiliate_id );
						
							if ( count( $affiliate_commission_rates ) > 1 )
								echo __( 'Commission Rates', 'slicewp' );
							else
								echo __( 'Commission Rate', 'slicewp' ); 

						?>
					</div>

					<div class="slicewp-card-inner">
						<?php

							foreach ( $affiliate_commission_rates as $key => $details ){

								echo sprintf ( __( '%s rate: %s', 'slicewp' ), $available_commission_types[$key]['label'], ( $details['rate_type'] == 'percentage' ? $details['rate'] . '%' : slicewp_format_amount( $details['rate'], slicewp_get_currency_symbol( slicewp_get_setting( 'active_currency', 'USD' ) ) ) ) );
								echo '<br>';

							}

						?>
					</div>
					
				</div><!-- / Commission Rate -->

			</div>

			<div class="slicewp-col-1-2">

				<!-- Cookie Duration -->
				<div class="slicewp-card slicewp-card-affiliate-dashboard">

					<div class="slicewp-card-header">
						<?php echo __( 'Cookie Duration', 'slicewp' ); ?>
					</div>

					<div class="slicewp-card-inner">
						<?php echo sprintf( __( '%s days', 'slicewp'), slicewp_get_setting( 'cookie_duration' ) );?>
					</div>

				</div><!-- / Cookie Duration -->

			</div>

		</div>

		<div class="slicewp-row">

			<div class="slicewp-col-1-2">

				<!-- Unpaid Commissions Last 30 Days -->
				<div class="slicewp-card slicewp-card-affiliate-dashboard">

					<div class="slicewp-card-header">
						<?php echo __( 'Total Unpaid Commissions', 'slicewp' ); ?>
					</div>

					<div class="slicewp-card-inner">
						<?php 
				$ub_commission_args = array(
				'affiliate_id'	=> $affiliate_id,
				'status'        => 'unpaid'
			);

			$unp_count = slicewp_get_commissions( $ub_commission_args, true );
			$unp_tot = $unp_count * 10;
			echo $unp_count .' x $10 = $' .$unp_tot;
						?>
					</div>

					<div class="slicewp-card-footer">
						<?php echo __( 'Total unpaid comissions', 'slicewp' ); ?>
					</div>
					
				</div><!-- / Unpaid Commissions Last 30 Days -->

			</div>

			<div class="slicewp-col-1-2">

				<!-- Unpaid Commissions All Time -->
				<div class="slicewp-card slicewp-card-affiliate-dashboard">

					<div class="slicewp-card-header">
						<?php echo __( 'Unpaid Commissions', 'slicewp' ); ?>
					</div>

					<div class="slicewp-card-inner">
						<?php
			$ub_commission_args = array(
				'affiliate_id'	=> $affiliate_id,
				'status'        => 'unpaid'
			);

			$unp_count = slicewp_get_commissions( $ub_commission_args, true );
			$unp_tot = $unp_count * 10;
			echo $unp_count .' x $10 = $' .$unp_tot;
						?>
					</div>

					<div class="slicewp-card-footer">
						<?php echo __( 'All Time', 'slicewp' ); ?>
					</div>

				</div><!-- / Unpaid Commissions All Time -->

			</div>

		</div>

		<div class="slicewp-row">

			<div class="slicewp-col-1-2">

				<!-- Paid Commissions Last 30 Days -->
				<div class="slicewp-card slicewp-card-affiliate-dashboard">

					<div class="slicewp-card-header">
						<?php echo __( 'Paid Commissions', 'slicewp' ); ?>
					</div>

					<div class="slicewp-card-inner">
						<?php
				$ubp_commission_args = array(
				'affiliate_id'	=> $affiliate_id,
				'status'        => 'paid'
			);
			echo slicewp_get_commissions( $ubp_commission_args, true );
						?>
					</div>

					<div class="slicewp-card-footer">
						<?php echo __( 'Since Last Payout', 'slicewp' ); ?>
					</div>
					
				</div><!-- / Paid Commissions Last 30 Days -->

			</div>

			<div class="slicewp-col-1-2">

				<!--  Paid Commissions All Time -->
				<div class="slicewp-card slicewp-card-affiliate-dashboard">

					<div class="slicewp-card-header">
						<?php echo __( 'Paid Commissions', 'slicewp' ); ?>
					</div>

					<div class="slicewp-card-inner">
						<?php
				$ubp_commission_args = array(
				'affiliate_id'	=> $affiliate_id,
				'status'        => 'paid'
			);
			echo slicewp_get_commissions( $ubp_commission_args, true );							
						?>
					</div>

					<div class="slicewp-card-footer">
						<?php echo __( 'All Time', 'slicewp' ); ?>
					</div>

				</div><!-- / Paid Commissions All Time -->

			</div>

		</div>

		<div class="slicewp-row slicewp-last">

			<div class="slicewp-col-1-2">

				<!-- Visits Last 30 Days -->
				<div class="slicewp-card slicewp-card-affiliate-dashboard">

					<div class="slicewp-card-header">
						<?php echo __( 'Visits', 'slicewp' ); ?>
					</div>

					<div class="slicewp-card-inner">
						<?php

							$args_visits = array(
								'affiliate_id'	=> $affiliate_id,
								'date_min'		=> get_gmt_from_date( $date_min->format( 'Y-m-d H:i:s' ) ),
								'date_max'		=> get_gmt_from_date( $date_max->format( 'Y-m-d H:i:s' ) ),
							);

							$visits = slicewp_get_visits( $args_visits, true );

							echo $visits;

						?>
					</div>

					<div class="slicewp-card-footer">
						<?php echo __( 'Last 30 Days', 'slicewp' ); ?>
					</div>
					
				</div><!-- / Visits Last 30 Days -->

			</div>

			<div class="slicewp-col-1-2">

				<!--  Visits All Time -->
				<div class="slicewp-card slicewp-card-affiliate-dashboard">

					<div class="slicewp-card-header">
						<?php echo __( 'Visits', 'slicewp' ); ?>
					</div>

					<div class="slicewp-card-inner">
						<?php

							$args_visits = array(
								'affiliate_id'	=> $affiliate_id,
							);

							$visits = slicewp_get_visits( $args_visits, true );

							echo $visits;

						?>
					</div>

					<div class="slicewp-card-footer">
						<?php echo __( 'All Time', 'slicewp' ); ?>
					</div>

				</div><!-- / Visits All Time -->

			</div>

		</div>
	</div><!-- / Tab: Dashboard -->

	<!-- Tab: Commissions -->
	<div class="slicewp-tab <?php echo ( $active_tab == 'commissions' ? 'slicewp-active' : '' ); ?>" data-slicewp-tab="commissions">
		<?php

			//Verify if a Payment ID is provided
			if ( ! empty( $_GET['payment_id'] ) ) {

				$payment_id = esc_attr( $_GET['payment_id'] );

				//Read the Payment
				$payment = slicewp_get_payment( $payment_id );

				//Get the Commissions IDs from the Payment
				if ( ! empty( $payment ) && $payment->get('affiliate_id') == $affiliate_id ) {

					$commission_ids = $payment->get('commission_ids');
					$commission_ids = array_map( 'trim', explode( ',', $payment->get('commission_ids') ) );

					$redirect_url = remove_query_arg( array( 'payment_id', 'page_commissions' ) );
					$redirect_url = add_query_arg( 'affiliate-account-tab', 'commissions', $redirect_url );
					echo sprintf( __( 'Showing all the commissions from Payout #%d.<br><a href="%s">View all commissions.</a><br><br>', 'slicewp' ), $payment_id, $redirect_url );

				}

			}

			//Prepare the commission args
			$commission_args = array(
				'number'		=> 30,
				'offset'		=> ( $page_commissions - 1 ) * 30,
				'include'		=> ( ! empty ( $commission_ids ) ? $commission_ids : '' ),
				'affiliate_id'	=> $affiliate_id,
				'status'        => 'unpaid'
			);

			//Read the commissions and show them to the user
			$commission_count = slicewp_get_commissions( $commission_args, true );
			$commissions = slicewp_get_commissions( $commission_args );
		?>

		<table>
			<tr>
				<th><?php echo __( 'ID', 'slicewp' ); ?></th>
				<th><?php echo __( 'Date', 'slicewp' ); ?></th>
				<th><?php echo __( 'Type', 'slicewp' ); ?></th>
				<th><?php echo __( 'Amount', 'slicewp' ); ?></th>
				<th><?php echo __( 'Status', 'slicewp' ); ?></th>
			<tr>
			
			<?php if ( empty( $commissions ) ): ?>				
				<tr>
					<td colspan="6"><?php echo __( 'You have no commissions.' , 'slicewp' ) ?></td>
				</tr>
			<?php else: ?>

				<?php foreach ( $commissions as $commission ) : ?>
					<tr>
						<td><?php echo $commission->get('id'); ?></td>
						<td><?php echo slicewp_date_i18n( $commission->get('date_created') ); ?></td>
						<td><?php echo $commission->get('type'); ?></td>
						<td><?php echo slicewp_format_amount( $commission->get('amount'), slicewp_get_setting( 'active_currency', 'USD' ) ); ?></td>
						<td><?php echo $commission->get('status'); ?></td>
					</tr>
				<?php endforeach; ?>

			<?php endif; ?>

		</table>
	
		<?php

			//Prepare the pagination of the table
			$commissions_paginate_args = array(
				'base'		=> '?affiliate-account-tab=commissions%_%',
				'format'	=> '&page_commissions=%#%',
				'total'		=> ceil( $commission_count / 30 ),
				'current'	=> $page_commissions,
				'prev_next'	=> false
			);

			echo paginate_links( $commissions_paginate_args );
		
		?>

	</div><!-- / Tab: Commissions -->
	
	<!-- Tab: Visits -->
	<div class="slicewp-tab <?php echo ( $active_tab == 'visits' ? 'slicewp-active' : '' ); ?>" data-slicewp-tab="visits">

		<?php
	
			$visit_args = array(
				'number'		=> 30,
				'offset'		=> ( $page_visits - 1 ) * 30,
				'affiliate_id'	=> $affiliate_id
			);

			$visits_count = slicewp_get_visits( array( 'affiliate_id' => $affiliate_id ), true );
			$visits = slicewp_get_visits( $visit_args );
		
		?>
		
		<table>
			<tr>
				<th><?php echo __( 'ID', 'slicewp' ); ?></th>
				<th><?php echo __( 'Date', 'slicewp' ); ?></th>
				<th><?php echo __( 'Landing URL', 'slicewp' ); ?></th>
				<th><?php echo __( 'Referrer URL', 'slicewp' ); ?></th>
			<tr>
			
			<?php if ( empty( $visits ) ): ?>				
				<tr>
					<td colspan="4"><?php echo __( 'You have no visits.' , 'slicewp' ); ?></td>
				</tr>
			<?php else: ?>

				<?php foreach ( $visits as $visit ) : ?>
					<tr>
						<td><?php echo $visit->get('id'); ?></td>
						<td><?php echo slicewp_date_i18n( $visit->get('date_created') ); ?></td>
						<td><?php echo $visit->get('landing_url'); ?></td>
						<td><?php echo $visit->get('referrer_url'); ?></td>
					</tr>
				<?php endforeach; ?>

			<?php endif; ?>

		</table>
		
		<?php

			//Prepare the pagination of the table
			$visits_paginate_args = array(
				'base'		=> '?affiliate-account-tab=visits%_%',
				'format'	=> '&page_visits=%#%',
				'total'		=> ceil( $visits_count / 30 ),
				'current'	=> $page_visits,
				'prev_next'	=> false
			);

			echo paginate_links( $visits_paginate_args );
		
		?>
		
	</div><!-- / Tab: Visits -->
	
	<!-- Tab: Creatives -->
	<div class="slicewp-tab <?php echo ( $active_tab == 'creatives' ? 'slicewp-active' : '' ); ?>" data-slicewp-tab="creatives">

		<?php
	
			$creative_args = array(
				'number'	=> 10,
				'offset'	=> ( $page_creatives - 1 ) * 10,
				'status'	=> 'active'
			);

			$creatives_count = slicewp_get_creatives( $creative_args, true );
			$creatives = slicewp_get_creatives( $creative_args );
		
		?>

		<?php if ( empty( $creatives ) ): ?>

			<p><?php echo __( "There aren't any creatives available." , 'slicewp' ); ?></p>
		
		<?php else: ?>

			<?php foreach ( $creatives as $creative ): ?>

				<div class="slicewp-creative-wrapper slicewp-creative-wrapper-<?php echo absint( $creative->get('id') ); ?> slicewp-creative-wrapper-type-<?php echo esc_attr( str_replace( '_', '-', $creative->get('type') ) ); ?> slicewp-creative-affiliate-wrapper">
					
					<div class="slicewp-creative-description">
						<?php echo wpautop( $creative->get('description') ); ?>
					</div>

					<?php if ( $creative->get('type') == 'image' ): ?>

						<img src="<?php echo esc_url( $creative->get('image_url') ); ?>" alt="<?php echo esc_attr( $creative->get('alt_text') ); ?>" />
						<textarea class="slicewp-creative-affiliate-textarea" readonly><a href="<?php echo esc_url( slicewp_get_affiliate_url( $affiliate_id, $creative->get('landing_url') ) ); ?>"><img src="<?php echo esc_url( $creative->get('image_url') ); ?>" alt="<?php echo esc_attr( $creative->get('alt_text') ); ?>" /></a></textarea>

					<?php elseif ( $creative->get('type') == 'text' ):?>

						<a href="#"><?php echo( $creative->get('text') ); ?></a>
						<textarea class="slicewp-creative-affiliate-textarea" readonly><a href="<?php echo esc_url( slicewp_get_affiliate_url( $affiliate_id, $creative->get('landing_url') ) ); ?>"><?php echo esc_textarea( $creative->get('text') ); ?></a></textarea>

					<?php elseif ( $creative->get('type') == 'long_text' ):?>

						<textarea class="slicewp-creative-affiliate-textarea" readonly><?php echo esc_textarea( $creative->get('text') ); ?></textarea>

					<?php endif; ?>

					<input type="submit" class="slicewp-input-copy" value="<?php echo __( 'Copy', 'slicewp' ); ?>" />
					<hr>
				</div>

			<?php endforeach; ?>

		<?php endif; ?>

		<?php

			$creatives_paginate_args = array(
				'base'		=> '?affiliate-account-tab=creatives%_%',
				'format'	=> '&page_creatives=%#%',
				'total'		=> ceil( $creatives_count / 10 ),
				'current'	=> $page_creatives,
				'prev_next'	=> false
			);

			echo paginate_links( $creatives_paginate_args );

		?>
		
	</div><!-- / Tab: Creatives -->

	<!-- Tab: Payments -->
	<div class="slicewp-tab <?php echo ( $active_tab == 'payments' ? 'slicewp-active' : '' ); ?>" data-slicewp-tab="payments">

		<?php
		
			$payments_args = array(
				'number'		=> 10,
				'offset'		=> ( $page_payments - 1 ) * 10,
				'affiliate_id'	=> $affiliate_id
			);

			$payments_count = slicewp_get_payments( array( 'affiliate_id' => $affiliate_id ), true );
			$payments = slicewp_get_payments( $payments_args );

		?>
		
		<table>
			<tr>
				<th><?php echo __( 'ID', 'slicewp' ); ?></th>
				<th><?php echo __( 'Date', 'slicewp' ); ?></th>
				<th><?php echo __( 'Amount', 'slicewp' ); ?></th>
				<th><?php echo __( 'Status', 'slicewp' ); ?></th>
				<th><?php echo __( 'Action', 'slicewp' ); ?></th>
			<tr>
			
			<?php if ( empty( $payments ) ): ?>				
				<tr>
					<td colspan="5"><?php echo __( 'You have no payouts.' , 'slicewp' ) ?></td>
				</tr>
			<?php else: ?>

				<?php foreach ( $payments as $payment ) : ?>

					<tr>
						<td><?php echo $payment->get('id'); ?></td>
						<td><?php echo slicewp_date_i18n( $payment->get('date_created') ); ?></td>
						<td><?php echo slicewp_format_amount( $payment->get('amount'), slicewp_get_setting( 'active_currency', 'USD' ) ); ?></td>
						<td><?php echo $payment->get('status'); ?></td>
						<td><?php 
						
						$redirect_url = remove_query_arg( array( 'affiliate-account-tab', 'page_commissions' ) );
						$redirect_url = add_query_arg( array( 'affiliate-account-tab' => 'commissions', 'payment_id' => $payment->get('id') ), $redirect_url );
		
						echo sprintf( __( '<a href="%s">view</a>', 'slicewp' ), $redirect_url ) ?></td>
					</tr>
					
				<?php endforeach; ?>

			<?php endif; ?>

		</table>
	
		<?php

			//Prepare the pagination of the table
			$payments_paginate_args = array(
				'base'		=> '?tab=payments%_%',
				'format'	=> '&page_payments=%#%',
				'total'		=> ceil( $payments_count / 10 ),
				'current'	=> $page_payments,
				'prev_next'	=> false
			);

			echo paginate_links( $payments_paginate_args );

		?>

	</div><!-- / Tab: Payments -->

	<!-- Tab: Settings -->
	<div class="slicewp-tab <?php echo ( $active_tab == 'settings' ? 'slicewp-active' : '' ); ?>" data-slicewp-tab="settings">

		<form action="" method="POST">

			<!-- Notices -->
			<?php do_action( 'slicewp_user_notices' ); ?>
			
			<!-- Postbox -->
			<div class="slicewp-card">

				<!-- Form Fields -->
				<div class="slicewp-card-inner">

					<!-- Payment Email -->
					<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

						<div class="slicewp-field-label-wrapper">
							<label for="slicewp-affiliate-payment-email"><?php echo __( 'Payment Email', 'slicewp' ); ?> <?php echo ( slicewp_get_setting( 'required_field_payment_email' ) ? '<span class="slicewp-field-required-marker">*</span>' : '' ); ?></label>
						</div>

						<input id="slicewp-affiliate-payment-email" name="payment_email" type="email" value="<?php echo ! empty( $_POST['payment_email'] ) ? $_POST['payment_email'] : esc_attr( $affiliate_payment_email ); ?>" />
					
					</div>	
				
					<!-- Website -->
					<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

						<div class="slicewp-field-label-wrapper">
							<label for="slicewp-affiliate-website"><?php echo __( 'Website', 'slicewp' ); ?> <?php echo ( slicewp_get_setting( 'required_field_website' ) ? '<span class="slicewp-field-required-marker">*</span>' : '' ); ?></label>
						</div>

						<input id="slicewp-affiliate-website" name="website" type="url" value="<?php echo ! empty( $_POST['website'] ) ? $_POST['website'] : esc_attr( $affiliate_website ); ?>" />
					
					</div>

				</div>

			</div>

			<!-- Action and nonce -->
			<input type="hidden" name="slicewp_action" value="update_affiliate_settings" />
			<?php wp_nonce_field( 'slicewp_update_affiliate_settings', 'slicewp_token', false ); ?>

			<!-- Submit -->
			<input type="submit" value="<?php echo __( 'Save', 'slicewp' ); ?>" />

		</form>

	</div><!-- / Tab: Settings -->


	<!-- Tab: Affiliate Links -->
	<div class="slicewp-tab <?php echo ( $active_tab == 'affiliate_links' ? 'slicewp-active' : '' ); ?>" data-slicewp-tab="affiliate_links">

		<!-- Postbox -->
		<div class="slicewp-section-general-affiliate-link slicewp-card">

			<!-- Form Fields -->
			<div class="slicewp-card-inner">

				<div class="slicewp-field-label-wrapper">
					<label for="slicewp-affiliate-link"><?php echo __( 'Your Affiliate Link', 'slicewp' ); ?></label>
				</div>

				<input id="slicewp-affiliate-link" name="affiliate_link" type="text" readonly value="<?php echo esc_url( slicewp_get_affiliate_url( $affiliate_id ) ); ?>" />

				<input type="submit" class="slicewp-input-copy" value="<?php echo __( 'Copy', 'slicewp' ); ?>" />

			</div>

		</div>

		<!-- Postbox -->
		<div class="slicewp-section-affiliate-link-generator slicewp-card">

			<!-- Form Fields -->
			<div class="slicewp-card-inner">

				<div class="slicewp-affiliate-custom-link-input">

					<div class="slicewp-user-notice slicewp-error" id="slicewp-affiliate-custom-link-input-empty" style="display:none"><?php echo __( 'Please provide a link!', 'slicewp' );?></div>
					<div class="slicewp-user-notice slicewp-error" id="slicewp-affiliate-custom-link-input-invalid-url" style="display:none"><?php echo __( 'The provided link is not valid!', 'slicewp' );?></div>

					<div class="slicewp-field-label-wrapper">
						<label for="slicewp-affiliate-custom-link-input"><?php echo __( 'Generate Affiliate Link', 'slicewp' ); ?></label>
					</div>

					<input id="slicewp-affiliate-custom-link-input" name="affiliate_link_input" type="text" placeholder="<?php echo __( 'Paste the link here', 'slicewp' ); ?>" />
				
					<input type="submit" class="slicewp-generate-affiliate-link" value="<?php echo __( 'Generate', 'slicewp' ); ?>" />

				
				</div>

				<div class="slicewp-affiliate-custom-link-output" style="display:none">

					<input id="slicewp-affiliate-custom-link-output" name="affiliate_link_output" type="text" />

					<input type="submit" class="slicewp-input-copy" value="<?php echo __( 'Copy', 'slicewp' ); ?>" />
				
				</div>

			</div>
		
		</div>

	</div><!-- / Tab: Affiliate Links -->	

</div>