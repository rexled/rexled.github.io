<?php
/**
 * Single Product Image
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.6.3
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post, $woocommerce, $product;
$attachment_ids = $product->get_gallery_attachment_ids();
?>

<div class="images">

    <div class="row">
        <div class="col-md-12">
            <?php
            $image_title = esc_attr( get_the_title( get_post_thumbnail_id() ) );
            $image_link  = wp_get_attachment_url( get_post_thumbnail_id() );
            $image = get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ));

            $itemTemplate = '<li><a href="%s" itemprop="image" class="woocommerce-main-image zoom" title="%s" data-rel="prettyPhoto[gallery]">%s</a></li>';

            ?>
            <div class="flexslider woo_flexslider">
                <ul class="slides">

                    <?php if($image):?>
                        <?php echo apply_filters( 'woocommerce_single_product_image_html', sprintf( $itemTemplate, $image_link, $image_title,$image ), $post->ID );?>
                    <?php endif;?>

                    <?php
                    foreach ( $attachment_ids as $attachment_id ) {

                        $classes = array( 'zoom' );



                        $image_link = wp_get_attachment_url( $attachment_id );

                        if ( ! $image_link )
                            continue;

                        $image       = wp_get_attachment_image( $attachment_id, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ) );
                        $image_class = esc_attr( implode( ' ', $classes ) );
                        $image_title = esc_attr( get_the_title( $attachment_id ) );

                        echo apply_filters( 'woocommerce_single_product_image_html', sprintf( $itemTemplate, $image_link, $image_title, $image ), $post->ID );

                    }

                    ?>

                </ul>
            </div>

            <?php do_action( 'woocommerce_product_thumbnails' ); ?>

        </div>
    </div>
</div>