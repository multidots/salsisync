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
class Settings_Tab_Sync_Check_For_Update {

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
			add_action( 'wp_ajax_nopriv_salsisync_check_updated_product_from_salsify_api', array( $this, 'salsisync_check_updated_product_from_salsify_api' ) );
			add_action( 'wp_ajax_salsisync_check_updated_product_from_salsify_api', array( $this, 'salsisync_check_updated_product_from_salsify_api' ) );
		}
	}

	/**
	 * [Job]
	 * 1. Fetch 100 products from the Salsify API
	 * 2. Compare the new data with the old data
	 * 3. Show the changes in the update tab of sync settings
	 * 4. [salsisync_salsify_updated_products] update option table with updated products
	 * 5. [salsisync_salsify_new_products] update option table with new products
	 *
	 * @return void
	 */
	public function salsisync_check_updated_product_from_salsify_api() {
		// Verify the nonce for security.
		check_ajax_referer( 'salsisync_settings_nonce', 'nonce' );

		/**
		 * Old file path needs to changed based on input number
		 * new data fetch functionality needs to changed based on input number
		 * because previosly it fetched 100 products but now it needs to fetch based on input number.
		 */
		// Check product_count_for_update value from the post request.
		$product_count_for_update = isset( $_POST['product_count_for_update'] ) ? intval( $_POST['product_count_for_update'] ) : 100;
		// $product_count_for_update = 100;
		// Make $product_count_for_update integer value.
		$product_count_for_update = absint( $product_count_for_update );

		if ( $product_count_for_update > 100 ) {
			$old_file_path = SALSI_SYNC_UPLOAD_DIR . 'data/salsify/all-page-response.json';
		} else {
			$old_file_path = SALSI_SYNC_UPLOAD_DIR . 'data/salsify/api-first-page-response.json';
		}
		// Check if the file exists and if it is empty then send wp_send_json_error.
		if ( file_exists( $old_file_path ) && filesize( $old_file_path ) === 0 ) {
			wp_send_json_error(
				array(
					'output' => '<p>File is empty.</p>',
				)
			);
		}
		// Check if the file exists and if it is not empty then send wp_send_json_error.
		if ( ! file_exists( $old_file_path ) || filesize( $old_file_path ) === 0 ) {
			wp_send_json_error(
				array(
					'output' => '<p>File does not exist or is empty.</p>',
				)
			);
		}
		// path where the previous data is stored.
		// phpcs:ignore $old_file_path = SALSI_SYNC_DIR . 'data/salsify/api-first-page-response.json';
		// phpcs:ignore $old_file_path = SALSI_SYNC_UPLOAD_DIR . 'data/salsify/api-first-page-response.json';
		/**
		 * Fetch again 100 products from the Salsify API
		 */
		if ( 100 === $product_count_for_update ) {
			$new_products = $this->salsisync__fetch_salsify_products( 100 ); // Replace this with your function to get products from Salsify.
		} else {
			$new_products = $this->salsisync__fetch_salsify_more_then_100_products( $product_count_for_update );
		}
		/**
		 * Read the old product data from the file
		 */
		if ( file_exists( $old_file_path ) ) {
			$old_data         = file_get_contents( $old_file_path ); //phpcs:ignore
			$old_products_arr = json_decode( $old_data, true );
			$old_products     = $old_products_arr['data'];
		} else {
			$old_products = array();
		}
		/**
		 * Compare the new data with the old data
		 * both data are in array format.
		 */
		$changes = $this->salsisync__compare_salsify_data( $old_products, $new_products['data'] );
		/**
		 * Output the changes
		 */
		$output           = '';
		$updated_products = array();
		$new_products     = array();
		/**
		 * Show the updated products
		 * and update the option table
		 * [salsisync_salsify_updated_products]
		 */
		if ( ! empty( $changes['updated'] ) ) {
			$output .= '<div class="updated-product-list"><h3>Updated Products:</h3><ul>';
			foreach ( $changes['updated'] as $product ) {
				$updated_products[ $product['salsify:id'] ] = $product['Product Name'];
				$output                                    .= '<li id="' . $product['salsify:id'] . '">' . $product['Product Name'] . ' (ID: ' . $product['salsify:id'] . ')</li>';
			}
			update_option( 'salsisync_salsify_updated_products', json_encode( $updated_products ) ); //phpcs:ignore
			$output .= '</ul></div>';
		} else {
			update_option( 'salsisync_salsify_updated_products', '' );
		}
		/**
		 * Show the new products
		 * and update the option table
		 * [salsisync_salsify_new_products]
		 */
		if ( ! empty( $changes['new'] ) ) {
			$output .= '<div class="new-product-list"><h3>New Products:</h3><ul>';
			foreach ( $changes['new'] as $product ) {
				$new_products[ $product['salsify:id'] ] = $product['Product Name'];
				$output                                .= '<li id="' . $product['salsify:id'] . '">' . $product['Product Name'] . ' (ID: ' . $product['salsify:id'] . ')</li>';
			}
			update_option( 'salsisync_salsify_new_products', json_encode( $new_products ) ); //phpcs:ignore
			$output .= '</ul></div>';
		} else {
			update_option( 'salsisync_salsify_new_products', '' );
		}
		/**
		 * If no changes detected
		 * show the message
		 */
		if ( empty( $output ) ) {
			$output = '<p>No changes detected.</p>';
		}
		wp_send_json_success(
			array(
				'output'  => $output,
				'changes' => $changes,
			)
		);
		wp_die();
	}
	/**
	 * Fetch 100 products from the Salsify API
	 *
	 * @param [type] $limit Limit of products to fetch.
	 * @return Text
	 */
	public function salsisync__fetch_salsify_products( $limit = 100 ) {
		$api_token           = get_option( 'salsisync_api_token__key' );
		$orgs_token          = get_option( 'salsisync_orgs__key' );
		$first_page_endpoint = "https://app.salsify.com/api/v1/orgs/{$orgs_token}/products?page=1&per_page={$limit}";
		/**
		 * Set up the arguments for the request.
		 */
		$args = array(
			'headers' => array(
				'Authorization' => 'Bearer ' . $api_token,
				'Content-Type'  => 'application/json',
			),
			'timeout' => 60,
		);

		/**
		 * Check for data from file
		 * if not then get hit the API
		 */
		// phpcs:ignore $fetched_product_file_path = SALSI_SYNC_DIR . 'data/salsify/api-update-page-response.json';
		$fetched_product_file_path = SALSI_SYNC_UPLOAD_DIR . 'data/salsify/api-update-page-response.json';
		$fetched_data              = file_get_contents( $fetched_product_file_path ); //phpcs:ignore
		$fetched_products_arr      = json_decode( $fetched_data, true );
		$fetched_products_arr      = false; //phpcs:ignore // remove this line if you want to fetch data from file.

		if ( $fetched_products_arr ) {
			$response = $fetched_products_arr;
			return $response;
		} else {
			$response = wp_remote_get( $first_page_endpoint, $args );
			if ( is_wp_error( $response ) ) {
				return array();
			}
			/**
			 * Get the body of the response
			 */
			$body   = wp_remote_retrieve_body( $response );
			$result = json_decode( $body, true );
			/**
			 * Define the file path for the API response
			 */
			// phpcs:ignore $new_content_file_path = SALSI_SYNC_DIR . 'data/salsify/api-update-page-response.json';
			$new_content_file_path = SALSI_SYNC_UPLOAD_DIR . 'data/salsify/api-update-page-response.json';
			$this->salsisync__insert_new_data_to_file( $result, $new_content_file_path );
			/**
			 * Return the result as an array.
			 */
			return $result;
		}
	}
	/**
	 * Fetch more than 100 products from the Salsify API
	 *
	 * @param integer $limit Limit of products to fetch.
	 * @return Array
	 */
	public function salsisync__fetch_salsify_more_then_100_products( $limit = 100 ) {
		// parse the limit to integer.
		$limit = absint( $limit );
		// Need to functionality to fetch more than 100 products.
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
		// phpcs:ignore $new_content_file_path = SALSI_SYNC_UPLOAD_DIR . 'data/salsify/api-update-page-response.json';
		$upload_dir = wp_upload_dir();
		$save_dir   = trailingslashit( $upload_dir['basedir'] ) . 'salsi-sync/data/salsify';

		// Ensure the directory exists or create it.
		if ( ! file_exists( $save_dir ) && ! wp_mkdir_p( $save_dir ) ) {
			error_log( 'Failed to create directory: ' . $save_dir ); // phpcs:ignore
			return false;
		}

		$save_path = trailingslashit( $save_dir ) . 'api-update-page-response.json';
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
		return $full_payload; // Return Array.
	}
	/**
	 * Compare new and old data to find changes
	 *
	 * @param [type] $old_products Old products data.
	 * @param [type] $new_products New products data.
	 * @return Array
	 */
	public function salsisync__compare_salsify_data( $old_products, $new_products ) {
		$changes = array(
			'new'     => array(),
			'updated' => array(),
		);

		// Map old products by ID for quick lookup.
		$old_products_by_id = array();
		foreach ( $old_products as $product ) {
			$old_products_by_id[ $product['salsify:id'] ] = $product;
		}

		// Compare new products against old products.
		foreach ( $new_products as $index => $new_product ) {
			$id = $new_product['salsify:id'];

			if ( ! isset( $old_products_by_id[ $id ] ) ) {
				// New product.
				$changes['new'][ $index ] = $new_product;
			} elseif ( $old_products_by_id[ $id ] !== $new_product ) {
				// Updated product.
				$changes['updated'][ $index ] = $new_product;
			}
		}
		return $changes;
	}
	/**
	 * [Helper function]
	 * Get WooCommerce product ID by Salsify ID
	 *
	 * @param [type] $salsify_id Salsify product ID to get WooCommerce product ID.
	 * @return Array
	 */
	public function salsisync__get_woocommerce_product_id_by_salsify_id( $salsify_id ) {
		global $wpdb;
		$meta_key = 'salsify_id'; // Replace with the meta key where Salsify ID is stored.
		$query    = $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value = %s", $meta_key, $salsify_id );
		return $wpdb->get_var( $query ); //phpcs:ignore
	}
	/**
	 * [Helper function]
	 * Insert new data to file
	 *
	 * @param [type] $data data to insert in the file.
	 * @param [type] $path file path where to insert the data.
	 * @return void
	 */
	public function salsisync__insert_new_data_to_file( $data, $path ) {
		// Define the file path.
		$file_path = $path;
		// Check if the file exists and empty it.
		if ( file_exists( $file_path ) ) {
			// If the file exists, clear its content.
			file_put_contents( $file_path, '' ); //phpcs:ignore
		}
		// Save the new data to the file.
		file_put_contents( $file_path, json_encode( $data, JSON_PRETTY_PRINT ) ); //phpcs:ignore
	}
}
