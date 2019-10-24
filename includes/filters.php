<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * class CME_Extensions
 * 
 * Load filters and actions for integration with third party plugins
 */
class CME_Extensions {
	var $extensions = array();
	
	function add( $object ) {
		if ( ! is_object( $object ) ) return;
		
		$this->extensions[ get_class( $object ) ] = $object;
	}
}

global $cme_extensions;
$cme_extensions = new CME_Extensions();

add_filter( 'map_meta_cap', '_cme_remap_term_meta_cap', 5, 4 );

if ( defined( 'WC_PLUGIN_FILE' ) ) {
	require_once ( dirname(__FILE__) . '/filters-woocommerce.php' );
	$cme_extensions->add( new CME_WooCommerce() );
}

if ( is_admin() ) {
	global $pagenow;
	if ( 'edit.php' == $pagenow ) {
		require_once ( dirname(__FILE__) . '/filters-admin.php' );
		new CME_AdminMenuNoPrivWorkaround();
	}
}

add_filter('plugin_action_links_' . plugin_basename(CME_FILE), '_cme_fltPluginActionLinks', 10, 2);

// allow edit_terms, delete_terms, assign_terms capabilities to function separately from manage_terms
function _cme_remap_term_meta_cap ( $caps, $cap, $user_id, $args ) {
	global $current_user, $cme_cap_helper;
	
	if ( ! empty( $cme_cap_helper ) ) {
		$cap_helper = $cme_cap_helper;
	} else {
		global $ppce_cap_helper;
		if ( ! empty( $ppce_cap_helper ) ) {
			$cap_helper = $ppce_cap_helper;
		}
	}
	
	if ( empty($cap_helper) || empty( $cap_helper->all_taxonomy_caps[$cap] ) ) {
		return $caps;
	}
	
	if ( ! $enabled_taxonomies = array_intersect( cme_get_assisted_taxonomies(), cme_get_detailed_taxonomies() ) ) {
		return $caps;
	}
	
	// meta caps
	switch ( $cap ) {
		case 'edit_term':
		case 'delete_term':
		case 'assign_term':
			$tx_cap = $cap . 's';
		
			if ( ! is_array($args) || empty($args[0]) ) {
				return $caps;
			}
			
			if ( $term = get_term( $args[0] ) ) {
				if ( in_array( $term->taxonomy, $enabled_taxonomies ) ) {
					if ( $tx_obj = get_taxonomy( $term->taxonomy ) ) {
						
						// If this taxonomy is set for distinct capabilities, we don't want manage_terms capability to be implicitly assigned.
						if ( empty( $current_user->allcaps[$tx_obj->cap->manage_terms] ) ) {
							$caps = array_diff( $caps, (array) $tx_obj->cap->manage_terms );
						}
						$caps[]= $tx_obj->cap->$tx_cap;
					}
				}
			}
			break;
		default:
	}
	
	// primitive caps
	foreach( $enabled_taxonomies as $taxonomy ) {
		if ( ! $tx_obj = get_taxonomy( $taxonomy ) ) {
			continue;
		}
		
		foreach( array( 'edit_terms', 'delete_terms', 'assign_terms' ) as $cap_prop ) {
			if ( $cap == $tx_obj->cap->$cap_prop ) {
				
				// If this taxonomy is set for distinct capabilities, we don't want manage_terms capability to be implicitly assigned.
				if ( empty( $current_user->allcaps[$tx_obj->cap->manage_terms] ) ) {
					$caps = array_diff( $caps, (array) $tx_obj->cap->manage_terms );
				}
				
				$caps[]= $tx_obj->cap->$cap_prop;
				return $caps;
			}
		}
	}
	
	return $caps;
}

// Note: this intentionally shares "pp_enabled_post_types" option with Press Permit 
function cme_get_assisted_post_types() {
	$type_args = array( 'public' => true, 'show_ui' => true );
	
	$types = get_post_types( $type_args, 'names', 'or' );
	
	if ( $omit_types = apply_filters( 'pp_unfiltered_post_types', array( 'wp_block' ) ) ) {
		$post_types = array_diff_key( $types, array_fill_keys( (array) $omit_types, true ) );
	}
	
	$enabled = (array) get_option( 'pp_enabled_post_types', array( 'post' => true, 'page' => true ) );
	$post_types = array_intersect( $post_types, array_keys( array_filter( $enabled ) ) );
	
	return apply_filters( 'cme_assisted_post_types', $post_types, $type_args );
}

// Note: this intentionally does NOT share Press Permit' option name, for back compat reasons
// Enabling filtered taxonomies in PP previously did not cause the edit_terms, delete_terms, assign_terms capabilities to be enforced
function cme_get_assisted_taxonomies() {
	$tx_args = array( 'public' => true );
	
	$taxonomies = get_taxonomies( $tx_args );
	
	if ( $omit_taxonomies = apply_filters( 'pp_unfiltered_taxonomies', array() ) ) {
		$taxonomies = array_diff_key( $taxonomies, array_fill_keys( (array) $omit_taxonomies, true ) );
	}
	
	$enabled = (array) get_option( 'pp_enabled_taxonomies', array() );
	$taxonomies = array_intersect( $taxonomies, array_keys( array_filter( $enabled ) ) );
	
	return apply_filters( 'cme_assisted_taxonomies', $taxonomies, $tx_args );
}

function cme_get_detailed_taxonomies() {
	$tx_args = array( 'public' => true );
	
	$taxonomies = get_taxonomies( $tx_args );
	
	if ( $omit_taxonomies = apply_filters( 'pp_unfiltered_taxonomies', array() ) ) {
		$taxonomies = array_diff_key( $taxonomies, array_fill_keys( (array) $omit_taxonomies, true ) );
	}
	
	$enabled = (array) get_option( 'cme_detailed_taxonomies', array() );
	$taxonomies = array_intersect( $taxonomies, array_keys( array_filter( $enabled ) ) );
	
	return apply_filters( 'cme_detailed_taxonomies', $taxonomies, $tx_args );
}

function _cme_get_plural( $slug, $type_obj = false ) {
	if ( $type_obj && ! empty( $type_obj->rest_base ) && ( $type_obj->rest_base != $slug ) && ( $type_obj->rest_base != "{$slug}s" ) ) {
		// Use plural form from rest_base
		if ( $pos = strpos( $type_obj->rest_base, '/' ) ) {
			return sanitize_key( substr( $type_obj->rest_base, 0, $pos + 1 ) );
		} else {
			return sanitize_key( $type_obj->rest_base );
		}
	} else {
		require_once ( dirname(__FILE__) . '/inflect-cme.php' );
		return sanitize_key( CME_Inflect::pluralize( $slug ) );	
	}
}

function _cme_fltPluginActionLinks($links, $file)
{
	if ($file == plugin_basename(CME_FILE)) {
		if (!is_network_admin()) {
			$links[] = "<a href='" . admin_url("admin.php?page=capsman") . "'>" . __('Edit Roles', 'capsman-enhanced') . "</a>";
		}
	}

	return $links;
}
