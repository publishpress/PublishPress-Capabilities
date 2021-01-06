<?php

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'BLOCK_PERMISSION_MODULE_FILE' ) ) {
	define( 'BLOCK_PERMISSION_MODULE_FILE', __FILE__ );
}

if ( ! class_exists( 'PPC_Block_Permission' ) ) {
	include_once dirname( BLOCK_PERMISSION_MODULE_FILE ) . '/includes/class-block-permission.php';
}

/**
 * The main function that returns the Block Permission class
 *
 * @since 1.0.0
 * @return object|PPC_Block_Permission
 */
function ppc_block_permission_load_module() {
	return PPC_Block_Permission::instance();
}

// Get the module after plugin is loaded.
add_action( 'plugins_loaded', 'ppc_block_permission_load_module' );
