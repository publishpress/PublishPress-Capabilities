<?php
/**
 * Plugin Name: PublishPress Capabilities
 * Plugin URI: https://publishpress.com/capability-manager/
 * Description: Manage WordPress role definitions, per-site or network-wide. Organizes post capabilities by post type and operation.
 * Version: 1.10.1
 * Author: PublishPress
 * Author URI: https://publishpress.com/
 * Text Domain: capsman-enhanced
 * Domain Path: /lang/
 * Min WP Version: 4.9.7
 * Requires PHP: 5.6.20
 * License: GPLv3
 *
 * Copyright (c) 2020 PublishPress
 *
 * ------------------------------------------------------------------------------
 * Based on Capability Manager
 * Author: Jordi Canals
 * Copyright (c) 2009, 2010 Jordi Canals
 * ------------------------------------------------------------------------------
 *
 * @package 	capability-manager-enhanced
 * @author		PublishPress
 * @copyright   Copyright (C) 2009, 2010 Jordi Canals; modifications Copyright (C) 2020 PublishPress
 * @license		GNU General Public License version 3
 * @link		https://publishpress.com/
 * @version 	1.10.1
 */

if (!defined('CAPSMAN_VERSION')) {
	define('CAPSMAN_VERSION', 			'1.10.1');
	define('CAPSMAN_ENH_VERSION', 		'1.10.1');
	define('PUBLISHPRESS_CAPS_VERSION', '1.10.1');
}

foreach (get_option('active_plugins') as $plugin_file) {
	if ( false !== strpos($plugin_file, 'capsman.php') ) {
		add_action('admin_notices', function() {
			$message = __( '<strong>Error:</strong> PublishPress Capabilities cannot function because another copy of Capability Manager is active.', 'capsman-enhanced' );
			echo '<div id="message" class="error fade" style="color: black">' . $message . '</div>';
		});
		return;
	}
}

$pro_active = false;

foreach ((array)get_option('active_plugins') as $plugin_file) {
    if (false !== strpos($plugin_file, 'capabilities-pro.php')) {
        $pro_active = true;
        break;
    }
}

if (!$pro_active && is_multisite()) {
    foreach (array_keys((array)get_site_option('active_sitewide_plugins')) as $plugin_file) {
        if (false !== strpos($plugin_file, 'capabilities-pro.php')) {
            $pro_active = true;
            break;
        }
    }
}

if ($pro_active) {
    add_filter(
        'plugin_row_meta', 
        function($links, $file)
        {
            if ($file == plugin_basename(__FILE__)) {
                $links[]= __('<strong>This plugin can be deleted.</strong>', 'press-permit-core');
            }

            return $links;
        },
        10, 2
    );
}

if (defined('CME_FILE') || $pro_active) {
	return;
}

define ( 'CME_FILE', __FILE__ );
define ('PUBLISHPRESS_CAPS_ABSPATH', __DIR__);

require_once (dirname(__FILE__) . '/includes/functions.php');

if (is_admin()) {
	require_once (dirname(__FILE__) . '/includes/functions-admin.php');
}

// ============================================ START PROCEDURE ==========

// Check required PHP version.
if ( version_compare(PHP_VERSION, '5.4.0', '<') ) {
	// Send an armin warning
	add_action('admin_notices', function() {
		$data = get_plugin_data(__FILE__);
		load_plugin_textdomain('capsman-enhanced', false, basename(dirname(__FILE__)) .'/lang');

		echo '<div class="error"><p><strong>' . __('Warning:', 'capsman-enhanced') . '</strong> '
			. sprintf(__('The active plugin %s is not compatible with your PHP version.', 'capsman-enhanced') .'</p><p>',
				'&laquo;' . $data['Name'] . ' ' . $data['Version'] . '&raquo;')
			. sprintf(__('%s is required for this plugin.', 'capsman-enhanced'), 'PHP-5 ')
			. '</p></div>';
	});
} else {
	global $pagenow;

	if ( is_admin() && 
	( isset($_REQUEST['page']) && in_array( $_REQUEST['page'], array( 'capsman', 'capsman-tool' ) ) 
	|| ( ! empty($_SERVER['SCRIPT_NAME']) && strpos( $_SERVER['SCRIPT_NAME'], 'p-admin/plugins.php' ) && ! empty($_REQUEST['action'] ) ) 
	|| ( isset($_GET['action']) && 'reset-defaults' == $_GET['action'] )
	|| in_array( $pagenow, array( 'users.php', 'user-edit.php', 'profile.php', 'user-new.php' ) )
	) ) {
		global $capsman;

		// Run the plugin
		require_once ( dirname(__FILE__) . '/framework/lib/formating.php' );
		require_once ( dirname(__FILE__) . '/framework/lib/users.php' );
		
		require_once ( dirname(__FILE__) . '/includes/manager.php' );
		$capsman = new CapabilityManager();
	} else {
		load_plugin_textdomain('capsman-enhanced', false, basename(dirname(__FILE__)) .'/lang');
		add_action( 'admin_menu', 'cme_submenus', 20 );
	}

	if (is_admin() && !defined('PUBLISHPRESS_CAPS_PRO_VERSION')) {
		require_once(__DIR__ . '/includes/CoreAdmin.php');
		new \PublishPress\Capabilities\CoreAdmin();
	}
}

add_action( 'init', '_cme_init' );
add_action( 'plugins_loaded', '_cme_act_pp_active', 1 );

add_action( 'init', '_cme_cap_helper', 49 );  // Press Permit Cap Helper, registered at 50, will leave caps which we've already defined
//add_action( 'wp_loaded', '_cme_cap_helper_late_init', 99 );	// now instead adding registered_post_type, registered_taxonomy action handlers for latecomers
																// @todo: do this in PP Core also

if ( is_multisite() )
	require_once ( dirname(__FILE__) . '/includes/network.php' );
