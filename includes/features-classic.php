<table class="wp-list-table widefat fixed pp-capability-menus-select post-features-classic" style="display:none;">

    <thead>
    <tr>
        <th class="menu-column"><?php _e('Classic Editor Screen', 'capsman-enhanced') ?></th>
        <th class="restrict-column"><?php _e('Restrict', 'capsman-enhanced') ?></th>
    </tr>
    </thead>

    <tfoot>
    <tr>
        <th class="menu-column"><?php _e('Classic Editor Screen', 'capsman-enhanced') ?></th>
        <th class="restrict-column"><?php _e('Restrict', 'capsman-enhanced') ?></th>
    </tr>
    </tfoot>

    <tbody>
    <tr class="ppc-menu-row parent-menu">

        <td class="menu-column ppc-menu-item">
            <label for="classic-check-all-item">
                                                        <span class="classic menu-item-link check-all-menu-link">
                                                            <strong><i class="dashicons dashicons-leftright"></i>
                                                            <?php _e('Toggle all', 'capsman-enhanced'); ?>
                                                            </strong>
                                                        </span></label>
        </td>

        <td class="restrict-column ppc-menu-checkbox">
            <input id="classic-check-all-item" class="check-item classic check-all-menu-item" type="checkbox"/>
        </td>

    </tr>


    <?php


    foreach ($ce_metaboxes as $index => $metabox) {
        $sn++;
        if ('' !== $metabox) {
            ?>
            <tr class="ppc-menu-row parent-menu">
                <td class="menu-column ppc-menu-item">

                    <label for="check-item-<?php echo $sn; ?>">
<span class="classic menu-item-link<?php echo (in_array($metabox, $ce_post_disabled)) ? ' restricted' : ''; ?>">
<strong><i class="dashicons dashicons-arrow-right"></i>
<?php echo $ce_metaboxes_names[$index]; ?>
</strong></span>
                    </label>
                </td>

                <td class="restrict-column ppc-menu-checkbox">
                    <input id="check-item-<?php echo $sn; ?>" class="check-item" type="checkbox"
                           name="capsman_feature_ce_post_disabled[]"
                           value="<?php echo $metabox; ?>"<?php echo (in_array($metabox, $ce_post_disabled)) ? ' checked' : ''; ?> />
                </td>
            </tr>
            <?php
        }
    }
    ?>

    </tbody>
</table>