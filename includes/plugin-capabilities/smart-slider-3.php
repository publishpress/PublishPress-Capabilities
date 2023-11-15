<?php

/**
 * PublishPress Capabilities [Free]
 *
 * Capabilities filters for the plugin Smart Slider 3.
 *
 * Generated with Capabilities Extractor
 */

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

add_filter('cme_plugin_capabilities', function ($pluginCaps) {
    $pluginCaps['Smart Slider 3'] = [
        'smartslider',
        'smartslider_config',
        'smartslider_delete',
        'smartslider_edit'
    ];

    return $pluginCaps;
});