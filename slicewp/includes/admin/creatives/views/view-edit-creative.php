<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$creative_id = ( ! empty( $_GET['creative_id'] ) ? sanitize_text_field( $_GET['creative_id'] ) : 0 );

if( empty( $creative_id ) )
	return;

$creative = slicewp_get_creative( $creative_id );

if( is_null( $creative ) )
	return;

?>

<div class="wrap slicewp-wrap slicewp-wrap-edit-creative">

	<form action="" method="POST">

		<!-- Page Heading -->
		<h1 class="wp-heading-inline"><?php echo __( 'Edit Creative', 'slicewp' ); ?></h1>
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

						<!-- Creative ID -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-creative-creative-id"><?php echo __( 'Creative ID', 'slicewp' ); ?></label>
							</div>
							
							<input id="slicewp-creative-creative-id" name="creative_id" disabled type="text" value="<?php echo esc_attr( $creative->get('id') ); ?>" />

						</div>

						<!-- Creative Name -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-creative-name"><?php echo __( 'Name', 'slicewp' ); ?> *</label>
								<?php echo slicewp_output_tooltip( __( 'The name will help you better identify the creative.', 'slicewp' ) ); ?>
							</div>
							
							<input id="slicewp-creative-name" name="name" type="text" value="<?php echo ( ! empty( $_POST['name'] ) ? esc_attr( $_POST['name'] ) : $creative->get('name') ); ?>" />

						</div>

						<!-- Creative Description -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-creative-description"><?php echo __( 'Description', 'slicewp' ); ?></label>
								<?php echo slicewp_output_tooltip( __( 'The description will help your affiliates better understand the creative.', 'slicewp' ) ); ?>
							</div>
							
							<textarea id="slicewp-creative-description" name="description"><?php echo ( ! empty( $_POST['description'] ) ? esc_attr( $_POST['description'] ) : $creative->get('description') ); ?></textarea>

						</div>
						
						<!-- Creative Type -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-creative-type"><?php echo __( 'Type', 'slicewp' ); ?></label>
							</div>
							
							<select id="slicewp-creative-type" name="type" class="slicewp-select2">

								<?php 
									foreach( slicewp_get_creative_available_types() as $type_slug => $type_name ) {
										echo '<option value="' . esc_attr( $type_slug ) . '"' . ($type_slug == $creative->get('type') ? 'selected' : '') . '>' . $type_name . '</option>';
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
							
							<input id="slicewp-creative-image" name="image_url" type="text" value="<?php echo ( ! empty( $_POST['image_url'] ) ? esc_attr( $_POST['image_url'] ) : $creative->get('image_url') ); ?>" />
							<input class="slicewp-button-secondary slicewp-image-select" type="button" value="<?php echo (__( 'Browse', 'slicewp' ) ); ?>" />

						</div>

						<!-- Creative Image Alt Text -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-creative-alt-text"><?php echo __( 'Alternative Text', 'slicewp' ); ?></label>
								<?php echo slicewp_output_tooltip( __( 'If the image can\'t be displayed for a reason, this text will be shown instead.', 'slicewp' ) ); ?>
							</div>
							
							<input id="slicewp-creative-alt-text" name="alt_text" type="text" value="<?php echo ( ! empty( $_POST['alt_text'] ) ? esc_attr( $_POST['alt_text'] ) : $creative->get('alt_text') ); ?>" />

						</div>

						<!-- Creative Text -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline" style="display:none;">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-creative-text"><?php echo __( 'Text', 'slicewp' ); ?> *</label>
								<?php echo slicewp_output_tooltip( __( 'This text will be your creative', 'slicewp' ) ); ?>
							</div>
							
							<input id="slicewp-creative-text" name="text" type="text" value="<?php echo ( ! empty( $_POST['text'] ) ? esc_attr( $_POST['text'] ) : $creative->get('text') ); ?>" />
							<textarea><?php echo esc_textarea( ! empty( $_POST['text'] ) ? $_POST['text'] : $creative->get('text') ); ?></textarea>

						</div>

						<!-- Creative Landing URL -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-creative-landing-url"><?php echo __( 'Landing URL', 'slicewp' ); ?></label>
								<?php echo slicewp_output_tooltip( __( 'Your creative will lead to this URL. If empty your domain URL will be used.', 'slicewp' ) ); ?>
							</div>
							
							<input id="slicewp-creative-landing-url" name="landing_url" type="text" value="<?php echo ( ! empty( $_POST['landing_url'] ) ? esc_attr( $_POST['landing_url'] ) : $creative->get('landing_url') ); ?>" />

						</div>

						<!-- Creative Status -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-creative-status"><?php echo __( 'Status', 'slicewp' ); ?></label>
								<?php echo slicewp_output_tooltip( __( 'If set to \'Active\', your affiliates will see the creative in their account.', 'slicewp' ) ); ?>
							</div>
							
							<select id="slicewp-creative-status" name="status" class="slicewp-select2">

								<?php 
									foreach( slicewp_get_creative_available_statuses() as $status_slug => $status_name ) {
										echo '<option value="' . esc_attr( $status_slug ) . '" ' . selected( $creative->get('status'), $status_slug, false ) . '>' . $status_name . '</option>';
									} 
								?>

							</select>

						</div>

						<!-- Creative Shortcode -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-last">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-creative-creative-shortcode"><?php echo __( 'Creative Shortcode', 'slicewp' ); ?></label>
							</div>
							
							<input id="slicewp-creative-creative-shortcode" disabled type="text" value="<?php echo esc_attr( '[slicewp_creative id="' . $creative->get('id') . '"]' ); ?>" />

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
					do_action( 'slicewp_view_creatives_edit_creative_secondary' );

				?>

			</div><!-- / Sidebar Content -->

		</div>

		<!-- Hidden creative id field -->
		<input type="hidden" name="creative_id" value="<?php echo $creative_id; ?>" />

		<!-- Action and nonce -->
		<input type="hidden" name="slicewp_action" value="update_creative" />
		<?php wp_nonce_field( 'slicewp_update_creative', 'slicewp_token', false ); ?>

		<!-- Submit -->
		<div id="slicewp-content-actions">
			
			<input type="submit" class="slicewp-form-submit slicewp-button-primary" value="<?php echo __( 'Update Creative', 'slicewp' ); ?>" />

			<span class="slicewp-trash"><a onclick="return confirm( '<?php echo __( "Are you sure you want to delete this creative?", "slicewp" ); ?>' )" href="<?php echo wp_nonce_url( add_query_arg( array( 'page' => 'slicewp-creatives', 'slicewp_action' => 'delete_creative', 'creative_id' => $creative->get('id') ), admin_url( 'admin.php' ) ), 'slicewp_delete_creative', 'slicewp_token' ); ?>"><?php echo __( 'Delete creative', 'slicewp' ) ?></a></span>

		</div>

	</form>

</div>