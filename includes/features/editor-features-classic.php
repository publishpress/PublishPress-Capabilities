<?php
$ce_elements = PP_Capabilities_Post_Features::elementsLayoutClassic();

$ce_post_disabled = [];

$def_post_types = array_unique(apply_filters('pp_capabilities_feature_post_types', ['post', 'page']));

asort($def_post_types);

if (count($def_post_types) > 6) {
    ?>
    <style type="text/css">
    .pp-columns-wrapper.pp-enable-sidebar .pp-column-left {width: 100% !important;}
    .pp-columns-wrapper.pp-enable-sidebar .pp-column-left, .pp-columns-wrapper.pp-enable-sidebar .pp-column-right {float: none !important;}
    </style>
    <?php
}

foreach($def_post_types as $type_name) {
    $_disabled = get_option("capsman_feature_restrict_classic_{$type_name}", []);
    $ce_post_disabled[$type_name] = !empty($_disabled[$default_role]) ? (array)$_disabled[$default_role] : [];
}
?>

<table class="wp-list-table widefat fixed striped pp-capability-menus-select editor-features-classic" <?php if (empty($_REQUEST['ppc-tab']) || ('classic' != $_REQUEST['ppc-tab'])) echo 'style="display:none;"';?>>
    <?php foreach(['thead', 'tfoot'] as $tag_name):?>
    <<?php echo esc_attr($tag_name);?>>
    <tr>
        <th class="menu-column"><?php if ('thead' == $tag_name || !defined('PUBLISHPRESS_CAPS_PRO_VERSION')) {esc_html_e('Classic Editor Screen', 'capsman-enhanced');}?></th>

        <?php foreach($def_post_types as $type_name) :
            $type_obj = get_post_type_object($type_name);    
        ?>
            <th class="restrict-column ppc-menu-row"><?php printf(esc_html__('%s Restrict', 'capsman-enhanced'), esc_html($type_obj->labels->singular_name));?><br />
            <input class="check-item classic check-all-menu-item" type="checkbox" title="<?php esc_attr_e('Toggle all', 'capsman-enhanced');?>" data-pp_type="<?php echo esc_attr($type_name);?>" />
            </th>
        <?php endforeach;?>
    </tr>
    </<?php echo esc_attr($tag_name);?>>
    <?php endforeach;?>

    <tbody>
    <?php
    foreach ($ce_elements as $section_title => $arr) {
        $section_slug = strtolower(ppc_remove_non_alphanumeric_space_characters($section_title));
        ?>
        <tr class="ppc-menu-row parent-menu">
            <td colspan="<?php echo (count($def_post_types) + 1);?>">
            <h4 class="ppc-menu-row-section"><?php echo esc_html($section_title);?></h4>
            <?php
            /**
	         * Add support for section description
             *
	         * @param array     $def_post_types          Post type.
	         * @param array     $ce_elements      All classic editor elements.
	         * @param array     $ce_post_disabled All classic editor disabled post type element.
             *
	         * @since 2.1.1
	         */
	        do_action( "pp_capabilities_feature_classic_{$section_slug}_section", $def_post_types, $ce_elements, $ce_post_disabled );
            ?>
            </td>
        </tr>
        
        <?php
        foreach ($arr as $feature_slug => $arr_feature) {
            if (!$feature_slug) {
                continue;
            }
            ?>
            <tr class="ppc-menu-row parent-menu">
                <td class="menu-column ppc-menu-item">
                    <span class="classic menu-item-link<?php echo (in_array($feature_slug, $ce_post_disabled['post'])) ? ' restricted' : ''; ?>">
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

                <?php foreach($def_post_types as $type_name) :?>
                    <td class="restrict-column ppc-menu-checkbox">
                        <input id="cb_<?php echo esc_attr($type_name) . '-' . esc_attr(str_replace(['#', '.'], '_', $feature_slug));?>" class="check-item" type="checkbox"
                                name="capsman_feature_restrict_classic_<?php echo esc_attr($type_name);?>[]"
                                value="<?php echo esc_attr($feature_slug); ?>" <?php checked(in_array($feature_slug, $ce_post_disabled[$type_name]));?> />
                    </td>
                <?php endforeach;?>
            </tr>
            <?php
        }
    }

    do_action('pp_capabilities_features_classic_after_table_tr');
    ?>

    </tbody>
</table>

<?php
do_action('pp_capabilities_features_classic_after_table');
