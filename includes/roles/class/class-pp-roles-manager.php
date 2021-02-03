<?php

class Pp_Roles_Manager
{

    /**
     * Pp_Roles_Manager constructor.
     */
    public function __construct()
    {

    }

    /**
     * Returns an array of all the available roles.
     * This method is used to show the roles list table.
     *
     * @return array[]
     */
    public function get_roles_for_list_table()
    {
        $roles = get_editable_roles();
        $count = count_users();
        $res = array();
        foreach ($roles as $role => $detail) {
            $res[] = array(
                'role' => $role,
                'name' => $detail['name'],
                'count' => isset($count['avail_roles'][$role]) ? $count['avail_roles'][$role] : 0,
                'is_system' => $this->is_system_role($role)
            );
        }

        return $res;
    }

    /**
     * Array containing all default wordpress roles
     *
     * @return array
     */
    public function get_system_roles()
    {

        $roles = array(
            'administrator',
            'editor',
            'author',
            'contributor',
            'subscriber',
            'revisor'
        );

        $roles = apply_filters('pp-roles-get-system-roles', $roles);

        return $roles;
    }

    /**
     * Checks if the given role is a system role
     *
     * @param $role
     *
     * @return bool
     */
    public function is_system_role($role)
    {

        $is = in_array($role, $this->get_system_roles());

        $is = apply_filters('pp-roles-is-system-role', $is, $role);

        return $is;
    }

    /**
     * Checks if he provided role exist
     *
     * @param $role
     *
     * @return bool
     */
    public function is_role($role)
    {
        return wp_roles()->is_role($role);
    }

    /**
     * Get role object from role
     *
     * @param $role
     *
     * @return WP_Role|null
     */
    public function get_role($role)
    {
        return wp_roles()->get_role($role);
    }

    /**
     * Get role name string form a role
     *
     * @param $role
     *
     * @return string
     */
    public function get_role_name($role)
    {
        if ($this->is_role($role)) {
            return wp_roles()->role_names[$role];
        }

        return $role;
    }

    /**
     * Add role to the system
     *
     * @param $role
     * @param $name
     *
     * @return WP_Role|null
     */
    public function add_role($role, $name)
    {
        $result = add_role($role, $name);

        return $result;
    }

    /**
     * Deletes a role from the system
     *
     * @param $role
     *
     * @return bool
     */
    public function delete_role($role)
    {
        remove_role($role);

        return !$this->is_role($role);
    }

}
