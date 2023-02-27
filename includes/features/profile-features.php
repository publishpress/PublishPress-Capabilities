<?php
/**
 * Capability Manager Profile Features.
 * Hide and block selected Profile Features like toolbar, dashboard widgets etc per-role.
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

require_once(dirname(CME_FILE) . '/includes/features/restrict-profile-features.php');

global $capsman;

$roles             = $capsman->roles;
$default_role      = $capsman->get_last_role();

$disabled_profile_items = !empty(get_option('capsman_disabled_profile_features')) ? (array)get_option('capsman_disabled_profile_features') : [];
$disabled_profile_items = array_key_exists($default_role, $disabled_profile_items) ? (array)$disabled_profile_items[$default_role] : [];

$profile_features_elements = \PublishPress\Capabilities\PP_Capabilities_Profile_Features::elementsLayout();
$profile_features_elements = isset($profile_features_elements[$default_role]) ? $profile_features_elements[$default_role] : [];

$refresh_url = admin_url('admin.php?page=pp-capabilities-profile-features&refresh_element=1');
?>

    <div class="wrap publishpress-caps-manage pressshack-admin-wrapper pp-capability-menus-wrapper profile-features <?php echo (empty($profile_features_elements) ? 'empty-elements' : ''); ?>">
        <div id="icon-capsman-admin" class="icon32"></div>
        <h2><?php esc_html_e('Profile Features Restrictions', 'capsman-enhanced'); ?></h2>

        <form method="post" id="ppc-profile-features-form" action="admin.php?page=pp-capabilities-profile-features">
            <?php wp_nonce_field('pp-capabilities-profile-features'); ?>

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
                                                <input type="submit" name="profile-features-submit"
                                                    value="<?php esc_attr_e('Save Changes', 'capsman-enhanced') ?>"
                                                    class="button-primary ppc-profile-features-submit" />
                                            </div>

                                            <select name="ppc-profile-features-role" class="ppc-profile-features-role">
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
                                                        <?php if (empty($profile_features_elements)) : ?>
                                                            <tr class="ppc-menu-row parent-menu empty-features-element">
                                                                <td colspan="2">
                                                                    <?php
                                                                    esc_html_e('There are no users in this role. To modify features on the "Profile" screen, please select a role with users.', 'capsman-enhanced');
                                                                    ?>
                                                                </td>
                                                            </tr>
                                                        <?php else : ?>
                                                            <?php
                                                            $sn = 0;
                                                            foreach ($profile_features_elements as $section_id => $section_array) :
                                                                $sn++;
                                                                $item_name      = $section_array['label'];
                                                                $restrict_value = $section_array['elements'];
                                                                $element_type   = $section_array['element_type'];

                                                                if (in_array($element_type, ['section', 'header'])) :
                                                                ?>
                                                                <tr class="ppc-menu-row parent-menu ppc-sortable-row <?php echo esc_attr($section_id); ?>"
                                                                    data-element_key="<?php echo esc_attr($section_id); ?>"
                                                                >
                                                                    <td class="restrict-column ppc-menu-checkbox">
                                                                        <input
                                                                            id="check-item-<?php echo (int) $sn; ?>"
                                                                            class="check-item" type="checkbox"
                                                                            name="capsman_disabled_profile_features[]"
                                                                            value="<?php echo esc_attr($restrict_value); ?>"
                                                                            <?php echo (in_array($restrict_value, $disabled_profile_items)) ? 'checked' : ''; ?>/>
                                                                    </td>
                                                                    <td class="menu-column ppc-menu-item">
                                                                        <label for="check-item-<?php echo (int) $sn; ?>">
                                                                            <strong class="menu-item-link<?php echo (in_array($restrict_value, $disabled_profile_items)) ? ' restricted' : ''; ?>">
                                                                                <span class="dashicons dashicons-portfolio"></span>
                                                                                <?php echo esc_html($item_name); ?>
                                                                            </strong>
                                                                        </label>
                                                                    </td>
                                                                </tr>
                                                                <?php else : ?>
                                                                <tr class="ppc-menu-row child-menu ppc-sortable-row <?php echo esc_attr($section_id); ?>"
                                                                    data-element_key="<?php echo esc_attr($section_id); ?>"
                                                                >
                                                                    <td class="restrict-column ppc-menu-checkbox">
                                                                        <input
                                                                            id="check-item-<?php echo (int) $sn; ?>"
                                                                            class="check-item" type="checkbox"
                                                                            name="capsman_disabled_profile_features[]"
                                                                            value="<?php echo esc_attr($restrict_value); ?>"
                                                                            <?php echo (in_array($restrict_value, $disabled_profile_items)) ? 'checked' : ''; ?>/>
                                                                    </td>
                                                                    <td class="menu-column ppc-menu-item">
                                                                        <label for="check-item-<?php echo (int) $sn; ?>">
                                                                            <span
                                                                                class="menu-item-link<?php echo (in_array($restrict_value, $disabled_profile_items)) ? ' restricted' : ''; ?>">
                                                                            <strong>
                                                                                 <?php echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &mdash;'; ?> 
                                                                                <?php echo esc_html($item_name); ?>
                                                                            </strong></span>
                                                                        </label>
                                                                    </td>
                                                                </tr>
                                                                <?php
                                                                endif;// $element_type check
                                                            endforeach; // $profile_features_elements loop
                                                        endif; // $profile_features_elements empty if
                                                	    ?>
		                                                <?php do_action('pp_capabilities_profile_features_after_table_tr'); ?>
		                                                </tbody>
		                                            </table>
		                                            <?php do_action('pp_capabilities_profile_features_after_table'); ?>
		                                        </div>

                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="capsman_profile_features_elements_order" class="capsman_profile_features_elements_order" value=""/>
                                    <input type="submit" name="profile-features-submit"
                                           value="<?php esc_attr_e('Save Changes', 'capsman-enhanced') ?>"
                                           class="button-primary ppc-profile-features-submit"/>
                                </td>
                            </tr>
                        </table>

                    </fieldset>
                </div><!-- .pp-column-left -->
                <div class="pp-column-right">
                <?php 
                $banners = new PublishPress\WordPressBanners\BannersMain; 
                ?>
                <?php 
                $banner_messages = ['<p>'];
                $banner_messages[] = '<i class="dashicons dashicons-arrow-right"></i> <a href="'. $refresh_url .'">' . esc_html__('Refresh profile items.', 'capsman-enhanced') .'</a>';
                $banner_messages[] = '</p>';
                $banners->pp_display_banner(
                    '',
                    __('Update Profile Features', 'capsman-enhanced'),
                    $banner_messages,
                    '',
                    '',
                    '',
                    ''
                );
                ?>
                <?php 
                $banner_messages = ['<p>'];
                $banner_messages[] = sprintf(esc_html__('%1$s = No change', 'capsman-enhanced'), '<input type="checkbox" title="'. esc_attr__('usage key', 'capsman-enhanced') .'" disabled>');
                $banner_messages[] = sprintf(esc_html__('%1$s = This feature is denied', 'capsman-enhanced'), '<input type="checkbox" title="'. esc_attr__('usage key', 'capsman-enhanced') .'" checked disabled>');
                $banner_messages[] = '</p>';
                $banners->pp_display_banner(
                    '',
                    __('How to use Profile Features', 'capsman-enhanced'),
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
            jQuery(document).ready(function($) {

                // -------------------------------------------------------------
                //   Set form action attribute to include role
                // -------------------------------------------------------------
                $('#ppc-profile-features-form').attr('action', '<?php echo esc_url_raw(admin_url('admin.php?page=pp-capabilities-profile-features&role=' . $default_role . '')); ?>')

                // -------------------------------------------------------------
                //   Instant restricted item class
                // -------------------------------------------------------------
                $(document).on('change', '.pp-capability-menus-wrapper .ppc-menu-row .check-item', function() {

                    if ($(this).is(':checked')) {
                        //add class if value is checked
                        $(this).closest('tr').find('.menu-item-link').addClass('restricted')

                        //toggle all checkbox
                        if ($(this).hasClass('check-all-menu-item')) {
                            $("input[type='checkbox'][name='capsman_disabled_profile_features[]']").prop('checked', true)
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
                            $("input[type='checkbox'][name='capsman_disabled_profile_features[]']").prop('checked', false)
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
                $(document).on('change', '.pp-capability-menus-wrapper .ppc-profile-features-role', function() {

                    //disable select
                    $('.pp-capability-menus-wrapper .ppc-profile-features-role').attr('disabled', true)

                    //hide button
                    $('.pp-capability-menus-wrapper .ppc-profile-features-submit').hide()

                    //show loading
                    $('#pp-capability-menu-wrapper').hide()
                    $('div.publishpress-caps-manage img.loading').show()

                    //go to url
                    window.location = '<?php echo esc_url_raw(admin_url('admin.php?page=pp-capabilities-profile-features&role=')); ?>' + $(this).val() + ''

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
