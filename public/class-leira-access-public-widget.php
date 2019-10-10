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
		if ( isset( $settings[ Leira_Access::META_KEY ] ) && $access = $settings[ Leira_Access::META_KEY ] ) {

			$widget->leira_access = $access;

			$visible = leira_access()->public->check_access( $widget );

			//$visible = apply_filters( 'leira_access_widget_visibility', $visible, $settings, $widget, $args );
		}

		return $visible ? $settings : false;
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
			$exclude = isset( $query_args['exclude'] ) ? $query_args['exclude'] : '';

			$exclude = rtrim( $exclude, ',' );

			$hidden_posts = leira_access()->public->get_hidden_post_type_ids();

			$exclude = implode( ',', $hidden_posts ) . ',' . $exclude;

			$query_args['exclude'] = $exclude;
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
			$visible_posts          = leira_access()->public->get_visible_post_type_ids();
			$query_args['post__in'] = $visible_posts;
		}

		return $query_args;
	}

	/**
	 * Exclude categories from the category widget
	 *
	 * @param array $cat_args
	 *
	 * @return array
	 */
	public function hide_categories_in_category_widget( $cat_args ) {
		$terms = get_terms( array(
			'fields'   => 'ids',
			'taxonomy' => 'category',
			'exclude'  => leira_access()->public->get_visible_term_ids()
		) );

//		$cat_args['exclude_tree'] = $terms;
		$cat_args['exclude'] = $terms;

		return $cat_args;
	}
}
