<?php
/**
 * The activation functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Salsi_Sync
 * @subpackage Salsi_Sync/admin
 * @author     Multidots <info@multidots.com>
 */

namespace Salsi_Sync\Inc;

use Salsi_Sync\Inc\Traits\Singleton;

/**
 * Activator class file.
 */
class Activator {

	use Singleton;

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		$version = 7;
		$theme   = wp_get_theme();
		if ( version_compare( $version, get_bloginfo( 'version' ), '>=' ) ) {
			return true;
		} else {
			wp_die( esc_html_e( 'Please activate Twenty two theme', 'salsisync' ), 'Theme dependency check', array( 'back_link' => true ) );
		}

		if ( ! class_exists( 'WooCommerce' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
			deactivate_plugins( SALSI_SYNC_BASEPATH );
			wp_die( esc_html_e( 'Please install and Activate WooCommerce.', 'salsisync' ), 'Plugin dependency check', array( 'back_link' => true ) );
		}
		if ( $theme->name !== 'Twenty Twenty-Two' || $theme->parent_theme !== 'Twenty Twenty-Two' ) { //phpcs:ignore
			$required_theme_name = 'Twenty Twenty-Two';
			$theme               = wp_get_theme();
			add_action(
				'admin_notices',
				function () use ( $required_theme_name ) {
					echo wp_kses_post(
						'<div class="notice notice-warning is-dismissible">
						<p>' . esc_html( sprintf( 'This plugin works best with the "%s" theme. You are currently using "%s". Some features may not work as expected.', $required_theme_name, wp_get_theme()->get( 'Name' ) ) ) . '</p>
					  </div>'
					);
				}
			);

		}

		self::create_custom_table();
	}

	/**
	 * Function to create custom table
	 */
	public static function create_custom_table() {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		global $salsisync_db_version;
		$installed_ver = get_option( 'salsisync_db_version' );

		if ( $installed_ver !== $salsisync_db_version ) {

			$table_name = $wpdb->prefix . 'custom_table';

			$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		name tinytext NOT NULL,
		text text NOT NULL,
		url varchar(100) DEFAULT '' NOT NULL,
		PRIMARY KEY  (id)
		) $charset_collate;";

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( $sql );

			update_option( 'salsisync_db_version', $salsisync_db_version );
		}
	}
	/**
	 * Create a table for bulk insert logs
	 */
	public static function salsisync__create_bulk_insert_log_table() {
		global $wpdb;
		$table_name      = $wpdb->prefix . 'salsisync__bulk_insert_logs';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			bulk_insert_log_id varchar(20) NOT NULL,
			log_data longtext NOT NULL,
			status varchar(50) NULL, 
			log_time datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}
	/**
	 * Create a folder and file for salsi sync
	 * in the uploads directory
	 *
	 * @return void
	 */
	public static function salsisync__create_salsi_sync_folder_and_file() {
		$upload_dir                    = wp_upload_dir(); // Get WordPress upload directory information.
		$base_dir                      = trailingslashit( $upload_dir['basedir'] ); // Get the filesystem path.
		$file_path                     = $base_dir . 'salsi-sync/data/salsify/api-first-page-response.json';
		$file_path_for_updated_content = $base_dir . 'salsi-sync/data/salsify/api-update-page-response.json';
		// Create the JSON file if it doesn't exist.
		if ( ! file_exists( $file_path ) ) {
			wp_mkdir_p( dirname( $file_path ) );
			// Prepare the initial content.
			$initial_content = wp_json_encode(
				array(
					'message' => 'This is the first response from the Salsify API',
					'data'    => array(),
				),
				JSON_PRETTY_PRINT
			);

			// Write the initial content to the file.
			file_put_contents( $file_path, $initial_content ); //phpcs:ignore
		}
		if ( ! file_exists( $file_path_for_updated_content ) ) {
			wp_mkdir_p( dirname( $file_path_for_updated_content ) );
			// Prepare the initial content.
			$initial_content = wp_json_encode(
				array(
					'message' => 'This is the first response from the Salsify update request',
					'data'    => array(),
				),
				JSON_PRETTY_PRINT
			);

			// Write the initial content to the file.
			file_put_contents( $file_path_for_updated_content, $initial_content ); //phpcs:ignore
		}
	}
}
