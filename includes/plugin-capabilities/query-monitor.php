<?php

/**
 * PublishPress Capabilities [Free]
 *
 * Capabilities filters for the plugin Query Monitor.
 *
 * Generated with Capabilities Extractor
 */

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

add_filter('cme_plugin_capabilities', function ($pluginCaps) {
    $pluginCaps['Query Monitor'] = [
        'view_query_monitor'
    ];

    return $pluginCaps;
});