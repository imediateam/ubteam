<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * The main class for the Note
 *
 */
class SliceWP_Note extends SliceWP_Base_Object {

	/**
	 * The Id of the booking
	 *
	 * @access protected
	 * @var    int
	 *
	 */
	protected $id;

	/**
	 * The type/context of the object the note is attached to
	 *
	 * @access protected
	 * @var    int
	 *
	 */
	protected $object_context;

	/**
	 * The id of the object the note is attached to
	 *
	 * @access protected
	 * @var    int
	 *
	 */
	protected $object_id;

	/**
	 * The id of the user to which this affiliate is assigned
	 *
	 * @access protected
	 * @var    int
	 *
	 */
	protected $user_id;

	/**
	 * The date when the affiliate was created
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $date_created;

	/**
	 * The message content of the note
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $note_content;
	
}