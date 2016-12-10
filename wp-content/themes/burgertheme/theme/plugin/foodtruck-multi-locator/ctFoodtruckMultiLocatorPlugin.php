<?php

/**
 * multi locator plugin
 * @author CreateIT
 */
class ctMultiLocatorPlugin
{

    /**
     * Is someone is using us on this page?
     * @var bool
     */

    protected $active = true;
    /**
     * @var bool
     */
    static public $foodtrucks;

    /**
     * @param $sections
     * @return array
     */
    public function addOptions($sections)
    {
        $sections[] = array(
            'icon' => NHP_OPTIONS_URL . 'img/glyphicons/glyphicons_280_settings.png',
            'group' => __("Multi Locator", 'ct_theme'),
            'title' => __('Setup', 'ct_theme'),
            'desc' => '<span style="color:red">' . __(' In this section you can setup multiple locations for FoodTrucks. To display locations on the map use Google Map Markers shortcode with foodtruck_locator="yes" parameter:<br> [google_maps_markers foodtruck_locator="yes"][/google_maps_markers]<br>
Below define number of trucks locations, which you want to display.<br>In each "truck settings" tab you can setup different location.</span>', 'ct_theme'),
            'fields' => array(
                array(
                    'id' => 'general_multi_locator_trucks',
                    'type' => 'text',
                    'title' => __('Enter trucks to support', 'ct_theme'),
                    'desc' => __('', 'ct_theme'),
                    'std' => 3,
                ),
            )
        );

        $foodtrucks = self::$foodtrucks;
        if ((int)self::$foodtrucks == 0) return $sections;
        //var_dump(self::$foodtrucks);exit();

        for ($i = 1; $i <= (int)$foodtrucks; $i++) {
            $savedToken = $this->getSavedToken($i);

            $sections[] = array(
                'icon' => NHP_OPTIONS_URL . 'img/glyphicons/glyphicons_242_google_maps.png',
                'group' => 'Multi Locator',
                'title' => str_replace(array('%truck_number%'), array($i), __("Truck %truck_number% settings", 'ct_theme')),
                'fields' => array(
                    array(
                        'id' => 'general_multi_location_name_' . $i,
                        'type' => 'text',
                        'title' => __('Name', 'ct_theme'),
                        'std' => '',
                        'desc' => __('Define a truck name - please notice - this name will not be used for marker label on the map.', 'ct_theme')
                    ),
                    array(
                        'id' => 'general_multi_location_secret_' . $i,
                        'type' => 'text',
                        'title' => __('Secret token used to secure FoodTruck Locator link (Truck ' . $i . ')', 'ct_theme'),
                        'desc' => __('This token is required to secure your request. Just make sure it is not empty ...', 'ct_theme'),
                        /*'std' => md5(time() . rand(100, 10000)),*/
                    ),
                    array(
                        'id' => 'general_multi_location_secret_link_' . $i,
                        'type' => 'info',
                        'option' => 'general_location_secret',
                        'desc' => 'Below you will find the geolocation link which will allow you to setup location. By default link directs to Google Map with your current location - to change the target truck location <br>- just drag and drop map marker and click on "Set location for truck: number"<br>' .
                            (!$savedToken ? '<br><span class="update-nag">Please click on "Save Changes" button to display link for location setup</span>' : '<a href="' . CT_THEME_DIR_URI . '/location_multi.php?k=' . $savedToken . '&truck=' . $i . '">'
                            . CT_THEME_DIR_URI . '/location.php?k=' . $savedToken . '&truck=' . $i . '</a>'))
                ,)
            );
        }

        return $sections;
    }


    /**
     * get truck number from Theme Options
     */
    public function __construct()
    {


        $ctOptions = get_option('foodtruck_options', false);
        if ($ctOptions && is_array($ctOptions) && isset($ctOptions['general_multi_locator_trucks'])) {
            self::$foodtrucks = $ctOptions['general_multi_locator_trucks'];
        } else {
            self::$foodtrucks = 0;

        }


        //var_dump(self::$foodtrucks);exit();

        add_filter('ct_theme_loader.options.load', array($this, 'addOptions'));
        add_action('admin_init', array($this, 'fillEmptyTokens'), 100);
        add_action('admin_init', array($this, 'removeUnusedMarkers'), 101);
        add_filter('ct_multi_locator_points', array($this, 'getPoints')); //call multimarker map shortcode
    }


    /**
     * @param $value
     * @return bool
     */
    public function getTrucksNumber($value)
    {
        return self::$foodtrucks;
    }

    /**
     * @param $value
     * @return bool
     */
    public function fillEmptyTokens()
    {
        //exit();
        for ($i = 1; $i <= self::$foodtrucks; $i++) {
            if (ct_get_option('general_multi_location_secret_' . $i) === '') {
                ct_set_option('general_multi_location_secret_' . $i, md5(time() . rand(100, 10000)));
            }
        }
    }


    /**
     * get token for selected foodtruck
     * @param $truck
     * @return bool
     */
    public function getSavedToken($truck)
    {
        $ctOptions = get_option('foodtruck_options', false);
        if ($ctOptions && is_array($ctOptions) && isset($ctOptions['general_multi_location_secret_' . $truck])) {
            $token = $ctOptions['general_multi_location_secret_' . $truck];
        } else {
            $token = false;
        }
        return $token;
    }

    /**
     * @param $points
     * @return array|string
     */
    public function getPoints($points)
    {

        $locations = get_option('general_locations_multi');
        if (is_array($locations) && !empty($locations)) {
            $markers = array();
            $counter = 0;
            foreach ($locations as $k => $v) {
                $name = ct_get_option('general_multi_location_name_' . $k); //get truck name by index key
                $name = '' === $name ? $k : $name;
                $markers[$counter]['latLng'] = array($v['lat'], $v['lng']);
                $markers[$counter]['data'] = $name;
                $markers[$counter]['options'] = array('icon' => get_stylesheet_directory_uri() . '/assets/images/marker-icon.png');

                //$markers['options'] = array('icon' => $icon);
                $counter++;
                //if ($counter == self::$foodtrucks)break;
            }
            return $markers;
        }
        return '';
    }

    /**
     *removes unused items from databsase
     */
    public function removeUnusedMarkers()
    {

        if (self::$foodtrucks == 0) {
            //clean all if 0 trucks set
            update_option('general_locations_multi', array());
            return;
        }
        $positions = get_option('general_locations_multi');
        $prepared = array_slice($positions, 0, self::$foodtrucks+1);

        update_option('general_locations_multi', $prepared);
    }


}

new ctMultiLocatorPlugin();