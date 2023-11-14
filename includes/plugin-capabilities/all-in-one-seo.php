<?php

/**
 * PublishPress Capabilities [Free]
 *
 * Capabilities filters for the plugin All in One SEO.
 *
 * Generated with Capabilities Extractor
 */

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

add_filter('cme_plugin_capabilities', function ($pluginCaps) {
    $pluginCaps['All in One SEO'] = [
        'aioseo_manage_seo',
        'aioseo_page_advanced_settings',
        'aioseo_page_analysis',
        'aioseo_page_general_settings',
        'aioseo_page_schema_settings',
        'aioseo_page_social_settings'
    ];

    return $pluginCaps;
});