<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Includes the files needed for the Affiliate admin area
 *
 */
function slicewp_include_files_admin_affiliate() {

	// Get affiliate admin dir path
	$dir_path = plugin_dir_path( __FILE__ );

	// Include submenu page
	if( file_exists( $dir_path . 'class-submenu-page-affiliates.php' ) )
		include $dir_path . 'class-submenu-page-affiliates.php';

	// Include actions
	if( file_exists( $dir_path . 'functions-actions-affiliates.php' ) )
		include $dir_path . 'functions-actions-affiliates.php';

	// Include affiliates list table
	if( file_exists( $dir_path . 'class-list-table-affiliates.php' ) )
		include $dir_path . 'class-list-table-affiliates.php';

	// Include merge tags
	if( file_exists( $dir_path . 'class-merge-tags-affiliates.php' ) )
		include $dir_path . 'class-merge-tags-affiliates.php';

}
add_action( 'slicewp_include_files', 'slicewp_include_files_admin_affiliate' );


/**
 * Register the Affiliate admin submenu page
 *
 */
function slicewp_register_submenu_page_affiliates( $submenu_pages ) {

	if( ! is_array( $submenu_pages ) )
		return $submenu_pages;

	$submenu_pages['affiliates'] = array(
		'class_name' => 'SliceWP_Submenu_Page_Affiliates',
		'data' 		 => array(
			'page_title' => __( 'Affiliates', 'slicewp' ),
			'menu_title' => __( 'Affiliates', 'slicewp' ),
			'capability' => apply_filters( 'slicewp_submenu_page_capability_affiliates', 'manage_options' ),
			'menu_slug'  => 'slicewp-affiliates'
		)
	);

	return $submenu_pages;

}
add_filter( 'slicewp_register_submenu_page', 'slicewp_register_submenu_page_affiliates', 20 );


/**
 * Adds affiliate related fields to the add new user page
 *
 * @param string $type
 *
 */
function slicewp_user_new_form_add_affiliate( $type ) {

	if( $type != 'add-new-user' )
		return;

	// Get affiliate auto register option
	$affiliate_auto_register = slicewp_get_setting( 'affiliate_auto_register' );

	?>

		<table class="form-table" role="presentation">
			<tbody>
				<tr>
					<th scope="row"><?php echo __( 'Register User as Affiliate', 'slicewp' ); ?></th>
					<td>
						<input type="checkbox" name="slicewp_register_user_as_affiliate" id="slicewp_register_user_as_affiliate" value="1" <?php echo ( ! empty( $affiliate_auto_register ) ? 'checked' : '' ); ?> />
						<label for="slicewp_register_user_as_affiliate"><?php echo __( 'Registers the new user as a new affiliate into SliceWP.', 'slicewp' ); ?></label>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php echo __( 'Send Affiliate Welcome Email', 'slicewp' ); ?></th>
					<td>
						<input type="checkbox" name="slicewp_register_affiliate_welcome_email" id="slicewp_register_affiliate_welcome_email" value="1" <?php echo ( ! empty( $affiliate_auto_register ) ? 'checked' : '' ); ?> />
						<label for="slicewp_register_affiliate_welcome_email"><?php echo __( 'Send a welcome email to your new affiliate after registration.', 'slicewp' ); ?></label>
					</td>
				</tr>
			</tbody>
		</table>

	<?php

}
add_action( 'user_new_form', 'slicewp_user_new_form_add_affiliate' );


/**
 * Registers the new user as an affiliate when user is manually added from the admin screen
 *
 * @param int $user_id
 *
 */
function slicewp_user_register_new_affiliate( $user_id ) {

	// Make sure the action is done when creating a new user
	if( empty( $_POST['action'] ) || $_POST['action'] !== 'createuser' )
		return;

	// Make sure everything is happening on the admin side
	if( ! is_admin() )
		return;

	// Make sure only admins can add new affiliates
	if( ! current_user_can( 'manage_options' ) )
		return;

	// Bail if the user isn't set as affiliate
	if( empty( $_POST['slicewp_register_user_as_affiliate'] ) )
		return;

	// Prepare affiliate data to be inserted
	$affiliate_data = array(
		'user_id' 		=> absint( $user_id ),
		'website'		=> ( ! empty( $_POST['url'] ) ? sanitize_text_field( $_POST['url'] ) : '' ),
		'payment_email' => sanitize_email( $_POST['email'] ),
		'date_created'  => slicewp_mysql_gmdate(),
		'date_modified' => slicewp_mysql_gmdate(),
		'status'		=> 'active'
	);

	// Insert affiliate into the database
	$affiliate_id = slicewp_insert_affiliate( $affiliate_data );

}
add_action( 'user_register', 'slicewp_user_register_new_affiliate' );