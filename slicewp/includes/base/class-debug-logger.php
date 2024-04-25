<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * The class that handles the logging of messages into the debug file
 *
 */
class SliceWP_Debug_Logger {

	/**
	 * Whether or not the logging option is enabled or not
	 *
	 * @access protected
	 * @var    bool
	 *
	 */
	protected $enable_logging;

	/**
	 * The path to the debug log file
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $filename;


	/**
	 * Constructor
	 *
	 */
	public function __construct() {

		// Check if logging is enabled
		$this->enable_logging = slicewp_get_setting( 'enable_logging', false );

		// Set the filename
		$upload_dir 	= wp_upload_dir( null, false );
		$this->filename = trailingslashit( $upload_dir['basedir'] ) . 'slicewp.log';

		// Create the log file if it doesn't exist
		if( $this->enable_logging && ! @file_exists( $this->filename ) ) {

			@file_put_contents( $this->filename, '' );
			@chmod( $this->filename, 0664 );

		}

	}


	/**
	 * Formats the given message and sends it to be writting in the file
	 *
	 * @param string $message
	 *
	 * @return bool
	 *
	 */
	public function add_log( $message ) {

		if( false === $this->enable_logging )
			return false;

		$message = get_date_from_gmt( slicewp_mysql_gmdate() ) . ' - ' . $message . "\r\n";

		return $this->write_to_file( $message );

	}


	/**
	 * Returns the contents of the debug file
	 *
	 * @return string
	 *
	 */
	public function get_file_contents() {

		$file_contents = '';

		if( @file_exists( $this->filename ) ) {

			$file_contents = @file_get_contents( $this->filename );

		}

		return $file_contents;
		
	}


	/**
	 * Writes the given message on a new line into the file
	 *
	 * @param string $message
	 *
	 */
	protected function write_to_file( $message ) {

		$file_contents  = $this->get_file_contents();
		$file_contents .= $message;

		return @file_put_contents( $this->filename, $file_contents );

	}

	/**
	 * Deletes the log file
	 *
	 * @return bool
	 *
	 */
	public function delete_file() {

		return @unlink( $this->filename );

	}

}