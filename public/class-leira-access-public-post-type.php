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

		$post                 = get_queried_object();
		$available_taxonomies = leira_access()->get_taxonomies();
		$available_post_types = leira_access()->get_post_types();
		$post_type            = ( isset( $post->post_type ) && ! empty( $post->post_type ) ) ? $post->post_type : false;

		if ( $post instanceof WP_Post && in_array( $post_type, $available_post_types ) ) {
			// post requested
			$access = get_post_meta( $post->ID, '_leira-access', true );

			$visible = leira_access()->public->check_access( $access, $post );

			// check also its parent taxonomies
			if ( $visible ) {

				$parent_terms = wp_get_post_terms( $post->ID, array_keys( $available_taxonomies ) );

				if ( is_array( $parent_terms ) ) {

					foreach ( $parent_terms as $term ) {

						$term_access = get_term_meta( $term->ID, '_leira-access', true );

						$visible = leira_access()->public->check_access( $term_access, $term );

						$visible = apply_filters( 'leira_access_term_visibility', $visible, $term );

						if ( ! $visible ) {
							break;
						}
					}
				}

				$ancestors = get_post_ancestors( $post->ID );
				//check if the post is descendant of a restricted post
			}

			$visible = apply_filters( 'leira_access_post_type_visibility', $visible, $post );

			if ( ! $visible ) {

				leira_access()->public->redirect( $post->ID );

			}
		}

		return true;
	}

	/**
	 * Change get posts query to prevent show up restricted content from displaying restricted posts on
	 * homepage and feeds
	 *
	 * @param WP_Query $query
	 */
	public function remove_posts_in_query( $query ) {
		if ( ! is_admin() && $query->is_main_query() ) {

			if ( ! is_single() ) {

				if ( is_user_logged_in() ) {
					/**
					 * User is logged in, show post types with "_leira-access" meta value equal to "in",
					 * meta doesn't exist or meta contains any of the current user role
					 */
					$user  = wp_get_current_user();
					$roles = ( array ) $user->roles;

					$meta_query = array(
						'relation' => 'OR',
						array(
							'key'     => '_leira-access',
							'value'   => 'in',
							'compare' => '=',

						),
						array(
							'key'     => '_leira-access',
							'compare' => 'NOT EXISTS',
						)
					);

					//in case the user has more than 1 role assigned
					foreach ( $roles as $role ) {
						$meta_query[] = array(
							'key'     => '_leira-access',
							'value'   => "$role",
							'compare' => 'LIKE',
						);
					}

				} else {
					/**
					 * User is not logged in, show post types with "_leira-access" meta value equal to "out"
					 * or meta doesn't exist
					 */
					$meta_query = array(
						'relation' => 'OR',
						array(
							'key'     => '_leira-access',
							'value'   => 'out',
							'compare' => '=',

						),
						array(
							'key'     => '_leira-access',
							'compare' => 'NOT EXISTS',
						)
					);
				}

				$query->set( 'meta_query', $meta_query );

			}
		}
	}

	/**
	 * Return the list of post ids which are restricted, for internal use only
	 *
	 * @since    1.0.0
	 * @access   protected
	 */
	protected function get_restricted_posts_ids() {

		$user                 = wp_get_current_user();
		$roles                = ( array ) $user->roles;
		$available_post_types = leira_access()->get_post_types();
		$available_post_types = array_keys( $available_post_types );

		$query_args = array(
			'posts_per_page' => - 1,
			'offset'         => 0,
			'fields'         => 'ids',
			'post_type'      => $available_post_types,
			'meta_query'     => array(
				'relation' => 'OR',
				array(
					'key'     => '_leira-access',
					'value'   => 'in',
					'compare' => '=',
				)
			)
		);

		//in case the user has more than 1 role assigned
		foreach ( $roles as $role ) {
			$meta_query[] = array(
				'key'     => '_leira-access',
				'value'   => "$role",
				'compare' => 'LIKE',
			);
		}

		$ids = get_posts( $query_args );

		return $ids;
	}
}
