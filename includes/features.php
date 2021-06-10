<?php
/**
 * Capability Manager Edit Posts Permission.
 * Edit Posts permission and visibility per roles.
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
$role_caption = translate_user_role($roles[$default_role]);

$classic_editor = pp_capabilities_is_classic_editor_available();

$gutenberg_metaboxes = pp_cabapbility_post_gutenberg_metaboxes();
$gutenberg_post_disabled = !empty(get_option('capsman_feature_gutenberg_post_disabled')) ? get_option('capsman_feature_gutenberg_post_disabled') : [];
$gutenberg_post_disabled = array_key_exists($default_role, $gutenberg_post_disabled) ? (array)$gutenberg_post_disabled[$default_role] : [];

if ($classic_editor) {
    $classic_editor = pp_cabapbility_post_metaboxes();
    $ce_metaboxes = $classic_editor['metaboxes'];
    $ce_metaboxes_names = $classic_editor['metaboxes_names'];
    $ce_post_disabled = !empty(get_option('capsman_feature_ce_post_disabled')) ? get_option('capsman_feature_ce_post_disabled') : [];
    $ce_post_disabled = array_key_exists($default_role, $ce_post_disabled) ? (array)$ce_post_disabled[$default_role] : [];
}
?>

    <div class="wrap publishpress-caps-manage pressshack-admin-wrapper pp-capability-menus-wrapper">
        <div id="icon-capsman-admin" class="icon32"></div>
        <h2><?php _e('Edit Posts Permission', 'capsman-enhanced'); ?></h2>

        <form method="post" id="ppc-post-features-form"
              action="admin.php?page=<?php echo $this->ID ?>-pp-post-features">
            <?php wp_nonce_field('capsman-pp-post-features'); ?>

            <fieldset>
                <table id="akmin">
                    <tr>
                        <td class="content">

                            <dl>
                                <dt><?php printf(__('Edit Posts Permission for %s', 'capsman-enhanced'), $role_caption); ?></dt>

                                <dd>
                                    <div class="publishpress-headline">
                                    <span class="cme-subtext">
                                    <strong><?php _e('Note', 'capsman-enhanced'); ?>:</strong>
                                    <?php _e('This feature only remove the option for the restricted roles.', 'capsman-enhanced'); ?>
                                    </span>
                                    </div>
                                </dd>

                                <table width='100%' class="form-table">
                                    <tr>
                                        <td>
                                            <select name="ppc-post-features-role" class="ppc-post-features-role">
                                                <?php
                                                foreach ($roles as $role => $name) :
                                                    $name = translate_user_role($name);
                                                    ?>
                                                    <option value="<?php echo $role; ?>" <?php selected($default_role, $role); ?>> <?php echo $name; ?>
                                                        &nbsp;
                                                    </option>
                                                <?php
                                                endforeach;
                                                ?>
                                            </select> &nbsp;

                                        <td>
                                            <div style="float:right">
                                                <input type="submit" name="post-features-submit"
                                                       value="<?php _e('Save Changes', 'capsman-enhanced') ?>"
                                                       class="button-primary ppc-post-features-submit"/> &nbsp;
                                            </div>
                                        </td>
                                        </td>
                                    </tr>
                                </table>
                                <?php if ($classic_editor) { ?>
                                    <ul class="nav-tab-wrapper">
                                        <li class="post-features-tab gutenberg-tab nav-tab nav-tab-active"
                                            data-tab=".post-features-gutenberg"><a
                                                    href="#"><?php _e('Gutenberg', 'capsman-enhanced') ?></a></li>
                                        <li class="post-features-tab classic-tab nav-tab"
                                            data-tab=".post-features-classic"><a
                                                    href="#"><?php _e('Classic', 'capsman-enhanced') ?></a></li>
                                    </ul>
                                <?php } ?>

                                <div id="pp-capability-menu-wrapper" class="postbox">
                                    <div class="pp-capability-menus">

                                        <div class="pp-capability-menus-wrap">
                                            <div id="pp-capability-menus-general"
                                                 class="pp-capability-menus-content editable-role"
                                                 style="display: block;">
                                                <?php
                                                $sn = 0;
                                                include(dirname(CME_FILE) . '/includes/features-gutenberg.php');
                                                if ($classic_editor) {
                                                    include(dirname(CME_FILE) . '/includes/features-classic.php');
                                                }
                                                ?>

                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <input type="submit" name="post-features-submit"
                                       value="<?php _e('Save Changes', 'capsman-enhanced') ?>"
                                       class="button-primary ppc-post-features-submit"/> &nbsp;

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
                $('#ppc-post-features-form').attr('action', '<?php echo admin_url('admin.php?page=' . $this->ID . '-pp-post-features&role=' . $default_role . ''); ?>');

                // -------------------------------------------------------------
                //   Instant restricted item class
                // -------------------------------------------------------------
                $(document).on('change', '.pp-capability-menus-wrapper .ppc-menu-row .check-item', function () {
                    var current_tab;
                    <?php if($classic_editor){ ?>
                    if ($('.nav-tab-wrapper .classic-tab').hasClass('nav-tab-active')) {
                        current_tab = 'classic';
                    } else {
                        current_tab = 'gutenberg';
                    }
                    <?php }else{ ?>
                    current_tab = 'gutenberg';
                    <?php } ?>

                    if ($(this).is(':checked')) {
                        //add class if value is checked
                        $(this).parent().parent().find('.menu-item-link').addClass('restricted');

                        //toggle all checkbox
                        if ($(this).hasClass('check-all-menu-item')) {
                            if (current_tab == 'gutenberg') {
                                $("input[type='checkbox'][name='capsman_feature_gutenberg_post_disabled[]']").prop('checked', true);
                                $('.gutenberg.menu-item-link').addClass('restricted');
                            } else {
                                $("input[type='checkbox'][name='capsman_feature_ce_post_disabled[]']").prop('checked', true);
                                $('.classic.menu-item-link').addClass('restricted');
                            }
                        } else {
                            if (current_tab == 'gutenberg') {
                                $('.gutenberg.check-all-menu-link').removeClass('restricted');
                                $('.gutenberg.check-all-menu-item').prop('checked', false);
                            } else {
                                $('.classic.check-all-menu-link').removeClass('restricted');
                                $('.classic.check-all-menu-item').prop('checked', false);
                            }
                        }

                    } else {
                        //unchecked value
                        $(this).parent().parent().find('.menu-item-link').removeClass('restricted');

                        //toggle all checkbox
                        if ($(this).hasClass('check-all-menu-item')) {
                            if (current_tab == 'gutenberg') {
                                $("input[type='checkbox'][name='capsman_feature_gutenberg_post_disabled[]']").prop('checked', false);
                                $('.gutenberg.menu-item-link').removeClass('restricted');
                            } else {
                                $("input[type='checkbox'][name='capsman_feature_ce_post_disabled[]']").prop('checked', false);
                                $('.classic.menu-item-link').removeClass('restricted');
                            }
                        } else {
                            if (current_tab == 'gutenberg') {
                                $('.gutenberg.check-all-menu-link').removeClass('restricted');
                                $('.gutenberg.check-all-menu-item').prop('checked', false);
                            } else {
                                $('.classic.check-all-menu-link').removeClass('restricted');
                                $('.classic.check-all-menu-item').prop('checked', false);
                            }
                        }

                    }

                });

                // -------------------------------------------------------------
                //   Load selected roles menu
                // -------------------------------------------------------------
                $(document).on('change', '.pp-capability-menus-wrapper .ppc-post-features-role', function () {

                    //disable select
                    $('.pp-capability-menus-wrapper .ppc-post-features-role').attr('disabled', true);

                    //hide button
                    $('.pp-capability-menus-wrapper .ppc-post-features-submit').hide();

                    //show loading
                    $('#pp-capability-menu-wrapper').html('<img src="<?php echo $capsman->mod_url; ?>/images/loader-black.gif" alt="loading...">');

                    //go to url
                    window.location = '<?php echo admin_url('admin.php?page=' . $this->ID . '-pp-post-features&role='); ?>' + $(this).val() + '';

                });


                // -------------------------------------------------------------
                //   Post features tab
                // -------------------------------------------------------------
                $('.post-features-tab').click(function (e) {
                    e.preventDefault();
                    $('.post-features-tab').removeClass('nav-tab-active');
                    $(this).addClass('nav-tab-active');
                    $('.pp-capability-menus-select').hide();
                    $($(this).attr('data-tab')).show();
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
