<?php

/**
 *
 * @link              https://agilestorelocator.com/
 * @since             1.0.0
 * @package           AgileStoreLocator
 *
 * @wordpress-plugin
 * Plugin Name:       Agile Store Locator
 * Plugin URI:        http://agilestorelocator.com
 * Description:       Agile Store Locator (Pro Version 4.2.5) is a Premium Store Finder Plugin designed to offer you immediate access to all the best stores in your local area. It enables you to find the very best stores and their location thanks to the power of Google Maps.
 * Version:           4.2.5
 * Author:            AGILELOGIX
 * Author URI:        https://agilestorelocator.com/
 * License:           Copyrights 2018
 * License URI:       
 * Text Domain:       asl_locator
 * Domain Path:       /languages/
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/*
function d($d){echo '<pre>';print_r($d);echo '</pre><hr/>';}
function dd($d){echo '<pre>';print_r($d);echo '</pre>';die;}
*/

global $wpdb;
global $wp_version;


define( 'ASL_URL_PATH', plugin_dir_url( __FILE__ ) );
define( 'ASL_PLUGIN_PATH', plugin_dir_path(__FILE__) );
define( 'ASL_BASE_PATH', dirname( plugin_basename( __FILE__ ) ) );
define( 'ASL_PREFIX', $wpdb->prefix."asl_" );
define( 'ASL_CVERSION', "4.2.5" );





if (version_compare($wp_version, '3.3.2', '<=')) {
	//die('version not supported');
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-agile-store-locator-activator.php
 */
function activate_AgileStoreLocator() {
	require_once ASL_PLUGIN_PATH . 'includes/class-agile-store-locator-activator.php';
	AgileStoreLocator_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-agile-store-locator-deactivator.php
 */
function deactivate_AgileStoreLocator() {
	require_once ASL_PLUGIN_PATH . 'includes/class-agile-store-locator-deactivator.php';
	AgileStoreLocator_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_AgileStoreLocator' );
register_deactivation_hook( __FILE__, 'deactivate_AgileStoreLocator' );






/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require ASL_PLUGIN_PATH . 'includes/class-agile-store-locator.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_AgileStoreLocator() {

	$plugin = new AgileStoreLocator();
	$plugin->run();
}

run_AgileStoreLocator();
