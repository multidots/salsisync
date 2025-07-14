<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       https://www.multidots.com/
 * @since      1.0.0
 *
 * @package    Salsi_Sync
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Option names to delete (replace with your actual option names).
$options_to_delete = array(
	'salsisync_api_token__key',
	'salsisync_orgs__key',
	'salsify_api_key_valid',
	'salsisync_product_name__key', // string.
	'salsisync_product_name__key__map_status', // value '1' (string) for mapped, '' for not mapped.
	'salsisync_product_category__key', // string.
	'salsisync_product_category__key__map_status', // value '1' (string) for mapped, '' for not mapped.
	'salsisync_product_main_image__key', // string.
	'salsisync_product_main_image__key__map_status', // value '1' (string) for mapped, '' for not mapped.
	'salsisync_product_description__key', // string.
	'salsisync_product_description__key__map_status', // value '1' (string) for mapped, '' for not mapped.
	'salsisync_product_description__key_required', // value '1' (string) for required, '' for not required.
	'salsisync_ajax_data_insert_running_status', // 'true' (string) if running, 'false' (string) if not running.
	'salsisync_test_insert_status', // 'true' (string) if test insert, 'false' (string) if not test insert.
	'salsisync_dismiss_success_notice', // 'true' (string) if dismissed, 'false' (string) if not dismissed.
	'salsisync_salsify_new_products', // JSON string.
	'salsisync_salsify_updated_products', // JSON string.
);

// Delete each option.
foreach ( $options_to_delete as $option ) {
	delete_option( $option );
}
