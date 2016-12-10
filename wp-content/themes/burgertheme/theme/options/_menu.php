<?php
$sections[] = array(
    'icon' => NHP_OPTIONS_URL . 'img/glyphicons/glyphicons_114_list.png',
    'title' => __('Global', 'ct_theme'),
    'desc' => __("Global products settings", 'ct_theme'),
    'group' => __("Menu", 'ct_theme'),
    'fields' => array(
        array(
            'id' => 'products_index_currency',
            'title' => __("Default currency", 'ct_theme'),
            'type' => 'text',
            'std' => '$'
        ),
        array(
            'id' => 'products_currency_position',
            'title' => __("Currency position", 'ct_theme'),
            'type' => 'select',
            'options' => array(
                'before_price' => 'before price',
                'after_price' => 'after price'),
            'std' => 'before_price'
        ),

    ),

);