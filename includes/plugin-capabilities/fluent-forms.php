<?php

/**
 * PublishPress Capabilities [Free]
 *
 * Capabilities filters for the plugin Fluent Forms.
 *
 * Generated with Capabilities Extractor
 */

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

add_filter('cme_plugin_capabilities', function ($pluginCaps) {
    $pluginCaps['Fluent Forms'] = [
        'fluentform_dashboard_access',
        'fluentform_entries_viewer',
        'fluentform_forms_manager',
        'fluentform_full_access',
        'fluentform_manage_entries',
        'fluentform_manage_payments',
        'fluentform_settings_manager',
        'fluentform_view_payments'
    ];

    return $pluginCaps;
});