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
		if ( isset( $settings['_leira-access'] ) && $access = $settings['_leira-access'] ) {

			$widget->leira_access = $access;

			$visible = leira_access()->public->check_access( $widget );

			//$visible = apply_filters( 'leira_access_widget_visibility', $visible, $settings, $widget, $args );
		}

		return $visible ? $settings : false;
	}
}
