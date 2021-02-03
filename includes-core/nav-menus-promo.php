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

global $capsman, $menu, $submenu;
$capsman->generateNames();
$roles = $capsman->roles;
$default_role = $capsman->current;
?>

<style>
#pp-capability-menu-wrapper div.pp-capability-menus-promo-nav {
    background-image: url("<?php echo plugin_dir_url(CME_FILE) . 'includes-core/pp-capabilities-nav-menus-promo-blur.png';?>");
}
</style>

    <div class="wrap publishpress-caps-manage pressshack-admin-wrapper pp-capability-menus-wrapper-promo">
        <div id="icon-capsman-admin" class="icon32"></div>
        <h2><?php _e('Navigation Menu Restrictions', 'capsman-enhanced'); ?></h2>

        <form method="post" id="ppc-nav-menu-form" action="admin.php?page=pp-capabilities-nav-menus">
            <fieldset>
                <table id="akmin">
                    <tr>
                        <td class="content">

                            <div class="publishpress-filters">
                                <select name="ppc-nav-menu-role" class="ppc-nav-menu-role">
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
                                <input type="submit" name="nav-menu-submit"
                                       value="<?php _e('Save Changes', 'capsman-enhanced') ?>"
                                       class="button-primary ppc-nav-menu-submit" style="float:right" />
                            </div>

                            <div id="pp-capability-menu-wrapper" class="postbox">
                                <div class="pp-capability-menus-promo-nav">
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>

            </fieldset>

        </form>

        <?php if (!defined('PUBLISHPRESS_CAPS_PRO_VERSION') || get_option('cme_display_branding')) {
            cme_publishpressFooter();
        }
        ?>
    </div>
<?php