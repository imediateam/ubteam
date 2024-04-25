<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="wrap slicewp-wrap slicewp-wrap-commissions">

	<form method="GET">

		<!-- Page Heading -->
		<h1 class="wp-heading-inline"><?php echo __( 'Commissions', 'slicewp' ); ?></h1>
		<a href="<?php echo add_query_arg( array( 'subpage' => 'add-commission' ), $this->admin_url ); ?>" class="page-title-action"><?php echo __( 'Add New Commission', 'slicewp' ); ?></a>
		<hr class="wp-header-end" />

		<!-- Commissions List Table -->
		<?php 
			$table = new SliceWP_WP_List_Table_Commissions();
			$table->views();
			$table->search_box( __( 'Search Commissions', 'slicewp' ), 'commission_search' );
			$table->display();
		?>

		<!-- Hidden fields needed for the search query -->
		<input type="hidden" name="page" value="slicewp-commissions">

	</form>

	<?php 

		/**
		 * Hook to add extra cards if needed
		 *
		 */
		do_action( 'slicewp_view_commissions_bottom' );

	?>

</div>