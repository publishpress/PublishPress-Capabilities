<?php
/*
 * PublishPress Capabilities [Free]
 *
 * HTML output for sidebar banner inviting to install Permissions
 *
 */

if( !class_exists('Capsman_PP_Sidebar_Banner') ){

	class Capsman_PP_Sidebar_Banner {

		function install_permissions_banner() {
			?>
			<p class="nav-tab-wrapper pp-recommendations-heading">
				<?php _e( 'Recommendations for you', 'capsman-enhanced' ) ?>
			</p>
			<div class="pp-sidebar-box">
				<h3>
					<?php _e( 'Control permissions for individual posts and pages', 'capsman-enhanced' ) ?>
				</h3>
				<ul>
					<li>
						<?php _e( 'Choose who can read and edit each post.', 'capsman-enhanced' ) ?>
					</li>
					<li>
						<?php _e( 'Allow specific user roles or users to manage each post.', 'capsman-enhanced' ) ?>
					</li>
					<li>
						<?php _e( 'PublishPress Permissions is 100% free to install.', 'capsman-enhanced' ) ?>
					</li>
				</ul>
				<p>
					<a class="button button-primary"
					   href="<?php echo admin_url('plugin-install.php?s=publishpress-ppcore-install&tab=search&type=term') ?>"
					>
						<?php _e( 'Click here to install PublishPress Permissions for free', 'capsman-enhanced' ); ?>
					</a>
				</p>
				<div class="pp-box-banner-image">
					<a href="<?php echo admin_url('plugin-install.php?s=publishpress-ppcore-install&tab=search&type=term') ?>">
						<img src="<?php echo plugin_dir_url(CME_FILE) . 'includes-core/pp-permissions-install.jpg';?>"
						title="<?php _e( 'Click here to install PublishPress Permissions for free', 'capsman-enhanced' ); ?>" />
					</a>
				</div>
			</div>
			<?php
		}
	}
}
