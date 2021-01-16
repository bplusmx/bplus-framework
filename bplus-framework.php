<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://bplus.mx
 * @since             1.0.0
 * @package           Bplus_Framework
 *
 * @wordpress-plugin
 * Plugin Name:       B+ Framework
 * Plugin URI:        http://bplus.mx
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Luis Abarca
 * Author URI:        http://bplus.mx
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bplus-framework
 * Domain Path:       /languages
 * Bitbucket Plugin URI: https://bitbucket.org/bplusmx/bplus-framework
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'BPLUS_FRAMEWORK', 		'1.0.0' );
define( 'BPLUS_FRAMEWORK_PATH', plugin_dir_path( __FILE__ ) );
define( 'BPLUS_FRAMEWORK_URL', 	plugin_dir_url( __FILE__ ) );

/*
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(E_ALL);
// */

// Table name with prefix.
//define( 'BPLUS_FRAMEWORK_TABLE_STATS', $wpdb->prefix . 'bplus_fw_registrations' );

include BPLUS_FRAMEWORK_PATH . 'vendor/autoload.php';

require_once BPLUS_FRAMEWORK_PATH . 'modules/class-bplus-framework-utils.php';
require_once BPLUS_FRAMEWORK_PATH . 'includes/class-bplus-framework-rest-auth.php';
require_once BPLUS_FRAMEWORK_PATH . 'includes/class-bplus-framework-jwt.php';
require_once BPLUS_FRAMEWORK_PATH . 'includes/class-bplus-framework-rest.php';

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-bplus-framework-activator.php
 */
function activate_bplus_framework() {
	require_once BPLUS_FRAMEWORK_PATH . 'includes/class-bplus-framework-activator.php';
	Bplus_Framework_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-bplus-framework-deactivator.php
 */
function deactivate_bplus_framework() {
	require_once BPLUS_FRAMEWORK_PATH . 'includes/class-bplus-framework-deactivator.php';
	Bplus_Framework_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_bplus_framework' );
register_deactivation_hook( __FILE__, 'deactivate_bplus_framework' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-bplus-framework.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_bplus_framework() {

	add_action( 'rest_api_init', array( 'Bplus_Framework_Rest', 'rest_api_init' ), 15 );

	$plugin = new Bplus_Framework();
	$plugin->run();

}

run_bplus_framework();
