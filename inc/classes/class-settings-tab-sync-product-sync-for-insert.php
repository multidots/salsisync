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
class Settings_Tab_Sync_Product_Sync_For_Insert {

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
			add_action( 'wp_ajax_nopriv_salsisync_count_products_for_insert', array( $this, 'salsisync_count_products_for_insert' ) );
			add_action( 'wp_ajax_salsisync_count_products_for_insert', array( $this, 'salsisync_count_products_for_insert' ) );

			add_action( 'wp_ajax_nopriv_salsisync_sync_insert_product', array( $this, 'salsisync_sync_insert_product' ) );
			add_action( 'wp_ajax_salsisync_sync_insert_product', array( $this, 'salsisync_sync_insert_product' ) );
		}
	}

	/**
	 * Count the new products for insert
	 *
	 * @return void
	 */
	public function salsisync_count_products_for_insert() {
		// Verify the nonce for security.
		check_ajax_referer( 'salsisync_settings_nonce', 'nonce' );
		/**
		 * Count the new product from the option table
		 */
		$new_products_obj      = json_decode( get_option( 'salsisync_salsify_new_products' ) );
		$new_products_arr      = get_object_vars( $new_products_obj );
		$new_products_arr_keys = array_keys( $new_products_arr );
		$new_products_count    = count( $new_products_arr );
		/**
		 * Send the response
		 */
		if ( $new_products_count > 0 ) {
			wp_send_json_success(
				array(
					'success'                         => true,
					'message'                         => esc_html__( 'Product inserting', 'salsisync' ),
					'new_products_count'              => $new_products_count,
					'new_available_products_arr_keys' => $new_products_arr_keys,
				)
			);
			wp_die();
		} else {
			wp_send_json_error(
				array(
					'success' => false,
					'message' => esc_html__( 'No product found to insert', 'salsisync' ),
				)
			);
			wp_die();
		}
	}
	/**
	 * Find the product index from the JSON file by salsify ID
	 *
	 * @param [type] $json_array - data from the JSON file.
	 * @param [type] $id - salsify ID.
	 * @param string $id_key - salsify ID key.
	 * @return null
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
	 * Insert the product in the WooCommerce
	 *
	 * @return void
	 */
	public function salsisync_sync_insert_product() {
		// Verify the nonce for security.
		check_ajax_referer( 'salsisync_settings_nonce', 'nonce' );
		$posted_product_id = isset( $_POST['product_id'] ) ? sanitize_text_field( wp_unslash( $_POST['product_id'] ) ) : '';
		/**
		 * Update the product in the WooCommerce
		 * $salsify_product_id           = isset( $_POST['product_id']  ) ? wp_unslash( $_POST['product_id'] ) : '';
		 */
		$salsify_product_id           = $posted_product_id;
		$single_product_update_status = $this->salsisync_update_woocommerce_product( $salsify_product_id );
		/**
		 * Send the response
		 */
		if ( $single_product_update_status ) {
			$product_url = get_the_permalink( $single_product_update_status );
			/**
			 * Update option table data
			 */
			$db_update_status = $this->salsisync_update_db_and_woocommerce_for_the_sync( $salsify_product_id );
			/**
			 * Update old file data with new file data
			 * Return
			 * 1. byte for successfull update
			 * 2. false for failed update
			 */
			$old_file_update_status = $this->salsisync_insert_old_file_data_with_new_data( $salsify_product_id );

			wp_send_json_success(
				array(
					'success'                      => true,
					'message'                      => esc_html__( 'Product updated successfully', 'salsisync' ),
					'option_table_update_status'   => $db_update_status,
					'single_product_insert_status' => $single_product_update_status,
					'old_file_update_status'       => $old_file_update_status,
					'product_url'                  => $product_url,
				)
			);
			wp_die();
		} else {
			wp_send_json_error(
				array(
					'success'                      => false,
					'message'                      => esc_html__( 'Product creation failed', 'salsisync' ),
					'single_product_update_status' => $single_product_update_status,
				)
			);
			wp_die();
		}
	}
	/**
	 * Update the product in the WooCommerce
	 *
	 * @param string $product_id - salsify product ID.
	 * @return bolean or integer
	 */
	public function salsisync_update_woocommerce_product( $product_id = '' ) {
		$product_id = $product_id;
		// Define the query arguments.
		$args = array(
			'post_type'  => 'product',
			'meta_key'   => 'salsify_product_id', // phpcs:ignore
			'meta_value' => $product_id, // phpcs:ignore
		);

		// Get posts based on the query.
		$product = get_posts( $args );

		// Check if any posts are found.
		if ( ! empty( $product ) ) {
			return false;
			//@codingStandardsIgnoreStart
			/**
			 * Get the product content from the JSON file
			 */
			// phpcs:ignore $updated_product_json_file_path     = SALSI_SYNC_DIR . 'data/salsify/api-update-page-response.json';
			$updated_product_json_file_path     = SALSI_SYNC_UPLOAD_DIR . 'data/salsify/api-update-page-response.json';
			$updated_json_data                  = file_get_contents( $updated_product_json_file_path );
			$updated_products                   = json_decode( $updated_json_data, true );
			$updated_products_data_arr          = $updated_products['data'];
			$find_index_by_id_from_updated_list = $this->salsisync_find_index_by_id( $updated_products_data_arr, $product_id, 'salsify:id' );
			$updated_product_data               = $updated_products_data_arr[ $find_index_by_id_from_updated_list ];
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
			if ( $updated_product_data ) {
				$single_product_update_status = wp_update_post(
					array(
						'ID'           => $product[0]->ID,
						'post_title'   => $updated_product_data[ $product_title_key ],
						'post_content' => $product_description,
					)
				);
				/**
				 * For successfull insert
				 * return product ID
				 * For failed insert
				 * return false
				 */
				if ( $single_product_update_status ) {
					return $single_product_update_status;
				} else {
					return false;
				}
			} else {
				return false;
			}
			//@codingStandardsIgnoreEnd
		} else {
			// phpcs:ignore $updated_product_json_file_path     = SALSI_SYNC_DIR . 'data/salsify/api-update-page-response.json';
			$updated_product_json_file_path     = SALSI_SYNC_UPLOAD_DIR . 'data/salsify/api-update-page-response.json';
			$updated_json_data                  = file_get_contents( $updated_product_json_file_path ); // phpcs:ignore -- use wp_remote_get()
			$updated_products                   = json_decode( $updated_json_data, true );
			$updated_products_data_arr          = $updated_products['data'];
			$find_index_by_id_from_updated_list = $this->salsisync_find_index_by_id( $updated_products_data_arr, $product_id, 'salsify:id' );
			$updated_product_data               = $updated_products_data_arr[ $find_index_by_id_from_updated_list ];
			/**
			 * Get the category key and
			 * insert the category in the WooCommerce
			 * if available then return existing category ID
			 */
			$product_category_key = get_option( 'salsisync_product_category__key' );
			$product_category_id  = $this->salsisync_handle_taxonomy_creation( $updated_product_data[ $product_category_key ] );
			/**
			 * Insert the product in the WooCommerce
			 */
			$product_title_key       = get_option( 'salsisync_product_name__key' );
			$new_product_title       = $updated_product_data[ $product_title_key ];
			$product_description_key = get_option( 'salsisync_product_description__key' );
			if ( array_key_exists( $product_description_key, $updated_product_data ) ) {
				$product_description = $updated_product_data[ $product_description_key ];
			} else {
				$product_description = '';
			}
			$product_insert_id = wp_insert_post(
				array(
					'post_title'   => $new_product_title,
					'post_content' => $product_description,
					'post_status'  => 'publish',
					'post_type'    => 'product',
				)
			);
			/**
			 * For successfull insert
			 */
			if ( ! is_wp_error( $product_insert_id ) ) {
				/**
				 * Update the product meta data
				 * [salsify_product_id]
				 */
				update_post_meta( $product_insert_id, 'salsify_product_id', $updated_product_data['salsify:id'] );
				/**
				 * Update the custom mapping data
				 */
				$this->salsisync__manage_custom_mapping_data( $updated_product_data, $product_insert_id );
				/**
				 * Update the category
				 */
				wp_set_object_terms( $product_insert_id, intval( $product_category_id ), 'product_cat' );
				/**
				 * Set featured image
				 */
				$product_main_image_key = get_option( 'salsisync_product_main_image__key' );
				if ( array_key_exists( $product_main_image_key, $updated_product_data ) ) {
					/**
					 * Get the main image URL from the product data
					 */
					$main_image_url = $this->salsisync_handle_main_image_url( $updated_product_data, $updated_product_data[ $product_main_image_key ] );
					/**
					 * Upload the image to the WordPress media library
					 */
					$featured_image_id = $this->salsisync_upload_image_from_url( $main_image_url );
					/**
					 * Set the uploaded image as the featured image for the product
					 */
					if ( $featured_image_id ) {
						set_post_thumbnail( $product_insert_id, $featured_image_id );
					}
					/**
					 * Get all the image URL except the main image
					 */
					$get_gallery_image_url_arr = $this->salsisync_get_all_image_url_without_main_image( $updated_product_data, $updated_product_data[ $product_main_image_key ] );
					/**
					 * Add the remaining images to the product gallery
					 */
					$add_images_to_product_gallery_status = $this->salsisync_add_images_to_product_gallery( $product_insert_id, $get_gallery_image_url_arr );
					if ( is_wp_error( $add_images_to_product_gallery_status ) ) {
						return sprintf( '❌ Error inserting image to product gallery: %s', $add_images_to_product_gallery_status );
					}
				}
				return $product_insert_id;
			} else {
				return false;
			}
		}
	}
	/**
	 * Update the option table once product is created in the WooCommerce
	 * [salsisync_salsify_new_products]
	 *
	 * @param string $product_id - salsify product ID.
	 * @return boolean
	 */
	public function salsisync_update_db_and_woocommerce_for_the_sync( $product_id = '' ) {
		$product_id                                = $product_id;
		$get_new_data_for_insert_from_option_table = json_decode( get_option( 'salsisync_salsify_new_products' ) );
		$new_products_arr                          = get_object_vars( $get_new_data_for_insert_from_option_table );
		if ( is_array( $new_products_arr ) && array_key_exists( $product_id, $new_products_arr ) ) {
			unset( $new_products_arr[ $product_id ] );
			update_option( 'salsisync_salsify_new_products', json_encode( $new_products_arr ) ); // phpcs:ignore
			return true;
		} else {
			return false;
		}
	}
	/**
	 * Update the old file data with the new file data
	 *
	 * @param string $product_id - salsify product ID.
	 * @return boolean
	 */
	public function salsisync_insert_old_file_data_with_new_data( $product_id = '' ) {
		$product_id = $product_id;

		// phpcs:ignore $old_product_json_file_path     = SALSI_SYNC_DIR . 'data/salsify/api-first-page-response.json';
		// phpcs:ignore $updated_product_json_file_path = SALSI_SYNC_DIR . 'data/salsify/api-update-page-response.json';
		$old_product_json_file_path     = SALSI_SYNC_UPLOAD_DIR . 'data/salsify/api-first-page-response.json';
		$updated_product_json_file_path = SALSI_SYNC_UPLOAD_DIR . 'data/salsify/api-update-page-response.json';
		/**
		 * Check if the JSON file exists
		 */
		if ( file_exists( $old_product_json_file_path ) && file_exists( $updated_product_json_file_path ) ) {
			/**
			 * Get the contents of the JSON file
			 */
			$old_product_json_data = file_get_contents( $old_product_json_file_path ); // phpcs:ignore -- use wp_remote_get()
			$updated_json_data     = file_get_contents( $updated_product_json_file_path ); // phpcs:ignore -- use wp_remote_get()
			/**
			 * Decode the JSON data into a PHP array
			 */
			$old_products_content  = json_decode( $old_product_json_data, true ); // phpcs:ignore
			$old_products_data_arr = $old_products_content['data'];
			$find_index_by_id      = $this->salsisync_find_index_by_id( $old_products_data_arr, $product_id, 'salsify:id' );

			$updated_products                   = json_decode( $updated_json_data, true );
			$updated_products_data_arr          = $updated_products['data'];
			$find_index_by_id_from_updated_list = $this->salsisync_find_index_by_id( $updated_products_data_arr, $product_id, 'salsify:id' );
			/**
			 * If no index found
			 */
			if ( null === $find_index_by_id && null === $find_index_by_id_from_updated_list ) {
				return false;
			}
			/**
			 * If index found
			 * 1. update the old file
			 * 2. return the status
			 */
			if ( null !== $find_index_by_id_from_updated_list ) {
				$new_product_data             = $updated_products_data_arr[ $find_index_by_id_from_updated_list ];
				$old_products_data_arr[]      = $new_product_data;
				$old_products_content['data'] = $old_products_data_arr;
				$updated_json_data            = json_encode( $old_products_content, JSON_PRETTY_PRINT ); // phpcs:ignore
				$update_status                = file_put_contents( $old_product_json_file_path, $updated_json_data ); // phpcs:ignore
				return $update_status;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	/**
	 * Find product image url from product array
	 *
	 * @param [array]  $product_arr - product data array.
	 * @param [string] $main_image_id - main image ID.
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
	 * @param [array] $product_arr - product data array.
	 * @param [text]  $main_image_id - main image ID.
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
	 * @param [array] $product_id - product ID.
	 * @param array   $image_urls - image URLs.
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
				return sprintf( '❌ Error updating gallery: %s', $gallery_update_status );
			}
		}
	}
	/**
	 * Fetch image from the URL and
	 * upload it to the WordPress media library
	 *
	 * @param [string] $image_url - image URL.
	 * @return Number - attachment ID of the uploaded image.
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
	 * Add product category to the WooCommerce product category
	 *
	 * @param [string] $taxonomy_name - category name.
	 * @return Text
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
	 * @param [string] $product_name - product name.
	 * @return String - Slug of the product name.
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
	 * Manage custom mapping data while insert product into the WooCommerce
	 *
	 * @param [array]   $single_product_arr - product data array.
	 * @param [integer] $product_id - product ID.
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
					$serialize_data = serialize( $data_value_arr ); // phpcs:ignore -- fix it later
					update_post_meta( $product_id, $value['key'], $serialize_data );
				} else {
					$data_value_arr = array(
						'label' => $value['label'],
						'value' => false,
					);
					$serialize_data = serialize( $data_value_arr ); // phpcs:ignore -- fix it later
					update_post_meta( $product_id, $value['key'], $serialize_data );
				}
			}
		}
		return true;
	}
}
