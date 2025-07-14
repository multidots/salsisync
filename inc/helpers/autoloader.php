<?php
/**
 * Autoloader file for theme.
 *
 * @package salsisync
 */

namespace Salsi_Sync\Inc\Helpers;

/**
 * Auto loader function.
 *
 * @param string $autoload_resource Source namespace.
 *
 * @return void
 */
function autoloader( $autoload_resource = '' ) {
	$resource_path  = false;
	$namespace_root = 'Salsi_Sync\\';
	$resource       = trim( (string) $autoload_resource, '\\' );

	if ( empty( $resource ) || strpos( $resource, '\\' ) === false || strpos( $resource, $namespace_root ) !== 0 ) {
		// Not our namespace, bail out.
		return;
	}

	// Remove our root namespace.
	$resource = str_replace( $namespace_root, '', $resource );

	$path = explode(
		'\\',
		str_replace( '_', '-', strtolower( $resource ) )
	);

	/**
	 * Time to determine which type of resource path it is,
	 * so that we can deduce the correct file path for it.
	 */
	if ( empty( $path[0] ) || empty( $path[1] ) ) {
		return;
	}

	$directory = '';
	$file_name = '';

	if ( 'inc' === $path[0] ) {

		switch ( $path[1] ) {
			case 'traits':
				$directory = 'traits';
				$file_name = sprintf( 'trait-%s', trim( strtolower( $path[2] ) ) );
				break;

			case 'classes':
				$directory = 'classes';
				$file_name = sprintf( 'class-%s', trim( strtolower( $path[1] ) ) );
				break;

			default:
				if ( ! empty( $path[2] ) ) {
					$directory = sprintf( 'classes/%s', $path[1] );
					$file_name = sprintf( 'class-%s', trim( strtolower( $path[2] ) ) );
					break;
				} else {
					$directory = 'classes';
					$file_name = sprintf( 'class-%s', trim( strtolower( $path[1] ) ) );
					break;
				}
		}

		$resource_path = sprintf( '%s/inc/%s/%s.php', untrailingslashit( SALSI_SYNC_DIR ), $directory, $file_name );

	}

	/**
	 * Load blocks class file from assets directory.
	 */
	if ( 'blocks' === $path[0] && ! empty( $path[1] ) ) {
		$block_dir_path = SALSI_SYNC_DIR . 'assets/src/js/blocks/' . $path[1];
		$resource_path  = sprintf( '%s/block.php', untrailingslashit( $block_dir_path ) );
	}

	/**
	 * If $is_valid_file has 0 means valid path or 2 means the file path contains a Windows drive path.
	 */
	$is_valid_file = validate_file( $resource_path );

	if ( ! empty( $resource_path ) && file_exists( $resource_path ) && ( 0 === $is_valid_file || 2 === $is_valid_file ) ) {
		// We already making sure that file is exists and valid.
		require_once( $resource_path ); // phpcs:ignore
	}
}

spl_autoload_register( '\Salsi_Sync\Inc\Helpers\autoloader' );
