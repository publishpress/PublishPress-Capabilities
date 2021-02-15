<table class="wp-list-table widefat fixed pp-capability-menus-select post-features-gutenberg">

    <thead>
    <tr>
        <th class="menu-column"><?php _e('Gutenberg Screen', 'capsman-enhanced') ?></th>
        <th class="restrict-column"><?php _e('Restrict', 'capsman-enhanced') ?></th>
    </tr>
    </thead>

    <tfoot>
    <tr>
        <th class="menu-column"><?php _e('Gutenberg Screen', 'capsman-enhanced') ?></th>
        <th class="restrict-column"><?php _e('Restrict', 'capsman-enhanced') ?></th>
    </tr>
    </tfoot>

    <tbody>
    <tr class="ppc-menu-row parent-menu">

        <td class="menu-column ppc-menu-item">
            <label for="gutenberg-check-all-item">
                                                        <span class="gutenberg menu-item-link check-all-menu-link">
                                                            <strong><i class="dashicons dashicons-leftright"></i>
                                                            <?php _e('Toggle all', 'capsman-enhanced'); ?>
                                                            </strong>
                                                        </span></label>
        </td>

        <td class="restrict-column ppc-menu-checkbox">
            <input id="gutenberg-check-all-item" class="check-item gutenberg check-all-menu-item" type="checkbox"/>
        </td>

    </tr>


    <?php


    foreach ($gutenberg_metaboxes as $name => $metabox) {
        $sn++;
        ?>
        <tr class="ppc-menu-row parent-menu">
            <td class="menu-column ppc-menu-item">

                <label for="check-item-<?php echo $sn; ?>">
<span class="gutenberg menu-item-link<?php echo (in_array($metabox, $gutenberg_post_disabled)) ? ' restricted' : ''; ?>">
<strong><i class="dashicons dashicons-arrow-right"></i>
<?php echo $name; ?>
</strong></span>
                </label>
            </td>

            <td class="restrict-column ppc-menu-checkbox">
                <input id="check-item-<?php echo $sn; ?>" class="check-item" type="checkbox"
                       name="capsman_feature_gutenberg_post_disabled[]"
                       value="<?php echo $metabox; ?>"<?php echo (in_array($metabox, $gutenberg_post_disabled)) ? ' checked' : ''; ?> />
            </td>
        </tr>
        <?php
    }
    ?>

    </tbody>
</table>