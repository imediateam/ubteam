<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="wrap slicewp-wrap slicewp-wrap-payments">

	<form method="GET">

		<!-- Page Heading -->
		<h1 class="wp-heading-inline"><?php echo __( 'Payments', 'slicewp' ); ?></h1>
		<a href="<?php echo $this->admin_url; ?>" class="page-title-action"><?php echo __( 'Back to Payouts', 'slicewp' ); ?></a>
		<hr class="wp-header-end" />

		<!-- Payments List Table -->
		<?php 
			$table = new SliceWP_WP_List_Table_Payments();
			$table->views();
			$table->display();
		?>

		<!-- Hidden fields needed for the search query -->
		<input type="hidden" name="page" value="slicewp-payouts">

	</form>

</div>