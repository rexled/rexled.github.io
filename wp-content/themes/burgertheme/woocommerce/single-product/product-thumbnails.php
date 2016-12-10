<?php
/**
 * Single Product Thumbnails
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.6.3
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post, $product, $woocommerce;

$attachment_ids = $product->get_gallery_attachment_ids();

if ( $attachment_ids ) {
	?>
	<div class="flexslider woo_flexslider_thumbs">
        <ul class="slides">
           <?php

           $featuredImage       = get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_small_thumbnail_size', 'shop_thumbnail' ));


?>
<?php if($featuredImage):?>
            <li><?php echo $featuredImage  ?></li>
<?php endif;?>

            <?php
		foreach ( $attachment_ids as $attachment_id ) {

			$classes = array( 'zoom' );



			$image_link = wp_get_attachment_url( $attachment_id );

			if ( ! $image_link )
				continue;

			$image       = wp_get_attachment_image( $attachment_id, apply_filters( 'single_product_small_thumbnail_size', 'shop_thumbnail' ) );
			$image_class = esc_attr( implode( ' ', $classes ) );
			$image_title = esc_attr( get_the_title( $attachment_id ) );

			echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', sprintf( '<li>%s</li>', $image ), $attachment_id, $post->ID );

		}

	?>

        </ul>
    </div>
	<?php
}