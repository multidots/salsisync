<?php
/**
 * The core plugin class.
 *
 * @since      1.0.0
 * @package    Salsi_Sync
 * @subpackage Salsi_Sync/includes
 * @author     Multidots <info@multidots.com>
 */

namespace Salsi_Sync\Inc;

use Salsi_Sync\Inc\Blocks;
use Salsi_Sync\Inc\Traits\Singleton;

/**
 * Main class File.
 */
class Salsi_Sync {


	use Singleton;

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Salsi_Sync_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'SALSI_SYNC_VERSION' ) ) {
			$this->version = SALSI_SYNC_VERSION;
		} else {
			$this->version = '1.1.0';
		}
		$this->plugin_name = 'salsisync';

		Front::get_instance();
		Admin::get_instance();
		Activator::get_instance();
		Deactivator::get_instance();
		I18::get_instance();
		Blocks::get_instance();
		Dependency::get_instance();
		Options::get_instance();
	}
}
