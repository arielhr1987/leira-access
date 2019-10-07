<?php

/**
 * The public-facing functionality for Feeds.
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
class Leira_Access_Public_Feed{

	/**
	 * Feed.
	 */
	public function rss_head() {
		add_filter( 'the_content', array( $this, 'check_access' ) );
	}

	/**
	 * Check access for feed content
	 *
	 * @param string $content
	 *
	 * @return string
	 * @since  1.0.0
	 * @access public
	 */
	public function check_access( $content ) {
		global $post;

		$visible = leira_access()->public->check_access( $post );

		$visible = apply_filters( 'leira_access_feed_visibility', $visible, $post );

		if ( is_feed() && ! $visible ) {
			return __( 'This content is restricted', 'leira-access' );
		} else {
			return $content;
		}
	}

}
