<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Class SliceWP_Migration_1_0_17
 *
 * Migration that runs when updating to version 1.0.17
 *
 */
class SliceWP_Migration_1_0_17 extends SliceWP_Abstract_Migration {

	/**
	 * Constructor
	 *
	 */
	public function __construct() {

		$this->id          = 'slicewp-update-1-0-17';
		$this->notice_type = 'none';

		if ( ! function_exists( 'get_plugins' ) ) {
		    require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$plugins = get_plugins();

		foreach( $plugins as $plugin_slug => $plugin_data ) {

			if( false !== strpos( $plugin_slug , 'slicewp-add-on-affiliate-user-role' ) ) {
				$this->notice_type = 'notice';
				break;
			}

		}

		parent::__construct();

	}


	/**
	 * Actually run the migration
	 *
	 */
	public function migrate() {

		// Get all affiliates
		$affiliates = slicewp_get_affiliates( array( 'number' => -1 ) );

		if( empty( $affiliates ) )
			return true;

		// Add user role to each affiliate
		foreach( $affiliates as $affiliate ) {

			$user = new WP_User( $affiliate->get( 'user_id' ) );

			$user->add_role( 'slicewp_affiliate' );

		}

		return true;

	}


	/**
	 * Get the full notice HTML used to output the admin notice.
	 * This function can be overridden in a migration class if needed.
	 *
	 */
	protected function get_notice() {

		$href         = wp_nonce_url( add_query_arg( array( 'slicewp_action' => 'migrate', 'slicewp_migration' => $this->id ) ), 'slicewp_migrate', 'slicewp_token' );
		$dismiss_href = wp_nonce_url( add_query_arg( array( 'slicewp_action' => 'migrate-dismiss', 'slicewp_migration' => $this->id ) ), 'slicewp_migrate_dismiss', 'slicewp_token' );
		
		?>

		<div class="notice notice-info">
			<h3 style="margin-top: 0.75em;"><?php echo __( 'SliceWP Important Notice', 'slicewp' ); ?></h3>
			<p><?php echo sprintf( __( 'The functionality of the %sAffiliate User Role add-on%s has been added to SliceWP core, making the add-on obsolete.', 'slicewp' ), '<strong>', '</strong>' ); ?></p>
			<p><?php echo sprintf( __( 'We recommend deactivating and deleting the add-on from the %sPlugins%s page as it will no longer be maintained.', 'slicewp' ), '<a href="' . admin_url( 'plugins.php' ) . '">', '</a>' ); ?></p>
			<p>
				<a href="<?php echo esc_url( $href ); ?>" class="slicewp-button-primary"><?php echo __( 'Thank you, I understand', 'slicewp' ); ?></a>
			</p>
		</div>

		<?php

	}

}