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
	 * @params obj $item - the menu item
	 * @params array $args
	 *
	 * @since  1.0.0
	 */
	public function form( $widget, $return, $instance ) {

		$id    = isset( $widget->id ) ? $widget->id : false;
		$roles = get_post_meta( $id, '_leira-restrict-content', true );
		leira_restrict_content()->admin->form( $roles, $id, array(
			'label' => __( 'Visible to', 'leira-restrict-content' ) . ':'
		) );

		return;
	}

	/**
	 * Save the roles as menu item meta
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function update( $menu_id, $menu_item_db_id ) {

	}

}
