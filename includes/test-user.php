<?php

class PP_Capabilities_Test_User
{
    /**
     * Cookie expiration seconds
     */
    const AUTH_COOKIE_EXPIRATION = DAY_IN_SECONDS;

    /**
     * Cookie hour in seconds
     */
    const AUTH_COOKIE_HOUR_IN_SECONDS = HOUR_IN_SECONDS;

    public function __construct()
    {
        //clear test user cookie on logout and login
        add_action('wp_logout', [$this, 'clearTestUserCookie']);
        add_action('wp_login', [$this, 'clearTestUserCookie']);

        $this->handleUserAction();

        if (is_admin() || self::testerAuth()) {
            require_once (PUBLISHPRESS_CAPS_ABSPATH . '/includes/test-user-ui.php');
            new PP_Capabilities_Test_User_UI();
        }
    }

    /**
     * Initialize test user component
     */
    public static function init()
    {
        $instance = new self;
        return $instance;
    }

    /**
     * Test user process
     */
    public function handleUserAction()
    {
        global $current_user;
            
        if (!is_user_logged_in() || !isset($_GET['ppc_test_user']) || !isset($_GET['_wpnonce'])) {
            return;
        }

        if (!wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'ppc-test-user')) {
            wp_die(esc_html__('Your link has expired, refresh the page and try again.', 'capsman-enhanced'));
        }

        $request_user_id = isset($_GET['ppc_test_user']) ? (int) base64_decode(sanitize_text_field($_GET['ppc_test_user'])) : 0;
        $ppc_return_back = isset($_GET['ppc_return_back']) ? (int) sanitize_text_field($_GET['ppc_return_back']) : 0;
        $request_user    = get_userdata($request_user_id);
        
        if (!$request_user || (is_object($request_user) && !isset($request_user->ID))) {
            wp_die(esc_html__('Unable to retrieve user data.', 'capsman-enhanced'));
        } else {
            if ($ppc_return_back > 0) {
                $user_auth        = wp_unslash(self::testerAuth());
                $original_user_id = wp_validate_auth_cookie($user_auth, 'logged_in');

                if ($original_user_id) {
                    wp_set_auth_cookie($original_user_id, false);

                    // Unset the cookie
                    $this->clearTestUserCookie();

                    //redirect back to admin dashboard
                    wp_safe_redirect(admin_url());
                    exit;
                }
            } elseif (is_admin() && current_user_can('manage_capabilities') && current_user_can('edit_user', $request_user_id)) {

                // Create and set auth cookie for current user before switching
                $token = function_exists('wp_get_session_token') ? wp_get_session_token() : '';
                $orig_auth_cookie = wp_generate_auth_cookie($current_user->ID, time() + self::AUTH_COOKIE_EXPIRATION, 'logged_in', $token);

                // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.cookies_setcookie
                setcookie('ppc_test_user_tester_'.COOKIEHASH, $orig_auth_cookie, time() + self::AUTH_COOKIE_EXPIRATION, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true);

                // Login as the other user
                wp_set_auth_cookie($request_user_id, false);

                //redirect user to admin dashboard
                wp_safe_redirect(admin_url());
                exit;
            }
        }
    }

    /**
     * Clear test user cookie on logout and login
     *
     * @return void
     */
    public function clearTestUserCookie() {
        // Unset the cookie
        // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.cookies_setcookie
        setcookie('ppc_test_user_tester_'.COOKIEHASH, 0, time() - self::AUTH_COOKIE_HOUR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true);
    }

    /**
     * Get tester user from cookie
     */
    protected static function testerAuth()
    {
        $auth_key = 'ppc_test_user_tester_'.COOKIEHASH;
        if (isset($_COOKIE[$auth_key]) && !empty($_COOKIE[$auth_key])) {
            // phpcs:ignore WordPressVIPMinimum.Variables.RestrictedVariables.cache_constraints___COOKIE
            return $_COOKIE[$auth_key];
        } else {
            return false;
        }
    }
}
