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
class Leira_Access_Admin_Menu{

	/**
	 * Constructor.
	 */
	public function __construct() {

	}

	/**
	 * Override default Admin Menu Walker.
	 * This could generate some interference with other plugins using the same technique to handle menu items edition
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function edit_nav_menu_walker( $walker, $menu_id ) {
		$class = 'Leira_Access_Walker_Nav_Menu_Edit';
		if ( ! class_exists( $class ) ) {
			//
			require_once( plugin_dir_path( __FILE__ ) . '/class-leira-access-walker-nav-menu-edit.php' );
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
	 * @access public
	 */
	public function form( $item_id, $item, $depth, $args ) {
		//new approach
		$roles = get_post_meta( $item->ID, Leira_Access::META_KEY, true );
		$id    = isset( $item->ID ) ? $item->ID : false;
		leira_access()->admin->form( array(
			'roles' => $roles,
			'id'    => $id
		) );

		return;
	}

	/**
	 * Save the roles as menu item meta
	 *
	 * @param integer $menu_id
	 * @param integer $menu_item_db_id The database menu item id
	 *
	 * @return string
	 * @since  1.0.0
	 * @access public
	 */
	public function update_nav_menu_item( $menu_id, $menu_item_db_id ) {
		leira_access()->admin->save( $menu_item_db_id );

		return true;
	}

}
