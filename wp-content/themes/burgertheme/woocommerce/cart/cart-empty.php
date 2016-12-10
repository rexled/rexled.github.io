<?php
/**
 * Empty cart page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

?>
</div></div>
<div class="bg-1 section pg404">
    <div class="inner" data-topspace="100" data-bottomspace="140" >
        <div class="container">
            <?php wc_print_notices();?>
            <h3 class="hdr1"><?php _e( 'Your cart is currently empty.', 'woocommerce' ) ?></h3>

            <div class="text-center">
                <span class="errorName"></span>
            </div>

            <div class="outer text-center">
                <h4 class="hdr3"><?php echo __('Sorry, but you need to add something to the cart.', 'ct_theme'); ?></h4>
                <a class="btn btn-primary" href="<?php echo apply_filters( 'woocommerce_return_to_shop_redirect', get_permalink( wc_get_page_id( 'shop' ) ) ); ?>"><?php _e( 'Return To Shop', 'woocommerce' ) ?></a>
            </div>

        </div>
    </div>
</div>
<div class="container">
    <?php do_action( 'woocommerce_cart_is_empty' ); ?>
</div>