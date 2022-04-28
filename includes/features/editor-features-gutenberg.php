<h3 class="editor-features-gutenberg-show" <?php if (!empty($_REQUEST['ppc-tab']) && ('gutenberg' !== $_REQUEST['ppc-tab'])) echo 'style="display:none;"';?> data-post_type="<?php echo esc_attr($type_obj->name); ?>"><?php echo sprintf( esc_html__('Gutenberg Editor %s Restriction', 'capsman-enhanced'), esc_html__($type_obj->labels->singular_name)); ; ?></h3>
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
        ?>
        <tr class="ppc-menu-row parent-menu">
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

        ?>
        <tr class="ppc-menu-row parent-menu">
            <td class="menu-column ppc-menu-item">
                <span class="gutenberg menu-item-link<?php echo (in_array($feature_slug, $gutenberg_post_disabled[$type_obj->name])) ? ' restricted' : ''; ?>">
                <strong><i class="dashicons dashicons-arrow-right"></i>
                    <?php 
                    if(isset($arr_feature['custom_element']) && ($arr_feature['custom_element'] === true)){
                        echo esc_html($arr_feature['element_label']) . ' <small class="entry">(' . esc_html($arr_feature['element_items']). ')</small> &nbsp; ' 
                        . '<span class="' . esc_attr($arr_feature['button_class'])  . '" data-id="' . esc_attr($arr_feature['button_data_id'])  . '" data-parent="' . esc_attr($arr_feature['button_data_parent'])  . '"><small>(' . esc_html__('Delete', 'capsman-enhanced') . ')</small></span>';
                    }else{
                        echo esc_html($arr_feature['label']);
                    }
                    ?>
                </strong></span>
            </td>

            <td class="restrict-column ppc-menu-checkbox">
                <input id="check-item-<?php echo esc_attr($type_obj->name) . '-' . esc_attr($feature_slug);?>" class="check-item" type="checkbox"
                    name="capsman_feature_restrict_<?php echo esc_attr($type_obj->name);?>[]"
                    value="<?php echo esc_attr($feature_slug);?>"<?php checked(in_array($feature_slug, $gutenberg_post_disabled[$type_obj->name]));?> />
            </td>
        </tr>
        <?php
        }
    }

    do_action('pp_capabilities_features_gutenberg_after_table_tr');
    ?>

    </tbody>
</table>

<?php
do_action('pp_capabilities_features_gutenberg_after_table');
