<?php
/**
 * Loop Add to Cart
 *
 * @author        WooThemes
 * @package    WooCommerce/Templates
 * @version     2.5.0
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly

global $product;

echo apply_filters('woocommerce_loop_add_to_cart_link',
    sprintf('<a href="%s" data-quantity="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" class="btn btn-primary btn-xs pull-right %s"><i class="fa fa-shopping-cart"></i> %s</a>',
        esc_url($product->add_to_cart_url()),
        esc_attr(isset($quantity) ? $quantity : 1),
        esc_attr($product->id),
        esc_attr($product->get_sku()),
        esc_attr( isset( $class ) ? preg_replace('/\bbutton\b/', '', $class) : 'button123' ),
        esc_html($product->add_to_cart_text())
    ),
    $product);


