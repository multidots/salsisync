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
class Settings_Tab_Template {

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
	 * Woocommerce template for product
	 *
	 * @var string
	 */
	public $product_template = '';
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

		//phpcs:ignore $this->salsisync_product_name__key__map_status = get_option('salsisync_product_name__key__map_status');

		if ( is_admin() ) {
			add_action( 'admin_init', array( $this, 'salsisync_register_settings__group__section__fields' ) );
		}
		// @codingStandardsIgnoreStart
		// $this->product_template = get_option('salsisync_product_name__key');
		// Hook to change the single product template
		// add_filter( 'theme_block_templates', array($this, 'salsisync__fse_plugin_register_templates') );
		// @codingStandardsIgnoreEnd
		add_filter( 'template_include', array( $this, 'salsisyncload_custom_single_product_template_for_woocommerce' ), 99 );
	}
	/**
	 * Register the settings group, section and fields for the template tab.
	 *
	 * @return void
	 */
	public function salsisync_register_settings__group__section__fields() {
		register_setting( 'salsisync_template__settings_group', 'salsisync_product_template__key_required', 'sanitize_text_field' );
		add_settings_section(
			'salsisync_template_page__section',
			esc_html__( 'Product Template', 'salsisync' ),
			null,
			'salsisync_tab_template'
		);

		add_settings_field(
			'salsisync_product_template__key_required',
			esc_html__( 'Select Template', 'salsisync' ) . '<span class="salsisync-product__help-tip"><span class="salsisync-product__help-inner-tip" tabindex="0" aria-label="' . esc_attr__( 'Select woocommerce template for default view', 'salsisync' ) . '"></span></span>',
			array( $this, 'salsisync_tab_template__dropdown' ),
			'salsisync_tab_template',
			'salsisync_template_page__section',
			array()
		);
	}
	/**
	 * Product template dropdown markup.
	 *
	 * @return void
	 */
	public function salsisync_tab_template__dropdown() {
		$salsisync_product_template__option = get_option( 'salsisync_product_template__key_required' );
		/**
		 * If we need to set a placeholder for the select dropdown.
		 * <option value="0"><?php // esc_html_e( 'Select Template', 'salsisync' ); ?></option>
		 */
		?>
		<div>
			<select class="regular-text" name="salsisync_product_template__key_required" id="salsisync_product_template__key_required">
				<option value="default-woo" <?php echo 'default-woo' === $salsisync_product_template__option ? 'selected' : ''; ?>><?php esc_html_e( 'Default Woo', 'salsisync' ); ?></option>
				<option value="salsify-woo-1" <?php echo 'salsify-woo-1' === $salsisync_product_template__option ? 'selected' : ''; ?>><?php esc_html_e( 'Salsify Woo 1', 'salsisync' ); ?></option>
				<option value="salsify-woo-2" <?php echo 'salsify-woo-2' === $salsisync_product_template__option ? 'selected' : ''; ?>><?php esc_html_e( 'Salsify Woo 2', 'salsisync' ); ?></option>
			</select>
		</div>
		<?php
	}
	/**
	 * Render view for the template tab.
	 *
	 * @return void
	 */
	public function salsisyncsettings_tab_content__template() {
		/**
		 * If data insertion is running then show the notice message.
		 */
		if ( 'true' == get_option( 'salsisync_ajax_data_insert_running_status' ) ) { //phpcs:ignore
			?>
			<div class="notice notice-error salsisyncnotice_message"><p><?php esc_html_e( '❗️Data Insertion is Running. Please wait.', 'salsisync' ); ?></p></div>
			<?php
		}

		if ( isset( $_POST['salsisyncsettings_tab_content__template_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['salsisyncsettings_tab_content__template_nonce'] ), 'salsisyncsettings_tab_content__template' ) ) {
			/**
			 * Update the value to option table.
			 */
			if ( isset( $_POST['salsisync_product_template__key_required'] ) ) {
				update_option( 'salsisync_product_template__key_required', sanitize_key( $_POST['salsisync_product_template__key_required'] ) );
			}
			?>
			<div class="notice notice-success salsisyncnotice_message is-dismissible"><p><?php esc_html_e( '✅ Data Updated', 'salsisync' ); ?></p></div>
			<?php
		}
		if ( get_option( 'salsify_api_key_valid' ) == 'false' || get_option( 'salsify_api_key_valid' ) == false ) { //phpcs:ignore
			?>
				<div class="notice notice-error salsisyncnotice_message">
					<p><?php esc_html_e( 'Connect API First', 'salsisync' ); ?></p>
				</div>
			<?php
			return;
		}
		?>
		<form class="salsisync-template__form salsisync-product__form" method="post" action="admin.php?page=salsisync&tab=template">
			<?php
			wp_nonce_field( 'salsisyncsettings_tab_content__template', 'salsisyncsettings_tab_content__template_nonce' );
			settings_fields( 'salsisync_template__settings_group' );
			do_settings_sections( 'salsisync_tab_template' );
			?>
			<div class="salsisync-template__form-buttons">
			<?php
			submit_button(
				'',
				'primary api-connection-button',
				esc_html__( 'submit', 'salsisync' ),
				true,
				//phpcs:ignore ($this->salsisync_api_key_connection_status == 'true' || $api_connection_status == 'true') ? 'disabled' : ''
			);
			?>
		</form>
		<?php
	}
	/**
	 * Check if the theme is a full site editing theme.
	 *
	 * @return boolean
	 */
	public function salsisync__check_if_fse_theme() {
		// Check if WordPress has the function to determine if it's a block theme. meading fse theme.
		if ( function_exists( 'wp_is_block_theme' ) ) {
			return wp_is_block_theme();
		}
		// If the above function is not available, manually check for `theme.json` and `block-templates`.
		$theme = wp_get_theme();
		// Check if the theme.json file exists in the root of the theme directory.
		$has_theme_json = file_exists( get_template_directory() . '/theme.json' );
		// Check if the block-templates directory exists in the theme directory.
		$has_block_templates = is_dir( get_template_directory() . '/block-templates' );
		// Return true if either theme.json or block-templates directory is found.
		return $has_theme_json || $has_block_templates;
	}
	/**
	 * Redirect to the custom single product template for WooCommerce.
	 *
	 * @param [type] $template string and choosen value from the template dropdown.
	 * @return String
	 */
	public function salsisyncload_custom_single_product_template_for_woocommerce( $template ) {
		// Check if we are on a WooCommerce single product page.
		if ( is_singular( 'product' ) ) {
			// Get the selected template from the plugin options.
			$selected_template = get_option( 'salsisync_product_template__key_required' );
			// If a custom template is selected, load it.
			if ( 'salsify-woo-1' === $selected_template ) {
				// Path to your custom template file.
				// need full local path to file that starts from /users/...
				if ( $this->salsisync__check_if_fse_theme() ) {
					$custom_template_path = SALSI_SYNC_DIR . 'templates/single-product/salsify-woo-1-fse.php';
				} else {
					$custom_template_path = SALSI_SYNC_DIR . 'templates/single-product/salsify-woo-1.php';
				}
				// Check if the custom template exists.
				if ( file_exists( $custom_template_path ) ) {
					return $custom_template_path;
				}
			} elseif ( 'salsify-woo-2' === $selected_template ) {
				// Path to your custom template file.
				// need full local path to file that starts from /users/...
				$custom_template_path = SALSI_SYNC_DIR . 'templates/single-product/salsify-woo-2.php';
				// Check if the custom template exists.
				if ( file_exists( $custom_template_path ) ) {
					return $custom_template_path;
				}
			}
		}
		// Return the default template if no custom template is selected or found.
		return $template;
	}
	/**
	 * Register custom templates in the plugin.
	 *
	 * @param array $templates List of block templates.
	 * @return array Modified list of block templates.
	 */
	public function salsisync__fse_plugin_register_templates( $templates ) {
		$plugin_template_dir = plugin_dir_path( __FILE__ ) . 'templates'; //phpcs:ignore

		$plugin_templates = array(
			'single-product' => array(
				'title' => esc_html__( 'Custom Single Product', 'salsisync' ),
				'file'  => SALSI_SYNC_DIR . 'templates/single-product/salsify-woo-1.html',
			),
		);
		return array_merge( $templates, $plugin_templates );
	}
}
