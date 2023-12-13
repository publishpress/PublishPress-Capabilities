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
        $elements[esc_html__('Frontend Elements', 'capability-manager-enhanced')] = self::getFrontendElements();

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

        return $elements_item;
    }
}
