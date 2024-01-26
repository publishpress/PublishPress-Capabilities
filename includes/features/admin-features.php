<?php
/**
 * Capability Manager Admin Features.
 * Hide and block selected Admin Features like toolbar, dashboard widgets etc per-role.
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

require_once(dirname(CME_FILE) . '/includes/features/restrict-admin-features.php');

global $capsman;

$roles        = $capsman->roles;
$default_role = $capsman->get_last_role();


$disabled_admin_items = !empty(get_option('capsman_disabled_admin_features')) ? (array)get_option('capsman_disabled_admin_features') : [];
$disabled_admin_items = array_key_exists($default_role, $disabled_admin_items) ? (array)$disabled_admin_items[$default_role] : [];

$admin_features_elements = PP_Capabilities_Admin_Features::elementsLayout();
?>

    <div class="wrap publishpress-caps-manage pressshack-admin-wrapper pp-capability-menus-wrapper admin-features">
        <div id="icon-capsman-admin" class="icon32"></div>
        <h2><?php esc_html_e('Admin Feature Restrictions', 'capability-manager-enhanced'); ?></h2>

        <form method="post" id="ppc-admin-features-form" action="admin.php?page=pp-capabilities-admin-features">
            <?php wp_nonce_field('pp-capabilities-admin-features'); ?>

            <div class="pp-columns-wrapper pp-enable-sidebar clear">
                <div class="pp-column-left">
                    <fieldset>
                        <table id="akmin">
                            <tr>
                                <td class="content">
                                    <div>

                                        <p>
                                        <div class="publishpress-filters">
                                            <div class="pp-capabilities-submit-top" style="float:right;">
                                                <input type="submit" name="admin-features-submit"
                                                    value="<?php esc_attr_e('Save Changes');?>"
                                                    class="button-primary ppc-admin-features-submit" />
                                            </div>

                                            <select name="ppc-admin-features-role" class="ppc-admin-features-role">
                                                <?php
                                                foreach ($roles as $role_name => $name) :
                                                    $name = translate_user_role($name);
                                                    ?>
                                                    <option value="<?php echo esc_attr($role_name); ?>" <?php selected($default_role,
                                                        $role_name); ?>><?php echo esc_html($name); ?></option>
                                                <?php
                                                endforeach;
                                                ?>
                                            </select> &nbsp;

                                            <img class="loading" src="<?php echo esc_url_raw($capsman->mod_url); ?>/images/wpspin_light.gif"
                                                    style="display: none">
                                        </div>
                                        </p>
                                    </div>
                                    <div id="pp-capability-menu-wrapper" class="postbox">
                                        <div class="pp-capability-menus">
	
		                                    <div class="pp-capability-menus-wrap">
		                                        <div id="pp-capability-menus-general"
		                                             class="pp-capability-menus-content editable-role" style="display: block;">
	
		                                            <table
		                                                class="wp-list-table widefat striped pp-capability-menus-select">
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
                                                                    <?php esc_html_e('Toggle all', 'capability-manager-enhanced'); ?>
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
                                                                        <?php esc_html_e('Toggle all', 'capability-manager-enhanced'); ?>
                                                                    </strong>
                                                                    </span>
                                                                    </label>
                                                                </td>

                                                            </tr>
                                                        </tfoot>

                                                        <tbody>

                                                        <?php
                                                        $icon_list = (array)PP_Capabilities_Admin_Features::elementLayoutItemIcons();

                                                        $sn = 0;
                                                        foreach ($admin_features_elements as $section_title => $section_elements) :
                                                            $sn++;
                                                            $section_slug = strtolower(ppc_remove_non_alphanumeric_space_characters($section_title));
                                                            $icon_name    = isset($icon_list[$section_slug]) ? $icon_list[$section_slug] : '&mdash;';
                                                            ?>

                                                            <tr class="ppc-menu-row parent-menu <?php echo esc_attr($section_slug); ?>">
		                                                        <?php if ($section_slug === 'admintoolbar') :
		                                                            $restrict_value = 'ppc_adminbar||admintoolbar';
		                                                       	?>
		                                                        <td class="features-section-header restrict-column ppc-menu-checkbox" style="text-align: left;" colspan="2">
		                                                            <input
		                                                                    id="check-item-<?php echo (int) $sn; ?>"
		                                                                    class="check-item" type="checkbox"
		                                                                    name="capsman_disabled_admin_features[]"
		                                                                    value="<?php echo esc_attr($restrict_value); ?>"
		                                                                    <?php echo (in_array($restrict_value, $disabled_admin_items)) ? 'checked' : ''; ?>/>
		                                                                    <label for="check-item-<?php echo (int) $sn; ?>">
		                                                            <strong class="menu-column ppc-menu-item menu-item-link<?php echo (in_array($restrict_value,
		                                                                            $disabled_admin_items)) ? ' restricted' : ''; ?>">
		                                                                <i class="dashicons dashicons-<?php echo esc_attr($icon_name) ?>"></i> <?php echo esc_html($section_title); ?>
		                                                            </strong>
		                                                        </label>
		                                                        </td>
		                                                        <?php else : ?>
		                                                                <td class="features-section-header" colspan="2">
		                                                                    <strong><i
		                                                                            class="dashicons dashicons-<?php echo esc_attr($icon_name) ?>"></i> <?php echo esc_html($section_title); ?>
		                                                                    </strong>
		                                                                </td>
		                                                        <?php endif; ?>

                                                            </tr>
                                                            <?php do_action("pp_capabilities_admin_features_{$section_slug}_before_subsection_tr"); ?>
                                                            <?php
                                                            foreach ($section_elements as $section_id => $section_array) :
                                                                $sn++;
                                                                if (!$section_id) {
                                                                    continue;
                                                                }
                                                                $item_name      = $section_array['label'];
                                                                $item_action    = $section_array['action'];
                                                                $restrict_value = $item_action.'||'.$section_id;
                                                                if($item_action === 'ppc_dashboard_widget'){
                                                                    $restrict_value .= '||'.$section_array['context'];
                                                                }
                                                                ?>

                                                                <tr class="ppc-menu-row child-menu <?php echo esc_attr($section_slug); ?>">

                                                                    <td class="restrict-column ppc-menu-checkbox">
                                                                        <input
                                                                            id="check-item-<?php echo (int) $sn; ?>"
                                                                            class="check-item" type="checkbox"
                                                                            name="capsman_disabled_admin_features[]"
                                                                            value="<?php echo esc_attr($restrict_value); ?>"
                                                                            <?php echo (in_array($restrict_value, $disabled_admin_items)) ? 'checked' : ''; ?>/>
                                                                    </td>
                                                                    <?php if (isset($section_array['custom_element']) && ($section_array['custom_element'] === true)) : ?>
                                                                        <td class="menu-column ppc-menu-item custom-item-row ppc-flex">
                                                                            <div class="ppc-flex-item">
                                                                                <div>
                                                                                    <label for="check-item-<?php echo (int) $sn; ?>">
                                                                                        <span
                                                                                            class="menu-item-link<?php echo (in_array($restrict_value,
                                                                                                $disabled_admin_items)) ? ' restricted' : ''; ?>">
                                                                                        <strong>
                                                                                            <?php
                                                                                            if ((isset($section_array['step']) && $section_array['step'] > 0) && isset($section_array['parent']) && !empty($section_array['parent'])) {
                                                                                                $step_margin = $section_array['step'] * 20;
                                                                                                echo '<span style="margin-left: ' . (int) $step_margin . 'px;"></span>';
                                                                                        echo ' &mdash; ';
                                                                                            } else {
                                                                                                if (isset($icon_list[$section_id])) {
                                                                                                    echo '<i class="dashicons dashicons-' . esc_attr($icon_list[$section_id]) . '"></i>';
                                                                                                } else {
                                                                                                    echo '&mdash;';
                                                                                                }
                                                                                            }
                                                                                            ?>
                                                                                            <?php echo esc_html($section_array['element_label']); ?>
                                                                                        </strong></span>
                                                                                    </label>
                                                                                </div>
                                                                                <div class="custom-item-output">
                                                                                    <div class="custom-item-display">
                                                                                        <?php echo esc_html($section_array['element_items']); ?>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="ppc-flex-item">
                                                                                <div class="button view-custom-item"><?php esc_html_e('View'); ?></div>
                                                                                    <?php /*<div class="button edit-custom-item" 
                                                                                        data-section="<?php echo esc_attr($section_slug); ?>"
                                                                                        data-label="<?php echo esc_attr($section_array['label']); ?>"
                                                                                        data-selector="<?php echo esc_attr($element_selector); ?>"
                                                                                        data-bodyclass="<?php echo esc_attr($element_bodyclass); ?>"
                                                                                        data-pages="<?php echo esc_attr(join(', ', (array) $section_array['pages'])); ?>"
                                                                                        data-post-types="<?php echo esc_attr(join(', ', (array) $section_array['post_types'])); ?>"
                                                                                        data-id="<?php echo esc_attr($section_id); ?>">
                                                                                        <?php esc_html_e('Edit', 'capability-manager-enhanced'); ?>
                                                                                    </div> <?php */ ?>
                                                                                    <div 
                                                                                        class="button <?php echo esc_attr($section_array['button_class']); ?> feature-red"
                                                                                        data-id="<?php echo esc_attr($section_array['button_data_id']); ?>">
                                                                                        <?php esc_html_e('Delete'); ?>    
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    <?php else : ?>
                                                                        <td class="menu-column ppc-menu-item">

                                                                            <label for="check-item-<?php echo (int) $sn; ?>">
                                                                                <span
                                                                                    class="menu-item-link<?php echo (in_array($restrict_value,
                                                                                        $disabled_admin_items)) ? ' restricted' : ''; ?>">
                                                                                <strong>
                                                                                    <?php
                                                                                    if ((isset($section_array['step']) && $section_array['step'] > 0) && isset($section_array['parent']) && !empty($section_array['parent'])) {
                                                                                        $step_margin = $section_array['step'] * 20;
                                                                                        echo '<span style="margin-left: ' . (int) $step_margin . 'px;"></span>';
                                                                                echo ' &mdash; ';
                                                                                    } else {
                                                                                        if (isset($icon_list[$section_id])) {
                                                                                            echo '<i class="dashicons dashicons-' . esc_attr($icon_list[$section_id]) . '"></i>';
                                                                                        } else {
                                                                                            echo '&mdash;';
                                                                                        }
                                                                                    }
                                                                                    ?>
                                                                                    <?php echo esc_html($item_name); ?>
                                                                                </strong></span>
                                                                            </label>
                                                                        </td>
                                                                    <?php endif; ?>

                                                                </tr>

                                                                <?php
                                                            endforeach; // $section_elements subsection loop
                                                        endforeach; // $admin_features_elements section loop
                                                		?>
		                                                <?php do_action('pp_capabilities_admin_features_after_table_tr'); ?>
		                                                </tbody>
		                                            </table>
		                                            <?php do_action('pp_capabilities_admin_features_after_table'); ?>
		                                        </div>

                                            </div>
                                        </div>
                                    </div>
                                    <input type="submit" name="admin-features-submit"
                                           value="<?php esc_attr_e('Save Changes');?>"
                                           class="button-primary ppc-admin-features-submit"/>
                                </td>
                            </tr>
                        </table>

                    </fieldset>
                </div><!-- .pp-column-left -->
                <div class="pp-column-right pp-capabilities-sidebar">
                <?php 
                $banner_messages = ['<p>'];
                $banner_messages[] = esc_html__('Admin Features allows you to remove elements from the admin area and toolbar.', 'capability-manager-enhanced');
                $banner_messages[] = '</p><p>';
                $banner_messages[] = sprintf(esc_html__('%1$s = No change', 'capability-manager-enhanced'), '<input type="checkbox" title="'. esc_attr__('usage key', 'capability-manager-enhanced') .'" disabled>') . ' <br />';
                $banner_messages[] = sprintf(esc_html__('%1$s = This feature is denied', 'capability-manager-enhanced'), '<input type="checkbox" title="'. esc_attr__('usage key', 'capability-manager-enhanced') .'" checked disabled>') . ' <br />';
                $banner_messages[] = '</p>';
                $banner_messages[] = '<p><a class="button ppc-checkboxes-documentation-link" href="https://publishpress.com/knowledge-base/admin-features-screen/"target="blank">' . esc_html__('View Documentation', 'capability-manager-enhanced') . '</a></p>';
                $banner_title  = __('How to use Admin Features', 'capability-manager-enhanced');
                pp_capabilities_sidebox_banner($banner_title, $banner_messages);
                // add promo sidebar
                pp_capabilities_pro_sidebox();
                ?>
                </div><!-- .pp-column-right -->
            </div><!-- .pp-columns-wrapper -->
        </form>

        <script type="text/javascript">
            /* <![CDATA[ */
            jQuery(document).ready(function($) {

                // -------------------------------------------------------------
                //   reload page for instant reflection if user is updating own role
                // -------------------------------------------------------------
                <?php if(!empty($ppc_page_reload) && (int)$ppc_page_reload === 1){ ?>
                window.location = '<?php echo esc_url_raw(admin_url('admin.php?page=pp-capabilities-admin-features&role=' . $default_role . '')); ?>'
                <?php } ?>

                // -------------------------------------------------------------
                //   Set form action attribute to include role
                // -------------------------------------------------------------
                $('#ppc-admin-features-form').attr('action', '<?php echo esc_url_raw(admin_url('admin.php?page=pp-capabilities-admin-features&role=' . $default_role . '')); ?>')

                // -------------------------------------------------------------
                //   Instant restricted item class
                // -------------------------------------------------------------
                $(document).on('change', '.pp-capability-menus-wrapper .ppc-menu-row .check-item', function() {

                    if ($(this).is(':checked')) {
                        //add class if value is checked
                        $(this).closest('tr').find('.menu-item-link').addClass('restricted')

                        //toggle all checkbox
                        if ($(this).hasClass('check-all-menu-item')) {
                            $("input[type='checkbox'][name='capsman_disabled_admin_features[]']").prop('checked', true)
                            $('.menu-item-link').addClass('restricted')
                        } else {
                            $('.check-all-menu-link').removeClass('restricted')
                            $('.check-all-menu-item').prop('checked', false)
                        }

                    } else {
                        //unchecked value
                        $(this).closest('tr').find('.menu-item-link').removeClass('restricted')

                        //toggle all checkbox
                        if ($(this).hasClass('check-all-menu-item')) {
                            $("input[type='checkbox'][name='capsman_disabled_admin_features[]']").prop('checked', false)
                            $('.menu-item-link').removeClass('restricted')
                        } else {
                            $('.check-all-menu-link').removeClass('restricted')
                            $('.check-all-menu-item').prop('checked', false)
                        }

                    }

                })

                // -------------------------------------------------------------
                //   Load selected roles menu
                // -------------------------------------------------------------
                $(document).on('change', '.pp-capability-menus-wrapper .ppc-admin-features-role', function() {

                    //disable select
                    $('.pp-capability-menus-wrapper .ppc-admin-features-role').attr('disabled', true)

                    //hide button
                    $('.pp-capability-menus-wrapper .ppc-admin-features-submit').hide()

                    //show loading
                    $('#pp-capability-menu-wrapper').hide()
                    $('div.publishpress-caps-manage img.loading').show()

                    //go to url
                    window.location = '<?php echo esc_url_raw(admin_url('admin.php?page=pp-capabilities-admin-features&role=')); ?>' + $(this).val() + ''

                })

                // -------------------------------------------------------------
                //   Admin Toolbar Check
                // -------------------------------------------------------------
                $(document).on('change', '.pp-capability-menus-wrapper .ppc-menu-row.parent-menu.admintoolbar .check-item', function() {

                    if ($(this).is(':checked')) {
                        //add class if value is checked
                        $('.ppc-menu-row.child-menu.admintoolbar').find('.menu-item-link').addClass('restricted')

                        //toggle all checkbox
                        $('.ppc-menu-row.child-menu.admintoolbar').find("input[type='checkbox'][name='capsman_disabled_admin_features[]']").prop('checked', true)
                        $('.ppc-menu-row.child-menu.admintoolbar').find('.menu-item-link').addClass('restricted')

                    } else {
                        //unchecked value
                        $('.ppc-menu-row.child-menu.admintoolbar').find('.menu-item-link').removeClass('restricted')

                        //toggle all checkbox
                        $('.ppc-menu-row.child-menu.admintoolbar').find("input[type='checkbox'][name='capsman_disabled_admin_features[]']").prop('checked', false)
                        $('.ppc-menu-row.child-menu.admintoolbar').find('.menu-item-link').removeClass('restricted')

                    }

                })
            })
            /* ]]> */
        </script>

        <?php if (!defined('PUBLISHPRESS_CAPS_PRO_VERSION') || get_option('cme_display_branding')) {
            cme_publishpressFooter();
        }
        ?>
    </div>
<?php
