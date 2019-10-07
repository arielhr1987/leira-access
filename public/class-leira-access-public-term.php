<?php

/**
 * The public-facing functionality for Terms.
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
class Leira_Access_Public_Term{

	/**
	 * Constructor.
	 */
	public function __construct() {

	}

	/**
	 * Check access to Term page, if not available redirect to login page
	 *
	 * @return bool
	 */
	public function check_access() {

		$term                 = get_queried_object();
		$available_taxonomies = leira_access()->get_taxonomies();
		$taxonomy             = ( isset( $term->taxonomy ) && ! empty( $term->taxonomy ) ) ? $term->taxonomy : false;

		if ( $term instanceof WP_Term && in_array( $taxonomy, $available_taxonomies ) ) {

			$visible = leira_access()->public->check_access( $term );

			//check for ancestors

			if ( ! $visible ) {

				leira_access()->public->redirect( $term->ID );

			}
		}

		return true;
	}
}
