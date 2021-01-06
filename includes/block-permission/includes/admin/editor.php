<?php
/**
 * Add assets for the block editor
 *
 * @package block-permission
 * @since   1.0.0
 */

namespace BlockPermission\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Internal dependencies
 */
use function BlockPermission\Utils\get_asset_file as get_asset_file;
use function BlockPermission\Utils\get_user_roles as get_user_roles;

/**
 * Enqueue plugin specific editor scripts and styles
 *
 * @since 1.0.0
 */
function enqueue_editor_assets() {

	/**
	 * Since we are using admin_init, we need to make sure the js is only loaded
	 * on pages with the Block Editor, this includes FSE pagess.
	 */
	if ( ! is_block_editor_page() ) {
		return;
	}

	// Scripts.
	$asset_file = get_asset_file( 'dist/block-permission-editor' );

	wp_enqueue_script(
		'block-permission-editor-scripts',
		BLOCK_PERMISSION_PLUGIN_MODULE_URL . 'dist/block-permission-editor.js',
		array_merge( $asset_file['dependencies'], array( 'wp-api' ) ),
		$asset_file['version'],
		false // Need false to ensure our filters can target third-party plugins.
	);

	// Create a global variable to indicate whether we are in full control mode
	// or not. This is needed for the Block Permission attribute filter since
	// it will not allow us to fetch this data directly.
	$is_full_control_mode = 'const BlockPermissionFullControlMode = ' . wp_json_encode( is_full_control_mode() ) . ';';

	wp_add_inline_script(
		'block-permission-editor-scripts',
		$is_full_control_mode,
		'before'
	);

	// Styles.
	$asset_file = get_asset_file( 'dist/block-permission-editor-styles' );

	wp_enqueue_style(
		'block-permission-editor-styles',
		BLOCK_PERMISSION_PLUGIN_MODULE_URL . 'dist/block-permission-editor-styles.css',
		array(),
		$asset_file['version']
	);
}
add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\enqueue_editor_assets' );



/**
 * Make sure we are on a page with the Block Editor, this include FSE pages.
 *
 * @since 1.0.0
 *
 * @return bool Returns true or false.
 */
function is_block_editor_page() {
	global $pagenow;

	return (
		is_admin() &&
		( 'post.php' === $pagenow || 'post-new.php' === $pagenow || 'admin.php' === $pagenow )
	);
}

/**
 * See if we are in full control mode.
 *
 * @since 1.0.0
 *
 * @return bool Returns true or false.
 */
function is_full_control_mode() {
	$settings = get_option( 'ppc_block_permission_settings' );

	if ( isset( $settings['module_settings']['enable_full_control_mode'] ) ) {
		return $settings['module_settings']['enable_full_control_mode'];
	} else {
		return false;
	}
}
