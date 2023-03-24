<?php
/**
 * Capability Manager Frontend Features.
 * Hide and block selected Frontend Features like toolbar, dashboard widgets etc per-role.
 *
 *    Copyright 2020, PublishPress <help@publishpress.com>
 *
 *    This program is free software; you can redistribute it and/or
 *    modify it under the terms of the GNU General Public License
 *    version 2 as published by the Free Software Foundation.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

use PublishPress\Capabilities\PP_Capabilities_Frontend_Features_UI;
use PublishPress\Capabilities\PP_Capabilities_Frontend_Features_Data;

require_once(dirname(CME_FILE) . '/includes/features/frontend-features/frontend-features-data.php');
require_once(dirname(CME_FILE) . '/includes/features/frontend-features/frontend-features-ui.php');


//instantiate frontend features ui
PP_Capabilities_Frontend_Features_UI::instance();


global $capsman;

$roles        = $capsman->roles;
$default_role = $capsman->get_last_role();

//add logged in and guest option
$ppc_other_permissions = [
    "ppc_users" => esc_html__('Logged In Users', 'capsman-enhanced'),
    "ppc_guest" => esc_html__('Logged Out Users', 'capsman-enhanced')
];

if (!empty($_REQUEST['role'])) {
    if (array_key_exists(sanitize_key($_REQUEST['role']), $ppc_other_permissions)) {
        $default_role = sanitize_key($_REQUEST['role']);
        $role_caption = $ppc_other_permissions[$default_role];
    } else {
        $role_caption = translate_user_role($roles[$default_role]);
    }
} else {
    $role_caption = translate_user_role($roles[$default_role]);
}

$disabled_frontend_items = !empty(get_option('capsman_disabled_frontend_features')) ? (array)get_option('capsman_disabled_frontend_features') : [];
$disabled_frontend_items = array_key_exists($default_role, $disabled_frontend_items) ? (array)$disabled_frontend_items[$default_role] : [];

$frontend_features_elements = PP_Capabilities_Frontend_Features_Data::elementsLayout();
$icon_list                  = PP_Capabilities_Frontend_Features_Data::elementLayoutItemIcons();

$active_tab_slug    = (!empty($_REQUEST['pp_caps_tab'])) ? sanitize_key($_REQUEST['pp_caps_tab']) : 'customstyles';
?>

<div class="wrap publishpress-caps-manage pressshack-admin-wrapper pp-capability-menus-wrapper frontend-features">
    <div id="icon-capsman-admin" class="icon32"></div>
    <h2><?php esc_html_e('Frontend Features', 'capsman-enhanced'); ?>
    </h2>

    <form method="post" id="ppc-frontend-features-form" action="admin.php?page=pp-capabilities-frontend-features">
        <?php wp_nonce_field('pp-capabilities-frontend-features'); ?>
        <input type="hidden" name="pp_caps_tab"
            value="<?php echo esc_attr($active_tab_slug);?>" />

        <div class="pp-columns-wrapper pp-enable-sidebar">
            <div class="pp-column-left">
                <table id="akmin" style="border-bottom: none !important;">
                    <tr>
                        <td class="content">

                            <div class="publishpress-filters">
                                <div class="pp-capabilities-submit-top" style="float:right;">
                                    <input type="submit" name="frontend-features-submit"
                                        value="<?php esc_attr_e('Save Changes', 'capsman-enhanced') ?>"
                                        class="button-primary ppc-frontend-features-submit" />
                                </div>

                                <select name="ppc-frontend-features-role" class="ppc-frontend-features-role">
                                    <optgroup label="Users">
                                        <?php 
                                            foreach ($ppc_other_permissions as $p_value => $p_title) { 
                                                ?>
                                            <option
                                                value="<?php echo esc_attr($p_value); ?>"
                                                <?php selected($default_role, $p_value); ?>>
                                                <?php echo esc_html($p_title); ?>
                                                &nbsp;
                                            </option>
                                            <?php
                                            }
                                        ?>
                                    </optgroup>

                                    <optgroup label="Roles">
                                        <?php 
                                        foreach ($roles as $role_name => $name)  {
                                            $name = translate_user_role($name); 
                                        ?>
                                        <option
                                            value="<?php echo esc_attr($role_name); ?>"
                                            <?php selected($default_role, $role_name); ?>>
                                            <?php echo esc_html($name); ?>
                                            &nbsp;
                                        </option>
                                        <?php
                                        } 
                                        ?>
                                    </optgroup>

                                </select> &nbsp;

                                <img class="loading"
                                    src="<?php echo esc_url_raw($capsman->mod_url); ?>/images/wpspin_light.gif"
                                    style="display: none">
                            </div>

                            <div id="pp-capability-menu-wrapper" class="postbox">
                                <div class="pp-capability-menus">

                                    <div class="pp-capability-menus-wrap">
                                        <div id="pp-capability-menus-general"
                                            class="pp-capability-menus-content editable-role" style="display: block;">
                                            <div id="ppc-capabilities-wrapper" class="postbox">

                                                <div class="ppc-capabilities-tabs">
                                                    <ul>
                                                        <?php
                                                            foreach ($frontend_features_elements as $section_title => $section_elements) {
                                                                $section_slug = strtolower(ppc_remove_non_alphanumeric_space_characters($section_title));
                                                                $icon_name    = isset($icon_list[$section_slug]) ? $icon_list[$section_slug] : '&nbsp;';
                                                                $active_class = ($section_slug === $active_tab_slug) ? 'ppc-capabilities-tab-active' : '';

                                                                $disabled_count  = count(PP_Capabilities_Frontend_Features_Data::getRestrictedElements($disabled_frontend_items, $section_slug)); ?>
                                                        <li data-slug="<?php esc_attr_e($section_slug); ?>"
                                                            data-content="cme-cap-type-tables-<?php esc_attr_e($section_slug); ?>"
                                                            data-name="<?php esc_attr_e($section_title); ?>"
                                                            class="<?php esc_attr_e($active_class); ?>">
                                                            <i
                                                                class="dashicons dashicons-<?php echo esc_attr($icon_name) ?>"></i>
                                                            <?php esc_html_e($section_title); ?>
                                                            <?php if ($disabled_count > 0) : ?>
                                                            <span class="pp-capabilities-feature-count">
                                                                <?php echo esc_html__('Enabled:', 'capsman-enhanced') . ' ' . esc_html($disabled_count); ?>
                                                            </span>
                                                            <?php endif; ?>
                                                        </li>
                                                        <?php
                                                            }
                                                                ?>
                                                    </ul>
                                                </div>

                                                <div class="ppc-capabilities-content">
                                                    <?php
                                                      $sn = 0;
                                                      foreach ($frontend_features_elements as $section_title => $section_elements) :
                                                          $sn++;
                                                          $section_slug = strtolower(ppc_remove_non_alphanumeric_space_characters($section_title));
                                                          $active_style = ($section_slug === $active_tab_slug) ? '' : 'display:none;';
                                                            ?>
                                                    <div id="cme-cap-type-tables-<?php esc_attr_e($section_slug); ?>"
                                                        style="<?php esc_attr_e($active_style); ?>">
                                                        <table
                                                            class="wp-list-table widefat striped pp-capability-menus-select">
                                                            <tfoot>
                                                                <tr class="ppc-menu-row parent-menu">

                                                                    <td class="restrict-column ppc-menu-checkbox">
                                                                        <input class="check-item check-all-menu-item"
                                                                            type="checkbox" />
                                                                    </td>
                                                                    <td class="menu-column ppc-menu-item">
                                                                    </td>

                                                                </tr>
                                                            </tfoot>
                                                            <tbody>
                                                                <?php do_action("pp_capabilities_frontend_features_{$section_slug}_before_subsection_tr"); 
                                                                $display_title_class = empty($section_elements) ? 'temporarily hidden-element' : ''; 
                                                                ?>
                                                                <tr class="custom-table-title <?php echo esc_attr($display_title_class); ?>">
                                                                    <td colspan="2" class="title-td">
                                                                        <label>
                                                                            <?php esc_attr_e('Edit Your Custom Styles', 'capsman-enhanced') ?>
                                                                        </label>
                                                                    </td>
                                                                </tr>
                                                                <tr class="custom-item-wrapper-tr <?php echo esc_attr($display_title_class); ?>">
                                                                    <td colspan="2" class="custom-item-wrapper-td">
                                                                        <table class="wp-list-table widefat fixed striped table-view-list custom-items-table">
                                                                            <thead>
                                                                                <tr>
                                                                                    <td class="manage-column column-cb check-column"><?php esc_attr_e('Enable', 'capsman-enhanced') ?></td>
                                                                                    <th scope="col" class="manage-column column-primary"
                                                                                    >
                                                                                        <?php esc_attr_e('Label', 'capsman-enhanced') ?>
                                                                                    </th>
                                                                                    <th scope="col" class="manage-column">
                                                                                        <?php esc_attr_e('View', 'capsman-enhanced') ?>
                                                                                    </th>
                                                                                    <th scope="col" class="manage-column">
                                                                                        <?php esc_attr_e('Edit', 'capsman-enhanced') ?>
                                                                                    </th>
                                                                                    <th scope="col" class="manage-column">
                                                                                        <?php esc_attr_e('Delete', 'capsman-enhanced') ?>
                                                                                    </th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                            <?php
                                                                            foreach ($section_elements as $section_id => $section_array) :
                                                                                $sn++;
                                                                                if (!$section_id) {
                                                                                    continue;
                                                                                }
                                                                                $section_tr_function = 'do_pp_capabilities_frontend_features_' . $section_slug . '_tr';
                                                                                $function_args = [
                                                                                    'disabled_frontend_items' => $disabled_frontend_items,
                                                                                    'section_array'           => $section_array,
                                                                                    'section_slug'            => $section_slug,
                                                                                    'section_id'              => $section_id,
                                                                                    'sn'                      => $sn
                                                                                ];
                                                                                //render tr ui for row
                                                                                if (method_exists('\PublishPress\Capabilities\PP_Capabilities_Frontend_Features_UI', $section_tr_function)) {
                                                                                    PP_Capabilities_Frontend_Features_UI::$section_tr_function($function_args);
                                                                                } elseif (function_exists($section_tr_function)) {
                                                                                    $section_tr_function($function_args);
                                                                                }
                                                                            endforeach; // $section_elements subsection loop
                                                                            ?>
                                                                            </tbody>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <?php
                                                        endforeach; // $frontend_features_elements section loop
                                                    ?>
                                                    <?php do_action('pp_capabilities_frontend_features_after_table'); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="editor-features-footer-meta">
                                <div style="float:right">
                                    <input type="submit" name="frontend-features-submit"
                                        value="<?php esc_attr_e('Save Changes', 'capsman-enhanced') ?>"
                                        class="button-primary ppc-frontend-features-submit" />
                                </div>
                            </div>

                        </td>
                    </tr>
                </table>
            </div><!-- .pp-column-left -->
            <div class="pp-column-right">
                <?php
                $banners = new PublishPress\WordPressBanners\BannersMain;
                $banner_messages = ['<p>'];
                $banner_messages[] = sprintf(esc_html__('%1$s = No change', 'capsman-enhanced'), '<input type="checkbox" title="'. esc_attr__('usage key', 'capsman-enhanced') .'" disabled>');
                $banner_messages[] = sprintf(esc_html__('%1$s = This feature is enabled', 'capsman-enhanced'), '<input type="checkbox" title="'. esc_attr__('usage key', 'capsman-enhanced') .'" checked disabled>');
                $banner_messages[] = '</p>';
                $banners->pp_display_banner(
                    '',
                    __('How to use Frontend Features', 'capsman-enhanced'),
                    $banner_messages,
                    'https://publishpress.com/knowledge-base/checkboxes/',
                    __('View Documentation', 'capsman-enhanced'),
                    '',
                    'button ppc-checkboxes-documentation-link'
                );
                ?>
                <?php if (defined('CAPSMAN_PERMISSIONS_INSTALLED') && !CAPSMAN_PERMISSIONS_INSTALLED) { ?>
                <?php
                    $banners = new PublishPress\WordPressBanners\BannersMain;
                    $banners->pp_display_banner(
                        esc_html__('Recommendations for you', 'capsman-enhanced'),
                        esc_html__('Control permissions for individual posts and pages', 'capsman-enhanced'),
                        array(
                            esc_html__('Choose who can read and edit each post.', 'capsman-enhanced'),
                            esc_html__('Allow specific user roles or users to manage each post.', 'capsman-enhanced'),
                            esc_html__('PublishPress Permissions is 100% free to install.', 'capsman-enhanced')
                        ),
                        admin_url('plugin-install.php?s=publishpress-ppcore-install&tab=search&type=term'),
                        esc_html__('Click here to install PublishPress Permissions', 'capsman-enhanced'),
                        'install-permissions.jpg'
                    );
                    ?>
                <?php } ?>
            </div><!-- .pp-column-right -->
        </div><!-- .pp-columns-wrapper -->
    </form>

    <?php if (!defined('PUBLISHPRESS_CAPS_PRO_VERSION') || get_option('cme_display_branding')) {
                        cme_publishpressFooter();
                    }
    ?>
</div>

<script type="text/javascript">
    /* <![CDATA[ */
    jQuery(document).ready(function($) {

        // Tabs and Content display
        $('.ppc-capabilities-tabs > ul > li').click(function() {
            var $pp_tab = $(this).attr('data-content');

            $("[name='pp_caps_tab']").val($(this).attr('data-slug'));

            // Show current Content
            $('.ppc-capabilities-content > div').hide();
            $('#' + $pp_tab).show();

            // Active current Tab
            $('.ppc-capabilities-tabs > ul > li').removeClass('ppc-capabilities-tab-active');
            $(this).addClass('ppc-capabilities-tab-active');

        });

        // -------------------------------------------------------------
        //   Set form action attribute to include role
        // -------------------------------------------------------------
        $('#ppc-frontend-features-form').attr('action',
            '<?php echo esc_url_raw(admin_url('admin.php?page=pp-capabilities-frontend-features&role=' . $default_role . '')); ?>'
        )

        // -------------------------------------------------------------
        //   Instant restricted item class
        // -------------------------------------------------------------
        $(document).on('change', '.pp-capability-menus-wrapper .ppc-menu-row .check-item', function() {

            if ($(this).is(':checked')) {
                //add class if value is checked
                $(this).closest('tr').find('.menu-item-link').addClass('restricted')

                //toggle all checkbox
                if ($(this).hasClass('check-all-menu-item')) {
                    $(this).closest('table').find(
                        "input[type='checkbox'][name='capsman_disabled_frontend_features[]']").prop(
                        'checked', true)
                    $(this).closest('table').find('.menu-item-link').addClass('restricted')
                } else {
                    $(this).closest('table').find('.check-all-menu-link').removeClass('restricted')
                    $(this).closest('table').find('.check-all-menu-item').prop('checked', false)
                }

            } else {
                //unchecked value
                $(this).closest('tr').find('.menu-item-link').removeClass('restricted')

                //toggle all checkbox
                if ($(this).hasClass('check-all-menu-item')) {
                    $(this).closest('table').find(
                        "input[type='checkbox'][name='capsman_disabled_frontend_features[]']").prop(
                        'checked', false)
                    $(this).closest('table').find('.menu-item-link').removeClass('restricted')
                } else {
                    $(this).closest('table').find('.check-all-menu-link').removeClass('restricted')
                    $(this).closest('table').find('.check-all-menu-item').prop('checked', false)
                }

            }

        })

        // -------------------------------------------------------------
        //   Load selected roles menu
        // -------------------------------------------------------------
        $(document).on('change', '.pp-capability-menus-wrapper .ppc-frontend-features-role', function() {

            //disable select
            $('.pp-capability-menus-wrapper .ppc-frontend-features-role').attr('disabled', true)

            //hide button
            $('.pp-capability-menus-wrapper .ppc-frontend-features-submit').hide()

            //show loading
            $('#pp-capability-menu-wrapper').hide()
            $('div.publishpress-caps-manage img.loading').show()

            //go to url
            window.location =
                '<?php echo esc_url_raw(admin_url('admin.php?page=pp-capabilities-frontend-features&role=')); ?>' +
                $(this).val() + ''

        })

    });
    /* ]]> */
</script>
<?php
?>