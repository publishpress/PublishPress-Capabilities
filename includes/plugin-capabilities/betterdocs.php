<?php

/**
 * PublishPress Capabilities [Free]
 *
 * Capabilities filters for the plugin BetterDocs.
 *
 * Generated with Capabilities Extractor
 */

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

add_filter('cme_plugin_capabilities', function ($pluginCaps) {
    $pluginCaps['BetterDocs'] = [
        'delete_doc_terms',
        'delete_docs',
        'delete_knowledge_base_terms',
        'delete_others_docs',
        'delete_private_docs',
        'delete_published_docs',
        'edit_doc_terms',
        'edit_docs',
        'edit_docs_settings',
        'edit_knowledge_base_terms',
        'edit_others_docs',
        'edit_private_docs',
        'edit_published_docs',
        'manage_doc_terms',
        'manage_knowledge_base_terms',
        'publish_docs',
        'read_docs_analytics',
        'read_private_docs'
    ];

    return $pluginCaps;
});