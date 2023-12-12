<?php

/**
 * PublishPress Capabilities [Free]
 *
 * Capabilities filters for the plugin SEOPress.
 *
 * Generated with Capabilities Extractor
 */

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

add_filter('cme_plugin_capabilities', function ($pluginCaps) {
    $pluginCaps['SEOPress'] = [
        'seopress_manage_advanced',
        'seopress_manage_analytics',
        'seopress_manage_bot',
        'seopress_manage_dashboard',
        'seopress_manage_instant_indexing',
        'seopress_manage_license',
        'seopress_manage_pro',
        'seopress_manage_schemas',
        'seopress_manage_social_networks',
        'seopress_manage_titles_metas',
        'seopress_manage_tools',
        'seopress_manage_xml_html_sitemap'
    ];

    return $pluginCaps;
});