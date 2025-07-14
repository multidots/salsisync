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
 * Summary.
 *
 * @since Version 3 digits
 */
class Options {

	use Singleton;

	/**
	 * General tab class initiator
	 *
	 * @var [type]
	 */
	public $settings_tab_general;
	/**
	 * Data tab class initiator
	 *
	 * @var [type]
	 */
	public $setting_tab_data;
	/**
	 * Sync tab class initiator
	 *
	 * @var [type]
	 */
	public $settings_tab_sync;
	/**
	 * Template tab class initiator
	 *
	 * @var [type]
	 */
	public $settings_tab_template;
	/**
	 * Help tab class initiator
	 *
	 * @var [type]
	 */
	public $settings_tab_help;

	/**
	 * Construct of Class
	 */
	public function __construct() {
		if ( is_admin() ) {
			add_action( 'admin_menu', array( $this, 'salsisync_create_menu' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'salsisync_option_scripts' ) );
		}
		/**
		 * Initialize the settings tabs
		 */
		$this->settings_tab_general  = Settings_Tab_General::get_instance();
		$this->setting_tab_data      = Settings_Tab_Data::get_instance();
		$this->settings_tab_sync     = Settings_Tab_Sync::get_instance();
		$this->settings_tab_template = Settings_Tab_Template::get_instance();
		$this->settings_tab_help     = Settings_Tab_Help::get_instance();
		Settings_Tab_Sync_Check_For_Update::get_instance();
		Settings_Tab_Sync_Product_Sync_For_Update::get_instance();
		Settings_Tab_Sync_Product_Sync_For_Insert::get_instance();
	}
	/**
	 * Add scripts and style
	 */
	public function salsisync_option_scripts() {
		wp_enqueue_script( 'ajax-insert-data', SALSI_SYNC_URL . 'dist/js/ajax-insert-data.js', array( 'jquery' ), '1.0', true );
		/**
		 * Localize the script with new data
		 * Pass the admin-ajax.php URL and nonce for security
		 */
		wp_localize_script(
			'ajax-insert-data',
			'salsisync_settings_ajax',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'salsisync_settings_nonce' ),
			)
		);
	}
	/**
	 * Create a menu page for salsisync.
	 */
	public function salsisync_create_menu() {
		add_menu_page(
			__( 'Salsi Sync', 'salsisync' ),
			__( 'Salsi Sync', 'salsisync' ),
			'manage_options',
			'salsisync',
			array( $this, 'salsisync_settings_page__content' ),
			// 'dashicons-update-alt',
			SALSI_SYNC_LOGO_ICON,
			2
		);
	}
	/**
	 * Saslify Bridge settings page tabs content
	 */
	public function salsisync_settings_page__content() {
		$ajax_data_insert_running_status = get_option( 'salsisync_ajax_data_insert_running_status' );
		$set_read_only                   = ( 'true' === $ajax_data_insert_running_status || true === $ajax_data_insert_running_status ) ? 'setdisabled' : '';
		echo '<div class="salsisync-product">';
		echo '<div class="salsisync-product__header">
		<h2 class="salsisync-product__header-title">SalsiSync</h2>
		</div>';
		$active_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'general'; //phpcs:ignore
		echo '<div class="salsisync-product__nav-wrap">';
		echo '<div class="nav-tab-wrapper">';
		echo '<a href="?page=salsisync&tab=general" class="nav-tab ' . ( 'general' === $active_tab ? 'nav-tab-active' : '' ) . '"><span>' . esc_html__( 'General', 'salsisync' ) . '</span></a>';
		echo '<a href="?page=salsisync&tab=data" class="nav-tab ' . ( 'data' === $active_tab ? 'nav-tab-active' : '' ) . '"><span>' . esc_html__( 'Data', 'salsisync' ) . '</span></a>';
		echo '<a href="?page=salsisync&tab=sync" class="' . esc_attr( $set_read_only ) . '  nav-tab sync-tab ' . ( 'sync' === $active_tab ? 'nav-tab-active' : '' ) . '"><span>' . esc_html__( 'Sync Product', 'salsisync' ) . '</span></a>';
		echo '<a href="?page=salsisync&tab=template" class="nav-tab ' . ( 'template' === $active_tab ? 'nav-tab-active' : '' ) . '"><span>' . esc_html__( 'Template', 'salsisync' ) . '</span></a>';
		echo '<a href="?page=salsisync&tab=help" class="nav-tab help ' . ( 'help' === $active_tab ? 'nav-tab-active' : '' ) . '"><span>' . esc_html__( 'Help', 'salsisync' ) . '</span></a>';
		echo '</div>'; // end of nav-tab-wrapper.
		/**
		 * Display content based on the active tab
		 */
		echo '<div class="salsisync-product__tab-content">';
		if ( 'general' === $active_tab ) {
			$this->settings_tab_general->salsisync_settings_tab_content__general();
		} elseif ( 'data' === $active_tab ) {
			$this->setting_tab_data->salsisyncsettings_tab_content__data();
		} elseif ( 'sync' === $active_tab ) {
			$this->settings_tab_sync->salsisyncsettings_tab_content__sync();
		} elseif ( 'template' === $active_tab ) {
			$this->settings_tab_template->salsisyncsettings_tab_content__template();
		} elseif ( 'help' === $active_tab ) {
			$this->settings_tab_help->salsisyncsettings_tab_content__help();
		}
		echo '</div>'; // end of salsisync-product__tab-content.
		echo '</div>';
		echo '</div>';
		echo '<div class="salsisync-product__copyright"><p>Crafted by the experts at <a href="https://www.multidots.com/" target="_blank">Multidots</a>, designed for professionals who build with WordPress.</p></div>';
	}
}
