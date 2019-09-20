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
	 */
	public function init() {
		add_action( 'add_meta_boxes', array( $this, 'add_metabox' ) );
		add_action( 'save_post', array( $this, 'update' ), 10, 2 );
	}

	/**
	 * Get all available post types to manage content access
	 *
	 * @return array
	 */
	public function get_post_types() {
		$post_types = get_post_types( array( 'public' => true, 'show_ui' => true ), 'names' );

		$exclude = apply_filters( 'leira-access_excluded_post_types', array(
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
	 * We hook the wp_loaded to make sure all post_types are registered so we can add our column to the list table
	 */
	public function current_screen() {
		$post_types = $this->get_post_types();
		$screen     = get_current_screen();

		if ( $screen && ! empty( $screen->post_type ) && in_array( $screen->post_type, $post_types ) ) {

			$post_type = $screen->post_type;

			//Filters the columns displayed in the Posts list table for a specific post type.
			//This filter is documented in: wp-admin/includes/class-wp-posts-list-table.php
			add_filter( "manage_{$post_type}_posts_columns", array( $this, 'custom_column_header' ) );

			//Fires for each custom column of a specific post type in the Posts list table.
			//This action is documented in: wp-admin/includes/class-wp-posts-list-table.php
			add_action( "manage_{$post_type}_posts_custom_column", array( $this, 'custom_column_content' ), 10, 2 );
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
	 * @param string $column_name The column name
	 * @param int    $post_id     The id of the post
	 */
	public function custom_column_content( $column_name, $post_id ) {
		if ( 'leira-access' != $column_name ) {
			return;
		}

		$access = get_post_meta( $post_id, '_leira-access', true );
		$output   = __( 'Everyone', 'leira-access' );

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

		echo $output;
	}

	/**
	 * Add metabox to posts
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
				__( 'Visibility', 'leira-access' ),
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
	 */
	public function render_metabox( $item ) {

		$id    = isset( $item->ID ) ? $item->ID : false;
		$roles = get_post_meta( $id, '_leira-access', true );
		leira_access()->admin->form( $roles, $id, array(
			'show_label' => false
		) );
	}

	/**
	 * Add interface to bulk edit form
	 *
	 * @param $column_name
	 * @param $post_type
	 */
	public function bulk_edit_custom_box( $column_name, $post_type ) {
		$post_types = $this->get_post_types();
		if ( 'leira-access' != $column_name || ! in_array( $post_type, $post_types ) ) {
			return;
		}

		$id    = '__';
		$roles = array();
		?>
        <div class="">
            <div class="inline-edit-col">
				<?php leira_access()->admin->form( $roles, $id ); ?>
            </div>
        </div>
		<?php
	}

	/**
	 * Add interface to quick edit form
	 *
	 * @param $column_name
	 * @param $post_type
	 */
	public function quick_edit_custom_box( $column_name, $post_type ) {
		$post_types = $this->get_post_types();
		if ( 'leira-access' != $column_name || ! in_array( $post_type, $post_types ) ) {
			return;
		}

		$id    = '__';
		$roles = array();
		?>
        <fieldset class="inline-edit-col-left">
            <div class="inline-edit-col">
				<?php leira_access()->admin->form( $roles, $id ); ?>
            </div>
        </fieldset>
		<?php
	}

	/**
	 * Save the roles as menu item meta
	 *
	 * @return string
	 * @since 1.0
	 */
	public function update( $menu_id, $menu_item_db_id ) {

	}

	/**
	 * Save the post meta
	 *
	 * @param integer $post_id The post we are saving
	 *
	 * @return mixed
	 */
	function save_quick_edit_data( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		if ( ! current_user_can( 'manage_options', $post_id ) ) {
			return $post_id;
		}

		if ( ! in_array( $_POST['post_type'], $this->get_post_types() ) ) {
			return $post_id;
		}

		//$data = empty( $_POST['headline_news'] ) ? 0 : 1;
		//update_post_meta( $post_id, 'headline_news', $data );
	}

}
