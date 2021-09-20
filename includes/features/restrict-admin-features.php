<?php

class PP_Capabilities_Admin_Features {

    /**
	 * Get all admin features layout.
	 *
	 * @return array Elements layout.
	 */
    public static function elementsLayout()
    {
        $elements = [];

        //Add toolbar
        $elements[__('Toolbar', 'capsman-enhanced')] = [
            'item' => ['label' => __('Item', 'capsman-enhanced'), 'action' => 'ppc_id'],
            'item2' => ['label' => __('Item 2', 'capsman-enhanced'), 'action' => 'ppc_class'],
        ];

        //Add dashboard widget
        $elements[__('Dashboard widgets', 'capsman-enhanced')] = self::formatDashboardWidgets();

        //Add other element
        $elements[__('Others', 'capsman-enhanced')] = [
            'admin-notices' => ['label' => __('Admin Notices', 'capsman-enhanced'), 'action' => 'ppc_admin_notices'],
        ];

        self::formatDashboardWidgets();

        return apply_filters('pp_capabilities_admin_features_elements', $elements);
    }
    
    /**
	 * Retrieve all items icons.
	 *
	 * @return array Items icons.
	 */
    public static function elementLayoutItemIcons(){
        $icons = [];

        $icons['toolbar']           = 'open-folder';
        $icons['dashboardwidgets']  = 'dashboard';
        $icons['others']            = 'admin-tools';

        return apply_filters('pp_capabilities_admin_features_icons', $icons); 
    }

	/**
	 * Get the list of dashboard widgets.
	 *
	 * @return array dashboard widgets.
	 */
	public static function dashboardWidgets() {
		global $wp_meta_boxes;

		$screen = is_network_admin() ? 'dashboard-network' : 'dashboard';

		$current_screen = get_current_screen();

		if ( ! isset( $wp_meta_boxes[ $screen ] ) || ! is_array( $wp_meta_boxes[ $screen ] ) ) {
			require_once ABSPATH . '/wp-admin/includes/dashboard.php';
			set_current_screen( $screen );
			wp_dashboard_setup();
		}

		$widgets = [];

		if ( isset( $wp_meta_boxes[ $screen ] ) ) {
			$widgets = $wp_meta_boxes[ $screen ];
		}

		set_current_screen( $current_screen );

		return $widgets;
	}

	/**
	 * Format dashboard widgets.
	 *
	 * @return array Elements layout item.
	 */
	public static function formatDashboardWidgets() {
		$widgets = self::dashboardWidgets();

		$elements_widget = [];

        //add widget that may not be part of wp_meta_boxes
        $elements_widget['dashboard_welcome_panel'] = ['label' => __('Welcome panel', 'capsman-enhanced'), 'action' => 'ppc_dashboard_widget'];
        //loop other widgets
		foreach ( $widgets as $context => $priority ) {
			foreach ( $priority as $data ) {
				foreach ( $data as $id => $widget ) {
					if ($widget ) {
                        $widget_title = isset( $widget['title'] ) ? wp_strip_all_tags($widget['title']) : '';
                        $elements_widget[$id] = ['label' => $widget_title, 'action' => 'ppc_dashboard_widget'];
                    }
				}
			}
		}

        return $elements_widget;

    }

}
