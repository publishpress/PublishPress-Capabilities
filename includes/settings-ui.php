<?php
/*
 * PublishPress Capabilities
 *
 * Plugin settings UI
 *
 */

class Capabilities_Settings_UI {
    public function __construct() {
        $this->settingsUI();
    }

    public function settingsUI() {
        $all_options        = pp_capabilities_settings_options();
        $first_table_display = defined('PUBLISHPRESS_CAPS_PRO_VERSION') ? 'display:none;' : '';
        ?>

        <ul id="publishpress-capability-settings-tabs" class="nav-tab-wrapper">
            <?php do_action('pp_capabilities_settings_before_menu_list'); ?>
            <li class="nav-tab"><a href="#ppcs-tab-capabilities"><?php esc_html_e('Capabilities', 'capsman-enhanced');?></a></li>
            <li class="nav-tab"><a href="#ppcs-tab-editor-features"><?php esc_html_e('Editor Features', 'capsman-enhanced');?></a></li>
            <?php do_action('pp_capabilities_settings_after_menu_list'); ?>
        </ul>

        <fieldset>
            <table id="akmin">
                <tr>
                    <td class="content">

                    <?php do_action('pp_capabilities_settings_before_menu_content'); ?>

                    <table class="form-table" role="presentation" id="ppcs-tab-capabilities" style="<?php echo esc_attr($first_table_display); ?>">
                        <tbody>

                        <tr>
                            <?php
                                $checked = checked(!empty(get_option('cme_capabilities_show_private_taxonomies', 0)), true, false);
                            ?>
                            <th scope="row"> <?php esc_html_e('Show Private Taxonomies', 'capsman-enhanced'); ?></th>
                            <td>
                                <label> 
                                <input type="checkbox" name="cme_capabilities_show_private_taxonomies" id="cme_capabilities_show_private_taxonomies" autocomplete="off" value="1" <?php echo esc_attr($checked);?>>
                                </label>
                                <br>
                            </td>
                        </tr>

                        <tr>
                            <?php

                            if (defined('PUBLISHPRESS_VERSION') && class_exists('PP_Custom_Status')):
                                $checked = checked(!empty(get_option('cme_custom_status_control')), true, false);
                            ?>
                            <th scope="row"><?php esc_html_e('Control Custom Statuses', 'capsman-enhanced'); ?></th>
                            <td>
                        <label for="" title="<?php esc_attr_e('Control selection of custom post statuses.', 'capsman-enhanced');?>"> 
                                <input type="checkbox" name="cme_custom_status_control" id="cme_custom_status_control" autocomplete="off" value="1" <?php echo esc_attr($checked);?>>
                                </label>
                                <br>
                            </td>
                            <?php endif;?>
                        </tr>
                        </tbody>
                    </table>

                    <table class="form-table" role="presentation" id="ppcs-tab-admin-menus" style="display:none;">
                        <tbody>
                        <tr>
                        <?php
                            $checked = checked(!empty(get_option('cme_admin_menus_restriction_priority', 1)), true, false);
                        ?>
                        <th scope="row"> <?php esc_html_e('Admin Menu Restrictions', 'capsman-enhanced'); ?></th>
                        <td>
                    <label for="" title="<?php esc_attr_e('Admin Menus: treatment of multiple roles', 'capsman-enhanced');?>"> 
                            <select name="cme_admin_menus_restriction_priority" id="cme_admin_menus_restriction_priority" autocomplete="off">
                            <option value="0"<?php echo (esc_attr($checked)) ? '' : ' selected';?>><?php esc_html_e('Any non-restricted user role allows access', 'capsman-enhanced');?></option>
                            <option value="1"<?php echo (esc_attr($checked)) ? ' selected' : '';?>><?php esc_html_e('Any restricted user role prevents access', 'capsman-enhanced');?></option>
                            </select>
                            <div class='cme-subtext'>
                            <?php esc_html_e('How are restrictions applied when a user has multiple roles?', 'capsman-enhanced');?>
                            </div>
                            </label>
                            <br>
                        </td>
                    </tr>
                        </tbody>
                    </table>

                    <table class="form-table" role="presentation" id="ppcs-tab-editor-features" style="display:none;">
                        <tbody>

                            <tr>
                            <?php
                                $checked = checked(!empty(get_option('cme_editor_features_private_post_type', 0)), true, false);
                            ?>
                            <th scope="row"> <?php esc_html_e('Support for Private Post Types', 'capsman-enhanced'); ?></th>
                            <td>
                                <label> 
                                <input type="checkbox" name="cme_editor_features_private_post_type" id="cme_editor_features_private_post_type" autocomplete="off" value="1" <?php echo esc_attr($checked);?>>
                                </label>
                                <br>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                    </td>
                </tr>
            </table>
        </fieldset>

        <script>
        jQuery(document).ready(function ($) {

            $('#publishpress-capability-settings-tabs').find('li').click(function (e) {
                e.preventDefault();
                $('#publishpress-capability-settings-tabs').children('li').filter('.nav-tab-active').removeClass('nav-tab-active');
                $(this).addClass('nav-tab-active');

                $('[id^="ppcs-"]').hide();
                $($(this).find('a').first().attr('href')).show();
            });

        });
        </script>

    <?php
        echo "<input type='hidden' name='all_options' value='" . implode(',', array_map('esc_attr', $all_options)) . "' />";
    }
} // end class
