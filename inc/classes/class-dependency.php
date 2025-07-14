<?php
// phpcs:ignoreFile
namespace Salsi_Sync\Inc;

use Salsi_Sync\Inc\Traits\Singleton;

/**
 * Dependency class file.
 */
class Dependency {

	use Singleton;

	public $site_installed_plugins = array();
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		// if (!did_action('woocommerce/loaded')) {
			add_action( 'admin_notices', array( $this, 'check_dependend_plugin_woocommerce' ) );
		// }
	}

	public function check_dependend_plugin_woocommerce() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		$woocommerce_main_file = 'woocommerce/woocommerce.php';

		if ( $this->salsisync_check_plugin_form_site( $woocommerce_main_file ) == false ) {
			/**
			 * if plugin not found
			 */
			$activation_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=woocommerce' ), 'install-plugin_woocommerce' );
			$message        = sprintf(
				__( '<strong>Salsi Sync</strong> requires <strong>Woocommerce</strong> plugin to be installed and activated. Please install <strong>Woocommerce</strong> to continue.', 'salsisync' ),
				'<strong>',
				'</strong>'
			);
			$button_text    = __( 'Install Woocommerce Now', 'salsisync' );

		} else {
			/**
			 * if found
			 */
			$activation_url = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $woocommerce_main_file . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $woocommerce_main_file );
			$message        = __( '<strong>Salsi Sync</strong> requires <strong>Woocommerce</strong> plugin to be active. Please activate Woocommerce to continue.', 'salsisync' );
			$button_text    = __( 'Activate Woocommerce Now', 'salsisync' );
		}

		if ( ! is_plugin_active( $woocommerce_main_file ) ) :
			$button = '<p><a href="' . esc_url( $activation_url ) . '" class="button-primary">' . esc_html( $button_text ) . '</a></p>';
			printf( '<div class="error"><p>%1$s</p>%2$s</div>', wp_kses_post( $message ), $button );
		endif;
	}

	public function salsisync_check_plugin_form_site( $plugin_base_name = '' ) {
		wp_cache_flush();
		$installed_plugins = get_plugins();
		if ( array_key_exists( $plugin_base_name, $installed_plugins ) ) {
			return true;
		} else {
			return false;
		}
	}
}
