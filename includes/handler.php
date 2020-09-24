<?php
class CapsmanHandler
{
	var $cm;

	function __construct( $manager_obj ) {
		$this->cm = $manager_obj;
	}
	
	function processAdminGeneral( $post ) {
		global $wp_roles;
		
		do_action('publishpress-caps_process_update');

		// Create a new role.
		if ( ! empty($post['CreateRole']) ) {
			if ( $newrole = $this->createRole($post['create-name']) ) {
				ak_admin_notify(__('New role created.', 'capsman-enhanced'));
				$this->cm->current = $newrole;
			} else {
				if ( empty($post['create-name']) && ( ! defined('WPLANG') || ! WPLANG ) )
					ak_admin_error( 'Error: No role name specified.', 'capsman-enhanced' );
				else
					ak_admin_error(__('Error: Failed creating the new role.', 'capsman-enhanced'));
			}

		// rename role
		} elseif (!empty($post['RenameRole']) && !empty($post['rename-name'])) {
			$current = get_role($post['current']);
			$new_title = sanitize_text_field($post['rename-name']);

			if ($current && isset($wp_roles->roles[$current->name]) && $new_title) {
				$old_title = $wp_roles->roles[$current->name]['name'];
				$wp_roles->roles[$current->name]['name'] = $new_title;
				update_option($wp_roles->role_key, $wp_roles->roles);

				ak_admin_notify(sprintf(__('Role "%s" (id %s) renamed to "%s"', 'capsman-enhanced'), $old_title, strtolower($current->name), $new_title));
				$this->cm->current = $current->name;
			}
		// Copy current role to a new one.
		} elseif ( ! empty($post['CopyRole']) ) {
			$current = get_role($post['current']);
			if ( $newrole = $this->createRole($post['copy-name'], $current->capabilities) ) {
				ak_admin_notify(__('New role created.', 'capsman-enhanced'));
				$this->cm->current = $newrole;
			} else {
				if ( empty($post['copy-name']) && ( ! defined('WPLANG') || ! WPLANG ) )
					ak_admin_error( 'Error: No role name specified.', 'capsman-enhanced' );
				else
					ak_admin_error(__('Error: Failed creating the new role.', 'capsman-enhanced'));
			}

		// Save role changes. Already saved at start with self::saveRoleCapabilities().
		} elseif ( ! empty($post['SaveRole']) ) {
			if ( MULTISITE ) {
				global $wp_roles;
				( method_exists( $wp_roles, 'for_site' ) ) ? $wp_roles->for_site() : $wp_roles->reinit();
			}
			
			$this->saveRoleCapabilities($post['current'], $post['caps'], $post['level']);
			
			if ( defined( 'PRESSPERMIT_ACTIVE' ) ) {  // log customized role caps for subsequent restoration
				// for bbPress < 2.2, need to log customization of roles following bbPress activation
				$plugins = ( function_exists( 'bbp_get_version' ) && version_compare( bbp_get_version(), '2.2', '<' ) ) ? array( 'bbpress.php' ) : array();	// back compat

				if ( ! $customized_roles = get_option( 'pp_customized_roles' ) )
					$customized_roles = array();
				
				$customized_roles[$post['role']] = (object) array( 'caps' => array_map( 'boolval', $post['caps'] ), 'plugins' => $plugins );
				update_option( 'pp_customized_roles', $customized_roles );
				
				global $wpdb;
				$wpdb->query( "UPDATE $wpdb->options SET autoload = 'no' WHERE option_name = 'pp_customized_roles'" );
			}
		// Create New Capability and adds it to current role.
		} elseif ( ! empty($post['AddCap']) ) {
			if ( MULTISITE ) {
				global $wp_roles;
				( method_exists( $wp_roles, 'for_site' ) ) ? $wp_roles->for_site() : $wp_roles->reinit();
			}
			
			$role = get_role($post['current']);
			$role->name = $post['current'];		// bbPress workaround

			if ( $newname = $this->createNewName($post['capability-name']) ) {
				$role->add_cap($newname['name']);

				// for bbPress < 2.2, need to log customization of roles following bbPress activation
				$plugins = ( function_exists( 'bbp_get_version' ) && version_compare( bbp_get_version(), '2.2', '<' ) ) ? array( 'bbpress.php' ) : array();	// back compat
				
				if ( ! $customized_roles = get_option( 'pp_customized_roles' ) )
					$customized_roles = array();

				$customized_roles[$post['role']] = (object) array( 'caps' => array_merge( $role->capabilities, array( $newname['name'] => 1 ) ), 'plugins' => $plugins );
				update_option( 'pp_customized_roles', $customized_roles );
				
				global $wpdb;
				$wpdb->query( "UPDATE $wpdb->options SET autoload = 'no' WHERE option_name = 'pp_customized_roles'" );

				$url = admin_url('admin.php?page=capsman&role=' . $post['role'] . '&added=1');
				wp_redirect($url);
				exit;
			} else {
				ak_admin_notify(__('Incorrect capability name.'));
			}
			
		} elseif ( ! empty($post['update_filtered_types']) || ! empty($post['update_filtered_taxonomies']) || ! empty($post['update_detailed_taxonomies']) ) {
			//if ( /*  settings saved successfully on plugins_loaded action  */ ) {
				ak_admin_notify(__('Type / Taxonomy settings saved.', 'capsman-enhanced'));
			//} else {
			//	ak_admin_error(__('Error saving capability settings.', 'capsman-enhanced'));
			//}
		} else {
			if (!apply_filters('publishpress-caps_submission_ok', false)) {
				ak_admin_error(__('Bad form received.', 'capsman-enhanced'));
			}
		}

		if ( ! empty($newrole) && defined('PRESSPERMIT_ACTIVE') ) {
			if ( ( ! empty($post['CreateRole']) && ! empty( $_REQUEST['new_role_pp_only'] ) ) || ( ! empty($post['CopyRole']) && ! empty( $_REQUEST['copy_role_pp_only'] ) ) ) {
				$pp_only = (array) pp_capabilities_get_permissions_option( 'supplemental_role_defs' );
				$pp_only[]= $newrole;

				pp_capabilities_update_permissions_option('supplemental_role_defs', $pp_only);
				
				_cme_pp_default_pattern_role( $newrole );
				pp_refresh_options();
			}
		}
	}

	
	/**
	 * Creates a new role/capability name from user input name.
	 * Name rules are:
	 * 		- 2-40 charachers lenght.
	 * 		- Only letters, digits, spaces and underscores.
	 * 		- Must to start with a letter.
	 *
	 * @param string $name	Name from user input.
	 * @return array|false An array with the name and display_name, or false if not valid $name.
	 */
	private function createNewName( $name ) {
		// Allow max 40 characters, letters, digits and spaces
		$name = trim(substr($name, 0, 40));
		$pattern = '/^[a-zA-Z][a-zA-Z0-9 _]+$/';

		if ( preg_match($pattern, $name) ) {
			$roles = ak_get_roles();

			$name = str_replace(' ', '_', $name);
			if ( in_array($name, $roles) || array_key_exists($name, $this->cm->capabilities) ) {
				return false;	// Already a role or capability with this name.
			}

			$display = explode('_', $name);
			$name = strtolower($name);

			// Apply ucfirst proper caps unless capitalization already provided
			foreach($display as $i => $word) {
				if ($word === strtolower($word)) {
					$display[$i] = ucfirst($word);
				}
			}

			$display = implode(' ', $display);

			return compact('name', 'display');
		} else {
			return false;
		}
	}

	/**
	 * Creates a new role.
	 *
	 * @param string $name	Role name to create.
	 * @param array $caps	Role capabilities.
	 * @return string|false	Returns the name of the new role created or false if failed.
	 */
	private function createRole( $name, $caps = array() ) {
		if ( ! is_array($caps) )
			$caps = array();

		$role = $this->createNewName($name);
		if ( ! is_array($role) ) {
			return false;
		}

		$new_role = add_role($role['name'], $role['display'], $caps);
		if ( is_object($new_role) ) {
			return $role['name'];
		} else {
			return false;
		}
	}

	 /**
	  * Saves capability changes to roles.
	  *
	  * @param string $role_name Role name to change its capabilities
	  * @param array $caps New capabilities for the role.
	  * @return void
	  */
	private function saveRoleCapabilities( $role_name, $caps, $level ) {
		$this->cm->generateNames();
		$role = get_role($role_name);

		// workaround to ensure db storage of customizations to bbp dynamic roles
		$role->name = $role_name;
		
		$stored_role_caps = ( ! empty($role->capabilities) && is_array($role->capabilities) ) ? array_intersect( $role->capabilities, array(true, 1) ) : array();
		$stored_negative_role_caps = ( ! empty($role->capabilities) && is_array($role->capabilities) ) ? array_intersect( $role->capabilities, array(false) ) : array();
		
		$old_caps = array_intersect_key( $stored_role_caps, $this->cm->capabilities);
		$new_caps = ( is_array($caps) ) ? array_map('boolval', $caps) : array();
		$new_caps = array_merge($new_caps, ak_level2caps($level));

		// Find caps to add and remove
		$add_caps = array_diff_key($new_caps, $old_caps);
		$del_caps = array_diff_key(array_merge($old_caps, $stored_negative_role_caps), $new_caps);

		$changed_caps = array();
		foreach( array_intersect_key( $new_caps, $old_caps ) as $cap_name => $cap_val ) {
			if ( $new_caps[$cap_name] != $old_caps[$cap_name] )
				$changed_caps[$cap_name] = $cap_val;
		}
		
		$add_caps = array_merge( $add_caps, $changed_caps );
		
		if ( ! $is_administrator = current_user_can('administrator') ) {
			unset($add_caps['manage_capabilities']);
			unset($del_caps['manage_capabilities']);
		}

		if ( 'administrator' == $role_name && isset($del_caps['manage_capabilities']) ) {
			unset($del_caps['manage_capabilities']);
			ak_admin_error(__('You cannot remove Manage Capabilities from Administrators', 'capsman-enhanced'));
		}
		
		// additional safeguard against removal of read capability
		if ( isset( $del_caps['read'] ) && _cme_is_read_removal_blocked( $role_name ) ) {
			unset( $del_caps['read'] );
		}
		
		// Add new capabilities to role
		foreach ( $add_caps as $cap => $grant ) {
			if ( $is_administrator || current_user_can($cap) )
				$role->add_cap( $cap, $grant );
		}

		// Remove capabilities from role
		foreach ( $del_caps as $cap => $grant) {
			if ( $is_administrator || current_user_can($cap) )
				$role->remove_cap($cap);
		}
		
		$this->cm->log_db_roles();
		
		if (is_multisite() && is_super_admin() && is_main_site()) {
			if ( ! $autocreate_roles = get_site_option( 'cme_autocreate_roles' ) )
				$autocreate_roles = array();
			
			$this_role_autocreate = ! empty($_REQUEST['cme_autocreate_role']);
			
			if ( $this_role_autocreate && ! in_array( $role_name, $autocreate_roles ) ) {
				$autocreate_roles []= $role_name;
				update_site_option( 'cme_autocreate_roles', $autocreate_roles );
			}
			
			if ( ! $this_role_autocreate && in_array( $role_name, $autocreate_roles ) ) {
				$autocreate_roles = array_diff( $autocreate_roles, array( $role_name ) );
				update_site_option( 'cme_autocreate_roles', $autocreate_roles );
			}
			
			if ( ! empty($_REQUEST['cme_net_sync_role']) ) {
				// loop through all sites on network, creating or updating role def
		
				global $wpdb, $wp_roles, $blog_id;
				$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs ORDER BY blog_id" );
				$orig_blog_id = $blog_id;	
		
				$role_caption = $wp_roles->role_names[$role_name];
				
				$new_caps = ( is_array($caps) ) ? array_map('boolval', $caps) : array();
				$new_caps = array_merge($new_caps, ak_level2caps($level) );
				
				$admin_role = $wp_roles->get_role('administrator');
				$main_admin_caps = array_merge( $admin_role->capabilities, ak_level2caps(10) );

				foreach ( $blog_ids as $id ) {				
					if ( 1 == $id )
						continue;
					
					switch_to_blog( $id );
					( method_exists( $wp_roles, 'for_site' ) ) ? $wp_roles->for_site() : $wp_roles->reinit();
					
					if ( $blog_role = $wp_roles->get_role( $role_name ) ) {
						$stored_role_caps = ( ! empty($blog_role->capabilities) && is_array($blog_role->capabilities) ) ? array_intersect( $blog_role->capabilities, array(true, 1) ) : array();
						
						$old_caps = array_intersect_key( $stored_role_caps, $this->cm->capabilities);

						// Find caps to add and remove
						$add_caps = array_diff_key($new_caps, $old_caps);
						$del_caps = array_intersect_key( array_diff_key($old_caps, $new_caps), $main_admin_caps );	// don't mess with caps that are totally unused on main site
						
						// Add new capabilities to role
						foreach ( $add_caps as $cap => $grant ) {
							$blog_role->add_cap( $cap, $grant );
						}

						// Remove capabilities from role
						foreach ( $del_caps as $cap => $grant) {
							$blog_role->remove_cap($cap);
						}
						
					} else {
						$wp_roles->add_role( $role_name, $role_caption, $new_caps );
					}
					
					restore_current_blog();
				}
				
				( method_exists( $wp_roles, 'for_site' ) ) ? $wp_roles->for_site() : $wp_roles->reinit();
			}
		} // endif multisite installation with super admin editing a main site role

		pp_capabilities_autobackup();
	}
	


	/**
	 * Deletes a role.
	 * The role comes from the $_GET['role'] var and the nonce has already been checked.
	 * Default WordPress role cannot be deleted and if trying to do it, throws an error.
	 * Users with the deleted role, are moved to the WordPress default role.
	 *
	 * @return void
	 */
	function adminDeleteRole ()
	{
		global $wpdb, $wp_roles;

		check_admin_referer('delete-role_' . $_GET['role']);
		
		$this->cm->current = $_GET['role'];
		$default = get_option('default_role');
		if (  $default == $this->cm->current ) {
			ak_admin_error(sprintf(__('Cannot delete default role. You <a href="%s">have to change it first</a>.', 'capsman-enhanced'), 'options-general.php'));
			return;
		}

		$like = $wpdb->esc_like( $this->cm->current );

		$query = $wpdb->prepare( "SELECT ID FROM {$wpdb->usermeta} INNER JOIN {$wpdb->users} "
			. "ON {$wpdb->usermeta}.user_id = {$wpdb->users}.ID "
			. "WHERE meta_key='{$wpdb->prefix}capabilities' AND meta_value LIKE %s", $like );

		$users = $wpdb->get_results($query);

		// Array of all roles except the one being deleted, for use below
		$role_names = array_diff_key( array_keys( $wp_roles->role_names ), array( $this->cm->current => true ) );
		
		$count = 0;
		foreach ( $users as $u ) {
			$skip_role_set = false;
		
			$user = new WP_User($u->ID);
			if ( $user->has_cap($this->cm->current) ) {		// Check again the user has the deleting role
				
				// Role may have been assigned supplementally.  Don't move a user to default role if they still have one or more roles following the deletion.
				foreach( $role_names as $_role_name ) {
					if ( $user->has_cap($_role_name) ) {
						$skip_role_set = true;
						break;
					}
				}
				
				if ( ! $skip_role_set ) {
					$user->set_role($default);
					$count++;
				}
			}
		}

		remove_role($this->cm->current);
		unset($this->cm->roles[$this->cm->current]);

		if ( $customized_roles = get_option( 'pp_customized_roles' ) ) {
			if ( isset( $customized_roles[$this->cm->current] ) ) {
				unset( $customized_roles[$this->cm->current] );
				update_option( 'pp_customized_roles', $customized_roles );
			}
		}
		
		ak_admin_notify(sprintf(__('Role has been deleted. %1$d users moved to default role %2$s.', 'capsman-enhanced'), $count, $this->cm->roles[$default]));
		$this->cm->current = $default;
	}
}

if ( ! function_exists('boolval') ) {
	function boolval( $val ) {
		return (bool) $val;
	}
}
