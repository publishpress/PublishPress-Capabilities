<?php

class Pp_Roles_Actions
{

    /**
     * @var string
     */
    protected $capability = 'manage_options';

    /**
     * @var Pp_Roles_Manager
     */
    protected $manager = null;

    /**
     * @var array
     */
    protected $actions = array(
        'pp-roles-add-role',
        'pp-roles-delete-role',
    );

    /**
     * Pp_Roles_Actions constructor.
     */
    public function __construct()
    {
        $this->manager = pp_roles()->manager;

        add_action('wp_ajax_pp-roles-add-role', array($this, 'handle'));
        add_action('wp_ajax_pp-roles-delete-role', array($this, 'handle'));

    }

    /**
     * Is ajax request
     *
     * @return bool
     */
    protected function is_ajax()
    {
        return (defined('DOING_AJAX') && DOING_AJAX);
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
            return $_REQUEST['action'];
        }

        if (isset($_REQUEST['action2']) && -1 != $_REQUEST['action2']) {
            return $_REQUEST['action2'];
        }

        return false;
    }

    /**
     * Notify the user with a message. Handles ajax and post requests
     *
     * @param string $message The message to show to the user
     * @param string $type The type of message to show [error|success|warning\info]
     * @param bool $redirect If we should redirect to referrer
     */
    protected function notify($message, $type = 'error', $redirect = true)
    {

        if (!in_array($type, array('error', 'success', 'warning'))) {
            $type = 'error';
        }

        if ($this->is_ajax()) {
            $format = '<div class="notice notice-%s is-dismissible"><p>%s</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">' . __('Dismiss this notice.', 'capsman-enhanced') . '</span></button></div>';
            $format = '<div class="notice notice-%s is-dismissible"><p>%s</p></div>';
            wp_send_json_error(sprintf($format, $type, $message));
        } else {
            //enqueue message
            pp_roles()->notify->add($type, $message);

            $redirect_url = wp_get_referer();
            $redirect_url = wp_get_raw_referer();
            if (empty($redirect_url)) {
                $params = array(
                    'page' => 'capsman-pp-roles',
                );
                $redirect_url = add_query_arg($params, admin_url('admin.php'));
            }
            if ($redirect) {
                wp_safe_redirect($redirect_url);
                die();
            }
        }
    }

    /**
     * Check if the user is able to access this page
     */
    protected function check_permissions()
    {

        if (!current_user_can($this->capability)) {
            $out = __('You do not have sufficient permissions to perform this action.', 'capsman-enhanced');
            $this->notify($out);
        }
    }

    /**
     * Check nonce and notify if error
     *
     * @param string $action
     * @param string $query_arg
     */
    protected function check_nonce($action = '-1', $query_arg = '_wpnonce')
    {

        $checked = isset($_REQUEST[$query_arg]) && wp_verify_nonce($_REQUEST[$query_arg], $action);
        if (!$checked) {
            $out = __('Your link has expired, refresh the page and try again.', 'capsman-enhanced');
            $this->notify($out);
        }
    }

    /**
     * Handles add role action
     */
    public function add_role()
    {
        /**
         * Check capabilities
         */
        $this->check_permissions();

        /**
         * Check nonce
         */
        $this->check_nonce('add-role');

        /**
         * Validate input data
         */
        $role = isset($_REQUEST['role']) ? sanitize_text_field($_REQUEST['role']) : false;
        $role = str_replace(' ', '', $role); // Remove spaces
        $name = isset($_REQUEST['name']) ? sanitize_text_field($_REQUEST['name']) : false;

        if (empty($role) || empty($name)) {
            $out = __('Missing parameters, refresh the page and try again.', 'capsman-enhanced');
            $this->notify($out);
        }

        /**
         * Check role doesn't exist
         */
        if ($this->manager->is_role($role)) {
            //this role already exist
            $out = __(sprintf('The role %s already exist, use other role identifier.', "<strong>" . esc_attr($role) . "</strong>"), 'capsman-enhanced');
            $this->notify($out);
        }

        /**
         * Add role
         */
        $result = $this->manager->add_role($role, $name);

        if (!$result instanceof WP_Role) {
            $out = __('Something went wrong, the system wasn\'t able to create the role, refresh the page and try again.', 'capsman-enhanced');
            $this->notify($out);
        }

        if ($this->is_ajax()) {
            /**
             * The role row
             */
            $count_users = count_users();
            ob_start();
            $GLOBALS['hook_suffix'] = '';//avoid warning outputs
            pp_roles()->admin->get_roles_list_table()->single_row(array(
                'role' => $result->name,
                'name' => $this->manager->get_role_name($result->name),
                'count' => isset($count_users['avail_roles'][$result->name]) ? $count_users['avail_roles'][$result->name] : 0,
                'is_system' => $this->manager->is_system_role($result->name)
            ));
            $out = ob_get_clean();

            wp_send_json_success($out);
        } else {
            /**
             * Notify user and redirect
             */
            $out = __(sprintf('The new role %s was created successfully.', '<strong>' . $role . '</strong>'), 'capsman-enhanced');
            $this->notify($out, 'success');
        }
    }

    /**
     * Delete role action
     */
    public function delete_role()
    {
        /**
         * Check capabilities
         */
        $this->check_permissions();

        /**
         * Check nonce
         */
        $this->check_nonce('bulk-roles');

        /**
         * Validate input data
         */
        $roles = array();
        if (isset($_REQUEST['role'])) {
            if (is_string($_REQUEST['role'])) {
                $input = sanitize_text_field($_REQUEST['role']);
                $roles[] = $input;
            } else if (is_array($_REQUEST['role'])) {
                foreach ($_REQUEST['role'] as $key => $id) {
                    $roles[] = sanitize_text_field($id);
                }
            }
        } else {
            return;
        }

        /**
         * If no roles provided return
         */
        if (empty($roles)) {
            $out = __('Missing parameters, refresh the page and try again.', 'capsman-enhanced');
            $this->notify($out);
        }

        /**
         * Check if is a system role
         */
        foreach ($roles as $key => $role) {

            if ($this->manager->is_system_role($role)) {
                unset($roles[$key]);
            }
        }

        if (empty($roles)) {
            $out = __('Deleting a system role is not allowed.', 'capsman-enhanced');
            $this->notify($out);
        }

        /**
         * Delete roles
         */
        foreach ($roles as $role) {
            $deleted = $this->manager->delete_role($role);
        }

        $single = sprintf('The role %s was successfully deleted.', '<strong>' . $roles[0] . '</strong>');
        $plural = sprintf('The selected %s roles were successfully deleted.', '<strong>' . count($roles) . '</strong>');
        $out = _n($single, $plural, count($roles), 'capsman-enhanced');
        if ($this->is_ajax()) {
            wp_send_json_success($out);
        } else {
            $this->notify($out, 'success');
        }
    }

}
