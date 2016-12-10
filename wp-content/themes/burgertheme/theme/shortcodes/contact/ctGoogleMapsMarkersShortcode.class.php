<?php

/**
 * Google maps shortcode
 */
class ctGoogleMapsMarkersShortcode extends ctShortcode
{

    /**
     * Returns name
     * @return string|void
     */
    public function getName()
    {
        return 'Google maps markers';
    }

    /**
     * Shortcode name
     * @return string
     */
    public function getShortcodeName()
    {
        return 'google_maps_markers';
    }

    /**
     * Enqueue scripts
     */

    public function enqueueScripts()
    {
        wp_register_script('ct-gmap', CT_THEME_ASSETS . '/js/gmap3.min.js', array('jquery'), false, true);
        wp_enqueue_script('ct-gmap');


        wp_register_script('ct_google_maps_api', 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false', array('jquery'), false, true);
        wp_enqueue_script('ct_google_maps_api');
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
        $id = ($id == '') ? 'gmap' . rand(100, 1000) : $id;

        //multi locator support
        if ($foodtruck_locator == 'yes'){
            $trucksQty = count(get_option('general_locations_multi'));//get active trucks number
            $points = apply_filters('ct_multi_locator_points', array());//get positions

            $temp = array();
            //show only active trucks
                for ($i=0;$i<$trucksQty;$i++){
                    $temp[] = $points[$i];
                }
                $points = $temp;//now we have only active trucks positions
        }else{
            //parse shortcode before filters
            do_shortcode($content);

            $points = $this->callPreFilter(''); //reference
            $this->cleanData('google_map_marker');
            ctGoogleMapMarkerShortcode::$counter = 0;
        }



        /*if (ct_is_browser_type('mobile') == true){
            $attributes['disable_auto_pan'] = 'true';
            $attributes['map_draggable'] = 'false';
        }*/


        $this->addInlineJS($this->getInlineJS($attributes, $id, $points));

        if (!is_numeric($height)) {
            $height = '286';
        }

        $mainContainerAtts = array(
            'class' => array(
                'markersGoogleMap',
                'googleMap',
                $class
            ),
            'data-height' => $height,
            'id' => $id,
        );

        $titleShortcode = $title ? '[header class="special" level="3" style="' . $title_style . '"]' . $title . '[/header]' : '';

        if ($curved_subtitle == 'yes' || $curved_subtitle == 'true') {
            $curved_subtitle = 'curved="yes" radius="' . $radius . '" direction="' . $direction . '"';
        } else {
            $curved_subtitle = '';
        }

        if ($subtitle) {
            $overlayHtml = array(
                'start' => '<div class="mapWithOverlay">[header level="4" style="' . $subtitle_style . '" ' . $curved_subtitle . ']' . $subtitle . '[/header]',
                'end' => '</div>');
        } else {
            $overlayHtml = array(
                'start' => '',
                'end' => '');
        }

        $html = do_shortcode($overlayHtml['start'] . $titleShortcode . '<div ' . $this->buildContainerAttributes($mainContainerAtts, $atts) . '></div>' . $overlayHtml['end']);

        return $html;


    }


    /**
     * returns inline js
     * @param $attributes
     * @param $id
     * @return string
     */
    protected function getInlineJS($attributes, $id, $points)
    {


        if ($attributes['street_view'] == 'yes'){
            $attributes['street_view'] = 'true';
        }else if ($attributes['street_view'] == 'no'){
            $attributes['street_view'] = 'false';
        }

        if ($attributes['map_draggable'] == 'yes'){
            $attributes['map_draggable'] = 'true';
        }else if ($attributes['map_draggable'] == 'no'){
            $attributes['map_draggable'] = 'false';
        }

      /*  if ($attributes['disable_auto_pan'] == 'yes'){
            $attributes['disable_auto_pan'] = 'true';
        }else if ($attributes['disable_auto_pan'] == 'no'){
            $attributes['disable_auto_pan'] = 'false';
        }*/


        $markers = $attributes['markers'];
        $markerList = explode(',',$markers);
        if(sizeof($markerList)>1) {
            return '
        // init gmap - Asynchronous Loading

				jQuery("#' . $id . '").each(function(){
					var $this = jQuery(this);
					var icon = $this.attr("data-icon");
					var text = $this.data("text");
					$this.css("min-height", $this.data("height") + "px");

					$this.gmap3({
						map:{
						  options:{

						  draggable: ' . $attributes['map_draggable'] . ',
						  streetViewControl: ' . $attributes['street_view'] . ',
						    maxZoom: 20,
					     scrollwheel: false,
						    mapTypeId: google.maps.MapTypeId.' . $attributes['map_type'] . '
						  }
						},
						marker:{
						  values:' . json_encode($points) . ',
						  options:{
						    draggable: false,
						  }
						}
					},"autofit");
            });

        ';
        }else{
            return '
        // init gmap - Asynchronous Loading

				jQuery("#' . $id . '").each(function(){
					var $this = jQuery(this);
					var icon = $this.attr("data-icon");
					var text = $this.data("text");
					$this.css("min-height", $this.data("height") + "px");

					$this.gmap3({
						map:{
						  options:{

						  draggable: ' . $attributes['map_draggable'] . ',
						  streetViewControl: ' . $attributes['street_view'] . ',
						    maxZoom: 18,
					     scrollwheel: false,
						    mapTypeId: google.maps.MapTypeId.' . $attributes['map_type'] . '
						  }
						},
						marker:{
						  values:' . json_encode($points) . ',
						  options:{
						    draggable: false,
						  }
						}
					},"autofit");
            });

        ';
        }
    }



    /**
     * Child shortcode info
     * @return array
     */

    public function getChildShortcodeInfo() {
        return array('name' => 'google_map_marker', 'min' => 1, 'max' => 30, 'default_qty' => 1);
    }


    /**
     * Returns config
     * @return null
     */
    public function getAttributes()
    {
        return array(
            'widgetmode' => array('default' => 'false', 'type' => false),
            "foodtruck_locator" => array('label' => __('Use FoodTruck Locator?', 'ct_theme'), 'default' => 'no', 'type' => 'select', 'choices' => array('yes' => __('yes', 'ct_theme'), 'no' => __('no', 'ct_theme')), 'help' => __("Please go to Appearance - Theme Options - Multi Locator for more info", 'ct_theme')),
            'markers'=>array('label' => __('Markers', 'ct_theme'), 'default' => '', 'type' => 'input', 'help' => __("Write down marker numbers(0 is your current location) and separate them with commas", 'ct_theme')),
            'id' => array('label' => __('ID', 'ct_theme'), 'default' => '', 'type' => 'input'),
            'height' => array('label' => __('height', 'ct_theme'), 'default' => '286', 'type' => 'input'),
            'title' => array('label' => __('Title', 'ct_theme'), 'default' => '', 'type' => 'input', 'help' => __("Title", 'ct_theme')),
            'subtitle' => array('label' => __('Subtitle', 'ct_theme'), 'default' => '', 'type' => 'input', 'help' => __("Subtitle", 'ct_theme')),
            'title_style' => array('label' => __('Select title style', 'ct_theme'), 'default' => '', 'type' => 'select', 'options' => array(
                '1' => '1',
                '2' => '2',
                '3' => '3',
                '4' => '4',
                '5' => '5',
                '6' => '6',
                '7' => '7',
                '8' => '8',
                '9' => '9',
                '' => '')),
            'subtitle_style' => array('label' => __('Select subtitle style', 'ct_theme'), 'default' => '', 'type' => 'select', 'options' => array(
                '1' => '1',
                '2' => '2',
                '3' => '3',
                '4' => '4',
                '5' => '5',
                '6' => '6',
                '7' => '7',
                '8' => '8',
                '9' => '9',
                '' => '')),
            'curved_subtitle' => array('label' => __('Curved subtitle', 'ct_theme'), 'default' => 'no', 'type' => 'select', 'options' => array('yes' => 'yes', 'no' => 'no')),
            'radius' => array('label' => __('Curvature radius subtitle', 'ct_theme'), 'default' => '800', 'type' => "input"),
            'direction' => array('label' => __('Curvature direction subtitle', 'ct_theme'), 'default' => '1', 'type' => "input"),
            'map_type' => array('label' => __('Select map type', 'ct_theme'), 'default' => 'HYBRID', 'type' => 'select', 'options' => array(
                'ROADMAP' => 'Roadmap',
                'SATELLITE' => 'Satellite',
                'HYBRID' => 'Hybrid',
                'TERRAIN' => 'Terrain',
            )),
            'street_view' => array('label' => __('Street View control ', 'ct_theme'), 'default' => 'true', 'type' => 'select', 'options' => array('true' => 'true', 'false' => 'false')),
            'map_draggable' => array('label' => __('Draggable', 'ct_theme'), 'default' => 'true', 'type' => 'select', 'options' => array('true' => 'true', 'false' => 'false'), 'help' => __("locked automatically on mobile devices", 'ct_theme')),
            'class' => array('label' => __("Custom class", 'ct_theme'), 'default' => '', 'type' => 'input', 'help' => __('Adding custom class allows you to set diverse styles in css to the element. Type in name of class, which you defined in css. You can add as much classes as you like.', 'ct_theme')),
        );

    }
}

new ctGoogleMapsMarkersShortcode();