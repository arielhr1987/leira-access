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
			$visible = leira_access()->public->check_access( $post );

			// check also its parent taxonomies
			if ( $visible ) {

				$parent_terms = wp_get_post_terms( $post->ID, array_keys( $available_taxonomies ) );

				if ( is_array( $parent_terms ) ) {

					foreach ( $parent_terms as $term ) {

						$visible = leira_access()->public->check_access( $term );

						if ( ! $visible ) {
							break;
						}
					}
				}

				$ancestors = get_post_ancestors( $post->ID );
				//check if the post is descendant of a restricted post
			}

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

				$meta_query = leira_access()->public->get_meta_query();

				$query->set( 'meta_query', $meta_query );

			}
		}
	}


	/**
	 * Prevent from displaying restricted posts in recent posts widget
	 *
	 * @param $query_args
	 *
	 * @return mixed
	 * @since    1.0.0
	 * @access   public
	 */
	public function hide_posts_in_recent_posts_widget( $query_args ) {

		if ( ! is_admin() ) {
			// Not a query for an admin page.
			$meta_query               = leira_access()->public->get_meta_query();
			$query_args['meta_query'] = $meta_query;
		}

		return $query_args;
	}


	/**
	 * Prevent from displaying restricted pages in pages widget
	 *
	 * @param $query_args
	 *
	 * @return mixed
	 * @since    1.0.0
	 * @access   public
	 */
	public function hide_pages_in_pages_widget( $query_args ) {

		if ( ! is_admin() ) {
			// Not a query for an admin page.
			$hidden_posts          = $this->get_visible_page_ids();
			$query_args['include'] = implode( ',', $hidden_posts );

			//TODO: dont use include, use exclude instead
		}

		return $query_args;
	}

	/**
	 * prevent from displaying restricted posts in recent comments widget
	 *
	 * @param $query_args
	 *
	 * @return mixed
	 * @since    1.0.0
	 * @access   public
	 */
	public function hide_posts_in_recent_comments_widget( $query_args ) {

		if ( ! is_admin() ) {
			$visible_posts          = $this->get_visible_page_ids();
			$query_args['post__in'] = $visible_posts;
		}

		return $query_args;
	}

	/**
	 * Return a list of post ids which are accessible, for internal use only
	 *
	 * @return array
	 * @since    1.0.0
	 * @access   protected
	 */
	protected function get_visible_post_ids() {
		$query_args = array(
			'posts_per_page' => - 1,
			'offset'         => 0,
			'fields'         => 'ids',
			'meta_query'     => leira_access()->public->get_meta_query()
		);

		$post_ids = get_posts( $query_args );

		return $post_ids;
	}

	/**
	 * Returns a list with all page ids that are accessible
	 *
	 * @return array
	 * @since    1.0.0
	 * @access   protected
	 */
	protected function get_visible_page_ids() {

		$query_args = array(
			'orderby'          => 'date',
			'order'            => 'DESC',
			'posts_per_page'   => - 1,
			'post_type'        => 'any',
			'post_status'      => 'publish',
			'offset'           => 0,
			'fields'           => 'ids',
			'suppress_filters' => true,
			'meta_query'       => leira_access()->public->get_meta_query()
		);
		$get_posts  = new WP_Query;

		return $get_posts->query( $query_args );
	}

	protected function get_visible_categoriy_ids() {

	}
}
