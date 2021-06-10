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
?>

    <div class="wrap publishpress-caps-manage pressshack-admin-wrapper pp-capability-menus-wrapper">
        <div id="icon-capsman-admin" class="icon32"></div>
        <h2><?php _e('Editor Feature Restriction', 'capsman-enhanced'); ?></h2>

        <form method="post" id="ppc-editor-features-form"
              action="admin.php?page=pp-capabilities-editor-features">
            <?php wp_nonce_field('pp-capabilities-editor-features'); ?>

            <fieldset>
                <table id="akmin">
                    <tr>
                        <td class="content">

                                    <div class="publishpress-headline">
                                    <span class="cme-subtext">
	                                <span class='pp-capability-role-caption'>
	                                <?php
	                                _e('Select editor features to remove. Note that this screen cannot be used to grant additional features to any role.', 'capabilities-pro');
	                                ?>
	                                </span>
                                    </span>
                                    </div>

                            		<div class="publishpress-filters">
                                            <select name="ppc-editor-features-role" class="ppc-editor-features-role">
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

                                			<img class="loading" src="<?php echo $capsman->mod_url; ?>/images/wpspin_light.gif" style="display: none">

                                            <input type="submit" name="editor-features-submit"
                                				value="<?php _e('Save Changes', 'capabilities-pro') ?>"
                                				class="button-primary ppc-editor-features-submit" style="float:right" />

											<input type="hidden" name="ppc-tab" value="<?php echo (!empty($_REQUEST['ppc-tab'])) ? sanitize_key($_REQUEST['ppc-tab']) : 'gutenberg';?>" />
                            		</div>

									<script type="text/javascript">
		                            /* <![CDATA[ */
		                            jQuery(document).ready(function($) {
		                                $('li.gutenberg-tab').click(function() {
		                                    $('div.publishpress-filters input[name=ppc-tab]').val('gutenberg');
		                                });
		
		                                $('li.classic-tab').click(function() {
		                                    $('div.publishpress-filters input[name=ppc-tab]').val('classic');
		                                });
		                            });
		                            /* ]]> */
		                            </script>

                                <?php if ($classic_editor) { ?>
                                    <ul class="nav-tab-wrapper">
                                    <li class="editor-features-tab gutenberg-tab nav-tab <?php if (empty($_REQUEST['ppc-tab']) || ('gutenberg' == $_REQUEST['ppc-tab'])) echo 'nav-tab-active';?>"
                                            data-tab=".editor-features-gutenberg"><a
                                                    href="#"><?php _e('Gutenberg', 'capsman-enhanced') ?></a></li>
                                        
                                    <li class="editor-features-tab classic-tab nav-tab <?php if (!empty($_REQUEST['ppc-tab']) && ('classic' == $_REQUEST['ppc-tab'])) echo 'nav-tab-active';?>"
                                            data-tab=".editor-features-classic"><a 
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
                                            include(dirname(__FILE__) . '/editor-features-gutenberg.php');

                                                if ($classic_editor) {
                                                include(dirname(__FILE__) . '/editor-features-classic.php');
                                                }
                                                ?>

                                            </div>

                                        </div>
                                    </div>
                                </div>

                            <input type="submit" name="editor-features-submit"
                                       value="<?php _e('Save Changes', 'capsman-enhanced') ?>"
                                    class="button-primary ppc-editor-features-submit"/> &nbsp;


                        </td>
                    </tr>
                </table>

            </fieldset>

        </form>

        <style>
        span.menu-item-link {
            webkit-user-select: none; /* Safari */        
            -moz-user-select: none; /* Firefox */
            -ms-user-select: none; /* IE10+/Edge */
            user-select: none; /* Standard */
        }
        </style>

        <script type="text/javascript">
            /* <![CDATA[ */
            jQuery(document).ready(function ($) {

                // -------------------------------------------------------------
                //   Set form action attribute to include role
                // -------------------------------------------------------------
                $('#ppc-editor-features-form').attr('action', '<?php echo admin_url('admin.php?page=pp-capabilities-editor-features&role=' . $default_role . ''); ?>');

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
                $(document).on('change', '.pp-capability-menus-wrapper .ppc-editor-features-role', function () {

                    //disable select
                    $('.pp-capability-menus-wrapper .ppc-editor-features-role').attr('disabled', true);

                    //hide button
                    $('.pp-capability-menus-wrapper .ppc-editor-features-submit').hide();

                    //show loading
                    $('#pp-capability-menu-wrapper').html('<img src="<?php echo $capsman->mod_url; ?>/images/loader-black.gif" alt="">');

                    //go to url
                    window.location = '<?php echo admin_url('admin.php?page=pp-capabilities-editor-features&role='); ?>' + $(this).val() + '';

                });


                // -------------------------------------------------------------
                //   Editor features tab
                // -------------------------------------------------------------
                $('.editor-features-tab').click(function (e) {
                    e.preventDefault();
                    $('.editor-features-tab').removeClass('nav-tab-active');
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
