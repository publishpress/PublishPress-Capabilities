<?php

/**
 * PublishPress Capabilities [Free]
 *
 * Capabilities filters for the plugin Sunshine Photo Cart.
 *
 * Generated with Capabilities Extractor
 */

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

add_filter('cme_plugin_capabilities', function ($pluginCaps) {
    $pluginCaps['Sunshine Photo Cart'] = [
        'delete_others_sunshine_galleries',
        'delete_others_sunshine_orders',
        'delete_others_sunshine_products',
        'delete_private_sunshine_galleries',
        'delete_private_sunshine_orders',
        'delete_private_sunshine_products',
        'delete_published_sunshine_galleries',
        'delete_published_sunshine_orders',
        'delete_published_sunshine_products',
        'delete_sunshine_galleries',
        'delete_sunshine_gallery',
        'delete_sunshine_order',
        'delete_sunshine_orders',
        'delete_sunshine_product',
        'delete_sunshine_products',
        'edit_others_sunshine_galleries',
        'edit_others_sunshine_orders',
        'edit_others_sunshine_products',
        'edit_private_sunshine_galleries',
        'edit_private_sunshine_orders',
        'edit_private_sunshine_products',
        'edit_published_sunshine_galleries',
        'edit_published_sunshine_orders',
        'edit_published_sunshine_products',
        'edit_sunshine_galleries',
        'edit_sunshine_gallery',
        'edit_sunshine_order',
        'edit_sunshine_orders',
        'edit_sunshine_product',
        'edit_sunshine_products',
        'publish_sunshine_galleries',
        'publish_sunshine_gallery',
        'publish_sunshine_product',
        'publish_sunshine_products',
        'read_private_sunshine_galleries',
        'read_private_sunshine_orders',
        'read_private_sunshine_products',
        'read_sunshine_gallery',
        'read_sunshine_order',
        'read_sunshine_product',
        'sunshine_manage_options'
    ];

    return $pluginCaps;
});