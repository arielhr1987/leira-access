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
	 * @since  1.0.0
	 * @access public
	 */
	public function init() {
		add_action( 'add_meta_boxes', array( $this, 'add_metabox' ) );
	}

	/**
	 * Get all available post types to manage content access
	 *
	 * @return array
	 * @since  1.0.0
	 * @access public
	 */
	public function get_post_types() {
		return leira_access()->get_post_types();
	}

	/**
	 * Add column header to list table
	 *
	 * @param array $columns List of available columns
	 *
	 * @return array
	 * @since  1.0.0
	 * @access public
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
	 * @since  1.0.0
	 * @access public
	 */
	public function custom_column_content( $column_name, $post_id ) {
		if ( 'leira-access' != $column_name ) {
			return;
		}

		$access = get_post_meta( $post_id, Leira_Access::META_KEY, true );
		$output = leira_access()->admin->column_content( $access );

		echo $output;
	}

	/**
	 * Add metabox to posts
	 *
	 * @since  1.0.0
	 * @access public
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
	 * @since  1.0.0
	 * @access public
	 */
	public function render_metabox( $item ) {

		$id    = isset( $item->ID ) ? $item->ID : false;
		$roles = get_post_meta( $id, Leira_Access::META_KEY, true );
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
	 * @since  1.0.0
	 * @access public
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
	 * @since  1.0.0
	 * @access public
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
	 * @since  1.0.0
	 * @access public
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
	 * @since  1.0.0
	 * @access public
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

		if ( ! isset( $_POST['post_type'] ) ) {
			return $post_id;
		}

		if ( ! in_array( sanitize_text_field($_POST['post_type']), $this->get_post_types() ) ) {
			return $post_id;
		}

		leira_access()->admin->save( $post_id, 'post', false );
	}

}
