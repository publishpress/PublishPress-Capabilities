<?php

/**
 * PublishPress Capabilities [Free]
 *
 * Capabilities filters for the plugin Formidable Forms.
 *
 * Generated with Capabilities Extractor
 */

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

add_filter('cme_plugin_capabilities', function ($pluginCaps) {
    $pluginCaps['Formidable Forms'] = [
        'frm_change_settings',
        'frm_delete_entries',
        'frm_delete_forms',
        'frm_edit_forms',
        'frm_view_entries',
        'frm_view_forms'
    ];

    return $pluginCaps;
});