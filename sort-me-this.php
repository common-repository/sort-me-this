<?php

/**
 * The plugin bootstrap file
 *
 * @since             1.0.0
 * @package           Sort_Me_This
 *
 * @wordpress-plugin
 * Plugin Name:       SortMeThis
 * Description:       This plugin allows you to fully manage, filter and organize your media in easy way.
 * Version:           1.1
 * Author:            Algaweb
 * Author URI:        http://algaweb.it/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       sort-me-this
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 */
define( 'SMET_VERSION', '1.1' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-sort-me-this-activator.php
 */
function activate_sort_me_this() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-sort-me-this-activator.php';
	Sort_Me_This_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-sort-me-this-deactivator.php
 */
function deactivate_sort_me_this() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-sort-me-this-deactivator.php';
	Sort_Me_This_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_sort_me_this' );
register_deactivation_hook( __FILE__, 'deactivate_sort_me_this' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-sort-me-this.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_sort_me_this() {

	$plugin = new Sort_Me_This();
	$plugin->run();

}
run_sort_me_this();