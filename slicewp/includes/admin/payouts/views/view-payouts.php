<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="wrap slicewp-wrap slicewp-wrap-payouts">

	<form method="GET">

		<!-- Page Heading -->
		<h1 class="wp-heading-inline"><?php echo __( 'Payouts', 'slicewp' ); ?></h1>
		<a href="<?php echo add_query_arg( array( 'subpage' => 'create-payout' ), $this->admin_url ); ?>" class="page-title-action"><?php echo __( 'Create Payout', 'slicewp' ); ?></a>
		<a href="<?php echo add_query_arg( array( 'subpage' => 'view-payments' ), $this->admin_url ); ?>" class="page-title-action"><?php echo __( 'All Payments', 'slicewp' ); ?></a>
		<hr class="wp-header-end" />
		
		<!-- Payouts List Table -->
		<?php 
			$table = new SliceWP_WP_List_Table_Payouts();
			$table->views();
			$table->display();
		?>

		<!-- Hidden fields needed for the search query -->
		<input type="hidden" name="page" value="slicewp-payouts">

	</form>

	<?php 

		/**
		 * Hook to add extra cards if needed
		 *
		 */
		do_action( 'slicewp_view_payouts_bottom' );

	?>

</div>