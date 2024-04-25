<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$affiliate_id = slicewp_get_current_affiliate_id();

?>

<div class="slicewp-creative-wrapper slicewp-creative-wrapper-<?php echo absint( $creative->get('id') ); ?> slicewp-creative-shortcode-wrapper">
    <?php echo sprintf( __( 'Creative: %s', 'slicewp' ), $creative->get('description') ); ?><br>

    <?php if ( $creative->get('type') == 'image' ): ?>

        <img src="<?php echo( $creative->get('image_url') ); ?>" alt="<?php echo( $creative->get('alt_text') ); ?>">
        <textarea class="slicewp-creative-shortcode-textarea" readonly><a href="<?php echo esc_url( slicewp_get_affiliate_url( $affiliate_id, $creative->get('landing_url') ) ); ?>"><img src="<?php echo( $creative->get('image_url') ); ?>" alt="<?php echo( $creative->get('alt_text') ); ?>"></a></textarea><br>

    <?php elseif ( $creative->get('type') == 'text' ):?>

        <a href="#"><?php echo( $creative->get('text') ); ?></a>
        <textarea class="slicewp-creative-shortcode-textarea" readonly><a href="<?php echo esc_url( slicewp_get_affiliate_url( $affiliate_id, $creative->get('landing_url') ) ); ?>"><?php echo( $creative->get('text') ); ?></a></textarea><br>

    <?php endif; ?>

    <button class="slicewp-input-copy"><?php echo __( 'Copy', 'slicewp' ); ?></button>
</div>
