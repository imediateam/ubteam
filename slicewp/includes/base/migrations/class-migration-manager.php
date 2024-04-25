<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Handles settings migrations from one version to another
 *
 */
class SliceWP_Migration_Manager {

	/**
	 * List of migration files
	 *
	 * @access public
	 * @var    array
	 *
	 */
	public static $migrations = array();

	/**
	 * Migrations that have already been ran before
	 *
	 * @access private
	 * @var    array
	 *
	 */
	private static $ran_migrations = array();


	/**
	 * Constructor
	 *
	 */
	public function __construct() {

		self::$ran_migrations = self::get_ran_migrations();

		self::$migrations = array(
			'class-migration-1-0-16.php' => 'SliceWP_Migration_1_0_16',
			'class-migration-1-0-17.php' => 'SliceWP_Migration_1_0_17'
		);

		add_action( 'plugins_loaded', array( $this, 'include_migrations' ), 50 );

	}


	/**
	 * Includes the actual migration files
	 *
	 */
	public function include_migrations() {

		if( ! current_user_can( 'manage_options' ) )
			return;

		foreach ( self::$migrations as $file => $class ) {
			include_once $file;
			$migration = new $class();
		}

	}


	/**
	 * Get the list of migrations that already have been done
	 *
	 * @return array
	 *
	 */
	public static function get_ran_migrations() {

		if ( empty( self::$ran_migrations ) )
			self::$ran_migrations = get_option( 'slicewp_migrations', array() );

		return self::$ran_migrations;

	}


	/**
	 * Update a migration status in the DB.
	 *
	 * @param string $migration_id
	 * @param string $status
	 *
	 */
	public static function update( $migration_id, $status = 'migrated' ) {

		$migrations                  = self::get_ran_migrations();
		$migrations[ $migration_id ] = array(
			'status' => sanitize_title( $status ),
			'time'   => time(),
		);

		update_option( 'slicewp_migrations', $migrations );

		self::$migrations = $migrations;

	}

}