<?php

/**
 * Created by PhpStorm.
 * User: User
 * Date: 2016-07-13
 * Time: 13:18
 */
class ctGoogleMapsApiManager
{


    /**
     * @var string
     */
    static $key = '';

    /**
     * ctGoogleMapsApiManager constructor.
     */
    public function __construct()
    {
        add_action('ct_theme_loader_after_init', array($this, 'initHooks'));
    }


    public function modifyJsSrc($src, $handle)
    {
        //var_dump($this->getKey());die;
        if ('ct_google_maps_api' === $handle && '' !== $this->getKey()) {
            $src .= '&key=' . $this->getKey();
        }
        return $src;
    }


    /**
     * @return string
     */
    public function getKey()
    {
        if ('' === self::$key) {
            if ($this->hasCustomizer()) {
                self::$key = get_theme_mod('ct_option_general_gmap_api_key', '');
            } else {
                self::$key = ct_get_option('general_gmap_api_key', '');
            }
        }
        return self::$key;
    }


    /**
     *
     */
    public function initHooks()
    {
        /*
         * Filter js src
         */
        add_filter('script_loader_src', array($this, 'modifyJsSrc'), 10, 2);




        /*
         * Add options to customizer OR theme options
         */
        if ($this->hasCustomizer()) {
            //add map section to customizer
            add_action('ct_customizer_mapper_post', array($this, 'addCustomizerOptions'));
        } else {
            //old themes support
            add_filter('ct_theme_loader.options.load', array($this, 'addLegacyOptions'));
        }
    }


    /**
     * @return bool
     */
    public function hasCustomizer()
    {
        return class_exists('ctAdvancedCustomizer');
    }

    /**
     * @param $mapper
     * @return mixed
     */
    public function addCustomizerOptions($mapper)
    {
        $mapper->panel(__('Google Maps API', 'ct_theme'))
            ->section(__('Config', 'ct_thme'))
            ->option('general_gmap_api_key', __('Google maps API key', 'ct_theme'), 'text',
                array('default' => '',
                    'description' => ''));
        return $mapper;
    }

    /**
     * @param $sections
     * @return mixed
     */
    public function addLegacyOptions($sections)
    {
//var_dump($sections[0]);die;

        $sections[0]['fields'][] = array(
            'id' => 'general_gmap_api_key',
            'type' => 'text',
            'title' => __('Google maps API Key', 'ct_theme'),
            'desc' => '<a target="_blank" href="https://developers.google.com/maps/documentation/javascript/get-api-key">' . __('Click here for instructions how to create API key', 'ct_theme') . '</a>'
        );


        return $sections;
    }

}

new ctGoogleMapsApiManager();

