<?php

/**
 * PublishPress Capabilities [Free]
 *
 * Capabilities filters for the plugin Yoast Duplicate Post.
 *
 * Generated with Capabilities Extractor
 */

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

add_filter('cme_plugin_capabilities', function ($pluginCaps) {
    $pluginCaps['Yoast Duplicate Post'] = [
        'copy_posts'
    ];

    return $pluginCaps;
});