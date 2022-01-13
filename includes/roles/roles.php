<div class="wrap publishpress-caps-manage pressshack-admin-wrapper pp-capability-roles-wrapper">

    <div class="wrap">
        <h1 class="wp-heading-inline"><?php esc_html_e('Roles', 'capsman-enhanced') ?> </h1>
        <?php
        if (isset($_REQUEST['s']) && $search_str = esc_attr(wp_unslash(sanitize_text_field($_REQUEST['s'])))) {
            /* translators: %s: search keywords */
            printf(' <span class="subtitle">' . esc_html__('Search results for &#8220;%s&#8221;', 'capsman-enhanced') . '</span>', esc_html($search_str));
        }

        //the roles table instance
        $table = pp_capabilities_roles()->admin->get_roles_list_table();
        $table->prepare_items();
        pp_capabilities_roles()->notify->display();
        ?>
        <hr class="wp-header-end">
        <div id="ajax-response"></div>
        <form class="search-form wp-clearfix" method="get">
            <?php $table->search_box(esc_html__('Search Roles', 'capsman-enhanced'), 'roles'); ?>
        </form>
        <div id="col-container" class="wp-clearfix">
            <div id="col-left">
                <div class="col-wrap">
                    <div class="form-wrap">
                        <h2><?php esc_html_e('Add New Role', 'capsman-enhanced') ?> </h2>
                        <form id="addrole" method="post" action="" class="validate">
                            <input type="hidden" name="action" value="pp-roles-add-role">
                            <input type="hidden" name="screen" value="<?php echo 'capabilities_page_pp-capabilities-roles';?>">
                            <?php
                            wp_nonce_field('add-role');
                            ?>
                            <div class="form-field form-required">
                                <label for="name"><?php esc_html_e('Name', 'capsman-enhanced') ?> </label>
                                <input name="name" id="name" type="text" value="" size="40">
                                <p><?php esc_html_e('The name is how it appears on your site.', 'capsman-enhanced'); ?></p>
                            </div>

                            <p class="submit">
                                <input type="submit" name="submit" id="submit" class="button button-primary"
                                       value="<?php esc_attr_e('Add'); ?> ">
                            </p>
                        </form>
                    </div>
                </div>
            </div>
            <div id="col-right">
                <div class="col-wrap">
                    <form action="" method="post">
                        <?php $table->display(); //Display the table ?>
                    </form>
                    <div class="form-wrap edit-term-notes">
                        <p><?php esc_html__('Description here.', 'capsman-enhanced') ?></p>
                    </div>
                </div>
            </div>
        </div>
        <form method="get">
            <?php $table->inline_edit() ?>
        </form>

    </div>


    <?php if (!defined('PUBLISHPRESS_CAPS_PRO_VERSION') || get_option('cme_display_branding')) {
        cme_publishpressFooter();
    }
    ?>
</div>
<?php
