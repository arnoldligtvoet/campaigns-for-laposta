<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/arnoldligtvoet/campaigns-for-laposta
 * @since             1.0.0
 * @package           Campaigns_For_Laposta
 *
 * @wordpress-plugin
 * Plugin Name:       Campaigns for Laposta
 * Plugin URI:        https://github.com/arnoldligtvoet/campaigns-for-laposta
 * Description:       Connect to Laposta to get your sent campaigns shown in Wordpress. 
 * Version:           1.0.0
 * Author:            Arnold Ligtvoet
 * Author URI:        https://github.com/arnoldligtvoet/campaigns-for-laposta/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       campaigns-for-laposta
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
define( 'CAMPAIGNS_FOR_LAPOSTA_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-campaigns-for-laposta-activator.php
 */
function activate_campaigns_for_laposta() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-campaigns-for-laposta-activator.php';
	Campaigns_For_Laposta_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-campaigns-for-laposta-deactivator.php
 */
function deactivate_campaigns_for_laposta() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-campaigns-for-laposta-deactivator.php';
	Campaigns_For_Laposta_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_campaigns_for_laposta' );
register_deactivation_hook( __FILE__, 'deactivate_campaigns_for_laposta' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-campaigns-for-laposta.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_campaigns_for_laposta() {

	$plugin = new Campaigns_For_Laposta();
	$plugin->run();

}
run_campaigns_for_laposta();
