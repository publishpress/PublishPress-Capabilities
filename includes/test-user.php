<?php

class PP_Capabilities_Test_User
{

    public function __construct()
    {
        //adds Test this user to users row action.
        add_filter('user_row_actions', [$this, 'ppc_test_user_row_actions'], 10, 2);
        //add return message notice link
        add_action('wp_head', [$this, 'ppc_test_user_revert_notice']);
        add_action('all_admin_notices', [$this, 'ppc_test_user_revert_notice']);

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
        $ppc_return_user = isset($_GET['ppc_return_user']) ? (int) base64_decode(sanitize_text_field($_GET['ppc_return_user'])) : 0;
        $request_user    = get_userdata($request_user_id);
        
        if (!$request_user || (is_object($request_user) && !isset($request_user->ID))) {
            wp_die(esc_html__('Unable to retrieve user data.', 'capsman-enhanced'));
        } else {
            if ($ppc_return_user > 0) {
                wp_set_auth_cookie($ppc_return_user, false);
                // Unset the cookie
                // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.cookies_setcookie
                setcookie('ppc_test_user_tester_'.COOKIEHASH, 0, time()-3600, SITECOOKIEPATH, COOKIE_DOMAIN, false, true);
                //redirect back to admin dashboard
                wp_redirect(admin_url());
                exit;
            } elseif (is_admin() && current_user_can('manage_capabilities') && current_user_can('edit_user', $request_user_id)) {

                // store current user cookie to enable switch back
                $hashed_id = $this->ppc_encrypt_decrypt_string('encrypt', $current_user->ID);
                // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.cookies_setcookie
                setcookie('ppc_test_user_tester_'.COOKIEHASH, $hashed_id, 0, SITECOOKIEPATH, COOKIE_DOMAIN, false, true);
                // Login as the other user
                wp_set_auth_cookie($request_user_id, false);
                //redirect user to admin dashboard
                wp_redirect(admin_url());
                exit;
            }
        }
    }


    /**
     * Add return message notice link
     */
    public function ppc_test_user_revert_notice()
    {

        $tester_user_id = self::ppc_test_user_tester_id();

        if (!empty($tester_user_id)) {
            $user = wp_get_current_user();

            $return_link = add_query_arg(
                [
                    'ppc_test_user'   => base64_encode($user->ID), 
                    'ppc_return_user' => base64_encode($tester_user_id),
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
    private static function ppc_test_user_tester_id()
    {
        $key = 'ppc_test_user_tester_'.COOKIEHASH;
        if (isset($_COOKIE[$key]) && !empty($_COOKIE[$key])) {
            // phpcs:ignore WordPressVIPMinimum.Variables.RestrictedVariables.cache_constraints___COOKIE
            $user_id = self::ppc_encrypt_decrypt_string('decrypt', sanitize_text_field($_COOKIE[$key]));
            return $user_id;
        } else {
            return false;
        }
    }

    /**
     * Encript and Decrypt
     */
    private static function ppc_encrypt_decrypt_string($action, $string)
    {
        $output = false;
        $encrypt_method = "AES-256-CBC";
        //secret key
        $secret_key = wp_salt();
        $secret_iv  = wp_salt('secure_auth');

        // hash
        $key = hash('sha256', $secret_key);

        // iv - encrypt method AES-256-CBC expects 16 bytes - else it's result in warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        if ($action == 'encrypt') {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } elseif ($action == 'decrypt') {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }
        return $output;
    }

}
