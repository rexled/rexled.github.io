<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woothemes.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.6.1
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly

global $product, $woocommerce_loop;

// Store column count for displaying the grid
if (empty($woocommerce_loop['columns']))
    $woocommerce_loop['columns'] = apply_filters('loop_shop_columns', 4);

// Ensure visibility
if (!$product || !$product->is_visible())
    return;

// Increase loop count
$woocommerce_loop['loop']++;

// Extra post classes
$classes = array();
if (0 == ($woocommerce_loop['loop'] - 1) % $woocommerce_loop['columns'] || 1 == $woocommerce_loop['columns'])
    $classes[] = 'first';
if (0 == $woocommerce_loop['loop'] % $woocommerce_loop['columns'])
    $classes[] = 'last';
?>


<?php if (ct_get_option('shop_index_prod_box_type', 'wc') == 'wc_box' || ct_get_option('shop_index_prod_box_type', 'wc') == ''): ?>

    <li <?php post_class($classes); ?>>
        <div class="prodBox modern">
            <?php do_action('woocommerce_before_shop_loop_item'); ?>

            <div class="frameImg">
                <a href="<?php the_permalink(); ?>">
                    <?php
                    /**
                     * woocommerce_before_shop_loop_item_title hook
                     *
                     * @hooked woocommerce_show_product_loop_sale_flash - 10
                     * @hooked woocommerce_template_loop_product_thumbnail - 10
                     */
                    do_action('woocommerce_before_shop_loop_item_title');
                    ?>
                </a>
            </div>
            <div class="inner">
                <a href="<?php the_permalink(); ?>">
                    <h4><?php the_title(); ?></h4>
                </a>
                <?php $count = $product->get_rating_count();
                if ($count != 0) {
                    echo '<span class="review_number">(' . $count . ' reviews)</span>';
                }
                ?>
                <?php
                /**
                 * woocommerce_after_shop_loop_item_title hook
                 *
                 * @hooked woocommerce_template_loop_rating - 5
                 * @hooked woocommerce_template_loop_price - 10
                 */
                do_action('woocommerce_after_shop_loop_item_title');
                ?>

                <div class="clearfix"></div>

                <a class="btn btn-default btn-xs pull-left"
                   href="<?php the_permalink(); ?>"><?php echo __('<i class="fa fa-eye"></i>', 'ct_theme'); ?></a>

                <?php do_action('woocommerce_after_shop_loop_item'); ?>
                <div class="clearfix"></div>
            </div>
        </div>
    </li>

<?php elseif (ct_get_option('shop_index_prod_box_type', 'wc') == 'ct_box'): ?>
    <li <?php post_class($classes); ?>>
        <?php if (ct_get_option('shop_ft_prod_box_style', 'normal') == 'normal' || ct_get_option('shop_ft_prod_box_style', 'normal') == ''): ?>
            <?php echo do_shortcode('[shop_product id="' . get_the_id() . '"]') ?>
        <?php elseif (ct_get_option('shop_ft_prod_box_style', 'normal') == 'rounded'): ?>
            <?php echo do_shortcode('[shop_product rounded="yes" id="' . get_the_id() . '"]') ?>
        <?php endif; ?>
    </li>

    <?php
elseif (ct_get_option('shop_index_prod_box_type', 'wc') == 'clean'): ?>



    <?php remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_single_excerpt', 5); ?>



    <li <?php post_class($classes); ?>>
        <div class="prodBoxImage">
            <?php do_action('woocommerce_before_shop_loop_item'); ?>

            <div class="frameImg">
                <a href="<?php the_permalink(); ?>">
                    <?php
                    /**
                     * woocommerce_before_shop_loop_item_title hook
                     *
                     * @hooked woocommerce_show_product_loop_sale_flash - 10
                     * @hooked woocommerce_template_loop_product_thumbnail - 10
                     */
                    //use custom thumbs for this style
                    remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
                    do_action('woocommerce_before_shop_loop_item_title');

                    ?>
                </a>
            </div>
            <div class="inner">
                <a href="<?php the_permalink(); ?>">
                    <h5><?php the_title(); ?></h5>
                </a>
                <?php
                /**
                 * woocommerce_after_shop_loop_item_title hook
                 *
                 * @hooked woocommerce_template_loop_rating - 5
                 * @hooked woocommerce_template_loop_price - 10
                 */
                //do_action('woocommerce_after_shop_loop_item_title');
                do_action('woocommerce_after_shop_loop_item_title', 10);
                ?>

                <div class="clearfix"></div>

                <?php do_action('woocommerce_after_shop_loop_item'); ?>
                <div class="clearfix"></div>
            </div>
        </div>
    </li>

<?php endif; ?>