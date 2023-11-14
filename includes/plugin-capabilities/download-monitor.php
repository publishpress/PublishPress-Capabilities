<?php

/**
 * PublishPress Capabilities [Free]
 *
 * Capabilities filters for the plugin Download Monitor.
 *
 * Generated with Capabilities Extractor
 */

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

add_filter('cme_plugin_capabilities', function ($pluginCaps) {
    $pluginCaps['Download Monitor'] = [
        'dlm_manage_logs',
        'dlm_view_reports',
        'manage_downloads'
    ];

    return $pluginCaps;
});