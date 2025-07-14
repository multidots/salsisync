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
class Settings_Tab_Sync {

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
	 * Store the product name key for the sync
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $product_name_key    salsify product name key.
	 */
	public $product_name_key = '';
	/**
	 * Store the product category key for the sync
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $product_category_key    salsify product category key.
	 */
	public $product_category_key = '';
	/**
	 * Store the product main image key for the sync
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $product_main_image_key    salsify product main image key.
	 */
	public $product_main_image_key = '';
	/**
	 * Store the product description key for the sync
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $product_description_key    salsify product description key.
	 */
	public $product_description_key = '';
	/**
	 * Store the product description key for the sync
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $salsisync_product_description__key_required    salsify product description key.
	 */
	public $salsisync_product_description__key_required = '';
	/**
	 * Store the product name key map status
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      boolean    $salsisync_product_name__key__map_status    salsify product name key map status.
	 */
	public $salsisync_product_name__key__map_status = false;
	/**
	 * Store the product category key map status
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      boolean    $salsisync_product_category__key__map_status    salsify product category key map status.
	 */
	public $salsisync_product_category__key__map_status = false;
	/**
	 * Store the product main image key map status
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      boolean    $salsisync_product_main_image__key__map_status    salsify product main image key map status.
	 */
	public $salsisync_product_main_image__key__map_status = false;
	/**
	 * Store the product description key map status
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      boolean    $salsisync_product_description__key__map_status    salsify product description key map status.
	 */
	public $salsisync_product_description__key__map_status = false;
	/**
	 * Store the new products for the sync insert process
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      array    $new_products    salsify new products from the api.
	 */
	public $new_products = array();
	/**
	 * Store the update available products for the sync update process
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      array    $update_available_products    salsify update available products from the api.
	 */
	public $update_available_products = array();
	/**
	 * Store the total number of product that available in salsify.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      array    $update_available_products    salsify update available products from the api.
	 */
	public $products_meta_in_salsify = array();

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

		$this->salsisync_product_name__key__map_status        = get_option( 'salsisync_product_name__key__map_status' );
		$this->salsisync_product_category__key__map_status    = get_option( 'salsisync_product_category__key__map_status' );
		$this->salsisync_product_main_image__key__map_status  = get_option( 'salsisync_product_main_image__key__map_status' );
		$this->salsisync_product_description__key__map_status = get_option( 'salsisync_product_description__key__map_status' );

		$this->new_products              = json_decode( get_option( 'salsisync_salsify_updated_products' ) );
		$this->update_available_products = json_decode( get_option( 'salsisync_salsify_new_products' ) );

		if ( is_admin() ) {
			/**
			 * Ajax Handle for [salsisync_get_product_data_count] action of jquery
			 * Count the total product from the JSON file
			 */
			add_action( 'wp_ajax_nopriv_salsisync_get_product_data_count', array( $this, 'salsisync_get_product_data_count' ) );
			add_action( 'wp_ajax_salsisync_get_product_data_count', array( $this, 'salsisync_get_product_data_count' ) );
			/**
			 * Ajax Handle for [salsisync_insert_data_item] action of jquery
			 * Insert data into the WooCommerce
			 */
			add_action( 'wp_ajax_nopriv_salsisync_insert_data_item', array( $this, 'salsisync_insert_data_item' ) );
			add_action( 'wp_ajax_salsisync_insert_data_item', array( $this, 'salsisync_insert_data_item' ) );
			/**
			 * Test insert
			 */
			add_action( 'wp_ajax_nopriv_salsisync_test_insert_data_item', array( $this, 'salsisync_test_insert_data_item' ) );
			add_action( 'wp_ajax_salsisync_test_insert_data_item', array( $this, 'salsisync_test_insert_data_item' ) );
			/**
			 * Test insert status
			 */
			add_action( 'wp_ajax_nopriv_salsisync_update_test_data_insert_status', array( $this, 'salsisync_update_test_data_insert_status' ) );
			add_action( 'wp_ajax_salsisync_update_test_data_insert_status', array( $this, 'salsisync_update_test_data_insert_status' ) );
			/**
			 * Ajax Handle for [salsisync_set_ajax_running_status] action
			 * Set the AJAX running status for bulk data insertion
			 */
			add_action( 'wp_ajax_nopriv_salsisync_set_ajax_running_status', array( $this, 'salsisync_set_ajax_running_status' ) );
			add_action( 'wp_ajax_salsisync_set_ajax_running_status', array( $this, 'salsisync_set_ajax_running_status' ) );
			/**
			 * Ajax Handle for [salsisync__custom_mapping_data_changes_log] action
			 */
			add_action( 'wp_ajax_nopriv_salsisync__reset_custom_data_mapping_value', array( $this, 'salsisync__reset_custom_data_mapping_value' ) );
			add_action( 'wp_ajax_salsisync__reset_custom_data_mapping_value', array( $this, 'salsisync__reset_custom_data_mapping_value' ) );
			/**
			 * Ajax Handle for [salsisync__fetch_and_show_log_data] action
			 * Fetch and show log data from the database
			 */
			add_action( 'wp_ajax_nopriv_salsisync__fetch_and_show_log_data', array( $this, 'salsisync__fetch_and_show_log_data' ) );
			add_action( 'wp_ajax_salsisync__fetch_and_show_log_data', array( $this, 'salsisync__fetch_and_show_log_data' ) );
		}
		$this->product_name_key                            = get_option( 'salsisync_product_name__key' );
		$this->product_category_key                        = get_option( 'salsisync_product_category__key' );
		$this->product_main_image_key                      = get_option( 'salsisync_product_main_image__key' );
		$this->product_description_key                     = get_option( 'salsisync_product_description__key' );
		$this->salsisync_product_description__key_required = get_option( 'salsisync_product_description__key_required' );

		if ( is_admin() ) {
			add_action( 'admin_init', array( $this, 'salsisync_register_settings__product__sync__fields' ) );
		}
		$this->salsisync_get_product_count_from_local_file();
	}
	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function salsisync_get_product_count_from_local_file() {
		$json_file_path = SALSI_SYNC_UPLOAD_DIR . 'data/salsify/api-first-page-response.json';
		if ( file_exists( $json_file_path ) ) {
			/**
			 * Get the contents of the JSON file
			 */
			$json_data = file_get_contents( $json_file_path ); // phpcs:ignore
			/**
			 * Decode the JSON data into a PHP array
			 */
			$products                       = json_decode( $json_data, true );
			$products_meta_arr              = is_array( $products ) && array_key_exists( 'meta', $products ) ? $products['meta'] : array();
			$this->products_meta_in_salsify = $products_meta_arr;
		}
	}
	/**
	 * Settings fields regarding number of products to fetch.
	 *
	 * @return void
	 */
	public function salsisync_register_settings__product__sync__fields() {
		$total_entries = array_key_exists( 'total_entries', $this->products_meta_in_salsify ) ? $this->products_meta_in_salsify['total_entries'] : '';
		/* translators: %s: Total number of products */
		$total_entries_text = sprintf( esc_html__( 'Fetch All %s Products', 'salsisync' ), $total_entries );
		/**
		 * Data Settings
		 */
		register_setting( 'salsisync_product_sync_mapping__settings_group', 'salsisync_all_product_to_sync__key', 'sanitize_text_field' );
		register_setting( 'salsisync_product_sync_mapping__settings_group', 'salsisync_number_of_product_to_sync__key', 'sanitize_text_field' );
		add_settings_section( 'salsisync_product_sync_custom_input__section', esc_html__( 'Sync Products Option', 'salsisync' ), null, 'salsisync_tab__product_sync_options' );
		// Field for checkbox of all product.
		add_settings_field(
			'salsisync_all_product_to_sync__key',
			esc_html( $total_entries_text ) . '<span class="salsisync-product__help-tip salsisync-product__help-desc-tip"><span class="salsisync-product__help-inner-tip" tabindex="0" aria-label="' . esc_attr__( 'Fetch all products from the Salsify API.', 'salsisync' ) . '"></span></span>',
			array( $this, 'salsisync_product__sync_all__callback' ),
			'salsisync_tab__product_sync_options',
			'salsisync_product_sync_custom_input__section',
			array(
				'label_for' => 'salsisync_all_product_to_sync__key',
				'desc'      => esc_html__( 'Fetch All Product', 'salsisync' ),
				'tip'       => esc_attr__( 'Fetch all products from the Salsify API', 'salsisync' ),
				'desc_tip'  => true,
				'autoload'  => false,
			)
		);
		// Field for custom input regarding number of product to fetch.
		add_settings_field(
			'salsisync_number_of_product_to_sync__key',
			esc_html__( 'Number of product to sync', 'salsisync' ) . '<span class="salsisync-product__help-tip"><span class="salsisync-product__help-inner-tip" tabindex="0" aria-label="' . esc_attr__( 'Enter the number of product to sync from Salsify API.', 'salsisync' ) . '"></span></span>',
			array( $this, 'salsisync_product__sync_custom_input__callback' ),
			'salsisync_tab__product_sync_options',
			'salsisync_product_sync_custom_input__section',
			array(
				'label_for' => 'salsisync_number_of_product_to_sync__key',
				'desc'      => esc_html__( 'Enter the product name key from the Salsify API.', 'salsisync' ),
				'tip'       => esc_attr__( 'Enter the product name key from the Salsify API.', 'salsisync' ),
				'desc_tip'  => true,
				'autoload'  => false,
			)
		);
	}
	/**
	 * Handle All product checkbox value.
	 * Tab - Sync Data
	 * Section ID - [sync-data]
	 */
	public function salsisync_product__sync_all__callback() {
		$value         = get_option( 'salsisync_all_product_to_sync__key', '' );
		$set_read_only = ( 'true' === get_option( 'salsisync_ajax_data_insert_running_status' ) ) ? 'disabled' : '';
		echo '<input type="checkbox" ' . esc_attr( $set_read_only ) . ' name="salsisync_all_product_to_sync__key" value="1" ' . checked( 1, $value, false ) . ' />';
	}
	/**
	 * Handle Custom input value regarding product fetch.
	 * Tab - sync-data
	 * Section ID - [sync-data]
	 */
	public function salsisync_product__sync_custom_input__callback() {
		$value         = get_option( 'salsisync_number_of_product_to_sync__key' );
		$set_read_only = ( 'true' === get_option( 'salsisync_ajax_data_insert_running_status' ) ) ? 'readonly' : '';
		echo '<input required class="regular-text" type="number" ' . esc_attr( $set_read_only ) . ' min="100" max="' . esc_html( $this->products_meta_in_salsify['total_entries'] ) . '" placeholder="' . esc_html__( 'Enter the number of product to sync from Salsify API.', 'salsisync' ) . '" name="salsisync_number_of_product_to_sync__key" value="' . esc_html( $value ? $value : 100 ) . '">';
	}
	/**
	 * Saslify Data Settings Tab
	 */
	public function salsisyncsettings_product_mapping__sync() {
		$total_entries = array_key_exists( 'total_entries', $this->products_meta_in_salsify ) ? $this->products_meta_in_salsify['total_entries'] : '';
		/**
		 * If data insertion is running then show the notice message
		 */
		if ( 'true' === get_option( 'salsisync_ajax_data_insert_running_status' ) || true === get_option( 'salsisync_ajax_data_insert_running_status' ) ) {
			?>
			<div class="notice notice-error salsisyncnotice_message"><p><?php esc_html_e( '❗️Data Insertion is Running. Please wait.', 'salsisync' ); ?></p></div>
			<?php
		}
		/**
		 * If API key is not valid then show the notice message
		 */
		if ( 'false' === get_option( 'salsify_api_key_valid' ) || false === get_option( 'salsify_api_key_valid' ) ) {
			?>
				<div class="notice notice-error salsisyncnotice_message"><p><?php esc_html_e( 'Connect API First', 'salsisync' ); ?></p></div>
			<?php
			return;
		}
		/**
		 * If nonce is not valid then show the notice message
		 */
		if ( isset( $_POST['salsisyncsettings_product_mapping__sync_nonce'] ) && ! wp_verify_nonce( sanitize_key( $_POST['salsisyncsettings_product_mapping__sync_nonce'] ), 'salsisyncsettings_product_mapping__sync' ) ) {
			?>
			<div class="notice notice-error salsisyncnotice_message"><p><?php esc_html_e( '❌ Wrong Validation Request', 'salsisync' ); ?></p></div>
			<?php
		}
		/**
		 * Verify the nonce and update the value to option table
		 */
		if ( isset( $_POST['salsisyncsettings_product_mapping__sync_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['salsisyncsettings_product_mapping__sync_nonce'] ), 'salsisyncsettings_product_mapping__sync' ) ) {

			// Sanitize and save the input fields.
			$salsisync_all_product_to_sync__key       = filter_input( INPUT_POST, 'salsisync_all_product_to_sync__key', FILTER_SANITIZE_SPECIAL_CHARS );
			$salsisync_number_of_product_to_sync__key = filter_input( INPUT_POST, 'salsisync_number_of_product_to_sync__key', FILTER_SANITIZE_SPECIAL_CHARS );

			$checkbox_value = isset( $salsisync_all_product_to_sync__key ) ? 1 : ''; // Set to 1 if checked, empty if unchecked.
			// Update the option in the database.
			update_option( 'salsisync_all_product_to_sync__key', $checkbox_value );

			if ( isset( $salsisync_number_of_product_to_sync__key ) ) {
				update_option( 'salsisync_number_of_product_to_sync__key', sanitize_text_field( wp_unslash( $salsisync_number_of_product_to_sync__key ) ) );
			}
			?>
			<div class="notice notice-success salsisyncnotice_message is-dismissible"><p><?php esc_html_e( '✅ Data Updated', 'salsisync' ); ?></p></div>
			<?php
		}

		?>
		<p class="salsisync-product__sync-note">
			<?php
			/* translators: %s: Total number of products */
			printf( esc_html__( 'You have %s products in your Salsify Account.', 'salsisync' ), esc_html( $total_entries ) );
			?>
		</p>
		<form class="salsisync-product_sync_custom_input__form" method="post" action="admin.php?page=salsisync&tab=sync">
		<?php
			wp_nonce_field( 'salsisyncsettings_product_mapping__sync', 'salsisyncsettings_product_mapping__sync_nonce' );
			settings_fields( 'salsisync_product_sync_mapping__settings_group' );
			do_settings_sections( 'salsisync_tab__product_sync_options' );
			submit_button(
				'',
				'primary',
				esc_html__( 'Save Changes', 'salsisync' ),
				true,
				array_filter( // Filters out attributes with empty values.
					array(
						'id'       => 'salsisync_product_sync_custom_input__submit',
						'class'    => 'button button-primary salsisync-product__sync-button',
						'disabled' => 'true' === get_option( 'salsisync_ajax_data_insert_running_status' ) ? 'disabled' : null, // Use null instead of '' to exclude the attribute.
					)
				)
			);
		?>
		</form>
		<?php
	}
	/**
	 * Set ajax running status for bulk data insertion
	 */
	public function salsisync_set_ajax_running_status() {
		check_ajax_referer( 'salsisync_settings_nonce', 'nonce' );
		global $wpdb;
		$table_name = $wpdb->prefix . 'salsisync__bulk_insert_logs';

		if ( 'POST' === $_SERVER['REQUEST_METHOD'] ) { // phpcs:ignore
			$bulk_insert_log_value = filter_input( INPUT_POST, 'bulkInsertLog', FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY );
			$bulk_insert_id        = isset( $_POST['bulkInsertId'] ) ? sanitize_text_field( wp_unslash( $_POST['bulkInsertId'] ) ) : '';
			$bulk_insert_log       = isset( $bulk_insert_log_value )
				? wp_unslash( $bulk_insert_log_value )
				: array();

			if ( is_array( $bulk_insert_log ) ) {
				$bulk_insert_log = array_map(
					function ( $item ) {
						return is_array( $item )
						? array_map( 'sanitize_text_field', $item )
						: sanitize_text_field( $item );
					},
					$bulk_insert_log
				);
			}
		}

		if ( '' === $bulk_insert_id || '' === $bulk_insert_log ) {
			$log_insert_status = false;
		} elseif ( $bulk_insert_id && $bulk_insert_log ) {
			// @codingStandardsIgnoreStart
			$log_insert_status = $wpdb->query(
				$wpdb->prepare(
					"INSERT INTO $table_name (bulk_insert_log_id, log_data) VALUES (%s, %s)",
					esc_sql( $bulk_insert_id ),
					wp_json_encode( $bulk_insert_log )
				)
			);
			// @codingStandardsIgnoreEnd
		}

		$get_status_from_ajax = isset( $_POST['status'] ) ? sanitize_key( wp_unslash( $_POST['status'] ) ) : '';
		if ( 'true' === $get_status_from_ajax ) {
			$status = update_option( 'salsisync_ajax_data_insert_running_status', 'true' );
		} else {
			$status = update_option( 'salsisync_ajax_data_insert_running_status', 'false' );
		}
		wp_send_json(
			array(
				'status'            => $status,
				'log_insert_status' => $log_insert_status,
			)
		);
		wp_die();
	}
	/**
	 * Reset custom data mapping value.
	 */
	public function salsisync__reset_custom_data_mapping_value() {
		check_ajax_referer( 'salsisync_settings_nonce', 'nonce' );
		//phpcs:ignore $status = update_option( 'salsisync__custom_mapping_data_changes_log', array() );
		$status = update_option( 'salsisync__custom_data_mapping_fields_update_status', false );
		wp_send_json(
			array(
				'status' => $status,
			)
		);
		wp_die();
	}
	/**
	 * Check plugin from site
	 *
	 * @param string $plugin_base_name Plugin base name.
	 * @return boolean
	 */
	public function salsisync_check_plugin_form_site( $plugin_base_name = '' ) {
		wp_cache_flush();
		$installed_plugins = get_plugins();
		if ( array_key_exists( $plugin_base_name, $installed_plugins ) ) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * Saslify Sync Settings Tab
	 *
	 * @return null
	 */
	public function salsisyncsettings_tab_content__sync() {
		$woocommerce_main_file = 'woocommerce/woocommerce.php';
		?>
		<?php if ( $this->salsisync_check_plugin_form_site( $woocommerce_main_file ) === false ) : ?>
			<div class="notice notice-error salsisyncnotice_message">
				<p><?php esc_html_e( 'WooCommerce needs to be Install and Active to sync data.', 'salsisync' ); ?></p>
			</div>
		<?php elseif ( ! class_exists( 'woocommerce' ) ) : ?>
			<div class="notice notice-error salsisyncnotice_message">
				<p><?php esc_html_e( 'WooCommerce needs to be active to sync data.', 'salsisync' ); ?></p>
			</div>
		<?php endif; ?>
		<?php
		/**
		 * Show the warning message if the API key is not connected
		 */
		if ( get_option( 'salsify_api_key_valid' ) === 'false' || get_option( 'salsify_api_key_valid' ) === false ) {
			?>
			<div class="notice notice-error salsisyncnotice_message">
				<p><?php esc_html_e( 'Connect API First', 'salsisync' ); ?></p>
			</div>
			<?php
			return;
		}
		?>

		<?php
		/**
		 * Show the warning message if the required fields are not mapped
		 */
		if ( ! $this->salsisync_product_name__key__map_status ||
			! $this->salsisync_product_category__key__map_status ||
			! $this->salsisync_product_main_image__key__map_status ||
			! $this->salsisync_product_description__key__map_status
		) {
			?>
			<div class="notice notice-error salsisyncnotice_message">
				<p><?php esc_html_e( 'Please map all the required fields first.', 'salsisync' ); ?></p>
			</div>
			<?php
		} elseif ( get_option( 'salsify_api_key_valid' ) === 'false' || get_option( 'salsify_api_key_valid' ) === false ) {
			?>
				<div class="notice notice-error salsisyncnotice_message">
					<p><?php esc_html_e( 'Connect API First', 'salsisync' ); ?></p>
				</div>
			<?php
			return;
		}
		?>
		<div class="salsisync-product__sync-wrap">
			<div class="salsisync-product__sync-tab">
				<a href="#" class="salsisync-product__sub-tab salsisync-product__sub-tab-active" data-sub-tab="sync-data"><span><?php esc_html_e( 'Sync Data', 'salsisync' ); ?></span></a>
				<a href="#" class="salsisync-product__sub-tab" data-sub-tab="sync-update"><span><?php esc_html_e( 'Update', 'salsisync' ); ?></span></a>
				<a href="#" class="salsisync-product__sub-tab" data-sub-tab="sync-logs"><span><?php esc_html_e( 'Logs', 'salsisync' ); ?></span></a>
			</div>
			<div class="salsisync-product__tab-panel active" id="sync-data">
				<?php
				/**
				 * Form for the input regarding the number of data needed to synced
				 * 1. Checkbox Input to fetch all prodcut.
				 * 2. If unchecked then show the input box to add custom number to fetch
				 * if if is below 500 then use default API to fetch
				 * If more than 500 use export API to fetch data
				 */
				$this->salsisyncsettings_product_mapping__sync();
				?>
				
				<?php
				/**
				 * Show active button if all the required fields are mapped
				 * Otherwise show the disabled button
				 */
				if (
					$this->salsisync_product_name__key__map_status &&
					$this->salsisync_product_category__key__map_status &&
					$this->salsisync_product_main_image__key__map_status &&
					$this->salsisync_product_description__key__map_status
				) {
					?>
					<?php if ( class_exists( 'woocommerce' ) ) : ?>
						<?php
						if ( 'true' === get_option( 'salsisync_ajax_data_insert_running_status' ) ) :
							?>
							<div class="salsisync-product__sync-note">
								<?php esc_html_e( '❗️ Data Insertion is running. Please wait.', 'salsisync' ); ?>
							</div>
						<?php else : ?>
						<div class="salsisync-product__sync-note">
							<?php esc_html_e( 'Click the button below to insert data Product to the database.', 'salsisync' ); ?>
						</div>
						<?php endif; ?>
					<?php endif; ?>
					<div class="button-wrapper">
						<div class="left-side-button">
							<?php
							if ( class_exists( 'woocommerce' ) ) :
								$set_read_only                   = ( 'true' === get_option( 'salsisync_ajax_data_insert_running_status' ) ) ? 'disabled' : '';
								$get_custom_mapping_changes_data = get_option( 'salsisync__custom_mapping_data_changes_log', array() );
								/**
								 * Get action parameter from the url.
								 */
								//phpcs:ignore $action = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';
								?>
								<button data-bulk-insert="false" <?php echo esc_attr( $set_read_only ); ?> class="button button-primary" id="insert-data-btn"><?php esc_html_e( 'Insert Data', 'salsisync' ); ?></button>
								<button data-active="false" <?php echo esc_attr( $set_read_only ); ?> class="button button-default" id="test-insert-data-btn"><?php esc_html_e( 'Test Insert', 'salsisync' ); ?></button>
							<?php endif; ?>
						</div>
						<div class="right-side-button">
							<button data-active="false" style="display:none;" class="stop-insertion-button button button-danger" id="stop-insertion-button"><?php esc_html_e( 'Stop Data Insertion', 'salsisync' ); ?></button>
						</div>
					</div>
					<?php
				} else {
					?>
					<button disabled class="button button-primary" id="insert-data-btn"><?php esc_html_e( 'Insert Data', 'salsisync' ); ?></button>
					<button disabled class="button button-default" id="test-insert-data-btn"><?php esc_html_e( 'Test Insert', 'salsisync' ); ?></button>
					<?php
				}
				?>

				<div class="salsisync-product__sync-progress" id="progress"></div>

				<table class="salsisync-product__sync-wrap-table">
					<thead>
						<tr id="salsisync-product__sync-report-title"></tr>
					</thead>
					<tbody>
						<tr>
							<td colspan="2" style="padding:0;border-bottom:none;">
								<div>
									<table class="salsisync-product__sync-report" id="report"></table>
								</div>	
							</td>
						</tr>		
					</tbody>
				</table>
			</div>
			<div class="salsisync-product__tab-panel" id="sync-update">
				<?php echo wp_kses_post( $this->salsisync__sub_tab_update() ); ?>
			</div>
			<div class="salsisync-product__tab-panel" id="sync-logs">
				<?php echo wp_kses_post( $this->salsisync__sub_tab_logs() ); ?>
			</div>
		</div>	
		<?php
	}
	/**
	 * View: Sub Tab Update
	 */
	public function salsisync__sub_tab_update() {
		/**
		 * If all key selected then the all number of product will be checked
		 * otherwise only the selected number of product will be checked
		 */
		$number_of_product_to_sync = get_option( 'salsisync_number_of_product_to_sync__key' );
		$all_product_to_sync       = get_option( 'salsisync_all_product_to_sync__key' );
		$number_of_product_to_sync = ( '1' === $all_product_to_sync ) ? $this->products_meta_in_salsify['total_entries'] : $number_of_product_to_sync;
		?>
		<p class="salsisync-product__sync-note" id="check-for-update-notice" data-needs-to-checked="<?php echo esc_html( $number_of_product_to_sync ); ?>">
			<?php
			/* translators: %s: Total number of products */
			printf( esc_html__( 'Check %s products from Salsify Account.', 'salsisync' ), esc_html( $number_of_product_to_sync ) )
			?>
		</p>
		<div class="button-wrapper">
			<div class="right-side-button">
				<?php
				if (
					$this->salsisync_product_name__key__map_status &&
					$this->salsisync_product_category__key__map_status &&
					$this->salsisync_product_main_image__key__map_status &&
					$this->salsisync_product_description__key__map_status
				) :
					?>

				<button data-active="false" style="display:block;" class="check-for-update-button button button-primary" id="check-for-update-button">
					<?php
					if ( $this->new_products || $this->update_available_products ) {
						esc_html_e( 'Recheck for update', 'salsisync' );
					} else {
						esc_html_e( 'Check for update', 'salsisync' );
					}
					?>
				</button>
				<?php else : ?>
					<button disabled data-active="false" style="display:block;" class="check-for-update-button button button-primary">
						<?php
							esc_html_e( 'Check for update', 'salsisync' );
						?>
					</button>
				<?php endif; ?>		
			</div>
		</div>
		<!-- news changes wrapper div -->
		<div id="new-changes"></div>
		<div class="salsisync-product__new-updated-content">
			<?php
			$updated_products     = json_decode( get_option( 'salsisync_salsify_updated_products' ) );
			$updated_products_arr = $updated_products ? get_object_vars( $updated_products ) : array();
			$new_products         = json_decode( get_option( 'salsisync_salsify_new_products' ) );
			$new_products_arr     = $new_products ? get_object_vars( $new_products ) : array();
			?>
			<div class="salsisync-product__updated-table">
				<div class="salsisync-product__updated-bulk-header">
					<h3><?php esc_html_e( 'Update Available Products', 'salsisync' ); ?></h3>
					<button class="button button-primary <?php echo count( $updated_products_arr ) > 0 ? 'show-bulk-button' : 'hide-bulk-button'; ?>" id="bulk-update"><?php esc_html_e( 'Bulk Update', 'salsisync' ); ?></button>
				</div>
				<table class="salsisync-product__sync-wrap-table" id="updated_product">
					<thead>
						<tr>
							<th><h4><?php esc_html_e( 'ID', 'salsisync' ); ?></h4></th>
							<th><h4><?php esc_html_e( 'Title', 'salsisync' ); ?></h4></th>
							<th><h4><?php esc_html_e( 'Status', 'salsisync' ); ?></h4></th>
						</tr>
					</thead>
					<tbody>
						<?php if ( ! empty( $updated_products ) ) : ?>
							<?php foreach ( $updated_products as $key => $value ) : ?>
								<tr id="<?php echo esc_attr( $key ); ?>">
									<td><?php echo esc_html( $key ); ?></td>
									<td><?php echo esc_html( $value ); ?></td>
									<td>
										<?php esc_html_e( 'Pending', 'salsisync' ); ?>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php else : ?>
							<tr>
								<td class="no-update-product" colspan="3"><?php esc_html_e( 'No Updated Products', 'salsisync' ); ?></td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
			<div class="salsisync-product__new-table">
				<div class="salsisync-product__bulk-insert-header">
					<h3><?php esc_html_e( 'New Products', 'salsisync' ); ?></h3>
					<button class="button button-primary <?php echo count( $new_products_arr ) > 0 ? 'show-bulk-button' : 'hide-bulk-button'; ?>" id="bulk-insert"><?php esc_html_e( 'Bulk Insert', 'salsisync' ); ?></button>
				</div>
				<table class="salsisync-product__sync-wrap-table" id="new_product">
					<thead>
						<tr>
							<th><h4><?php esc_html_e( 'ID', 'salsisync' ); ?></h4></th>
							<th><h4><?php esc_html_e( 'Title', 'salsisync' ); ?></h4></th>
							<th><h4><?php esc_html_e( 'Status', 'salsisync' ); ?></h4></th>
						</tr>
					</thead>
					<tbody>
						<?php if ( ! empty( $new_products ) ) : ?>
							<?php foreach ( $new_products as $key => $value ) : ?>
								<tr id="<?php echo esc_attr( $key ); ?>">
									<td><?php echo esc_html( $key ); ?></td>
									<td><?php echo esc_html( $value ); ?></td>
									<td>
										<?php esc_html_e( 'Pending', 'salsisync' ); ?>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php else : ?>
							<tr>
								<td class="no-new-product" colspan="3"><?php esc_html_e( 'No New Products', 'salsisync' ); ?></td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
		<?php
	}
	/**
	 * View: Sub Tab Logs
	 *
	 * @return void
	 */
	public function salsisync__sub_tab_logs() {
		$insert_logs = $this->salsisync__get_log_data() ? $this->salsisync__get_log_data() : array();
		?>
		<?php if ( count( $insert_logs ) > 0 ) : ?>
		<select class="regular-text" name="log-dropdwon" id="bulk-log-dropdown">
			<option value="">Select Log ID</option>
			<?php foreach ( $insert_logs as $row ) : ?>
				<option value="<?php echo esc_attr( $row['bulk_insert_log_id'] ); ?>"><?php echo esc_html( $this->salsisync__display_readable_unique_id( $row['bulk_insert_log_id'] ) ); ?></option>
			<?php endforeach; ?>
		</select>
		<div id="log-data">
			<div id="log-data-output"></div>
			<table class="salsisync-product__sync-wrap-table">
				<thead>
					<tr>
						<th><h4><?php esc_html_e( 'ID', 'salsisync' ); ?></h4></th>
						<th><h4><?php esc_html_e( 'Log Data', 'salsisync' ); ?></h4></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan="2" style="padding:0;border-bottom:none;">
							<div>
								<table class="salsisync__sync-log" id="log-report-table">
									<tbody></tbody>
								</table>
							</div>	
						</td>
					</tr>		
				</tbody>
			</table>
		</div>
		<?php endif; ?>
		<?php
	}
	/**
	 * Work on: Sub Tab Logs
	 * Get the log ID from the database.
	 *
	 * @return array or string
	 */
	public function salsisync__get_log_data() {

		global $wpdb;
		$table_name = $wpdb->prefix . 'salsisync__bulk_insert_logs';

		// Query to get the first 10 records.
		// @codingStandardsIgnoreStart
		$results = $wpdb->get_results(
			$wpdb->prepare( "SELECT id, bulk_insert_log_id FROM {$table_name} ORDER BY id DESC LIMIT %d", 10 ),
			ARRAY_A // Retrieve the results as an associative array.
		);
		// @codingStandardsIgnoreEnd

		// Display the results.
		if ( ! empty( $results ) ) {
			return $results;
		} else {
			echo esc_html__( 'No records found.', 'salsisync' );
		}
	}
	/**
	 * Work on: Sub Tab Logs
	 * Display the readable unique ID
	 *
	 * @param string $id_string data string to covert into readable format.
	 * @return string
	 */
	public function salsisync__display_readable_unique_id( $id_string = '' ) {
		// Get the saved ID from the options table.
		$unique_id = $id_string;

		if ( $unique_id ) {
			// Extract the date part from the ID (assuming the format ID_YYYYMMDDHHMMSS).
			$date_string = substr( $unique_id, 3 ); // Remove 'ID_' prefix.

			// Convert the date string to a human-readable format.
			$readable_date = \DateTime::createFromFormat( 'YmdHis', $date_string );

			if ( $readable_date ) {
				// Return the readable format (e.g., October 11, 2024 at 2:45 PM).
				return $readable_date->format( 'F j, Y \a\t g:i A' );
			}
		}

		return esc_html__( 'No unique ID found.', 'salsisync' );
	}
	/**
	 * Work on: Sub Tab Logs
	 * Fetch and show log data from the database
	 */
	public function salsisync__fetch_and_show_log_data() {
		check_ajax_referer( 'salsisync_settings_nonce', 'nonce' );
		global $wpdb;
		$table_name = $wpdb->prefix . 'salsisync__bulk_insert_logs';
		$log_id     = isset( $_POST['log_id'] ) ? sanitize_text_field( wp_unslash( $_POST['log_id'] ) ) : '';

		if ( ! empty( $log_id ) ) {
			// @codingStandardsIgnoreStart
			$result = $wpdb->get_row(
				$wpdb->prepare( "SELECT log_data FROM {$table_name} WHERE bulk_insert_log_id = %s", $log_id ),
				ARRAY_A
			);
			// @codingStandardsIgnoreEnd
			if ( $result ) {
				wp_send_json(
					array(
						'status' => 'success',
						'data'   => json_decode( $result['log_data'] ),
					)
				);
			} else {
				wp_send_json(
					array(
						'status' => 'false',
						'data'   => esc_html__( 'No data found.', 'salsisync' ),
					)
				);
			}
		} else {
			wp_send_json(
				array(
					'status' => 'false',
					'data'   => esc_html__( 'No data found.', 'salsisync' ),
				)
			);
		}
		wp_die();
	}
	/**
	 * Work on: Sub Tab Logs
	 * Create a unique ID based on the current date and time
	 *
	 * @return string
	 */
	public function salsisync__create_unique_id_by_time() {
		// Generate a unique ID with the date() function.
		$unique_id = 'ID_' . gmdate( 'YmdHis' );
		return $unique_id;
	}
	/**
	 * Hit Salsify API to fetch the data
	 *
	 * @param integer $limit Number of data to fetch.
	 * @return boolean
	 */
	public function salsisync_fetch_custom_number_of_data_from_salisify_api( $limit = 100 ) {
		// parse $limit to int.
		$limit = intval( $limit );
		/**
		 * Get the stored API token from the WordPress options table.
		 */
		$api_token  = get_option( 'salsisync_api_token__key' );
		$orgs_token = get_option( 'salsisync_orgs__key' );
		/**
		 * Set the parameters for the API request.
		 */
		$per_page       = 100;
		$total_needed   = $limit;
		$page           = 1;
		$all_data       = array();
		$last_meta      = array();
		$count_all_data = count( $all_data );
		/**
		 * Loop through the API pages until we have enough data or there are no more pages.
		 */
		while ( $count_all_data < $total_needed ) {
			$endpoint = "https://app.salsify.com/api/v1/orgs/{$orgs_token}/products?page={$page}&per_page={$per_page}";
			$args     = array(
				'headers' => array(
					'Authorization' => 'Bearer ' . $api_token,
					'Content-Type'  => 'application/json',
				),
				'timeout' => 30,
			);
			$response = wp_remote_get( $endpoint, $args );
			if ( is_wp_error( $response ) ) {
				error_log( 'Salsify API error: ' . $response->get_error_message() ); // phpcs:ignore
				return false;
			}
			$body   = wp_remote_retrieve_body( $response );
			$parsed = json_decode( $body, true );
			if ( empty( $parsed['data'] ) ) {
				break;
			}
			$all_data  = array_merge( $all_data, $parsed['data'] );
			$last_meta = $parsed['meta']; // Save meta from the last page fetched.
			++$page;
		}
		// Trim to exactly 450 items if more.
		$all_data     = array_slice( $all_data, 0, $total_needed );
		$full_payload = array(
			'data' => $all_data,
			'meta' => $last_meta,
		);
		// Prepare file path.
		$upload_dir = wp_upload_dir();
		$save_dir   = trailingslashit( $upload_dir['basedir'] ) . 'salsi-sync/data/salsify';
		if ( ! file_exists( $save_dir ) ) {
			wp_mkdir_p( $save_dir );
		}
		$save_path = $save_dir . '/all-page-response.json';
		/**
		 * Clean up the file content if exists.
		 */
		if ( file_exists( $save_path ) ) {
			// clean the file content.
			if ( filesize( $save_path ) > 0 ) {
				$cleaned = file_put_contents( $save_path, '' ); // phpcs:ignore
				if ( false === $cleaned ) {
					error_log( 'Failed to clean the file content of ' . $save_path ); // phpcs:ignore
					return false;
				}
			}
		}
		$written = file_put_contents( $save_path, wp_json_encode( $full_payload, JSON_PRETTY_PRINT ) ); // phpcs:ignore
		if ( ! $written ) {
			error_log( 'Failed to write product data to ' . $save_path ); // phpcs:ignore
			return false;
		}
		return true;
	}

	/**
	 * Work on: Sync data tab
	 * Get the product data count
	 */
	public function salsisync_get_product_data_count() {
		check_ajax_referer( 'salsisync_settings_nonce', 'nonce' );
		$params   = isset( $_POST['params'] ) ? sanitize_text_field( wp_unslash( $_POST['params'] ) ) : '';
		$limit    = isset( $_POST['limit'] ) ? sanitize_text_field( wp_unslash( $_POST['limit'] ) ) : 100; // Return 100 by default.
		$limit    = intval( $limit );
		$api_type = isset( $_POST['api_type'] ) ? sanitize_text_field( wp_unslash( $_POST['api_type'] ) ) : ''; // Return defult or export.
		/**
		 * Retrieves the 'fetchAll' value from the POST request.
		 *
		 * Checks if the 'fetchAll' key exists in the $_POST array, sanitizes its value,
		 * and assigns it to the $fetch_all variable. Returns an empty string if not set.
		 *
		 * @var string $fetch_all True or false as a string, based on the POST value.
		 */
		$fetch_all = isset( $_POST['fetchAll'] ) ? sanitize_text_field( wp_unslash( $_POST['fetchAll'] ) ) : '';
		if ( 'custom_data_mapping_update' === $params ) {
			$custom_data_mapping_fields_data = get_option( 'salsisync__custom_data_mapping_fiels', array() );
			update_option( 'salsisync__last_inserted_custom_data_mapping_fields', $custom_data_mapping_fields_data );
		}
		if ( 'true' === $fetch_all ) {
			$limit = $this->products_meta_in_salsify['total_entries'];
			$limit = intval( $limit );
			/**
			 * Check if all-page-response.json file exists
			 * then get the number of products from the meta
			 * if $limit is equal to the number of products
			 * not hit the API and return the data.
			 */
			$all_page_json_file_path = SALSI_SYNC_UPLOAD_DIR . 'data/salsify/all-page-response.json';
			if ( file_exists( $all_page_json_file_path ) ) {
				$json_data = file_get_contents( $all_page_json_file_path ); // phpcs:ignore
				if ( ! empty( $json_data ) ) {
					$products           = json_decode( $json_data, true );
					$products_data_arr  = $products['data'];
					$product_data_count = count( $products_data_arr );
					if ( $limit === $product_data_count ) {
						wp_send_json(
							array(
								'message'   => esc_html__( 'Not hit the API because limit is equal to the number of products', 'salsisync' ),
								'count'     => count( $products_data_arr ),
								'unique_id' => $this->salsisync__create_unique_id_by_time(),
							)
						);
					}
				}
			}
			$get_fetch_status = $this->salsisync_fetch_custom_number_of_data_from_salisify_api( $limit );
			if ( true === $get_fetch_status ) {
				$json_file_path     = SALSI_SYNC_UPLOAD_DIR . 'data/salsify/all-page-response.json';
				$json_data = file_get_contents( $json_file_path ); // phpcs:ignore
				$products           = json_decode( $json_data, true );
				$products_data_arr  = $products['data'];
				$product_data_count = count( $products_data_arr );
				wp_send_json(
					array(
						'message'   => esc_html__( 'All Products Fetched and Ready to Insert.', 'salsisync' ),
						'count'     => count( $products_data_arr ),
						'unique_id' => $this->salsisync__create_unique_id_by_time(),
					)
				);
			} else {
				wp_send_json(
					array(
						'message'   => esc_html__( 'Error fetching data from Salsify API.', 'salsisync' ),
						'status'    => 'error',
						'count'     => 0,
						'unique_id' => $this->salsisync__create_unique_id_by_time(),
					)
				);
			}
		}
		if ( 'false' === $fetch_all && $limit > 500 ) {
			// Write functionality to fetch data with export API.
			/**
			 * Check if all-page-response.json file exists
			 * then get the number of products from the meta
			 * if $limit is equal to the number of products
			 * not hit the API and return the data
			 */
			$all_page_json_file_path = SALSI_SYNC_UPLOAD_DIR . 'data/salsify/all-page-response.json';
			if ( file_exists( $all_page_json_file_path ) ) {
				$json_data = file_get_contents( $all_page_json_file_path ); // phpcs:ignore
				if ( ! empty( $json_data ) ) {
					$products           = json_decode( $json_data, true );
					$products_data_arr  = $products['data'];
					$product_data_count = array_key_exists( 'data', $products ) ? count( $products_data_arr ) : 0;
					if ( $limit === $product_data_count ) {
						wp_send_json(
							array(
								'message'   => esc_html__( 'Not hit the API because limit is equal to the number of products', 'salsisync' ),
								'count'     => count( $products_data_arr ),
								'unique_id' => $this->salsisync__create_unique_id_by_time(),
							)
						);
					}
				}
			}
			$get_fetch_status = $this->salsisync_fetch_custom_number_of_data_from_salisify_api( $limit );
			if ( true === $get_fetch_status ) {
				$json_file_path     = SALSI_SYNC_UPLOAD_DIR . 'data/salsify/all-page-response.json';
				$json_data = file_get_contents( $json_file_path ); // phpcs:ignore
				$products           = json_decode( $json_data, true );
				$products_data_arr  = $products['data'];
				$product_data_count = count( $products_data_arr );
				wp_send_json(
					array(
						'message'   => esc_html__( 'Products Fetched and Ready to Insert.', 'salsisync' ),
						'count'     => count( $products_data_arr ),
						'unique_id' => $this->salsisync__create_unique_id_by_time(),
					)
				);
			} else {
				wp_send_json(
					array(
						'message'   => esc_html__( 'Error fetching data from Salsify API.', 'salsisync' ),
						'status'    => 'error',
						'count'     => 0,
						'unique_id' => $this->salsisync__create_unique_id_by_time(),
					)
				);
			}
		}
		if ( 'false' === $fetch_all && $limit <= 500 && $limit >= 100 ) {
			/**
			 * Check if all-page-response.json file exists
			 * then get the number of products from the meta
			 * if $limit is equal to the number of products
			 * not hit the API and return the data
			 */
			$all_page_json_file_path = SALSI_SYNC_UPLOAD_DIR . 'data/salsify/all-page-response.json';
			if ( file_exists( $all_page_json_file_path ) ) {
				$json_data = file_get_contents( $all_page_json_file_path ); // phpcs:ignore
				if ( ! empty( $json_data ) ) {
					$products           = json_decode( $json_data, true );
					$products_data_arr  = $products['data'];
					$product_data_count = count( $products_data_arr );
					if ( $limit === $product_data_count ) {
						wp_send_json(
							array(
								'message'   => esc_html__( 'Not hit the API because limit is equal to the number of products', 'salsisync' ),
								'count'     => count( $products_data_arr ),
								'unique_id' => $this->salsisync__create_unique_id_by_time(),
							)
						);
					}
				}
			}
			$get_fetch_status = $this->salsisync_fetch_custom_number_of_data_from_salisify_api( $limit );
			if ( true === $get_fetch_status ) {
				$json_file_path     = SALSI_SYNC_UPLOAD_DIR . 'data/salsify/all-page-response.json';
				$json_data = file_get_contents( $json_file_path ); // phpcs:ignore
				$products           = json_decode( $json_data, true );
				$products_data_arr  = $products['data'];
				$product_data_count = count( $products_data_arr );
				wp_send_json(
					array(
						'message'   => esc_html__( 'Products Fetched and Ready to Insert.', 'salsisync' ),
						'count'     => count( $products_data_arr ),
						'unique_id' => $this->salsisync__create_unique_id_by_time(),
					)
				);
			} else {
				wp_send_json(
					array(
						'message'   => esc_html__( 'Error fetching data from Salsify API.', 'salsisync' ),
						'status'    => 'error',
						'count'     => count( 0 ),
						'unique_id' => $this->salsisync__create_unique_id_by_time(),
					)
				);
			}
		}
		if ( 'false' === $fetch_all && 100 === $limit ) {
			/**
			 * Get the JSON file path
			 */
			// phpcs:ignore $json_file_path = SALSI_SYNC_DIR . 'data/salsify/api-first-page-response.json';
			$json_file_path = SALSI_SYNC_UPLOAD_DIR . 'data/salsify/api-first-page-response.json';
			/**
			 * Check if the JSON file exists
			 */
			if ( file_exists( $json_file_path ) ) {
				/**
				 * Get the contents of the JSON file
				 */
				$json_data = file_get_contents( $json_file_path ); // phpcs:ignore
				/**
				 * Decode the JSON data into a PHP array
				 */
				$products          = json_decode( $json_data, true );
				$products_data_arr = $products['data'];
				wp_send_json(
					array(
						'count'     => count( $products_data_arr ),
						'unique_id' => $this->salsisync__create_unique_id_by_time(),
					)
				);
			}
		}
		wp_die();
	}
	/**
	 * Work on: Sync data tab
	 * Insert product data into the WooCommerce
	 */
	public function salsisync_insert_data_item() {
		/**
		 * Verify the nonce
		 */
		check_ajax_referer( 'salsisync_settings_nonce', 'nonce' );

		/**
		 * Change the file path.
		 */
		$get_read_filename = isset( $_POST['response_file_name'] ) ? sanitize_text_field( wp_unslash( $_POST['response_file_name'] ) ) : '';
		$file_name         = sanitize_file_name( $get_read_filename );
		if ( 'all-page-response.json' === $file_name ) {
			$json_file_path = SALSI_SYNC_UPLOAD_DIR . 'data/salsify/all-page-response.json';
		} else {
			$json_file_path = SALSI_SYNC_UPLOAD_DIR . 'data/salsify/api-first-page-response.json';
		}
		// phpcs:ignore $json_file_path = SALSI_SYNC_DIR . 'data/salsify/api-first-page-response.json';
		$index = intval( isset( $_POST['index'] ) ? sanitize_text_field( wp_unslash( $_POST['index'] ) ) : '' );

		if ( file_exists( $json_file_path ) ) {
			/**
			 * Get the contents of the JSON file
			 */
			$json_data = file_get_contents( $json_file_path ); // phpcs:ignore
			/**
			 * Decode the JSON data into a PHP array
			 */
			$products          = json_decode( $json_data, true );
			$products_data_arr = $products['data'];
			/**
			 * Check if the decoding was successful
			 * and the index is valid
			 * then insert the product into the WooCommerce
			 */
			if ( isset( $products_data_arr[ $index ] ) ) {
				$post_data = $products_data_arr[ $index ];
				/**
				 * Check if the product name exists in the product data
				 */
				if ( ! array_key_exists( $this->product_name_key, $post_data ) ) {
					printf( esc_html__( 'Product is not added. ❌ No product name found for ID - ', 'salsisync' ) . '%s', esc_html( $post_data['salsify:id'] ) );
					wp_die();
				}
				/**
				 * Check if the product description exists in the product data
				 */
				if ( '1' === $this->salsisync_product_description__key_required ) {
					if ( ! array_key_exists( $this->product_description_key, $post_data ) ) {
						printf( esc_html__( 'Product is not added. ❌ No description found for ID - ', 'salsisync' ) . '%s', esc_html( $post_data['salsify:id'] ) );
						wp_die();
					}
				}
				/**
				 * Check if the product category exists in the product data
				 * if not insert the product category
				 * otherwise return product category id
				 *
				 * @return [string] $product_category_id
				 */
				$product_category_id = $this->salsisync_handle_taxonomy_creation( $post_data[ $this->product_category_key ] );
				/**
				 * If product category id exists then insert the product into woocommerce
				 */
				if ( $product_category_id ) {
					/**
					 * Insert product into the WooCommerce
					 * return Array [ 'status' => 'success', 'message' => '✅ Product inserted successfully with ID : ' . $product_id, 'data' => $product_id ]
					 */
					$product_insert_status = $this->salsisync_handle_insert_product_to_woocommerce( $post_data, intval( $product_category_id ) );
					//phpcs:ignore echo esc_html( $product_insert_status['message'] );
					if ( 'success' === $product_insert_status['status'] ) {
						// Update the product data in the database.
						echo esc_html( $product_insert_status['message'] );
					} else {
						// Update the product data in the database.
						echo esc_html( $product_insert_status['message'] );
					}
				} else {
					// phpcs:ignore printf( esc_html__( '❌ Error creating category for the id - ', 'salsisync' ) . '%s', esc_html( $post_data['salsify:id'] ) );
					esc_html_e( '❌ Error creating category for the id - ', 'salsisync' );
					echo esc_html( $post_data['salsify:id'] );
					//phpcs:ignore esc_html_e( '❌ Error creating category for the id - ' . $post_data['salsify:id'], 'salsisync' );
				}
			} else {
				esc_html_e( 'Invalid index. Product not found.', 'salsisync' );
			}
			wp_die();
		}
	}
	/**
	 * Insert product data into the WooCommerce
	 * For testing purpose
	 */
	public function salsisync_test_insert_data_item() {
		check_ajax_referer( 'salsisync_settings_nonce', 'nonce' );
		/**
		 * Get the JSON file path
		 */
		// phpcs:ignore $json_file_path = SALSI_SYNC_DIR . 'data/salsify/api-first-page-response.json';
		$json_file_path = SALSI_SYNC_UPLOAD_DIR . 'data/salsify/api-first-page-response.json';
		/**
		 * Check if the JSON file exists
		 */
		if ( file_exists( $json_file_path ) ) {
			/**
			 * Get the contents of the JSON file
			 */
			$json_data = file_get_contents( $json_file_path ); // phpcs:ignore
			/**
			 * Decode the JSON data into a PHP array
			 */
			$products          = json_decode( $json_data, true );
			$products_data_arr = $products['data'];
			echo esc_html( 1 ); //phpcs:ignore
			wp_die();
		}
	}
	/**
	 * Insert product data into the WooCommerce
	 * For testing purpose
	 */
	public function salsisync_update_test_data_insert_status() {
		check_ajax_referer( 'salsisync_settings_nonce', 'nonce' );
		/**
		 * Get the JSON file path
		 */
		$update_status = update_option( 'salsisync_test_insert_status', 'true' );
		printf( esc_html__( 'Updated status - ', 'salsisync' ) . '%s', $update_status ? 'true' : 'false' );
		wp_die();
	}
	/**
	 * Insert product into post table
	 *
	 * @param [array]   $single_product_arr Single product data array.
	 * @param [integer] $category_id - Category ID.
	 * @return Array [ 'status' => 'success', 'message' => '✅ Product inserted successfully with ID : ' . $product_id, 'data' => $product_id ]
	 */
	public function salsisync_handle_insert_product_to_woocommerce( $single_product_arr, $category_id ) {
		if ( array_key_exists( $this->product_description_key, $single_product_arr ) ) {
			$product_description = $single_product_arr[ $this->product_description_key ];
		} else {
			$product_description = '';
		}
		/**
		 * Check if the product already exists in the database
		 * Array
		 * [ 'status' => true, 'product_id' => $posts[0]->ID ]
		 * [ 'status' => false, 'product_id' => '' ]
		 */
		$check_if_already_inserted = $this->get_posts_by_meta_value_with_get_posts( 'salsify_product_id', $single_product_arr['salsify:id'] );
		$product_id                = $check_if_already_inserted['status'] ? $check_if_already_inserted['product_id'] : '';
		/**
		 * Check if update is needed regarding custom mapping data
		 * If yes then update the product data
		 */
		//phpcs:ignore $get_changes_data = get_option( 'salsisync__custom_mapping_data_changes_log', array() );
		$get_changes_data_status = get_option( 'salsisync__custom_data_mapping_fields_update_status' );
		//phpcs:ignore if ( ! empty( $get_changes_data ) && $product_id ) {
		if ( $get_changes_data_status && $product_id ) {
			/**
			 * Update the product
			 */
			$this->salsisync__manage_custom_mapping_data( $single_product_arr, $product_id );
			$response = array(
				'status'  => 'error',
				'message' => sprintf( esc_html__( '✔️ Product Updated successfully with ID : ', 'salsisync' ) . '%s', $product_id ),
				'data'    => '',
			);
			return $response;
		}
		/**
		 * If the product already exists then return the status
		 */
		if ( true === $check_if_already_inserted['status'] ) {
			$response = array(
				'status'  => 'error',
				'message' => sprintf( esc_html__( '❗️Product already exists for ID : ', 'salsisync' ) . '%s', $single_product_arr['salsify:id'] ),
				'data'    => '',
			);
			return $response;
		}
		/**
		 * Insert product into the WooCommerce
		 */
		$product_id = wp_insert_post(
			array(
				'post_title'   => esc_html( $single_product_arr[ $this->product_name_key ] ),
				'post_content' => esc_html( $product_description ),
				'post_status'  => 'publish',
				'post_type'    => 'product',
			)
		);
		/**
		 * Check if the product was inserted successfully
		 */
		if ( ! is_wp_error( $product_id ) ) {
			update_post_meta( $product_id, 'salsify_product_id', $single_product_arr['salsify:id'] );
			$this->salsisync__manage_custom_mapping_data( $single_product_arr, $product_id );
			/**
			 * Set featured image
			 */
			if ( array_key_exists( $this->product_main_image_key, $single_product_arr ) ) {
				/**
				 * Get the main image URL from the product data
				 */
				$main_image_url = $this->salsisync_handle_main_image_url( $single_product_arr, $single_product_arr[ $this->product_main_image_key ] );
				/**
				 * Upload the image to the WordPress media library
				 */
				$featured_image_id = $this->salsisync_upload_image_from_url( $main_image_url );
				/**
				 * Set the uploaded image as the featured image for the product
				 */
				if ( $featured_image_id ) {
					set_post_thumbnail( $product_id, $featured_image_id );
				}
				/**
				 * Get all the image URL except the main image
				 */
				$get_gallery_image_url_arr = $this->salsisync_get_all_image_url_without_main_image( $single_product_arr, $single_product_arr[ $this->product_main_image_key ] );
				/**
				 * Add the remaining images to the product gallery
				 */
				$add_images_to_product_gallery_status = $this->salsisync_add_images_to_product_gallery( $product_id, $get_gallery_image_url_arr );
				if ( is_wp_error( $add_images_to_product_gallery_status ) ) {
					return sprintf( esc_html__( '❌ Error inserting image to product gallery: ', 'salsisync' ) . '%s', $add_images_to_product_gallery_status );
				}
			}
			/**
			 * Assign the product to categories
			 */
			$category_ids = array( $category_id ); // Array of WooCommerce category IDs.
			wp_set_object_terms( $product_id, $category_ids, 'product_cat' );
			/**
			 * Return the product ID
			 * '✅ Product inserted successfully with ID : ' . $product_id;
			 * '❌ Error inserting product: ' . $product_id->get_error_message();
			 */
			$response = array(
				'status'  => 'success',
				'message' => sprintf( esc_html__( '✅ Product inserted successfully with ID : ', 'salsisync' ) . ' %s', $product_id ),
				'data'    => $product_id,
			);
			return $response;
		} else {
			$response = array(
				'status'  => 'error',
				'message' => sprintf( esc_html__( '❌ Error inserting product: ', 'salsisync' ) . '%s', $product_id->get_error_message() ),
				'data'    => '',
			);
			return $response;
		}
	}
	/**
	 * Get product by meta value ( salsify-id ) using get_posts
	 *
	 * @param [string] $meta_key - Meta key.
	 * @param [string] $meta_value - Meta value.
	 * @return array
	 */
	public function get_posts_by_meta_value_with_get_posts( $meta_key, $meta_value ) {
		// Define the query arguments.
		$args = array(
			'post_type'  => 'product',
			'meta_key'   => $meta_key, //phpcs:ignore
			'meta_value' => $meta_value, //phpcs:ignore
		);

		// Get posts based on the query.
		$posts = get_posts( $args );

		// Check if any posts are found.
		if ( ! empty( $posts ) ) {
			return array(
				'status'     => true,
				'product_id' => $posts[0]->ID,
			);
		} else {
			return array(
				'status'     => false,
				'product_id' => '',
			);
		}
	}
	/**
	 * Add product category to the WooCommerce product category
	 *
	 * @param [string] $taxonomy_name - Product category name.
	 * @return string
	 */
	public function salsisync_handle_taxonomy_creation( $taxonomy_name ) {
		$taxonomy         = 'product_cat';
		$term             = $taxonomy_name;
		$term_slug        = $this->salsisync_handle_create_slug( $taxonomy_name );
		$term_description = '';
		$term_parent      = '';
		$term_id          = term_exists( $term, $taxonomy );
		if ( 0 !== $term_id && null !== $term_id ) {
			$term_id = $term_id['term_id'];
			return $term_id;
		} else {
			$term_id = wp_insert_term(
				$term,
				$taxonomy,
				array(
					'description' => $term_description,
					'slug'        => $term_slug,
					'parent'      => $term_parent,
				)
			);
			if ( is_wp_error( $term_id ) ) {
				return $term_id->get_error_message();
			} else {
				return $term_id['term_id'];
			}
		}
	}
	/**
	 * Create a slug from a string
	 *
	 * @param [string] $product_name - Product name.
	 * @return String - Slug
	 */
	public function salsisync_handle_create_slug( $product_name ) {
		if ( ! is_null( $product_name ) ) {
			// Convert the string to lowercase.
			$slug = strtolower( $product_name );

			// Replace non-letter or digits with hyphens.
			$slug = preg_replace( '/[^a-z0-9]+/', '-', $slug );

			// Trim hyphens from the beginning and end.
			$slug = trim( $slug, '-' );

			return $slug;
		}
	}
	/**
	 * Find product image url from product array
	 *
	 * @param [array]  $product_arr - Product data array.
	 * @param [string] $main_image_id - Main image ID.
	 * @return URL
	 */
	public function salsisync_handle_main_image_url( $product_arr, $main_image_id ) {
		if ( is_array( $main_image_id ) ) {
			$main_image_id = $main_image_id[0];
		}
		foreach ( $product_arr['salsify:digital_assets'] as $digital_asset ) {
			if ( $digital_asset['salsify:id'] === $main_image_id ) {
				return $digital_asset['salsify:url'];
			}
		}
	}
	/**
	 * Get all images URL except the main image
	 *
	 * @param [array] $product_arr - Product data array.
	 * @param [text]  $main_image_id - Main image ID.
	 * @return URL
	 */
	public function salsisync_get_all_image_url_without_main_image( $product_arr, $main_image_id ) {
		if ( is_array( $main_image_id ) ) {
			$main_image_id = $main_image_id[0];
		}
		$all_image_url = array();
		foreach ( $product_arr['salsify:digital_assets'] as $digital_asset ) {
			if ( $digital_asset['salsify:id'] !== $main_image_id ) {
				$all_image_url[] = $digital_asset['salsify:url'];
			}
		}
		return $all_image_url;
	}
	/**
	 * Add images to the product gallery
	 *
	 * @param [array] $product_id - WooCommerce product ID.
	 * @param array   $image_urls - Array of image URLs.
	 * @return Integer or text
	 */
	public function salsisync_add_images_to_product_gallery( $product_id, $image_urls = array() ) {
		// Array to store image IDs.
		$gallery_image_ids_arr = array();

		foreach ( $image_urls as $image_url ) {
			// Upload the image to the media library.
			$image_id = $this->salsisync_upload_image_from_url( $image_url );

			if ( ! is_wp_error( $image_id ) ) {
				// Add the image ID to the gallery array.
				$gallery_image_ids_arr[] = $image_id;
			}
		}
		/**
		 * Loop through the image IDs and add them to the product gallery
		 */
		if ( ! empty( $gallery_image_ids_arr ) ) {
			// Convert the array of image IDs to a comma-separated string.
			$gallery_image_ids_string = implode( ',', $gallery_image_ids_arr );

			// Update the product's gallery meta field with the image IDs.
			$gallery_update_status = update_post_meta( $product_id, '_product_image_gallery', $gallery_image_ids_string );
			if ( is_wp_error( $gallery_update_status ) ) {
				// Add the image ID to the gallery array.
				return sprintf( esc_html__( '❌ Error updating gallery: ', 'salsisync' ) . '%s', $gallery_update_status );
			}
		}
	}
	/**
	 * Fetch image from the URL and
	 * upload it to the WordPress media library
	 *
	 * @param [string] $image_url - URL of the image to upload.
	 * @return Number - attachment ID
	 */
	public function salsisync_upload_image_from_url( $image_url ) {
		// Get the WordPress upload directory.
		$upload_dir = wp_upload_dir();

		// Get the image file name and path.
		$image_data = file_get_contents( $image_url ); // phpcs:ignore
		$filename   = basename( $image_url );

		// Generate unique filename.
		$filename = wp_unique_filename( $upload_dir['path'], $filename );

		// Create the image file in the upload directory.
		$image_path = $upload_dir['path'] . '/' . $filename;
		file_put_contents( $image_path, $image_data ); // phpcs:ignore

		// Check the file type and generate attachment metadata.
		$wp_filetype = wp_check_filetype( $filename, null );

		// Prepare the attachment data array.
		$attachment = array(
			'guid'           => $upload_dir['url'] . '/' . $filename,
			'post_mime_type' => $wp_filetype['type'],
			'post_title'     => sanitize_file_name( $filename ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		);

		// Insert the attachment into the WordPress media library.
		$attach_id = wp_insert_attachment( $attachment, $image_path );

		// Generate attachment metadata and update the attachment.
		require_once ABSPATH . 'wp-admin/includes/image.php';
		$attach_data = wp_generate_attachment_metadata( $attach_id, $image_path );
		wp_update_attachment_metadata( $attach_id, $attach_data );

		return $attach_id;
	}
	/**
	 * Manage custom mapping data while insert product into the WooCommerce
	 *
	 * @param [array]   $single_product_arr - Product data array.
	 * @param [integer] $product_id - Salsify product ID.
	 * @return Boolean
	 */
	public function salsisync__manage_custom_mapping_data( $single_product_arr, $product_id ) {
		/**
		 * Get all the custom mapping data from the option table
		 */
		$custom_mapping_data = get_option( 'salsisync__custom_data_mapping_fiels' );
		/**
		 * Check if the data exists in the product data - $single_product_arr
		 * If exists then insert the data into the product meta
		 * If not exists then skip the data
		 */
		if ( $custom_mapping_data ) {
			foreach ( $custom_mapping_data as $key => $value ) {
				if ( array_key_exists( $value['key'], $single_product_arr ) ) {
					$data_value_arr = array(
						'label' => $value['label'],
						'value' => $single_product_arr[ $value['key'] ],
					);
					$serialize_data = maybe_serialize( $data_value_arr );
					update_post_meta( $product_id, $value['key'], $serialize_data );
				} else {
					$data_value_arr = array(
						'label' => $value['label'],
						'value' => false,
					);
					$serialize_data = maybe_serialize( $data_value_arr );
					update_post_meta( $product_id, $value['key'], $serialize_data );
				}
			}
		}
		return true;
	}


	/**
	 * [Helper function]
	 * Get WooCommerce product ID by Salsify ID
	 *
	 * @param [string] $salsify_id - Salsify ID.
	 * @return string
	 */
	public function get_woocommerce_product_id_by_salsify_id( $salsify_id ) {
		global $wpdb;
		$meta_key = 'salsify_id';
		$query    = $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value = %s", $meta_key, $salsify_id );
		return $wpdb->get_var( $query ); //phpcs:ignore
	}
}
