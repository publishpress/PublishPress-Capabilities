<?php

/**
 * PublishPress Capabilities [Free]
 *
 * Capabilities filters for the plugin Gravity Forms.
 *
 * Generated with Capabilities Extractor
 */

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

add_filter('cme_plugin_capabilities', function ($pluginCaps) {
    $pluginCaps['Gravity Forms'] = [
        'gform_full_access',
        'gravityforms_api_settings',
        'gravityforms_create_form',
        'gravityforms_delete_entries',
        'gravityforms_delete_forms',
        'gravityforms_edit_entries',
        'gravityforms_edit_entry_notes',
        'gravityforms_edit_forms',
        'gravityforms_edit_settings',
        'gravityforms_export_entries',
        'gravityforms_logging',
        'gravityforms_preview_forms',
        'gravityforms_system_status',
        'gravityforms_uninstall',
        'gravityforms_view_addons',
        'gravityforms_view_entries',
        'gravityforms_view_entry_notes',
        'gravityforms_view_settings',
        'gravityforms_view_updates'
    ];

    return $pluginCaps;
});