<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="wrap slicewp-wrap slicewp-wrap-add-creative">

	<form action="" method="POST">

		<!-- Page Heading -->
		<h1 class="wp-heading-inline"><?php echo __( 'Add a New Creative', 'slicewp' ); ?></h1>
		<hr class="wp-header-end" />

		<div id="slicewp-content-wrapper">
			
			<!-- Primary Content -->
			<div id="slicewp-primary">

				<!-- Postbox -->
				<div class="slicewp-card slicewp-first">

					<div class="slicewp-card-header">
						<?php echo __( 'Creative Details', 'slicewp' ); ?>
					</div>

					<!-- Form Fields -->
					<div class="slicewp-card-inner">

						<!-- Creative Name -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-creative-name"><?php echo __( 'Name', 'slicewp' ); ?> *</label>
								<?php echo slicewp_output_tooltip( __( 'The name will help you better identify the creative.', 'slicewp' ) ); ?>
							</div>
							
							<input id="slicewp-creative-name" name="name" type="text" value="<?php echo ( ! empty( $_POST['name'] ) ? esc_attr( $_POST['name'] ) : '' ); ?>" />
						</div>

						<!-- Creative Description -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-creative-description"><?php echo __( 'Description', 'slicewp' ); ?></label>
								<?php echo slicewp_output_tooltip( __( 'The description will help your affiliates better understand the creative.', 'slicewp' ) ); ?>
							</div>
							
							<textarea id="slicewp-creative-description" name="description"><?php echo ( ! empty( $_POST['description'] ) ? esc_attr( $_POST['description'] ) : '' ); ?></textarea>

						</div>

						<!-- Creative Type -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-creative-type"><?php echo __( 'Type', 'slicewp' ); ?></label>
							</div>
							
							<select id="slicewp-creative-type" name="type" class="slicewp-select2">

								<?php 
									foreach( slicewp_get_creative_available_types() as $type_slug => $type_name ) {
										echo '<option value="' . esc_attr( $type_slug ) . '" ' . selected( $type_slug, $_POST['type'] ) . '>' . $type_name . '</option>';
									} 
								?>

							</select>

						</div>

						<!-- Creative Image URL -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-field-wrapper-creative-image">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-creative-image"><?php echo __( 'Image URL', 'slicewp' ); ?> *</label>
								<?php echo slicewp_output_tooltip( __( 'Select an image or fill with the URL of the creative\'s image.', 'slicewp' ) ); ?>
							</div>
							
							<input id="slicewp-creative-image" name="image_url" type="text" value="<?php echo ( ! empty( $_POST['image_url'] ) ? esc_attr( $_POST['image_url'] ) : '' ); ?>" />
							<input class="slicewp-button-secondary slicewp-image-select" type="button" value="<?php echo (__( 'Browse', 'slicewp' ) ); ?>" />

						</div>

						<!-- Creative Image Alt Text -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-creative-alt-text"><?php echo __( 'Alternative Text', 'slicewp' ); ?></label>
								<?php echo slicewp_output_tooltip( __( 'If the image can\'t be displayed for a reason, this text will be shown instead.', 'slicewp' ) ); ?>
							</div>
							
							<input id="slicewp-creative-alt-text" name="alt_text" type="text" value="<?php echo ( ! empty( $_POST['alt_text'] ) ? esc_attr( $_POST['alt_text'] ) : '' ); ?>" />

						</div>

						<!-- Creative Text -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline" style="display:none;">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-creative-text"><?php echo __( 'Text', 'slicewp' ); ?> *</label>
								<?php echo slicewp_output_tooltip( __( 'This text will be your creative', 'slicewp' ) ); ?>
							</div>
							
							<input id="slicewp-creative-text" name="text" type="text" value="<?php echo ( ! empty( $_POST['text'] ) ? esc_attr( $_POST['text'] ) : '' ); ?>" />
							<textarea><?php echo ( ! empty( $_POST['text'] ) ? esc_textarea( $_POST['text'] ) : '' ); ?></textarea>

						</div>

						<!-- Creative Landing URL -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-creative-landing-url"><?php echo __( 'Landing URL', 'slicewp' ); ?></label>
								<?php echo slicewp_output_tooltip( __( 'Your creative will lead to this URL. If empty your domain URL will be used.', 'slicewp' ) ); ?>
							</div>
							
							<input id="slicewp-creative-landing-url" name="landing_url" type="text" value="<?php echo ( ! empty( $_POST['landing_url'] ) ? esc_attr( $_POST['landing_url'] ) : '' ); ?>" />

						</div>

						<!-- Creative Status -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-last">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-creative-status"><?php echo __( 'Status', 'slicewp' ); ?></label>
								<?php echo slicewp_output_tooltip( __( 'If set to \'Active\', your affiliates will see the creative in their account.', 'slicewp' ) ); ?>
							</div>
							
							<select id="slicewp-creative-status" name="status" class="slicewp-select2">

								<?php 
									foreach( slicewp_get_creative_available_statuses() as $status_slug => $status_name ) {
										echo '<option value="' . esc_attr( $status_slug ) . '" ' . selected( $status_slug, $_POST['status'] ) . '>' . $status_name . '</option>';
									} 
								?>

							</select>

						</div>
						
					</div>

				</div>

			</div><!-- / Primary Content -->

			<!-- Sidebar Content -->
			<div id="slicewp-secondary">

				<?php 

					/**
					 * Hook to add extra cards if needed in the sidebar
					 *
					 */
					do_action( 'slicewp_view_creatives_add_creative_secondary' );

				?>

			</div><!-- / Sidebar Content -->

		</div>

		<!-- Action and nonce -->
		<input type="hidden" name="slicewp_action" value="add_creative" />
		<?php wp_nonce_field( 'slicewp_add_creative', 'slicewp_token', false ); ?>

		<!-- Submit -->
		<input type="submit" class="slicewp-form-submit slicewp-button-primary" value="<?php echo __( 'Add Creative', 'slicewp' ); ?>" />
		
	</form>

</div>