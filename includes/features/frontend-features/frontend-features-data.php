<?php
namespace PublishPress\Capabilities;

class PP_Capabilities_Frontend_Features_Data
{
    /**
     * Get all admin features layout.
     *
     * @return array Elements layout.
     */
    public static function elementsLayout()
    {
        $elements = [];

        //Add frontend elements
        $elements[esc_attr('Frontend Elements')] = self::getFrontendElements();

        return apply_filters('pp_capabilities_frontend_features_elements', $elements);
    }

    /**
     * Get frontend elements
     *
     * @return array Elements layout item.
     */
    public static function getFrontendElements()
    {
        $elements_item = (array)get_option('capsman_frontend_features_elements', []);
        $elements_item = array_filter($elements_item);
        $elements_item = array_reverse($elements_item);

        if (empty($elements_item) && empty(get_option('capsman_frontend_features_demo_installed'))) {
            $elements_item = [];
            //add Site Dark Mode
            $element_id = uniqid(true) . 11;
            $elements_item[$element_id] = [
                'element_id'    => $element_id,
                'label'         => esc_html__('Site Dark Mode', 'capability-manager-enhanced'),
                'elements'      => [
                    'selector'  => '',
                    'styles'    => 'body.dark-mode {
  background-color: #000000;
  color: #ffffff;
}',
                    'bodyclass' => 'dark-mode',
                ],
                'pages'         => ['whole_site'],
                'post_types'    => []
            ];
            //add Site Light Mode
            $element_id = uniqid(true) . 22;
            $elements_item[$element_id] = [
                'element_id'    => $element_id,
                'label'         => esc_html__('Site Light Mode', 'capability-manager-enhanced'),
                'elements'      => [
                    'selector'  => '',
                    'styles'    => 'body.light-mode {
  background-color: #ffffff;
  color: #000000;
}',
                    'bodyclass' => 'light-mode',
                ],
                'pages'         => ['whole_site'],
                'post_types'    => []
            ];
            //add Hide Twenty Twenty-Three Credit Footer
            $element_id = uniqid(true) . 33;
            $elements_item[$element_id] = [
                'element_id'    => $element_id,
                'label'         => esc_html__('Hide Twenty Twenty-Three Credit Footer', 'capability-manager-enhanced'),
                'elements'      => [
                    'selector'  => '',
                    'styles'    => 'footer {
  display: none !important;
}',
                    'bodyclass' => '',
                ],
                'pages'         => ['whole_site'],
                'post_types'    => []
            ];
            $elements_item = array_reverse($elements_item);
            update_option('capsman_frontend_features_elements', $elements_item);
            update_option('capsman_frontend_features_demo_installed', 1);
            $elements_item = array_reverse($elements_item);
        }

        return $elements_item;
    }
}
