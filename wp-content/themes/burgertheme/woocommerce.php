<?php get_template_part('templates/page', 'head'); ?>
<?php $breadcrumbs = ct_show_single_post_breadcrumbs('page') ? 'yes' : 'no'; ?>


<?php if (is_shop()) {
    $pageTitle = woocommerce_page_title(false);
} elseif (is_product_category()) {
    $pageTitle = woocommerce_page_title(false);
} elseif (is_product_tag()) {
    $pageTitle = woocommerce_page_title(false);
} else {
    $pageTitle = ct_get_single_post_title('page');
} ?>

<?php global $wp_customize; ?>
<?php echo "</div><div class='inner inner-woocommerce'  data-image='" . get_theme_mod("shop_bg") . "' data-topspace='none' data-bottomspace='none' data-scroll='scroll'>" ?>



<?php if ($pageTitle && !is_product()): ?>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <?php echo do_shortcode('[title_row header="' . $pageTitle . '" breadcrumbs="' . $breadcrumbs . '"]') ?>
            </div>
        </div>


    </div>
<?php endif; ?>



<div class="container">
    <?php if (ct_get_option('shop_index_sidebar', 'left') == 'top' && !(is_single())): ?>
        <div class="wc_top_sidebar">
            <?php

            //$parent = new WC_Widget_Product_Categories();
            $wc_cats = new Custom_WC_Widget_Product_Categories;
            $args = array(
                'before_widget' => '<div class="widget">',
                'after_widget' => '</div>',
                'before_title' => '',
                'after_title' => '',

            );
            $wc_cats->widget($args, array('top'=>true, 'hide_empty'=>false, 'orderby' => 'order', 'title' => 'dfghdgh', 'hierarchical' => true, 'dropdown' => 0, 'count' => false, 'show_children_only'=>false,));
            //the_widget('WC_Widget_Product_Categories',$args, array('orderby' => 'order', 'title' => false, 'hierarchical' => 1, 'dropdown' => 0, 'count' => 0));
            ?>
        </div>
    <?php endif ?>
</div>

<div class="container">


    <?php if (is_product()): ?>
        <div class="row">
            <div class="col-md-12">
                <?php woocommerce_content(); ?>
            </div>
        </div>
    <?php else: ?>
        <?php // with sidebar ?>
        <?php if (1): ?>


            <?php if (ct_get_option('shop_index_sidebar', 'left') == 'left' || ct_get_option('shop_index_sidebar', 'left') == ''): ?>
                <div class="row">
                    <div class="col-md-8 col-md-push-4">
                        <?php woocommerce_content(); ?>
                    </div>
                    <div class="col-md-3 col-md-pull-8 column-sidebar sidebar-alt">
                        <?php get_template_part('templates/sidebar-woocommerce') ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="row">
                    <div class="col-md-12">
                        <?php woocommerce_content(); ?>
                    </div>
                </div>
            <?php endif ?>



            <!--row_end!-->
            <?php // no sidebar?>
        <?php else: ?>
            <div class="row">
                <div class="col-md-12">
                    <?php woocommerce_content(); ?>
                </div>


            </div>
            <!--row_end!-->
        <?php endif; ?>

    <?php endif; ?>

</div>

<?php echo do_shortcode('[spacer height="60"]') ?>
<div class="container">
    <?php get_template_part('templates/sidebar-woocommerce-single-bottom') ?>
</div>
<?php echo ct_shop_promo_text('index') ?>
<?php echo do_shortcode('[spacer height="60"]') ?>

<?php echo "</div>" ?>



