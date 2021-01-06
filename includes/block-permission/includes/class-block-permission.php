<?php
/**
 * Setup Block Permission
 *
 * @package block-permission
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Main Block Permission Class.
 *
 * @since 1.0.0
 */
final class PPC_Block_Permission {

	/**
	 * Block Permission version.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $version = '1.0.0';

	/**
	 * Return singleton instance of the Block Permission plugin.
	 *
	 * @since 1.0.0
	 * @return PPC_Block_Permission
	 */
	public static function instance() {
		static $instance = false;

		if ( ! $instance ) {
			$instance = new self();
		}
		return $instance;
	}

	/**
	 * Cloning instances of the class is forbidden.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __clone() {
		_doing_it_wrong(
			__FUNCTION__,
			esc_html__( 'Cloning instances of the class is forbidden.', 'capsman-enhanced' ),
			'1.0'
		);
	}

	/**
	 * Unserializing instances of the class is forbidden.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __wakeup() {
		_doing_it_wrong(
			__FUNCTION__,
			esc_html__( 'Unserializing instances of the class is forbidden.', 'capsman-enhanced' ),
			'1.0'
		);
	}

	/**
	 * Initialise the plugin.
	 */
	private function __construct() {
		$this->define_constants();
		$this->includes();
		$this->actions();
	}

	/**
	 * Load required actions.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function actions() {
		add_action( 'wp_loaded', array( $this, 'add_attributes_to_registered_blocks' ), 100 );
		add_action( 'enqueue_block_editor_assets', array( $this, 'block_localization' ) );
	}

	/**
	 * Include required files.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function includes() {

		// Needs to be included at all times due to show_in_rest.
		include_once BLOCK_PERMISSION_ABSPATH . 'includes/register-settings.php';
		include_once BLOCK_PERMISSION_ABSPATH . 'includes/rest-api/register-routes.php';

		// Utility functions that are also used by register-routes.php so
		// needs to be included at all times.
		include_once BLOCK_PERMISSION_ABSPATH . 'includes/utils/user-functions.php';

		// Only include in the admin.
		if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
			include_once BLOCK_PERMISSION_ABSPATH . 'includes/admin/editor.php';

			// Utility functions.
			include_once BLOCK_PERMISSION_ABSPATH . 'includes/utils/get-asset-file.php';
		}

		// Only include on the frontend.
		if ( ! is_admin() ) {
			include_once BLOCK_PERMISSION_ABSPATH . 'includes/frontend/render-block.php';
		}
	}

	/**
	 * Define the contants for the Block Permission base (BVB) plugin.
	 *
	 * @since 1.0.0
	 */
	private function define_constants() {
		$this->define( 'BLOCK_PERMISSION_ABSPATH', dirname( BLOCK_PERMISSION_MODULE_FILE ) . '/' );
		$this->define( 'BLOCK_PERMISSION_PPC_ABSPATH', dirname( CME_FILE ) . '/' );
		$this->define( 'BLOCK_PERMISSION_VERSION', $this->version );
		$this->define( 'BLOCK_PERMISSION_PLUGIN_MODULE_URL', plugin_dir_url( BLOCK_PERMISSION_MODULE_FILE ) );
		$this->define( 'BLOCK_PERMISSION_PLUGIN_MODULE_BASENAME', plugin_basename( BLOCK_PERMISSION_MODULE_FILE ) );
		$this->define( 'BLOCK_PERMISSION_SUPPORT_URL', 'https://wordpress.org/plugins/capability-manager-enhanced/' );
		$this->define( 'BLOCK_PERMISSION_SETTINGS_URL', admin_url( 'admin.php?page=capsman' ) );
	}

	/**
	 * Define constant if not already set.
	 *
	 * @since 1.0.0
	 * @param string      $name  Constant name.
	 * @param string|bool $value Constant value.
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			// phpcs:ignore
			define( $name, $value );
		}
	}

	/**
	 * This is needed to resolve an issue with blocks that use the
	 * ServerSideRender component. Regustering the attributes only in js
	 * can cause an error message to appear. Registering the attributes in
	 * PHP as well, seems to resolve the issue. Ideally, this bug will be
	 * fixed in the future.
	 *
	 * Reference: https://github.com/WordPress/gutenberg/issues/16850
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function add_attributes_to_registered_blocks() {

		$registered_blocks = WP_Block_Type_Registry::get_instance()->get_all_registered();

		foreach ( $registered_blocks as $name => $block ) {
			$block->attributes['BlockPermission'] = array(
				'type'       => 'object',
				'properties' => array(
					'hideBlock'             => array(
						'type' => 'boolean',
					),
					'visibilityByRole'      => array(
						'type' => 'string',
					),
					'hideOnRestrictedRoles' => array(
						'type' => 'boolean',
					),
					'restrictedRoles'       => array(
						'type'  => 'array',
						'items' => array(
							'type' => 'string',
						),
					),
				),
				'default'    => array(
					'hideBlock'        => false,
					'visibilityByRole' => 'all',
					'restrictedRoles'  => array(),
				),
			);
		}
	}

	/**
	 * Enqueue localization data for our blocks.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function block_localization() {
		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations(
				'block-permission-editor-scripts',
				'capsman-enhanced',
				BLOCK_PERMISSION_PPC_ABSPATH . '/lang'
			);

			wp_set_script_translations(
				'block-permission-setting-scripts',
				'capsman-enhanced',
				BLOCK_PERMISSION_PPC_ABSPATH . '/lang'
			);
		}
	}
}
