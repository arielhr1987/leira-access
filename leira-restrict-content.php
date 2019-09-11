<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/arielhr1987
 * @since             1.0.0
 * @package           Leira_Restrict_Content
 *
 * @wordpress-plugin
 * Plugin Name:       Leira Restrict Content
 * Plugin URI:        leira-restrict-content
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Ariel
 * Author URI:        https://github.com/arielhr1987
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       leira-restrict-content
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'LEIRA_RESTRICT_CONTENT_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-leira-restrict-content-activator.php
 */
function activate_leira_restrict_content() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-leira-restrict-content-activator.php';
	Leira_Restrict_Content_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-leira-restrict-content-deactivator.php
 */
function deactivate_leira_restrict_content() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-leira-restrict-content-deactivator.php';
	Leira_Restrict_Content_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_leira_restrict_content' );
register_deactivation_hook( __FILE__, 'deactivate_leira_restrict_content' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-leira-restrict-content.php';

/**
 * Helper method to get the main instance of the plugin
 *
 * @return Leira_Restrict_Content
 */
function leira_restrict_content() {
	return Leira_Restrict_Content::instance();
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
leira_restrict_content()->run();
