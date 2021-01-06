<?php
/**
 * Register custom REST API routes.
 *
 * @package block-permission
 * @since   1.0.0
 */

namespace BlockPermission\RestApi;

defined( 'ABSPATH' ) || exit;

/**
 * Internal dependencies
 */
use PPC_Block_Permission_REST_Settings_Controller;
use PPC_Block_Permission_REST_Variables_Controller;

/**
 * Function to register our new routes from the controller.
 */
function register_routes() {
	$settings_controller = new PPC_Block_Permission_REST_Settings_Controller();
	$settings_controller->register_routes();
	$variables_controller = new PPC_Block_Permission_REST_Variables_Controller();
	$variables_controller->register_routes();
}
add_action( 'rest_api_init', __NAMESPACE__ . '\register_routes' );

/**
 * Include our custom REST API controllers.
 */
require_once BLOCK_PERMISSION_ABSPATH . 'includes/rest-api/controllers/class-block-permission-rest-settings-controller.php';
require_once BLOCK_PERMISSION_ABSPATH . 'includes/rest-api/controllers/class-block-permission-rest-variables-controller.php';
