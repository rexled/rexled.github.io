<?php

/**
 * Draws works
 */
class ctTimelineShortcode extends ctShortcodeQueryable
{

    /**
     * Returns name
     * @return string|void
     */
    public function getName()
    {
        return 'Timeline';
    }

    /**
     * Shortcode name
     * @return string
     */
    public function getShortcodeName()
    {
        return 'timeline';
    }

    public function enqueueScripts()

    {
        wp_register_script('ct-flex-story', CT_THEME_ASSETS . '/js/timeline/js/storyjs-embed.js', array('jquery'), false, true);
        wp_enqueue_script('ct-flex-story');
    }



    public function getTaxName($atts, $content = null)
    {

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

        $timelineCollection = $this->getCollection($attributes, array('post_type' => 'timeline'));

        $timelineItems = array();
        $counter = 0;
        $firstItemKey = 0;
        $oldDate = null;

        if (empty($timelineCollection)){
            return'';
        }

        /*build timeline items array*/
        foreach ($timelineCollection as $p) {
            $custom = get_post_custom($p->ID);

            $timelineItems[$counter]['id'] = $p->ID;
            $timelineItems[$counter]['thumbnail'] = (ct_timeline_featured_image2_src($p->ID, 'featured-image-timeline-2', 'timeline_thumbnail')) ? ct_timeline_featured_image2_src($p->ID, 'featured-image-timeline-2', 'timeline_thumbnail') : null;
            $timelineItems[$counter]['title'] = ($p->post_title);
            $timelineItems[$counter]['content'] = ($p->post_content);
            $timelineItems[$counter]['start_date'] = $custom['start_date'][0];
            $timelineItems[$counter]['end_date'] = $custom['end_date'][0];
            $timelineItems[$counter]['media_caption'] = $custom['media_caption'][0];
            $timelineItems[$counter]['media_credit'] = $custom['media_credit'][0];

            /*get attached media url*/
            if ($custom['wikipedia_url'][0]) {
                $timelineItems[$counter]['media_url'] = $custom['wikipedia_url'][0];
            } else if ($custom['map_url'][0]) {
                $timelineItems[$counter]['media_url'] = $custom['map_url'][0];
            } else if ($custom['video_url'][0]) {
                $timelineItems[$counter]['media_url'] = $custom['video_url'][0];
            } else if (ct_get_feature_image_src($p->ID, 'timeline_featured_image')) {
                $timelineItems[$counter]['media_url'] = ct_get_feature_image_src($p->ID, 'timeline_featured_image');
            } else {
                $timelineItems[$counter]['media_url'] = '';
            }

            /*get the earliest date index*/
            if ($counter > 0 && new DateTime($timelineItems[$counter]['start_date']) > new DateTime($oldDate)) {
                $firstItemKey = $counter - 1;
            } else {
                $firstItemKey = $counter;
            }
            $oldDate = $timelineItems[$counter]['start_date'];
            $counter++;
        }


        /*Generating json*/
        $json = array();

        /*build json first item*/
        $json['timeline']['startDate'] = str_replace('-', ',', $timelineItems[$firstItemKey]['start_date']);
        $json['timeline']['endDate'] = $firstItemKey['end_date'] ? str_replace('-', ',', $timelineItems[$firstItemKey]['end_date']) : '';
        $json['timeline']['headline'] = $timelineItems[$firstItemKey]['title'];
        $json['timeline']['type'] = 'default';
        $json['timeline']['text'] = $timelineItems[$firstItemKey]['content'];
        $json['timeline']['asset'] = array(
            'media' => $timelineItems[$firstItemKey]['media_url'],
            'thumbnail' => $timelineItems[$firstItemKey]['thumbnail'],
            'credit' => $timelineItems[$firstItemKey]['media_credit'],
            'caption' => $timelineItems[$firstItemKey]['media_caption'],
        );

        /*remove first item from items array*/
        array_splice($timelineItems, $firstItemKey, 1);

        /*build json items*/
        foreach ($timelineItems as $key => $value) {

            $json['timeline']['date'][] = array(
                'startDate' => str_replace('-', ',', $value['start_date']),
                'endDate' => ($value['end_date']) ? str_replace('-', ',', $value['end_date']) : '',
                'headline' => $value['title'],
                'text' => $value['content'],
                'asset' => array(
                    'media' => $value['media_url'],
                    'thumbnail' => $value['thumbnail'],
                    'credit' => $value['media_credit'],
                    'caption' => $value['media_caption'],
                ),
            );
        }
        $json = json_encode($json);


        $this->addInlineJS($this->getInlineJS($attributes, $id, $json));
        return '<div id="timeline-embed"></div>';

    }


    /**
     * returns inline js
     * @param $attributes
     * @return string
     */
    protected function getInlineJS($attributes, $id, $json)
    {
        extract($attributes);


        return '


	if (jQuery("#timeline-embed").length > 0) {

		createStoryJS({
			width: "' . $width . '",
			height: "' . $height . '",
			source: ' . $json . ',
			embed_id: "timeline-embed",               //OPTIONAL USE A DIFFERENT DIV ID FOR EMBED
			start_at_end: ' . $start_at_end . ',                          //OPTIONAL START AT LATEST DATE
			start_at_slide: "' . $start_at_slide . '",                            //OPTIONAL START AT SPECIFIC SLIDE
			start_zoom_adjust: "' . $start_zoom_adjust . '",                            //OPTIONAL TWEAK THE DEFAULT ZOOM LEVEL
			hash_bookmark: false,                           //OPTIONAL LOCATION BAR HASHES
			debug: false,                           //OPTIONAL DEBUG TO CONSOLE
			lang: "en",                           //OPTIONAL LANGUAGE
			maptype: "' . $map_type . '",                   //OPTIONAL MAP STYLE
			css: "' . $css . '",     //OPTIONAL PATH TO CSS
			js: "' . CT_THEME_ASSETS . '/js/timeline/js/timeline-min.js"    //OPTIONAL PATH TO JS
		});
	}
        ';
    }


    /**
     * creates class name for the category
     * @param $cat
     * @return string
     */
    protected function getCatFilterClass($cat)
    {
        return strtolower(str_replace(' ', '-', $cat->slug));
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
            'limit' => array('label' => __('limit', 'ct_theme'), 'default' => 50, 'type' => 'input', 'help' => __("Number of portfolio elements", 'ct_theme')),
            'width' => array('label' => __('Width', 'ct_theme'), 'default' => '100%', 'type' => 'input',),
            'height' => array('label' => __('Height', 'ct_theme'), 'default' => 600, 'type' => 'input',),
            'start_at_end' => array('label' => __('Start at end', 'ct_theme'), 'default' => 'false', 'type' => 'select', 'choices' => array('true' => 'true', 'false' => 'false'), 'help' => __("Start at latest date", 'ct_theme')),
            'start_at_slide' => array('label' => __('Start at slide', 'ct_theme'), 'default' => 0, 'type' => 'input', 'help' => __("Start at specific slide", 'ct_theme')),
            'start_zoom_adjust' => array('label' => __('Start zoom adjust', 'ct_theme'), 'default' => 2, 'type' => 'input', 'help' => __("tweak the default zoom level", 'ct_theme')),
            'map_type' => array('label' => __('Select map style', 'ct_theme'), 'default' => 'HYBRID', 'type' => 'select', 'options' => array(
                'ROADMAP' => 'Roadmap',
                'SATELLITE' => 'Satellite',
                'HYBRID' => 'Hybrid',
                'TERRAIN' => 'Terrain',
            )),
            'css' => array('label' => __('css path', 'ct_theme'), 'default' => CT_THEME_ASSETS . '/js/timeline/css/timeline.css', 'type' => 'input', 'help' => __("optional path to css", 'ct_theme')),
        ));

        if (isset($atts['cat'])) {
            unset($atts['cat']);
        }
        return $atts;
    }
}

new ctTimelineShortcode();