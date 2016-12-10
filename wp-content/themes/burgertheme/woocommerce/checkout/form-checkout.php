<?php
/**
 * Checkout Form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce;

wc_print_notices();

do_action( 'woocommerce_before_checkout_form', $checkout );

// If checkout registration is disabled and not logged in, the user cannot checkout
if ( ! $checkout->enable_signup && ! $checkout->enable_guest_checkout && ! is_user_logged_in() ) {
	echo apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) );
	return;
}

// filter hook for include new pages inside the payment method
$get_checkout_url = apply_filters( 'woocommerce_get_checkout_url', WC()->cart->get_checkout_url() ); ?>

    <h4 class="hdr4 cart-title"><?php _e('Your Order', 'ct_theme');?></h4>
<form name="checkout" method="post" class="checkout" action="<?php echo esc_url( $get_checkout_url ); ?>">

    <div class="woo-table-responsive">
    <table class="shop_table">
        <thead>
        <tr>
            <th class="product-remove"></th>
            <th class="product-thumbnail"><?php _e( 'Product', 'woocommerce' ); ?></th>
            <th class="product-name">&nbsp;</th>
            <th class="product-price"><?php _e( 'Price', 'woocommerce' ); ?></th>
            <th class="product-quantity"><?php _e( 'Quantity', 'woocommerce' ); ?></th>
            <th class="product-total"><?php _e( 'Total', 'woocommerce' ); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        do_action( 'woocommerce_review_order_before_cart_contents' );

        foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
            $_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

            if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
                ?>
                <tr class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
                    <td class="product-remove">
                        <?php
                        echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf( '<a href="%s" class="remove" title="%s">&times;</a>', esc_url( WC()->cart->get_remove_url( $cart_item_key ) ), __( 'Remove this item', 'woocommerce' ) ), $cart_item_key );
                        ?>
                    </td>
                    <td class="product-thumbnail">
                        <?php
                        $thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );

                        if ( ! $_product->is_visible() )
                            echo $thumbnail;
                        else
                            printf( '<a href="%s">%s</a>', $_product->get_permalink(), $thumbnail );
                        ?>
                    </td>
                    <td class="product-name">
                        <?php echo apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s" class="ct_product_name">%s</a>', $_product->get_permalink(), $_product->get_title() ), $cart_item, $cart_item_key );                        ?>
                        <?php echo WC()->cart->get_item_data( $cart_item ); ?>
                    </td>

                    <td class="product-price">
                        <?php
                        echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
                        ?>
                    </td>

                    <td class="product-quantity">
                        <?php echo apply_filters( 'woocommerce_checkout_cart_item_quantity', ' <strong class="product-quantity">' . sprintf( '%s', $cart_item['quantity'] ) . '</strong>', $cart_item, $cart_item_key ); ?>
                    </td>
                    <td class="product-subtotal">
                        <?php
                        echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );
                        ?>
                    </td>
                </tr>
            <?php
            }
        }

        do_action( 'woocommerce_review_order_after_cart_contents' );
        ?>
        </tbody>
    </table>
    </div>

    <div class="row ct-checkout-totals">
        <div class="col-sm-6 col-sm-offset-3">
            <div class="woo-table-responsive">
                <span class="pull-right ct-checkout-totals-title"><h5><?php _e('Cart Total', 'ct_theme');?></h5></span>
                <div class="clearfix"></div>
                <table class="ct-checkout-totals-table">
                    <tbody>

                    <tr class="cart-subtotal">
                        <th><?php _e( 'Cart Subtotal', 'woocommerce' ); ?></th>
                        <td><?php wc_cart_totals_subtotal_html(); ?></td>
                    </tr>

                    <?php foreach ( WC()->cart->get_coupons( 'cart' ) as $code => $coupon ) : ?>
                        <tr class="cart-discount coupon-<?php echo esc_attr( $code ); ?>">
                            <th><?php wc_cart_totals_coupon_label( $coupon ); ?></th>
                            <td><?php wc_cart_totals_coupon_html( $coupon ); ?></td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>

                        <?php do_action( 'woocommerce_review_order_before_shipping' ); ?>

                        <?php wc_cart_totals_shipping_html(); ?>

                        <?php do_action( 'woocommerce_review_order_after_shipping' ); ?>

                    <?php endif; ?>

                    <?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
                        <tr class="fee">
                            <th><?php echo esc_html( $fee->name ); ?></th>
                            <td><?php wc_cart_totals_fee_html( $fee ); ?></td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if ( WC()->cart->tax_display_cart === 'excl' ) : ?>
                        <?php if ( get_option( 'woocommerce_tax_total_display' ) === 'itemized' ) : ?>
                            <?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : ?>
                                <tr class="tax-rate tax-rate-<?php echo sanitize_title( $code ); ?>">
                                    <th><?php echo esc_html( $tax->label ); ?></th>
                                    <td><?php echo wp_kses_post( $tax->formatted_amount ); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr class="tax-total">
                                <th><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></th>
                                <td><?php echo wc_price( WC()->cart->get_taxes_total() ); ?></td>
                            </tr>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php foreach ( WC()->cart->get_coupons( 'order' ) as $code => $coupon ) : ?>
                        <tr class="order-discount coupon-<?php echo esc_attr( $code ); ?>">
                            <th><?php wc_cart_totals_coupon_label( $coupon ); ?></th>
                            <td><?php wc_cart_totals_coupon_html( $coupon ); ?></td>
                        </tr>
                    <?php endforeach; ?>

                    <?php do_action( 'woocommerce_review_order_before_order_total' ); ?>

                    <tr class="order-total">
                        <th><?php _e( 'Order Total', 'woocommerce' ); ?></th>
                        <td><?php wc_cart_totals_order_total_html(); ?></td>
                    </tr>

                    <?php do_action( 'woocommerce_review_order_after_order_total' ); ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

	<?php if ( sizeof( $checkout->checkout_fields ) > 0 ) : ?>

		<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

		<div class="row" id="customer_details">
            <div class="col-md-12">
                <?php do_action( 'woocommerce_checkout_billing' ); ?>
            </div>

			<div class="col-md-12">

				<?php do_action( 'woocommerce_checkout_shipping' ); ?>

			</div>

		</div>

		<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

	<?php endif; ?>
    <?php remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );?>
	<?php do_action( 'woocommerce_checkout_order_review' ); ?>

</form>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>

<?php echo do_shortcode('[spacer height="60"]')?>
<?php echo ct_shop_promo_text('index') ?>
<?php echo do_shortcode('[spacer height="60"]')?>