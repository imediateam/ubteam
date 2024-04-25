<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="wrap slicewp-wrap slicewp-wrap-visits">

	<form method="GET">

		<!-- Page Heading -->
		<h1 class="wp-heading-inline"><?php echo __( 'Visits', 'slicewp' ); ?></h1>
		<hr class="wp-header-end" />

		<!-- Visits List Table -->
		<?php 
			$table = new SliceWP_WP_List_Table_Visits();
			$table->views();
			$table->search_box( __( 'Search Visits', 'slicewp' ), 'visit_search' );
			$table->display();
		?>

		<!-- Hidden fields needed for the search query -->
		<input type="hidden" name="page" value="slicewp-visits">

	</form>

	<?php 

		/**
		 * Hook to add extra cards if needed
		 *
		 */
		do_action( 'slicewp_view_visits_bottom' );

	?>

</div>