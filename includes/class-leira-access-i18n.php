<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://github.com/arielhr1987
 * @since      1.0.0
 *
 * @package    Leira_Restrict_Content
 * @subpackage Leira_Restrict_Content/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Leira_Restrict_Content
 * @subpackage Leira_Restrict_Content/includes
 * @author     Ariel <arielhr1987@gmail.com>
 */
class Leira_Access_i18n{

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'leira-access',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}

}
