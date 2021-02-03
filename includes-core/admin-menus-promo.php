<?php
/**
 * Capability Manager Admin Menu Permissions.
 * Hide and block selected Admin Menus per-role.
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
$capsman->generateNames();
$roles = $capsman->roles;
$default_role = $capsman->current;
?>

<style>
#pp-capability-menu-wrapper div.pp-capability-menus-promo {
    background-image: url("<?php echo plugin_dir_url(CME_FILE) . 'includes-core/pp-capabilities-admin-menus-promo-blur.png';?>");
}
</style>

<div class="wrap publishpress-caps-manage pressshack-admin-wrapper pp-capability-menus-wrapper-promo">
    <div id="icon-capsman-admin" class="icon32"></div>
    <h2><?php _e('Admin Menu Restrictions', 'capsman-enhanced'); ?></h2>

    <form method="post" id="ppc-admin-menu-form" action="admin.php?page=pp-capabilities-admin-menus">
        <fieldset>
            <table id="akmin">
                <tr>
                    <td class="content">

                        <div class="publishpress-headline">
                            <span class="cme-subtext">
                            <span class='pp-capability-role-caption'>
                            <?php
                            _e('Note: You are only restricting access to admin menu screens. Some plugins may also add features to other areas of WordPress.', 'capsman-enhanced');
                            ?>
                            </span>
                            </span>
                        </div>
                        <div class="publishpress-filters">
                            <select name="ppc-admin-menu-role" class="ppc-admin-menu-role">
                                <?php
                                foreach ($roles as $role => $name) :
                                    $name = translate_user_role($name);
                                    ?>
                                    <option value="<?php echo $role;?>" <?php selected($default_role, $role);?>><?php echo $name;?></option>
                                <?php
                                endforeach;
                                ?>
                            </select> &nbsp;
                            <input type="submit" name="admin-menu-submit"
                                value="<?php _e('Save Changes', 'capsman-enhanced') ?>"
                                class="button-primary ppc-admin-menu-submit" style="float:right" />
                        </div>
                        <div id="pp-capability-menu-wrapper" class="postbox">
                            <div class="pp-capability-menus-promo"> 
                            </div>
                        </div>
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
                window.location = '<?php echo admin_url('admin.php?page=pp-capabilities-admin-menus&role=' . $default_role . ''); ?>';
            <?php } ?>

            // -------------------------------------------------------------
            //   Set form action attribute to include role
            // -------------------------------------------------------------
            $('#ppc-admin-menu-form').attr('action', '<?php echo admin_url('admin.php?page=pp-capabilities-admin-menus&role=' . $default_role . ''); ?>');

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
                window.location = '<?php echo admin_url('admin.php?page=pp-capabilities-admin-menus&role='); ?>' + $(this).val() + '';

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
