<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/coldlamper
 * @since             1.0.0
 * @package           Wptides
 *
 * @wordpress-plugin
 * Plugin Name:       wpTides
 * Plugin URI:        https://github.com/coldlamper/wpTides
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Brian Keith
 * Author URI:        https://github.com/coldlamper
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wptides
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'WPTIDES_VERSION', '1.0.0' );

function deactivate_wptides() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wptides-deactivator.php';
	Wptides_Deactivator::deactivate();
}

register_deactivation_hook( __FILE__, 'deactivate_wptides' );

/**
 * The core plugin class that is used to define
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wptides.php';

function run_wptides() {

	$plugin = new Wptides();
	$plugin->run();

}
run_wptides();
