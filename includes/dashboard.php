<?php
/**
 * Capability Manager Dashboard.
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
?>

<div class="wrap publishpress-caps-manage pressshack-admin-wrapper pp-capability-menus-wrapper capabilities-dashboard">
    <h2>
        <?php esc_html_e('Dashboard', 'capsman-enhanced'); ?>
    </h2>

    <form method="post" id="ppc-capabilities-dashboard-form" action="admin.php?page=pp-capabilities-dashboard">
        <?php wp_nonce_field('pp-capabilities-dashboard'); ?>
        <div class="settings-submit">
            <input type="submit" name="dashboard-settings-submit" value="<?php esc_attr_e('Save Changes', 'capsman-enhanced') ?>" class="button-primary"/>
        </div>
        <div class="clear"></div>
        <div class="dashboard-settings-boxes">
            <?php foreach (pp_capabilities_dashboard_options() as $feature => $option) : ?>
                <div class="dashboard-settings-box">
                    <h3><?php echo esc_html($option['label']); ?></h3>
                    <div class="dashboard-settings-description"><?php echo esc_html($option['description']); ?></div>
                    <div class="dashboard-settings-control">
                        <div class="ppc-switch-button">
                            <label class="switch">
                                <input 
                                    type="checkbox" 
                                    name="capsman_dashboard_features[]" 
                                    value="<?php echo esc_attr($feature); ?>" 
                                    <?php checked(pp_capabilities_feature_enabled($feature), true); ?>
                                />
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="settings-submit">
            <input type="submit" name="dashboard-settings-submit" value="<?php esc_attr_e('Save Changes', 'capsman-enhanced') ?>" class="button-primary"/>
        </div>
        <div class="clear"></div>
    </form>
    <?php if (!defined('PUBLISHPRESS_CAPS_PRO_VERSION') || get_option('cme_display_branding')) {
        cme_publishpressFooter();
    }
    ?>
</div>
<?php
?>