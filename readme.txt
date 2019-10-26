=== Capability Manager Enhanced===
Contributors: publishpress, kevinB, stevejburge, andergmartins
Tags: role, capabilities, post types, taxonomies, editor, network, woocommerce
Requires at least: 4.1
Tested up to: 5.2.4
Stable tag: 1.8.1
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

A simple way to manage WordPress roles and capabilities.

== Description ==

Control permissions requirements for your post types, and assign those capabilities to any WordPress role.

= Features: =

* Create or Copy Roles
* Manage Role Capabilities
* Enable type-specific capability requirements and assignments for any Post Type
* Enable distinct edit, delete and assign capabilities for any Taxonomy
* Some specialized support for WooCommerce Post Types
* Negation: any capability can be granted, not granted, or blocked
* Integration with PressPermit and PublishPress for comprehensive publishing solutions
* Network: Copy any role to all sites, or auto-copy to future sites
* Backup (automatic / manual) and restore roles and capabilities to revert your changes

Capability Manager Enhanced is professionally developed and supported by the experienced <a href="https://publishpress.com">PublishPress</a> team.

It has been a reliable tool since 2012, when PublishPress team member Kevin Behrens forked it from Jordi Canals' abandoned Capability Manager plugin.

For additional versatility and convenience, try <a href="https://wordpress.org/plugins/press-permit-core">PressPermit</a> plugin integration. Capabilities added indirectly by supplemental type-specific roles (Page Contributor, Product Editor, etc.) will be highlighted in the CME role editor.

== Screenshots ==

1. View or modify Capabilities for any Role
2. Users Menu
3. Network: copy Role to existing or future Sites
4. Role operations
5. Permissions Menu (PressPermit integration)
6. Shading of Capabilities granted by supplemental Type-Specific Roles
7. Enforce Type-Specific Capabilities
8. Enforce Taxonomy-Specific Capabilities
9. Detailed Taxonomy Capabilities
10. Backup / Restore tool

== Frequently Asked Questions ==

= How can I grant capabilities for a custom post type? =

The custom post type must be defined to impose type-specific capability requirements.  This is normally done by setting the "capability type" property equal to the post type name.

= I have configured a role to edit a custom post type. Why do the users still see "You are not allowed the edit this post?" when they try to save/submit a new post? =

You may need to adjust your custom post type definition by enabling the map_meta_cap property. If you are calling register_post_type manually, just add this property to the options array.

= Even after I added capabilities, WordPress is not working the way I want. =

Keep in mind that this plugin's main purpose is to expose switches (defined capabilities). The wiring of those switches is up to the WordPress core or other plugins. If granting or removing a capability does not cause the expected results, your issue is probably with the other package.  With that context in mind, you are still welcome to <a href="https://publishpress.com/contact/">contact us</a> about it.

= Can role management be delegated? =

Yes. Users with the 'manage_capabilities' capability can edit roles. This Capability is created at install time and assigned to Administrators. You can add it to any role, but non-administrators cannot manage roles or users that have a higher role level than their own.

= Where can I find more information about this plugin, usage and support ? =

* Feel free to <a href="https://publishpress.com/contact/">submit a help ticket</a> if you can't find an answer in the <a href="https://publishpress.com/knowledge-base/how-to-use-capability-manager/">documentation</a>. 

== Changelog ==

= 1.8.2-beta =
  * Change : Reinstate support for users who can edit_published_posts but not publish_posts (with correct status filtering)

= 1.8.1 - 25 Oct 2019 =
  * Fixed : Automatic publication of blank auto-drafts, WooCommerce posts save with incorrect post status (since 1.8)

= 1.8 - 24 Oct 2019 =
  * Feature : WooCommerce, PublishPress, PressPermit capabilities grouped in sections on role editor screen
  * Feature : Plugin API - plugins can hook into "cme_plugin_capabilities" filter to register their capabilities 
  * Feature : Work around WordPress issue preventing users with edit_published_posts (but not publish_posts) capability from updating published posts (https://core.trac.wordpress.org/ticket/47443)
  * Feature : Work around WordPress issue allowing users with edit_published_posts (but not publish_posts) to unpublish published posts
  * Fixed : If a unique edit/delete capability is already defined, don't change the definition
  * Fixed : Removed add_users from the Core WordPress Capabilities section because it is was replaced by promote_users
  * Fixed : PHP Notices on Role Capabilities screen for undefined index, under some configurations 
  * Fixed : HTML validation errors on Manage Capabilities screen
  * Fixed : PHP 5.x : Notice for undefined constant PHP_INT_MIN on wp-admin Posts / Pages listing
  * Change : Move Role Capabilities menu item to Permissions menu if PressPermit plugin is active (restoring previous behavior with Press Permit Core)
  * Change : Edit Roles link in CME row of Plugins list
  * Change : PublishPress icon, footer on Roles and Capabilities screen

= 1.7.5 - 24 May 2019 =
  * Fixed : Users' inclusion or non-inclusion in Authors dropdown was not updated based on role edit

= 1.7.4 - 1 May 2019 =
  * Fixed : On some sites, capabilities added dynamically by other code were forced into stored role definition (and could not be removed).
  * Fixed : Negative role capabilities could not be directly unset (had to be checked, saved, then unchecked).

= 1.7.3 - 9 Apr 2019 = 
  * Fixed : Work around WP quirk of completely blocking admin page access for a post type if user lacks create capability for the post type and there are no other accessible items on the menu.
  * Fixed : PHP Notices on Roles and Capabilities screen for non-Administrator with WooCommerce active

= 1.7.2 - 3 Apr 2019 = 
  * Compat : WooCommerce integration - Users lacking access to the "Add New Order" submenu could not access Posts, Pages, Products or any other Post Type listing. This occurred if "use create_posts" option enabled and user lacks the create capability for Orders. 

= 1.7.1 - 29 Mar 2019 = 
  * Fixed : Press Permit integration - cannot load Permissions > Role Capabilities with Press Permit Core < 2.7

= 1.7 - 28 Mar 2019 = 
  * Feature : New right sidebar setting: "Type-Specific Capabilities" for selected post types (without activating Press Permit Core).
  * Feature : New right sidebar setting: "Taxonomy-Specific Capabilities" ensures a distinct manage capability for selected taxonomies
  * Feature : New right sidebar setting: "Detailed Taxonomy Capabilities" causes term assign, edit and deletion capabilities to be required and credited separate from management capability
  * Feature : WooCommerce - Ensure orders can be edited or added based on edit_shop_orders / create_shop_orders capability
  * Change : Lockout safeguard (preventing read capability removal) is bypassed if role has no WP admin / edit capabilities, or if it has "dashboard_lockout_ok" capability
  * Compat : Press Permit: new plugin page slugs in Press Permit Core 2.7

= 1.6.1 =
  * Feature : Prevent read capability from being removed from a standard role
  * Feature : If read capability is missing from a standard role, display warning and instant fix link 
  * Feature : Additional save button at top of Roles and Capabilities screen!
  * Change : Reinstate Press Permit description link  
  * Change : Thickbox popups for related plugins

= 1.6 =
  * Feature : WooCommerce - If current user has duplicate_products capability, make Woo honor it
  * Feature : Link to Backup Tool from sidebar of Roles and Capabilities screen
  * Feature : Link to Roles and Capabilities screen from Backup Tool
  * Change : Minor code cleanup and refactor
  * Change : Copyrights, onscreen link for PublishPress ownership
  * Change : Links to Related Permissions Plugins in sidebar on Roles and Capabilities screen
  
= 1.5.11 =
  * Feature : Automatically save backup of WP roles on plugin activation or update
  * Feature : When roles are manually backed up, also retain initial role backup
  * Feature : Backup Tool can also display contents of role backups

= 1.5.10 =
  * Fixed : Back button caused mismatching role dropdown selection
  * Compat : PHP 7.2 - warning for deprecated function if a second copy of CME is activated

= 1.5.9 =
  * Fixed : Potential vulnerability in wp-admin (but exposure was only to users with role editing capability)

= 1.5.8 =
  * Fixed : PHP warning for deprecated function WP_Roles::reinit
  * Change : Don't allow non-Administrator to edit Administrators, even if Administrator role level is set to 0
  
= 1.5.7 =
  * Change : Revert menu captions to previous behavior ("Permissions > Role Capabilities" if Press Permit Core is active, otherwise "Users > Capabilities")

= 1.5.6 =
  * Fixed : Correct some irregularities in CME admin menu item display

= 1.5.5 =
  * Fixed : User editing was improperly blocked in some cases

= 1.5.4 =
  * Fixed : Non-administrators' user editing capabilities were blocked if Press Permit Core was also active
  * Fixed : Non-administrators could not edit other users with their role (define constant CME_LEGACY_USER_EDIT_FILTER to retain previous behavior)
  * Fixed : Non-administrators could not assign their role to other users (define constant CME_LEGACY_USER_EDIT_FILTER to retain previous behavior)
  * Lang : Changed text domain for language pack conformance

= 1.5.3 =
  * Fixed : On single-site installations, non-Administrators with delete_users capability could give new users an Administrator role (since 1.5.2) 
  * Fixed : Deletion of a third party plugin role could cause users to be demoted to Subscriber inappropriately
  * Compat : Press Permit Core - Permission Group refresh was not triggered if Press Permit Core is inactive when CME deletes a role definition
  * Compat : Support third party display of available capabilities via capsman_get_capabilities or members_get_capabilities filter
  * Change : If user_level of Administrator role was cleared, non-Administrators with user editing capabilities could create/edit/delete Administrators.  Administrator role is now implicitly treated as level 10.
  * Fixed : CSS caused formatting issues around wp-admin Update button on some installations
  * Perf : Don't output wp-admin CSS on non-CME screens
  * Lang : Fixed erroneous text_domain argument for numerous strings
  * Lang : Updated .pot and .po files
  
= 1.5.2 =
  * Fixed : Network Super Administrators without an Administrator role on a particular site could not assign an Administrator role to other users of that site

= 1.5.1 =
  * Fixed : Non-administrators with user editing capabilities could give new users a role with a higher level than their own (including Administrator)

= 1.5 =
  * Feature : Support negative capabilities (storage to wp_roles array with false value)
  * Feature : Multisite - Copy a role definition to all current sites on a network
  * Feature : Multisite - Copy a role definition to new (future) sites on a network
  * Feature : Backup / Restore tool requires "restore_roles" capability or super admin status
  * Fixed : Role reset to WP defaults did not work, caused a PHP error / white screen
  * Change : Clarified English captions on Backup Tool screen
  * Fixed : Term deletion capability was not included in taxonomies grid even if defined
  * Fixed : jQuery notices for deprecated methods on Edit Role screen
  * Compat : Press Permit - if a role is marked as hidden, also default it for use by PP Pro as a Pattern Role (when PP Collaborative Editing is activated and Advanced Settings enabled)
  * Change : Press Permit promotional message includes link to display further info
  
= 1.4.10 =
  * Perf :  Eliminated unused framework code (reduced typical wp-admin memory usage by 0.6 MB)
  * Fixed : Failure to save capability changes, on some versions of PHP
  * Compat : Press Permit - PHP Warning on role save
  * Compat : Press Permit - PHP Warning on "Force Type-Specific Capabilities" settings update
  * Compat : Press Permit - "supplemental only" option stored redundant entries
  * Compat : Press Permit - green background around capabilities which 
  * Compat : Press Permit - PHP Warning on "Force Type-Specific Capabilities" settings update
  * Maint  : Stop using $GLOBALS superglobal
  * Change : Reduced download size by moving screenshots to assets folder of project folder

= 1.4.9 =
  * Fixed : Role capabilities were not updated / refreshed properly on multisite installations
  * Feature : If create_posts capabilities are defined, organize checkboxes into a column alongside edit_posts
  * Feature : "Use create_posts capability" checkbox in sidebar auto-defines create_posts capabilities (requires Press Permit)
  * Compat : bbPress + Press Permit - Modified bbPress role capabilities were not redisplayed following save, required reload
  * Compat : bbPress + Press Permit - Adding a capability via the "Add Cap" textbox caused the checkbox to be available but not selected
  * Compat : Press Permit - "supplemental only" option was always enabled for newly created and copied roles, regardless of checkbox setting near Create/Copy button
  
= 1.4.8 =
  * Compat : bbPress + Press Permit - "Add Capability" form failed when used on a bbPress role, caused creation of an invalid role

= 1.4.7 =
  * Compat : Press Permit - flagging of roles as "supplemental assignment only" was not saved

= 1.4.6 =
  * Compat : bbPress 2.2 (supports customization of dynamic forum role capabilities)
  * Compat : Press Permit + bbPress - customized role capabilities were not properly maintained on bbPress activation / deactivation, in some scenarios
  * Fixed : Role update and copy failed if currently stored capability array is corrupted
 
= 1.4.5 =
  * Fixed : Capabilities were needlessly re-saved on role load
  * Fixed : Capability labels in "Other WordPress" section did not toggle checkbox selection
  * Press Permit integration: If capability is granted by the role's Permit Group, highlight it as green with a descriptive caption title, but leave checkbox enabled for display/editing of role defintion setting (previous behavior caused capability to be stripped out of WP role definition under some PP configurations)
  
= 1.4.4 =
  * Fixed : On translated sites, roles could not be edited
  * Fixed : Menu item change to "Role Capabilities" broke existing translations

= 1.4.3 =
  * Fixed : Separate checkbox was displayed for cap->edit_published_posts even if it was defined to the be same as cap->edit_posts
  * Press Permit integration: automatically store a backup copy of each role's last saved capability set so they can be reinstated if necessary (currently for bbPress)

= 1.4.2 =
  * Language: updated .pot file
  * Press Permit integration: roles can be marked for supplemental assignment only (and suppressed from WP role assignment dropdown, requires PP 1.0-beta1.4)

= 1.4.1 =
  * https compatibility: use content_url(), plugins_url()
  * Press Permit integration: if role definitions are reset to WP defaults, also repopulate PP capabilities (pp_manage_settings, etc.)

= 1.4 =
  * Organized capabilities UI by post type and operation
  * Editing UI separates WP core capabilities and 3rd party capabilities
  * Clarified sidebar captions
  * Don't allow a non-Administrator to add or remove a capability they don't have
  * Fixed : PHP Warnings for unchecked capabilities
  * Press Permit integration: externally (dis)enable Post Types, Taxonomies for PP filtering (which forces type-specific capability definitions)
  * Show capabilities which Press Permit adds to the role by supplemental type-specific role assignment
  * Reduce memory usage by loading framework and plugin code only when needed
  
= 1.3.2 = 
  * Added Swedish translation.

= 1.3.1 =
  * Fixed a bug where administrators could not create or manage other administrators.
  
= 1.3 =
  * Cannot edit users with more capabilities than current user.
  * Cannot assign to users a role with more capabilities than current user.
  * Solved an incompatibility with Chameleon theme.
  * Migrated to the new Alkivia Framework.
  * Changed license to GPL version 2.

= 1.2.5 =
  * Tested up to WP 2.9.1.

= 1.2.4 =
  * Added Italian translation.

= 1.2.3 =
  * Added German and Belorussian translations.

= 1.2.2 =
  * Added Russian translation.

= 1.2.1 =
  * Coding Standards.
  * Corrected internal links.
  * Updated Framework.

= 1.2 =
  * Added backup/restore tool.

= 1.1 =
  * Role deletion added.

= 1.0.1 =
  * Some code improvements.
  * Updated Alkivia Framework.

= 1.0 =
  * First public version.

== Upgrade Notice ==

= 1.5.1 =
Fixed : Non-administrators with user editing capabilities could add new Administrators

= 1.3.2 = 
Only Swedish translation.

= 1.3.1 =
Bug fixes.
  
= 1.3 =
Improved security esiting users. You can now create real user managers. 
