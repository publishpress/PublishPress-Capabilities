<?php

/**
 * PublishPress Capabilities [Free]
 *
 * Capabilities filters for the plugin All in One SEO.
 *
 * Generated with Capabilities Extractor
 */

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

add_filter('cme_plugin_capabilities', function ($pluginCaps) {
    $pluginCaps['All in One SEO'] = [
        'aioseo_manage_seo',
        'aioseo_page_advanced_settings',
        'aioseo_page_analysis',
        'aioseo_page_general_settings',
        'aioseo_page_schema_settings',
        'aioseo_page_social_settings'
    ];

    return $pluginCaps;
});<?php

/**
 * PublishPress Capabilities [Free]
 *
 * Capabilities filters for the plugin BackWPup.
 *
 * Generated with Capabilities Extractor
 */

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

add_filter('cme_plugin_capabilities', function ($pluginCaps) {
    $pluginCaps['BackWPup'] = [
        'backwpup',
        'backwpup_backups',
        'backwpup_backups_delete',
        'backwpup_backups_download',
        'backwpup_jobs',
        'backwpup_jobs_edit',
        'backwpup_jobs_start',
        'backwpup_logs',
        'backwpup_logs_delete',
        'backwpup_restore',
        'backwpup_settings'
    ];

    return $pluginCaps;
});<?php

/**
 * PublishPress Capabilities [Free]
 *
 * Capabilities filters for the plugin bbPress.
 *
 * Generated with Capabilities Extractor
 */

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

add_filter('cme_plugin_capabilities', function ($pluginCaps) {
    $pluginCaps['bbPress'] = [
        'edit_forums',
        'edit_replies',
        'edit_topics',
        'keep_gate',
        'moderate',
        'read_private_replies',
        'read_private_topics',
        'spectate',
        'view_trash'
    ];

    return $pluginCaps;
});<?php

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
});<?php

/**
 * PublishPress Capabilities [Free]
 *
 * Capabilities filters for the plugin BuddyPress.
 *
 * Generated with Capabilities Extractor
 */

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

add_filter('cme_plugin_capabilities', function ($pluginCaps) {
    $pluginCaps['BuddyPress'] = [
        'bp_members_invitations_view_screens',
        'bp_moderate'
    ];

    return $pluginCaps;
});<?php

/**
 * PublishPress Capabilities [Free]
 *
 * Capabilities filters for the plugin Download Monitor.
 *
 * Generated with Capabilities Extractor
 */

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

add_filter('cme_plugin_capabilities', function ($pluginCaps) {
    $pluginCaps['Download Monitor'] = [
        'dlm_manage_logs',
        'dlm_view_reports',
        'manage_downloads'
    ];

    return $pluginCaps;
});<?php

/**
 * PublishPress Capabilities [Free]
 *
 * Capabilities filters for the plugin Fluent Forms.
 *
 * Generated with Capabilities Extractor
 */

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

add_filter('cme_plugin_capabilities', function ($pluginCaps) {
    $pluginCaps['Fluent Forms'] = [
        'fluentform_dashboard_access',
        'fluentform_entries_viewer',
        'fluentform_forms_manager',
        'fluentform_full_access',
        'fluentform_manage_entries',
        'fluentform_manage_payments',
        'fluentform_settings_manager',
        'fluentform_view_payments'
    ];

    return $pluginCaps;
});<?php

/**
 * PublishPress Capabilities [Free]
 *
 * Capabilities filters for the plugin Fluent Forms.
 */

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

add_filter('cme_plugin_capabilities', function ($pluginCaps) {
    $pluginCaps['Fluent Forms'] = [
        'fluentform_dashboard_access',
        'fluentform_entries_viewer',
        'fluentform_forms_manager',
        'fluentform_full_access',
        'fluentform_manage_entries',
        'fluentform_manage_payments',
        'fluentform_settings_manager',
        'fluentform_view_payments',
    ];

    return $pluginCaps;
});
<?php

/**
 * PublishPress Capabilities [Free]
 *
 * Capabilities filters for the plugin Formidable Forms.
 *
 * Generated with Capabilities Extractor
 */

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

add_filter('cme_plugin_capabilities', function ($pluginCaps) {
    $pluginCaps['Formidable Forms'] = [
        'frm_change_settings',
        'frm_delete_entries',
        'frm_delete_forms',
        'frm_edit_forms',
        'frm_view_entries',
        'frm_view_forms'
    ];

    return $pluginCaps;
});<?php

/**
 * PublishPress Capabilities [Free]
 *
 * Capabilities filters for the plugin Forminator.
 *
 * Generated with Capabilities Extractor
 */

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

add_filter('cme_plugin_capabilities', function ($pluginCaps) {
    $pluginCaps['Forminator'] = [
        'manage_forminator'
    ];

    return $pluginCaps;
});<?php

/**
 * PublishPress Capabilities [Free]
 *
 * Capabilities filters for the plugin Give - Donation Plugin.
 *
 * Generated with Capabilities Extractor
 */

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

add_filter('cme_plugin_capabilities', function ($pluginCaps) {
    $pluginCaps['Give Donation Plugin'] = [
        'assign_give_form_terms',
        'assign_give_payment_terms',
        'delete_give_form_terms',
        'delete_give_forms',
        'delete_give_payment_terms',
        'delete_give_payments',
        'delete_others_give_forms',
        'delete_others_give_payments',
        'delete_private_give_forms',
        'delete_private_give_payments',
        'delete_published_give_forms',
        'delete_published_give_payments',
        'edit_give_form_terms',
        'edit_give_forms',
        'edit_give_payment_terms',
        'edit_give_payments',
        'edit_others_give_forms',
        'edit_others_give_payments',
        'edit_private_give_forms',
        'edit_private_give_payments',
        'edit_published_give_forms',
        'edit_published_give_payments',
        'export_give_reports',
        'import_give_forms',
        'import_give_payments',
        'manage_give_form_terms',
        'manage_give_payment_terms',
        'manage_give_settings',
        'publish_give_forms',
        'publish_give_payments',
        'read_private_give_forms',
        'read_private_give_payments',
        'view_give_form_stats',
        'view_give_payment_stats',
        'view_give_payments',
        'view_give_reports',
        'view_give_sensitive_data'
    ];

    return $pluginCaps;
});<?php

/**
 * PublishPress Capabilities [Free]
 *
 * Capabilities filters for the plugin Gravity Forms.
 *
 * Generated with Capabilities Extractor
 */

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

add_filter('cme_plugin_capabilities', function ($pluginCaps) {
    $pluginCaps['Gravity Forms'] = [
        'gform_full_access',
        'gravityforms_api_settings',
        'gravityforms_create_form',
        'gravityforms_delete_entries',
        'gravityforms_delete_forms',
        'gravityforms_edit_entries',
        'gravityforms_edit_entry_notes',
        'gravityforms_edit_forms',
        'gravityforms_edit_settings',
        'gravityforms_export_entries',
        'gravityforms_logging',
        'gravityforms_preview_forms',
        'gravityforms_system_status',
        'gravityforms_uninstall',
        'gravityforms_view_addons',
        'gravityforms_view_entries',
        'gravityforms_view_entry_notes',
        'gravityforms_view_settings',
        'gravityforms_view_updates'
    ];

    return $pluginCaps;
});<?php

/**
 * PublishPress Capabilities [Free]
 *
 * Capabilities filters for the plugin GravityView.
 *
 * Generated with Capabilities Extractor
 */

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

add_filter('cme_plugin_capabilities', function ($pluginCaps) {
    $pluginCaps['GravityView'] = [
        'edit_gravityviews',
        'gravityview_edit_entries',
        'gravityview_full_access',
        'gravityview_getting_started'
    ];

    return $pluginCaps;
});<?php

/**
 * PublishPress Capabilities [Free]
 *
 * Capabilities filters for the plugin LearnDash LMS.
 *
 * Generated with Capabilities Extractor
 */

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

add_filter('cme_plugin_capabilities', function ($pluginCaps) {
    $pluginCaps['LearnDash LMS'] = [
        'delete_assignment',
        'delete_course',
        'delete_courses',
        'delete_essays',
        'delete_group',
        'delete_groups',
        'delete_others_assignments',
        'delete_others_courses',
        'delete_others_essays',
        'delete_others_groups',
        'delete_private_courses',
        'delete_published_assignments',
        'delete_published_courses',
        'delete_published_essays',
        'delete_published_groups',
        'edit_assignment',
        'edit_assignments',
        'edit_courses',
        'edit_essays',
        'edit_groups',
        'edit_others_assignments',
        'edit_others_courses',
        'edit_others_essays',
        'edit_others_groups',
        'edit_private_courses',
        'edit_published_assignments',
        'edit_published_courses',
        'edit_published_essays',
        'edit_published_groups',
        'enroll_users',
        'publish_assignments',
        'publish_courses',
        'publish_essays',
        'publish_groups',
        'read_assignment',
        'read_course',
        'read_essays',
        'read_group',
        'read_private_assignments',
        'read_private_courses',
        'read_private_essays',
        'read_private_groups',
        'wpProQuiz_add_quiz',
        'wpProQuiz_change_settings',
        'wpProQuiz_delete_quiz',
        'wpProQuiz_edit_quiz',
        'wpProQuiz_export',
        'wpProQuiz_import',
        'wpProQuiz_reset_statistics',
        'wpProQuiz_show',
        'wpProQuiz_show_statistics',
        'wpProQuiz_toplist_edit'
    ];

    return $pluginCaps;
});<?php

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
});<?php

/**
 * PublishPress Capabilities [Free]
 *
 * Capabilities filters for the plugin MailPoet.
 *
 * Generated with Capabilities Extractor
 */

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

add_filter('cme_plugin_capabilities', function ($pluginCaps) {
    $pluginCaps['MailPoet'] = [
        'mailpoet_access_plugin_admin',
        'mailpoet_manage_automations',
        'mailpoet_manage_emails',
        'mailpoet_manage_features',
        'mailpoet_manage_forms',
        'mailpoet_manage_segments',
        'mailpoet_manage_settings',
        'mailpoet_manage_subscribers'
    ];

    return $pluginCaps;
});<?php

/**
 * PublishPress Capabilities [Free]
 *
 * Capabilities filters for the plugin NextGEN Gallery.
 *
 * Generated with Capabilities Extractor
 */

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

add_filter('cme_plugin_capabilities', function ($pluginCaps) {
    $pluginCaps['NextGEN Gallery'] = [
        'NextGEN Attach Interface',
        'NextGEN Change options',
        'NextGEN Change style',
        'NextGEN Edit album',
        'NextGEN Gallery overview',
        'NextGEN Manage gallery',
        'NextGEN Manage others gallery',
        'NextGEN Manage tags',
        'NextGEN Upload images',
        'NextGEN Use TinyMCE'
    ];

    return $pluginCaps;
});<?php

/**
 * PublishPress Capabilities [Free]
 *
 * Capabilities filters for the plugin PublishPress Planner.
 */

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

add_filter('cme_plugin_capabilities', function ($pluginCaps) {
    $caps = [
        'edit_metadata',
        'edit_post_subscriptions',
        'pp_manage_roles',
        'pp_set_notification_channel',
        'pp_view_calendar',
        'pp_view_content_overview',
    ];

    /**
     * @deprecated 2.11.0
     */
    $caps = apply_filters_deprecated('cme_publishpress_capabilities', [$caps], '2.11.0', 'cme_plugin_capabilities');

    $pluginCaps['PublishPress Planner'] = $caps;

    return $pluginCaps;
});
<?php

/**
 * PublishPress Capabilities [Free]
 *
 * Capabilities filters for the plugin Query Monitor.
 *
 * Generated with Capabilities Extractor
 */

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

add_filter('cme_plugin_capabilities', function ($pluginCaps) {
    $pluginCaps['Query Monitor'] = [
        'view_query_monitor'
    ];

    return $pluginCaps;
});<?php

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
});<?php

/**
 * PublishPress Capabilities [Free]
 *
 * Capabilities filters for the plugin Site Kit by Google.
 *
 * Generated with Capabilities Extractor
 */

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

add_filter('cme_plugin_capabilities', function ($pluginCaps) {
    $pluginCaps['Site Kit by Google'] = [
        'googlesitekit_authenticate',
        'googlesitekit_manage_options',
        'googlesitekit_setup',
        'googlesitekit_view_dashboard',
        'googlesitekit_view_wp_dashboard_widget'
    ];

    return $pluginCaps;
});<?php

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
});<?php

/**
 * PublishPress Capabilities [Free]
 *
 * Capabilities filters for the plugin Smash Balloon Instagram Feed.
 *
 * Generated with Capabilities Extractor
 */

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

add_filter('cme_plugin_capabilities', function ($pluginCaps) {
    $pluginCaps['Smash Balloon Instagram Feed'] = [
        'manage_custom_instagram_feed_options',
        'manage_instagram_feed_options'
    ];

    return $pluginCaps;
});<?php

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
});<?php

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
});<?php

/**
 * PublishPress Capabilities [Free]
 *
 * Capabilities filters for the plugin Wordfence Security.
 *
 * Generated with Capabilities Extractor
 */

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

add_filter('cme_plugin_capabilities', function ($pluginCaps) {
    $pluginCaps['Wordfence Security'] = [
        'wf2fa_activate_2fa_others',
        'wf2fa_activate_2fa_self',
        'wf2fa_manage_settings'
    ];

    return $pluginCaps;
});<?php

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
});<?php

/**
 * PublishPress Capabilities [Free]
 *
 * Capabilities filters for the plugin Yoast SEO.
 *
 * Generated with Capabilities Extractor
 */

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

add_filter('cme_plugin_capabilities', function ($pluginCaps) {
    $pluginCaps['Yoast SEO'] = [
        'view_site_health_checks',
        'wpseo_bulk_edit',
        'wpseo_edit_advanced_metadata',
        'wpseo_manage_options'
    ];

    return $pluginCaps;
});