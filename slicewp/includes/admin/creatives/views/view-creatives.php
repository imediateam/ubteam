<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="wrap slicewp-wrap slicewp-wrap-creatives">

	<form method="GET">

		<!-- Page Heading -->
		<h1 class="wp-heading-inline"><?php echo __( 'Creatives', 'slicewp' ); ?></h1>
		<a href="<?php echo add_query_arg( array( 'subpage' => 'add-creative' ), $this->admin_url ); ?>" class="page-title-action"><?php echo __( 'Add New Creative', 'slicewp' ); ?></a>
		<hr class="wp-header-end" />

		<!-- Creatives List Table -->
		<?php 
			$table = new SliceWP_WP_List_Table_Creatives();
			$table->views();
			$table->search_box( __( 'Search Creatives', 'slicewp' ), 'creative_search' );
			$table->display();
		?>

		<!-- Hidden fields needed for the search query -->
		<input type="hidden" name="page" value="slicewp-creatives">

	</form>

	<?php 

		/**
		 * Hook to add extra cards if needed
		 *
		 */
		do_action( 'slicewp_view_creatives_bottom' );

	?>

</div>