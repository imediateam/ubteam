<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Class that outputs the entire notes card
 *
 */
class SliceWP_Outputter_Notes {

	/**
	 * The type of object the notes are added to
	 *
	 * @access protected
	 * @var    int
	 *
	 */
	protected $object_context;

	/**
	 * The object the notes are added to
	 *
	 * @access protected
	 * @var    int
	 *
	 */
	protected $object_id;

	/**
	 * The current index when looping through notes
	 *
	 * @access protected
	 * @var    int
	 *
	 */
	protected $note_index;


	/**
	 * Constructor
	 *
	 * @param int $object_id
	 *
	 */
	public function __construct( $object_context, $object_id = 0 ) {

		$this->object_context = $object_context;
		$this->object_id 	  = $object_id;
		$this->note_index 	  = 1;

	}

	/**
	 * Outputs the entire Notes card
	 *
	 */
	public function output_card() {

		if( ! empty( $this->object_id ) )
			$notes = slicewp_get_notes( array( 'object_context' => $this->object_context, 'object_id' => $this->object_id ) );
		else
			$notes = array();

		echo '<div id="slicewp-notes-wrapper" class="slicewp-card">';

			// Card header
			echo '<div class="slicewp-card-header">';
				echo sprintf( __( 'Notes', 'slicewp' ) . ' <div class="slicewp-notes-count-wrapper">(<span class="slicewp-notes-count">%d</span>)</div>', count( $notes ) );
			echo '</div>' ;

			// No notes
			echo '<div class="slicewp-card-inner slicewp-notes-empty" ' . ( ! empty( $notes ) ? 'style="display: none;"' : '' ) . '>';
				echo __( 'There are no notes here.', 'slicewp' );
			echo '</div>';

			// Notes
			foreach( $notes as $note ) {

				$this->output_note( $note );

				$this->note_index++;

			}

			// Show all notes
			if( count( $notes ) > 3 ) {

				echo '<div class="slicewp-card-inner slicewp-notes-view-all">';
					echo '<a href="#">' . sprintf( __( 'View all %s notes', 'slicewp' ), '<span class="slicewp-notes-count">' . count( $notes ) . '</span>' ) . '</a>';
				echo '</div>';

			}

			// Card footer
			echo '<div class="slicewp-card-footer">';

				echo '<div class="slicewp-field-wrapper">';
					echo '<textarea id="slicewp-note-content" placeholder="' . __( 'Type a note here...', 'slicewp' ) . '"></textarea>';
				echo '</div>';

				echo '<a href="#" class="slicewp-button-secondary slicewp-add-note">' . __( 'Add note', 'slicewp' ) . '</a>';
				echo '<div class="spinner"></div>';

				// Token
				wp_nonce_field( 'slicewp_action_note', 'slicewp_token_notes', false );

				// Add context
				echo '<input type="hidden" name="note_object_context" value="' . esc_attr( $this->object_context ) . '" />';

				// Add object_id
				echo '<input type="hidden" name="note_object_id" value="' . esc_attr( $this->object_id ) . '" />';

				// Hidden field used to add the object_id to the notes when inserting a new object
				echo '<input type="hidden" name="note_ids" value="" />';

			echo '</div>';

		echo '</div>';

	}


	/**
	 * Outputs a single Note
	 *
	 */
	public function output_note( $note ) {

		$meta_avatar = ( ! empty( $note->get( 'user_id' ) ) ? get_avatar_url( $note->get( 'user_id' ), array( 'size' => 100 ) ) : SLICEWP_PLUGIN_DIR_URL . 'assets/img/slicewp-logo-icon-350x350.png' );
		$meta_author = ( ! empty( $note->get( 'user_id' ) ) ? get_the_author_meta( 'display_name', $note->get( 'user_id' ) ) : 'SliceWP' );

		echo '<div data-note-id="' . absint( $note->get( 'id' ) ) . '" class="slicewp-card-inner slicewp-note ' . ( $this->note_index == 1 ? 'slicewp-first' : '' ) . ' ' . ( $this->note_index > 3 ? 'slicewp-note-hidden' : '' ) . '" ' . ( wp_doing_ajax() ? 'style="display: none;"' : '' ) . '>';

			echo wpautop( $note->get( 'note_content' ) );

			echo '<div class="slicewp-note-meta">';

				echo '<img src="' . $meta_avatar . '" />';

				echo '<span class="slicewp-note-meta-author">' . $meta_author . '</span>';
				echo '<span class="slicewp-note-meta-separator">&bull;</span>';
				echo '<span class="slicewp-note-meta-date">' . slicewp_date_i18n( $note->get( 'date_created' ) ) . '</span>';

				echo '<a href="#" class="slicewp-note-delete" data-confirmation-message="' . __( 'Are you sure you want to delete this note?', 'slicewp' ) . '">' . __( 'Delete note', 'slicewp' ) . '</a>';

			echo '</div>';

		echo '</div>';

	}

}