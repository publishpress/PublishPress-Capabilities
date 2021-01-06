<?php
/**
 * Register custom REST API routes.
 *
 * @package block-permission
 * @since   1.0.0
 */

namespace BlockPermission\RestRoutes;

/**
 * WordPress dependencies
 */
use WP_REST_Server;
use WP_REST_Response;

/**
 * Internal dependencies
 */
use function BlockPermission\Utils\get_user_roles as get_user_roles;
use function BlockPermission\Utils\get_current_user_role as get_current_user_role;

/**
 * Register our custom REST API routes.
 *
 * @since 1.0.0
 */
function register_routes() {
	$namespace = 'block-permission/v1';

	register_rest_route(
		$namespace,
		'/settings',
		array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => __NAMESPACE__ . '\get_settings',
				'permission_callback' => '__return_true', // Read only, so anyone can view.
				'args'                => array(),
			),
		)
	);

	register_rest_route(
		$namespace,
		'/variables',
		array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => __NAMESPACE__ . '\get_variables',
				'permission_callback' => '__return_true', // Read only, so anyone can view.
				'args'                => array(),
			),
		)
	);
}
add_action( 'rest_api_init', __NAMESPACE__ . '\register_routes' );

/**
 * Get the Block Permission module settings.
 *
 * @since 1.0.0
 *
 * @return WP_Error|WP_REST_Response
 */
function get_settings() {
	$settings = get_option( 'ppc_block_permission_settings' );

	if ( $settings ) {
		return new WP_REST_Response( $settings, 200 );
	} else {
		return new WP_Error( '404', __( 'Something went wrong, the visibility settings could not be found.', 'capsman-enhanced' ) );
	}
}

/**
 * Get module variables.
 *
 * @since 1.0.0
 *
 * @return WP_REST_Response
 */
function get_variables() {
	$settings = get_option( 'ppc_block_permission_settings' );

	if ( isset( $settings['module_settings']['enable_full_control_mode'] ) ) {
		$is_full_control_mode = $settings['module_settings']['enable_full_control_mode'];
	} else {
		$is_full_control_mode = false;
	}

	$plugin_variables = array(
		'version'     => BLOCK_PERMISSION_VERSION,
		'settingsUrl' => BLOCK_PERMISSION_SETTINGS_URL,
		'reviewUrl'   => '',
		'supportUrl'  => BLOCK_PERMISSION_SUPPORT_URL,
	);

	$variables = array(
		'currentUsersRoles' => get_current_user_role(),
		'userRoles'         => get_user_roles(),
		'pluginVariables'   => $plugin_variables,
		'isFullControlMode' => $is_full_control_mode,
	);

	return new WP_REST_Response( $variables, 200 );
}
