<?php

/**
 * PublishPress Capabilities [Free]
 *
 * Capabilities filters for the plugin Strong Testimonials.
 *
 * Generated with Capabilities Extractor
 */

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

add_filter('cme_plugin_capabilities', function ($pluginCaps) {
    $pluginCaps['Strong Testimonials'] = [
        'strong_testimonials_about',
        'strong_testimonials_fields',
        'strong_testimonials_options',
        'strong_testimonials_views'
    ];

    return $pluginCaps;
});