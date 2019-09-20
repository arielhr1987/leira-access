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
	 * Get the list of available taxonomies
	 *
	 * @return array
	 * @access public
	 * @since  1.0.0
	 */
	public function get_taxonomies() {
		$taxonomies = get_taxonomies( array( 'public' => true, 'show_ui' => true ), 'names' );

		$exclude = apply_filters( 'leira_access_excluded_taxonomies', array() );

		$taxonomies = array_diff( $taxonomies, $exclude );

		return $taxonomies;
	}

	/**
	 * Add column header to taxonomy list table
	 *
	 * @param array $columns List of available columns
	 *
	 * @return array
	 * @access public
	 * @since  1.0.0
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
	 *
	 * @access public
	 * @since  1.0.0
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
	 * Add quick edit form to taxonomies list table
	 *
	 * @param string $column_name
	 * @param string $post_type
	 *
	 * @access public
	 * @since  1.0.0
	 */
	public function quick_edit_custom_box( $column_name, $post_type ) {
		$screen   = get_current_screen();
		$taxonomy = isset( $screen->taxonomy ) ? $screen->taxonomy : false;
		if ( 'leira-access' != $column_name || ! in_array( $taxonomy, $this->get_taxonomies() ) ) {
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
	 *
	 * @access public
	 * @since  1.0.0
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
	 *
	 * @access public
	 * @since  1.0.0
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
	 *
	 * @access public
	 * @since  1.0.0
	 */
	public function edit( $term_id ) {
		leira_access()->admin->save( $term_id, 'term' );
	}

	/**
	 * Save new taxonomy
	 *
	 * @param $term_id
	 *
	 * @access public
	 * @since  1.0.0
	 */
	public function save( $term_id ) {
		leira_access()->admin->save( $term_id, 'term', false );
	}

}
