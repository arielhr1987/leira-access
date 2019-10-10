<?php

/**
 * The admin-specific functionality for widgets.
 *
 * @since      1.0.0
 *
 * @package    Leira_Access
 * @subpackage Leira_Access/admin
 * @author     Ariel <arielhr1987@gmail.com>
 */
class Leira_Access_Admin_Widget{
	/**
	 * Constructor.
	 */
	public function __construct() {

	}

	/**
	 * Handles 'in_widget_form' action
	 *
	 * Use this hook to add extra fields to the widget form.
	 * The hook is only fired if the value passed to the ‘widget_form_callback’ hook is not false.
	 *
	 * @param $widget
	 * @param $return
	 * @param $instance
	 *
	 * @return array
	 * @since  1.0.0
	 * @access public
	 */
	public function form( $widget, $return, $instance ) {

		$id    = isset( $widget->id ) ? $widget->id : false;
		$roles = isset( $instance[Leira_Access::META_KEY] ) ? $instance[Leira_Access::META_KEY] : false;

		leira_access()->admin->form( array(
			'roles' => $roles,
			'id'    => $id,
			'label' => __( 'Access', 'leira-access' ) . ':'
		) );

		return array( $widget, $return, $instance );
	}

	/**
	 * Save the roles as menu item meta
	 *
	 * @param array     $instance     The current widget instance's settings.
	 * @param array     $new_instance Array of new widget settings.
	 * @param array     $old_instance Array of old widget settings.
	 * @param WP_Widget $widget       The current widget instance
	 *
	 * @return array
	 * @since  1.0.0
	 * @access public
	 */
	public function update( $instance, $new_instance, $old_instance, $widget ) {
		$save = leira_access()->admin->save( $widget->id, 'widget' );

		$new_instance[Leira_Access::META_KEY] = $save;

		return $new_instance;
	}

}
