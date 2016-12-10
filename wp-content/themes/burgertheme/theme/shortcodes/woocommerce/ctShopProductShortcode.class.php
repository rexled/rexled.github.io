<?php

/**
 * Draws products
 */
class ctShopProductShortcode extends ctShortcodeQueryable
{

    /**
     * Returns name
     * @return string|void
     */
    public function getName()
    {
        return 'Shop Product';
    }

    /**
     * Shortcode name
     * @return string
     */
    public function getShortcodeName()
    {
        return 'shop_product';
    }


    /**
     * @return string
     */
    public function getTaxonomyNamespace()
    {
        return 'cat';
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
        $rounded = '';
        extract($attributes);


        $currency_position = get_option('woocommerce_currency_pos', 'left');

        //we grab only 1 product
        $products = $this->getCollection($attributes, array('post_type' => 'product', 'limit' => 1));
        if (!isset($products[0])) {
            return '';
        }
        $p = $products[0];

        $custom = get_post_custom($p->ID);
        $productObj = get_product($p->ID);
        $priceRange = array();
        $productPrice2 = array(0 => '', 1 => '');
        $buttonText = $button_label;
        $product_class = '';


        /*get min and max price if wc product is group or variable*/
        /*no simple product?*/
        if ($productObj->is_type('grouped') || $productObj->is_type('variable')) {

            $buttonLink = get_permalink($p->ID);

            /*variable product ?*/
            if ($productObj->is_type('variable')) {

                $priceRange = ctGetVariableProdPriceRange($productObj->get_available_variations());
                $buttonText = $button_variable_label;

                /*grouped  product ?*/
            } elseif ($productObj->is_type('grouped')) {

                $priceRange = ctGetGroupedProdPriceRange($productObj);
                $buttonText = $button_grouped_label;
            }

            if ($priceRange != false && !empty($priceRange) && ($priceRange['min_price'] !== $priceRange['max_price'])) {
                $price = str_replace('.', ',', $priceRange['min_price']);
                $price2 = str_replace('.', ',', $priceRange['max_price']);
                $productPrice = explode(",", $price);
                $productPrice2 = explode(",", $price2);

                /*one variation or one product in group or same prices ?*/
            } elseif ($priceRange != false && !empty($priceRange) && ($priceRange['min_price'] == $priceRange['max_price'])) {
                $price = str_replace('.', ',', $priceRange['min_price']);
                $productPrice = explode(",", $price);
            } else {
                $productPrice = array(0 => '', 1 => '');
            }
        } else {
            $product_class = 'product_type_simple';
            $sale_price = $this->getFromArray($custom, '_sale_price');


            if ($sale_price != $this->getFromArray($custom, '_regular_price') && $sale_price == $this->getFromArray($custom, '_price')) {
                $price = str_replace('.', ',', $this->getFromArray($custom, '_regular_price'));
                $price2 = str_replace('.', ',', $this->getFromArray($custom, '_sale_price'));
                $productPrice2 = explode(",", $price2);
            } else {
                $price = str_replace('.', ',', $this->getFromArray($custom, '_regular_price'));

            }


            $productPrice = explode(",", $price);
            $buttonLink = '?add-to-cart=' . $p->ID;
            $buttonText = $button_label;

        }


        $imageSrc = '';
        if ($images == 'yes') {
            if ($rounded == 'yes') {
                $imageSrc = $use_thumbnail == 'yes' ? ct_product_featured_image2_src($p->ID, 'product_thumb', 'product_box_2') : ct_get_feature_image_src($p->ID, 'product_box');
            } else {
                $imageSrc = $use_thumbnail == 'yes' ? ct_product_featured_image2_src($p->ID, 'product_thumb', 'product_box') : ct_get_feature_image_src($p->ID, 'product_box');
            }
        }



        $sku = $productObj->get_sku();

        $shortcode = $this->embedShortcode('shop_product_box', array(
            'button_label' => $buttonText,
            'button_link' => $buttonLink,
            'postscript' => $this->getFromArray($custom, 'postscript'),
            'align' => $align,
            'style' => $style,
            'rounded' => $rounded,
            'title' => $p->post_title,
            'title_link' => get_permalink($p->ID),
            'image' => $imageSrc,
            'image_link' => get_permalink($p->ID),
            'price' => ($showprice == 'yes' || $showprice == 'true') ? $productPrice[0] : '',
            'price2' => ($showprice == 'yes' || $showprice == 'true') ? $productPrice2[0] : '',
            'subprice' => isset($productPrice[1]) ? $productPrice[1] : '00',
            'subprice2' => isset($productPrice2[1]) ? $productPrice2[1] : '00',
            'currency' => get_woocommerce_currency_symbol(),
            'above_price_text' => $above_price_text,
            'currency_position' => $currency_position,
            'button_attribute' => 'data-product_id=' . $p->ID . ' ' . (!empty($sku) ? 'data-product_sku=' . $sku . '' : ''),
            'button_class' => $product_class
        ), ct_get_excerpt_by_id($p->ID));

        return do_shortcode($shortcode);
    }

    /**
     * Returns params from array ($custom)
     * @param $arr
     * @param $key
     * @param int $index
     * @param string $default
     * @return bool
     */

    protected function getFromArray($arr, $key, $index = 0, $default = '')
    {
        return isset($arr[$key][$index]) ? $arr[$key][$index] : $default;;
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
        //here we want use default IQueryable attributes because we want to get only 1 element
        return array(
            'slug' => array('query_map' => 'category_name', 'default' => '', 'type' => 'posts_select', 'post_type' => 'product', 'value_method' => 'slug', 'label' => __("Product", 'ct_theme')),
            'id' => array('default' => '', 'type' => 'posts_select', 'post_type' => 'input', 'label' => __("Product ID", 'ct_theme'), 'help' => array(__("Product ID. You can use slug or ID to find a product", 'ct_theme'))),
            'above_price_text' => array('label' => __("Above price text", 'ct_theme'), 'default' => 'just', 'type' => 'input', 'help' => __("Word above the price", 'ct_theme')),
            'button_label' => array('default' => 'Add to cart', 'type' => 'input', 'label' => __("Add to cart button text", 'ct_theme')),
            'button_variable_label' => array('default' => __('Select options', 'ct_theme'), 'type' => 'input', 'label' => __("Select options button text", 'ct_theme'), 'help' => __("For variable product", 'ct_theme')),
            'button_grouped_label' => array('default' => __('View products', 'ct_theme'), 'type' => 'input', 'label' => __("View products button text", 'ct_theme'), 'help' => __("For grouped product", 'ct_theme')),
            'images' => array('label' => __('Show images?', 'ct_theme'), 'default' => 'yes', 'type' => 'select', 'choices' => array('yes' => __('yes', 'ct_theme'), 'no' => __('no', 'ct_theme')), 'help' => __("Show images ? ", 'ct_theme')),
            'use_thumbnail' => array('label' => __('Use thumbnail', 'ct_theme'), 'default' => 'yes', 'type' => 'select', 'options' => array(
                'yes' => 'yes',
                'no' => 'no',
            ), 'help' => __('Should thumbnail image be used instead of default product image?', 'ct_theme')),
            'align' => array('label' => __('Align', 'ct_theme'), 'default' => '', 'type' => 'select', 'options' => array(
                '' => __('default', 'ct_theme'),
                'left' => __('left', 'ct_theme'),
                'right' => __('right', 'ct_theme')
            )),
            'style' => array('label' => __('Select style', 'ct_theme'), 'default' => '1', 'type' => 'select', 'options' => array(
                '1' => '1',
                '2' => '2',
                '3' => '3',
            )),
            'rounded' => array('label' => __('Rounded ?', 'ct_theme'), 'default' => 'no', 'type' => 'select', 'options' => array(
                'yes' => 'yes',
                'no' => 'no',
            )),
            'showprice' => array('label' => __('Show price ?', 'ct_theme'), 'default' => 'yes', 'type' => 'select', 'options' => array(
                'yes' => 'yes',
                'no' => 'no',
            )),
        );
    }
}

if (ct_is_woocommerce_active()) {
    new ctShopProductShortcode();
}
