<div class="wrap publishpress-caps-manage pressshack-admin-wrapper pp-capability-menus-wrapper">
    <div id="icon-capsman-admin" class="icon32"></div>
    <h2><?php _e('Menu Restrictions', 'capsman-enhanced'); ?></h2>

    <form method="post" id="ppc-admin-menu-form" action="admin.php?page=<?php echo $this->ID ?>-pp-admin-menus">
        <fieldset>
            <table id="akmin">
                <tr>
                    <td class="content">

                        <dl>
                            <dt><?php _e('Pro Feature: Admin Menu Restrictions', 'capsman-enhanced'); ?></dt>

                            <dd>
                                <div class="publishpress-headline">
                                    <span class="cme-subtext">
                                    <?php printf(
                                        __('To restrict access to any Admin Menu or Submenu item per-role, upgrade to %sCapabilities Pro%s.', 'capsman-enhanced'),
                                        "<a href='https://publishpress.com/links/capabilities-banner'>",
                                        '</a>'
                                        );
                                    ?>
                                    </span>
                                </div>
                            </dd>

                        </dl>

                    </td>
                </tr>
            </table>

        </fieldset>

    </form>

    <?php if (!defined('PUBLISHPRESS_CAPS_PRO_VERSION') || get_option('cme_display_branding')) {
        cme_publishpressFooter();
    }
    ?>
</div>
<?php
