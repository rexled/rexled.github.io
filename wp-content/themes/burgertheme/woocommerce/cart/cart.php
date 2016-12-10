<?php
/**
 * Cart Page
 *
 * @author        WooThemes
 * @package    WooCommerce/Templates
 * @version     2.3.8
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly

global $woocommerce;

$shop_page_url = get_permalink(wc_get_page_id('shop'));

do_action('woocommerce_before_cart'); ?>
<div class="row">
    <div class="col-xs-12 text-center">
        <a class="back-shop" href="<?php echo $shop_page_url ?>"><?php echo __('Back to shop', 'ct_theme') ?></a>
    </div>
</div>

<?php wc_print_notices(); ?>

<form action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">

    <?php do_action('woocommerce_before_cart_table'); ?>

    <div class="woo-table-responsive">
        <table class="shop_table cart" cellspacing="0">
            <thead>
            <tr>
                <th class="product-remove"></th>
                <th class="product-thumbnail"><?php _e('Product', 'woocommerce'); ?></th>
                <th class="product-name">&nbsp;</th>
                <th class="product-price"><?php _e('Price', 'woocommerce'); ?></th>
                <th class="product-quantity"><?php _e('Quantity', 'woocommerce'); ?></th>
                <th class="product-subtotal"><?php _e('Total', 'woocommerce'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php do_action('woocommerce_before_cart_contents'); ?>

            <?php
            foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

                if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) {
                    ?>
                    <tr class="<?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">
                        <td class="product-remove">
                            <?php
                            echo apply_filters('woocommerce_cart_item_remove_link', sprintf('<a href="%s" class="remove" title="%s">&times;</a>', esc_url(WC()->cart->get_remove_url($cart_item_key)), __('Remove this item', 'woocommerce')), $cart_item_key);
                            ?>
                        </td>
                        <td class="product-thumbnail">
                            <?php
                            $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key);

                            if (!$_product->is_visible())
                                echo $thumbnail;
                            else
                                printf('<a href="%s">%s</a>', $_product->get_permalink(), $thumbnail);
                            ?>
                        </td>

                        <td class="product-name">
                            <?php
                            if (!$_product->is_visible())
                                echo apply_filters('woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key);
                            else
                                echo apply_filters('woocommerce_cart_item_name', sprintf('<a href="%s" class="ct_product_name">%s</a>', $_product->get_permalink(), $_product->get_title()), $cart_item, $cart_item_key);

                            // Meta data
                            echo WC()->cart->get_item_data($cart_item);

                            // Backorder notification
                            if ($_product->backorders_require_notification() && $_product->is_on_backorder($cart_item['quantity']))
                                echo '<p class="backorder_notification">' . __('Available on backorder', 'woocommerce') . '</p>';
                            ?>
                        </td>

                        <td class="product-price">
                            <?php
                            echo apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key);
                            ?>
                        </td>

                        <td class="product-quantity">
                            <?php
                            if ($_product->is_sold_individually()) {
                                $product_quantity = sprintf('1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key);
                            } else {
                                $product_quantity = woocommerce_quantity_input(array(
                                    'input_name' => "cart[{$cart_item_key}][qty]",
                                    'input_value' => $cart_item['quantity'],
                                    'max_value' => $_product->backorders_allowed() ? '' : $_product->get_stock_quantity(),
                                    'min_value' => '0'
                                ), $_product, false);
                            }

                            echo apply_filters('woocommerce_cart_item_quantity', $product_quantity, $cart_item_key);
                            ?>
                        </td>

                        <td class="product-subtotal">
                            <?php
                            echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key);
                            ?>
                        </td>
                    </tr>
                    <?php
                }
            }

            do_action('woocommerce_cart_contents');
            ?>
            <tr>
                <td colspan="6" class="actions">

                    <?php if ( wc_coupons_enabled() ) { ?>
                        <div class="coupon">

                            <label for="coupon_code"><?php _e('Coupon', 'woocommerce'); ?>:</label> <input type="text"
                                                                                                           name="coupon_code"
                                                                                                           class="input-text form-control input-xs"
                                                                                                           id="coupon_code"
                                                                                                           value=""
                                                                                                           placeholder="<?php _e('Coupon code', 'woocommerce'); ?>"/>
                            <input type="submit" class="button" name="apply_coupon"
                                   value="<?php _e('Apply Coupon', 'woocommerce'); ?>"/>

                            <?php do_action('woocommerce_cart_coupon'); ?>

                        </div>
                    <?php } ?>

                    <input type="submit" class="button" name="update_cart"
                           value="<?php _e('Update Cart', 'woocommerce'); ?>"/>
                    <!-- <input type="submit" class="checkout-button button alt wc-forward" name="proceed" value="<?php //_e( 'Proceed to Checkout', 'woocommerce' ); ?>" /> -->

                    <?php //do_action('woocommerce_proceed_to_checkout'); ?>

                    <?php wp_nonce_field('woocommerce-cart'); ?>
                </td>
            </tr>

            <?php do_action('woocommerce_after_cart_contents'); ?>
            </tbody>
        </table>
    </div>

    <?php do_action('woocommerce_after_cart_table'); ?>

</form>

<div class="cart-collaterals">
    <div class="row">
        <div class="col-md-6">
            <?php if (ct_get_option('shop_cart_show_baner', 1)): ?>

                <h2 class="hdr8"><?php echo ct_get_option('shop_cart_baner_title', ''); ?></h2>
                <span class="advertising">
                    <a href="<?php echo ct_get_option('shop_cart_baner_link', '') ?>"><img
                            src="<?php echo ct_get_option('shop_cart_baner_image', '') ?>" alt="Advertising"></a>
                </span>


            <?php endif ?>
        </div>

        <div class="col-md-6">
            <?php woocommerce_cart_totals(); ?>

            <?php woocommerce_shipping_calculator(); ?>

            <a href="<?php echo $woocommerce->cart->get_checkout_url() ?>" class="checkout-button pull-right"><i
                    class="fa fa-shopping-cart"></i> <?php _e('Checkout','ct_theme')?></a>
        </div>
    </div>

</div>


<?php do_action('woocommerce_after_cart'); ?>
