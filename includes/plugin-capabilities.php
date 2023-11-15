<?php

namespace PublishPress\Capabilities;

class Plugin_Capabilities
{
    private static $instance = null;

    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new Plugin_Capabilities();
        }

        return self::$instance;
    }

    public function __construct()
    {
        //PublishPress Capabilities
        add_filter('cme_plugin_capabilities', [$this, 'cme_publishpress_capabilities_capabilities']);
        //PublishPress Authors
        add_filter('cme_plugin_capabilities', [$this, 'cme_multiple_authors_capabilities']);
        //PublishPress Permissions
        add_filter('cme_plugin_capabilities', [$this, 'cme_presspermit_capabilities']);
        //Gravity Forms
        add_filter('cme_plugin_capabilities', [$this, 'cme_gravityforms_capabilities']);
        //WPML
        add_filter('cme_plugin_capabilities', [$this, 'cme_wpml_capabilities']);
        //WS Form
        add_filter('cme_plugin_capabilities', [$this, 'cme_wsform_capabilities']);
        //TaxoPress
        add_filter('cme_plugin_capabilities', [$this, 'cme_taxopress_capabilities']);
        //WooCommerce`
        add_filter('cme_plugin_capabilities', [$this, 'cme_woocommerce_capabilities']);
        //Echo Knowledge Base
        add_filter('cme_plugin_capabilities', [$this, 'cme_echo_knowledge_base_capabilities']);
        // Yoast SEO
        add_filter('cme_plugin_capabilities', [$this, 'cme_yoast_seo_capabilities']);
    }

    /**
     * PublishPress Capabilities
     *
     * @param array $plugin_caps
     *
     * @return array
     */
    public function cme_publishpress_capabilities_capabilities($plugin_caps)
    {

        $plugin_caps['PublishPress Capabilities'] = apply_filters('cme_publishpress_capabilities_capabilities', []);

        return $plugin_caps;
    }

    /**
     * PublishPress Authors
     *
     * @param array $plugin_caps
     *
     * @return array
     */
    public function cme_multiple_authors_capabilities($plugin_caps)
    {

        if (defined('PUBLISHPRESS_MULTIPLE_AUTHORS_VERSION')) {
            if ($_caps = apply_filters('cme_multiple_authors_capabilities', [])) {
                $plugin_caps['PublishPress Authors'] = $_caps;
            }
        }

        return $plugin_caps;
    }

    /**
     * PublishPress Permissions
     *
     * @param array $plugin_caps
     *
     * @return array
     */
    public function cme_presspermit_capabilities($plugin_caps)
    {

        if (defined('PRESSPERMIT_VERSION')) {
            $plugin_caps['PublishPress Permissions'] = apply_filters(
                'cme_presspermit_capabilities',
                [
                    'edit_own_attachments',
                    'list_others_unattached_files',
                    'pp_administer_content',
                    'pp_assign_roles',
                    'pp_associate_any_page',
                    'pp_create_groups',
                    'pp_create_network_groups',
                    'pp_define_moderation',
                    'pp_define_post_status',
                    'pp_define_privacy',
                    'pp_delete_groups',
                    'pp_edit_groups',
                    'pp_exempt_edit_circle',
                    'pp_exempt_read_circle',
                    'pp_force_quick_edit',
                    'pp_list_all_files',
                    'pp_manage_capabilities',
                    'pp_manage_members',
                    'pp_manage_network_members',
                    'pp_manage_settings',
                    'pp_moderate_any',
                    'pp_set_associate_exceptions',
                    'pp_set_edit_exceptions',
                    'pp_set_read_exceptions',
                    'pp_set_revise_exceptions',
                    'pp_set_term_assign_exceptions',
                    'pp_set_term_associate_exceptions',
                    'pp_set_term_manage_exceptions',
                    'pp_unfiltered',
                    'set_posts_status',
                ]
            );
        }

        return $plugin_caps;
    }

    /**
     * Gravity Forms
     *
     * @param array $plugin_caps
     *
     * @return array
     */
    public function cme_gravityforms_capabilities($plugin_caps)
    {
        if (defined('GF_PLUGIN_DIR_PATH')) {
            $plugin_caps['Gravity Forms'] = apply_filters(
                'cme_gravityforms_capabilities',
                [
                    'gravityforms_create_form',
                    'gravityforms_delete_forms',
                    'gravityforms_edit_forms',
                    'gravityforms_preview_forms',
                    'gravityforms_view_entries',
                    'gravityforms_edit_entries',
                    'gravityforms_delete_entries',
                    'gravityforms_view_entry_notes',
                    'gravityforms_edit_entry_notes',
                    'gravityforms_export_entries',
                    'gravityforms_view_settings',
                    'gravityforms_edit_settings',
                    'gravityforms_view_updates',
                    'gravityforms_view_addons',
                    'gravityforms_system_status',
                    'gravityforms_uninstall',
                    'gravityforms_logging',
                    'gravityforms_api_settings',
                ]
            );
        }

        return $plugin_caps;
    }

    /**
     * WPML
     *
     * @param array $plugin_caps
     *
     * @return array
     */
    public function cme_wpml_capabilities($plugin_caps)
    {

        if (defined('WPML_PLUGIN_FILE')) {
            $plugin_caps['WPML'] = apply_filters(
                'cme_wpml_capabilities',
                [
                    'wpml_manage_translation_management',
                    'wpml_manage_languages',
                    'wpml_manage_translation_options',
                    'wpml_manage_troubleshooting',
                    'wpml_manage_taxonomy_translation',
                    'wpml_manage_wp_menus_sync',
                    'wpml_manage_translation_analytics',
                    'wpml_manage_string_translation',
                    'wpml_manage_sticky_links',
                    'wpml_manage_navigation',
                    'wpml_manage_theme_and_plugin_localization',
                    'wpml_manage_media_translation',
                    'wpml_manage_support',
                    'wpml_manage_woocommerce_multilingual',
                    'wpml_operate_woocommerce_multilingual',
                ]
            );
        }

        return $plugin_caps;
    }

    /**
     * WS Form
     *
     * @param array $plugin_caps
     *
     * @return array
     */
    public function cme_wsform_capabilities($plugin_caps)
    {

        if (defined('WS_FORM_VERSION')) {
            $plugin_caps['WS Form'] = apply_filters(
                'cme_wsform_capabilities',
                [
                    'create_form',
                    'delete_form',
                    'edit_form',
                    'export_form',
                    'import_form',
                    'publish_form',
                    'read_form',
                    'delete_submission',
                    'edit_submission',
                    'export_submission',
                    'read_submission',
                    'manage_options_wsform',
                ]
            );
        }

        return $plugin_caps;
    }

    /**
     * TaxoPress
     *
     * @param array $plugin_caps
     *
     * @return array
     */
    public function cme_taxopress_capabilities($plugin_caps)
    {

        if (defined('STAGS_VERSION')) {
            $plugin_caps['TaxoPress'] = apply_filters(
                'cme_taxopress_capabilities',
                [
                    'simple_tags',
                    'admin_simple_tags'
                ]
            );
        }

        return $plugin_caps;
    }

    /**
     * Echo Knowledge Base
     *
     * @param array $plugin_caps
     *
     * @return array
     */
    public function cme_echo_knowledge_base_capabilities($plugin_caps)
    {

        if (defined('EPKB_PLUGIN_NAME')) {
            $plugin_caps['Echo Knowledge Base'] = apply_filters(
                'cme_echo_knowledge_base_capabilities',
                [
                    'admin_eckb_access_manager_page',
                    'admin_eckb_access_crud_users',
                    'admin_eckb_access_frontend_editor_write',
                    'admin_eckb_access_search_analytics_read',
                    'admin_eckb_access_order_articles_write',
                    'admin_eckb_access_need_help_read',
                    'admin_eckb_access_addons_news_read'
                ]
            );
        }

        return $plugin_caps;
    }

    /**
     * Yoast SEO
     *
     * @param array $plugin_caps
     *
     * @return array
     */
    public function cme_yoast_seo_capabilities($plugin_caps) {
        if (defined('WPSEO_FILE')) {
            $plugin_caps['Yoast SEO'] = apply_filters('cme_yoast_seo_capabilities',
                [
                        'view_site_health_checks',
                        'wpseo_bulk_edit',
                        'wpseo_edit_advanced_metadata',
                        'wpseo_manage_options'

                ]
            );
        }

        return $plugin_caps;
    }

    /**
     * WooCommerce
     *
     * @param array $plugin_caps
     *
     * @return array
     */
    public function cme_woocommerce_capabilities($plugin_caps)
    {

        if (defined('WC_PLUGIN_FILE')) {
            $plugin_caps['WooCommerce'] = apply_filters(
                'cme_woocommerce_capabilities',
                [
                    'assign_product_terms',
                    'assign_shop_coupon_terms',
                    'assign_shop_discount_terms',
                    'assign_shop_order_terms',
                    'assign_shop_payment_terms',
                    'create_shop_orders',
                    'delete_others_products',
                    'delete_others_shop_coupons',
                    'delete_others_shop_discounts',
                    'delete_others_shop_orders',
                    'delete_others_shop_payments',
                    'delete_private_products',
                    'delete_private_shop_coupons',
                    'delete_private_shop_orders',
                    'delete_private_shop_discounts',
                    'delete_private_shop_payments',
                    'delete_product_terms',
                    'delete_products',
                    'delete_published_products',
                    'delete_published_shop_coupons',
                    'delete_published_shop_discounts',
                    'delete_published_shop_orders',
                    'delete_published_shop_payments',
                    'delete_shop_coupons',
                    'delete_shop_coupon_terms',
                    'delete_shop_discount_terms',
                    'delete_shop_discounts',
                    'delete_shop_order_terms',
                    'delete_shop_orders',
                    'delete_shop_payments',
                    'delete_shop_payment_terms',
                    'edit_others_products',
                    'edit_others_shop_coupons',
                    'edit_others_shop_discounts',
                    'edit_others_shop_orders',
                    'edit_others_shop_payments',
                    'edit_private_products',
                    'edit_private_shop_coupons',
                    'edit_private_shop_discounts',
                    'edit_private_shop_orders',
                    'edit_private_shop_payments',
                    'edit_product_terms',
                    'edit_products',
                    'edit_published_products',
                    'edit_published_shop_coupons',
                    'edit_published_shop_discounts',
                    'edit_published_shop_orders',
                    'edit_published_shop_payments',
                    'edit_shop_coupon_terms',
                    'edit_shop_coupons',
                    'edit_shop_discounts',
                    'edit_shop_discount_terms',
                    'edit_shop_order_terms',
                    'edit_shop_orders',
                    'edit_shop_payments',
                    'edit_shop_payment_terms',
                    'export_shop_payments',
                    'export_shop_reports',
                    'import_shop_discounts',
                    'import_shop_payments',
                    'manage_product_terms',
                    'manage_shop_coupon_terms',
                    'manage_shop_discounts',
                    'manage_shop_discount_terms',
                    'manage_shop_payment_terms',
                    'manage_shop_order_terms',
                    'manage_shop_settings',
                    'manage_woocommerce',
                    'publish_products',
                    'publish_shop_coupons',
                    'publish_shop_discounts',
                    'publish_shop_orders',
                    'publish_shop_payments',
                    'read_private_products',
                    'read_private_shop_coupons',
                    'read_private_shop_discounts',
                    'read_private_shop_payments',
                    'read_private_shop_orders',
                    'view_admin_dashboard',
                    'view_shop_discount_stats',
                    'view_shop_payment_stats',
                    'view_shop_reports',
                    'view_shop_sensitive_data',
                    'view_woocommerce_reports',
                ]
            );
        }

        return $plugin_caps;
    }
}
