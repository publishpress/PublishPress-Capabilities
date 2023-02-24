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
          'global'    => esc_html__('Every Pages', 'capsman-enhanced'),
          'frontpage' => esc_html__('Home / FrontPage', 'capsman-enhanced'),
          'archive'   => esc_html__('Archive Pages', 'capsman-enhanced'),
          'singlular' => esc_html__('Singlular Pages', 'capsman-enhanced')
        ];
      
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
                <table class="frontend-features-form simple-form">
                    <tr class="ppc-menu-row parent-menu">
                        <td colspan="2">
                            <p class="cme-subtext">
                                <?php esc_html_e('You can remove elements from frontend area by adding their IDs or classes below:', 'capsman-enhanced'); ?>
                            </p>
                            </h4>
                        </td>
                    </tr>

                    <tr class="field-row">
                        <th scope="row">
                            <?php esc_html_e('Label', 'capsman-enhanced'); ?>
                            <font color="red">*</font>
                        </th>
                        <td>
                            <input class="frontend-element-new-name frontent-form-field" type="text" /><br />
                            <small>
                                <?php esc_html_e('Enter the name/label to identify the element on this screen.', 'capsman-enhanced'); ?>
                            </small>
                        </td>
                    </tr>

                    <tr class="field-row">
                        <th scope="row">
                            <?php esc_html_e('Element IDs or Classes', 'capsman-enhanced'); ?>
                            <font color="red">*</font>
                        </th>
                        <td>
                            <textarea class="frontend-element-new-element frontent-form-field"></textarea><br />
                            <small>
                                <?php esc_html_e('IDs or classes to hide. Separate multiple values by comma (.custom-item-one, .custom-item-two, #new-item-id).', 'capsman-enhanced'); ?>
                            </small>
                        </td>
                    </tr>

                    <tr class="field-row">
                        <th scope="row">
                            <?php esc_html_e('Restrict to pages:', 'capsman-enhanced'); ?>
                            <font color="red">*</font>
                        </th>
                        <td>
                            <select class="frontend-element-new-element-pages chosen-cpt-select"
                                data-placeholder="<?php esc_attr_e('Select option...', 'capsman-enhanced'); ?>"
                                multiple>
                                <?php foreach (self::getElementFormPageOptions() as $value => $label) : ?>
                                <option
                                    value="<?php echo esc_attr($value); ?>">
                                    <?php echo esc_html($label); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <input class="frontend-element-new-element-posts frontent-form-field" type="text"
                                placeholder="Enter multiple page/post ID separated by space." />
                            <br />
                            <small>
                                <?php esc_html_e('You can select post or pages where this element should be added. Additional custom post ID can be added in the provided textbox separating multiple values by space (87 873 203).', 'capsman-enhanced'); ?>
                            </small>
                        </td>
                    </tr>

                    <tr class="field-row">
                        <td colspan="2">
                            <input class="frontend-element-form-nonce" type="hidden"
                                value="<?php echo esc_attr(wp_create_nonce('frontend-element-nonce')); ?>" />
                            <button type="button" class="frontend-element-form-submit button button-secondary"
                                data-required="<?php esc_attr_e('All fields are required.', 'capsman-enhanced'); ?>">
                                <?php esc_html_e('Add', 'capsman-enhanced'); ?></button>
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
                <table class="frontend-features-form simple-form">
                    <tr class="ppc-menu-row parent-menu">
                        <td colspan="2">
                            <p class="cme-subtext">
                                <?php esc_html_e('You can add page body class using the form below:', 'capsman-enhanced'); ?>
                            </p>
                            </h4>
                        </td>
                    </tr>

                    <tr class="field-row">
                        <th scope="row">
                            <?php esc_html_e('Label', 'capsman-enhanced'); ?>
                            <font color="red">*</font>
                        </th>
                        <td>
                            <input class="body-class-new-name frontent-form-field" type="text" /><br />
                            <small>
                                <?php esc_html_e('Enter the name/label to identify the element on this screen.', 'capsman-enhanced'); ?>
                            </small>
                        </td>
                    </tr>

                    <tr class="field-row">
                        <th scope="row">
                            <?php esc_html_e('Classes', 'capsman-enhanced'); ?>
                            <font color="red">*</font>
                        </th>
                        <td>
                            <textarea class="body-class-new-element frontent-form-field"></textarea><br />
                            <small>
                                <?php esc_html_e('Enter classes that should be added to body html. Separate multiple values by space (custom-item-one custom-item-two).', 'capsman-enhanced'); ?>
                            </small>
                        </td>
                    </tr>

                    <tr class="field-row">
                        <th scope="row">
                            <?php esc_html_e('Restrict to pages:', 'capsman-enhanced'); ?>
                            <font color="red">*</font>
                        </th>
                        <td>
                            <select class="body-class-new-element-pages chosen-cpt-select"
                                data-placeholder="<?php esc_attr_e('Select option...', 'capsman-enhanced'); ?>"
                                multiple>
                                <?php foreach (self::getElementFormPageOptions() as $value => $label) : ?>
                                <option
                                    value="<?php echo esc_attr($value); ?>">
                                    <?php echo esc_html($label); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <input class="body-class-new-element-posts frontent-form-field" type="text"
                                placeholder="Enter multiple page/post ID separated by space." />
                            <br />
                            <small>
                                <?php esc_html_e('You can select post or pages where this element should be added. Additional custom post ID can be added in the provided textbox separating multiple values by space (87 873 203).', 'capsman-enhanced'); ?>
                            </small>
                        </td>
                    </tr>

                    <tr class="field-row">
                        <td colspan="2">
                            <input class="body-class-form-nonce" type="hidden"
                                value="<?php echo esc_attr(wp_create_nonce('bodyclass-nonce')); ?>" />
                            <button type="button" class="body-class-form-submit button button-secondary"
                                data-required="<?php esc_attr_e('All fields are required.', 'capsman-enhanced'); ?>">
                                <?php esc_html_e('Add', 'capsman-enhanced'); ?></button>
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
                <table class="frontend-features-form simple-form">
                    <tr class="ppc-menu-row parent-menu">
                        <td colspan="2">
                            <p class="cme-subtext">
                                <?php esc_html_e('You can add custom style css to be added to frontend pages using the form below:', 'capsman-enhanced'); ?>
                            </p>
                            </h4>
                        </td>
                    </tr>

                    <tr class="field-row">
                        <th scope="row">
                            <?php esc_html_e('Label', 'capsman-enhanced'); ?>
                            <font color="red">*</font>
                        </th>
                        <td>
                            <input class="customstyles-element-new-name frontent-form-field" type="text" /><br />
                            <small>
                                <?php esc_html_e('Enter the name/label to identify the element on this screen.', 'capsman-enhanced'); ?>
                            </small>
                        </td>
                    </tr>

                    <tr class="field-row">
                        <th scope="row">
                            <?php esc_html_e('Style CSS', 'capsman-enhanced'); ?>
                            <font color="red">*</font>
                        </th>
                        <td>
                            <textarea class="customstyles-element-new-element ppc-code-editor-page-css"></textarea><br />
                            <div class="customstyles-new-element-clear"></div>
                            <small>
                                <?php esc_html_e('Example: .custom-style-1 { color: red;} #custom-header { background: red; } ', 'capsman-enhanced'); ?>
                            </small>
                        </td>
                    </tr>

                    <tr class="field-row">
                        <th scope="row">
                            <?php esc_html_e('Restrict to pages:', 'capsman-enhanced'); ?>
                            <font color="red">*</font>
                        </th>
                        <td>
                            <select class="customstyles-new-element-pages chosen-cpt-select"
                                data-placeholder="<?php esc_attr_e('Select option...', 'capsman-enhanced'); ?>"
                                multiple>
                                <?php foreach (self::getElementFormPageOptions() as $value => $label) : ?>
                                <option
                                    value="<?php echo esc_attr($value); ?>">
                                    <?php echo esc_html($label); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <input class="customstyles-new-element-posts frontent-form-field" type="text"
                                placeholder="Enter multiple page/post ID separated by space." />
                            <br />
                            <small>
                                <?php esc_html_e('You can select post or pages where this element should be added. Additional custom post ID can be added in the provided textbox separating multiple values by space (87 873 203).', 'capsman-enhanced'); ?>
                            </small>
                        </td>
                    </tr>

                    <tr class="field-row">
                        <td colspan="2">
                            <input class="customstyles-form-nonce" type="hidden"
                                value="<?php echo esc_attr(wp_create_nonce('customstyles-nonce')); ?>" />
                            <button type="button" class="customstyles-form-submit button button-secondary"
                                data-required="<?php esc_attr_e('All fields are required.', 'capsman-enhanced'); ?>">
                                <?php esc_html_e('Add', 'capsman-enhanced'); ?></button>
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
        $sn                      = $args['sn'];
        $item_name               = $section_array['label'];
        $restrict_value          = $section_slug.'||'.$section_id;
        $additional_class        = isset($args['additional_class']) ? $args['additional_class'] : '';

        ob_start(); ?>
        <tr
            class="ppc-menu-row child-menu <?php echo esc_attr($section_slug . ' ' . $additional_class); ?>">
            <td class="restrict-column ppc-menu-checkbox">
                <input id="check-item-<?php echo (int) $sn; ?>"
                    class="check-item" type="checkbox" name="capsman_disabled_frontend_features[]"
                    value="<?php echo esc_attr($restrict_value); ?>"
                    <?php echo (in_array($restrict_value, $disabled_frontend_items)) ? 'checked' : ''; ?>/>
            </td>
            <td class="menu-column ppc-menu-item">

                <label for="check-item-<?php echo (int) $sn; ?>">
                    <span
                        class="menu-item-link<?php echo (in_array($restrict_value, $disabled_frontend_items)) ? ' restricted' : ''; ?>">
                        <strong>
                            &mdash;
                            <?php
                                            echo esc_html($section_array['label']) . ' <small class="frontend-feature-entry">(' . esc_html($section_array['elements']). ')</small> <small class="frontend-feature-entry-pages">[' . esc_html(join(', ', $section_array['pages'])). ']</small> &nbsp; '
                                            . '<span class="frontend-features-delete-item frontend-feature-red" data-section="' . esc_attr($section_slug)  . '" data-id="' . esc_attr($section_id)  . '" data-delete-nonce="'. esc_attr(wp_create_nonce('frontend-delete' . $section_id .'-nonce')) .'"><small>(' . esc_html__('Delete', 'capsman-enhanced') . ')</small></span>' . ''; ?>
                        </strong>
                    </span>
                </label>
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
        $sn                      = $args['sn'];
        $item_name               = $section_array['label'];
        $restrict_value          = $section_slug.'||'.$section_id;
        $additional_class        = isset($args['additional_class']) ? $args['additional_class'] : '';

        ob_start(); ?>
        <tr
            class="ppc-menu-row child-menu <?php echo esc_attr($section_slug . ' ' . $additional_class); ?>">
            <td class="restrict-column ppc-menu-checkbox">
                <input id="check-item-<?php echo (int) $sn; ?>"
                    class="check-item" type="checkbox" name="capsman_disabled_frontend_features[]"
                    value="<?php echo esc_attr($restrict_value); ?>"
                    <?php echo (in_array($restrict_value, $disabled_frontend_items)) ? 'checked' : ''; ?>/>
            </td>
            <td class="menu-column ppc-menu-item">

                <label for="check-item-<?php echo (int) $sn; ?>">
                    <span
                        class="menu-item-link<?php echo (in_array($restrict_value, $disabled_frontend_items)) ? ' restricted' : ''; ?>">
                        <strong>
                            &mdash;
                            <?php
                                            echo esc_html($section_array['label']) . ' <small class="frontend-feature-entry-pages">[' . esc_html(join(', ', $section_array['pages'])). ']</small> &nbsp; '
                                            . '<span class="frontend-features-delete-item frontend-feature-red" data-section="' . esc_attr($section_slug)  . '" data-id="' . esc_attr($section_id)  . '" data-delete-nonce="'. esc_attr(wp_create_nonce('frontend-delete' . $section_id .'-nonce')) .'"><small>(' . esc_html__('Delete', 'capsman-enhanced') . ')</small></span>' . ''; ?>
                        </strong>
                    </span>
                </label>
                <pre
                    class="frontend-custom-styles-output"><?php esc_html_e($section_array['elements']); ?></pre>
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
                            "width": "30%"
                          });
                    }
                });
             })(jQuery);'
        );
    }
}
?>