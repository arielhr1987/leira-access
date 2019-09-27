<?php

/**
 * The public-facing functionality for Widgets.
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
class Leira_Access_Public_Widget{

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
	 * @since    1.0.0
	 * @access   public
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
	 * Exclude widgets from the array to show
	 *
	 * @param array     $settings
	 * @param WP_Widget $widget
	 * @param array     $args
	 *
	 * @return mixed
	 * @since    1.0.0
	 * @access   public
	 */
	public function check_access( $settings, $widget, $args ) {
		//just a simple check
		$visible = true;
		if ( isset( $settings['_leira-access'] ) && $access = $settings['_leira-access'] ) {

			$visible = leira_access()->public->check_access( $access, $widget );

			$visible = apply_filters( 'leira_access_widget_visibility', $visible, $settings, $widget, $args );
		}

		return $visible ? $settings : false;
	}


	/**
	 * prevent from displaying restricted posts in recent posts widget
	 *
	 * @since    1.2.0
	 */
	public function hide_rectricted_posts_in_recent_posts( $query_args ) {

		if ( ! is_admin() && ! is_user_logged_in() ) {
			// Not a query for an admin page.
			// User NOT logged in

			$query_args['meta_query'] = array(
				'relation' => 'OR',
				array(
					'key'     => 'arc_restricted_post',
					'value'   => 1,
					'compare' => '!=',
					'type'    => 'NUMERIC'

				),
				array(
					'key'     => 'arc_restricted_post',
					'compare' => 'NOT EXISTS',
				)
			);
		}

		return $query_args;
	}

	/**
	 * prevent from displaying restricted posts in recent comments widget
	 *
	 * @since    1.2.0
	 */
	public function hide_rectricted_posts_in_recent_comments( $query_args ) {
		$restricted_posts = $this->get_restricted_posts_ids();

		if ( ! is_admin() && ! is_user_logged_in() && is_array( $restricted_posts ) && sizeof( $restricted_posts ) > 0 ) {
			// Not a query for an admin page.
			// User NOT logged in

			$query_args['post__not_in'] = $restricted_posts;
		}

		return $query_args;
	}


	/**
	 * return list of post ids which are restricted, for internal use only
	 *
	 * @since    1.2.0
	 */
	private function get_restricted_posts_ids() {
		$query_args = array(
			'posts_per_page' => - 1,
			'offset'         => 0,
			'fields'         => 'ids',
			'meta_query'     => array(
				array(
					'key'     => 'arc_restricted_post',
					'value'   => 1,
					'compare' => '=',
					'type'    => 'NUMERIC'

				)
			)
		);

		$postslist = get_posts( $query_args );

		return $postslist;
	}
}
