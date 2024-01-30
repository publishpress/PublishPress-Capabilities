<h3 class="editor-features-gutenberg-show" <?php if (!empty($_REQUEST['ppc-tab']) && ('gutenberg' !== $_REQUEST['ppc-tab'])) echo 'style="display:none;"';?> data-post_type="<?php echo esc_attr($type_obj->name); ?>"><?php echo sprintf( esc_html__('Gutenberg Editor %s Restrictions', 'capability-manager-enhanced'), esc_html__($type_obj->labels->singular_name)); ; ?></h3>
<table class="wp-list-table widefat fixed striped pp-capability-menus-select editor-features-gutenberg" <?php if (!empty($_REQUEST['ppc-tab']) && ('gutenberg' != $_REQUEST['ppc-tab'])) echo 'style="display:none;"';?> data-post_type="<?php echo esc_attr($type_obj->name); ?>">
    <?php foreach(['thead', 'tfoot'] as $tag_name):?>
    <<?php echo esc_attr($tag_name);?>>
    <tr>
        <th class="menu-column"></th>

        <th class="restrict-column ppc-menu-row"> 
            <input class="check-item gutenberg check-all-menu-item" type="checkbox" data-pp_type="<?php echo esc_attr($type_obj->name);?>" />
        </th>
    </tr>
    </<?php echo esc_attr($tag_name);?>>
    <?php endforeach;?>

    <tbody>
    <?php
    foreach ($gutenberg_elements as $section_title => $arr) {
        $section_slug = strtolower(ppc_remove_non_alphanumeric_space_characters($section_title));
        //set empty feature value as true
        $empty_post_type_feature[$type_obj->name][$section_slug] = 1;
        ?>
        <tr class="ppc-menu-row parent-menu <?php echo esc_attr($type_obj->name); ?> <?php echo esc_attr($section_slug); ?>">
            <td colspan="2">
            <h4 class="ppc-menu-row-section"><?php echo esc_html($section_title);?></h4>
            <?php
            /**
	         * Add support for section description
             *
	         * @param array     $def_post_types          Post type.
	         * @param array     $gutenberg_elements      All gutenberg elements.
	         * @param array     $gutenberg_post_disabled All gutenberg disabled post type element.
             *
	         * @since 2.1.1
	         */
	        do_action( "pp_capabilities_feature_gutenberg_{$section_slug}_section", $def_post_types, $gutenberg_elements, $gutenberg_post_disabled );
            ?>
            </td>
        </tr>

        <?php
        foreach ($arr as $feature_slug => $arr_feature) {
            $feature_slug = esc_attr($feature_slug);

            //check if post type support feature
            if (isset($arr_feature['support_key'])) {
                if (isset($arr_feature['support_type']) && $arr_feature['support_type'] === 'taxonomy') {
                    if (!in_array($arr_feature['support_key'], get_object_taxonomies($type_obj->name))) {
                        continue;
                    }
                } elseif (isset($arr_feature['support_type']) && $arr_feature['support_type'] === 'metabox') {
                    if (!in_array($type_obj->name, $arr_feature['support_key'])) {
                        continue;
                    }
                } else {
                    /**
                     * Skip this element for post type if for some reason 
                     * global $_wp_post_type_features is empty or doesn't 
                     * contain data for current post type
                     */
                    if (empty($_wp_post_type_features) || !is_array($_wp_post_type_features) || !isset($_wp_post_type_features[$type_obj->name])) {
                        continue;
                    }
                    //skip this element if post type does not support feature
                    if (!post_type_supports($type_obj->name, $arr_feature['support_key'])) {
                        continue;
                    }
                }
            }

            //unset if it has feature support
            if (isset($empty_post_type_feature[$type_obj->name][$section_slug])) {
                /**
                 * add phpcs ignore due to false alarm 
                 * as the variable is defined in main page
                 */
                // phpcs:ignore WordPressVIPMinimum.Variables.VariableAnalysis.UndefinedUnsetVariable
                unset($empty_post_type_feature[$type_obj->name][$section_slug]);
            }
        ?>
        <?php if(isset($arr_feature['custom_element']) && ($arr_feature['custom_element'] === true)) : ?>
            <tr class="ppc-menu-row parent-menu">
                <td class="menu-column ppc-menu-item custom-item-row ppc-flex">
                    <div class="ppc-flex-item">
                        <div>
                            <span class="gutenberg menu-item-link<?php echo (in_array($feature_slug, $gutenberg_post_disabled[$type_obj->name])) ? ' restricted' : ''; ?>">
                                <strong><i class="dashicons dashicons-arrow-right"></i>
                                    <?php echo esc_html(wp_strip_all_tags($arr_feature['label'])); ?>
                                </strong>
                            </span>
                        </div>
                        <div class="custom-item-output">
                            <div class="custom-item-display">
                                <?php echo esc_html($arr_feature['element_items']); ?>
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
                                class="button <?php echo esc_attr($arr_feature['button_class']); ?> feature-red" 
                                data-parent="<?php echo esc_attr($arr_feature['button_data_parent']); ?>" 
                                data-id="<?php echo esc_attr($arr_feature['button_data_id']); ?>">
                                <?php esc_html_e('Delete'); ?>    
                            </div>
                        </div>
                    </div>
                </td>

                <td class="restrict-column ppc-menu-checkbox">
                    <input id="check-item-<?php echo esc_attr($type_obj->name) . '-' . esc_attr($feature_slug);?>" class="check-item" type="checkbox"
                        name="capsman_feature_restrict_<?php echo esc_attr($type_obj->name);?>[]"
                        value="<?php echo esc_attr($feature_slug);?>"<?php checked(in_array($feature_slug, $gutenberg_post_disabled[$type_obj->name]));?> />
                </td>
            </tr>
        <?php else : ?>
            <tr class="ppc-menu-row parent-menu">
                <td class="menu-column ppc-menu-item">
                    <span class="gutenberg menu-item-link<?php echo (in_array($feature_slug, $gutenberg_post_disabled[$type_obj->name])) ? ' restricted' : ''; ?>">
                    <strong><i class="dashicons dashicons-arrow-right"></i>
                        <?php echo esc_html(wp_strip_all_tags($arr_feature['label'])); ?>
                    </strong></span>
                </td>

                <td class="restrict-column ppc-menu-checkbox">
                    <input id="check-item-<?php echo esc_attr($type_obj->name) . '-' . esc_attr($feature_slug);?>" class="check-item" type="checkbox"
                        name="capsman_feature_restrict_<?php echo esc_attr($type_obj->name);?>[]"
                        value="<?php echo esc_attr($feature_slug);?>"<?php checked(in_array($feature_slug, $gutenberg_post_disabled[$type_obj->name]));?> />
                </td>
            </tr>
        <?php endif; ?>
        <?php
        }
        //add class to remove row list
        if (isset($empty_post_type_feature[$type_obj->name][$section_slug])) {
            if ($section_slug === 'metaboxes') {
                //we want to leave metabox header with message
                ?>
                <tr class="ppc-menu-row parent-menu">
                    <td class="menu-column ppc-menu-item" colspan="2">
                    <p class="cme-subtext">
                        <?php printf(
                            esc_html__(
                                'No metabox found for %1s. %2s Click here %3s to visit the %4s screen and refresh this page after to load new metabox',
                                'capability-manager-enhanced'
                            ), 
                            esc_html($type_obj->labels->singular_name), 
                            '<a href="'. esc_url(admin_url('post-new.php?post_type='.$type_obj->name)) .'">', 
                            '</a>',
                            esc_html($type_obj->labels->singular_name)
                        ); ?>
                    </p>
                    </td>
                </tr>
                <?php
            } else {
                $empty_post_type_feature_class[] = '.editor-features-gutenberg .parent-menu.' . $type_obj->name . '.' . $section_slug . '';
            }
        }
    }

    do_action('pp_capabilities_features_gutenberg_after_table_tr');
    ?>

    </tbody>
</table>

<?php
do_action('pp_capabilities_features_gutenberg_after_table');
