<?php
/**
 * Mini-cart
 *
 * Contains the markup for the mini-cart, used by the cart widget
 *
 * @author        WooThemes
 * @package    WooCommerce/Templates
 * @version     2.5.0
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly

global $woocommerce;
?>

<?php do_action('woocommerce_before_mini_cart'); ?>

    <ul class="cart_list product_list_widget <?php echo $args['list_class']; ?>">

        <?php if (sizeof(WC()->cart->get_cart()) > 0) : ?>

            <?php
            foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

                if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key)) {

                    $product_name = apply_filters('woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key);
                    $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key);
                    $product_price = apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key);

                    ?>
                    <li>
                        <div class="pull-left ct-mini-cart-image">
                            <a href="<?php echo get_permalink($product_id); ?>">
                                <?php echo str_replace(array('http:', 'https:'), '', $thumbnail); ?>
                            </a>
                        </div>

                        <div class="pull-right ct-mini-cart-desc">
                            <a href="<?php echo get_permalink($product_id); ?>" class="ct-mini-cart-name">
                                <?php echo $product_name; ?>
                            </a>
                            <?php echo $product_price ?>
                            <div class="clearfix"></div>
                            <?php echo apply_filters('woocommerce_widget_cart_item_quantity', '<span class="quantity"><span class="quantity-text">Quantity:</span> ' . sprintf('%s', $cart_item['quantity']) . '</span>', $cart_item, $cart_item_key); ?>

                            <?php echo WC()->cart->get_item_data($cart_item); ?>
                        </div>


                    </li>
                    <?php
                }
            }
            ?>

        <?php else : ?>

            <li class="empty"><?php _e('No products in the cart.', 'woocommerce'); ?></li>

        <?php endif; ?>

    </ul><!-- end product list -->

<?php if (sizeof(WC()->cart->get_cart()) > 0) : ?>

    <p class="total"><?php _e('Total', 'woocommerce'); ?>: <?php echo WC()->cart->get_cart_subtotal(); ?></p>

    <?php do_action('woocommerce_widget_shopping_cart_before_buttons'); ?>

    <p class="buttons">
        <?php
        //legacy support (woo<2.5)
        if (!function_exists('wc_get_cart_url')) {
            $miniCartURL = apply_filters('woocommerce_get_cart_url', wc_get_page_permalink('cart'));
        } else {
            $miniCartURL = wc_get_cart_url();
        }
        ?>
        <a href="<?php echo esc_url($miniCartURL); ?>"
           class="btn btn-xs pull-left btn-primary"><?php _e('View cart', 'woocommerce'); ?></a>
        <a href="<?php echo esc_url(wc_get_checkout_url()); ?>"
           class="btn btn-xs pull-right btn-primary checkout"><?php _e('continue to checkout', 'woocommerce'); ?></a>
    </p>

<?php endif; ?>

<?php do_action('woocommerce_after_mini_cart'); ?>