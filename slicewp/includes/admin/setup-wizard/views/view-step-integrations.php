<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="slicewp-card">

	<div class="slicewp-card-inner">

		<div class="slicewp-setup-integrations">

			<h2><?php echo __( "Welcome to the setup wizard! Ready to get your affiliate program up and running?", 'slicewp' ); ?></h2>

			<p><?php echo __( "First things first, please select the eCommerce plugin that powers your business.", 'slicewp' ); ?></p>

			<p><?php echo __( "SliceWP will integrate seamlessly with any of these options, to track visits and generate commissions for orders referred by your affiliates.", 'slicewp' ); ?></p>

			<br />

			<div class="row">
				<?php $index = 1; ?>
				<?php foreach( slicewp()->integrations as $integration_slug => $integration ): ?>

					<div>
						<input id="slicewp-integration-<?php echo esc_attr( $integration_slug ); ?>" type="checkbox" value="<?php echo esc_attr( $integration_slug ); ?>" name="integrations[]" />
						<label for="slicewp-integration-<?php echo esc_attr( $integration_slug ); ?>">
							<span class="dashicons dashicons-yes-alt"></span>
							<?php echo $integration->get( 'name' ); ?>
						</label>
					</div>

					<?php if( $index % 2 == 0 && $index != count( slicewp()->integrations ) ): ?>
				        </div><div class="row">
				    <?php endif; ?>

				    <?php $index++; ?>

				<?php endforeach; ?>
			</div>

		</div>

	</div>

	<div class="slicewp-card-footer">

		<div class="slicewp-submit-wrapper-setup-wizard">

			<input type="submit" class="slicewp-form-submit slicewp-button-primary" value="<?php echo __( 'Continue', 'slicewp' ); ?>" />

		</div>

	</div>

</div>