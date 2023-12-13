<?php

class PP_Capabilities_Test_User_UI extends PP_Capabilities_Test_User
{
    public function __construct()
    {
        // Admin Dashboard: Add "Test this user" as a Users row action.
        add_filter('user_row_actions', [$this, 'adminUsersRowActions'], 10, 2);
        // Adds "Test this user" to user profile edit page
        add_action('personal_options', [$this, 'adminUserEditAction']);

        // Admin Dashboard, Admin Bar and Front End Footer: Testing indicator, switch back link
        add_action('wp_enqueue_scripts', [$this, 'adminBarScripts'], 9);
        add_action('admin_enqueue_scripts', [$this, 'adminBarScripts'], 9);

        add_action('wp_footer', [$this, 'switchBackNotice']);
        add_action('all_admin_notices', [$this, 'switchBackNotice']);
    }

    /**
     * Adds "Test this user" to Users screen row actions.
     *
     * @param $actions
     * @param $user
     * 
     * @return $action
     */
    public function adminUsersRowActions($actions, $user)
    {
        if (PP_Capabilities_Test_User::canTestUser($user)) {

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
                esc_html__('Test user', 'capability-manager-enhanced')
            );
        }

        return $actions;
    }

    /**
     * Adds "Test this user" to user profile edit page
     *
     * @return void
     */
    public function adminUserEditAction($user)
    {
        if (current_user_can('manage_capabilities_user_testing') && current_user_can('edit_user', $user->ID) && $user->ID !== get_current_user_id()) {
            $link = add_query_arg(
                [
                    'ppc_test_user' => base64_encode($user->ID), 
                    '_wpnonce'      => wp_create_nonce('ppc-test-user')
                ], 
                admin_url('users.php')
            );
            ?>
            <tr class="user-test-user-wrap">
                <th scope="row"><?php esc_html_e('Test user', 'capability-manager-enhanced'); ?></th>
                <td>
                    <?php 
                    printf(
                        '<a href="%s" class="button">%s</a>',
                        esc_url($link),
                        esc_html__('Test this user', 'capability-manager-enhanced')
                    );
                    ?>
                </td>
            </tr>
            <?php
        }
    }

	function adminBarScripts() {
        if (did_action('admin_bar_init') && get_option('cme_test_user_admin_bar', 1)) {
            wp_enqueue_script('jquery');

            $action_name = (is_admin()) ? 'admin_footer' : 'wp_footer';
            add_action($action_name, [$this, 'modifyAdminBar']);
        }
    }

    public function modifyAdminBar()
    {
        if (!PP_Capabilities_Test_User::testerAuth()) {
            return;
        }
        ?>
            <style>
            .ppc-testing-user.top-notice {
                color: #E6A341;
                font-weight: bold !important;
            }

            #wp-admin-bar-user-actions a.pp-capabilities-return {
                color: #E6A341;
                text-align: center;
            }
            </style>

            <script type="text/javascript">
            /* <![CDATA[ */
            jQuery(document).ready( function($) {
                if ($('#wp-admin-bar-user-actions a.pp-capabilities-return').length) {
                    return;
                }

                var switchBackDiv = '<span class="ppc-testing-user top-notice">'
                + '<?php echo wp_kses(
                        esc_html__('Testing: ', 'capability-manager-enhanced'), 
                        [
                            'a' => ['href' => []],
                        ]
                    );
                    ?>'
                + '</span>';

                var sel = $('#wp-admin-bar-my-account a.ab-item:visible:has(span.display-name)');

                $(sel).html(switchBackDiv + $(sel).find('span.display-name').prop('outerHTML'));

                <?php
                $message = sprintf(
                    esc_html__('%1$sReturn to Administrator view%2$s', 'capability-manager-enhanced'),
                    sprintf('<a class="pp-capabilities-return" href="%s">', esc_url($this->switchBackLink())),
                    '</a>'
                );
                ?>
                
                $('#wp-admin-bar-logout').after('<?php echo $message;?>');
            });
            /* ]]> */
            </script>
        <?php
    }

    private function switchBackLink() {
        $user = wp_get_current_user();
        
        return add_query_arg(
            [
                'ppc_test_user'   => base64_encode($user->ID), 
                'ppc_return_back' => 1,
                '_wpnonce'        => wp_create_nonce('ppc-test-user')
            ], 
            home_url()
        );
    }

    /**
     * Add return message notice link
     */
    public function switchBackNotice()
    {
        if (!PP_Capabilities_Test_User::testerAuth()) {
            return;
        }

        if (!is_admin() && !get_option('cme_test_user_footer_notice', 1)) {
            return;
        }

        $user = wp_get_current_user();
        ?>

        <div class="ppc-testing-user notice published">
            <p>
                <span class="dashicons dashicons-admin-users" style="color:#E6A341"></span>
                <?php
                $message = sprintf(
                    esc_html__('Testing as user: %1$s. %2$sReturn to Administrator view%3$s', 'capability-manager-enhanced'),
                    $user->display_name,
                    sprintf('<a href="%s">', esc_url($this->switchBackLink())),
                    '</a>'
                );
                echo wp_kses(
                    $message, [
                        'a' => [
                            'href' => [],
                        ],
                    ]
                );
                ?>
            </p>
        </div>

        <?php if (!is_admin()) : ?>
            <style>
                .ppc-testing-user.notice {
                    position: relative !important;
                    background: #fff !important;
                    border: 1px solid #c3c4c7 !important;
                    border-left-width: 4px !important;
                    border-left-color: #E6A341 !important;
                    box-shadow: 0 1px 1px rgb(0 0 0 / 4%) !important;
                    padding-right: 38px !important;
                    padding: 1px 12px !important;
                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif !important;
                    color: #3c434a !important;
                    font-size: 13px !important;
                    line-height: 1.4em !important;
                    max-width: 640px !important;
                    margin: 0px auto !important;
                    margin-bottom: 20px !important;
                    margin-top: 10px !important;
                    width: 100% !important;
                    z-index: 99999;
                }
                .ppc-testing-user.notice .dashicons {
                    color: #E6A341 !important;
                }
                .ppc-testing-user.notice p {
                    font-size: 13px !important;
                    line-height: 1.5 !important;
                    margin: 0.5em 0 !important;
                    padding: 2px !important;
                }
                .ppc-testing-user.notice a {
                    padding-bottom: 2px !important;
                }
                .ppc-testing-user.notice a,
                .ppc-testing-user.notice a:hover,
                .ppc-testing-user.notice a:active
                .ppc-testing-user.notice a:visited {
                    color: #2271b1 !important;
                    text-decoration: underline !important;
                }
            </style>
        <?php else:?>
            <style>
                .ppc-testing-user.notice {
                    position: relative !important;
                    background: #fff !important;
                    border: 1px solid #c3c4c7 !important;
                    border-left-width: 4px !important;
                    border-left-color: #E6A341 !important;
                }
            </style>
        <?php endif;
    }
}
