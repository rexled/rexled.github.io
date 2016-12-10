<?php
$sections[] = array(
    'icon' => NHP_OPTIONS_URL . 'img/glyphicons/glyphicons_019_cogwheel.png',

    'title' => __('Global', 'ct_theme'),
    'desc' => __("Global Shop settings", 'ct_theme'),
    'group' => __("WooCommerce", 'ct_theme'),
    'fields' => array(
        array(
            'id' => 'shop_index_sidebar',
            'title' => __("Index page sidebar type", 'ct_theme'),
            'type' => 'select',
            'options' => array(
                'left' => 'Left sidebar',
                'top' => 'Top sorting navigation',
                'none' => 'Hide sidebars'
            ),
        ),

        array(
            'id' => 'shop_index_prod_box_type',
            'title' => __("Choose index product box type", 'ct_theme'),
            'type' => 'select',
            'options' => array(
                'wc_box' => 'Woocommerce',
                'ct_box' => 'Foodtruck Classic',
                'clean' => 'Image with Overlay'
            ),
            'std' => 'wc_box'
        ),

        array(
            'id' => 'shop_ft_prod_box_style',
            'title' => __("Foodtruck classic product box style", 'ct_theme'),
            'type' => 'select',
            'options' => array(
                'normal' => 'Normal',
                'rounded' => 'Rounded',
            ),
            'std' => 'normal'
        ),

        array(
            'id' => 'shop_price_area_type',
            'title' => __("Choose price area style", 'ct_theme'),
            'type' => 'select',
            'options' => array(
                'ft' => 'Foodtruck Classic',
                'wc' => 'Woocommerce',
            ),
            'std' => 'ft'
        ),


        array(
            'id' => 'shop_index_bottom_promo_text_title',
            'title' => __("Shop index bottom promo text title", 'ct_theme'),
            'type' => 'text',
            'std' => ''
        ),
        array(
            'id' => 'shop_index_bottom_promo_text_subtitle',
            'title' => __("Shop index bottom promo text subtitle", 'ct_theme'),
            'type' => 'text',
            'std' => ''
        ),
        array(
            'id' => 'shop_index_bottom_promo_text_class',
            'title' => __("Shop index bottom promo text class", 'ct_theme'),
            'type' => 'text',
            'std' => ''
        ),


        array(
            'id' => 'shop_single_bottom_promo_text_title',
            'title' => __("Shop single bottom promo text title", 'ct_theme'),
            'type' => 'text',
            'std' => ''
        ),
        array(
            'id' => 'shop_single_bottom_promo_text_subtitle',
            'title' => __("Shop single bottom promo text subtitle", 'ct_theme'),
            'type' => 'text',
            'std' => ''
        ),
        array(
            'id' => 'shop_single_bottom_promo_text_class',
            'title' => __("Shop single bottom promo text class", 'ct_theme'),
            'type' => 'text',
            'std' => ''
        ),

        array(
            'id' => 'shop_index_above_price',
            'title' => __("Shop index above price text (for Foodtruck Classic box)", 'ct_theme'),
            'type' => 'text',
            'std' => 'just'
        ),


    ),


);


$sections[] = array(
    'icon' => NHP_OPTIONS_URL . 'img/glyphicons/glyphicons_202_shopping_cart.png',
    'title' => __('Cart', 'ct_theme'),
    'desc' => __("Cart settings", 'ct_theme'),
    'group' => __("WooCommerce", 'ct_theme'),
    'fields' => array(


        array(
            'id' =>'shop_cart_show_baner',
            'title' =>__("Show baner?", 'ct_theme'),
            'type' => 'select_show',
            'std' => 0
        ),

        array(
            'id' => 'shop_cart_baner_title',
            'title' => __("Baner title", 'ct_theme'),
            'type' => 'text',
            'std' => ''
        ),

        array(
            'id' => 'shop_cart_baner_link',
            'title' => __("Baner link", 'ct_theme'),
            'type' => 'text',
            'std' => ''
        ),

        array(
            'id' => 'shop_cart_baner_image',
            'title' => __("Baner image", 'ct_theme'),
            'type' => 'upload',
            'std' => ''
        ),


    ),


);