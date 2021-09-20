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
        $elements[__('Dashboard widgets', 'capsman-enhanced')] = [
             'item' => ['label' => __('Item', 'capsman-enhanced'), 'action' => 'ppc_id'],
            'item2' => ['label' => __('Item 2', 'capsman-enhanced'), 'action' => 'ppc_class'],
        ];

        //Add other element
        $elements[__('Others', 'capsman-enhanced')] = [
            'item' => ['label' => __('Item', 'capsman-enhanced'), 'action' => 'call_back_function'],
        ];

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

}
