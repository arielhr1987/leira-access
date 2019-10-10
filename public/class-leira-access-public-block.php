<?php

/**
 * The public-facing functionality for Gutenberg Blocks.
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
class Leira_Access_Public_Block{


	/**
	 * Check access for feed content
	 *
	 * @param string $block
	 * @param array  $options
	 *
	 * @return string
	 * @since  1.0.0
	 * @access public
	 */
	public function check_access( $block, $options ) {

		$access = true;

		if ( isset( $options['attrs'][ Leira_Access::META_KEY ] ) ) {
			$access = $options['attrs'][ Leira_Access::META_KEY ];
			$access = leira_access()->public->check_access( $access );
		}

		return $access ? $block : '';
	}

}
