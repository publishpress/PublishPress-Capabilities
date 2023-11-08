<?php

/**
 * PublishPress Capabilities [Free]
 *
 * Capabilities filters for the plugin Yoast SEO.
 *
 * Generated with Capabilities Extractor
 */

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

add_filter('cme_plugin_capabilities', function ($pluginCaps) {
    $pluginCaps['Yoast SEO'] = [
        'view_site_health_checks',
        'wpseo_bulk_edit',
        'wpseo_edit_advanced_metadata',
        'wpseo_manage_options'
    ];

    return $pluginCaps;
});