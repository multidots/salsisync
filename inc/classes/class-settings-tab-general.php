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
class Settings_Tab_General {

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
	 * Undocumented variable
	 *
	 * @var string
	 */
	public $salsisync_api_key_connection_status = 'false';

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
			/**
			 * Dismiss the API connection success notice
			 */
			add_action( 'wp_ajax_nopriv_salsisync_dismiss_success_notice', array( $this, 'salsisync_dismiss_success_notice' ) );
			add_action( 'wp_ajax_salsisync_dismiss_success_notice', array( $this, 'salsisync_dismiss_success_notice' ) );
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
		 * General - API Settings
		 */
		register_setting( 'salsisync_general__settings_group', 'salsisync_api_token__key', 'sanitize_text_field' );
		register_setting( 'salsisync_general__settings_group', 'salsisync_orgs__key', 'sanitize_text_field' );
		// sections of the tab.
		add_settings_section( 'salsisync_general_page__section', esc_html__( 'API Settings', 'salsisync' ), null, 'salsisync_tab_general' );
		// fields of the tab.
		add_settings_field(
			'salsisync_api_token__key',
			esc_html__( 'API Token', 'salsisync' ) . '<span class="salsisync-product__help-tip"><span class="salsisync-product__help-inner-tip" tabindex="0" aria-label="' . esc_attr__( 'Enter the API token from salsify dasboard.', 'salsisync' ) . '"></span></span>',
			array( $this, 'salsisync_api_token__input__callback' ),
			'salsisync_tab_general',
			'salsisync_general_page__section',
			array(
				'label_for' => 'salsisync_api_token__key',
				'desc'      => esc_html__( 'Enter the API token from salsify dasboard.', 'salsisync' ),
				'tip'       => esc_attr__( 'Enter the API token from salsify dasboard.', 'salsisync' ),
				'desc_tip'  => true,
				'autoload'  => false,
			)
		);
		add_settings_field(
			'salsisync_orgs__key',
			esc_html__( 'ORGS Key', 'salsisync' ) . '<span class="salsisync-product__help-tip"><span class="salsisync-product__help-inner-tip" tabindex="0" aria-label="' . esc_attr__( 'Enter the ORGS Key from salsify dashboard.', 'salsisync' ) . '"></span></span>',
			array( $this, 'salsisync_org_key__input__callback' ),
			'salsisync_tab_general',
			'salsisync_general_page__section',
			array(
				'label_for' => 'salsisync_orgs__key',
				'desc'      => esc_html__( 'Enter the ORGS Key from salsify dashboard.', 'salsisync' ),
				'tip'       => esc_attr__( 'Enter the ORGS Key from salsify dashboard.', 'salsisync' ),
				'desc_tip'  => true,
				'autoload'  => false,
			)
		);
	}

	/**
	 * Handle API Token Input
	 * Tab - General
	 * Section ID - [salsisync_api_token__key]
	 */
	public function salsisync_api_token__input__callback() {
		$value                 = get_option( 'salsisync_api_token__key' );
		$api_connection_status = get_option( 'salsify_api_key_valid' );
		$set_read_only         = ( 'true' === $api_connection_status || true === $api_connection_status ) ? 'readonly' : '';
		echo '<input required class="regular-text" type="text" ' . esc_html( $set_read_only ) . ' placeholder="' . esc_html__( 'Add API token here', 'salsisync' ) . '" name="salsisync_api_token__key" value="' . esc_attr( $value ) . '">';
	}
	/**
	 * Handle ORGS Key Input
	 * Tab - General
	 * Section ID - [salsisync_orgs__key]
	 */
	public function salsisync_org_key__input__callback() {
		$value                 = get_option( 'salsisync_orgs__key' );
		$api_connection_status = get_option( 'salsify_api_key_valid' );
		$set_read_only         = ( 'true' === $api_connection_status || true === $api_connection_status ) ? 'readonly' : '';
		echo '<input required class="regular-text" type="text" ' . esc_html( $set_read_only ) . ' placeholder="' . esc_html__( 'Add ORGS key here', 'salsisync' ) . '" name="salsisync_orgs__key" value="' . esc_attr( $value ) . '">';
	}
	/**
	 * Salsify General Settings Tab Content
	 */
	public function salsisync_settings_tab_content__general() {
		if ( 'true' === get_option( 'salsisync_ajax_data_insert_running_status' ) || true === get_option( 'salsisync_ajax_data_insert_running_status' ) ) {
			?>
			<div class="notice notice-error salsisyncnotice_message"><p><?php esc_html_e( '❗️Data Insertion is Running. Please wait.', 'salsisync' ); ?></p></div>
			<?php
		}
		/**
		 * If nonce is not valid then show the notice message
		 */
		if ( isset( $_POST['salsisyncsettings_tab_content__general_nonce'] ) && ! wp_verify_nonce( sanitize_key( $_POST['salsisyncsettings_tab_content__general_nonce'] ), 'salsisyncsettings_tab_content__general' ) ) {
			?>
			<div class="notice notice-error salsisyncnotice_message"><p><?php esc_html_e( '❌ Wrong Validation Request', 'salsisync' ); ?></p></div>
			<?php
		}
		/**
		 * If reset nonce is not valid then show the notice message
		 */
		if ( isset( $_POST['salsisyncsettings_tab_content__general_reset_nonce'] ) && ! wp_verify_nonce( sanitize_key( $_POST['salsisyncsettings_tab_content__general_reset_nonce'] ), 'salsisyncsettings_tab_content__general_reset' ) ) {
			?>
			<div class="notice notice-error salsisyncnotice_message"><p><?php esc_html_e( '❌ Wrong Reset Validation Request', 'salsisync' ); ?></p></div>
			<?php
		}
		/**
		 * If reset nonce is valid then reset the data
		 */
		if ( isset( $_POST['salsisyncsettings_tab_content__general_reset_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['salsisyncsettings_tab_content__general_reset_nonce'] ), 'salsisyncsettings_tab_content__general_reset' ) ) {
			/**
			 * Reset the data
			 * by clicking the reset button
			 */
			if ( isset( $_POST['reset-form'] ) ) {
				update_option( 'salsify_api_key_valid', 'false' );
				update_option( 'salsisync_api_token__key', '' );
				update_option( 'salsisync_orgs__key', '' );
				update_option( 'salsisync_dismiss_success_notice', 'false' );
			}
		}
		/**
		 * Check if the API token is set
		 * Then show the API connection status
		 * and return from execution rest of the code
		 */
		$api_connection_status                   = get_option( 'salsify_api_key_valid' );
		$salsisync_dismiss_success_notice_status = wp_unslash( get_option( 'salsisync_dismiss_success_notice' ) );

		if ( 'true' === $api_connection_status ) {
			if ( 'false' === $salsisync_dismiss_success_notice_status || false === $salsisync_dismiss_success_notice_status ) :
				?>
				<div class="salsisyncnotice_message notice notice-success is-dismissible" style="margin:0 0 20px; padding:15px;">
					<span><?php esc_html_e( 'The API key is valid and successfully connected.', 'salsisync' ); ?></span>
				</div>
				<?php
			endif;
		}
		if ( isset( $_POST['salsisyncsettings_tab_content__general_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['salsisyncsettings_tab_content__general_nonce'] ), 'salsisyncsettings_tab_content__general' ) ) {
			/**
			 * Update the value to option table
			 */
			$salsisync_api_token__key = filter_input( INPUT_POST, 'salsisync_api_token__key', FILTER_SANITIZE_SPECIAL_CHARS );
			$salsisync_orgs__key      = filter_input( INPUT_POST, 'salsisync_orgs__key', FILTER_SANITIZE_SPECIAL_CHARS );
			if ( isset( $salsisync_api_token__key ) ) {
				update_option( 'salsisync_api_token__key', $salsisync_api_token__key );
			}
			if ( isset( $salsisync_orgs__key ) ) {
				update_option( 'salsisync_orgs__key', $salsisync_orgs__key );
			}
		}
		/**
		 * Check the Salsify API credentials and show the status message
		 */
		if ( 'false' === $api_connection_status || false === $api_connection_status || '' === get_option( 'salsify_api_key_valid' ) ) {
			$api_connection_message = $this->salsisync_verify_salsify_api_credentials();
			echo wp_kses_post( $api_connection_message );
		}
		?>
		<form class="salsisync-product__form" method="post" action="admin.php?page=salsisync&tab=general">
			<?php
			wp_nonce_field( 'salsisyncsettings_tab_content__general', 'salsisyncsettings_tab_content__general_nonce' );
			settings_fields( 'salsisync_general__settings_group' );
			do_settings_sections( 'salsisync_tab_general' );
			?>
			<div class="salsisync-product__form-buttons">
			<?php
			submit_button(
				'',
				'primary api-connection-button',
				esc_html__( 'submit', 'salsisync' ),
				true,
				array(
					'id' => ( 'true' === $this->salsisync_api_key_connection_status || 'true' === $api_connection_status ) ? 'api-connected-button' : 'api-connection-button',
				)
			);

			if ( 'true' === $this->salsisync_api_key_connection_status || 'true' === $api_connection_status ) :
				?>
				<button 
				class="button" 
				form="reset-form" 
				<?php echo 'true' == get_option( 'salsisync_ajax_data_insert_running_status' ) ? 'disabled' : ''; //phpcs:ignore ?>
				><?php esc_html_e( 'Reset', 'salsisync' ); ?></button>
			</div>	
				<?php
			endif;
			?>
		</form>
		<form id="reset-form" class="salsisync-product__form" method="post" action="admin.php?page=salsisync&tab=general">
			<?php
			wp_nonce_field( 'salsisyncsettings_tab_content__general_reset', 'salsisyncsettings_tab_content__general_reset_nonce' );
			?>
			<input type="hidden" name="reset-form" value="true">
		</form>
		<?php
	}
	/**
	 * Verify Salsify API Credentials
	 * It will only fired when API is not connected
	 * and salsify_api_key_valid is false
	 */
	public function salsisync_verify_salsify_api_credentials() {
		/**
		 * Get the stored API token from the WordPress options table.
		 */
		$api_token  = get_option( 'salsisync_api_token__key' );
		$orgs_token = get_option( 'salsisync_orgs__key' );
		/**
		 * Define the file path for the API response.
		 */
		// phpcs:ignore $file_path = SALSI_SYNC_DIR . 'data/salsify/api-first-page-response.json';
		$file_path = SALSI_SYNC_UPLOAD_DIR . 'data/salsify/api-first-page-response.json';
		// Check if the file exists.
		if ( file_exists( $file_path ) ) {
			// todo: replace file_put_contents() to wp_filesystems.
			file_put_contents( $file_path, '' ); // phpcs:ignore
		}
		/**
		 * Check if the API token is not set
		 * Then show the API connection status.
		 */
		if ( ! $api_token ) {
			return sprintf(
				'<div class="salsisyncnotice_message notice notice-error"><p>%s</p></div>',
				esc_html__( 'API token is not configured. Please set it first.', 'salsisync' )
			);
		}
		/**
		 * Define the Salsify API endpoint for POST requests.
		 */
		//phpcs:ignore $endpoint = "https://app.salsify.com/api/v1/orgs/{$orgs_token}/products/{$productId}";
		$first_page_endpoint = "https://app.salsify.com/api/v1/orgs/{$orgs_token}/products?page=1&per_page=100";
		/**
		 * Set up the arguments for the request.
		 */
		$args = array(
			'headers' => array(
				'Authorization' => 'Bearer ' . $api_token,
				'Content-Type'  => 'application/json',
			),
			'timeout' => 20,
		);
		/**
		 * Make the POST request to the Salsify API.
		 */
		$response = wp_remote_get( $first_page_endpoint, $args );
		/**
		 * Check for errors.
		 */
		if ( is_wp_error( $response ) ) {
			return 'Request error: ' . $response->get_error_message();
		}
		/**
		 * Get the response body from the response.
		 */
		$body = wp_remote_retrieve_body( $response );
		/**
		 * Decode the JSON response body.
		 */
		$result = json_decode( $body, true );
		/**
		 * Convert the decoded data back to a JSON string.
		 * todo: replace json_encode() to wp_json_encode()
		 */
		$new_formated_data = json_encode( $result, JSON_PRETTY_PRINT ); // phpcs:ignore
		/**
		 * Save response in a file.
		 * todo: replace file_put_contents() to wp_filesystem
		 */
		file_put_contents( $file_path, $new_formated_data ); // phpcs:ignore
		/**
		 * Check if the API key is invalid.
		 */
		if ( is_array( $result ) ) {
			if ( array_key_exists( 'error', $result ) ) {
				update_option( 'salsify_api_key_valid', 'false' );
				return sprintf(
					'<div class="salsisyncnotice_message notice notice-error"><p>%s</p></div>',
					esc_html__( 'API key is not valid. Please enter the valid key.', 'salsisync' )
				);
			}
		} else {
			update_option( 'salsify_api_key_valid', 'false' );
			return sprintf(
				'<div class="salsisyncnotice_message notice notice-error"><p>%s</p></div>',
				esc_html__( 'ORGS key is not valid. Please enter the valid key.', 'salsisync' )
			);
		}
		/**
		 * Update the option table with the API key status
		 */
		if ( json_last_error() === JSON_ERROR_NONE ) {
			update_option( 'salsify_api_key_valid', 'true' );
			$this->salsisync_api_key_connection_status = 'true';
			return sprintf(
				'<div class="salsisyncnotice_message notice notice-success is-dismissible"><p>%s</p></div>',
				esc_html__( '✅ The API key is valid and successfully connected.', 'salsisync' )
			);
		} else {
			update_option( 'salsify_api_key_valid', 'false' );
			$this->salsisync_api_key_connection_status = 'false';
			return '<div class="salsisyncnotice_message notice notice-error"><p>Error decoding JSON: ' . json_last_error_msg() . '</p></div>';
		}
	}
	/**
	 * Dismiss success notice for the ajax call.
	 *
	 * @return void
	 */
	public function salsisync_dismiss_success_notice() {
		$dismiss_status       = 'hss';
		$check_dismiss_status = get_option( 'salsisync_dismiss_success_notice' );
		if ( 'false' === $check_dismiss_status || false === $check_dismiss_status ) {
			update_option( 'salsisync_dismiss_success_notice', 'true' );
			$dismiss_status = 'true';
		}
		echo esc_html( $dismiss_status );
		wp_die();
	}
}
