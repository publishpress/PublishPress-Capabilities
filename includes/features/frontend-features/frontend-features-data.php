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

        //Add custom styles
        $elements[esc_html__('Custom Styles', 'capsman-enhanced')] = self::getCustomStyles();

        //Add frontend elements
        $elements[esc_html__('Frontend Elements', 'capsman-enhanced')] = self::getFrontendElements();

        //Add body class
        $elements[esc_html__('Body Class', 'capsman-enhanced')] = self::getBodyClass();

        return apply_filters('pp_capabilities_frontend_features_elements', $elements);
    }

    /**
     * Retrieve all items icons.
     *
     * @return array Items icons.
     */
    public static function elementLayoutItemIcons()
    {
        $icons = [];

        $icons['frontendelements']  = 'admin-home';
        $icons['bodyclass']         = 'html';
        $icons['customstyles']      = 'dashboard';

        return apply_filters('pp_capabilities_frontend_features_icons', $icons);
    }

    /**
     * Get frontend elements
     *
     * @return array Elements layout item.
     */
    public static function getFrontendElements()
    {
        $elements_item = (array)get_option('capsman_frontend_features_hide_elements', []);
        $elements_item = array_filter($elements_item);

        return $elements_item;
    }

    /**
     * Get body class.
     *
     * @return array Elements layout item.
     */
    public static function getBodyClass()
    {
        $elements_item = (array)get_option('capsman_frontend_features_body_class', []);
        $elements_item = array_filter($elements_item);

        return $elements_item;
    }

    /**
     * Get custom styles.
     *
     * @return array Elements layout item.
     */
    public static function getCustomStyles()
    {
        $elements_item = (array)get_option('capsman_frontend_features_custom_styles', []);
        $elements_item = array_filter($elements_item);

        return $elements_item;
    }

    /**
     * Get array elements that starts with a specific word
     *
     * @param array $restricted_features All restricted elements to check against.
     * @param string $start_with The word to look for in array.
     *
     * @return array Filtered array.
     */
    public static function getRestrictedElements($restricted_elements, $start_with = 'frontendelements')
    {
        //get all items of the array starting with the specified string.
        $new_elements = array_filter(
            $restricted_elements,
            function ($value, $key) use ($start_with) {
                return strpos($value, $start_with) === 0;
            },
            ARRAY_FILTER_USE_BOTH
        );

        return $new_elements;
    }
}
