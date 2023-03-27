<?php

namespace PublishPress\Capabilities;

class PP_Capabilities_Frontend_Features_UI
{
    private static $instance = null;

    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new PP_Capabilities_Frontend_Features_UI();
        }

        return self::$instance;
    }

    public function __construct()
    {
        if (is_admin()) {
            //load settings page scripts
            self::loadFeaturesAdminAssets();
            //add frontend features form
            add_action('pp_capabilities_frontend_features_frontendelements_before_subsection_tr', [$this, 'fontendElementsForm']);
            //add body class form
            add_action('pp_capabilities_frontend_features_bodyclass_before_subsection_tr', [$this, 'bodyClassForm']);
            //add custom styles form
            add_action('pp_capabilities_frontend_features_customstyles_before_subsection_tr', [$this, 'customStylesForm']);
        }
    }

    /**
     * Form element page options
     *
     * @return array
     */
    public static function getElementFormPageOptions()
    {
        $options = [
          'whole_site'    => esc_html__('Whole Site', 'capsman-enhanced'),
          'homepage'      => esc_html__('Homepage', 'capsman-enhanced'),
          'archive_pages' => esc_html__('Archive Pages', 'capsman-enhanced'),
          'single_pages'  => esc_html__('Single Pages', 'capsman-enhanced')
        ];
      
        return $options;
    }

    /**
     * Form element post type options
     *
     * @return array
     */
    public static function getElementFormPostTypesOptions()
    {
        $post_types = get_post_types(['public' => true], 'objects');
        unset($post_types['attachment']);

        $options = [];
        foreach ($post_types as $name => $post_type) {
            $options[$name] = $post_type->label;
        }
      
        return $options;
    }

    /**
     * Add frontend elements form
     *
     */
    public function fontendElementsForm()
    {
        ?>
        <tr class="ppc-menu-row child-menu frontendelements">
            <td colspan="2" class="form-td">
                <table class="frontend-features-form simple-form frontendelements-form">
                    <tr class="ppc-menu-row parent-menu">
                        <td colspan="2">
                            <p class="cme-subtext">
                                <?php esc_html_e('You can remove frontend elements by adding their IDs or classes below:', 'capsman-enhanced'); ?>
                            </p>
                            <p class="editing-custom-item">
                                <strong><?php esc_html_e('Editing:', 'capsman-enhanced'); ?></strong> 
                                <span class="title"></span>
                            </p>
                        </td>
                    </tr>

                    <tr class="field-row">
                        <th scope="row">
                            <?php esc_html_e('Label', 'capsman-enhanced'); ?>
                            <font color="red">*</font>
                        </th>
                        <td>
                            <input class="frontend-element-new-name frontent-form-field frontendelements-form-label" type="text" /><br />
                            <small>
                                <?php esc_html_e('This will only show here in the WordPress admin area.', 'capsman-enhanced'); ?>
                            </small>
                        </td>
                    </tr>

                    <tr class="field-row">
                        <th scope="row">
                            <?php esc_html_e('Element IDs or Classes', 'capsman-enhanced'); ?>
                            <font color="red">*</font>
                        </th>
                        <td>
                            <textarea class="frontend-element-new-element frontent-form-field frontendelements-form-element"></textarea><br />
                            <small>
                                <?php esc_html_e('IDs or classes to hide. Separate multiple values with a comma (.custom-item-one, .custom-item-two, #new-item-id).', 'capsman-enhanced'); ?>
                            </small>
                        </td>
                    </tr>

                    <tr class="field-row">
                        <th scope="row">
                            <?php esc_html_e('Load on page types:', 'capsman-enhanced'); ?>
                        </th>
                        <td>
                            <select class="frontend-element-new-element-pages chosen-cpt-select frontendelements-form-pages"
                                data-placeholder="<?php esc_attr_e('Select pages...', 'capsman-enhanced'); ?>"
                                multiple>
                                <?php foreach (self::getElementFormPageOptions() as $value => $label) : ?>
                                <option
                                    value="<?php echo esc_attr($value); ?>">
                                    <?php echo esc_html($label); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <br />
                            <small>
                                <?php esc_html_e('You can select page types where this element will be added.', 'capsman-enhanced'); ?>
                            </small>
                        </td>
                    </tr>

                    <tr class="field-row">
                        <th scope="row">
                            <?php esc_html_e('Add post metabox:', 'capsman-enhanced'); ?>
                        </th>
                        <td>
                            <select class="frontend-element-new-element-post-types chosen-cpt-select frontendelements-form-post-types"
                                data-placeholder="<?php esc_attr_e('Select post types...', 'capsman-enhanced'); ?>"
                                multiple>
                                <?php foreach (self::getElementFormPostTypesOptions() as $value => $label) : ?>
                                <option
                                    value="<?php echo esc_attr($value); ?>">
                                    <?php echo esc_html($label); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <br />
                            <small>
                                <?php esc_html_e('This will add a metabox on the post editing screen. You can use this feature to add body classes only for that post.', 'capsman-enhanced'); ?>
                            </small>
                        </td>
                    </tr>

                    <tr class="field-row">
                        <td colspan="2">
                            <input type="hidden" class="custom-edit-id" value="" />
                            <input class="frontend-element-form-nonce" type="hidden"
                                value="<?php echo esc_attr(wp_create_nonce('frontend-element-nonce')); ?>" />
                            <div class="custom-item-submit-buttons">
                                <div class="cancel-custom-item-edit button button-secondary"
                                    data-section="frontendelements">
                                    <?php esc_html_e('Cancel Edit', 'capsman-enhanced'); ?></div>

                                <button type="button" class="submit-button frontend-element-form-submit button button-secondary"
                                    data-required="<?php esc_attr_e('All fields are required.', 'capsman-enhanced'); ?>"
                                    data-add="<?php esc_attr_e('Add Frontend Element', 'capsman-enhanced'); ?>"
                                    data-edit="<?php esc_attr_e('Save Edit', 'capsman-enhanced'); ?>"
                                    data-section="frontendelements">
                                    <?php esc_html_e('Add Frontend Element', 'capsman-enhanced'); ?></button>
                            </div>
                            <span class="ppc-feature-post-loader spinner"></span>
                            <div class="ppc-post-features-note"></div>
                            </th>
                    </tr>

                </table>
            </td>
        </tr>
    <?php
    }

    /**
     * Add body class form
     *
     */
    public function bodyClassForm()
    {
        ?>
        <tr class="ppc-menu-row child-menu bodyclass">
            <td colspan="2" class="form-td">
                <table class="frontend-features-form simple-form bodyclass-form">
                    <tr class="ppc-menu-row parent-menu">
                        <td colspan="2">
                            <p class="cme-subtext">
                                <?php esc_html_e('You can add a page body class using the form below:', 'capsman-enhanced'); ?>
                            </p>
                            <p class="editing-custom-item">
                                <strong><?php esc_html_e('Editing:', 'capsman-enhanced'); ?></strong> 
                                <span class="title"></span>
                            </p>
                        </td>
                    </tr>

                    <tr class="field-row">
                        <th scope="row">
                            <?php esc_html_e('Label', 'capsman-enhanced'); ?>
                            <font color="red">*</font>
                        </th>
                        <td>
                            <input class="body-class-new-name frontent-form-field bodyclass-form-label" type="text" /><br />
                            <small>
                                <?php esc_html_e('This will only show here in the WordPress admin area.', 'capsman-enhanced'); ?>
                            </small>
                        </td>
                    </tr>

                    <tr class="field-row">
                        <th scope="row">
                            <?php esc_html_e('Classes', 'capsman-enhanced'); ?>
                            <font color="red">*</font>
                        </th>
                        <td>
                            <textarea class="body-class-new-element frontent-form-field bodyclass-form-element"></textarea><br />
                            <small>
                                <?php esc_html_e('Enter classes that should be added to the body HTML. Separate multiple values with a space (custom-item-one custom-item-two).', 'capsman-enhanced'); ?>
                            </small>
                        </td>
                    </tr>

                    <tr class="field-row">
                        <th scope="row">
                            <?php esc_html_e('Load on page types:', 'capsman-enhanced'); ?>
                        </th>
                        <td>
                            <select class="body-class-new-element-pages chosen-cpt-select bodyclass-form-pages"
                                data-placeholder="<?php esc_attr_e('Select pages...', 'capsman-enhanced'); ?>"
                                multiple>
                                <?php foreach (self::getElementFormPageOptions() as $value => $label) : ?>
                                <option
                                    value="<?php echo esc_attr($value); ?>">
                                    <?php echo esc_html($label); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <br />
                            <small>
                                <?php esc_html_e('You can select post or pages where this element should be added.', 'capsman-enhanced'); ?>
                            </small>
                        </td>
                    </tr>

                    <tr class="field-row">
                        <th scope="row">
                            <?php esc_html_e('Add post metabox:', 'capsman-enhanced'); ?>
                        </th>
                        <td>
                            <select class="body-class-new-element-post-types chosen-cpt-select bodyclass-form-post-types"
                                data-placeholder="<?php esc_attr_e('Select post types...', 'capsman-enhanced'); ?>"
                                multiple>
                                <?php foreach (self::getElementFormPostTypesOptions() as $value => $label) : ?>
                                <option
                                    value="<?php echo esc_attr($value); ?>">
                                    <?php echo esc_html($label); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <br />
                            <small>
                                <?php esc_html_e('This will add a metabox on the post editing screen. You can use this feature to add body classes only for that post.', 'capsman-enhanced'); ?>
                            </small>
                        </td>
                    </tr>

                    <tr class="field-row">
                        <td colspan="2">
                            <input type="hidden" class="custom-edit-id" value="" />
                            <input class="body-class-form-nonce" type="hidden"
                                value="<?php echo esc_attr(wp_create_nonce('bodyclass-nonce')); ?>" />
                                <div class="custom-item-submit-buttons">
                                    <div class="cancel-custom-item-edit button button-secondary"
                                        data-section="bodyclass">
                                        <?php esc_html_e('Cancel Edit', 'capsman-enhanced'); ?></div>
                                    <button type="button" class="submit-button body-class-form-submit button button-secondary"
                                        data-required="<?php esc_attr_e('All fields are required.', 'capsman-enhanced'); ?>"
                                        data-add="<?php esc_attr_e('Save Body Class', 'capsman-enhanced'); ?>"
                                        data-edit="<?php esc_attr_e('Save Edit', 'capsman-enhanced'); ?>"
                                        data-section="bodyclass">
                                        <?php esc_html_e('Save Body Class', 'capsman-enhanced'); ?></button>
                                </div>
                            <span class="ppc-feature-post-loader spinner"></span>
                            <div class="ppc-post-features-note"></div>
                            </th>
                    </tr>

                </table>
            </td>
        </tr>
    <?php
    }

    /**
     * Add custom styles form
     *
     */
    public function customStylesForm()
    {
        ?>
        <tr class="ppc-menu-row child-menu customstyles">
            <td colspan="2" class="form-td">
                <table class="frontend-features-form simple-form customstyles-form">
                    <tr class="ppc-menu-row parent-menu">
                        <td colspan="2">
                            <p class="cme-subtext">
                                <?php esc_html_e('You can add custom CSS styles that will be applied on the frontend of your site.', 'capsman-enhanced'); ?>
                            </p>
                            <p class="editing-custom-item">
                                <strong><?php esc_html_e('Editing:', 'capsman-enhanced'); ?></strong> 
                                <span class="title"></span>
                            </p>
                        </td>
                    </tr>

                    <tr class="field-row">
                        <th scope="row">
                            <?php esc_html_e('Label', 'capsman-enhanced'); ?>
                            <font color="red">*</font>
                        </th>
                        <td>
                            <input class="customstyles-element-new-name frontent-form-field customstyles-form-label" type="text" /><br />
                            <small>
                                <?php esc_html_e('This will only show here in the WordPress admin area.', 'capsman-enhanced'); ?>
                            </small>
                        </td>
                    </tr>

                    <tr class="field-row">
                        <th scope="row">
                            <?php esc_html_e('CSS Styles', 'capsman-enhanced'); ?>
                            <font color="red">*</font>
                        </th>
                        <td>
                            <textarea class="customstyles-element-new-element ppc-code-editor-page-css customstyles-form-element"></textarea><br />
                            <div class="customstyles-new-element-clear"></div>
                            <small>
                                <?php esc_html_e('Examples: .custom-style-1 { color: red; } #custom-header { background: red; } ', 'capsman-enhanced'); ?>
                            </small>
                        </td>
                    </tr>

                    <tr class="field-row">
                        <th scope="row">
                            <?php esc_html_e('Load on page types:', 'capsman-enhanced'); ?>
                        </th>
                        <td>
                            <select class="customstyles-new-element-pages chosen-cpt-select  customstyles-form-pages"
                                data-placeholder="<?php esc_attr_e('Select pages...', 'capsman-enhanced'); ?>"
                                multiple>
                                <?php foreach (self::getElementFormPageOptions() as $value => $label) : ?>
                                <option
                                    value="<?php echo esc_attr($value); ?>">
                                    <?php echo esc_html($label); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <br />
                            <small>
                                <?php esc_html_e('You can select post or pages where this element should be added.', 'capsman-enhanced'); ?>
                            </small>
                        </td>
                    </tr>

                    <tr class="field-row">
                        <th scope="row">
                            <?php esc_html_e('Add post metabox:', 'capsman-enhanced'); ?>
                        </th>
                        <td>
                            <select class="customstyles-new-element-post-types chosen-cpt-select customstyles-form-post-types"
                                data-placeholder="<?php esc_attr_e('Select post types...', 'capsman-enhanced'); ?>"
                                multiple>
                                <?php foreach (self::getElementFormPostTypesOptions() as $value => $label) : ?>
                                <option
                                    value="<?php echo esc_attr($value); ?>">
                                    <?php echo esc_html($label); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <br />
                            <small>
                                <?php esc_html_e('This will add a metabox on the post editing screen. You can use this feature to add body classes only for that post.', 'capsman-enhanced'); ?>
                            </small>
                        </td>
                    </tr>

                    <tr class="field-row">
                        <td colspan="2">
                            <input type="hidden" class="custom-edit-id" value="" />
                            <input class="customstyles-form-nonce" type="hidden"
                                value="<?php echo esc_attr(wp_create_nonce('customstyles-nonce')); ?>" />
                            <div class="custom-item-submit-buttons">
                                <div class="cancel-custom-item-edit button button-secondary"
                                    data-section="customstyles">
                                    <?php esc_html_e('Cancel Edit', 'capsman-enhanced'); ?></div>
                                <button type="button" class="submit-button customstyles-form-submit button button-secondary"
                                    data-required="<?php esc_attr_e('All fields are required.', 'capsman-enhanced'); ?>"
                                    data-add="<?php esc_attr_e('Add Custom Style', 'capsman-enhanced'); ?>"
                                    data-edit="<?php esc_attr_e('Save Edit', 'capsman-enhanced'); ?>"
                                    data-section="customstyles">
                                    <?php esc_html_e('Add Custom Style', 'capsman-enhanced'); ?></button>
                            </div>
                            <span class="ppc-feature-post-loader spinner"></span>
                            <div class="ppc-post-features-note"></div>
                            </th>
                    </tr>

                </table>
            </td>
        </tr>
    <?php
    }

    /**
     * Load body class tr
     *
     * @param array $args
     * @param boolean $echo
     * @return string
     */
    public static function do_pp_capabilities_frontend_features_bodyclass_tr($args, $echo = true)
    {
        //this uses same template as frontend element
        $return = self::do_pp_capabilities_frontend_features_frontendelements_tr($args, false);

        if ($echo) {
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $return;
        } else {
            return $return;
        }
    }

    /**
     * Load frontend element tr
     *
     * @param array $args
     * @param boolean $echo
     * @return string
     */
    public static function do_pp_capabilities_frontend_features_frontendelements_tr($args, $echo = true)
    {
        $disabled_frontend_items = $args['disabled_frontend_items'];
        $section_array           = $args['section_array'];
        $section_slug            = $args['section_slug'];
        $section_id              = $args['section_id'];
        $item_name               = $section_array['label'];
        $restrict_value          = $section_slug.'||'.$section_id;
        $additional_class        = isset($args['additional_class']) ? $args['additional_class'] : '';
        $additional_class       .= ' custom-item-' . $section_id;

        ob_start(); ?>
        <tr
            class="custom-item-row ppc-menu-row child-menu <?php echo esc_attr($section_slug . ' ' . $additional_class); ?>">
            <td class="restrict-column ppc-menu-checkbox">
                <input id="check-item-<?php echo $section_id; ?>"
                    class="check-item" type="checkbox" name="capsman_disabled_frontend_features[]"
                    value="<?php echo esc_attr($restrict_value); ?>"
                    <?php echo (in_array($restrict_value, $disabled_frontend_items)) ? 'checked' : ''; ?>/>
            </td>
            <td class="menu-column ppc-menu-item primay-td">

                <label for="check-item-<?php echo $section_id; ?>">
                    <span
                        class="menu-item-link<?php echo (in_array($restrict_value, $disabled_frontend_items)) ? ' restricted' : ''; ?>">
                        <strong>
                            <?php echo esc_html($section_array['label']); ?>
                        </strong>
                    </span>
                </label>
                <div class="custom-item-output">
                    <pre
                        class="custom-item-display"><?php echo esc_html($section_array['elements']); ?>
                    </pre>
                    <div>
                        <p class="frontend-feature-entry-pages">
                            <?php if (!empty($section_array['pages'])) : ?>
                            <strong><?php esc_html_e('Pages', 'capsman-enhanced'); ?>:</strong>
                            <?php echo esc_html(str_replace(['-', '_'], ' ', join(', ', $section_array['pages']))); ?>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </td>
            <td>
                <div class="button view-custom-item"><?php esc_html_e('View', 'capsman-enhanced'); ?></div>
            </td>
            <td>
                <div class="button edit-custom-item" 
                    data-section="<?php echo esc_attr($section_slug); ?>"
                    data-label="<?php echo esc_attr($section_array['label']); ?>"
                    data-element="<?php echo esc_attr($section_array['elements']); ?>"
                    data-pages="<?php echo esc_attr(join(', ', $section_array['pages'])); ?>"
                    data-post-types="<?php echo esc_attr(join(', ', $section_array['post_types'])); ?>"
                    data-id="<?php echo esc_attr($section_id); ?>">
                <?php esc_html_e('Edit', 'capsman-enhanced'); ?>
                </div>
            </td>
            <td>
                <div 
                    class="button frontend-features-delete-item frontend-feature-red" 
                    data-section="<?php echo esc_attr($section_slug); ?>" 
                    data-id="<?php echo esc_attr($section_id); ?>" 
                    data-delete-nonce="<?php echo esc_attr(wp_create_nonce('frontend-delete' . $section_id .'-nonce')); ?>">
                    <?php esc_html_e('Delete', 'capsman-enhanced'); ?>    
                </div>
            </td>
        </tr>
        <?php
        $return = ob_get_clean();

        if ($echo) {
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $return;
        } else {
            return $return;
        }
    }

    /**
     * Load custom styles tr
     *
     * @param array $args
     * @param boolean $echo
     * @return string
     */
    public static function do_pp_capabilities_frontend_features_customstyles_tr($args, $echo = true)
    {
        $disabled_frontend_items = $args['disabled_frontend_items'];
        $section_array           = $args['section_array'];
        $section_slug            = $args['section_slug'];
        $section_id              = $args['section_id'];
        $item_name               = $section_array['label'];
        $restrict_value          = $section_slug.'||'.$section_id;
        $additional_class        = isset($args['additional_class']) ? $args['additional_class'] : '';
        $additional_class       .= ' custom-item-' . $section_id;

        ob_start(); ?>
        <tr
            class="custom-item-row ppc-menu-row child-menu <?php echo esc_attr($section_slug . ' ' . $additional_class); ?>">
            <td class="restrict-column ppc-menu-checkbox">
                <input id="check-item-<?php echo $section_id; ?>"
                    class="check-item" type="checkbox" name="capsman_disabled_frontend_features[]"
                    value="<?php echo esc_attr($restrict_value); ?>"
                    <?php echo (in_array($restrict_value, $disabled_frontend_items)) ? 'checked' : ''; ?>/>
            </td>
            <td class="menu-column ppc-menu-item primay-td">

                <label for="check-item-<?php echo $section_id; ?>">
                    <span
                        class="menu-item-link<?php echo (in_array($restrict_value, $disabled_frontend_items)) ? ' restricted' : ''; ?>">
                        <strong>
                            <?php
                            echo esc_html($section_array['label']); ?>
                        </strong>
                    </span>
                </label>
                <div class="custom-item-output">
                    <pre
                        class="frontend-custom-styles-output"><?php esc_html_e($section_array['elements']); ?>
                    </pre>
                    <div>
                        <p class="frontend-feature-entry-pages">
                            <?php if (!empty($section_array['pages'])) : ?>
                            <strong><?php esc_html_e('Pages', 'capsman-enhanced'); ?>:</strong>
                            <?php echo esc_html(str_replace(['-', '_'], ' ', join(', ', $section_array['pages']))); ?>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="customstyles-new-element-update"></div>
                </div>
            </td>
            <td>
                <div class="button view-custom-item"><?php esc_html_e('View', 'capsman-enhanced'); ?></div>
            </td>
            <td>
                <div class="button edit-custom-item" 
                    data-section="<?php echo esc_attr($section_slug); ?>"
                    data-label="<?php echo esc_attr($section_array['label']); ?>"
                    data-pages="<?php echo esc_attr(join(', ', $section_array['pages'])); ?>"
                    data-post-types="<?php echo esc_attr(join(', ', $section_array['post_types'])); ?>"
                    data-id="<?php echo esc_attr($section_id); ?>">
                <?php esc_html_e('Edit', 'capsman-enhanced'); ?>
                </div>
            </td>
            <td>
                <div 
                    class="button frontend-features-delete-item frontend-feature-red" 
                    data-section="<?php echo esc_attr($section_slug); ?>" 
                    data-id="<?php echo esc_attr($section_id); ?>" 
                    data-delete-nonce="<?php echo esc_attr(wp_create_nonce('frontend-delete' . $section_id .'-nonce')); ?>">
                    <?php esc_html_e('Delete', 'capsman-enhanced'); ?>    
                </div>
            </td>
        </tr>
        <?php
        $return = ob_get_clean();

        if ($echo) {
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $return;
        } else {
            return $return;
        }
    }

    /**
     * Enqueue admin required css/js
     *
     * @return void
     */
    public static function loadFeaturesAdminAssets()
    {
        //add code editor
        wp_enqueue_code_editor(array('type' => 'text/html'));
        //initialize code editor
        wp_add_inline_script(
            'code-editor',
            ' (function($){
                $(function(){
                    if( $(".ppc-code-editor-page-css").length ) {
                        var editorSettings = wp.codeEditor.defaultSettings ? _.clone( wp.codeEditor.defaultSettings ) : {};
                        editorSettings.codemirror = _.extend(
                            {},
                            editorSettings.codemirror,
                            {
                                indentUnit: 2,
                                tabSize: 2,
                                mode: "css",
                            }
                        );
                        customstyles_editor = wp.codeEditor.initialize( $(".ppc-code-editor-page-css"), editorSettings );
                        $(document).on("keyup", ".CodeMirror-code", function(){
                            customstyles_editor.codemirror.save();
                            $(".ppc-code-editor-page-css").val(customstyles_editor.codemirror.getValue());
                            $(".ppc-code-editor-page-css").trigger("change");
                        });
                        $(document).on("click", ".customstyles-new-element-clear", function(){
                            customstyles_editor.codemirror.setValue("");
                            $(".ppc-code-editor-page-css").val("");
                            $(".ppc-code-editor-page-css").trigger("change");
                        });
                        $(document).on("click", ".customstyles-new-element-update", function(){
                            var element_value = $(this).closest(".custom-item-row").find(".frontend-custom-styles-output").html();
                            customstyles_editor.codemirror.setValue(element_value);
                            $(".ppc-code-editor-page-css").val(element_value);
                            $(".ppc-code-editor-page-css").trigger("change");
                        });
                    }
                });
             })(jQuery);'
        );

        //add chosen css
        wp_enqueue_style(
            'pp-capabilities-chosen-css',
            plugin_dir_url(CME_FILE) . 'common/libs/chosen-v1.8.7/chosen.css',
            false,
            CAPSMAN_VERSION
        );

        //add chosen js
        wp_enqueue_script(
            'pp-capabilities-chosen-js',
            plugin_dir_url(CME_FILE) . 'common/libs/chosen-v1.8.7/chosen.jquery.js',
            ['jquery'],
            CAPSMAN_VERSION
        );
        //initialize chosen select
        wp_add_inline_script(
            'pp-capabilities-chosen-js',
            ' (function($){
                $(function(){
                    if( $(".chosen-cpt-select").length ) {
                        $(".chosen-cpt-select").chosen({
                            "width": "100%"
                          });
                    }
                });
             })(jQuery);'
        );
    }
}
?>