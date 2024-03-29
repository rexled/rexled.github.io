<?php
class Custom_WooCommerce_Widget_Cart extends WC_Widget_Cart
{



    public function widget($args, $instance)
    {
        extract( $args );

        //if ( is_cart() || is_checkout() ) return;

        $title = apply_filters('widget_title', empty( $instance['title'] ) ? __( 'Cart', 'woocommerce' ) : $instance['title'], $instance, $this->id_base );
        $hide_if_empty = empty( $instance['hide_if_empty'] ) ? 0 : 1;

        echo $before_widget;

        if ( $title )
            echo $before_title . $title . $after_title;

        if ( $hide_if_empty )
            echo '<div class="hide_cart_widget_if_empty">';

        // Insert cart widget placeholder - code in woocommerce.js will update this on page load
        echo '<div class="widget_shopping_cart_content"></div>';

        if ( $hide_if_empty )
            echo '</div>';

        echo $after_widget;


    }


}

new Custom_WooCommerce_Widget_Cart();