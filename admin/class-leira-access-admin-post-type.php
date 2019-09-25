<?php

/**
 * The admin-specific functionality for menu items.
 *
 * @since      1.0.0
 *
 * @package    Leira_Access
 * @subpackage Leira_Access/admin
 * @author     Ariel <arielhr1987@gmail.com>
 */
class Leira_Access_Admin_Post_Type{

	/**
	 * Constructor.
	 */
	public function __construct() {

	}

	/**
	 * Init
	 *
	 * @access public
	 * @since  1.0.0
	 */
	public function init() {
		add_action( 'add_meta_boxes', array( $this, 'add_metabox' ) );
	}

	/**
	 * Get all available post types to manage content access
	 *
	 * @return array
	 * @access public
	 * @since  1.0.0
	 */
	public function get_post_types() {
		$post_types = get_post_types( array( 'public' => true, 'show_ui' => true ), 'names' );

		$exclude = apply_filters( 'leira_access_excluded_post_types', array(
			'forum',
			'topic',
			'reply',
			//'product',
			'attachment'
		) );

		$post_types = array_diff( $post_types, $exclude );

		return $post_types;
	}

	/**
	 * Add column header to list table
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
	 * @param string $column_name The column name
	 * @param int    $post_id     The id of the post
	 *
	 * @access public
	 * @since  1.0.0
	 */
	public function custom_column_content( $column_name, $post_id ) {
		if ( 'leira-access' != $column_name ) {
			return;
		}

		$access = get_post_meta( $post_id, '_leira-access', true );
		$output = __( 'Everyone', 'leira-access' );

		if ( $access == 'out' ) {
			$output = __( 'Logged Out Users', 'leira-access' );
		} else {
			//is "in" or array of roles
			if ( $access == 'in' ) {
				$output = __( 'Logged In Users', 'leira-access' );
			} else if ( is_array( $access ) ) {
				$output = __( 'Logged In Users with Roles', 'leira-access' );
			}
		}

		//Add inline edit values
		$output .= sprintf( '<div class="hidden inline-leira-access">%s</div>', json_encode( $access ) );

		echo $output;
	}

	/**
	 * Add metabox to posts
	 *
	 * @access public
	 * @since  1.0.0
	 */
	public function add_metabox() {

		/**
		 * Get public post types
		 */
		$post_types = $this->get_post_types();
		$screen     = get_current_screen();

		if ( $screen && ! empty( $screen->post_type ) && in_array( $screen->post_type, $post_types ) ) {
			add_meta_box(
				'leira-access-meta-box',
				__( 'Access', 'leira-access' ),
				array( $this, 'render_metabox' ),
				$screen->post_type,
				'side',
				'default',
				array(
					//will only be visible in the classic editor
					//'__back_compat_meta_box' => true,
					//will show a message with that the meta is not available with gutenberg
					//'__block_editor_compatible_meta_box' => false,
				)
			);
		}
	}

	/**
	 * Render meta-box in the posts editor page
	 *
	 * @param WP_Post $item
	 *
	 * @access public
	 * @since  1.0.0
	 */
	public function render_metabox( $item ) {

		$id    = isset( $item->ID ) ? $item->ID : false;
		$roles = get_post_meta( $id, '_leira-access', true );
		leira_access()->admin->form( array(
			'roles'      => $roles,
			'show_label' => false
		) );
	}

	/**
	 * Enqueue quick edit list table script
	 *
	 * @param $hook
	 *
	 * @access public
	 * @since  1.0.0
	 */
	public function admin_enqueue_quick_edit_scripts( $hook ) {
		$pages = array( 'edit.php' );
		if ( ! in_array( $hook, $pages ) ) {
			return;
		}

		$screen    = get_current_screen();
		$post_type = isset( $screen->post_type ) ? $screen->post_type : false;
		if ( ! in_array( $post_type, $this->get_post_types() ) ) {
			return;
		}

		wp_enqueue_script( 'leira-access-admin-quick-edit-post-js', plugins_url( '/js/leira-access-admin-quick-edit-post.js', __FILE__ ), array(
			'jquery',
			//'inline-edit-post'
		) );
	}

	/**
	 * Add form to bulk edit form
	 *
	 * @param $column_name
	 * @param $post_type
	 *
	 * @access public
	 * @since  1.0.0
	 */
	public function bulk_edit_custom_box( $column_name, $post_type ) {
		$post_types = $this->get_post_types();
		if ( 'leira-access' != $column_name || ! in_array( $post_type, $post_types ) ) {
			return;
		}

		$id    = '';
		$roles = array();
		?>
        <div class="">
            <div class="inline-edit-col">
				<?php leira_access()->admin->form( array(
					'roles' => $roles,
					'id'    => $id
				) ); ?>
            </div>
        </div>
		<?php
	}

	/**
	 * Add form to quick edit
	 *
	 * @param $column_name
	 * @param $post_type
	 *
	 * @access public
	 * @since  1.0.0
	 */
	public function quick_edit_custom_box( $column_name, $post_type ) {
		$post_types = $this->get_post_types();
		if ( 'leira-access' != $column_name || ! in_array( $post_type, $post_types ) ) {
			return;
		}

		$id    = '';
		$roles = array();
		?>
        <fieldset class="inline-edit-col-left">
            <div class="inline-edit-col">
				<?php leira_access()->admin->form( array(
					'roles' => $roles,
					'id'    => $id
				) ); ?>
            </div>
        </fieldset>
		<?php
	}

	/**
	 * Save the post meta
	 *
	 * @param integer $post_id The post we are saving
	 *
	 * @return mixed
	 * @access public
	 * @since  1.0.0
	 */
	function save( $post_id ) {
		if ( ! is_admin() ) {
			return $post_id;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		if ( ! current_user_can( 'manage_options', $post_id ) ) {
			return $post_id;
		}

		if ( ! in_array( $_POST['post_type'], $this->get_post_types() ) ) {
			return $post_id;
		}

		leira_access()->admin->save( $post_id, 'post', false );
	}

}
