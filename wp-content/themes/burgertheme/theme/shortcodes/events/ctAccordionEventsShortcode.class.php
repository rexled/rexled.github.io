<?php

/**
 * Draws events
 */
class ctAccordionEventsShortcode extends ctShortcodeQueryable
{

    /**
     * Returns name
     * @return string|void
     */
    public function getName()
    {
        return 'Events Accourdion';
    }

    /**
     * Shortcode name
     * @return string
     */
    public function getShortcodeName()
    {
        return 'events_accordion';
    }


    /**
     * Handles shortcode
     * @param $atts
     * @param null $content
     * @return string
     */


    public function getEventHtml()
    {

    }


    public function handle($atts, $content = null)
    {
        $attributes = shortcode_atts($this->extractShortcodeAttributes($atts), $atts);
        extract($attributes);


        if ($sort_by_event_date === 'yes') {
            $events = $this->getCollection($attributes, array('orderby' => 'meta_value', 'post_type' => 'event', 'meta_query' => array(array('key' => 'date', 'compare' => 'LIKE',))));
        } else {
            $events = $this->getCollection($attributes,
                array(
                    'post_type' => 'event',
                    'meta_key' => 'date',
                    'order' => 'DESC',
                    'order_by' => 'meta_value_num',
                    'post_status' => 'publish'
                ));
        }


        $items = '';


        $panelRawHTML = '
            <div class="panel panel-default accordion">
<div class="panel-heading">
<h4 class="panel-title">
<a data-toggle="collapse" data-parent="#accordion" href="#collapse%i%" class="%COLLAPSED%">
%DAY_NAME% %DATE%
</a>


</h4>
</div>
<div class="clearfix"></div>
<div id="collapse%i%" class="panel-collapse collapse %IN%">
<div class="panel-body">
<ul class="eventSection">
%ITEMS%
</ul>
</div>
</div>
<div class="ct-titleLine"></div>
</div>';

        $eventItemRawHTML = '
        <li>
<div class="row">
<div class="col-md-8 col-sm-8 col-xs-12">
<div class="eventText">
<h6>
<p class="large">%POST_TITLE%</p>
<span class="place">%POST_SUBTITLE%</span>
<span class="content">%POST_CONTENT%</span>
</h6>
<a href="%MAP_IT_URL%"
data-rel="prettyPhoto" class="mapit"><i></i>%MAP_IT_LABEL%</a>
<a target="_blank"
href="http://www.facebook.com/sharer/sharer.php?u=%SHARE_IT_URL%"
class="shareit"><i></i>%SHARE_IT_LABEL%</a>
</div>
</div>
<div class="col-md-4 col-sm-4 col-xs-12">%GOOGLE_MAP%</div>
</div>
<hr>
</li>';

        $output = '';
        $itemsOutput = '';
        $itemsSorted = array();
        $mapUrl = '';
        $i = 1;

        //prepare data
        foreach ($events as $p) {
            $custom = get_post_custom($p->ID);
            $dateRaw = isset($custom["date"][0]) ? $custom["date"][0] : "";
            $subtitle = isset($custom["subtitle"][0]) ? $custom["subtitle"][0] : "";
            $amap = isset($custom["amap"][0]) ? $custom["amap"][0] : "";


            if ($amap) {
                /*is amap iframe?*/
                preg_match('/src="([^"]+)"/', $amap, $match);
                if ($match == null) {
                    //$mapUrl = $amap. '&output=embed?iframe=true&width=640&height=480';
                    $mapUrl = $amap . '&output=embed?iframe=true&width=640&height=480';
                    $amap = '';
                } else {
                    $mapUrl = $match[1] . '?iframe=true&width=640&height=480';
                }
            }

            $itemsSorted[$dateRaw][] = array(
                '%POST_TITLE%' => $p->post_title,
                '%POST_SUBTITLE%' => $subtitle,
                '%POST_CONTENT%' => $p->post_content,
                '%MAP_IT_URL%' => htmlentities($mapUrl),
                '%MAP_IT_LABEL%' => $map_btn_label,
                '%SHARE_IT_LABEL%' => $share_btn_label,
                '%SHARE_IT_URL%' => get_permalink($p->ID),
                '%GOOGLE_MAP%' => $amap
            );

        }


        //prepare HTML
        foreach ($itemsSorted as $k => $v) {
            foreach ($v as $k2 => $v2) {
                /*add new event item*/
                $itemsOutput .= str_replace(array(
                    '%POST_TITLE%',
                    '%POST_SUBTITLE%',
                    '%POST_CONTENT%',
                    '%MAP_IT_URL%',
                    '%MAP_IT_LABEL%',
                    '%SHARE_IT_LABEL%',
                    '%SHARE_IT_URL%',
                    '%GOOGLE_MAP%',
                ),
                    array(
                        $v2['%POST_TITLE%'],
                        $v2['%POST_SUBTITLE%'],
                        $v2['%POST_CONTENT%'],
                        $v2['%MAP_IT_URL%'],
                        $v2['%MAP_IT_LABEL%'],
                        $v2['%SHARE_IT_LABEL%'],
                        $v2['%SHARE_IT_URL%'],
                        $v2['%GOOGLE_MAP%'],
                    ),
                    $eventItemRawHTML);
            }


            $date = $k ? strtotime($k) : '';
            if ($date != '') {
                //$day = date('l', $date);
                $day = date_i18n('l', $date);
            } else {
                $day = '';
            }

            /*new date: close panel, open new panel*/
            //var_dump($i);

            $output .= str_replace(
                array(
                    '%i%',
                    '%COLLAPSED%',
                    '%DAY_NAME%',
                    '%DATE%',
                    '%IN%',
                    '%ITEMS%',
                ),
                array(
                    $i,
                    $i == 1 ? '' : 'collapsed',//forst panel default opened
                    $day,
                    $k,
                    $i == 1 ? 'in' : '',//forst panel default opened
                    $itemsOutput
                ), $panelRawHTML);
            $itemsOutput = '';
            $i++;
        }


        $mainContainerAtts = array(
            'class' => array(
                'eventBox',
                'accordionEvent',
                $class
            ),
        );


        $return = '<div ' . $this->buildContainerAttributes($mainContainerAtts, $atts) . '><div class="panel-group" id="accordion">' . $output . '</div></div>';


        return do_shortcode($return);
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
            'map_btn_label' => array('label' => __('Map it label', 'ct_theme'), 'default' => 'map it', 'type' => 'input'),
            'share_btn_label' => array('label' => __('Share it label', 'ct_theme'), 'default' => 'share it', 'type' => 'input'),
            'cat_name' => array('query_map' => 'category_name', 'default' => '', 'type' => 'input', 'label' => __("Category name", 'ct_theme'), 'help' => __("Name of category to filter", 'ct_theme')),
            'tag' => array('default' => '', 'type' => 'input', 'label' => __("Tag name (slug)", 'ct_theme'), 'help' => __("Comma separated values: tag1,tag2 To exclude tags use '-' minus: -mytag will exclude tag 'mytag'", 'ct_theme')),
            'limit' => array('label' => __('limit', 'ct_theme'), 'default' => 50, 'type' => 'input', 'help' => __("Number of elements", 'ct_theme')),
            'sort_by_event_date' => array('label' => __('Sort by event date', 'ct_theme'), 'default' => 'no', 'type' => 'select', 'options' => array('yes' => 'yes', 'no' => 'no')),
            'class' => array('label' => __("Custom class for single box", 'ct_theme'), 'default' => '', 'type' => 'input', 'help' => __('Adding custom class allows you to set diverse styles in css to the element. Type in name of class, which you defined in css. You can add as much classes as you like.', 'ct_theme')),
        ));

        return $atts;
    }
}

new ctAccordionEventsShortcode();