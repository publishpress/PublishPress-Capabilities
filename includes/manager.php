<?php
/**
 * Capability Manager.
 * Plugin to create and manage roles and capabilities.
 *
 * @author		Jordi Canals, Kevin Behrens
 * @copyright   Copyright (C) 2009, 2010 Jordi Canals, (C) 2019 PublishPress
 * @license		GNU General Public License version 2
 * @link		https://publishpress.com
 *

	Copyright 2009, 2010 Jordi Canals <devel@jcanals.cat>
	Modifications Copyright 2019, PublishPress <help@publishpress.com>

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	version 2 as published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

add_action( 'init', 'cme_update_pp_usage' );  // update early so resulting post type cap changes are applied for this request's UI construction

function cme_update_pp_usage() {
	if ( ! empty($_REQUEST['update_filtered_types']) || ! empty($_REQUEST['update_filtered_taxonomies']) || ! empty($_REQUEST['update_detailed_taxonomies']) || ! empty($_REQUEST['SaveRole']) ) {
		require_once( dirname(__FILE__).'/pp-handler.php' );
		return _cme_update_pp_usage();
	}
}

// Core WP roles to apply safeguard preventing accidental lockout from dashboard
function _cme_core_roles() {
	return apply_filters( 'pp_caps_core_roles', array( 'administrator', 'editor', 'revisor', 'author', 'contributor', 'subscriber' ) );
}

function _cme_core_caps() {
	$core_caps = array_fill_keys( array( 'switch_themes', 'edit_themes', 'activate_plugins', 'edit_plugins', 'edit_users', 'edit_files', 'manage_options', 'moderate_comments', 
	'manage_links', 'upload_files', 'import', 'unfiltered_html', 'read', 'delete_users', 'create_users', 'unfiltered_upload', 'edit_dashboard',
	'update_plugins', 'delete_plugins', 'install_plugins', 'update_themes', 'install_themes', 
	'update_core', 'list_users', 'remove_users', 'promote_users', 'edit_theme_options', 'delete_themes', 'export' ), true );
	
	ksort( $core_caps );
	return $core_caps;
}

function _cme_is_read_removal_blocked( $role_name ) {
	$role = get_role($role_name);
	$rcaps = $role->capabilities;
	
	$core_caps = array_diff_key( _cme_core_caps(), array_fill_keys( array( 'unfiltered_html', 'unfiltered_upload', 'upload_files', 'edit_files', 'read' ), true ) );
	
	if ( empty( $rcaps['dashboard_lockout_ok'] ) ) {
		$edit_caps = array();
		foreach ( get_post_types( array( 'public' => true ), 'object' ) as $type_obj ) {
			$edit_caps = array_merge( $edit_caps, array_values( array_diff_key( (array) $type_obj->cap, array( 'read_private_posts' => true ) ) ) );
		}
		
		$edit_caps = array_fill_keys( $edit_caps, true );
		unset( $edit_caps['read'] );
		unset( $edit_caps['upload_files'] );
		unset( $edit_caps['edit_files'] );
		
		if ( $role_has_admin_caps = in_array( $role_name, _cme_core_roles() ) && ( array_intersect_key( $rcaps, array_diff_key( $core_caps, array( 'read' => true ) ) ) || array_intersect_key( $rcaps, $edit_caps ) ) ) {
			return true;
		}
	}
	
	return false;
}

/**
 * Class CapabilityManager.
 * Sets the main environment for all Capability Manager components.
 *
 * @author		Jordi Canals, Kevin Behrens
 * @link		https://publishpress.com
 */
class CapabilityManager
{
	/**
	 * Array with all capabilities to be managed. (Depends on user caps).
	 * The array keys are the capability, the value is its screen name.
	 * @var array
	 */
	var $capabilities = array();

	/**
	 * Array with roles that can be managed. (Depends on user roles).
	 * The array keys are the role name, the value is its translated name.
	 * @var array
	 */
	var $roles = array();
	
	/**
	 * Current role we are managing
	 * @var string
	 */
	var $current;

	/**
	 * Maximum level current manager can assign to a user.
	 * @var int
	 */
	private $max_level;

	private $log_db_role_objects = array();
	
	var $message;
	
	/**
	 * Module ID. Is the module internal short name.
	 *
	 * @var string
	 */
	public $ID;

	public function __construct()
	{
		$this->ID = 'capsman';
		$this->mod_url = plugins_url( '', CME_FILE );
		
		$this->moduleLoad();
		
		add_action('admin_menu', array($this, 'adminMenus'), 5);  // execute prior to PP, to use menu hook

		// Load styles
		add_action('admin_print_styles', array($this, 'adminStyles'));

		if ( isset($_REQUEST['page']) && ( 'capsman' == $_REQUEST['page'] ) ) {
			add_action('admin_enqueue_scripts', array($this, 'adminScriptsPP'));
		}
	}

    /**
     * Enqueues administration styles.
     *
     * @hook action 'admin_print_styles'
	 *
     * @return void
     */
    function adminStyles()
    {
		if ( empty( $_REQUEST['page'] ) || ! in_array( $_REQUEST['page'], array( 'capsman', 'capsman-tool' ) ) )
			return;
		
		wp_enqueue_style('revisionary-admin-common', $this->mod_url . '/common/css/pressshack-admin.css', [], CAPSMAN_ENH_VERSION);

		wp_register_style( $this->ID . 'framework_admin', $this->mod_url . '/framework/styles/admin.css', false, CAPSMAN_ENH_VERSION);
   		wp_enqueue_style( $this->ID . 'framework_admin');
		
   		wp_register_style( $this->ID . '_admin', $this->mod_url . '/admin.css', false, CAPSMAN_ENH_VERSION);
   		wp_enqueue_style( $this->ID . '_admin');
		
		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '.dev' : '';
		$url = $this->mod_url . "/admin{$suffix}.js";
		wp_enqueue_script( 'cme_admin', $url, array('jquery'), CAPSMAN_VERSION, true );
		wp_localize_script( 'cme_admin', 'cmeAdmin', array( 
			'negationCaption' => __( 'Explicity negate this capability by storing as disabled', 'capsman-enhanced' ),
			'typeCapsNegationCaption' => __( 'Explicitly negate these capabilities by storing as disabled', 'capsman-enhanced' ),
			'typeCapUnregistered' => __( 'Post type registration does not define this capability distinctly', 'capsman-enhanced' ),
			'capNegated' => __( 'This capability is explicitly negated. Click to add/remove normally.', 'capsman-enhanced' ), 
			'chkCaption' => __( 'Add or remove this capability from the WordPress role', 'capsman-enhanced' ), 
			'switchableCaption' => __( 'Add or remove capability from the role normally', 'capsman-enhanced' ) ) 
		);
    }
	
	function adminScriptsPP() {
		wp_enqueue_style( 'plugin-install' );
		wp_enqueue_script( 'plugin-install' );
		add_thickbox();
	}

	/**
	 * Creates some filters at module load time.
	 *
	 * @return void
	 */
    protected function moduleLoad ()
    {
		$old_version = get_option($this->ID . '_version');
		if ( version_compare( $old_version, CAPSMAN_ENH_VERSION, 'ne') ) {
			update_option($this->ID . '_version', CAPSMAN_ENH_VERSION);
			$this->pluginUpdate();
		}
		
        // Only roles that a user can administer can be assigned to others.
        add_filter('editable_roles', array($this, 'filterEditRoles'));

        // Users with roles that cannot be managed, are not allowed to be edited.
        add_filter('map_meta_cap', array(&$this, 'filterUserEdit'), 10, 4);
		
		// ensure storage, retrieval of db-stored customizations to dynamic roles
		if ( isset($_REQUEST['page']) && in_array( $_REQUEST['page'], array( 'capsman', 'capsman-tool' ) ) ) {
			global $wpdb;
			$role_key = $wpdb->prefix . 'user_roles';
			$this->log_db_roles();
			add_filter( 'option_' . $role_key, array( &$this, 'reinstate_db_roles' ), PHP_INT_MAX );
		}
		
		add_filter( 'plugins_loaded', array( &$this, 'processRoleUpdate' ) );
    }

	// Direct query of stored role definitions
	function log_db_roles( $legacy_arg = '' ) {
		global $wpdb;

		$results = (array) maybe_unserialize( $wpdb->get_var("SELECT option_value FROM $wpdb->options WHERE option_name = '{$wpdb->prefix}user_roles' LIMIT 1") );
		foreach( $results as $_role_name => $_role ) {
			$this->log_db_role_objects[$_role_name] = (object) $_role;
		}

		return $legacy_arg;
	}

	// note: this is only applied when accessing the cme role edit form
	function reinstate_db_roles( $passthru_roles = array() ) {
		global $wp_roles;

		if ( isset($wp_roles) && $this->log_db_role_objects ) {
			$intersect = array_intersect_key( $wp_roles->role_objects, $this->log_db_role_objects );
			foreach( array_keys( $intersect ) as $key ) {
				if ( ! empty( $this->log_db_role_objects[$key]->capabilities ) )
					$wp_roles->role_objects[$key]->capabilities = $this->log_db_role_objects[$key]->capabilities;
			}
		}
		
		return $passthru_roles;
	}

	/**
	 * Updates Capability Manager to a new version
	 *
	 * @return void
	 */
	protected function pluginUpdate ()
	{
		$backup = get_option($this->ID . '_backup');
		if ( false === $backup ) {		// No previous backup found. Save it!
			global $wpdb;
			$roles = get_option($wpdb->prefix . 'user_roles');
			update_option( $this->ID . '_backup', $roles, false );
			update_option( $this->ID . '_backup_datestamp', current_time( 'timestamp' ), false );
		}
	}

	/**
	 * Adds admin panel menus. (At plugins loading time. This is before plugins_loaded).
	 * User needs to have 'manage_capabilities' to access this menus.
	 * This is set as an action in the parent class constructor.
	 *
	 * @hook action admin_menu
	 * @return void
	 */
	public function adminMenus ()
	{
		// First we check if user is administrator and can 'manage_capabilities'.
		if ( current_user_can('administrator') && ! current_user_can('manage_capabilities') ) {
			$this->setAdminCapability();
		}

		add_action( 'admin_menu', array( &$this, 'cme_menu' ), 20 );
	}

	public function cme_menu() {
		$cap_name = ( is_super_admin() ) ? 'manage_capabilities' : 'restore_roles';
		add_management_page(__('Capability Manager', 'capsman-enhanced'),  __('Capability Manager', 'capsman-enhanced'), $cap_name, $this->ID . '-tool', array($this, 'backupTool'));
		
		if ( did_action( 'pp_admin_menu' ) ) { // Put Capabilities link on Permissions menu if Press Permit is active and user has access to it
			global $pp_admin;
			$menu_caption = ( defined('WPLANG') && WPLANG && ( 'en_EN' != WPLANG ) ) ? __('Capabilities', 'capsman-enhanced') : 'Role Capabilities';
			add_submenu_page( $pp_admin->get_menu('options'), __('Capability Manager', 'capsman-enhanced'),  $menu_caption, 'manage_capabilities', $this->ID, array($this, 'generalManager') );
		
		} elseif(did_action('presspermit_admin_menu') && function_exists('presspermit')) {
			$menu_caption = ( defined('WPLANG') && WPLANG && ( 'en_EN' != WPLANG ) ) ? __('Capabilities', 'capsman-enhanced') : 'Role Capabilities';
			add_submenu_page( presspermit()->admin()->getMenuParams('options'), __('Capability Manager', 'capsman-enhanced'),  $menu_caption, 'manage_capabilities', $this->ID, array($this, 'generalManager') );
		
		} else {
			add_users_page( __('Capability Manager', 'capsman-enhanced'),  __('Capabilities', 'capsman-enhanced'), 'manage_capabilities', $this->ID, array($this, 'generalManager'));
		}	
	}
	
	/**
	 * Sets the 'manage_capabilities' cap to the administrator role.
	 *
	 * @return void
	 */
	public function setAdminCapability ()
	{
		$admin = get_role('administrator');
		$admin->add_cap('manage_capabilities');
	}

	/**
	 * Filters roles that can be shown in roles list.
	 * This is mainly used to prevent an user admin to create other users with
	 * higher capabilities.
	 *
	 * @hook 'editable_roles' filter.
	 *
	 * @param $roles List of roles to check.
	 * @return array Restircted roles list
	 */
	function filterEditRoles ( $roles )
	{
	    $this->generateNames();
        $valid = array_keys($this->roles);

        foreach ( $roles as $role => $caps ) {
            if ( ! in_array($role, $valid) ) {
                unset($roles[$role]);
            }
        }

        return $roles;
	}

	/**
	 * Checks if a user can be edited or not by current administrator.
	 * Returns array('do_not_allow') if user cannot be edited.
	 *
	 * @hook 'map_meta_cap' filter
	 *
	 * @param array $caps Current user capabilities
	 * @param string $cap Capability to check
	 * @param int $user_id Current user ID
	 * @param array $args For our purpose, we receive edited user id at $args[0]
	 * @return array Allowed capabilities.
	 */
	function filterUserEdit ( $caps, $cap, $user_id, $args )
	{
	    if ( ! in_array( $cap, array( 'edit_user', 'delete_user', 'promote_user', 'remove_user' ) ) || ( ! isset($args[0]) ) || $user_id == (int) $args[0] ) {
	        return $caps;
	    }
		
		$user = new WP_User( (int) $args[0] );
		
		$this->generateNames();
		
		if ( defined( 'CME_LEGACY_USER_EDIT_FILTER' ) && CME_LEGACY_USER_EDIT_FILTER ) {
			$valid = array_keys($this->roles);
			
			foreach ( $user->roles as $role ) {
				if ( ! in_array($role, $valid) ) {
					$caps = array('do_not_allow');
					break;
				}
			}
		} else {
			global $wp_roles;

			foreach ( $user->roles as $role ) {
				$r = get_role( $role );
    			$level = ak_caps2level($r->capabilities);
				
				if ( ( ! $level ) && ( 'administrator' == $role ) )
					$level = 10;
				
	    		if ( $level > $this->max_level ) {
		    		$caps = array('do_not_allow');
					break;
			    }
    		}
			
		}

		return $caps;
	}

	function processRoleUpdate() {
		if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ( ! empty($_REQUEST['SaveRole']) || ! empty($_REQUEST['AddCap']) ) ) {
			if ( ! current_user_can('manage_capabilities') && ! current_user_can('administrator') ) {
				// TODO: Implement exceptions.
				wp_die('<strong>' .__('What do you think you\'re doing?!?', 'capsman-enhanced') . '</strong>');
			}

			if ( ! empty($_REQUEST['current']) ) { // don't process role update unless form variable is received
				check_admin_referer('capsman-general-manager');
				
				$role = get_role($_REQUEST['current']);
				$current_level = ($role) ? ak_caps2level($role->capabilities) : 0;
				
				$this->processAdminGeneral();
				
				$set_level = (isset($_POST['level'])) ? $_POST['level'] : 0;
				
				if ($set_level != $current_level) {
					global $wp_roles, $wp_version;
					
					if ( version_compare($wp_version, '4.9', '>=') ) {
						$wp_roles->for_site();
					} else {
						$wp_roles->reinit();
					}
					
					foreach( get_users(array('role' => $_REQUEST['current'], 'fields' => 'ID')) as $ID ) {
						$user = new WP_User($ID);
						$user->get_role_caps();
						$user->update_user_level_from_caps();
					}
				}
			}
		}
	}
	
	/**
	 * Manages global settings admin.
	 *
	 * @hook add_submenu_page
	 * @return void
	 */
	function generalManager () {
		if ( ! current_user_can('manage_capabilities') && ! current_user_can('administrator') ) {
            // TODO: Implement exceptions.
		    wp_die('<strong>' .__('What do you think you\'re doing?!?', 'capsman-enhanced') . '</strong>');
		}

		if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
			if ( empty($_REQUEST['SaveRole']) && empty($_REQUEST['AddCap']) ) {
				check_admin_referer('capsman-general-manager');
				$this->processAdminGeneral();
			} elseif ( ! empty($_REQUEST['SaveRole']) ) {
				ak_admin_notify( $this->message );  // moved update operation to earlier action to avoid UI refresh issues.  But outputting notification there breaks styling.
			} elseif ( ! empty($_REQUEST['AddCap']) ) {
				ak_admin_notify( $this->message );
			}
		}

		$this->generateNames();
		$roles = array_keys($this->roles);

		if ( isset($_GET['action']) && 'delete' == $_GET['action']) {
			require_once( dirname(__FILE__).'/handler.php' );
			$capsman_modify = new CapsmanHandler( $this );
			$capsman_modify->adminDeleteRole();
		}
		
		if ( ! isset($this->current) ) { // By default, we manage the default role
			$this->current = get_option('default_role');
		}
		
		if ( ! in_array($this->current, $roles) ) {    // Current role has been deleted.
			$this->current = array_shift($roles);
		}
		
		include ( dirname(CME_FILE) . '/includes/admin.php' );
	}

	/**
	 * Processes and saves the changes in the general capabilities form.
	 *
	 * @return void
	 */
	private function processAdminGeneral ()
	{
		if (! isset($_POST['action']) || 'update' != $_POST['action'] ) {
		    // TODO: Implement exceptions. This must be a fatal error.
			ak_admin_error(__('Bad form Received', 'capsman-enhanced'));
			return;
		}

		$post = stripslashes_deep($_POST);
		if ( empty ($post['caps']) ) {
		    $post['caps'] = array();
		}

		$this->current = $post['current'];
		
		// Select a new role.
		if ( ! empty($post['LoadRole']) ) {
			$this->current = $post['role'];
		} else {
			require_once( dirname(__FILE__).'/handler.php' );
			$capsman_modify = new CapsmanHandler( $this );
			$capsman_modify->processAdminGeneral( $post );
		}
	}
	
	/**
	 * Callback function to create names.
	 * Replaces underscores by spaces and uppercases the first letter.
	 *
	 * @access private
	 * @param string $cap Capability name.
	 * @return string	The generated name.
	 */
	function _capNamesCB ( $cap )
	{
		$cap = str_replace('_', ' ', $cap);
		//$cap = ucfirst($cap);

		return $cap;
	}

	/**
	 * Generates an array with the system capability names.
	 * The key is the capability and the value the created screen name.
	 *
	 * @uses self::_capNamesCB()
	 * @return void
	 */
	function generateSysNames ()
	{
		$this->max_level = 10;
		$this->roles = ak_get_roles(true);
		$caps = array();

		foreach ( array_keys($this->roles) as $role ) {
			$role_caps = get_role($role);
			$caps = array_merge( $caps, (array) $role_caps->capabilities );  // user reported PHP 5.3.3 error without array cast
		}

		$keys = array_keys($caps);
		$names = array_map(array($this, '_capNamesCB'), $keys);
		$this->capabilities = array_combine($keys, $names);

		asort($this->capabilities);
	}

	/**
	 * Generates an array with the user capability names.
	 * If user has 'administrator' role, system roles are generated.
	 * The key is the capability and the value the created screen name.
	 * A user cannot manage more capabilities that has himself (Except for administrators).
	 *
	 * @uses self::_capNamesCB()
	 * @return void
	 */
	function generateNames ()
	{
		if ( current_user_can('administrator') || ( is_multisite() && is_super_admin() ) ) {
			$this->generateSysNames();
		} else {
		    global $user_ID;
		    $user = new WP_User($user_ID);
		    $this->max_level = ak_caps2level($user->allcaps);
			
		    $keys = array_keys($user->allcaps);
    		$names = array_map(array($this, '_capNamesCB'), $keys);
			
	    	$this->capabilities = ( $keys ) ? array_combine($keys, $names) : array();

		    $roles = ak_get_roles(true);
    		unset($roles['administrator']);

			if ( ( defined( 'CME_LEGACY_USER_EDIT_FILTER' ) && CME_LEGACY_USER_EDIT_FILTER ) || ( ! empty( $_REQUEST['page'] ) && 'capsman' == $_REQUEST['page'] ) ) {
				foreach ( $user->roles as $role ) {			// Unset the roles from capability list.
					unset ( $this->capabilities[$role] );
					unset ( $roles[$role]);					// User cannot manage his roles.
				}
			}
			
	    	asort($this->capabilities);

		    foreach ( array_keys($roles) as $role ) {
			    $r = get_role($role);
    			$level = ak_caps2level($r->capabilities);

	    		if ( $level > $this->max_level ) {
		    		unset($roles[$role]);
			    }
    		}

	    	$this->roles = $roles;
		}
	}

	/**
	 * Manages backup, restore and resset roles and capabilities
	 *
	 * @hook add_management_page
	 * @return void
	 */
	function backupTool ()
	{
		if ( ! current_user_can('restore_roles') && ! is_super_admin() ) {
		    // TODO: Implement exceptions.
			wp_die('<strong>' .__('What do you think you\'re doing?!?', 'capsman-enhanced') . '</strong>');
		}

		if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
			require_once( dirname(__FILE__).'/backup-handler.php' );
			$cme_backup_handler = new Capsman_BackupHandler( $this );
			$cme_backup_handler->processBackupTool();
		}

		if ( isset($_GET['action']) && 'reset-defaults' == $_GET['action']) {
			require_once( dirname(__FILE__).'/backup-handler.php' );
			$cme_backup_handler = new Capsman_BackupHandler( $this );
			$cme_backup_handler->backupToolReset();
		}

		include ( dirname(CME_FILE) . '/includes/backup.php' );
	}
}
