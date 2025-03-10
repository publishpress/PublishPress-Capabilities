<?php

class Pp_Roles_Admin
{

    /**
     * The capability
     *
     * @access   protected
     * @var string
     */
    protected $capability = 'manage_capabilities_roles';

    /**
     * Roles list table instance
     *
     * @var null
     */
    protected $roles_list_table;

    /**
     * Initialize the class and set its properties.
     *
     */
    public function __construct()
    {
        global $hook_suffix;    //avoid warning outputs
        if (!isset($hook_suffix)) {
            $hook_suffix = '';
        }

        $this->roles_list_table = null;
        $this->get_roles_list_table();
    }

    /**
     * Handle post actions
     */
    public function handle()
    {
        $current_action = $this->current_action();

        if (in_array($current_action, $this->actions)) {
            $current_action = str_replace('pp-roles-', '', $current_action);
            $current_action = str_replace('-', '_', $current_action);
            $this->$current_action();
        }
    }

    /**
     * Get the current action selected from the bulk actions dropdown.
     *
     * @return string|false The action name or False if no action was selected
     */
    protected function current_action()
    {
        if (isset($_REQUEST['filter_action']) && !empty($_REQUEST['filter_action'])) {
            return false;
        }

        if (isset($_REQUEST['action']) && -1 != $_REQUEST['action']) {
            return sanitize_key($_REQUEST['action']);
        }

        if (isset($_REQUEST['action2']) && -1 != $_REQUEST['action2']) {
            return sanitize_key($_REQUEST['action2']);
        }

        return false;
    }

    /**
     * Returns the admin list table instance
     *
     * @return PP_Capabilities_Roles_List_Table
     */
    public function get_roles_list_table()
    {
        if ($this->roles_list_table === null) {
            if (!class_exists('PP_Capabilities_Roles_List_Table')) {
                require_once plugin_dir_path(__FILE__) . 'class-pp-roles-list-table.php';
            }

            $this->roles_list_table = new PP_Capabilities_Roles_List_Table([
                'screen' => get_current_screen()
            ]);
        }

        return $this->roles_list_table;
    }

    /**
     * Get the fields tabs to be rendered on role screen
     *
     * @param mixed $current
     * @param bool $role_edit whether current action is role edit
     * @param bool $role_copy whether current action is role copy
     *
     * @return array
     */
    public static function get_fields_tabs($current, $role_edit, $role_copy)
    {
        $fields_tabs = [];

        $fields_tabs['general'] = [
            'label' => esc_html__('General', 'capability-manager-enhanced'),
            'icon'  => 'dashicons dashicons-admin-tools',
        ];

        $fields_tabs['editing'] = [
            'label'    => esc_html__('Editing', 'capability-manager-enhanced'),
            'icon'     => 'dashicons dashicons-edit-page',
        ];

        if (defined('WC_PLUGIN_FILE')) {
            $fields_tabs['woocommerce'] = [
                'label'    => esc_html__('WooCommerce', 'capability-manager-enhanced'),
                'icon'     => 'dashicons dashicons-products',
            ];
        }
        
        $fields_tabs['advanced'] = [
            'label' => esc_html__('Advanced', 'capability-manager-enhanced'),
            'icon'     => 'dashicons dashicons-admin-generic',
        ];

        if ($role_edit && !$current['is_system']) {
            $fields_tabs['delete'] = [
                'label'    => esc_html__('Delete'),
                'icon'     => 'dashicons dashicons-trash',
            ];
        }

        /**
         * Customize fields tabs presented on role screen.
         *
         * @param array $fields_tabs Existing fields tabs to display.
         * @param mixed $current
         */
        $fields_tabs = apply_filters('pp_roles_tabs', $fields_tabs, $current);

        return $fields_tabs;
    }

    /**
     * Get the fields to be rendered on role screen
     *
     * @param mixed $current.
     * @param bool $role_edit whether current action is role edit
     * @param bool $role_copy whether current action is role copy
     *
     * @return array
     */
    public static function get_fields($current, $role_edit, $role_copy)
    {
        $editor_options = [];

        $editor_options['block_editor']       = esc_html__('Gutenberg editor', 'capability-manager-enhanced');
        if (class_exists('Classic_Editor')) {
            $editor_options['classic_editor'] = esc_html__('Classic editor', 'capability-manager-enhanced');
        }

        $show_block_control = true;
        $fields = [];

        if ($role_edit && $current && isset($current['role']) && $current['role'] === 'administrator') {
            $show_block_control = false;
        }

        //add role_name
        $fields['role_name'] = [
            'label'     => esc_html__('Role Name', 'capability-manager-enhanced'),
            'type'      => 'text',
            'value_key' => 'name',
            'tab'       => 'general',
            'editable'  => true,
            'required'  => true,
        ];

        //add role_slug
        $fields['role_slug'] = [
            'label'     => esc_html__('Role Slug', 'capability-manager-enhanced'),
            'description' => esc_html__('The "slug" is the URL-friendly version of the role. It is usually all lowercase and contains only letters, numbers and underscores.', 'capability-manager-enhanced'),
            'type'      => 'text',
            'value_key' => 'role',
            'tab'       => 'general',
            'editable'  => ($role_edit) ? false : true,
            'required'  => false,
        ];

        if ($show_block_control) {
            //add disable_role_user_login
            $fields['disable_role_user_login'] = [
                'label'        => esc_html__('Block Login', 'capability-manager-enhanced'),
                'description'  => esc_html__('Block users in this role from logging into the site.', 'capability-manager-enhanced'),
                'type'         => 'checkbox',
                'value_key'    => 'disable_role_user_login',
                'tab'          => 'advanced',
                'editable'     => true,
                'required'     => false,
            ];
            //add block_dashboard_access
            $fields['block_dashboard_access'] = [
                'label'        => esc_html__('Block Dashboard Access', 'capability-manager-enhanced'),
                'description'  => esc_html__('Block users in this role from accessing admin area.', 'capability-manager-enhanced'),
                'type'         => 'checkbox',
                'value_key'    => 'block_dashboard_access',
                'tab'          => 'advanced',
                'editable'     => true,
                'required'     => false,
            ];
        }

        //add role_level
        $fields['role_level'] = [
            'label'     => esc_html__('Role Level', 'capability-manager-enhanced'),
            'description' => esc_html__('Each user role has a level from 0 to 10. The Subscriber role defaults to the lowest level (0). The Administrator role defaults to level 10.', 'capability-manager-enhanced'),
            'type'      => 'select',
            'value_key' => 'role_level',
            'tab'       => 'advanced',
            'editable'  => ($role_edit && $current && isset($current['role']) && $current['role'] === 'administrator') ? false : true,
            'options'   => [
                '10' => '10',
                '9' => '9',
                '8' => '8',
                '7' => '7',
                '6' => '6',
                '5' => '5',
                '4' => '4',
                '3' => '3',
                '2' => '2',
                '1' => '1',
                '0' => '0',
            ],
        ];

        //add delete_role
        $fields['delete_role'] = [
            'label'       => esc_html__('Delete role', 'capability-manager-enhanced'),
            'description' => esc_html__('Deleting this role will completely remove it from database and is irrecoverable.', 'capability-manager-enhanced'),
            'type'      => 'button',
            'value_key' => '',
            'tab'       => 'delete',
            'editable'  => true,
        ];

        //add disable_code_editor
        $fields['disable_code_editor'] = [
            'label'        => /* Translators: "Editor" means post editor like Gutenberg */ esc_html__('Disable Code Editor', 'capability-manager-enhanced'),
            'description'  => /* Translators: "Editor" means post editor like Gutenberg */ esc_html__('Disable the "Code editor" option for the Gutenberg block editor.', 'capability-manager-enhanced'),
            'type'         => 'checkbox',
            'value_key'    => 'disable_code_editor',
            'tab'          => 'editing',
            'editable'     => true,
            'required'     => false,
        ];

        if (count($editor_options) > 1) {
            //add role_editor
            $fields['role_editor'] = [
                'label'       => /* Translators: "Editor" means post editor like Gutenberg */ esc_html__('Control Allowed Editors', 'capability-manager-enhanced'),
                'description' => /* Translators: "Editor" means post editor like Gutenberg */ esc_html__('Select the allowed editor options for users in this role.', 'capability-manager-enhanced'),
                'type'        => 'select',
                'multiple'    => true,
                'value_key'   => 'role_editor',
                'tab'         => 'editing',
                'editable'    => true,
                'options'     => $editor_options,
            ];
        }
        
        if (defined('WC_PLUGIN_FILE')) {
            //add disable_woocommerce_admin_restrictions
            $fields['disable_woocommerce_admin_restrictions'] = [
                'label'        => esc_html__('Disable WooCommerce admin restrictions', 'capability-manager-enhanced'),
                'description'   => sprintf(esc_html__('By default, WooCommerce prevents most users from accessing the WordPress admin area. When enabled, this setting will remove those restrictions for this role. %1s Click here for more details. %2s', 'capability-manager-enhanced'), '<a href="https://publishpress.com/knowledge-base/wordpress-admin-area-access-for-woocommerce-users/" target="blank">', '</a>'),
                'type'         => 'checkbox',
                'value_key'    => 'disable_woocommerce_admin_restrictions',
                'tab'          => 'woocommerce',
                'editable'     => true,
                'required'     => false,
            ];
        }
        
        /**
         * Customize fields presented on role screen.
         *
         * @param array $fields Existing fields to display.
         * @param mixed $current Author to be rendered.
         */
        $fields = apply_filters('pp_roles_fields', $fields, $current);

        return $fields;
    }

    /**
     * Get a rendered field partial
     *
     * @param array $args Arguments to render in the partial.
     * @param array $current current form data.
     */
    private static function get_rendered_role_partial($args, $current)
    {
        $defaults = [
            'description' => '',
            'type'        => 'text',
            'tab'         => 'general',
            'editable'    => true,
            'required'    => false,
            'multiple'    => false,
            'value'       => '',
            'options'     => [],
            'label'       => '',
        ];
        $args      = array_merge($defaults, $args);
        $key       = $args['key'];
        $default_tab  = (!empty($_GET) && !empty($_GET['active_tab'])) ? sanitize_key($_GET['active_tab']) : 'general';
        $tab_class = 'pp-roles-tab-tr pp-roles-' . $args['tab'] . '-tab';
        $tab_style = ($args['tab'] === $default_tab) ? '' : 'display:none;';
        ?>
        <tr valign="top" 
            class="<?php echo esc_attr('form-field role-' . $key . '-wrap '. $tab_class); ?>"
            data-tab="<?php echo esc_attr($args['tab']); ?>"
            style="<?php echo esc_attr($tab_style); ?>"
            >
            <th scope="row">
                <?php if (!empty($args['label'])) : ?>
                    <label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($args['label']); ?></label>
                    <?php if ($args['required']) { ?>
                        <span class="required">*</span>
                    <?php } ?>
                <?php endif; ?>
                 <?php if ($key === 'role_slug') { ?>
                    <p id="pp-role-slug-exists" class="red-warning" style="display:none;">
                        <?php esc_html_e('Slug already exists', 'capability-manager-enhanced'); ?>
                        <span class="dashicons dashicons-warning"></span>
                    </p>
                <?php } ?>
            </th>
            <td>
                <?php 
                if ($key === 'role_editor') : ?>
                    <?php 
                    $allowed_editor = (isset($args['value']) && is_array($args['value']) && !empty($args['value'])) ? true : false;             
                    $select_style   = ($allowed_editor) ? '' : 'display:none;';
                    ?>
                    <div class="role-editor-toggle-box">
                        <input name="<?php echo esc_attr($key.'-toggle'); ?>" 
                            id="<?php echo esc_attr($key); ?>" 
                            class="allowed-editor-toggle"
                            type="checkbox"
                            value="1"
                            <?php checked(true, $allowed_editor); ?>/>
                    </div>

                    <div class="role-editor-select-box" style="<?php echo esc_attr($select_style); ?>">
                        <select 
                            name="<?php echo esc_attr($key); ?><?php echo $args['multiple'] ? '[]' : '';?>"
                            id="<?php echo esc_attr($key.'-select'); ?>"
                            class="pp-capabilities-role-choosen"
                            data-placeholder="<?php /* Translators: "Editor" means post editor like Gutenberg */ esc_html_e('Select allowed editor', 'capability-manager-enhanced'); ?>"
                            data-message="<?php /* Translators: "Editor" means post editor like Gutenberg */ esc_attr_e('You must select at least one editor for the role when managing allowed editor.',  'capability-manager-enhanced'); ?>"
                            <?php echo ($args['multiple'] ? 'multiple' : '');?>
                            <?php echo ($args['required'] ? 'required="true"' : '');?>>
                            <?php
                            foreach ($args['options'] as $select_key => $select_label) {
                                if ($args['multiple']) {
                                    $selected_option = (isset($args['value']) && is_array($args['value']) && in_array($select_key, $args['value'])) ? true : false;
                                } else {
                                    $selected_option = (isset($args['value']) && $select_key == $args['value']) ? true : false;
                                }
                                ?>
                                <option value="<?php echo esc_attr($select_key); ?>"
                                        <?php selected(true, $selected_option); ?>>
                                        <?php echo esc_html($select_label); ?>
                                </option>
                            <?php } ?>
                        </select>
                        <?php if (isset($args['description'])) : ?>
                            <p class="description">
                                <?php echo esc_html($args['description']); ?>
                            </p>
                        <?php endif; ?>
                        </div>
                <?php 
                elseif ($args['type'] === 'select') : ?>
                    <select 
                        name="<?php echo esc_attr($key); ?><?php echo $args['multiple'] ? '[]' : '';?>"
                        id="<?php echo esc_attr($key); ?>"
                        class="<?php echo (!$args['editable'] ? '' : 'pp-capabilities-role-choosen'); ?>"
                        data-placeholder="<?php printf(esc_html__('Select %s', 'capability-manager-enhanced'), esc_html(strtolower($args['label']))); ?>"
                        <?php echo ($args['multiple'] ? 'multiple' : '');?>
                        <?php echo ($args['required'] ? 'required="true"' : '');?>>
                        <?php
                        foreach ($args['options'] as $select_key => $select_label) {
                            if ($args['multiple']) {
                                $selected_option = (isset($args['value']) && is_array($args['value']) && in_array($select_key, $args['value'])) ? true : false;
                            } else {
                                $selected_option = (isset($args['value']) && $select_key == $args['value']) ? true : false;
                            }
                            ?>
                            <option value="<?php echo esc_attr($select_key); ?>"
                                    <?php echo (!$args['editable'] && !$selected_option ? 'disabled' : ''); ?>
                                    <?php selected(true, $selected_option); ?>>
                                    <?php echo esc_html($select_label); ?>
                            </option>
                        <?php } ?>
                    </select>
                    <?php if (isset($args['description'])) : ?>
                        <p class="description">
                            <?php echo esc_html($args['description']); ?>
                            <?php if ($key === 'role_level') : ?>
                                <a href="https://publishpress.com/blog/user-role-levels/" target="blank">
                                    <?php esc_html_e('Read more on Role Level.',  'capability-manager-enhanced'); ?>
                                </a>
                            <?php endif; ?>
                        </p>
                    <?php endif; ?>
                <?php
                elseif ($args['type'] === 'button') :
                    ?>
                    <input type="submit" 
                        class="button-secondary pp-roles-delete-botton" 
                        id="<?php echo esc_attr($key); ?>"
                        name="<?php echo esc_attr($key); ?>"
                        value="<?php echo esc_attr($args['label']); ?>"
                        onclick="return confirm('<?php esc_attr_e('Are you sure you want to delete this role?',  'capability-manager-enhanced'); ?>');"
                         />
                        <?php if (isset($args['description'])) : ?>
                            <p class="description" style="color: red;"><?php echo esc_html($args['description']); ?></p>
                        <?php endif; ?>
                        <?php
                elseif ($args['type'] === 'checkbox') :
                    ?>
                    <input name="<?php echo esc_attr($key); ?>" 
                        id="<?php echo esc_attr($key); ?>" 
                        type="<?php echo esc_attr($args['type']); ?>"
                        value="1"
                        <?php checked(1, (int)$args['value']); ?>
                        <?php echo ($args['required'] ? 'required="true"' : '');?> 
                        <?php echo (!$args['editable'] ? 'readonly="readonly"' : ''); ?>/>
                        <?php if (isset($args['description'])) : ?>
                            <span class="description"><?php echo $args['description']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
                        <?php endif; ?>
                <?php else : ?>
                    <input name="<?php echo esc_attr($key); ?>" 
                        id="<?php echo esc_attr($key); ?>"
                        type="<?php echo esc_attr($args['type']); ?>"
                        value="<?php echo esc_attr($args['value']); ?>"
                       <?php echo ($args['required'] ? 'required="true"' : '');?> 
                       <?php echo (!$args['editable'] ? 'readonly="readonly"' : ''); ?>/>
                        <?php if (isset($args['description'])) : ?>
                            <p class="description"><?php echo esc_html($args['description']); ?></p>
                        <?php endif; ?>
                <?php endif; ?>
            </td>
        </tr>
        <?php
    }

    /**
     * Get role edit screen
     *
     */
    public function get_roles_edit_ui()
    {
        global $wp_roles;
        
        if (!empty($_GET) && !empty($_GET['role_action'])) {
            $role_action = sanitize_key($_GET['role_action']);
        } else {
            $role_action = 'new';
        }

        $default_tab  = (!empty($_GET) && !empty($_GET['active_tab'])) ? sanitize_key($_GET['active_tab']) : 'general';
        $tab_class    = 'ppc-' . $role_action;
        $current_role = '';
        $current      = false;
        $role_edit    = false;
        $role_copy    = false;

        if ($role_action === 'edit' && !empty($_GET['role']) && $role_data = pp_roles_get_role_data(sanitize_key($_GET['role']))) {
            $current_role   = sanitize_key($_GET['role']);
            $current        = $role_data;
            $role_edit      = true;
        } elseif ($role_action === 'copy' && !empty($_GET['role']) && $role_data = pp_roles_get_role_data(sanitize_key($_GET['role']))) {
            $current_role   = sanitize_key($_GET['role']);
            $current    = $role_data;
            $role_copy  = true;
        }

        if ($current_role) {
            //add role options
            $role_option = get_option("pp_capabilities_{$current_role}_role_option", []);
            if (is_array($role_option) && !empty($role_option)) {
                $current = array_merge($role_option, $current);
            }
            //add role level
            $current['role_level'] = (is_array($current) && isset($current['capabilities'])) ? ak_caps2level($current['capabilities']) : '0';
        }
        
        $fields_tabs  = apply_filters('pp_roles_fields_tabs', self::get_fields_tabs($current, $role_edit, $role_copy), $current, $role_edit, $role_copy);
        $fields       = apply_filters('pp_roles_fields', self::get_fields($current, $role_edit, $role_copy), $current, $role_edit, $role_copy);

        if ($role_copy) {
            pp_capabilities_roles()->notify->add('info', sprintf( esc_html__('%s role copied. Please click the "Create Role" button to create this new role.', 'capability-manager-enhanced'), $current['name']));
            //update new name and remove slug
            $current['role'] = $current['role'] . '_copy';
            $current['name'] = $current['name'] . ' Copy';
        }

        $save_button_text = esc_html__('Save Changes', 'capability-manager-enhanced');

        $capabilities_counts = (!empty($current['capabilities'])) ? count($current['capabilities']) : 0;
        $editor_features_counts = (!empty($current['editor_features'])) ? (int) $current['editor_features'] : 0;
        $admin_features_counts = (!empty($current['admin_features'])) ? (int) $current['admin_features'] : 0;
        $profile_features_counts = (!empty($current['profile_features'])) ? (int) $current['profile_features'] : 0;
        $admin_menus_counts = (!empty($current['admin_menus'])) ? (int) $current['admin_menus'] : 0;
        $nav_menus_counts = (!empty($current['nav_menus'])) ? (int) $current['nav_menus'] : 0;

        if (!empty($current['role'])) {
            $features_counts = [
                esc_html__('Editor Features', 'capability-manager-enhanced') => '<a target="blank" href="' . admin_url('admin.php?page=pp-capabilities-editor-features&role=' . $current['role'] . '') . '">(' . $editor_features_counts . ')</a>',
                esc_html__('Admin Features', 'capability-manager-enhanced') => '<a target="blank" href="' . admin_url('admin.php?page=pp-capabilities-admin-features&role=' . $current['role'] . '') . '">(' . $admin_features_counts . ')</a>',
                esc_html__('Profile Features', 'capability-manager-enhanced') => '<a target="blank" href="' . admin_url('admin.php?page=pp-capabilities-profile-features&role=' . $current['role'] . '') . '">(' . $profile_features_counts . ')</a>',
                esc_html__('Admin Menus', 'capability-manager-enhanced') => '<a target="blank" href="' . admin_url('admin.php?page=pp-capabilities-admin-menus&role=' . $current['role'] . '') . '">(' . $admin_menus_counts . ')</a>',
                esc_html__('Nav Menus', 'capability-manager-enhanced') => '<a target="blank" href="' . admin_url('admin.php?page=pp-capabilities-nav-menus&role=' . $current['role'] . '') . '">(' . $nav_menus_counts . ')</a>',
            ];
        } else {
            $features_counts = [];
        }

        pp_capabilities_roles()->notify->display();
        ?>
        <div class="wrap pp-role-edit-wrap <?php echo esc_attr($tab_class); ?>">
            <h1>
            <?php 
            if ($role_edit) {
                printf( esc_html__('Edit Role: %s', 'capability-manager-enhanced'), esc_html($current['name']));
            } elseif ($role_copy) {
                esc_html_e('Copy Role', 'capability-manager-enhanced');
            } else {
                esc_html_e('Create New Role', 'capability-manager-enhanced');
            }
            ?>
            <a href="<?php echo esc_url(admin_url('admin.php?page=pp-capabilities-roles')); ?>" class="page-title-action">
                <?php esc_html_e('All Roles', 'capability-manager-enhanced'); ?>
            </a>
            </h1>
            <div class="wp-clearfix"></div>

            <form method="post" action="" onkeydown="return event.key != 'Enter';"> 
                <input type="hidden" name="active_tab" class="ppc-roles-active-tab" value="<?php echo esc_attr($default_tab); ?>">
                <input type="hidden" name="role_action" value="<?php echo esc_attr($role_action); ?>">
                <input type="hidden" name="action" value="<?php echo ($role_action === 'edit' ? 'pp-roles-edit-role' : 'pp-roles-add-role'); ?>">
                <input type="hidden" class="ppc-roles-all-roles" value="<?php echo esc_attr(join(',', array_keys($wp_roles->get_names()))); ?>">
                <input type="hidden" name="_wpnonce" 
                value="<?php echo esc_attr($role_action === 'edit' ? wp_create_nonce('edit-role') : wp_create_nonce('add-role') ); ?>"
                >
                <input type="hidden" name="current_role" class="ppc-roles-current-role" value="<?php echo esc_attr($current_role); ?>">
                <div id="poststuff">
                    <div id="post-body" class="metabox-holder columns-2">
                        <div id="post-body-content">
                            <div class="ppc-roles-section postbox">
                                
                                <div class="inside">
                                    <div class="main">

                                        <ul class="ppc-roles-tab">
                                            <?php     
                                            foreach ($fields_tabs as $key => $args) {
                                                $active_tab = ($key === $default_tab) ? ' active' : '';
                                                ?>
                                                <li class="<?php echo esc_attr($active_tab); ?>" 
                                                    data-tab="<?php echo esc_attr($key); ?>"
                                                    >
                                                    <a href="#">
                                                        <span class="<?php echo esc_attr($args['icon']); ?>"></span>
                                                        <span><?php echo esc_html($args['label']); ?></span>
                                                    </a>
                                                </li>
                                                <?php
                                            } 
                                            ?>
                                        </ul>
                                       
                                        <div class="ppc-roles-tab-content">
                                            <table class="form-table">
                                                <?php     
                                                foreach ($fields as $key => $args) {
                                                    $args['key']   = $key;
                                                    $args['value'] = (is_array($current) && isset($current[$args['value_key']])) ? $current[$args['value_key']] : '';

                                                    self::get_rendered_role_partial($args, $current);
                                                }
                                                ?>
                                            </table>
                                        </div>                    
                                        <div class="clear"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
        
                        <div id="postbox-container-1" class="postbox-container ppc-roles-sidebar">
                            <div id="submitdiv" class="postbox">
                                <div class="inside">
                                    <div id="minor-publishing">
                                        <div id="misc-publishing-actions">
                                            <div class="misc-pub-section misc-pub-section-last" style="margin:0;">
                                                <p>
                                                    <input type="submit" 
                                                        value="<?php echo esc_attr($save_button_text); ?>" class="submit-role-form button-primary" id="publish" name="publish">
                                                </p>
                                                <p class="role-submit-response"></p>
                                            </div>
                                        </div>

                                        
                                        <div id="major-publishing-actions">
                                            <div id="publishing-action">
                                                <div class="features-counts">
                                                    <?php if (!empty($features_counts)) : ?>
                                                        <ul>
                                                            <?php foreach ($features_counts as $features_title => $features_link) : ?>
                                                                <li>
                                                                    <span class="title"><?php echo $features_title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
                                                                    <span class="link"><?php echo $features_link; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
                                                                </li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                        <hr />
                                                    <?php endif; ?>

                                                </div>
                                                <h2 class="roles-capabilities-title">
                                                <span class="title"><?php esc_html_e('Capabilities', 'capability-manager-enhanced'); ?></span>
                                                <span class="link">(<?php echo $capabilities_counts; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>)</span>
                                                </h2>
                                                <p class="description">
                                                <?php 
                                                
                                                if ($role_action === 'edit' && current_user_can('manage_capabilities') && pp_capabilities_feature_enabled('capabilities')) {
                                                    $edit_link = '<a href="' . esc_url(add_query_arg(['page' => 'pp-capabilities', 'role' => esc_attr($current_role)], admin_url('admin.php'))) .'">';
                                                    $closing_tag = '</a>';
                                                } else {
                                                    $edit_link = '';
                                                    $closing_tag = '</a>';
                                                }
                                                
                                                    printf(
                                                        esc_html__(
                                                            'These can be edited on the %1s Capabilities screen %2s', 
                                                            'capability-manager-enhanced'
                                                        ),
                                                        $edit_link,
                                                        $closing_tag
                                                    );
                                                ?>
                                                </p>
                                                <ul class="pp-roles-capabilities">
                                                <?php
                                                if($current &&  isset($current['capabilities']) && is_array($current['capabilities'])) :
                                                    ksort($current['capabilities']);
                                                    $sn = 0;
                                                    foreach ($current['capabilities'] as $cap_name => $val) :
                                                        if (0 === strpos($cap_name, 'level_')) {
                                                            continue;
                                                        }
                                                        $sn++;
                                                        $style = ($sn > 6) ? 'display:none;' : '';
                                                        ?>
                                                        <li style="<?php echo esc_attr($style);?>">
                                                            &nbsp; <?php echo esc_html($cap_name);?>
                                                        </li>
                                                    <?php endforeach; ?>

                                                    <?php if ($sn > 6) :?>
                                                    <div class="roles-capabilities-load-more">
                                                        <?php echo esc_html__('Load More', 'capability-manager-enhanced'); ?>
                                                    </div>
                                                    <div class="roles-capabilities-load-less" style="display:none;">
                                                        <?php echo esc_html__('Load Less', 'capability-manager-enhanced'); ?>
                                                    </div>
                                                    <?php endif;?>
                                                    
                                                <?php endif; ?>
                                                </ul>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br class="clear">
                    </div>
                </div>
            </form>
        </div>
        <?php
    }

    /**
     * Display admin flash notices
     */
    public function admin_notices()
    {
        pp_capabilities_roles()->notify->display();
    }

    /**
     * Handle post actions
     */
    public function handle_actions()
    {

        $actions = pp_capabilities_roles()->actions;

        $actions->handle();
    }

}
