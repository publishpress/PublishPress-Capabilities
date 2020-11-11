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
$roles = $this->roles;
$default_role = $this->current;


//add logged in and guest option
$ppc_other_permissions = array("ppc_users" => __('Logged In Users', 'capsman-enhanced'), "ppc_guest" => __('Logged Out Users', 'capsman-enhanced'));

if (!empty($_REQUEST['role'])) {

    if (array_key_exists($_REQUEST['role'], $ppc_other_permissions)) {
        $default_role = $_REQUEST['role'];
        $role_caption = $ppc_other_permissions[$_REQUEST['role']];
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


    <div class="wrap publishpress-caps-manage pressshack-admin-wrapper pp-capability-menus-wrapper">
        <div id="icon-capsman-admin" class="icon32"></div>
        <h2><?php _e('Navigation Menus Permission', 'capsman-enhanced'); ?></h2>

        <form method="post" id="ppc-nav-menu-form" action="admin.php?page=<?php echo $this->ID ?>-pp-nav-menus">
            <?php wp_nonce_field('capsman-pp-nav-menus'); ?>

            <fieldset>
                <table id="akmin">
                    <tr>
                        <td class="content">

                            <dl>
                                <dt><?php printf(__('Nav Menus Permission for %s', 'capsman-enhanced'), $role_caption); ?></dt>

                                <dd>
                                    <div class="publishpress-headline">
                                    <span class="cme-subtext">
                                    <strong><?php _e('Note', 'capsman-enhanced'); ?>:</strong>
                                    <?php _e('This feature only remove menu from navigation and prevent access to the menu page if it\'s not custom menu link for the restricted roles and users.', 'capsman-enhanced'); ?>
                                    </span>
                                    </div>
                                </dd>

                                <table width='100%' class="form-table">
                                    <tr>
                                        <td>
                                            <select name="ppc-nav-menu-role" class="ppc-nav-menu-role">
                                                <optgroup label="Users">
                                                    <?php
                                                    foreach ($ppc_other_permissions as $p_value => $p_title) {
                                                        ?>
                                                        <option value="<?php echo $p_value; ?>" <?php selected($default_role, $p_value); ?>> <?php echo $p_title; ?>
                                                            &nbsp;
                                                        </option>
                                                    <?php }
                                                    ?>
                                                </optgroup>

                                                <optgroup label="Roles">
                                                    <?php
                                                    foreach ($roles as $role => $name) {
                                                        $name = translate_user_role($name);
                                                        ?>
                                                        <option value="<?php echo $role; ?>" <?php selected($default_role, $role); ?>> <?php echo $name; ?>
                                                            &nbsp;
                                                        </option>
                                                    <?php } ?>
                                                </optgroup>

                                            </select> &nbsp;

                                        <td>
                                            <div style="float:right">
                                                <input type="submit" name="nav-menu-submit"
                                                       value="<?php _e('Save Changes', 'capsman-enhanced') ?>"
                                                       class="button-primary ppc-nav-menu-submit"/> &nbsp;
                                            </div>
                                        </td>
                                        </td>
                                    </tr>
                                </table>

                                <div id="pp-capability-menu-wrapper" class="postbox">
                                    <div class="pp-capability-menus">

                                        <div class="pp-capability-menus-wrap">
                                            <div id="pp-capability-menus-general"
                                                 class="pp-capability-menus-content editable-role"
                                                 style="display: block;">

                                                <table class="wp-list-table widefat fixed pp-capability-menus-select">

                                                    <thead>
                                                    <tr>
                                                        <th class="menu-column"><?php _e('Menu', 'capsman-enhanced') ?></th>
                                                        <th class="restrict-column"><?php _e('Restrict', 'capsman-enhanced') ?></th>
                                                    </tr>
                                                    </thead>

                                                    <tfoot>
                                                    <tr>
                                                        <th class="menu-column"><?php _e('Menu', 'capsman-enhanced') ?></th>
                                                        <th class="restrict-column"><?php _e('Restrict', 'capsman-enhanced') ?></th>
                                                    </tr>
                                                    </tfoot>

                                                    <tbody>
                                                    <tr class="ppc-menu-row parent-menu">

                                                        <td class="menu-column ppc-menu-item">
                                                            <label for="check-all-item">
                                                        <span class="menu-item-link check-all-menu-link">
                                                            <strong><i class="dashicons dashicons-leftright"></i>
                                                            <?php _e('Toggle all', 'capsman-enhanced'); ?>
                                                            </strong>
                                                        </span></label>
                                                        </td>

                                                        <td class="restrict-column ppc-menu-checkbox">
                                                            <input id="check-all-item"
                                                                   class="check-item check-all-menu-item"
                                                                   type="checkbox"/>
                                                        </td>

                                                    </tr>

                                                    <?php

                                                    if (count($nav_menus) > 0) {

                                                        $sn = 0;
                                                        foreach ($nav_menus as $menu_id => $menu_name) {
                                                            ?>

                                                            <tr class="ppc-menu-row parent-menu">

                                                                <td class="menu-column ppc-menu-item parent">

                                                                    <label for="check-item-<?php echo $sn; ?>">
                                                                    <span class="menu-item-link">
                                                                    <strong><i class="dashicons dashicons-arrow-right"></i>
                                                                        <?php echo strip_tags($menu_name); ?>
                                                                    </strong></span>
                                                                    </label>
                                                                </td>

                                                                <td class="restrict-column ppc-menu-checkbox">

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
                                                                    $depth_space = '&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &bull;';
                                                                } else {
                                                                    $depth_space = '&nbsp;&nbsp;&nbsp; &#x25cf;';
                                                                }
                                                                ?>
                                                                <tr class="ppc-menu-row child-menu">

                                                                    <td class="menu-column ppc-menu-item'">

                                                                        <label for="check-item-<?php echo $sn; ?>">
                                                                        <span class="menu-item-link<?php echo (in_array($sub_menu_value, $nav_menu_item_option)) ? ' restricted' : ''; ?>">
                                                                        <strong><?php echo $depth_space; ?>
                                                                            <?php echo strip_tags($menu_item->title); ?>
                                                                        </strong></span>
                                                                        </label>

                                                                    </td>

                                                                    <td class="restrict-column ppc-menu-checkbox">
                                                                        <input id="check-item-<?php echo $sn; ?>"
                                                                               class="check-item" type="checkbox"
                                                                               name="pp_cababilities_restricted_items[]"
                                                                               value="<?php echo $sub_menu_value; ?>"
                                                                            <?php echo (in_array($sub_menu_value, $nav_menu_item_option)) ? 'checked' : ''; ?> />
                                                                    </td>

                                                                </tr>
                                                                <?php
                                                            }  // end foreach menu_items

                                                            $sn++;

                                                        } // end foreach nav_menus

                                                    } else {
                                                        ?>
                                                        <tr>
                                                            <td style="color: red;"> <?php _e('No menu found', 'capsman-enhanced'); ?></td>
                                                        </tr>
                                                        <?php
                                                    }

                                                    ?>
                                                    <tr class="ppc-menu-row parent-menu">

                                                        <td class="menu-column ppc-menu-item">
                                                            <label for="check-all-item-2">
                                                            <span class="menu-item-link check-all-menu-link">
                                                            <strong>
                                                                <i class="dashicons dashicons-leftright"></i>
                                                                <?php _e('Toggle all', 'capsman-enhanced'); ?>
                                                            </strong>
                                                            </span>
                                                            </label>
                                                        </td>

                                                        <td class="restrict-column ppc-menu-checkbox">
                                                            <input id="check-all-item-2"
                                                                   class="check-item check-all-menu-item"
                                                                   type="checkbox"/>
                                                        </td>

                                                    </tr>

                                                    </tbody>
                                                </table>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <input type="submit" name="nav-menu-submit"
                                       value="<?php _e('Save Changes', 'capsman-enhanced') ?>"
                                       class="button-primary ppc-nav-menu-submit"/> &nbsp;

                            </dl>

                        </td>
                    </tr>
                </table>

            </fieldset>

        </form>

        <script type="text/javascript">
            /* <![CDATA[ */
            jQuery(document).ready(function ($) {

                // -------------------------------------------------------------
                //   Set form action attribute to include role
                // -------------------------------------------------------------
                $('#ppc-nav-menu-form').attr('action', '<?php echo admin_url('admin.php?page=' . $this->ID . '-pp-nav-menus&role=' . $default_role . ''); ?>');

                // -------------------------------------------------------------
                //   Instant restricted item class
                // -------------------------------------------------------------
                $(document).on('change', '.pp-capability-menus-wrapper .ppc-menu-row .check-item', function () {

                    if ($(this).is(':checked')) {
                        //add class if value is checked
                        $(this).parent().parent().find('.menu-item-link').addClass('restricted');

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
                        $(this).parent().parent().find('.menu-item-link').removeClass('restricted');

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
                    $('#pp-capability-menu-wrapper').html('<img src="<?php echo $capsman->mod_url; ?>/images/loader-black.gif" alt="loading...">');

                    //go to url
                    window.location = '<?php echo admin_url('admin.php?page=' . $this->ID . '-pp-nav-menus&role='); ?>' + $(this).val() + '';

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
