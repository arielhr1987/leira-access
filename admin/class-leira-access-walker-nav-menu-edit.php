<?php

/**
 * Custom Walker for Nav Menu Editor
 * Thanks to "Kathy Darling" for this code from "Nav Roles" plugin
 *
 * @package Leira_Access
 * @since   1.0.0
 * @uses    Walker_Nav_Menu_Edit
 */
class Leira_Access_Walker_Nav_Menu_Edit extends Walker_Nav_Menu_Edit{
	/**
	 * Start the element output.
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item   Menu item data object.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   Not used.
	 * @param int    $id     Not used.
	 *
	 * @see    Walker_Nav_Menu::start_el()
	 * @since  1.0.0
	 * @access public
	 *
	 */
	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$item_output = '';
		$output      .= parent::start_el( $item_output, $item, $depth, $args, $id );
		$output      .= preg_replace(
		// NOTE: Check this regex on major WP version updates!
			'/(?=<fieldset[^>]+class="[^"]*field-move)/',
			$this->get_custom_fields( $item, $depth, $args ),
			$item_output
		);
	}

	/**
	 * Get custom fields
	 *
	 * @access protected
	 *
	 * @param object $item  Menu item data object.
	 * @param int    $depth Depth of menu item. Used for padding.
	 * @param array  $args  Menu item args.
	 * @param int    $id    Nav menu ID.
	 *
	 * @return string Form fields
	 * @uses   do_action() Calls 'menu_item_custom_fields' hook
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function get_custom_fields( $item, $depth, $args = array(), $id = 0 ) {
		ob_start();
		$item_id = intval( $item->ID );
		/**
		 * Get menu item custom fields from plugins/themes
		 *
		 * @param int    $item_id post ID of menu
		 * @param object $item    Menu item data object.
		 * @param int    $depth   Depth of menu item. Used for padding.
		 * @param array  $args    Menu item args.
		 *
		 * @return string Custom fields
		 * @since 1.0.0
		 *
		 */
		do_action( 'wp_nav_menu_item_custom_fields', $item_id, $item, $depth, $args );

		return ob_get_clean();
	}
} // Walker_Nav_Menu_Edit