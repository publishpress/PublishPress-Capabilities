<?php

/**
 * PublishPress Capabilities [Free]
 *
 * Capabilities filters for the plugin BackWPup.
 *
 * Generated with Capabilities Extractor
 */

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

add_filter('cme_plugin_capabilities', function ($pluginCaps) {
    $pluginCaps['BackWPup'] = [
        'backwpup',
        'backwpup_backups',
        'backwpup_backups_delete',
        'backwpup_backups_download',
        'backwpup_jobs',
        'backwpup_jobs_edit',
        'backwpup_jobs_start',
        'backwpup_logs',
        'backwpup_logs_delete',
        'backwpup_restore',
        'backwpup_settings'
    ];

    return $pluginCaps;
});