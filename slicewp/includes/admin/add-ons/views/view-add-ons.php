<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="wrap slicewp-wrap slicewp-wrap-add-ons">

	<!-- Page Heading -->
	<h1 class="wp-heading-inline"><?php echo __( 'Add-ons', 'slicewp' ); ?></h1>
	<hr class="wp-header-end" />

	<?php if( empty( $add_ons ) ): ?>

		<p><?php echo __( 'Something went wrong. Could not connect to the server to retrieve the add-ons. Please refresh the page to try again.', 'slicewp' ); ?></p>

	<?php else: ?>

		<?php if( ! slicewp_add_ons_exist() || true ): ?>
			<div class="slicewp-card slicewp-card-price-notice">
				<div class="slicewp-card-inner">
					<span><?php echo __( 'Upgrade to Pro and expand your affiliate program with these premium add-ons.', 'slicewp' ); ?></span>
					<a href="https://slicewp.com/?utm_source=plugin-free&amp;utm_medium=plugin-add-ons-page&amp;utm_campaign=SliceWPFree" target="_blank" class="slicewp-button-primary"><?php echo __( 'View Pricing', 'slicewp' ); ?></a>
				</div>
			</div>
		<?php endif; ?>

		<?php $current = 0; ?>
		<?php foreach( $add_ons as $add_on ): ?>

			<?php echo ( $current % 2 == 0 ? '<div class="slicewp-row">' : '' ); ?>

			<div class="slicewp-col-1-2">

				<div class="slicewp-card slicewp-card-add-on">

					<div class="slicewp-card-inner">
						<h4><?php echo esc_html( $add_on['name'] ); ?></h4>
						<p><?php echo esc_html( $add_on['description'] ); ?></p>
					</div>

					<div class="slicewp-card-footer">
						<a href="<?php echo esc_url( $add_on['url'] ) . '?utm_source=add-on-' . sanitize_title( $add_on['name'] ) . '&utm_medium=plugin-add-ons-page&utm_campaign=SliceWPFree'; ?>" target="_blank" class="slicewp-button-secondary"><?php echo __( 'Get this add-on', 'slicewp' ) ?></a>
					</div>

				</div>

			</div>

			<?php echo ( $current % 2 == 1 ? '</div>' : '' ); ?>

		<?php $current++; endforeach; ?>

	<?php endif; ?>

</div>