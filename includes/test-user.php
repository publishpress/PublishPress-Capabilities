<?php
/**
 * Capability Manager.
 * Plugin to create and manage roles and capabilities.
 *
 * @author        Jordi Canals, Kevin Behrens
 * @copyright   Copyright (C) 2009, 2010 Jordi Canals, (C) 2020 PublishPress
 * @license        GNU General Public License version 2
 * @link        https://publishpress.com/
 *
 *
 *    Copyright 2009, 2010 Jordi Canals <devel@jcanals.cat>
 *
 *    Modifications Copyright 2020, PublishPress <help@publishpress.com>
 *
 *    This program is free software; you can redistribute it and/or
 *    modify it under the terms of the GNU General Public License
 *    version 2 as published by the Free Software Foundation.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program. If not, see <http://www.gnu.org/licenses/>.
 */


class PPC_TestUser
{

    /**
     * Sets up all the filters and actions.
     */
    public function run()
    {
        add_filter('user_has_cap', array($this, 'ppc_filter_user_has_cap'), 10, 4);
        add_filter('map_meta_cap', array($this, 'ppc_filter_map_meta_cap'), 10, 4);
        add_filter('manage_users_columns', array($this, 'ppc_testuser_column'), 1000);
        add_filter('manage_users_custom_column', array($this, 'ppc_testuser_column_content'), 15, 3);
        add_filter('login_redirect', array($this, 'ppc_login_redirect'), 20, 3);

        add_action('init', array($this, 'ppc_init'));
        add_action('personal_options', array($this, 'ppc_personal_options'));
        add_action('wp_logout', array($this, 'ppc_test_user_clear_olduser_cookie'));
        add_action('wp_login', array($this, 'ppc_test_user_clear_olduser_cookie'));
        add_action('admin_bar_menu', array($this, 'ppc_action_admin_bar_menu'), 8);
        add_action('wp_head', array($this, 'ppc_filter_revert_home_message'), 1);
        add_action('all_admin_notices', array($this, 'ppc_filter_revert_home_message'), 1);
        add_action('wp_head', array($this, 'ppc_custom_styles'));
        add_action('admin_head', array($this, 'ppc_custom_styles'));
	}

    /**
     * Returns whether or not the current logged in user is has a persistent browser cookie
     *
     * @return bool
     */
    public static function ppc_remember_me()
    {
        /** This filter is documented in wp-includes/pluggable.php */
        $cookie_life = apply_filters('auth_cookie_expiration', 259200, get_current_user_id(), false);
        $current = wp_parse_auth_cookie('', 'logged_in');

        // Here we calculate the expiration length of the current auth cookie and compare it to the default expiration.
        // If it's greater than this, then we know the user checked 'Remember Me' when they logged in.
        return (($current['expiration'] - time()) > $cookie_life);
    }

    /**
     * Returns the nonce-secured URL needed to switch to a given user ID.
     *
     * @param WP_User $user The user to be switched to.
     * @return string The required URL.
     */
    public static function ppc_testuser_url(WP_User $user)
    {
        $return_url = admin_url('users.php');

        return wp_nonce_url(add_query_arg(array(
            'action' => 'ppc_test_user',
            'user_id' => $user->ID,
            'back_url' => $return_url,
        ), wp_login_url()), "ppc_test_user_{$user->ID}");
    }

    /**
     * Returns the nonce-secured URL needed to switch back to the originating user.
     *
     * @param WP_User $user The old user.
     * @return string        The required URL.
     */
    public static function ppc_back_url(WP_User $user)
    {
        return wp_nonce_url(add_query_arg(array(
            'action' => 'ppc_test_user_revert',

        ), wp_login_url()), "ppc_test_user_revert_{$user->ID}");
    }

    /**
     * Returns the current URL.
     *
     * @return string The current URL.
     */
    public static function ppc_current_url()
    {
        return (is_ssl() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    /**
     * Removes a list of common confirmation-style query args from a URL.
     *
     * @param string $url A URL.
     * @return string The URL with query args removed.
     */
    public static function ppc_remove_query_args($url)
    {
        if (function_exists('wp_removable_query_args')) {
            $url = remove_query_arg(wp_removable_query_args(), $url);
        }

        return $url;
    }

    /**
     * Returns whether or not User Switching's equivalent of the 'logged_in' cookie should be secure.
     *
     * This is used to set the 'secure' flag on the old user cookie, for enhanced security.
     *
     * @link https://core.trac.wordpress.org/ticket/15330
     *
     * @return bool Should the old user cookie be secure?
     */
    public static function ppc_secure_olduser_cookie()
    {
        return (is_ssl() && ('https' === parse_url(home_url(), PHP_URL_SCHEME)));
    }

    public static function ppc_secure_back_url_cookie()
    {
        return (is_ssl() && ('https' === parse_url(home_url(), PHP_URL_SCHEME)));
    }

    /**
     * Returns whether or not User Switching's equivalent of the 'auth' cookie should be secure.
     *
     * This is used to determine whether to set a secure auth cookie or not.
     *
     * @return bool Should the auth cookie be secure?
     */
    public static function ppc_secure_auth_cookie()
    {
        return (is_ssl() && ('https' === parse_url(wp_login_url(), PHP_URL_SCHEME)));
    }

    /**
     * Instructs WooCommerce to forget the session for the current user, without deleting it.
     *
     * @param WooCommerce $wc The WooCommerce instance.
     */
    public static function ppc_forget_woocommerce_session(WooCommerce $wc)
    {
        if (!property_exists($wc, 'session')) {
            return false;
        }

        if (!method_exists($wc->session, 'forget_session')) {
            return false;
        }

        $wc->session->forget_session();
    }

    /**
     * Fetches the URL to redirect to for a given user (used after switching).
     *
     * @param WP_User $new_user Optional. The new user's WP_User object.
     * @param WP_User $old_user Optional. The old user's WP_User object.
     * @return string The URL to redirect to.
     */
    protected static function ppc_get_redirect(WP_User $new_user = null, WP_User $old_user = null)
    {
        if (!empty($_REQUEST['redirect_to'])) {
            $redirect_to = self::ppc_remove_query_args(wp_unslash($_REQUEST['redirect_to']));
            $requested_redirect_to = wp_unslash($_REQUEST['redirect_to']);
        } else {
            $redirect_to = '';
            $requested_redirect_to = '';
        }

        if (!$new_user) {
            /** This filter is documented in wp-login.php */
            $redirect_to = apply_filters('logout_redirect', $redirect_to, $requested_redirect_to, $old_user);
        } else {
            /** This filter is documented in wp-login.php */
            $redirect_to = apply_filters('login_redirect', $redirect_to, $requested_redirect_to, $new_user);
        }

        return $redirect_to;
    }

    public function ppc_login_redirect($redirect_to, $requested, $user)
    {
        if (!isset($_REQUEST['action'])) {
            return $redirect_to;
        }

        if ($_REQUEST['action'] != 'ppc_test_user' && $_REQUEST['action'] != 'ppc_test_user_revert') {
            return $redirect_to;
        }
    }

    /**
     * Loads actions depending on the 'action' query var.
     */
    public function ppc_init()
    {
        if (!isset($_REQUEST['action'])) {
            return;
        }

        $current_user = (is_user_logged_in()) ? wp_get_current_user() : null;

        switch ($_REQUEST['action']) {

            case 'ppc_test_user':
                if (isset($_REQUEST['user_id'])) {
                    $user_id = absint($_REQUEST['user_id']);
                } else {
                    $user_id = 0;
                }

                // Check authentication
                if (!current_user_can('ppc_test_user', $user_id)) {
                    wp_die(esc_html__('You did not have permission to test this user.', 'capsman-enhanced'));
                }

                // Check intent
                check_admin_referer("ppc_test_user_{$user_id}");

                // Switch user
                $user = $this->ppc_test_user($user_id, self::ppc_remember_me());
                if ($user) {
                    $redirect_to = self::ppc_get_redirect($user, $current_user);

                    // Redirect to the dashboard or the home URL depending on capabilities:

                    if ($redirect_to) {
                        wp_safe_redirect($redirect_to, 302, 'PublishPress Capabilities - WordPress Plugin');
                    } elseif (!current_user_can('read')) {
                        wp_safe_redirect(home_url(), 302, 'PublishPress Capabilities - WordPress Plugin');
                    } else {
                        wp_safe_redirect(admin_url(), 302, 'PublishPress Capabilities - WordPress Plugin');
                    }
                    exit;
                } else {
                    wp_die(esc_html__('Could not test this user.', 'capsman-enhanced'));
                }
                break;

            case 'ppc_test_user_revert':
                // Fetch the originating user data
                $old_user = $this->ppc_get_old_user();
                if (!$old_user) {
                    wp_die(esc_html__('Could not test this user.', 'capsman-enhanced'));
                }

                // Check authentication
                if (!self::ppc_authenticate_old_user($old_user)) {
                    wp_die(esc_html__('You did not have permission to test this user.', 'capsman-enhanced'));
                }

                // Check intent
                check_admin_referer("ppc_test_user_revert_{$old_user->ID}");

                // Switch user
                if ($this->ppc_test_user($old_user->ID, self::ppc_remember_me(), false)) {

                    if (!empty($_REQUEST['interim-login'])) {
                        $GLOBALS['interim_login'] = 'success'; // @codingStandardsIgnoreLine
                        login_header('', '');
                        exit;
                    }

                    $redirect_to = self::ppc_get_redirect($old_user, $current_user);

                    if ($redirect_to) {
                        wp_safe_redirect($redirect_to, 302, 'PublishPress Capabilities - WordPress Plugin');
                    } else {
                        // redirect the user to the correct page
                        $ppc_test_user_get_back_url_cookie = $this->ppc_test_user_get_back_url_cookie();
                        $back_url = (!empty($ppc_test_user_get_back_url_cookie)) ? urldecode($ppc_test_user_get_back_url_cookie) : admin_url('users.php');
                        wp_safe_redirect($back_url, 302, 'PublishPress Capabilities - WordPress Plugin');
                    }
                    exit;
                } else {
                    wp_die(esc_html__('Could not test this user.', 'capsman-enhanced'));
                }
                break;
        }
    }

    /**
     * Validates the old user cookie and returns its user data.
     *
     * @return false|WP_User False if there's no old user cookie or it's invalid, WP_User object if it's present and valid.
     */
    public function ppc_get_old_user()
    {
        $cookie = $this->ppc_test_user_get_olduser_cookie();
        if (!empty($cookie)) {
            $old_user_id = wp_validate_auth_cookie($cookie, 'logged_in');

            if ($old_user_id) {
                return get_userdata($old_user_id);
            }
        }
        return false;
    }

    /**
     * Authenticates an old user by verifying the latest entry in the auth cookie.
     *
     * @param WP_User $user A WP_User object (usually from the logged_in cookie).
     * @return bool Whether verification with the auth cookie passed.
     */
    public function ppc_authenticate_old_user(WP_User $user)
    {
        $cookie = $this->ppc_test_user_get_auth_cookie();
        if (!empty($cookie)) {
            if (self::ppc_secure_auth_cookie()) {
                $scheme = 'secure_auth';
            } else {
                $scheme = 'auth';
            }

            $old_user_id = wp_validate_auth_cookie(end($cookie), $scheme);

            if ($old_user_id) {
                return ($user->ID === $old_user_id);
            }
        }
        return false;
    }

    /**
     * Adds a 'Test user' and `Testing user` link to admin bar.
     *
     * @param WP_Admin_Bar $wp_admin_bar The admin bar object.
     */
    public function ppc_action_admin_bar_menu(WP_Admin_Bar $wp_admin_bar)
    {
        if (!is_admin_bar_showing()) {
            return;
        }

        $user = wp_get_current_user();
        $old_user = $this->ppc_get_old_user();

        if ($old_user instanceof WP_User) {
            //switched back data

            $node_link = self::ppc_back_url($old_user);
            if (!empty($_REQUEST['interim-login'])) {
                $node_link = add_query_arg(array(
                    'interim-login' => '1',
                ), $node_link);
            } elseif (!empty($_REQUEST['redirect_to'])) {
                $node_link = add_query_arg(array(
                    'redirect_to' => urlencode(wp_unslash($_REQUEST['redirect_to'])),
                ), $node_link);
            }
            $role = $this->ppc_get_user_roles($user);
            $node_title = esc_html(sprintf(
            /* Translators: 1: user display name; 2: roles; */
                __('Testing user: %1$s (%2$s)', 'capsman-enhanced'),
                $user->display_name,
                $role
            ));

            $node_icon = 'visibility';
        } else {
            //new test data

            $node_link = admin_url('/users.php');
            $node_title = __('Test user', 'capsman-enhanced');
            $node_icon = 'hidden';

        }

        // Add menu item.
        $wp_admin_bar->add_node(array(
            'id' => 'ppc-menu-bar',
            'parent' => 'top-secondary',
            'title' => '<span class="ab-label">' . $node_title . '</span> <span class="ab-icon dashicons dashicons-' . $node_icon . ' alignright" aria-hidden="true"></span>',
            'href' => $node_link,
            'meta' => array(
                'title' => $node_title,
            ),
        ));


    }

    /**
     * Adds a 'Switch back to {user}' link to the WordPress frontend for user without admin access
     *
     * @return string The login screen message.
     */
    public function ppc_filter_revert_home_message()
    {
        $user = wp_get_current_user();
        $old_user = $this->ppc_get_old_user();

        if ($old_user instanceof WP_User) {
            $switched_locale = false;
            $lang_attr = '';

            if (function_exists('get_user_locale')) {
                $locale = get_user_locale($old_user);
                $switched_locale = switch_to_locale($locale);
                $lang_attr = str_replace('_', '-', $locale);
            }

            // add the class to the body
            add_filter('body_class', array($this, 'ppc_body_class'), 10, 3);

            ?>
            <div class="ppc-testing-user updated notice is-dismissible">
                <?php
                if ($lang_attr) {
                    printf(
                        '<p lang="%s">',
                        esc_attr($lang_attr)
                    );
                } else {
                    echo '<p>';
                }
                ?>
                <span class="dashicons dashicons-admin-users" style="color:#56c234" aria-hidden="true"></span>
                <?php

                $switch_back_url = self::ppc_back_url($old_user);

                if (!empty($_REQUEST['interim-login'])) {
                    $switch_back_url = add_query_arg(array(
                        'interim-login' => '1',
                    ), $switch_back_url);
                } elseif (!empty($_REQUEST['redirect_to'])) {
                    $switch_back_url = add_query_arg(array(
                        'redirect_to' => urlencode(wp_unslash($_REQUEST['redirect_to'])),
                    ), $switch_back_url);
                }
                $role = $this->ppc_get_user_roles($user);

                $message = sprintf(
                    ' <a href="%s">%s</a>.',
                    esc_url($switch_back_url),
                    esc_html(sprintf(
                    /* Translators: 1: user display name; 2: roles; */
                        __('You are testing %1$s (%2$s). Click here to return to your Administrator view', 'capsman-enhanced'),
                        $old_user->display_name,
                        $role
                    ))
                );

                echo wp_kses($message, array(
                    'a' => array(
                        'href' => array(),
                    ),
                ));
                ?>
                </p>
            </div>
            <?php
            if ($switched_locale) {
                restore_previous_locale();
            }
        }
    }

    /**
     * Returns the switch to or switch back URL for a given user.
     *
     * @param WP_User $user The user to be switched to.
     * @return string|false The required URL, or false if there's no old user or the user doesn't have the required capability.
     */
    public function build_the_ppc_test_user_url(WP_User $user)
    {
        $old_user = $this->ppc_get_old_user();

        if ($old_user && ($old_user->ID === $user->ID)) {
            return self::ppc_back_url($old_user);
        } elseif (current_user_can('ppc_test_user', $user->ID)) {
            return self::ppc_testuser_url($user);
        } else {
            return false;
        }
    }

    /**
     * Filters a user's capabilities so they can be altered at runtime.
     *
     * This is used to:
     *  - Grant the 'ppc_test_user' capability to the user if they have the ability to edit the user they're trying to
     *    switch to (and that user is not themselves).
     *
     * Important: This does not get called for Super Admins. See ppc_filter_map_meta_cap() below.
     *
     * @param bool[] $user_caps Array of key/value pairs where keys represent a capability name and boolean values
     *                                represent whether the user has that capability.
     * @param string[] $required_caps Required primitive capabilities for the requested capability.
     * @param array $args {
     *     Arguments that accompany the requested capability check.
     *
     * @type string    $0 Requested capability.
     * @type int       $1 Concerned user ID.
     * @type mixed  ...$2 Optional second and further parameters.
     * }
     * @param WP_User $user Concerned user object.
     * @return bool[] Concerned user's capabilities.
     */
    public function ppc_filter_user_has_cap(array $user_caps, array $required_caps, array $args, WP_User $user)
    {
        if ('ppc_test_user' === $args[0]) {
            $user_caps['ppc_test_user'] = (user_can($user->ID, 'edit_user', $args[2]) && ($args[2] !== $user->ID));
        }

        return $user_caps;
    }

    /**
     * Filters the required primitive capabilities for the given primitive or meta capability.
     *
     * This is used to:
     *  - Add the 'do_not_allow' capability to the list of required capabilities when a Super Admin is trying to switch
     *    to themselves.
     *
     * It affects nothing else as Super Admins can do everything by default.
     *
     * @param string[] $required_caps Required primitive capabilities for the requested capability.
     * @param string $cap Capability or meta capability being checked.
     * @param int $user_id Concerned user ID.
     * @param array $args {
     *     Arguments that accompany the requested capability check.
     *
     * @type mixed ...$0 Optional second and further parameters.
     * }
     * @return string[] Required capabilities for the requested action.
     */
    public function ppc_filter_map_meta_cap(array $required_caps, $cap, $user_id, array $args)
    {
        if (('ppc_test_user' === $cap) && ($args[0] === $user_id)) {
            $required_caps[] = 'do_not_allow';
        }
        return $required_caps;
    }

    public function ppc_personal_options(WP_User $user)
    {
        $ppc_test_user_url = $this->build_the_ppc_test_user_url($user);

        if (get_current_user_id() != $user->ID && !empty($user->user_login)) {
            echo '<a class="button" href="' . esc_url($ppc_test_user_url) . '" title="' . esc_html__('Test', 'capsman-enhanced') . ' ' . esc_html__($user->user_login, 'capsman-enhanced') . '"> ' . esc_html__('Test', 'capsman-enhanced') . ' <strong>' . esc_html__($user->user_login, 'capsman-enhanced') . '</strong></a>';
        } else {
            echo __('Currently logged in account.', 'capsman-enhanced');
        }
    }

    public function ppc_testuser_column_content($val, $column_name, $user_id)
    {
        global $wpdb;
        switch ($column_name) {
            case 'ppc_testuser_column':
                $user = new WP_User($user_id);

                $ppc_test_user_url = $this->build_the_ppc_test_user_url($user);

                if (!$ppc_test_user_url || empty($user->user_login)) {
                    return __('Currently logged in account.', 'capsman-enhanced');
                }

                return '<a class="button" href="' . esc_url($ppc_test_user_url) . '" title="' . esc_html__('Test', 'capsman-enhanced') . ' ' . esc_html__($user->user_login, 'capsman-enhanced') . '"> ' . esc_html__('Test', 'capsman-enhanced') . ' <strong>' . esc_html__($user->user_login, 'capsman-enhanced') . '</strong></a>';
                break;
            default:
        }
        return $val;
    }

    /**
     * Add extra column after username
     */
    function ppc_testuser_column($columns)
    {
        $new_columns = array();

        foreach ($columns as $column_name => $column_info) {

            $new_columns[$column_name] = $column_info;

            if ('username' === $column_name) {
                $new_columns['ppc_testuser_column'] = __('Test User', 'capsman-enhanced');
            }
        }

        return $new_columns;
    }

    /**
     * Sets authorisation cookies containing the originating user information.
     *
     * @param int $old_user_id The ID of the originating user, usually the current logged in user.
     * @param bool $pop Optional. Pop the latest user off the auth cookie, instead of appending the new one. Default false.
     * @param string $token Optional. The old user's session token to store for later reuse. Default empty string.
     */
    public function ppc_test_user_set_olduser_cookie($old_user_id, $pop = false, $token = '')
    {
        $secure_auth_cookie = PPC_TestUser::ppc_secure_auth_cookie();
        $secure_olduser_cookie = PPC_TestUser::ppc_secure_olduser_cookie();
        $secure_back_url_cookie = PPC_TestUser::ppc_secure_back_url_cookie();
        $expiration = time() + (86400 * 3); // 3 days
        $auth_cookie = $this->ppc_test_user_get_auth_cookie();
        $olduser_cookie = wp_generate_auth_cookie($old_user_id, $expiration, 'logged_in', $token);

        if ($secure_auth_cookie) {
            $auth_cookie_name = 'wp_ppc_testuser_secure_' . COOKIEHASH;
            $scheme = 'secure_auth';
        } else {
            $auth_cookie_name = 'wp_ppc_testuser_' . COOKIEHASH;
            $scheme = 'auth';
        }

        if ($pop) {
            array_pop($auth_cookie);
        } else {
            array_push($auth_cookie, wp_generate_auth_cookie($old_user_id, $expiration, $scheme, $token));
        }

        $auth_cookie = json_encode($auth_cookie);

        /** This filter is documented in wp-includes/pluggable.php */
        if (!apply_filters('send_auth_cookies', true)) {
            return;
        }

        setcookie($auth_cookie_name, $auth_cookie, $expiration, SITECOOKIEPATH, COOKIE_DOMAIN, $secure_auth_cookie, true);
        setcookie('wp_ppc_testuser_olduser_' . COOKIEHASH, $olduser_cookie, $expiration, COOKIEPATH, COOKIE_DOMAIN, $secure_olduser_cookie, true);
        $get_back_url = isset($_GET['back_url']) ? esc_url_raw($_GET['back_url']) : '';

        setcookie('wp_ppc_testuser_backurl_' . COOKIEHASH, $get_back_url, $expiration, COOKIEPATH, COOKIE_DOMAIN, $secure_back_url_cookie, true);
    }

    /**
     * Clears the cookies containing the originating user, or pops the latest item off the end if there's more than one.
     *
     * @param bool $clear_all Optional. Whether to clear the cookies (as opposed to just popping the last user off the end). Default true.
     */
    public function ppc_test_user_clear_olduser_cookie($clear_all = true)
    {
        $auth_cookie = $this->ppc_test_user_get_auth_cookie();
        if (!empty($auth_cookie)) {
            array_pop($auth_cookie);
        }
        if ($clear_all || empty($auth_cookie)) {

            /** This filter is documented in wp-includes/pluggable.php */
            if (!apply_filters('send_auth_cookies', true)) {
                return;
            }

            $expire = time() - 31536000;
            setcookie('wp_ppc_testuser_' . COOKIEHASH, ' ', $expire, SITECOOKIEPATH, COOKIE_DOMAIN);
            setcookie('wp_ppc_testuser_secure_' . COOKIEHASH, ' ', $expire, SITECOOKIEPATH, COOKIE_DOMAIN);
            setcookie('wp_ppc_testuser_olduser_' . COOKIEHASH, ' ', $expire, COOKIEPATH, COOKIE_DOMAIN);
            setcookie('wp_ppc_testuser_backurl_' . COOKIEHASH, ' ', $expire, COOKIEPATH, COOKIE_DOMAIN);
        } else {
            if (PPC_TestUser::ppc_secure_auth_cookie()) {
                $scheme = 'secure_auth';
            } else {
                $scheme = 'auth';
            }

            $old_cookie = end($auth_cookie);

            $old_user_id = wp_validate_auth_cookie($old_cookie, $scheme);
            if ($old_user_id) {
                $parts = wp_parse_auth_cookie($old_cookie, $scheme);
                $this->ppc_test_user_set_olduser_cookie($old_user_id, true, $parts['token']);
            }
        }
    }

    /**
     * Gets the value of the cookie containing the originating user.
     *
     * @return string|false The old user cookie, or boolean false if there isn't one.
     */
    public function ppc_test_user_get_olduser_cookie()
    {
        if (isset($_COOKIE['wp_ppc_testuser_olduser_' . COOKIEHASH])) {
            return wp_unslash($_COOKIE['wp_ppc_testuser_olduser_' . COOKIEHASH]);
        } else {
            return false;
        }
    }

    /**
     * Gets the value of the cookie containing the originating user.
     *
     * @return string|false The old user cookie, or boolean false if there isn't one.
     */
    public function ppc_test_user_get_back_url_cookie()
    {
        if (isset($_COOKIE['wp_ppc_testuser_backurl_' . COOKIEHASH])) {
            return wp_unslash($_COOKIE['wp_ppc_testuser_backurl_' . COOKIEHASH]);
        } else {
            return false;
        }
    }

    /**
     * Gets the value of the auth cookie containing the list of originating users.
     *
     * @return string[] Array of originating user authentication cookie values. Empty array if there are none.
     */
    public function ppc_test_user_get_auth_cookie()
    {
        if (PPC_TestUser::ppc_secure_auth_cookie()) {
            $auth_cookie_name = 'wp_ppc_testuser_secure_' . COOKIEHASH;
        } else {
            $auth_cookie_name = 'wp_ppc_testuser_' . COOKIEHASH;
        }

        if (isset($_COOKIE[$auth_cookie_name]) && is_string($_COOKIE[$auth_cookie_name])) {
            $cookie = json_decode(wp_unslash($_COOKIE[$auth_cookie_name]));
        }
        if (!isset($cookie) || !is_array($cookie)) {
            $cookie = array();
        }
        return $cookie;
    }

    /**
     * Switches the current logged in user to the specified user.
     *
     * @param int $user_id The ID of the user to switch to.
     * @param bool $remember Optional. Whether to 'remember' the user in the form of a persistent browser cookie. Default false.
     * @param bool $set_old_user Optional. Whether to set the old user cookie. Default true.
     * @return false|WP_User WP_User object on success, false on failure.
     */
    public function ppc_test_user($user_id, $remember = false, $set_old_user = true)
    {
        $user = get_userdata($user_id);

        if (!$user) {
            return false;
        }

        $old_user_id = (is_user_logged_in()) ? get_current_user_id() : false;
        $old_token = function_exists('wp_get_session_token') ? wp_get_session_token() : '';
        $auth_cookie = $this->ppc_test_user_get_auth_cookie();
        $cookie_parts = wp_parse_auth_cookie(end($auth_cookie));

        if ($set_old_user && $old_user_id) {
            // Switching to another user
            $new_token = '';
            $this->ppc_test_user_set_olduser_cookie($old_user_id, false, $old_token);
        } else {
            // Switching back, either after being switched off or after being switched to another user
            $new_token = isset($cookie_parts['token']) ? $cookie_parts['token'] : '';
            $this->ppc_test_user_clear_olduser_cookie(false);
        }

        /**
         * Attaches the original user ID and session token to the new session when a user switches to another user.
         *
         * @param array $session Array of extra data.
         * @param int $user_id User ID.
         * @return array Array of extra data.
         */
        $session_filter = function (array $session, $user_id) use ($old_user_id, $old_token) {
            $session['logged_in_from_id'] = $old_user_id;
            $session['logged_in_from_session'] = $old_token;
            return $session;
        };

        add_filter('attach_session_information', $session_filter, 99, 2);

        wp_clear_auth_cookie();
        wp_set_auth_cookie($user_id, $remember, '', $new_token);
        wp_set_current_user($user_id);

        remove_filter('attach_session_information', $session_filter, 99);

        if ($old_token && $old_user_id && !$set_old_user) {
            // When switching back, destroy the session for the old user
            $manager = WP_Session_Tokens::get_instance($old_user_id);
            $manager->destroy($old_token);
        }

        // When switching, instruct WooCommerce to forget about the current user's session
        if (function_exists('WC')) {
            PPC_TestUser::ppc_forget_woocommerce_session(WC());
        }

        return $user;
    }


    /**
     * Get the roles HTML to add to the user view title.
     *
     * @param \WP_User $user The user object.
     * @return  string
     */
    public function ppc_get_user_roles($user)
    {
        global $wp_roles;

        $user_role_list = '';
        if ($user) {

            $user_roles = $user->roles;

            foreach ($user_roles as $user_role) {
                //add role name
                $user_role_list .= translate_user_role($wp_roles->roles[$user_role]['name']);
                //add seperator
                $user_role_list .= ", ";
            }
            //strip last seperator
            $user_role_list = rtrim($user_role_list, ", ");
        }

        return $user_role_list;
    }

    /**
     * Add body class when admin is testing user.
     *
     * @param array $classes
     * @return array
     */
    public function ppc_body_class($classes)
    {
        $classes[] = 'admin-has-been-logged-in-as-a-user';
        return $classes;
    }

    /**
     * Admin bar custom styles.
     *
     */
    public function ppc_custom_styles()
    {
        ?>
        <style>
            #wpadminbar #wp-admin-bar-ppc-menu-bar ul li, #wpadminbar #wp-admin-bar-ppc-menu-bar .ab-item {
                clear: both;
                z-index: auto;
                line-height: 26px;
            }

            #wpadminbar #wp-admin-bar-ppc-menu-bar > .ab-item .ab-label {
                float: left;
            }

            #wpadminbar #wp-admin-bar-ppc-menu-bar .ab-item .ab-icon.alignright {
                float: right;
                margin-right: 0;
                margin-left: 6px;
            }

            #wpadminbar #wp-admin-bar-ppc-menu-bar > .ab-item .ab-icon {
                top: 2px;
            }

            @media screen and (max-width: 782px) {
                #wpadminbar #wp-admin-bar-ppc-menu-bar > .ab-item > .ab-icon {
                    margin-left: 0;
                }

                #wpadminbar #wp-admin-bar-ppc-menu-bar {
                    display: block;
                    position: static;
                }
            }
        </style>
        <?php
    }

}