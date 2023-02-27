<?php
/**
 * Capability Manager Nav Menus Permission.
 * Nav menus permission and visibility per roles.
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

global $capsman;
$roles        = $capsman->roles;
$default_role = $capsman->get_last_role();

//add logged in and guest option
$ppc_other_permissions = ["ppc_users" => esc_html__('Logged In Users', 'capsman-enhanced'), "ppc_guest" => esc_html__('Logged Out Users', 'capsman-enhanced')];

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


$nav_menus = (array)get_terms('nav_menu');
$nav_menus = array_combine(wp_list_pluck($nav_menus, 'term_id'), wp_list_pluck($nav_menus, 'name'));


$nav_menu_item_option = !empty(get_option('capsman_nav_item_menus')) ? get_option('capsman_nav_item_menus') : [];
$nav_menu_item_option = array_key_exists($default_role, $nav_menu_item_option) ? (array)$nav_menu_item_option[$default_role] : [];

?>


    <div class="wrap publishpress-caps-manage pressshack-admin-wrapper pp-capability-menus-wrapper nav-menus">
        <div id="icon-capsman-admin" class="icon32"></div>
        <h2><?php esc_html_e('Navigation Menu Restrictions', 'capsman-enhanced'); ?></h2>

        <form method="post" id="ppc-nav-menu-form" action="admin.php?page=pp-capabilities-nav-menus">
            <?php wp_nonce_field('pp-capabilities-nav-menus'); ?>
            <div class="pp-columns-wrapper pp-enable-sidebar clear">
                <div class="pp-column-left">
                    <fieldset>
                        <table id="akmin">
                            <tr>
                                <td class="content">

                                    <div class="publishpress-filters">
                                        <select name="ppc-nav-menu-role" class="ppc-nav-menu-role">
                                            <optgroup label="Users">
                                                <?php
                                                foreach ($ppc_other_permissions as $p_value => $p_title) {
                                                    ?>
                                                    <option value="<?php echo esc_attr($p_value); ?>" <?php selected($default_role, $p_value); ?>> <?php echo esc_html($p_title); ?>
                                                        &nbsp;
                                                    </option>
                                                <?php }
                                                ?>
                                            </optgroup>

                                            <optgroup label="Roles">
                                                <?php
                                                foreach ($roles as $role_name => $name) {
                                                    $name = translate_user_role($name);
                                                    ?>
                                                    <option value="<?php echo esc_attr($role_name); ?>" <?php selected($default_role, $role_name); ?>> <?php echo esc_html($name); ?>
                                                        &nbsp;
                                                    </option>
                                                <?php } ?>
                                            </optgroup>

                                        </select> &nbsp;

                                        <img class="loading" src="<?php echo esc_url($capsman->mod_url); ?>/images/wpspin_light.gif" style="display: none">

                                        <input type="submit" name="nav-menu-submit"
                                            value="<?php esc_attr_e('Save Changes', 'capsman-enhanced') ?>"
                                            class="button-primary ppc-nav-menu-submit" style="float:right" />
                                    </div>

                                    <div id="pp-capability-menu-wrapper" class="postbox">
                                        <div class="pp-capability-menus">

                                            <div class="pp-capability-menus-wrap">
                                                <div id="pp-capability-menus-general"
                                                    class="pp-capability-menus-content editable-role"
                                                    style="display: block;">

                                                    <table class="wp-list-table widefat fixed striped pp-capability-menus-select">

                                                        <thead>
                                                            <tr class="ppc-menu-row parent-menu">

                                                                <td class="restrict-column ppc-menu-checkbox">
                                                                    <input id="check-all-item"
                                                                        class="check-item check-all-menu-item"
                                                                        type="checkbox"/>
                                                                </td>
                                                                <td class="menu-column ppc-menu-item">
                                                                    <label for="check-all-item">
                                                                <span class="menu-item-link check-all-menu-link">
                                                                    <strong>
                                                                    <?php esc_html_e('Toggle all', 'capsman-enhanced'); ?>
                                                                    </strong>
                                                                </span></label>
                                                                </td>

                                                            </tr>
                                                        </thead>

                                                        <tfoot>
                                                            <tr class="ppc-menu-row parent-menu">

                                                                <td class="restrict-column ppc-menu-checkbox">
                                                                    <input id="check-all-item-2"
                                                                        class="check-item check-all-menu-item"
                                                                        type="checkbox"/>
                                                                </td>
                                                                <td class="menu-column ppc-menu-item">
                                                                    <label for="check-all-item-2">
                                                                    <span class="menu-item-link check-all-menu-link">
                                                                    <strong>
                                                                        <?php esc_html_e('Toggle all', 'capsman-enhanced'); ?>
                                                                    </strong>
                                                                    </span>
                                                                    </label>
                                                                </td>

                                                            </tr>
                                                        </tfoot>

                                                        <tbody>
                                                        
                                                        <?php

                                                        if (count($nav_menus) > 0) {

                                                            $sn = 0;
                                                            foreach ($nav_menus as $menu_id => $menu_name) {
                                                                ?>

                                                                <tr class="ppc-menu-row parent-menu">

                                                                    <td class="restrict-column ppc-menu-checkbox">

                                                                    </td>
                                                                    <td class="menu-column ppc-menu-item parent">

                                                                        <label for="check-item-<?php echo (int) $sn; ?>">
                                                                        <span class="menu-item-link">
                                                                        <strong><i class="dashicons dashicons-menu-alt"></i>
                                                                            <?php echo esc_html(wp_strip_all_tags($menu_name)); ?>
                                                                        </strong></span>
                                                                        </label>
                                                                    </td>

                                                                </tr>

                                                                <?php
                                                                //begin menu item query
                                                                $menu_items = (array)wp_get_nav_menu_items($menu_id);

                                                                if (count($menu_items) === 0) {
                                                                    continue;
                                                                }

                                                                foreach ($menu_items as $menu_item) {
                                                                    $sn++;

                                                                    $sub_menu_value = $menu_item->ID . '_' . $menu_item->object_id . '_' . $menu_item->object;
                                                                    /**
                                                                     * 1.) Item ID
                                                                     * 2.) Object Id
                                                                     * 3.) Object (e.g, category)
                                                                     * object as last as it can contain underscore
                                                                     */

                                                                    if ($menu_item->menu_item_parent > 0) {
                                                                        $depth_space = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &mdash;&mdash;';
                                                                    } else {
                                                                        $depth_space = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &mdash;';
                                                                    }
                                                                    ?>
                                                                    <tr class="ppc-menu-row child-menu">

                                                                        <td class="restrict-column ppc-menu-checkbox">
                                                                            <input id="check-item-<?php echo (int) $sn; ?>"
                                                                                class="check-item" type="checkbox"
                                                                                name="pp_cababilities_restricted_items[]"
                                                                                value="<?php echo esc_attr($sub_menu_value); ?>"
                                                                                <?php echo (in_array($sub_menu_value, $nav_menu_item_option)) ? 'checked' : ''; ?> />
                                                                        </td>
                                                                        <td class="menu-column ppc-menu-item'">

                                                                            <label for="check-item-<?php echo (int) $sn; ?>">
                                                                            <span class="menu-item-link<?php echo (in_array($sub_menu_value, $nav_menu_item_option)) ? ' restricted' : ''; ?>">
                                                                            <strong><?php echo esc_html($depth_space); ?>
                                                                                <?php echo esc_html(wp_strip_all_tags($menu_item->title)); ?>
                                                                            </strong></span>
                                                                            </label>

                                                                        </td>

                                                                    </tr>
                                                                    <?php
                                                                }  // end foreach menu_items

                                                                $sn++;

                                                            } // end foreach nav_menus

                                                        } else {
                                                            ?>
                                                            <tr>
                                                                <td colspan="2"> <?php esc_html_e('There are no frontend menu links. To control access to navigation menus, please add menu links.', 'capsman-enhanced'); ?></td>
                                                            </tr>
                                                            <?php
                                                        }

                                                        ?>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="submit" name="nav-menu-submit"
                                        value="<?php esc_attr_e('Save Changes', 'capsman-enhanced') ?>"
                                        class="button-primary ppc-nav-menu-submit"/>

                                </td>
                            </tr>
                        </table>

                    </fieldset>
                </div><!-- .pp-column-left -->
                <div class="pp-column-right">
                <?php 
                $banners = new PublishPress\WordPressBanners\BannersMain;
                $banner_messages = ['<p>'];
                $banner_messages[] = sprintf(esc_html__('%1$s = No change', 'capsman-enhanced'), '<input type="checkbox" title="'. esc_attr__('usage key', 'capsman-enhanced') .'" disabled>');
                $banner_messages[] = sprintf(esc_html__('%1$s = This feature is denied', 'capsman-enhanced'), '<input type="checkbox" title="'. esc_attr__('usage key', 'capsman-enhanced') .'" checked disabled>');
                $banner_messages[] = '</p>';
                $banners->pp_display_banner(
                    '',
                    __('How to use Nav Menus', 'capsman-enhanced'),
                    $banner_messages,
                    'https://publishpress.com/knowledge-base/checkboxes/',
                    __('View Documentation', 'capsman-enhanced'),
                    '',
                    'button ppc-checkboxes-documentation-link'
                );
                ?>
                    <?php if (defined('CAPSMAN_PERMISSIONS_INSTALLED') && !CAPSMAN_PERMISSIONS_INSTALLED) { ?>
                            <?php
                            $banners->pp_display_banner(
                                esc_html__( 'Recommendations for you', 'capsman-enhanced' ),
                                esc_html__( 'Control permissions for individual posts and pages', 'capsman-enhanced' ),
                                array(
                                    esc_html__( 'Choose who can read and edit each post.', 'capsman-enhanced' ),
                                    esc_html__( 'Allow specific user roles or users to manage each post.', 'capsman-enhanced' ),
                                    esc_html__( 'PublishPress Permissions is 100% free to install.', 'capsman-enhanced' )
                                ),
                                admin_url( 'plugin-install.php?s=publishpress-ppcore-install&tab=search&type=term' ),
                                esc_html__( 'Click here to install PublishPress Permissions', 'capsman-enhanced' ),
                                'install-permissions.jpg'
                            );
                            ?>
                    <?php } ?>
                </div><!-- .pp-column-right -->
            </div><!-- .pp-columns-wrapper -->
        </form>

        <script type="text/javascript">
            /* <![CDATA[ */
            jQuery(document).ready(function ($) {

                // -------------------------------------------------------------
                //   Set form action attribute to include role
                // -------------------------------------------------------------
                $('#ppc-nav-menu-form').attr('action', '<?php echo esc_url_raw(admin_url('admin.php?page=pp-capabilities-nav-menus&role=' . $default_role . '')); ?>');

                // -------------------------------------------------------------
                //   Instant restricted item class
                // -------------------------------------------------------------
                $(document).on('change', '.pp-capability-menus-wrapper .ppc-menu-row .check-item', function () {

                    if ($(this).is(':checked')) {
                        //add class if value is checked
                        $(this).closest('tr').find('.menu-item-link').addClass('restricted');

                        //toggle all checkbox
                        if ($(this).hasClass('check-all-menu-item')) {
                            $("input[type='checkbox'][name='pp_cababilities_restricted_nav[]']").prop('checked', true);
                            $("input[type='checkbox'][name='pp_cababilities_restricted_items[]']").prop('checked', true);
                            $('.menu-item-link').addClass('restricted');
                        } else {
                            $('.check-all-menu-link').removeClass('restricted');
                            $('.check-all-menu-item').prop('checked', false);
                        }

                    } else {
                        //unchecked value
                        $(this).closest('tr').find('.menu-item-link').removeClass('restricted');

                        //toggle all checkbox
                        if ($(this).hasClass('check-all-menu-item')) {
                            $("input[type='checkbox'][name='pp_cababilities_restricted_nav[]']").prop('checked', false);
                            $("input[type='checkbox'][name='pp_cababilities_restricted_items[]']").prop('checked', false);
                            $('.menu-item-link').removeClass('restricted');
                        } else {
                            $('.check-all-menu-link').removeClass('restricted');
                            $('.check-all-menu-item').prop('checked', false);
                        }

                    }

                });

                // -------------------------------------------------------------
                //   Load selected roles menu
                // -------------------------------------------------------------
                $(document).on('change', '.pp-capability-menus-wrapper .ppc-nav-menu-role', function () {

                    //disable select
                    $('.pp-capability-menus-wrapper .ppc-nav-menu-role').attr('disabled', true);

                    //hide button
                    $('.pp-capability-menus-wrapper .ppc-nav-menu-submit').hide();

                    //show loading
                    $('#pp-capability-menu-wrapper').hide();
                    $('div.publishpress-caps-manage img.loading').show();

                    //go to url
                    window.location = '<?php echo esc_url_raw(admin_url('admin.php?page=pp-capabilities-nav-menus&role=')); ?>' + $(this).val() + '';

                });

            });
            /* ]]> */
        </script>


        <?php if (!defined('PUBLISHPRESS_CAPS_PRO_VERSION') || get_option('cme_display_branding')) {
            cme_publishpressFooter();
        }
        ?>
    </div>
<?php
