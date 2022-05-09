<?php

class Pp_Roles_Admin
{

    /**
     * The capability
     *
     * @access   protected
     * @var string
     */
    protected $capability = 'manage_options';

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
        $fields_tabs = [
            'general'     => [
                'label'    => esc_html__('General', 'capsman-enhanced'),
                'icon'     => 'dashicons dashicons-admin-tools',
            ],
            'advanced'  => [
                'label' => esc_html__('Advanced', 'capsman-enhanced'),
                'icon'     => 'dashicons dashicons-admin-generic',
            ],
        ];

        if ($role_edit && !$current['is_system']) {
            $fields_tabs['delete'] = [
                'label'    => esc_html__('Delete', 'capsman-enhanced'),
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
        $fields = [
            'role_name'      => [
                'label'     => esc_html__('Role Name', 'capsman-enhanced'),
                'type'      => 'text',
                'value_key' => 'name',
                'tab'       => 'general',
                'editable'  => true,
                'required'  => true,
            ],
            'role_slug'      => [
                'label'     => esc_html__('Role Slug', 'capsman-enhanced'),
                'description' => esc_html__('The "slug" is the URL-friendly version of the role. It is usually all lowercase and contains only letters, numbers and underscores.', 'capsman-enhanced'),
                'type'      => 'text',
                'value_key' => 'role',
                'tab'       => 'general',
                'editable'  => ($role_edit) ? false : true,
                'required'  => false,
            ],
            'role_level'     => [
                'label'     => esc_html__('Role Level', 'capsman-enhanced'),
                'description' => esc_html__('Each user role has a level from 0 to 10. The Subscriber role defaults to the lowest level (0). The Administrator role defaults to level 10.', 'capsman-enhanced'),
                'type'      => 'select',
                'value_key' => '',
                'tab'       => 'advanced',
                'editable'  => true,
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
                'selected'  => (is_array($current) && isset($current['capabilities'])) ? ak_caps2level($current['capabilities']) : '0',
            ],
            'delete_role'     => [
                'label'       => esc_html__('Delete role', 'capsman-enhanced'),
                'description' => esc_html__('Deleting this role will completely remove it from database and is irrecoverable.', 'capsman-enhanced'),
                'type'      => 'button',
                'value_key' => '',
                'tab'       => 'delete',
                'editable'  => true,
            ],
        ];
        
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
     */
    private static function get_rendered_role_partial($args)
    {
        $defaults = [
            'description' => '',
            'type'        => 'text',
            'tab'         => 'general',
            'editable'    => true,
            'required'    => false,
            'value'       => '',
            'options'     => [],
            'selected'    => '',
            'label'       => '',
        ];
        $args      = array_merge($defaults, $args);
        $key       = $args['key'];
        $tab_class = 'pp-roles-tab-tr pp-roles-' . $args['tab'] . '-tab';
        $tab_style = ($args['tab'] === 'general') ? '' : 'display:none;';
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
                        <?php esc_html_e('Slug already exists', 'capsman-enhanced'); ?>
                        <span class="dashicons dashicons-warning"></span>
                    </p>
                <?php } ?>
            </th>
            <td>
                <?php 
                if ($args['type'] === 'select') : ?>
                    <select name="<?php echo esc_attr($key); ?>" <?php echo ($args['required'] ? 'required="true"' : '');?>>
                        <?php
                        foreach ($args['options'] as $select_key => $select_label) {
                            ?>
                            <option value="<?php esc_attr_e($select_key); ?>"
                                    <?php selected($select_key, $args['selected']); ?>>
                                    <?php echo esc_html($select_label); ?>
                            </option>
                        <?php } ?>
                    </select>
                    <?php if (isset($args['description'])) : ?>
                        <p class="description">
                            <?php echo esc_html($args['description']); ?>
                            <?php if ($key === 'role_level') : ?>
                                <a href="https://publishpress.com/blog/user-role-levels/" target="blank">
                                    <?php esc_html_e('Read more on Role Level.',  'capsman-enhanced'); ?>
                                </a>
                            <?php endif; ?>
                        </p>
                    <?php endif; ?>
                <?php
                elseif ($args['type'] === 'button') :
                    ?>
                    <input type="submit" 
                            class="button-secondary pp-roles-delete-botton" 
                            name="<?php echo esc_attr($key); ?>"
                            value="<?php echo esc_attr($args['label']); ?>"
                            onclick="return confirm('<?php esc_attr_e('Are you sure you want to delete this role?',  'capsman-enhanced'); ?>');"
                             />
                        <?php if (isset($args['description'])) : ?>
                            <p class="description" style="color: red;"><?php echo esc_html($args['description']); ?></p>
                        <?php endif; ?>
                <?php else : ?>
                    <input name="<?php echo esc_attr($key); ?>" type="<?php echo esc_attr($args['type']); ?>"
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
        
        $fields_tabs  = apply_filters('pp_roles_fields_tabs', self::get_fields_tabs($current, $role_edit, $role_copy), $current, $role_edit, $role_copy);
        $fields       = apply_filters('pp_roles_fields', self::get_fields($current, $role_edit, $role_copy), $current, $role_edit, $role_copy);

        if ($role_copy) {
            pp_capabilities_roles()->notify->add('info', sprintf( esc_html__('%s role copied to editor. Please click the "Create Role" button to create this new role.', 'capsman-enhanced'), $current['name']));
            //update new name and remove slug
            $current['role'] = $current['role'] . '_copy';
            $current['name'] = $current['name'] . ' Copy';
        }

        $save_button_text = ($role_edit) ? esc_html__('Update Role', 'capsman-enhanced') : esc_html__('Create Role', 'capsman-enhanced');

        pp_capabilities_roles()->notify->display();
        ?>
        <div class="wrap pp-role-edit-wrap <?php echo esc_attr($tab_class); ?>">
            <h1>
            <?php 
            if ($role_edit) {
                esc_html_e('Edit Role', 'capsman-enhanced');
            } elseif ($role_copy) {
                esc_html_e('Copy Role', 'capsman-enhanced');
            } else {
                esc_html_e('Create New Role', 'capsman-enhanced');
            }
            ?>
            <a href="<?php echo esc_url(admin_url('admin.php?page=pp-capabilities-roles')); ?>" class="page-title-action">
                <?php esc_html_e('All Roles', 'capsman-enhanced'); ?>
            </a>
            </h1>
            <div class="wp-clearfix"></div>

            <form method="post" action=""> 
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
                                                <li class="<?php esc_attr_e($active_tab); ?>" 
                                                    data-tab="<?php esc_attr_e($key); ?>"
                                                    >
                                                    <a href="#">
                                                        <span class="<?php esc_attr_e($args['icon']); ?>"></span>
                                                        <span><?php esc_html_e($args['label']); ?></span>
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

                                                    self::get_rendered_role_partial($args);
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
                                                        value="<?php echo esc_attr($save_button_text); ?>" class="button-primary" id="publish" name="publish">
                                                </p>
                                            </div>
                                        </div>

                                        
                                        <div id="major-publishing-actions">
                                            <div id="publishing-action">
                                                <h2 class="roles-capabilities-title"><?php esc_html_e('Capabilities', 'capsman-enhanced'); ?></h2>
                                                <p class="description">
                                                <?php 
                                                    printf(
                                                        esc_html__(
                                                            'These can be edited on the %1s Capabilities screen %2s', 
                                                            'capsman-enhanced'
                                                        ),
                                                        ($role_action === 'edit') ? '<a href="' . esc_url(add_query_arg(['page' => 'pp-capabilities', 'role' => esc_attr($current_role)], admin_url('admin.php'))) .'">' : '',
                                                        ($role_action === 'edit') ? '</a>' : ''
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
                                                        <?php echo esc_html__('Load More', 'capsman-enhanced'); ?>
                                                    </div>
                                                    <div class="roles-capabilities-load-less" style="display:none;">
                                                        <?php echo esc_html__('Load Less', 'capsman-enhanced'); ?>
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
