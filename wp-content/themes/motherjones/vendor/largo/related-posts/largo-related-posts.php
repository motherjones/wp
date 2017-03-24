<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://nerds.inn.org
 * @since             1.0.0
 * @package           Largo_Related_Posts
 *
 * @wordpress-plugin
 * Plugin Name:       Largo Related Posts Widget
 * Plugin URI:        https://inn.org
 * Description:       Display related posts in a widget using taxonomy relationships.
 * Version:           1.0.0
 * Author:            INN Nerds
 * Author URI:        https://nerds.inn.org
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       largo-related-posts
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-largo-related-posts.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_largo_related_posts() {

	$plugin = new Largo_Related_Posts();
	$plugin->run();

}
run_largo_related_posts();
