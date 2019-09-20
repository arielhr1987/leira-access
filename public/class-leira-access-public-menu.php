<?php

/**
 * The public-facing functionality for Nav Menu.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @link       https://github.com/arielhr1987
 * @since      1.0.0
 * @package    Leira_Access
 * @subpackage Leira_Access/public
 * @author     Ariel <arielhr1987@gmail.com>
 */
class Leira_Access_Public_Menu{

	/**
	 * Constructor.
	 */
	public function __construct() {

	}

	/**
	 * Add roles to the menu item
	 *
	 * @param $menu_item
	 *
	 * @return mixed
	 */
	public function setup_nav_menu_item( $menu_item ) {
		if ( isset( $menu_item->ID ) ) {
			$roles = get_post_meta( $menu_item->ID, '_leira-access', true );
			if ( ! empty( $roles ) ) {
				$menu_item->roles = $roles;
			}
		}

		return $menu_item;
	}

	/**
	 * Exclude menus from the array to show
	 *
	 * @param $items
	 *
	 * @return mixed
	 */
	public function exclude_menu_items( $items ) {
		//just a simple check
		if ( ! empty( $items ) ) {
			$hidden_items = array();
			foreach ( $items as $key => $item ) {
				$visible = true;
				//check if the item is child of a hidden item
				if ( isset( $item->menu_item_parent ) && in_array( $item->menu_item_parent, $hidden_items ) ) {
					$visible = false;
				}
				$visible = apply_filters( 'nav_menu_item_visibility', $item, $visible );
				if ( ! $visible ) {
					//remove the menu item from the list and add it to hidden items
					if ( isset( $item->ID ) ) {
						$hidden_items[] = $item->ID;
					}
					unset( $items[ $key ] );
				}
			}
		}

		return $items;
	}

	/**
	 * Filter if the menu item should be visible or not.
	 * This is an example of how to use the this filter
	 *
	 * @param WP_Post $item
	 * @param bool    $visible
	 *
	 * @return bool
	 */
	public function filter_menu_item_visible( $item, $visible ) {
		$main = leira_access();
		if ( $main->is_request( 'frontend' ) and $visible ) {
			if ( $item->post_title == 'Google' ) {
				$visible = false;
			}
		}

		return $visible;
	}

}
