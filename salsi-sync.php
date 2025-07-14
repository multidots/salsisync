<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.multidots.com/
 * @since             1.0
 * @package           Salsi_Sync
 *
 * @wordpress-plugin
 * Plugin Name:       Salsi Sync
 * Requires Plugins:  woocommerce
 * Plugin URI:        https://www.multidots.com
 * Description:       A plugin designed for seamless synchronization between Salsify and WordPress.
 * Version:           1.1
 * Author:            Multidots
 * Author URI:        https://www.multidots.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       salsisync
 * Domain Path:       /languages
 */

namespace Salsi_Sync;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'SALSI_SYNC_VERSION', '1.1' );
define( 'SALSI_SYNC_URL', plugin_dir_url( __FILE__ ) );
define( 'SALSI_SYNC_DIR', plugin_dir_path( __FILE__ ) );
$base_dir = wp_upload_dir()['basedir']  . '/salsi-sync/';
define( 'SALSI_SYNC_UPLOAD_DIR', $base_dir);

define( 'SALSI_SYNC_BASEPATH', plugin_basename( __FILE__ ) );
define( 'SALSI_SYNC_SRC_BLOCK_DIR_PATH', untrailingslashit( SALSI_SYNC_DIR . 'assets/build/js/blocks' ) );
define( 'SALSI_SYNC_LOGO_ICON', SALSI_SYNC_URL . 'assets/images/menu-icon.svg' );

if ( ! defined( 'SALSI_SYNC_PATH' ) ) {
	define( 'SALSI_SYNC_PATH', __DIR__ );
}

// Load the autoloader.
require_once plugin_dir_path( __FILE__ ) . '/inc/helpers/autoloader.php';


register_activation_hook( __FILE__, array( \Salsi_Sync\Inc\Activator::class, 'activate' ) );
register_activation_hook( __FILE__, array( \Salsi_Sync\Inc\Activator::class, 'salsisync__create_bulk_insert_log_table' ) );
register_activation_hook( __FILE__, array( \Salsi_Sync\Inc\Activator::class, 'salsisync__create_salsi_sync_folder_and_file' ) );
register_deactivation_hook( __FILE__, array( \Salsi_Sync\Inc\Deactivator::class, 'deactivate' ) );

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function run_md_scaffold() {
	$plugin = new \Salsi_Sync\Inc\Salsi_Sync();
}
run_md_scaffold();
