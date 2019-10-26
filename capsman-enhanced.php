<?php
/**
 * Plugin Name: Capability Manager Enhanced
 * Plugin URI: https://publishpress.com
 * Description: Manage WordPress role definitions, per-site or network-wide. Organizes post capabilities by post type and operation.
 * Version: 1.8.1
 * Author: PublishPress
 * Author URI: https://publishpress.com
 * Text Domain: capsman-enhanced
 * Domain Path: /lang/
 * License: GPLv3
 *
 * Copyright (c) 2019 PublishPress
 *
 * ------------------------------------------------------------------------------
 * Based on Capability Manager
 * Author: Jordi Canals
 * Copyright (c) 2009, 2010 Jordi Canals
 * ------------------------------------------------------------------------------
 *
 * @package 	capability-manager-enhanced
 * @author		PublishPress
 * @copyright   Copyright (C) 2009, 2010 Jordi Canals; modifications Copyright (C) 2019 PublishPress
 * @license		GNU General Public License version 3
 * @link		https://publishpress.com
 * @version 	1.8
 */

if ( ! defined( 'CAPSMAN_VERSION' ) ) {
	define( 'CAPSMAN_VERSION', '1.8.1' );
	define( 'CAPSMAN_ENH_VERSION', '1.8.1' );
}

if ( cme_is_plugin_active( 'capsman.php' ) ) {
	function _cme_conflict_notice() {
		$message = __( '<strong>Error:</strong> Capability Manager Extended cannot function because another copy of Capability Manager is active.', 'capsman-enhanced' );
		echo '<div id="message" class="error fade" style="color: black">' . $message . '</div>';
	}
	add_action('admin_notices', _cme_conflict_notice() );
	return;
} else {
	define ( 'CME_FILE', __FILE__ );

	/**
	 * Sets an admin warning regarding required PHP version.
	 *
	 * @hook action 'admin_notices'
	 * @return void
	 */
	function _cman_php_warning() {
		$data = get_plugin_data(__FILE__);
		load_plugin_textdomain('capsman-enhanced', false, basename(dirname(__FILE__)) .'/lang');

		echo '<div class="error"><p><strong>' . __('Warning:', 'capsman-enhanced') . '</strong> '
			. sprintf(__('The active plugin %s is not compatible with your PHP version.', 'capsman-enhanced') .'</p><p>',
				'&laquo;' . $data['Name'] . ' ' . $data['Version'] . '&raquo;')
			. sprintf(__('%s is required for this plugin.', 'capsman-enhanced'), 'PHP-5 ')
			. '</p></div>';
	}

	// ============================================ START PROCEDURE ==========

	// Check required PHP version.
	if ( version_compare(PHP_VERSION, '5.0.0', '<') ) {
		// Send an armin warning
		add_action('admin_notices', '_cman_php_warning');
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
	}
}

add_action( 'init', '_cme_init' );
add_action( 'plugins_loaded', '_cme_act_pp_active', 1 );

add_action( 'init', '_cme_cap_helper', 49 );  // Press Permit Cap Helper, registered at 50, will leave caps which we've already defined
//add_action( 'wp_loaded', '_cme_cap_helper_late_init', 99 );	// now instead adding registered_post_type, registered_taxonomy action handlers for latecomers
																// @todo: do this in PP Core also

function _cme_act_pp_active() {
	if ( defined('PRESSPERMIT_VERSION') || ( defined('PPC_VERSION') && function_exists( 'pp_init_cap_caster' ) ) ) {
		define( 'PRESSPERMIT_ACTIVE', true );
	} else {
		if ( defined('SCOPER_VERSION') || ( defined('PP_VERSION') && function_exists('pp_init_users_interceptor') ) ) {
			define( 'OLD_PRESSPERMIT_ACTIVE', true );
		}
	}
}

function _cme_cap_helper() {
	global $cme_cap_helper;
	
	require_once ( dirname(__FILE__) . '/includes/cap-helper.php' );
	$cme_cap_helper = new CME_Cap_Helper();
	
	add_action( 'registered_post_type', '_cme_post_type_late_reg', 5, 2 );
	add_action( 'registered_taxonomy', '_cme_taxonomy_late_reg', 5, 2 );
}

function _cme_post_type_late_reg( $post_type, $type_obj ) {
	global $cme_cap_helper;
	
	if ( ! empty( $type_obj->public ) || ! empty( $type_obj->show_ui ) ) {
		$cme_cap_helper->refresh();
	}
}

function _cme_taxonomy_late_reg( $taxonomy, $tx_obj ) {
	global $cme_cap_helper;
	
	if ( ! empty( $tx_obj->public ) ) {
		$cme_cap_helper->refresh();
	}
}

function _cme_init() {
	require_once ( dirname(__FILE__) . '/includes/filters.php' );

	load_plugin_textdomain('capsman-enhanced', false, dirname(__FILE__) . '/lang');
}

// perf enchancement: display submenu links without loading framework and plugin code
function cme_submenus() {
	$cap_name = ( is_super_admin() ) ? 'manage_capabilities' : 'restore_roles';
	add_management_page(__('Capability Manager', 'capsman-enhanced'),  __('Capability Manager', 'capsman-enhanced'), $cap_name, 'capsman' . '-tool', 'cme_fakefunc');
	
	if (did_action('pp_admin_menu')) {	// Put Capabilities link on Permissions menu if Press Permit is active and user has access to it
		global $pp_admin;
		$menu_caption = ( defined('WPLANG') && WPLANG && ( 'en_EN' != WPLANG ) ) ? __('Capabilities', 'capsman-enhanced') : 'Role Capabilities';
		add_submenu_page( $pp_admin->get_menu('options'), __('Capability Manager', 'capsman-enhanced'),  $menu_caption, 'manage_capabilities', 'capsman', 'cme_fakefunc' );
	
	} elseif(did_action('presspermit_admin_menu') && function_exists('presspermit')) {
		$menu_caption = ( defined('WPLANG') && WPLANG && ( 'en_EN' != WPLANG ) ) ? __('Capabilities', 'capsman-enhanced') : 'Role Capabilities';
		add_submenu_page( presspermit()->admin()->getMenuParams('options'), __('Capability Manager', 'capsman-enhanced'),  $menu_caption, 'manage_capabilities', 'capsman', 'cme_fakefunc' );
	
	} else {
		add_users_page( __('Capability Manager', 'capsman-enhanced'),  __('Capabilities', 'capsman-enhanced'), 'manage_capabilities', 'capsman', 'cme_fakefunc');	
	}
}

function cme_is_plugin_active($check_plugin_file) {
	if ( ! $check_plugin_file )
		return false;

	$plugins = get_option('active_plugins');

	foreach ( $plugins as $plugin_file ) {
		if ( false !== strpos($plugin_file, $check_plugin_file) )
			return $plugin_file;
	}
}

// if a role is marked as hidden, also default it for use by Press Permit as a Pattern Role (when PP Collaborative Editing is activated and Advanced Settings enabled)
function _cme_pp_default_pattern_role( $role ) {
	if ( ! $pp_role_usage = get_option( 'pp_role_usage' ) )
		$pp_role_usage = array();
		
	if ( empty( $pp_role_usage[$role] ) ) {
		$pp_role_usage[$role] = 'pattern';
		update_option( 'pp_role_usage', $pp_role_usage );
	}
}

function capsman_get_pp_option( $option_basename ) {
	return ( function_exists( 'presspermit_get_option') ) ? presspermit_get_option($option_basename) : pp_get_option($option_basename);
}

if ( is_multisite() )
	require_once ( dirname(__FILE__) . '/includes/network.php' );