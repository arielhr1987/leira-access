<?php

/**
 * The public-facing functionality for Post Types.
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
class Leira_Access_Public_Post_Type{

	/**
	 * Constructor.
	 */
	public function __construct() {

	}

	/**
	 * Check access to post type pages, if not available redirect to login page.
	 * It will check also for the post terms, if the any of the terms is not visible to the user dont show the post
	 *
	 * @return bool
	 * @since  1.0.0
	 * @access public
	 */
	public function check_access() {

		$post = get_queried_object();

		if ( $post instanceof WP_Post ) {

			$visible = leira_access()->public->check_post_type_access( $post );

			if ( ! $visible ) {

				leira_access()->public->redirect();

			}
		}

		return true;
	}

	/**
	 * Change get posts query to prevent show up restricted content from displaying restricted posts on
	 * homepage and feeds
	 *
	 * @param WP_Query $query
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function remove_posts_in_query( $query ) {

		if ( ! is_admin() && $query->is_main_query() ) {

			if ( ! is_single() ) {

				$meta_query = leira_access()->public->get_meta_query();

				$query->set( 'meta_query', $meta_query );

			}
		}
	}
}
