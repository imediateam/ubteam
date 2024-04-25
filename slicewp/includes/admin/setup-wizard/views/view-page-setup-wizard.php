<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>

	<head>
		<meta name="viewport" content="width=device-width" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title><?php esc_html_e( 'SliceWP &rsaquo; Setup Wizard', 'slicewp' ); ?></title>
		<?php wp_enqueue_style( 'colors' ); ?>
		<?php do_action( 'admin_enqueue_scripts' ); ?>
		<?php do_action( 'admin_print_styles' ); ?>
		<?php do_action( 'admin_head' ); ?>
		<?php do_action( 'admin_print_scripts' ); ?>
	</head>

	<body class="slicewp-pagestyles slicewp-setup">

		<div class="slicewp-wrap">
			
			<!-- Logo -->
			<h1 class="slicewp-setup-logo"><a href="https://slicewp.com/" target="_blank"><img src="<?php echo SLICEWP_PLUGIN_DIR_URL; ?>/assets/img/slicewp-logo.png" /></a></h1>

			<!-- Setup Steps -->
			<div class="slicewp-setup-steps-wrapper">

				<ul class="slicewp-setup-steps">
					<?php $index_step = 0; ?>
					<?php foreach( $this->steps as $step_slug => $step_name ): ?>


						<li class="<?php echo ( $index_step < array_search( $this->current_step, array_keys( $this->steps ) ) ? 'slicewp-done' : '' ); ?> <?php echo ( $step_slug == $this->current_step ? 'slicewp-current' : '' ) ?>">
							<span class="slicewp-setup-step-name">
								<?php if( $index_step < array_search( $this->current_step, array_keys( $this->steps ) ) ): ?>
									<a href="<?php echo add_query_arg( array( 'page' => 'slicewp-setup', 'current_step' => $step_slug ), admin_url( 'index.php' ) ); ?>"><?php echo $step_name; ?></a>
								<?php else: ?>
									<?php echo $step_name; ?>
								<?php endif; ?>
							</span>
							<span class="slicewp-setup-step-index"><?php echo $index_step + 1; ?></span>
						</li>

						<?php $index_step++; ?>
					<?php endforeach; ?>
				</ul>

			</div>

			<!-- Step Settings -->
			<form action="<?php echo ( $this->current_step == 'finished' ? 'https://slicewp.us19.list-manage.com/subscribe/post?u=506ed65ce0a7eec2aa1c7cc61&amp;id=5fe80d913e&amp;SIGNUP=plugin_wizard' : '' ); ?>" method="POST" <?php echo( $this->current_step == 'finished' ? 'target="_blank" novalidate' : '' ); ?>>

				<?php

					$dir_path = plugin_dir_path( __FILE__ );

					if( file_exists( $dir_path . 'view-step-' . $this->current_step . '.php' ) )
						include $dir_path . 'view-step-' . $this->current_step . '.php';
					
				?>

				<!-- Action and nonce -->
				<input type="hidden" name="slicewp_action" value="process_setup_wizard_step_<?php echo esc_attr( $this->current_step ); ?>" />
				<?php wp_nonce_field( 'slicewp_process_setup_wizard_step', 'slicewp_token', false ); ?>

				<!-- Next step -->
				<?php $steps_keys = array_keys( $this->steps ); ?>
				<?php if( isset( $steps_keys[$this->current_step_index + 1] ) ): ?>
					<input type="hidden" name="next_step" value="<?php echo esc_attr( $steps_keys[$this->current_step_index + 1] ); ?>" />
				<?php endif; ?>

			</form>

			<!-- Skip -->
			<div class="slicewp-setup-skip">

				<?php if( $this->current_step_index == 0 ): ?>
					<a href="<?php echo admin_url(); ?>"><?php echo __( 'Skip setup wizard', 'slicewp' ) ?></a>
				<?php elseif( $this->current_step_index < count( $this->steps ) - 1 ): ?>
					<a href="<?php echo add_query_arg( array( 'page' => 'slicewp-setup', 'current_step' => array_keys( $this->steps )[$this->current_step_index + 1] ), admin_url( 'index.php' ) ); ?>"><?php echo __( 'Skip step', 'slicewp' ) ?></a>
				<?php endif; ?>

			</div>

		</div>

	</body>

    <script type="text/javascript">
        var ajaxurl = "<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>";
    </script>

</html>