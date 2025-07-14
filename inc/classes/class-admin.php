<?php
/**
 * The admin-specific functionality of the plugin.
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
 * Main class file.
 */
class Admin {

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
		if ( defined( 'SALSI_SYNC_VERSION' ) ) {
			$this->version = SALSI_SYNC_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->setup_admin_hooks();
	}
	/**
	 * Function is used to define admin hooks.
	 *
	 * @since   1.0.0
	 */
	public function setup_admin_hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'salsisync_enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'salsisync_enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'salsisync_theme_options_enqueue_scripts' ) );
		add_filter( 'plugin_row_meta', array( $this, 'salsisync_add_view_details_link' ), 10, 2 );
	}
	/**
	 * Add view details linked to the plugin row meta.
	 *
	 * @param [type] $links The existing plugin links.
	 * @param [type] $file The plugin file name.
	 * @return url
	 */
	public function salsisync_add_view_details_link( $links, $file ) {
		// Define the plugin file name (adjust this to your plugin's file).
		$plugin_file = 'salsisync/md-salsify.php';  // Replace with your plugin's file path.

		// Check if we're working with the correct plugin.
		if ( $file === $plugin_file ) {
			// Define your custom link URL (this can point to a page with more details).
			$custom_link = '<a href="https://www.multidots.com/salsisync/" target="_blank">View Details</a>';

			if ( isset( $links[2] ) && strpos( $links[2], 'plugin site' ) !== false ) {
				$links[2] = $custom_link;  // Replace the existing link if it exists.
			} else {
				$links[] = $custom_link;    // Add the link if it's not there.
			}
		}
		return $links;
	}


	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function salsisync_enqueue_styles() {
		wp_enqueue_style( 'salsisync', SALSI_SYNC_URL . 'assets/build/admin.css', array(), $this->version, 'all' );

		//phpcs:ignore wp_enqueue_style( 'salsisync-tailwind-cdn', 'https://cdn.tailwindcss.com', array(), $this->version, 'all' );
	}

	/**
	 * Enqueue script for AJAX handling.
	 */
	public function salsisync_theme_options_enqueue_scripts() {
		wp_enqueue_script( 'ajax-insert-data', SALSI_SYNC_URL . 'dist/js/ajax-insert-data.js', array( 'jquery' ), '1.0', true );

		// Pass the admin-ajax.php URL and nonce for security.
		wp_localize_script(
			'ajax-insert-data',
			'theme_options_ajax',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'theme_options_nonce' ),
			)
		);
	}
	//phpcs:ignore add_action('admin_enqueue_scripts', 'theme_options_enqueue_scripts');

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function salsisync_enqueue_scripts() {
		wp_enqueue_script( 'salsisync', SALSI_SYNC_URL . 'assets/build/admin.js', array( 'jquery' ), $this->version, false );

		wp_localize_script(
			'salsisync',
			'siteConfig',
			array(
				'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
				'ajax_nonce' => wp_create_nonce( 'loadmore_post_nonce' ),
			)
		);
	}
}
