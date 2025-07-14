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
class Settings_Tab_Data {

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
	 * Store product keys from the API response
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      array    $product_key_arr    product keys array
	 */
	public $product_key_arr = array();
	/**
	 * Store product keys mapping status
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      boolean    $salsisync_product_name__key__map_status    Key mapping status
	 */
	public $salsisync_product_name__key__map_status = false;
	/**
	 * Store product category keys mapping status
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      boolean    $salsisync_product_category__key__map_status   Category Key mapping status
	 */
	public $salsisync_product_category__key__map_status = false;
	/**
	 * Store product main image keys mapping status
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      boolean    $salsisync_product_main_image__key__map_status   Main Image Key mapping status
	 */
	public $salsisync_product_main_image__key__map_status = false;
	/**
	 * Store product description keys mapping status
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      boolean    $salsisync_product_description__key__map_status   Description Key mapping status
	 */
	public $salsisync_product_description__key__map_status = false;
	/**
	 * Store product custom keys mapping status
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      boolean    $salsisync_product_custom__key__map_status   Custom Key mapping status
	 */
	public $salsisync_product_custom__key__map_status = false;
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
		if ( 'true' === get_option( 'salsify_api_key_valid' ) ) {
			$this->salsisync_fetch_all_key_from_the_api_response();
		}
		if ( is_admin() ) {
			add_action( 'admin_init', array( $this, 'salsisync_register_settings__group__section__fields' ) );
		}
	}
	/**
	 * Salsify Settings
	 * Register Group
	 * Register Section
	 * Register Fields
	 */
	public function salsisync_register_settings__group__section__fields() {
		/**
		 * Data Settings
		 */
		register_setting( 'salsisync_product_data_mapping__settings_group', 'salsisync_product_name__key', 'sanitize_text_field' );
		register_setting( 'salsisync_product_data_mapping__settings_group', 'salsisync_product_category__key', 'sanitize_text_field' );
		register_setting( 'salsisync_product_data_mapping__settings_group', 'salsisync_product_main_image__key', 'sanitize_text_field' );
		register_setting( 'salsisync_product_data_mapping__settings_group', 'salsisync_product_description__key', 'sanitize_text_field' );
		register_setting( 'salsisync_product_data_mapping__settings_group', 'salsisync_product_description__key_required', 'sanitize_text_field' );
		// sections.
		add_settings_section( 'salsisync_data_mapping_page__section', esc_html__( 'Data Mapping', 'salsisync' ), null, 'salsisync_tab__data' );
		// fields.
		add_settings_field(
			'salsisync_product_name__key',
			esc_html__( 'Product Name', 'salsisync' ) . '<span class="salsisync-product__help-tip"><span class="salsisync-product__help-inner-tip" tabindex="0" aria-label="' . esc_attr__( 'Enter the product name key from the Salsify API.', 'salsisync' ) . '"></span></span>',
			array( $this, 'salsisync_product_name__input__callback' ),
			'salsisync_tab__data',
			'salsisync_data_mapping_page__section',
			array(
				'label_for' => 'salsisync_product_name__key',
				'desc'      => esc_html__( 'Enter the product name key from the Salsify API.', 'salsisync' ),
				'tip'       => esc_attr__( 'Enter the product name key from the Salsify API.', 'salsisync' ),
				'desc_tip'  => true,
				'autoload'  => false,
			)
		);
		add_settings_field(
			'salsisync_product_category__key',
			esc_html__( 'Product Category', 'salsisync' ) . '<span class="salsisync-product__help-tip"><span class="salsisync-product__help-inner-tip" tabindex="0" aria-label="' . esc_attr__( 'Enter the product category key from the Salsify API.', 'salsisync' ) . '"></span></span>',
			array( $this, 'salsisync_product_category__input__callback' ),
			'salsisync_tab__data',
			'salsisync_data_mapping_page__section',
			array(
				'label_for' => 'salsisync_product_category__key',
				'desc'      => esc_html__( 'Enter the product category key from the Salsify API.', 'salsisync' ),
				'tip'       => esc_attr__( 'Enter the product category key from the Salsify API.', 'salsisync' ),
				'desc_tip'  => true,
				'autoload'  => false,
			)
		);
		add_settings_field(
			'salsisync_product_main_image__key',
			esc_html__( 'Product Image', 'salsisync' ) . '<span class="salsisync-product__help-tip"><span class="salsisync-product__help-inner-tip" tabindex="0" aria-label="' . esc_attr__( 'Enter the product main image key from the Salsify API.', 'salsisync' ) . '"></span></span>',
			array( $this, 'salsisync_product_main_image__input__callback' ),
			'salsisync_tab__data',
			'salsisync_data_mapping_page__section',
			array(
				'label_for' => 'salsisync_product_main_image__key',
				'desc'      => esc_html__( 'Enter the product main image key from the Salsify API.', 'salsisync' ),
				'tip'       => esc_attr__( 'Enter the product main image key from the Salsify API.', 'salsisync' ),
				'desc_tip'  => true,
				'autoload'  => false,
			)
		);
		add_settings_field(
			'salsisync_product_description__key',
			esc_html__( 'Product Description', 'salsisync' ) . '<span class="salsisync-product__help-tip"><span class="salsisync-product__help-inner-tip" tabindex="0" aria-label="' . esc_attr__( 'Enter the product description key from the Salsify API.', 'salsisync' ) . '"></span></span>',
			array( $this, 'salsisync_product_description__input__callback' ),
			'salsisync_tab__data',
			'salsisync_data_mapping_page__section',
			array(
				'label_for' => 'salsisync_product_description__key',
				'desc'      => esc_html__( 'Enter the product description key from the Salsify API.', 'salsisync' ),
				'tip'       => esc_attr__( 'Enter the product description key from the Salsify API.', 'salsisync' ),
				'desc_tip'  => true,
				'autoload'  => false,
			)
		);
		add_settings_field(
			'salsisync_product_description__key_required',
			esc_html__( 'Set Description Required', 'salsisync' ) . '<span class="salsisync-product__help-tip salsisync-product__help-desc-tip"><span class="salsisync-product__help-inner-tip" tabindex="0" aria-label="' . esc_attr__( 'Make product description as required field when API response is inserted to WordPress as product.', 'salsisync' ) . '"></span></span>',
			array( $this, 'salsisync_product_description__checkbox__callback' ),
			'salsisync_tab__data',
			'salsisync_data_mapping_page__section',
			array(
				'label_for' => 'salsisync_product_description__key_required',
				'desc'      => esc_html__( 'To make product description as required field.', 'salsisync' ),
				'tip'       => esc_attr__( 'Make product description as required field when API response is inserted to WordPress as product.', 'salsisync' ),
				'desc_tip'  => true,
				'autoload'  => false,
			)
		);
	}

	/**
	 * Handle Product Name Input
	 * Tab - Data
	 * Section ID - [salsisync_product_name__key]
	 */
	public function salsisync_product_name__input__callback() {
		$value         = get_option( 'salsisync_product_name__key' );
		$set_read_only = ( 'true' === get_option( 'salsisync_ajax_data_insert_running_status' ) ) ? 'readonly' : '';
		echo '<input required class="regular-text" type="text" ' . esc_attr( $set_read_only ) . ' placeholder="' . esc_html__( 'Product Name', 'salsisync' ) . '" name="salsisync_product_name__key" value="' . esc_html( $value ) . '">';
		if ( $value && false === array_search( $value, $this->product_key_arr, true ) ) {
			echo '<p class="salsisync_error_message">' . esc_html__( 'Key not found in the API response', 'salsisync' ) . '</p>';
		}
	}
	/**
	 * Handle Product Category Input
	 * Tab - Data
	 * Section ID - [salsisync_product_category__key]
	 */
	public function salsisync_product_category__input__callback() {
		$value         = get_option( 'salsisync_product_category__key' );
		$set_read_only = ( 'true' === get_option( 'salsisync_ajax_data_insert_running_status' ) ) ? 'readonly' : '';
		echo '<input required class="regular-text" type="text" ' . esc_attr( $set_read_only ) . ' placeholder="' . esc_html__( 'Product Category', 'salsisync' ) . '" name="salsisync_product_category__key" value="' . esc_attr( $value ) . '">';
		if ( $value && false === array_search( $value, $this->product_key_arr, true ) ) {
			echo '<p class="salsisync_error_message">' . esc_html__( 'Key not found in the API response', 'salsisync' ) . '</p>';
		}
	}
	/**
	 * Handle Product Main Image Input
	 * Tab - Data
	 * Section ID - [salsisync_product_main_image__key]
	 */
	public function salsisync_product_main_image__input__callback() {
		$value         = get_option( 'salsisync_product_main_image__key' );
		$set_read_only = ( 'true' === get_option( 'salsisync_ajax_data_insert_running_status' ) ) ? 'readonly' : '';
		echo '<input required class="regular-text" type="text" ' . esc_attr( $set_read_only ) . ' placeholder="' . esc_html__( 'Product Image', 'salsisync' ) . '" name="salsisync_product_main_image__key" value="' . esc_attr( $value ) . '">';
		if ( $value && false === array_search( $value, $this->product_key_arr, true ) ) {
			echo '<p class="salsisync_error_message">' . esc_html__( 'Key not found in the API response', 'salsisync' ) . '</p>';
		}
	}
	/**
	 * Handle Product Description Input
	 * Tab - Data
	 * Section ID - [salsisync_product_description__key]
	 */
	public function salsisync_product_description__input__callback() {
		$value         = get_option( 'salsisync_product_description__key' );
		$set_read_only = ( 'true' === get_option( 'salsisync_ajax_data_insert_running_status' ) ) ? 'readonly' : '';
		echo '<input required class="regular-text" type="text" ' . esc_attr( $set_read_only ) . ' placeholder="' . esc_html__( 'Product Description', 'salsisync' ) . '" name="salsisync_product_description__key" value="' . esc_attr( $value ) . '">';
		if ( $value && false === array_search( $value, $this->product_key_arr, true ) ) {
			echo '<p class="salsisync_error_message">' . esc_html__( 'Key not found in the API response', 'salsisync' ) . '</p>';
		}
	}
	/**
	 * Handle Product Description Input
	 * Tab - Data
	 * Section ID - [salsisync_product_description__key_required]
	 */
	public function salsisync_product_description__checkbox__callback() {
		$value         = get_option( 'salsisync_product_description__key_required', '' );
		$set_read_only = ( 'true' === get_option( 'salsisync_ajax_data_insert_running_status' ) ) ? 'disabled' : '';
		echo '<input type="checkbox" ' . esc_attr( $set_read_only ) . ' name="salsisync_product_description__key_required" value="1" ' . checked( 1, $value, false ) . ' />';
	}
	/**
	 * Saslify Data Settings Tab
	 */
	public function salsisyncsettings_tab_content__data() {
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
		 * Verify custom mapping data nonce
		 */
		if ( isset( $_POST['salsisyncsettings_tab_content__data_custom_mapping_nonce'] ) && ! wp_verify_nonce( sanitize_key( $_POST['salsisyncsettings_tab_content__data_custom_mapping_nonce'] ), 'salsisyncsettings_tab_content__data_custom_mapping' ) ) {
			?>
			<div class="notice notice-error salsisyncnotice_message"><p><?php esc_html_e( '❌ Wrong Validation Request', 'salsisync' ); ?></p></div>
			<?php
		}
		/**
		 * If nonce is not valid then show the notice message
		 */
		if ( isset( $_POST['salsisyncsettings_tab_content__data_nonce'] ) && ! wp_verify_nonce( sanitize_key( $_POST['salsisyncsettings_tab_content__data_nonce'] ), 'salsisyncsettings_tab_content__data' ) ) {
			?>
			<div class="notice notice-error salsisyncnotice_message"><p><?php esc_html_e( '❌ Wrong Validation Request', 'salsisync' ); ?></p></div>
			<?php
		}
		/**
		 * Verify the nonce and update the value to option table
		 */
		if ( isset( $_POST['salsisyncsettings_tab_content__data_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['salsisyncsettings_tab_content__data_nonce'] ), 'salsisyncsettings_tab_content__data' ) ) {

			// Sanitize and save the input fields.
			$salsisync_product_name__key                 = filter_input( INPUT_POST, 'salsisync_product_name__key', FILTER_SANITIZE_SPECIAL_CHARS );
			$salsisync_product_category__key             = filter_input( INPUT_POST, 'salsisync_product_category__key', FILTER_SANITIZE_SPECIAL_CHARS );
			$salsisync_product_main_image__key           = filter_input( INPUT_POST, 'salsisync_product_main_image__key', FILTER_SANITIZE_SPECIAL_CHARS );
			$salsisync_product_description__key          = filter_input( INPUT_POST, 'salsisync_product_description__key', FILTER_SANITIZE_SPECIAL_CHARS );
			$salsisync_product_description__key_required = filter_input( INPUT_POST, 'salsisync_product_description__key_required', FILTER_SANITIZE_SPECIAL_CHARS );

			if ( isset( $salsisync_product_name__key ) ) {
				update_option( 'salsisync_product_name__key', sanitize_text_field( wp_unslash( $salsisync_product_name__key ) ) );
				if ( array_search( $salsisync_product_name__key, $this->product_key_arr, true ) ) {
					update_option( 'salsisync_product_name__key__map_status', true );
				} else {
					update_option( 'salsisync_product_name__key__map_status', false );
				}
			}
			if ( isset( $salsisync_product_category__key ) ) {
				update_option( 'salsisync_product_category__key', sanitize_text_field( wp_unslash( $salsisync_product_category__key ) ) );
				if ( array_search( $salsisync_product_category__key, $this->product_key_arr, true ) ) {
					update_option( 'salsisync_product_category__key__map_status', true );
				} else {
					update_option( 'salsisync_product_category__key__map_status', false );
				}
			}
			if ( isset( $salsisync_product_main_image__key ) ) {
				update_option( 'salsisync_product_main_image__key', sanitize_text_field( wp_unslash( $salsisync_product_main_image__key ) ) );
				if ( array_search( $salsisync_product_main_image__key, $this->product_key_arr, true ) ) {
					update_option( 'salsisync_product_main_image__key__map_status', true );
				} else {
					update_option( 'salsisync_product_main_image__key__map_status', false );
				}
			}
			if ( isset( $salsisync_product_description__key ) ) {
				update_option( 'salsisync_product_description__key', sanitize_text_field( wp_unslash( $salsisync_product_description__key ) ) );
				if ( array_search( $salsisync_product_description__key, $this->product_key_arr, true ) ) {
					update_option( 'salsisync_product_description__key__map_status', true );
				} else {
					update_option( 'salsisync_product_description__key__map_status', false );
				}
			}

			// Sanitize and save the checkbox value.
			$checkbox_value = isset( $salsisync_product_description__key_required ) ? 1 : ''; // Set to 1 if checked, empty if unchecked.

			// Update the option in the database.
			update_option( 'salsisync_product_description__key_required', $checkbox_value );
			?>
			<div class="notice notice-success salsisyncnotice_message is-dismissible"><p><?php esc_html_e( '✅ Data Updated', 'salsisync' ); ?></p></div>
			<?php
		}

		?>
		<div class="salsisync-product__data-tab-wrap">
			<div class="salsisync-product__data-tab">
				<a href="#" class="salsisync-product__sub-tab salsisync-product__sub-tab-active" data-sub-tab="data-mapping">
					<span><?php esc_html_e( 'Data Mapping', 'salsisync' ); ?></span>
				</a>
				<a href="#" class="salsisync-product__sub-tab" data-sub-tab="data-custom-mapping">
					<span><?php esc_html_e( 'Custom Data Mapping', 'salsisync' ); ?></span>
				</a>
				<div class="salsisync-product__tab-content__keys">
					<div class="show_api_keys_heading">
						<h2><?php esc_html_e( 'Show API Keys', 'salsisync' ); ?></h2>
						<span>
							<input type="checkbox" name="show_api_keys" id="show_api_keys">
						</span>
					</div>
					<div class="product-keys-array">
						<?php
							sort( $this->product_key_arr );
							$this->product_key_arr = array_combine( range( 1, count( $this->product_key_arr ) ), array_values( $this->product_key_arr ) );
						?>
					</div>
					<ul id="api_keys_wrapper">
						<?php
						foreach ( $this->product_key_arr as $key => $value ) {
							echo '<li>' . esc_html( $value ) . '</li>';
						}
						?>
					</ul>
				</div>
			</div>
			<div class="salsisync-product__tab-panel active" id="data-mapping">
				<form class="salsisync-product__form" method="post" action="admin.php?page=salsisync&tab=data">
					<?php
					wp_nonce_field( 'salsisyncsettings_tab_content__data', 'salsisyncsettings_tab_content__data_nonce' );
					settings_fields( 'salsisync_product_data_mapping__settings_group' );
					do_settings_sections( 'salsisync_tab__data' );
					submit_button(
						'',
						'primary',
						esc_html__( 'Save Changes', 'salsisync' ),
						true,
						'true' === get_option( 'salsisync_ajax_data_insert_running_status' ) ? 'disabled' : ''
					);
					?>
				</form>
			</div>
			<div class="salsisync-product__tab-panel" id="data-custom-mapping">
				<?php
				echo wp_kses_post( $this->salsisync__handle_custom_data_mapping_content() );
				?>
			</div>
		</div>
		<?php
	}
	/**
	 * Manage Custom Data Mapping
	 *
	 * @return void
	 */
	public function salsisync__handle_custom_data_mapping_content() {
		/**
		 * Verify nonce first and then save data
		 * Get previous data
		 * $get_previous_data = get_option( 'salsisync__custom_data_mapping_fiels', array() );
		 */
		if ( isset( $_POST['salsisyncsettings_tab_content__data_custom_mapping_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['salsisyncsettings_tab_content__data_custom_mapping_nonce'] ), 'salsisyncsettings_tab_content__data_custom_mapping' ) ) {
			$get_previous_data = get_option( 'salsisync__last_inserted_custom_data_mapping_fields', array() );
			$repeater_fields   = filter_input( INPUT_POST, 'repeater_fields', FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY );

			/**
			 * If repeater field not found then update the option table with empty array
			 */
			if ( $_POST && ! isset( $repeater_fields ) ) {
				update_option( 'salsisync__custom_data_mapping_fiels', array() );
				// phpcs:ignore $get_previous_data = get_option( 'salsisync__last_inserted_custom_data_mapping_fields', array() );
				$repeater_fields = isset( $repeater_fields) ? $repeater_fields : []; //phpcs:ignore
				$status          = $this->salsisync__compare_repeater_data( $get_previous_data, $repeater_fields );
				/**
				 * Update changes status
				 */
				update_option( 'salsisync__custom_data_mapping_fields_update_status', $status );
			}

			if ( isset( $repeater_fields ) && is_array( $repeater_fields ) ) {
				$repeater_fields = $repeater_fields; //phpcs:ignore

				// Sanitize the input fields.
				foreach ( $repeater_fields as &$field ) {
					$field['label'] = sanitize_text_field( $field['label'] );
					$field['key']   = sanitize_text_field( $field['key'] );
				}

				// Save the data as an option in the WordPress database.
				update_option( 'salsisync__custom_data_mapping_fiels', $repeater_fields );
				// Check for changes.
				// phpcs:ignore $this->salsisync__check_for_changes( 'salsisync__custom_data_mapping_fiels', $get_previous_data, $repeater_fields );
				// phpcs:ignore $this->salsisync__check_for_changes( 'salsisync__last_inserted_custom_data_mapping_fields', $get_previous_data, $repeater_fields );
				$status = $this->salsisync__compare_repeater_data( $get_previous_data, $repeater_fields );
				/**
				 * Update changes status
				 */
				update_option( 'salsisync__custom_data_mapping_fields_update_status', $status );

				?>
				<div class="notice notice-success salsisyncnotice_message is-dismissible">
					<p><?php esc_html_e( '✅ Data Updated', 'salsisync' ); ?></p>
				</div>
				<?php
			}
		}
		$saved_fields                  = get_option( 'salsisync__custom_data_mapping_fiels', array() );
		$get_changes_data              = get_option( 'salsisync__custom_mapping_data_changes_log', array() );
		$temp_keys_array_to_check_only = array();
		/**
		 * Loop through the saved fields and check if any key is not found the product key array
		 */
		foreach ( $saved_fields as $field ) {
			$temp_keys_array_to_check_only[] = $field['key'];
		}
		$mapping_status = $this->salsisync__validate_input_keys( $temp_keys_array_to_check_only, $this->product_key_arr );
		/**
		 * Get update status
		 */
		$get_custom_data_mapping_update_status = get_option( 'salsisync__custom_data_mapping_fields_update_status' );
		/**
		 * Get the product fetch option value.
		 */
		$fetch_all_value    = get_option( 'salsisync_all_product_to_sync__key', '0' );
		$fetch_all_value    = ( '1' === $fetch_all_value ) ? 'true' : 'false';
		$custom_limit_value = get_option( 'salsisync_number_of_product_to_sync__key', '100' );
		/**
		 * Show data changes notice to the user
		 */
		//phpcs:ignore if ( ! empty( $get_changes_data ) &&  true === $mapping_status) {
		if ( $get_custom_data_mapping_update_status && true === $mapping_status ) {
			?>
			<div class="notice notice-success salsisyncnotice_message is-dismissibles" style="display:flex; align-items:center; justify-content:space-between;">
				<p><?php esc_html_e( '✅ Data Mapping Changed.', 'salsisync' ); ?></p>
				<a id="data-mapping-changed" class="button button-primary" href="<?php echo esc_url( 'admin.php?page=salsisync&tab=sync&action=update&limit=' . $custom_limit_value . '&api_type=default&fetchAll=' . $fetch_all_value ); ?>">
					<?php esc_html_e( 'Update Existing Products', 'salsisync' ); ?>
				</a>
			</div>
			<?php
		}
		?>
		<form method="post" action="">
			<?php wp_nonce_field( 'salsisyncsettings_tab_content__data_custom_mapping', 'salsisyncsettings_tab_content__data_custom_mapping_nonce' ); ?>
			<div class="salsisync-product__repeater-container">
				<div id="repeater-wrapper">
					<?php
					if ( ! empty( $saved_fields ) ) {
						foreach ( $saved_fields as $index => $field ) {
							echo '<div data-index="' . esc_attr( $index ) . '" class="repeater-group">';
							echo '<input required type="text" name="repeater_fields[' . esc_attr( $index ) . '][label]" value="' . esc_attr( $field['label'] ) . '" placeholder="' . esc_html__( 'Label', 'salsisync' ) . '" />';
							echo '<input required type="text" name="repeater_fields[' . esc_attr( $index ) . '][key]" value="' . esc_attr( $field['key'] ) . '" placeholder="' . esc_html__( 'Key', 'salsisync' ) . '" />';
							echo '<button type="button" class="dashicons dashicons-dismiss remove-row"></button>';
							if ( $field['key'] && ! array_search( $field['key'], $this->product_key_arr, true ) ) {
								echo '<p style="color:#b32d2e;" class="key__error_message">' . esc_html__( 'Key not found in the API response', 'salsisync' ) . '</p>';
								// @codingStandardsIgnoreStart
								// if ( $field['key'] && false === array_search( $field['key'], $this->product_key_arr, true ) ) {
								// echo '<p style="color:red;" class="key__error_message">' . esc_html__( 'Key not found in the API response', 'salsisync' ) . '</p>';
								// }
								// @codingStandardsIgnoreEnd
							}
							echo '</div>';
						}
					}
					?>
				</div>
				<button type="button" class="button button-default" id="add-row">
					<?php esc_html_e( 'Add New Field', 'salsisync' ); ?>
				</button>
			</div>
				<?php submit_button( 'Save Changes' ); ?>
		</form>
		<?php
	}
	/**
	 * Validata user inputs regarding the custom data mapping fields
	 *
	 * @param [type] $custom_data_mapping_keys User input keys.
	 * @param [type] $keys_from_api Keys from the API response.
	 * @return boolean
	 */
	public function salsisync__validate_input_keys( $custom_data_mapping_keys, $keys_from_api ) {
		$input_keys   = $custom_data_mapping_keys;
		$product_keys = $keys_from_api;

		// Ensure both are arrays before processing.
		if ( ! is_array( $input_keys ) || ! is_array( $product_keys ) ) {
			return false; // Invalid data, treat it as a failure.
		}

		// Loop through each input key and check if it exists in product_key_arr.
		foreach ( $input_keys as $key ) {
			if ( ! in_array( $key, $product_keys, true ) ) {
				return false; // Return false if any key is missing.
			}
		}

		return true; // All input keys exist.
	}
	/**
	 * Fetch all key from the API response
	 *
	 * @return Array
	 */
	public function salsisync_fetch_all_key_from_the_api_response() {
		// phpcs:ignore $json_file_path = SALSI_SYNC_DIR . 'data/salsify/api-first-page-response.json';
		$json_file_path = SALSI_SYNC_UPLOAD_DIR . 'data/salsify/api-first-page-response.json';
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
			foreach ( $products_data_arr as $product ) {
				$product_key_arr       = array_keys( $product );
				$this->product_key_arr = array_merge( $this->product_key_arr, $product_key_arr );
			}
			$this->product_key_arr = array_unique( $this->product_key_arr );
			return $this->product_key_arr;
		}
	}
	/**
	 * Check for changes in the custom data mapping fields.
	 *
	 * @param [type] $option_name comes from the db.
	 * @param [type] $old_value old value of the option.
	 * @param [type] $new_value new value of the option.
	 */
	public function salsisync__check_for_changes( $option_name, $old_value, $new_value ) {
		$changes = array();

		// Compare old and new data.
		foreach ( $new_value as $index => $new_item ) {
			if ( ! isset( $old_value[ $index ] ) ) {
				$changes[] = array(
					'type' => 'added',
					'data' => $new_item,
				);
			} elseif ( $new_item !== $old_value[ $index ] ) {
				$changes[] = array(
					'type' => 'modified',
					'data' => $new_item,
					'old'  => $old_value[ $index ],
				);
			}
		}

		// Check for removed items.
		foreach ( $old_value as $index => $old_item ) {
			if ( ! isset( $new_value[ $index ] ) ) {
				$changes[] = array(
					'type' => 'removed',
					'data' => $old_item,
				);
			}
		}
		// Log or handle the changes (for example, save them in the database or show an admin notice).
		if ( ! empty( $changes ) ) {
			update_option( 'salsisync__custom_mapping_data_changes_log', $changes ); // Save changes in the database.
		} else {
			update_option( 'salsisync__custom_mapping_data_changes_log', $changes ); // Save changes in the database.
		}
	}
	/**
	 * Compare repeater data
	 *
	 * @param [type] $old_data Old input data.
	 * @param [type] $new_data New input data.
	 * @return boolean
	 */
	public function salsisync__compare_repeater_data( $old_data, $new_data ) {
		// Ensure both are arrays and not empty.
		if ( ! is_array( $old_data ) || ! is_array( $new_data ) ) {
			return false;
		}
		/**
		 * If old data is empty and new data is not empty
		 */
		if ( ! empty( $old_data ) && empty( $new_data ) ) {
			return true;
		}
		/**
		 * If new data is empty and old data is not empty
		 */
		if ( empty( $old_data ) && ! empty( $new_data ) ) {
			return true;
		}
		/**
		 * If two data length is not same
		 */
		if ( count( $old_data ) !== count( $new_data ) ) {
			return true;
		}

		// Check if old and new data differ.
		$is_different = false;

		foreach ( $new_data as $index => $new_entry ) {
			if ( isset( $old_data[ $index ] ) ) {
				// Compare each key-value pair within the repeater fields.
				if ( $old_data[ $index ] !== $new_entry ) {
					$is_different = true;
					break;
				}
			} else {
				// If an index doesn't exist in the old data, it's a new entry.
				$is_different = true;
				break;
			}
		}

		return $is_different;
	}
}
