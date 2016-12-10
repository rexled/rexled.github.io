<?php

/**
 * Pricelist shortcode
 */
class ctShopProductBoxShortcode extends ctShortcode
{


    public function setPrices($prices = array())
    {

    }


    /**
     * Returns name
     * @return string|void
     */
    public function getName()
    {
        return 'Shop Product Box';
    }

    /**
     * Shortcode name
     * @return string
     */
    public function getShortcodeName()
    {
        return 'shop_product_box';
    }

    /**
     * Returns shortcode type
     * @return mixed|string
     */

    public function getShortcodeType()
    {
        return self::TYPE_SHORTCODE_ENCLOSING;
    }


    /**
     * Handles shortcode
     * @param $atts
     * @param null $content
     * @return string
     */

    public function handle($atts, $content = null)
    {

        extract(shortcode_atts($this->extractShortcodeAttributes($atts), $atts));

        $mainContainerAtts = array(
            'class' => array(
                'prodBox',
                'prodBoxWoo',
                ($style == '1') ? '' : 'type' . $style,
                ($align == 'right') ? 'pull-right' : '',
                ($align == 'left') ? 'pull-left' : '',
                $class
            ),
        );

        if (!empty($image)) {
            if ($rounded == 'yes' || $rounded == 'true') {
                if ($image_link) {
                    $imageCode = '<a href="' . $image_link . '">[rounded_img  size="174" src ="' . $image . '"][/rounded_img]</a>';
                } else {
                    $imageCode = '[rounded_img  size="174" src ="' . $image . '"][/rounded_img]';
                }
            } else {
                if ($image_link) {
                    $imageCode = '<a href="' . $image_link . '"><img src="' . $image . '" alt=" "></a>';
                } else {
                    $imageCode = '<img src="' . $image . '" alt=" ">';
                }
            }
        }else{
            $imageCode='[spacer height="50"]';
        }


        $buttonHtml = $button_label && $button_link ? ('<a href="' . $button_link . '" '.$button_attribute.' class="add_to_cart_button btn btn-primary btn-sm '.$button_class.'">' . $button_label . '</a>') : '';
        $postscript = $postscript ? '<span class="info">' . $postscript . '</span>' : '';
        $subprice = $subprice ? '<span>' . $subprice . '</span>' : '';
        $subprice2 = $subprice2 != '' ? '<span>' . $subprice2 . '</span>' : '';
        $price2 = $price2 != '' ? ' - ' . $price2 : '';

        if ($price != '') {
            $priceArea = '<span class="price ' . ($price2 != '' ? 'grouped' : '') . '"><small>' . $above_price_text . '</small>';
            switch ($currency_position) {
                case 'left':
                    $priceArea .= '<em>' . $currency . '</em>' . $price . $subprice . $price2 . $subprice2;
                    break;
                case 'right':
                    $priceArea .= $price . $subprice . $price2 . $subprice2 . '<em>' . $currency . '</em>';
                    break;
                case 'left_space':
                    $priceArea .= '<em>' . $currency . '</em> ' . $price . $subprice . $price2 . $subprice2;
                    break;
                case 'right_space':
                    $priceArea .= $price . $subprice . $price2 . $subprice2 . ' <em>' . $currency . '</em>';
                    break;
                default:
                    $priceArea .= '<em>' . $currency . '</em> ' . $price . $subprice . $price2 . $subprice2;
            }
        } else {
            $priceArea = '';
        }

        $title = $title_link ? '<a href="' . $title_link . '">' . $title . '</a>' : $title;

        $html = '
		<div ' . $this->buildContainerAttributes($mainContainerAtts, $atts) . '>
            <div class="frameImg">' . $imageCode . '</div>
            <div class="inner">
              <h4>' . $title . '</h4>
              <p>' . $content . '[spacer]'.$buttonHtml . '</p>';
        $html .= $priceArea . '
              ' . $postscript . '

            </div>
          </div>
		';
        return do_shortcode($html);
    }

    /**
     * Returns config
     * @return null
     */
    public function getAttributes()
    {
        return array(
            'title' => array('label' => __('title', 'ct_theme'), 'default' => '', 'type' => 'input'),
            'title_link' => array('label' => __('title link', 'ct_theme'), 'default' => '', 'type' => 'input', 'help' => __("leave empty for disable title link", 'ct_theme')),
            'content' => array('label' => __('content', 'ct_theme'), 'default' => '', 'type' => 'textarea'),
            'style' => array('label' => __('Select style', 'ct_theme'), 'default' => '1', 'type' => 'select', 'options' => array(
                '1' => '1',
                '2' => '2',
                '3' => '3',
            )),
            'rounded' => array('label' => __('Rounded ?', 'ct_theme'), 'default' => 'no', 'type' => 'select', 'options' => array(
                'yes' => 'yes',
                'no' => 'no',
            )),
            'postscript' => array('label' => __('postcript', 'ct_theme'), 'default' => '', 'type' => 'input', 'help' => __("e.g. *promotion only on weekdays", 'ct_theme')),
            'image' => array('label' => __("image", 'ct_theme'), 'default' => '', 'type' => 'image', 'help' => __("Image source", 'ct_theme')),
            'image_link' => array('label' => __("image link", 'ct_theme'), 'default' => '', 'type' => 'image', 'help' => __("leave empty for disable image link", 'ct_theme')),
            'price' => array('label' => __('price', 'ct_theme'), 'default' => '', 'type' => 'input', 'example' => '456,50'),
            'subprice' => array('label' => __('subprice', 'ct_theme'), 'default' => '', 'type' => 'input'),
            'price2' => array('label' => __('second price', 'ct_theme'), 'default' => '', 'type' => 'input', 'example' => '456,50', 'help' => __("for grouped product", 'ct_theme')),
            'subprice2' => array('label' => __('second subprice', 'ct_theme'), 'default' => '', 'type' => 'input', 'help' => __("for grouped product", 'ct_theme')),
            'above_price_text' => array('label' => __("Above price text", 'ct_theme'), 'default' => '', 'type' => 'input', 'help' => __("Word above the price", 'ct_theme')),
            'currency' => array('label' => __('currency', 'ct_theme'), 'default' => ct_get_option('products_index_currency', '$'), 'type' => 'input'),
            'button_label' => array('default' => '', 'type' => 'input', 'label' => __("button text", 'ct_theme')),
            'button_link' => array('default' => '#', 'type' => 'input', 'label' => __("button link", 'ct_theme')),
            'align' => array('label' => __('Align', 'ct_theme'), 'default' => '', 'type' => 'select', 'options' => array(
                '' => __('default', 'ct_theme'),
                'left' => __('left', 'ct_theme'),
                'right' => __('right', 'ct_theme')
            )),
            'currency_position' => array('label' => __('Currency position', 'ct_theme'), 'default' => 'left', 'type' => 'select', 'options' => array(
                'left' => __('Left', 'ct_theme'),
                'right' => __('Right', 'ct_theme'),
                'left_space' => __('Left with space', 'ct_theme'),
                'right_space' => __('Right with space', 'ct_theme')
            )),
            'class' => array('label' => __("Custom class", 'ct_theme'), 'default' => '', 'type' => 'input', 'help' => __('Adding custom class allows you to set diverse styles in css to the element. Type in name of class, which you defined in css. You can add as much classes as you like.', 'ct_theme')),
            'button_attribute' => array('label' => __("Button attribute", 'ct_theme'), 'default' => '', 'type' => 'input', 'help' => __('Additional button attributes.', 'ct_theme')),
            'button_class' => array('label' => __("Button class", 'ct_theme'), 'default' => '', 'type' => 'input', 'help' => __('Additional button classes.', 'ct_theme')),
        );
    }
}

if (ct_is_woocommerce_active()) {
    new ctShopProductBoxShortcode();
}
