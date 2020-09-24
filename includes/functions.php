<?php
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
	
	require_once ( dirname(__FILE__) . '/cap-helper.php' );
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
	require_once ( dirname(__FILE__) . '/filters.php' );

	load_plugin_textdomain('capsman-enhanced', false, dirname(plugin_basename(__FILE__)) . '/lang');
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

// deprecated
function capsman_get_pp_option( $option_basename ) {
	return pp_capabilities_get_permissions_option($option_basename);
}

function pp_capabilities_autobackup() {
	global $wpdb;

	$roles = get_option($wpdb->prefix . 'user_roles');
	update_option('cme_backup_auto_' . current_time('Y-m-d_g-i-s_a'), $roles, false);

	$max_auto_backups = (defined('CME_AUTOBACKUPS')) ? CME_AUTOBACKUPS : 20;

	$keep_ids = $wpdb->get_col("SELECT option_id FROM $wpdb->options WHERE option_name LIKE 'cme_backup_auto_%' ORDER BY option_id DESC LIMIT $max_auto_backups");

	if (count($keep_ids) == $max_auto_backups) {
		$id_csv = implode("','", $keep_ids);

		$wpdb->query(
			"DELETE FROM $wpdb->options WHERE option_name LIKE 'cme_backup_auto_%' AND option_id NOT IN ('$id_csv')"
		);
	}
}

function pp_capabilities_get_permissions_option($option_basename) {
	return (function_exists('presspermit')) ? presspermit()->getOption($option_basename) : pp_get_option($option_basename);
}

function pp_capabilities_update_permissions_option($option_basename, $option_val) {
	function_exists('presspermit') ? presspermit()->updateOption($option_basename, $option_val) : pp_update_option($option_basename, $option_val);
}
