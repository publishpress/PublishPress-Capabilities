<?php
if (!class_exists('PP_Capabilities_Admin_Notices')) {
    class PP_Capabilities_Admin_Notices
    {
        /**
         * @var 
         */
        private $admin_notice_data;

        public function __construct()
        {
            $this->admin_notice_data = (array) get_option('cme_admin_notice_data', []);

            // Add admin notices script and styles
            add_action('admin_enqueue_scripts', [$this, 'admin_scripts']);
            // Wrap admin menus in custom opening div
            $hooks = ['admin_notices', 'user_admin_notices', 'network_admin_notices'];
            foreach ($hooks as $hook) {
                add_action($hook, [$this, 'start_hook_capture'], PHP_INT_MIN);
            }
            // Close admin menu custom opening div
            add_action('all_admin_notices', [$this, 'end_hook_capture'], PHP_INT_MAX - 1);
            // Add admin notices to admin toolbar
            add_action('admin_bar_menu', [$this, 'add_toolbar_item'], 998);
            // Render our toolbar panel in the footer
            add_action('admin_footer', [$this, 'render_panel']);
            // Admin notices whitelist, blacklist and undo ajax callback
            add_action('wp_ajax_ppc_admin_notice_action', [$this, 'adminNoticeAjaxHandler']);
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
         * Add admin notices script and styles
         * 
         * @return void
         */
        public function admin_scripts() {
                
            //enqueue styles
            wp_enqueue_style(
                'ppc-admin-notice-css', 
                plugin_dir_url(CME_FILE) . 'includes/admin-notices/assets/css/admin-notices.css', 
                [], 
                PUBLISHPRESS_CAPS_VERSION, 
                'all'
            );

            //enqueue scripts
            wp_enqueue_script(
                'ppc-admin-notice-js', 
                plugin_dir_url(CME_FILE) . 'includes/admin-notices/assets/js/admin-notices.js', 
                ['jquery'], 
                PUBLISHPRESS_CAPS_VERSION, 
                false
            );

            // localize scripts
            wp_localize_script(
                'ppc-admin-notice-js',
                'ppcAdminNoticesData',
                [
                    'nonce' => wp_create_nonce('ppc-admin-notices-action'),
                    'admin_notice_options' => $this->admin_notice_options(),
                    'admin_notice_data' => $this->admin_notice_data,
                    'whitelist_label' => esc_html__('Whitelist Notice', 'capability-manager-enhanced'),
                    'blacklist_label' => esc_html__('Blacklist Notice', 'capability-manager-enhanced'),
                    'remove_whitelist_label' => esc_html__('Revert Whitelist', 'capability-manager-enhanced'),
                    'remove_blacklist_label' => esc_html__('Revert Blacklist', 'capability-manager-enhanced'),
                    'whitelist_note' => esc_html__('Whitelisted notices will no longer be removed from admin pages.', 'capability-manager-enhanced'),
                    'blacklist_note' => esc_html__('Blacklisted notices will be removed completely from admin pages and Capabilities admin notice notification.', 'capability-manager-enhanced'),
                ]
            );

        }

        /**
         * Wrap admin menus in custom opening div
         * @return void
         */
        public function start_hook_capture() {
            // Wrap the whole admin notice inside our hidden div
            echo '<div class="ppc-admin-notices-selector" style="display: none;">';
        }

        /**
         * Close admin menu custom opening div
         * @return void
         */
        public function end_hook_capture() {
            // close the opened notice hidden selector
            echo '</div>';
        }

        /**
         * Get admin notice option for current role
         * 
         * @return array
         */
        private function admin_notice_options() {
            global $admin_notice_options;

            if (!is_array($admin_notice_options)) {
                $cme_admin_notice_options = (array) get_option('cme_admin_notice_options');
                
                $admin_notice_options = [
                    'enable_toolbar_access' => 0,
                    'notice_type_remove' => [],
                    'notice_type_display' => []
                ];

                if (is_user_logged_in()) {
                    $user = wp_get_current_user();
                    foreach ((array) $user->roles as $user_role) {
                        if (isset($cme_admin_notice_options[$user_role])) {
                            $data = $cme_admin_notice_options[$user_role];
                            // Merge enable_toolbar_access, take the first non-empty value
                            if (empty($admin_notice_options["enable_toolbar_access"]) && !empty($data["enable_toolbar_access"])) {
                                $admin_notice_options["enable_toolbar_access"] = $data["enable_toolbar_access"];
                            }
                            // Merge notice_type_remove
                            if (!empty($data["notice_type_remove"])) {
                                $admin_notice_options["notice_type_remove"] = array_unique(
                                    array_merge($admin_notice_options["notice_type_remove"], $data["notice_type_remove"])
                                );
                            }
                            // Merge notice_type_display
                            if (!empty($data["notice_type_display"])) {
                                $admin_notice_options["notice_type_display"] = array_unique(
                                    array_merge($admin_notice_options["notice_type_display"], $data["notice_type_display"])
                                );
                            }
                        }
                    }
                }
            }
            
            return $admin_notice_options;
        }


        /**
         * Check if current user can manage notice
         * @return bool
         */
        private function canSeeAdminToolbar() {
            $admin_notice_options = $this->admin_notice_options();

            $can_see_admin_toobal = false;

            if (!empty($admin_notice_options['enable_toolbar_access']) && !empty($admin_notice_options['notice_type_remove']) && !empty($admin_notice_options['notice_type_display'])) {
                $can_see_admin_toobal = true;
            }

            return $can_see_admin_toobal;
        }

        /**
         * Add admin notices to admin toolbar
         * 
         * @param \WP_Admin_Bar $wp_admin_bar WordPress admin bar.
         * 
         * @return void
         */
        public function add_toolbar_item($wp_admin_bar) {

            if (!$this->canSeeAdminToolbar() || !is_admin_bar_showing()) {
                return;
            }
            
            $args = [
                'id'     => 'ppc-admin-notices-panel',
                'title'  => '<span class="ab-label">' . esc_html__('Admin Notices', 'capability-manager-enhanced') . ' <span class="ppc-admin-notices-count"></span></span>',
                'href'   => '#',
                'parent' => 'top-secondary',
                'meta'   => [
                    'class' => 'ppc-admin-notices-toolbar-item',
                ],
            ];
            $wp_admin_bar->add_node($args);
        }
        
        /**
         * Render our toolbar panel in the footer
         * @return void
         */
        public function render_panel() {
            if (!$this->canSeeAdminToolbar()) 
            {
                return;
            }
            ?>
            <div id="ppc-admin-notices-panel">
                <div class="admin-notices-tab" style="display: none;">
                    <div class="admin-notices-button-group" data-hide-selector=".ppc-panel-notice-item">
                        <label class="active-notices selected" style="display: none;">
                            <input type="radio" name="admin_notices_tab" value=".active-notices-item" checked>
                            <?php esc_html_e('Active Notices', 'capability-manager-enhanced'); ?><sup class="tab-notice-count"></sup>
                        </label>
                        <label class="whitelisted-notices" style="display: none;">
                            <input type="radio" name="admin_notices_tab" value=".whitelisted-notices-item">
                            <?php esc_html_e('Whitelisted Notices', 'capability-manager-enhanced'); ?><sup class="tab-notice-count"></sup>
                        </label>
                        <label class="blacklisted-notices" style="display: none;">
                            <input type="radio" name="admin_notices_tab" value=".blacklisted-notices-item">
                            <?php esc_html_e('Blacklisted Notices', 'capability-manager-enhanced'); ?><sup class="tab-notice-count"></sup>
                        </label>
                    </div>
                </div>
                <div class="ppc-admin-notices-panel-content"></div>
            </div>
            <?php
        }

        /**
         * Admin notices whitelist, blacklist and undo ajax callback
         *
         */
        public function adminNoticeAjaxHandler()
        {
            $response['status']  = 'error';
            $response['message'] = esc_html__('An error occured!', 'capability-manager-enhanced');
            $response['content'] = '';

            $nonce   = isset($_POST['nonce']) ? sanitize_key($_POST['nonce']) : '';
            $action_type = isset($_POST['action_type']) ? sanitize_text_field($_POST['action_type']) : '';
            $action_option = isset($_POST['action_option']) ? sanitize_text_field($_POST['action_option']) : '';
            $notice_id = isset($_POST['notice_id']) ? sanitize_text_field($_POST['notice_id']) : '';

            if (!$this->canSeeAdminToolbar()) {
                $response['message'] = esc_html__('You do not have permission to manage admin notices.', 'capability-manager-enhanced');
            } elseif (!wp_verify_nonce($nonce, 'ppc-admin-notices-action')) {
                $response['message'] = esc_html__('Invalid action. Reload this page and try again.', 'capability-manager-enhanced');
            } elseif (empty($action_type) || empty($action_option) && empty($notice_id)) {
                $response['message'] = esc_html__('Invalid form.', 'capability-manager-enhanced');
            } else {
                
                $admin_notice_data = (array) get_option('cme_admin_notice_data', []);

                // remove current notice from both whitelist and blacklist if present
                if (!empty($admin_notice_data['whitelist_notices'])) {
                    $admin_notice_data['whitelist_notices'] = array_values(array_diff($admin_notice_data['whitelist_notices'], [$notice_id]));
                }
                if (!empty($admin_notice_data['blacklist_notices'])) {
                    $admin_notice_data['blacklist_notices'] = array_values(array_diff($admin_notice_data['blacklist_notices'], [$notice_id]));
                }

                // add notice to whitelist/blacklist if action is default and not undo action
                if ($action_option == 'default') {
                    if ($action_type == 'whitelist') {
                        $admin_notice_data['whitelist_notices'][] = $notice_id;
                    } else {
                        $admin_notice_data['blacklist_notices'][] = $notice_id;
                    }
                }

                update_option('cme_admin_notice_data', $admin_notice_data);

                $response['message'] = esc_html__('Admin notice status updated successfully.', 'capability-manager-enhanced');
                $response['status']  = 'success';
            }

            wp_send_json($response);
        }
    }
}