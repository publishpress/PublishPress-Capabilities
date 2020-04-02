<?php
/**
 * Capability Manager Backup Tool.
 * Provides backup and restore functionality to Capability Manager.
 *
 * @version		$Rev: 198515 $
 * @author		Jordi Canals
 * @copyright   Copyright (C) 2009, 2010 Jordi Canals
 * @license		GNU General Public License version 2
 * @link		http://alkivia.org
 * @package		Alkivia
 * @subpackage	CapsMan
 *
 * 
 *	Copyright 2009, 2010 Jordi Canals <devel@jcanals.cat>
 *
 *	Modifications Copyright 2020, PublishPress <help@publishpress.com>
 * 
 *	This program is free software; you can redistribute it and/or
 *	modify it under the terms of the GNU General Public License
 *	version 2 as published by the Free Software Foundation.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

global $wpdb;

$auto_backups = $wpdb->get_results("SELECT option_name, option_value FROM $wpdb->options WHERE option_name LIKE 'cme_backup_auto_%' ORDER BY option_id DESC");
?>

<div class="wrap publishpress-caps-manage pressshack-admin-wrapper">
	<div id="icon-capsman-admin" class="icon32"></div>
	<h2><?php printf( __('Backup Tool for %1$sPublishPress Capabilities%2$s', 'capsman-enhanced'), '<a href="admin.php?page=capsman">', '</a>' );?></h2>

	<form method="post" action="admin.php?page=<?php echo $this->ID ?>-tool">
	<?php wp_nonce_field('capsman-backup-tool'); ?>
	<fieldset>
	<table id="akmin">
	<tr>
		<td class="content">
		<dl>
			<dt><?php _e('Backup and Restore', 'capsman-enhanced'); ?></dt>
			<dd>
				<table width='100%' class="form-table">
				<tr>
					<th scope="row"><?php _e('Select action:', 'capsman-enhanced'); ?></th>
					<td>
						<select id="cme_select_backup" name="action">
							<option value="backup"> <?php _e('Backup roles and capabilities', 'capsman-enhanced'); ?> </option>

							<?php
							if ( $initial = get_option( 'capsman_backup_initial' ) ):?>
								<option value="restore_initial"> <?php _e('Restore initial backup', 'capsman-enhanced'); ?> </option>
							<?php endif;?>

							<option value="restore"> <?php _e('Restore last manually saved backup', 'capsman-enhanced'); ?> </option>

							<?php foreach($auto_backups as $row):
								$arr = explode('_', str_replace('cme_backup_auto_', '', $row->option_name));
								$arr[1] = str_replace('-', ':', $arr[1]);
								$date_caption = implode(' ', $arr);
							?>
								<option value="<?php echo $row->option_name;?>"><?php printf(__('Restore auto-backup (%s)', 'capsman-enhanced'), $date_caption);?></option>
							<?php endforeach;?>
						</select> &nbsp;
						<input type="submit" name="Perform" value="<?php _e('Do Action', 'capsman-enhanced') ?>" class="button-primary" />

						<p>&nbsp;<span class="cme-subtext">
						<?php
						_e('To view the contents of a backup, select it in the dropdown above.', 'press-permit-core');
						?>
						</span></p>
					</td>
				</tr>
				</table>
			</dd>

		<script type="text/javascript">
		/* <![CDATA[ */
		jQuery(document).ready( function($) {
			$('#cme_select_backup').click(function() {
				$('div.cme-show-backup').hide();

				var selected_val = $(this).val();

				switch(selected_val) {
					case 'restore_initial':
						$('#cme_display_capsman_backup_initial').show();
						break;
					case 'restore':
						$('#cme_display_capsman_backup').show();
						break;
					default:
						$('#cme_display_' + selected_val).show();
				}
			});
		});
		/* ]]> */
		</script>

		<?php
			global $wp_roles;

			$initial_caption = ( $backup_datestamp = get_option( 'capsman_backup_initial_datestamp' ) ) ? sprintf( __('Initial Backup - %s', 'capsman-enhanced'), date( 'j M Y, g:i a', $backup_datestamp ) ) : __('Initial Backup', 'capsman-enhanced');
			$last_caption = ( $backup_datestamp = get_option( 'capsman_backup_datestamp' ) ) ? sprintf( __('Last Manual Backup - %s', 'capsman-enhanced'), date( 'j M Y, g:i a', $backup_datestamp ) ) : __('Last Backup', 'capsman-enhanced');
			
			$backups = array( 
				'capsman_backup_initial' => $initial_caption, 
				'capsman_backup' =>			$last_caption,
			);
			
			foreach($auto_backups as $row) {
				$arr = explode('_', str_replace('cme_backup_auto_', '', $row->option_name));
				$arr[1] = str_replace('-', ':', $arr[1]);
				$backups[$row->option_name] = "Auto-backup from " . implode(' ', $arr);
			}

			foreach( $backups as $name => $caption ) {
				if ( $backup_data = get_option( $name ) ) :?>
					<div id="cme_display_<?php echo $name;?>" style="display:none;padding-left:20px;" class="cme-show-backup">
					<h3><?php printf( __( "%s (%s roles)", 'capsman-enhanded' ), $caption, count($backup_data) ); ?></h3>
					
					<?php foreach( $backup_data as $role => $props ) :?>
						<?php if ( ! isset( $props['name'] ) ) continue;?>
						<?php 
						$level = 0;
						for( $i=10; $i>=0; $i--) {
							if ( ! empty( $props['capabilities']["level_{$i}"] ) ) {
								$level = $i;
								break;
							}
						}
						?>
						<?php 
						$role_caption = $props['name'];
						if ( empty( $wp_roles->role_objects[$role] ) ) $role_caption = "<span class='cme-plus' style='color:green;font-weight:800'>$role_caption</span>";?>
						<h4><?php printf( __( '%s (level %s)', 'capsman-enhanced' ), translate_user_role($role_caption), $level );?></h4>
						<ul style="list-style:disc;padding-left:30px">
						
						<?php
						ksort( $props['capabilities'] );
						foreach( $props['capabilities'] as $cap_name => $val ) :
							if ( 0 === strpos( $cap_name, 'level_' ) ) continue;
						?>
							<?php if ( $val && ( empty( $wp_roles->role_objects[$role] ) || empty( $wp_roles->role_objects[$role]->capabilities[$cap_name] ) ) ) $cap_name = "<span class='cme-plus' style='color:green;font-weight:800'>$cap_name</span>";?>
							<li><?php echo ( $val ) ? $cap_name : "<strike>$cap_name</strike>";?></li>
						<?php endforeach;?>

						</ul>
					<?php endforeach;?>
					</div>
				<?php endif;
			}
			?>
		</dl>

		<dl>
			<dt><?php if ( defined('WPLANG') && WPLANG && ( 'en_EN' != WPLANG ) ) _e('Reset WordPress Defaults', 'capsman-enhanced'); else echo 'Reset Roles to WordPress Defaults';?></dt>
			<dd>
				<p style="text-align:center;"><strong><span style="color:red;"><?php _e('WARNING:', 'capsman-enhanced'); ?></span> <?php if ( defined('WPLANG') && WPLANG && ( 'en_EN' != WPLANG ) ) _e('Reseting default Roles and Capabilities will set them to the WordPress install defaults.', 'capsman-enhanced'); else echo 'This will delete and/or modify stored role definitions.'; ?></strong><br />
					<br />
					<?php 
					_e('If you have installed any plugin that adds new roles or capabilities, these will be lost.', 'capsman-enhanced')?><br />
					<strong><?php if ( defined('WPLANG') && WPLANG && ( 'en_EN' != WPLANG ) ) _e('It is recommended to use this only as a last resource!'); else echo('It is recommended to use this only as a last resort!');?></strong></p>
				<p style="text-align:center;"><a class="ak-delete" title="<?php echo esc_attr(__('Reset Roles and Capabilities to WordPress defaults', 'capsman-enhanced')) ?>" href="<?php echo wp_nonce_url("admin.php?page={$this->ID}-tool&amp;action=reset-defaults", 'capsman-reset-defaults'); ?>" onclick="if ( confirm('<?php echo esc_js(__("You are about to reset Roles and Capabilities to WordPress defaults.\n 'Cancel' to stop, 'OK' to reset.", 'capsman-enhanced')); ?>') ) { return true;}return false;"><?php _e('Reset to WordPress defaults', 'capsman-enhanced')?></a>

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