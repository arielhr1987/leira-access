<?php

/**
 * The admin-specific functionality for widgets.
 *
 * @since      1.0.0
 *
 * @package    Leira_Restrict_Content
 * @subpackage Leira_Restrict_Content/admin
 * @author     Ariel <arielhr1987@gmail.com>
 */
class Leira_Restrict_Content_Admin_Widget{
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
	 */
	public function form( $widget, $return, $instance ) {

		$id    = isset( $widget->id ) ? $widget->id : false;
		$roles = isset( $instance['_leira-restrict-content'] ) ? $instance['_leira-restrict-content'] : false;

		leira_restrict_content()->admin->form( $roles, $id, array(
			'label' => __( 'Visible to', 'leira-restrict-content' ) . ':'
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
	 * @since 1.0.0
	 */
	public function update( $instance, $new_instance, $old_instance, $widget ) {
		$save                                    = leira_restrict_content()->admin->save( $widget->id, 'widget' );
		$new_instance['_leira-restrict-content'] = $save;

		return $new_instance;
	}

}
