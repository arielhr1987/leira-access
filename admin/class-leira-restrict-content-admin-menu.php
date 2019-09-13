<?php

/**
 * The admin-specific functionality for menu items.
 *
 * @since      1.0.0
 *
 * @package    Leira_Restrict_Content
 * @subpackage Leira_Restrict_Content/admin
 * @author     Ariel <arielhr1987@gmail.com>
 */
class Leira_Restrict_Content_Admin_Menu{
	/**
	 * Constructor.
	 */
	public function __construct() {

	}

	/**
	 * Override default Admin Menu Walker.
	 * This could generate some interference with other plugins using the same technique to handle menu items edition
	 *
	 * @since 1.0
	 */
	public function edit_nav_menu_walker( $walker, $menu_id ) {
		$class = 'Leira_Restrict_Content_Walker_Nav_Menu_Edit';
		if ( ! class_exists( $class ) ) {
			//
			require_once( plugin_dir_path( __FILE__ ) . '/class-leira-restrict-content-walker-nav-menu-edit.php' );
		}

		return $class;
	}

	/**
	 * Add fields to hook added in Walker
	 * This will allow us to play nicely with any other plugin that is adding the same hook
	 *
	 * @params obj $item - the menu item
	 * @params array $args
	 *
	 * @since  1.0.0
	 */
	public function form( $item_id, $item, $depth, $args ) {
		//new approach
		$roles = get_post_meta( $item->ID, '_leira-restrict-content', true );
		$id    = isset( $item->ID ) ? $item->ID : false;
		leira_restrict_content()->admin->form( $roles, $id );

		return;
	}

	/**
	 * Save the roles as menu item meta
	 *
	 * @return string
	 * @since 1.0
	 */
	public function update_nav_menu_item( $menu_id, $menu_item_db_id ) {
		$wp_roles      = wp_roles();
		$allowed_roles = apply_filters( 'leira_restrict_content_available_roles', $wp_roles->role_names );
		// Verify this came from our screen and with proper authorization.
		if ( ! isset( $_POST['leira-restrict-content-nonce'] ) || ! wp_verify_nonce( $_POST['leira-restrict-content-nonce'], 'leira-restrict-content-nonce-name' ) ) {
			return false;
		}
		$saved_data = false;
		if ( isset( $_POST['leira-restrict-content-status'][ $menu_item_db_id ] ) && $_POST['leira-restrict-content-status'][ $menu_item_db_id ] == 'in' && ! empty ( $_POST['leira-restrict-content-role'][ $menu_item_db_id ] ) ) {

			$custom_roles = array();

			// Only save allowed roles.
			foreach ( (array) $_POST['leira-restrict-content-role'][ $menu_item_db_id ] as $role ) {

				if ( array_key_exists( $role, $allowed_roles ) ) {
					$custom_roles[] = $role;
				}
			}

			if ( ! empty ( $custom_roles ) ) {
				$saved_data = $custom_roles;
			}

		} else if ( isset( $_POST['leira-restrict-content-status'][ $menu_item_db_id ] ) && in_array( $_POST['leira-restrict-content-status'][ $menu_item_db_id ], array(
				'in',
				'out'
			) ) ) {

			$saved_data = $_POST['leira-restrict-content-status'][ $menu_item_db_id ];

		}

		if ( $saved_data ) {
			update_post_meta( $menu_item_db_id, '_leira-restrict-content', $saved_data );
		} else {
			delete_post_meta( $menu_item_db_id, '_leira-restrict-content' );
		}

		return true;
	}

}
