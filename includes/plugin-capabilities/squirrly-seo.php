<?php

/**
 * PublishPress Capabilities [Free]
 *
 * Capabilities filters for the plugin Squirrly SEO (Newton).
 *
 * Generated with Capabilities Extractor
 */

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

add_filter('cme_plugin_capabilities', function ($pluginCaps) {
    $pluginCaps['Squirrly SEO Newton'] = [
        'sq_manage_focuspages',
        'sq_manage_settings',
        'sq_manage_snippet',
        'sq_manage_snippets'
    ];

    return $pluginCaps;
});