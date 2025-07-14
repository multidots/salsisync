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
class Settings_Tab_Help {

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
		if ( is_admin() ) {
			add_action( 'admin_init', array( $this, 'salsisync_register_settings__group__section__fields' ) );
		}
	}
	/**
	 * Register settings group, section and fields
	 */
	public function salsisync_register_settings__group__section__fields() {
		register_setting( 'salsisync_template__help_group', 'salsisync_help__section', 'sanitize_text_field' );
		add_settings_section( 'salsisync_template_page__help', esc_html__( 'Help', 'salsisync' ), null, 'salsisync_tab_help' );
	}
	/**
	 * Help tab content
	 */
	public function salsisyncsettings_tab_content__help() {
		/**
		 * If data insertion is running then show the notice message
		 */
		if ( 'true' === get_option( 'salsisync_ajax_data_insert_running_status' ) || true === get_option( 'salsisync_ajax_data_insert_running_status' ) ) {
			?>
			<div class="notice notice-error salsisyncnotice_message"><p><?php esc_html_e( '❗️Data Insertion is Running. Please wait.', 'salsisync' ); ?></p></div>
			<?php

		}
		?>
		<div class="salsisync-template__form salsisync-product__form salsisync__help_tab_content">
			<?php
				do_settings_sections( 'salsisync_tab_help' );
			?>
			<div>
				<p>
					<?php esc_html_e( 'Salsi Sync is a powerful plugin that enables WooCommerce site owners to synchronize their products from the Salsify API to WooCommerce effortlessly. This plugin simplifies product updates, image synchronization, custom data mappings, and more, providing a complete integration solution between Salsify and WooCommerce.', 'salsisync' ); ?>
				</p>
			</div>
			<div class="salsisync__help_accordion">
				<?php
					$this->salsisync__help__1();
					$this->salsisync__help__2();
					$this->salsisync__help__3();
				?>
			</div>
		</div>
		<?php
	}
	/**
	 * Help content 1
	 */
	public function salsisync__help__1() {
		?>
		<div class="accordion-item">
			<div class="accordion-header">
				<span class="">
					<?php esc_html_e( 'How do I configure the Salsify API?', 'salsisync' ); ?>
				</span>
				<span class="icon dashicons dashicons-arrow-down-alt2"></span>
			</div>
			<div class="accordion-content">
				<?php
					esc_html_e( 'In the plugin settings, you can enter your Salsify API credentials, including API token and organization key, to connect with Salsify.', 'salsisync' );
				?>
			</div>
		</div>
		<?php
	}
	/**
	 * Help content 2
	 */
	public function salsisync__help__2() {
		?>
		<div class="accordion-item">
			<div class="accordion-header">
				<span class="">
					<?php esc_html_e( 'How does the custom data mapping work?', 'salsisync' ); ?>
				</span>
				<span class="icon dashicons dashicons-arrow-down-alt2"></span>
			</div>
			<div class="accordion-content">
				<?php
					esc_html_e( 'Custom data mapping allows you to map specific fields from the Salsify API data to custom WooCommerce fields, so additional information from Salsify is displayed on your WooCommerce products.', 'salsisync' );
				?>
			</div>
		</div>
		<?php
	}
	/**
	 * Help content 3
	 */
	public function salsisync__help__3() {
		?>
		<div class="accordion-item">
			<div class="accordion-header">
				<span class="">
					<?php esc_html_e( 'Will this plugin overwrite existing WooCommerce products?', 'salsisync' ); ?>
				</span>
				<span class="icon dashicons dashicons-arrow-down-alt2"></span>
			</div>
			<div class="accordion-content">
				<?php
					esc_html_e( 'No, the plugin is designed to skip existing products if they are already synced unless you choose to update them. Only new or updated products from Salsify will be inserted.', 'salsisync' );
				?>
			</div>
		</div>
		<?php
	}
}
