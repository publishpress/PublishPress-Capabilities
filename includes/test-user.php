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
        //adds Test this user to users row action.
        add_filter('user_row_actions', [$this, 'ppc_test_user_row_actions'], 10, 2);
        //add return message notice link
        add_action('wp_head', [$this, 'ppc_test_user_revert_notice']);
        add_action('all_admin_notices', [$this, 'ppc_test_user_revert_notice']);
        //clear test user cookie on logout and login
        add_action('wp_logout', [$this, 'ppc_test_user_clear_olduser_cookie']);
        add_action('wp_login', [$this, 'ppc_test_user_clear_olduser_cookie']);

        $this->ppc_test_user_action();
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
     * Adds Test this user to users row action.
     *
     * @param $actions
     * @param $user
     * 
     * @return $action
     */
    public function ppc_test_user_row_actions($actions, $user)
    {
        if (current_user_can('manage_capabilities') && current_user_can('edit_user', $user->ID) && $user->ID !== get_current_user_id()) {

            $link = add_query_arg(
                [
                    'ppc_test_user' => base64_encode($user->ID), 
                    '_wpnonce'      => wp_create_nonce('ppc-test-user')
                ], 
                admin_url('users.php')
            );

            $actions['ppc_test_user'] = sprintf(
                '<a href="%s">%s</a>',
                esc_url($link),
                esc_html__('Test this user', 'capsman-enhanced')
            );
        }

        return $actions;
    }

    /**
     * Test user process
     */
    public function ppc_test_user_action()
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
                $user_auth        = wp_unslash(self::ppc_test_user_tester_auth());
                $original_user_id = wp_validate_auth_cookie($user_auth, 'logged_in');
                if ($original_user_id) {
                    wp_set_auth_cookie($original_user_id, false);
                    // Unset the cookie
                    $this->ppc_test_user_clear_olduser_cookie();
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
    public function ppc_test_user_clear_olduser_cookie() {
        // Unset the cookie
        // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.cookies_setcookie
        setcookie('ppc_test_user_tester_'.COOKIEHASH, 0, time() - self::AUTH_COOKIE_HOUR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true);
    }


    /**
     * Add return message notice link
     */
    public function ppc_test_user_revert_notice()
    {

        if (!empty(self::ppc_test_user_tester_auth())) {
            $user = wp_get_current_user();

            $return_link = add_query_arg(
                [
                    'ppc_test_user'   => base64_encode($user->ID), 
                    'ppc_return_back' => 1,
                    '_wpnonce'        => wp_create_nonce('ppc-test-user')
                ], 
                home_url()
            );
            ?>
            <div class="ppc-testing-user updated notice is-dismissible published">
                <p>
                    <span class="dashicons dashicons-admin-users" style="color:#56c234"></span>
                    <?php
                    $message = sprintf(
                        ' <a href="%s">%s</a>',
                        esc_url($return_link),
                        esc_html(
                            sprintf(
                                esc_html__('You are testing as this user: %1$s. Click here to return to your Administrator view.', 'capsman-enhanced'),
                                $user->display_name
                            )
                        )
                    );
                    echo wp_kses(
                        $message, array(
                        'a' => array(
                            'href' => array(),
                        ),
                        )
                    );
                    ?>
                </p>
            </div>
            <?php
        }
    }

    /**
     * Get tester user from cookie
     */
    private static function ppc_test_user_tester_auth()
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
