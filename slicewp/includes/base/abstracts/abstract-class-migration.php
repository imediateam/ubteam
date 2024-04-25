<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Abstract for migration classes
 *
 */
abstract class SliceWP_Abstract_Migration {

	/**
	 * Unique ID for this migration
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $id;

	/**
	 * Whether this migration is dismissible
	 *
	 * @access protected
	 * @var    bool
	 *
	 */
	protected $dismissible = false;

	/**
	 * The migration's type
	 *
	 * If set to 'notice' there will be a admin notice shown to the admin
	 *
	 * @var string
	 *
	 */
	protected $notice_type = 'none'; // 'notice', 'none'


	/**
	 * Constructor
	 *
	 */
	public function __construct() {

		$this->redirect = remove_query_arg( array( 'slicewp_action', 'slicewp_migration', 'slicewp_token' ) );

		if ( ! $this->has_run_before() )
			$this->init();

	}


	/**
	 * Initialize the components this migration needs to function
	 *
	 */
	public function init() {

		// Show a notice before updating
		if ( $this->notice_type == 'notice' ) {
			
			add_action( 'admin_notices', array( $this, 'admin_notice' ) );
			add_action( 'admin_init', array( $this, 'check_for_migrate_actions' ), 50 );

		// Automatically update in the background
		} elseif ( $this->notice_type == 'none' ) {

			$this->run_migration();

		}

	}


	/**
	 * Initialize the migration
	 *
	 * @return bool
	 *
	 */
	abstract public function migrate();


	/**
	 * Revert the migration
	 *
	 */
	public function revert() {}


	/**
	 * Run the migration
	 *
	 * @return bool
	 *
	 */
	public function run_migration() {

		if( ! current_user_can( 'manage_options' ) )
			return;

		$this->migrate();
		$this->mark_as_ran();

		// Redirect after the migration has been completed.
		if ( ! empty( $this->redirect ) ) {
			wp_redirect( $this->redirect );
			die;
		}

	}


	/**
	 * Check if this migration has been run before
	 *
	 * @return bool
	 *
	 */
	public function has_run_before() {

		return in_array( $this->id, array_keys( slicewp()->services['migration_manager']::get_ran_migrations() ) );

	}


	/**
	 * Update the migration to be marked as ran
	 *
	 */
	public function mark_as_ran() {

		slicewp()->services['migration_manager']::update( $this->id );

	}


	/**
	 * Check for any migration actions being taken
	 *
	 * @return bool
	 *
	 */
	public function check_for_migrate_actions() {

		// Bail if currently no migrations are being run
		if ( ! isset( $_GET['slicewp_action'] ) || ! in_array( $_GET['slicewp_action'], array( 'migrate', 'migrate-dismiss' ) ) )
			return false;

		// Bail if its not this migration being run
		if ( ! isset( $_GET['slicewp_migration'] ) || $_GET['slicewp_migration'] != $this->id )
			return false;

		if ( $_GET['slicewp_action'] == 'migrate-dismiss' ) {

			slicewp()->services['migration_manager']::update( $this->id, 'dismissed' );
			wp_redirect( remove_query_arg( array( 'slicewp_action', 'slicewp_migration', 'slicewp_token' ) ) );
			die;

		}

		// Run the migration
		return $this->run_migration();

	}


	/**
	 * Output the admin notice in the 'admin_notices' hook
	 *
	 */
	public function admin_notice() {

		$this->get_notice();

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

		<div class="notice notice-success">
			<p><?php echo wp_kses_post( $this->get_notice_text() ); ?></p>
			<p>
				<a href="<?php echo esc_url( $href ); ?>" class="slicewp-button-primary"><?php echo __( 'Migrate', 'slicewp' ); ?></a>
				<?php if ( $this->dismissible ): ?>
					
					&nbsp;&nbsp;<a href="<?php esc_url( $dismiss_href ); ?>" class="slicewp-button-secondary"><?php echo __( 'No, thank you', 'slicewp' ); ?></a>
					
				<?php endif; ?>
			</p>
		</div>

		<?php

	}


	/**
	 * Get the default notice text for when there is data to be migrated
	 *
	 * Migrations classes should overwrite this if needed
	 *
	 * @return string
	 *
	 */
	protected function get_notice_text() {

		return __( 'Thank you for updating! We need to migrate some data as there has been changes that require that.', 'slicewp' );

	}

}
