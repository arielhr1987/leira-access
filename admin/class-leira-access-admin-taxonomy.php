<?php

/**
 * The admin-specific functionality for taxonomy items.
 *
 * @since      1.0.0
 *
 * @package    Leira_Access
 * @subpackage Leira_Access/admin
 * @author     Ariel <arielhr1987@gmail.com>
 */
class Leira_Access_Admin_Taxonomy{

	/**
	 * Constructor.
	 */
	public function __construct() {

	}

	/**
	 * Init
	 */
	public function init() {
		//add_action( 'add_meta_boxes', array( $this, 'add_metabox' ) );
		//add_action( 'save_post', array( $this, 'update' ), 10, 2 );

		$taxonomies = $this->get_taxonomies();
		//$screen     = get_current_screen();

		foreach ( $taxonomies as $taxonomy ) {
			//custom column header
			$screen_id = 'edit-' . $taxonomy;
			add_filter( "manage_{$screen_id}_columns", array( $this, 'custom_column_header' ), 10 );

			//custom column content
			add_action( "manage_{$taxonomy}_custom_column", array(
				$this,
				'custom_column_content'
			), 10, 3 );

			//add quick edit fields
			add_action( 'quick_edit_custom_box', array( $this, 'quick_edit_custom_box' ), 10, 2 );

			//add fields to taxonomy edit screen
			add_action( "{$taxonomy}_edit_form_fields", array( $this, 'edit_form_fields' ) );

			add_action( "{$taxonomy}_add_form_fields", array( $this, 'add_form_fields' ) );

			add_action( "edited_{$taxonomy}", array( $this, 'edit' ), 10, 2 );

			add_action( "create_{$taxonomy}", array( $this, 'save' ), 10, 2 );
		}
	}

	/**
	 * Get the list of available taxonomies
	 *
	 * @return array
	 */
	public function get_taxonomies() {
		$taxonomies = get_taxonomies( array( 'public' => true, 'show_ui' => true ), 'names' );

		$exclude = apply_filters( 'leira-access_excluded_taxonomies', array() );

		$taxonomies = array_diff( $taxonomies, $exclude );

		return $taxonomies;
	}

	/**
	 * We hook the current_screen to add custom columns to taxonomies list table
	 */
	public function current_screen() {
		$taxonomies = $this->get_taxonomies();
		$screen     = get_current_screen();

		if ( ! empty( $screen->taxonomy ) && in_array( $screen->taxonomy, $taxonomies ) ) {

			//custom column header
			add_filter( "manage_{$screen->id}_columns", array( $this, 'custom_column_header' ), 10 );

			//custom column content
			add_action( "manage_{$screen->taxonomy}_custom_column", array(
				$this,
				'custom_column_content'
			), 10, 3 );

			//add quick edit fields
			add_action( 'quick_edit_custom_box', array( $this, 'quick_edit_custom_box' ), 10, 2 );

			//add fields to taxonomy edit screen
			add_action( "{$screen->taxonomy}_edit_form_fields", array( $this, 'edit_form_fields' ) );

			add_action( "{$screen->taxonomy}_add_form_fields", array( $this, 'add_form_fields' ) );

			add_action( "edited_{$screen->taxonomy}", array( $this, 'edit' ), 10, 2 );

			add_action( "create_{$screen->taxonomy}", array( $this, 'save' ), 10, 2 );

		}

	}

	/**
	 * Add column header to list table
	 *
	 * @param array $columns List of available columns
	 *
	 * @return array
	 */
	public function custom_column_header( $columns ) {
		$columns['leira-access'] = __( 'Access', 'leira-access' );

		return $columns;
	}

	/**
	 * Set content for columns in management page
	 *
	 * @param string $string      Blank string.
	 * @param string $column_name Name of the column.
	 * @param int    $term_id     Term ID.
	 */
	public function custom_column_content( $string, $column_name, $term_id ) {
		if ( 'leira-access' != $column_name ) {
			return;
		}

		$access = get_term_meta( $term_id, '_leira-access', true );
		$output = __( 'Everyone', 'leira-access' );

		if ( $access == 'out' ) {
			$output = __( 'Logged Out Users', 'leira-access' );
		} else {
			//is "in" or array of roles
			if ( $access == 'in' ) {
				$output = __( 'Logged In Users', 'leira-access' );
			} else if ( is_array( $access ) ) {
				$roles = '';
				if ( ! empty( $access ) ) {
					$roles .= '<ul>';
					foreach ( $access as $role ) {
						$roles .= sprintf( '<li>%s</li>', $role );
					}
					$roles .= '</ul>';
				}
				$output = sprintf( __( 'Logged In Users %s', 'leira-access' ), $roles );
			}
		}

		echo $output;
	}

	/**
	 * Add interface to quick edit form
	 *
	 * @param string $column_name
	 * @param string $post_type
	 */
	public function quick_edit_custom_box( $column_name, $post_type ) {
		if ( 'leira-access' != $column_name ) {
			return;
		}

		$id    = '__';
		$roles = array();
		?>
        <fieldset class="">
            <div class="inline-edit-col">
				<?php leira_access()->admin->form( $roles, $id ); ?>
            </div>
        </fieldset>
		<?php
	}

	/**
	 * Show form in edit term screen
	 *
	 * @param $tag
	 */
	public function edit_form_fields( $tag ) {
		$roles = get_term_meta( $tag->term_id, '_leira-access', true );

		?>
        <tr>
            <th scope="row">
                <label for=""><?php _e( 'Access', 'leira-access' ) ?></label>
            </th>
            <td>
				<?php leira_access()->admin->form( $roles, $tag->term_id, array(
					'show_label' => false
				) ) ?>
                <p class="description">
                    <!-- An optional description -->
                </p>
            </td>
        </tr>
		<?php
	}

	/**
	 * Show form in edit term screen
	 *
	 * @param $taxonomy
	 */
	public function add_form_fields( $taxonomy ) {

		?>
        <div class="form-field">
            <label for=""><?php _e( 'Access', 'leira-access' ) ?> </label>
			<?php leira_access()->admin->form( false, '', array(
				'show_label' => false
			) ) ?>
            <p class="">
                <!--An optional description-->
            </p>
        </div>
		<?php
	}

	/**
	 * Edit taxonomy
	 *
	 * @param $term_id
	 */
	public function edit( $term_id ) {
		leira_access()->admin->save( $term_id, 'term' );
	}

	/**
	 * Save new taxonomy
	 *
	 * @param $term_id
	 */
	public function save( $term_id ) {
		leira_access()->admin->save( $term_id, 'term', false );
	}

}
