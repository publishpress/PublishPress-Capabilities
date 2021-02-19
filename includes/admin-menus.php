<?php
/**
 * Capability Manager Admin Menus Permission.
 * Admin menus permission and visibility per roles.
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

global $capsman, $menu, $submenu;
$roles = $this->roles;
$default_role = $this->current;
$role_caption = translate_user_role($roles[$default_role]);

$admin_global_menu = PPC_ADMIN_GLOBAL_MENU;
$admin_global_submenu = PPC_ADMIN_GLOBAL_SUBMENU;

$admin_menu_option = !empty(get_option('capsman_admin_menus')) ? get_option('capsman_admin_menus') : [];
$admin_menu_option = array_key_exists($default_role, $admin_menu_option) ? (array)$admin_menu_option[$default_role] : [];

$admin_child_menu_option = !empty(get_option('capsman_admin_child_menus')) ? get_option('capsman_admin_child_menus') : [];
$admin_child_menu_option = array_key_exists($default_role, $admin_child_menu_option) ? (array)$admin_child_menu_option[$default_role] : [];
?>

<div class="wrap publishpress-caps-manage pressshack-admin-wrapper pp-capability-menus-wrapper">
    <div id="icon-capsman-admin" class="icon32"></div>
    <h2><?php _e('Admin Menus Permission', 'capsman-enhanced'); ?></h2>

    <form method="post" id="ppc-admin-menu-form" action="admin.php?page=<?php echo $this->ID ?>-pp-admin-menus">
        <?php wp_nonce_field('capsman-pp-admin-menus'); ?>

        <fieldset>
            <table id="akmin">
                <tr>
                    <td class="content">

                        <dl>
                            <dt><?php printf(__('Admin Menus Permission for %s', 'capsman-enhanced'), $role_caption); ?></dt>

                            <dd>
                                <div class="publishpress-headline">
                                    <span class="cme-subtext">
                                    <strong><?php _e('Note', 'capsman-enhanced'); ?>:</strong>
                                    <?php _e('This feature only remove menu from admin sidebar menu list and prevent access to the menu page for the restricted roles.', 'capsman-enhanced'); ?>
                                    </span>
                                </div>
                            </dd>

                            <table width='100%' class="form-table">
                                <tr>
                                    <td>
                                        <select name="ppc-admin-menu-role" class="ppc-admin-menu-role">
                                            <?php
                                            foreach ($roles as $role => $name) :
                                                $name = translate_user_role($name);
                                                ?>
                                                <option value="<?php echo $role;?>" <?php selected($default_role, $role);?>> <?php echo $name;?> &nbsp;</option>
                                            <?php
                                            endforeach;
                                            ?>
                                        </select> &nbsp;

                                        <td>
                                            <div style="float:right">
                                                <input type="submit" name="admin-menu-submit"
                                                    value="<?php _e('Save Changes', 'capsman-enhanced') ?>"
                                                    class="button-primary ppc-admin-menu-submit"/> &nbsp;
                                            </div>
                                        </td>
                                    </td>
                                </tr>
                            </table>

                            <div id="pp-capability-menu-wrapper" class="postbox">
                                <div class="pp-capability-menus">

                                    <div class="pp-capability-menus-wrap">
                                        <div id="pp-capability-menus-general"
                                             class="pp-capability-menus-content editable-role" style="display: block;">

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
                                                        <input id="check-all-item" class="check-item check-all-menu-item" type="checkbox"/>
                                                    </td>
                                                
                                                </tr>

                                                <?php

                                                if (isset($admin_global_menu) && '' !== $admin_global_menu) {

                                                    $sn = 0;
                                                    foreach ($admin_global_menu as $key => $item) {

                                                        $item_menu_slug = $item[2];

                                                        if ('' === $item_menu_slug || (!$item[0] && !isset($admin_global_submenu[$item_menu_slug]))) {
                                                            continue;
                                                        }

                                                        //disable capmans checkbox if admin is editing own role
                                                        if ($item_menu_slug === 'capsman' && in_array($default_role, wp_get_current_user()->roles)) {
                                                            $disabled_field = ' disabled="disabled"';
                                                            $disabled_class = ' disabled';

                                                            $disabled_info = '<div class="tooltip"><i class="dashicons dashicons-info"></i> <span class="tooltiptext">' 
                                                            . __('This option is disabled to prevent complete lockout', 'capsman-enhanced') 
                                                            . '</span></div>';

                                                        } else {
                                                            $disabled_field = $disabled_class = $disabled_info = '';
                                                        }
                                                        ?>
                                                                            
                                                        <tr class="ppc-menu-row parent-menu">

                                                            <td class="menu-column ppc-menu-item <?php echo $disabled_class;?>">

                                                                <label for="check-item-<?php echo $sn;?>">
                                                                    <span class="menu-item-link<?php echo (in_array($item_menu_slug, $admin_menu_option)) ? ' restricted' : '';?>">
                                                                    <strong><i class="dashicons dashicons-arrow-right"></i>
                                                                        <?php echo strip_tags($item[0]);?>
                                                                    </strong></span>
                                                                </label> 
                                                                
                                                                <?php echo $disabled_info;?>
                                                            </td>

                                                            <td class="restrict-column ppc-menu-checkbox">
                                                            <input id="check-item-<?php echo $sn;?>"<?php echo $disabled_field;?> class="check-item" type="checkbox" 
                                                                name="pp_cababilities_disabled_menu<?php echo $disabled_class;?>[]" 
                                                                value="<?php echo $item_menu_slug;?>"<?php echo (in_array($item_menu_slug, $admin_menu_option)) ? ' checked' : '';?> />
                                                            </td>

                                                        </tr>

                                                        <?php
                                                        if (!isset($admin_global_submenu[$item_menu_slug])) {
                                                            continue;
                                                        }

                                                        foreach ($admin_global_submenu[$item_menu_slug] as $subkey => $subitem) {
                                                            $sn++;
                                                            $submenu_slug = $subitem[2];

                                                            //disable capsman-pp-admin-menus checkbox if admin is editing own role
                                                            if ( $submenu_slug === 'capsman-pp-admin-menus' && in_array($default_role, wp_get_current_user()->roles)) {
                                                                $disabled_field = ' disabled="disabled"';
                                                                $disabled_class = ' disabled';

                                                                $disabled_info = '<div class="tooltip"><i class="dashicons dashicons-info"></i> <span class="tooltiptext">' 
                                                                . __('This option is disabled to prevent complete lockout', 'capsman-enhanced') 
                                                                . '</span></div>';

                                                            } else {
                                                                $disabled_field = $disabled_class = $disabled_info = '';
                                                            }

                                                            $sub_menu_value = $item_menu_slug . $subkey;

                                                            ?>
                                                            <tr class="ppc-menu-row child-menu">

                                                                <td class="menu-column ppc-menu-item'<?php echo $disabled_class;?>">

                                                                    <label for="check-item-<?php echo $sn;?>">
                                                                        <span class="menu-item-link<?php echo (in_array($sub_menu_value, $admin_child_menu_option)) ? ' restricted' : '';?>">
                                                                        <strong>&nbsp;&nbsp;&nbsp; &#x25cf;
                                                                        <?php echo strip_tags($subitem[0]);?>
                                                                        </strong></span>
                                                                    </label>
                                                                    
                                                                    <?php echo $disabled_info;?>
                                                                </td>

                                                                <td class="restrict-column ppc-menu-checkbox">
                                                                    <input id="check-item-<?php echo $sn;?>"<?php echo $disabled_field;?> class="check-item" type="checkbox" 
                                                                        name="pp_cababilities_disabled_child_menu<?php echo $disabled_class;?>[]" 
                                                                        value="<?php echo $sub_menu_value;?>" 
                                                                        <?php echo (in_array($sub_menu_value, $admin_child_menu_option)) ? 'checked' : '';?> 
                                                                        data-val="<?php echo $sub_menu_value;?>" />
                                                                </td>

                                                            </tr>
                                                        <?php
                                                        }  // end foreach admin_global_submenu

                                                        $sn++;

                                                    } // end foreach admin_global_menu

                                                } else {
                                                    ?>
                                                    <tr><td style="color: red;"> <?php _e('No menu found', 'capsman-enhanced');?></td></tr>
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
                                                        <input id="check-all-item-2" class="check-item check-all-menu-item" type="checkbox"/>
                                                    </td>

                                                </tr>

                                                </tbody>
                                            </table>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <input type="submit" name="admin-menu-submit"
                                   value="<?php _e('Save Changes', 'capsman-enhanced') ?>"
                                   class="button-primary ppc-admin-menu-submit"/> &nbsp;

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
            //   reload page for instant reflection if user is updating own role
            // -------------------------------------------------------------
            <?php if((int)$ppc_admin_menu_reload === 1){ ?>
                window.location = '<?php echo admin_url('admin.php?page=' . $this->ID . '-pp-admin-menus&role=' . $default_role . ''); ?>';
            <?php } ?>

            // -------------------------------------------------------------
            //   Set form action attribute to include role
            // -------------------------------------------------------------
            $('#ppc-admin-menu-form').attr('action', '<?php echo admin_url('admin.php?page=' . $this->ID . '-pp-admin-menus&role=' . $default_role . ''); ?>');

            // -------------------------------------------------------------
            //   Instant restricted item class
            // -------------------------------------------------------------
            $(document).on('change', '.pp-capability-menus-wrapper .ppc-menu-row .check-item', function () {

                if ($(this).is(':checked')) {
                    //add class if value is checked
                    $(this).parent().parent().find('.menu-item-link').addClass('restricted');

                    //toggle all checkbox
                    if ($(this).hasClass('check-all-menu-item')) {
                        $("input[type='checkbox'][name='pp_cababilities_disabled_menu[]']").prop('checked', true);
                        $("input[type='checkbox'][name='pp_cababilities_disabled_child_menu[]']").prop('checked', true);
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
                        $("input[type='checkbox'][name='pp_cababilities_disabled_menu[]']").prop('checked', false);
                        $("input[type='checkbox'][name='pp_cababilities_disabled_child_menu[]']").prop('checked', false);
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
            $(document).on('change', '.pp-capability-menus-wrapper .ppc-admin-menu-role', function () {

                //disable select
                $('.pp-capability-menus-wrapper .ppc-admin-menu-role').attr('disabled', true);

                //hide button
                $('.pp-capability-menus-wrapper .ppc-admin-menu-submit').hide();

                //show loading
                $('#pp-capability-menu-wrapper').html('<img src="<?php echo $capsman->mod_url; ?>/images/loader-black.gif" alt="loading...">');

                //go to url
                window.location = '<?php echo admin_url('admin.php?page=' . $this->ID . '-pp-admin-menus&role='); ?>' + $(this).val() + '';

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
