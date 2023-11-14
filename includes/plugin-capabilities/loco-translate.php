<?php

/**
 * PublishPress Capabilities [Free]
 *
 * Capabilities filters for the plugin Loco Translate.
 *
 * Generated with Capabilities Extractor
 */

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

add_filter('cme_plugin_capabilities', function ($pluginCaps) {
    $pluginCaps['Loco Translate'] = [
        'loco_admin'
    ];

    return $pluginCaps;
});