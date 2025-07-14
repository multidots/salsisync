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
class Settings_Tab_Sync_Product_Sync_For_Update {

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
			/**
			 * Check updated product from salsify api for new products
			 */
			add_action( 'wp_ajax_nopriv_salsisync_count_products_for_update', array( $this, 'salsisync_count_products_for_update' ) );
			add_action( 'wp_ajax_salsisync_count_products_for_update', array( $this, 'salsisync_count_products_for_update' ) );

			add_action( 'wp_ajax_nopriv_salsisync_sync_update_product', array( $this, 'salsisync_sync_update_product' ) );
			add_action( 'wp_ajax_salsisync_sync_update_product', array( $this, 'salsisync_sync_update_product' ) );
		}
	}

	/**
	 * Count update available products from the option table
	 * Send JSON response with
	 * 1. success message with updated product count
	 * 2. update available product ID
	 *
	 * @return void
	 */
	public function salsisync_count_products_for_update() {
		/**
		 * Verify the nonce for security
		 */
		check_ajax_referer( 'salsisync_settings_nonce', 'nonce' );
		/**
		 * Count the updated product from the option table
		 */
		$updated_products_obj      = json_decode( get_option( 'salsisync_salsify_updated_products' ) );
		$updates_products_arr      = get_object_vars( $updated_products_obj );
		$updates_products_arr_keys = array_keys( $updates_products_arr );
		$updated_products_count    = count( $updates_products_arr );
		/**
		 * Send the response
		 */
		if ( $updated_products_count > 0 ) {
			wp_send_json_success(
				array(
					'success'                            => true,
					'message'                            => esc_html__( 'Product updated successfully', 'salsisync' ),
					'updated_products_count'             => $updated_products_count,
					'update_available_products_arr_keys' => $updates_products_arr_keys,
				)
			);
			wp_die();
		} else {
			wp_send_json_error(
				array(
					'success' => false,
					'message' => esc_html__( 'No product found to update', 'salsisync' ),
				)
			);
			wp_die();
		}
	}
	/**
	 * Find single product index by [salsify:id] from json data array
	 *
	 * @param [type] $json_array - JSON data array.
	 * @param [type] $id - Salsify Product ID value.
	 * @param string $id_key - Salsify Product ID key.
	 * @return integer or null
	 */
	public function salsisync_find_index_by_id( $json_array, $id, $id_key = 'salsify:id' ) {
		foreach ( $json_array as $index => $item ) {
			if ( isset( $item[ $id_key ] ) && $item[ $id_key ] === $id ) {
				return $index; // Return the index if the ID matches.
			}
		}
		return null; // Return null if the ID is not found.
	}
	/**
	 * Jobs
	 * 1. Update the old file data with new file data
	 * 2. Update the option table data
	 * 3. Update the product in the WooCommerce
	 * 4. Send the response
	 */
	public function salsisync_sync_update_product() {
		check_ajax_referer( 'salsisync_settings_nonce', 'nonce' );
		/**
		 * Update old file data with new file data
		 * Return
		 * 1. byte for successfull update
		 * 2. false for failed update
		 */
		$product_id                   = isset( $_POST['product_id'] ) ? sanitize_text_field( wp_unslash( $_POST['product_id'] ) ) : '';
		$single_product_update_status = $this->salsisync_update_woocommerce_product_for_the_sync( $product_id );
		if ( $single_product_update_status ) {
			/**
			 * Update option table data
			 */
			$db_update_status = $this->salsisync_update_db_for_the_sync( $product_id );
			/**
			 * Update the old file
			 */
			$old_file_update_status = $this->salsisync_update_old_file_data_with_new_data( $product_id );
			/**
			 * Send the response
			 */
			$product_url = get_the_permalink( $single_product_update_status );
			wp_send_json_success(
				array(
					'success'                      => true,
					'message'                      => esc_html__( 'Product updated successfully', 'salsisync' ),
					'db_update_status'             => $db_update_status,
					'single_product_update_status' => $single_product_update_status,
					'old_file_update_status'       => $old_file_update_status,
					'product_url'                  => $product_url,
				)
			);
			wp_die();
		} else {
			wp_send_json_error(
				array(
					'success'    => false,
					'message'    => esc_html__( 'Update failed', 'salsisync' ),
					'product id' => $product_id,
				)
			);
			wp_die();
		}
	}
	/**
	 * Update option table data for updated products
	 *
	 * @param string $product_id Salsify Product ID.
	 * @return boolean
	 */
	public function salsisync_update_db_for_the_sync( $product_id = '' ) {
		$product_id                                  = $product_id;
		$get_update_available_data_from_option_table = json_decode( get_option( 'salsisync_salsify_updated_products' ) );
		$updated_products_arr                        = get_object_vars( $get_update_available_data_from_option_table );
		if ( is_array( $updated_products_arr ) && array_key_exists( $product_id, $updated_products_arr ) ) {
			unset( $updated_products_arr[ $product_id ] );
			update_option( 'salsisync_salsify_updated_products', json_encode( $updated_products_arr ) ); // phpcs:ignore
			return true;
		} else {
			return false;
		}
	}
	/**
	 * Update single respective product in woocommerce for the sync from the updated content
	 * Find the product by [salsify_product_id] meta key from the WooCommerce
	 *
	 * @param string $product_id Salsify Product ID.
	 * @return boolean
	 */
	public function salsisync_update_woocommerce_product_for_the_sync( $product_id = '' ) {
		$product_id = $product_id;
		$args       = array(
			'post_type'  => 'product',
			'meta_key'   => 'salsify_product_id', // phpcs:ignore
			'meta_value' => $product_id, // phpcs:ignore
		);
		$product    = get_posts( $args );
		/**
		 * Check if the product exists
		 */
		if ( ! empty( $product ) ) {
			/**
			 * Get the product content from the new JSON file
			 */
			// phpcs:ignore $updated_product_json_file_path = SALSI_SYNC_DIR . 'data/salsify/api-update-page-response.json';
			$updated_product_json_file_path = SALSI_SYNC_UPLOAD_DIR . 'data/salsify/api-update-page-response.json';
			$updated_json_data              = file_get_contents( $updated_product_json_file_path ); // phpcs:ignore
			$updated_products               = json_decode( $updated_json_data, true ); // phpcs:ignore
			$updated_products_data_arr      = $updated_products['data'];
			$index_number_from_updated_list = $this->salsisync_find_index_by_id( $updated_products_data_arr, $product_id, 'salsify:id' );
			$updated_product_data           = $updated_products_data_arr[ $index_number_from_updated_list ];
			/**
			 * Update the product in the WooCommerce
			 */
			$product_title_key       = get_option( 'salsisync_product_name__key' );
			$product_description_key = get_option( 'salsisync_product_description__key' );
			if ( array_key_exists( $product_description_key, $updated_product_data ) ) {
				$product_description = $updated_product_data[ $product_description_key ];
			} else {
				$product_description = '';
			}
			/**
			 * Update the product title and content
			 */
			$single_product_update_status = wp_update_post(
				array(
					'ID'           => $product[0]->ID,
					'post_title'   => $updated_product_data[ $product_title_key ],
					'post_content' => $product_description,
				)
			);
			if ( $single_product_update_status ) {
				// [Int|WP_Error] The post ID on success. The value 0 or WP_Error on failure.
				return $single_product_update_status;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	/**
	 * Update the old JSON file data with new JSON file data
	 *
	 * @param string $product_id - Salsify Product ID.
	 * @return Byte
	 */
	public function salsisync_update_old_file_data_with_new_data( $product_id = '' ) {
		$product_id = $product_id;

		// phpcs:ignore $old_product_json_file_path     = SALSI_SYNC_DIR . 'data/salsify/api-first-page-response.json';
		// phpcs:ignore $updated_product_json_file_path = SALSI_SYNC_DIR . 'data/salsify/api-update-page-response.json';
		$old_product_json_file_path     = SALSI_SYNC_UPLOAD_DIR . 'data/salsify/api-first-page-response.json';
		$updated_product_json_file_path = SALSI_SYNC_UPLOAD_DIR . 'data/salsify/api-update-page-response.json';
		/**
		 * Check if the JSON file exists.
		 */
		if ( file_exists( $old_product_json_file_path ) && file_exists( $updated_product_json_file_path ) ) {
			/**
			 * Get the contents of the old JSON file and new JSON file
			 */
			$old_product_json_data = file_get_contents( $old_product_json_file_path ); // phpcs:ignore
			$updated_json_data     = file_get_contents( $updated_product_json_file_path );	// phpcs:ignore
			/**
			 * Decode the JSON data into a PHP array
			 */
			$old_products_content  = json_decode( $old_product_json_data, true );
			$old_products_data_arr = $old_products_content['data'];
			$find_index_by_id      = $this->salsisync_find_index_by_id( $old_products_data_arr, $product_id, 'salsify:id' );

			$updated_products                   = json_decode( $updated_json_data, true );
			$updated_products_data_arr          = $updated_products['data'];
			$find_index_by_id_from_updated_list = $this->salsisync_find_index_by_id( $updated_products_data_arr, $product_id, 'salsify:id' );

			/**
			 * Update old file
			 */
			$old_products_data_arr[ $find_index_by_id ] = $updated_products_data_arr[ $find_index_by_id_from_updated_list ];
			/**
			 * Update the json file
			 */
			$old_products_content['data'] = $old_products_data_arr;
			$updated_json_data            = json_encode( $old_products_content, JSON_PRETTY_PRINT ); // phpcs:ignore
			$update_status                = file_put_contents( $old_product_json_file_path, $updated_json_data ); // phpcs:ignore

			return $update_status;
		} else {
			return false;
		}
	}
}
