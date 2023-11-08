<?php

/**
 * PublishPress Capabilities [Free]
 *
 * Capabilities filters for the plugin Site Kit by Google.
 *
 * Generated with Capabilities Extractor
 */

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

add_filter('cme_plugin_capabilities', function ($pluginCaps) {
    $pluginCaps['Site Kit by Google'] = [
        'googlesitekit_authenticate',
        'googlesitekit_manage_options',
        'googlesitekit_setup',
        'googlesitekit_view_dashboard',
        'googlesitekit_view_wp_dashboard_widget'
    ];

    return $pluginCaps;
});