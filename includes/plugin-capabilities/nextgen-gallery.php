<?php

/**
 * PublishPress Capabilities [Free]
 *
 * Capabilities filters for the plugin NextGEN Gallery.
 *
 * Generated with Capabilities Extractor
 */

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

add_filter('cme_plugin_capabilities', function ($pluginCaps) {
    $pluginCaps['NextGEN Gallery'] = [
        'NextGEN Attach Interface',
        'NextGEN Change options',
        'NextGEN Change style',
        'NextGEN Edit album',
        'NextGEN Gallery overview',
        'NextGEN Manage gallery',
        'NextGEN Manage others gallery',
        'NextGEN Manage tags',
        'NextGEN Upload images',
        'NextGEN Use TinyMCE'
    ];

    return $pluginCaps;
});