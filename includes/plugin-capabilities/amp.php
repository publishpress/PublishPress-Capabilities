<?php

/**
 * PublishPress Capabilities [Free]
 *
 * Capabilities filters for the plugin AMP.
 *
 * Generated with Capabilities Extractor
 */

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

add_filter('cme_plugin_capabilities', function ($pluginCaps) {
    $pluginCaps['AMP'] = [
        'amp_validate'
    ];

    return $pluginCaps;
});