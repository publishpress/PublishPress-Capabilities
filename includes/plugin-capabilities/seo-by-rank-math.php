<?php

/**
 * PublishPress Capabilities [Free]
 *
 * Capabilities filters for the plugin Rank Math SEO.
 *
 * Generated with Capabilities Extractor
 */

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

add_filter('cme_plugin_capabilities', function ($pluginCaps) {
    $pluginCaps['Rank Math SEO'] = [
        'rank_math_404_monitor',
        'rank_math_admin_bar',
        'rank_math_analytics',
        'rank_math_content_ai',
        'rank_math_edit_htaccess',
        'rank_math_general',
        'rank_math_link_builder',
        'rank_math_onpage_advanced',
        'rank_math_onpage_analysis',
        'rank_math_onpage_general',
        'rank_math_onpage_snippet',
        'rank_math_onpage_social',
        'rank_math_redirections',
        'rank_math_role_manager',
        'rank_math_site_analysis',
        'rank_math_sitemap',
        'rank_math_titles'
    ];

    return $pluginCaps;
});