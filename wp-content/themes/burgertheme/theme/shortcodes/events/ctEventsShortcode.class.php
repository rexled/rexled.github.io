<?php

/**
 * Draws events
 */
class ctEventsShortcode extends ctShortcodeQueryable
{

    /**
     * Returns name
     * @return string|void
     */
    public function getName()
    {
        return 'Events';
    }

    /**
     * Shortcode name
     * @return string
     */
    public function getShortcodeName()
    {
        return 'events';
    }


    /**
     * Handles shortcode
     * @param $atts
     * @param null $content
     * @return string
     */

    public function handle($atts, $content = null)
    {
        $attributes = shortcode_atts($this->extractShortcodeAttributes($atts), $atts);
        extract($attributes);


        if ($sort_by_event_date === 'yes') {
            $events = $this->getCollection($attributes, array('orderby' => 'meta_value', 'post_type' => 'event', 'meta_query' => array(array('key' => 'date', 'compare' => 'LIKE',))));
        } else {
            $events = $this->getCollection($attributes, array('post_type' => 'event'));
        }

        $eventBoxHtml = '';
        foreach ($events as $p) {
            $eventBoxHtml .= '[row]';
            $eventBoxHtml .= $this->embedShortcode('event_box', array_merge($attributes, array('slug' => $p->post_name, 'class' => $class)));
            $eventBoxHtml .= '[/row]';
        }
        return do_shortcode($eventBoxHtml);
    }


    /**
     * Shortcode type
     * @return string
     */
    public function getShortcodeType()
    {
        return self::TYPE_SHORTCODE_SELF_CLOSING;
    }

    /**
     * Returns config
     * @return null
     */
    public function getAttributes()
    {
        $atts = $this->getAttributesWithQuery(array(
            'cat_name' => array('query_map' => 'category_name', 'default' => '', 'type' => 'input', 'label' => __("Category name", 'ct_theme'), 'help' => __("Name of category to filter", 'ct_theme')),
            'tag' => array('default' => '', 'type' => 'input', 'label' => __("Tag name (slug)", 'ct_theme'), 'help' => __("Comma separated values: tag1,tag2 To exclude tags use '-' minus: -mytag will exclude tag 'mytag'", 'ct_theme')),
            'limit' => array('label' => __('limit', 'ct_theme'), 'default' => 20, 'type' => 'input', 'help' => __("Number of elements", 'ct_theme')),
            'sort_by_event_date' => array('label' => __('Sort by event date', 'ct_theme'), 'default' => 'no', 'type' => 'select', 'options' => array('yes' => 'yes', 'no' => 'no')),
            'class' => array('label' => __("Custom class for single box", 'ct_theme'), 'default' => '', 'type' => 'input', 'help' => __('Adding custom class allows you to set diverse styles in css to the element. Type in name of class, which you defined in css. You can add as much classes as you like.', 'ct_theme')),
        ));

        return $atts;
    }
}

new ctEventsShortcode();