<?php
if (!function_exists('ct_is_woocommerce_active')) {

    function ct_is_woocommerce_active()
    {
        return class_exists('WooCommerce');
    }
}
if (!ct_is_woocommerce_active()) {
    return;
}


add_theme_support('woocommerce');
//include_once(__DIR__ . '/ CT_THEME_SETTINGS_MAIN_DIR');
ctThemeLoader::getFilesLoader()->includeOnce( CT_THEME_SETTINGS_MAIN_DIR.'/plugin/woocommerce/ctTermFieldsExtension.php');


/**
 * Woocommerce
 */


/* custom js */
if (!function_exists('ct_woo_scripts')) {
    function ct_woo_scripts()
    {
        wp_register_script('ct-woo-select2', CT_THEME_DIR_URI . '/woocommerce/js/select2.js', array('jquery'), false, true);
        wp_enqueue_script('ct-woo-select2');

        wp_register_script('ct-woo-customjs', CT_THEME_DIR_URI . '/woocommerce/js/woocommerce.js', array('jquery'), false, true);
        wp_enqueue_script('ct-woo-customjs');

        wp_register_script('ct-isotope', CT_THEME_DIR_URI . '/woocommerce/js/jquery.isotope.min.js');
        wp_enqueue_script('ct-isotope');

        global $woocommerce;
        if(floatval($woocommerce->version) >= 2.3){
            wp_register_script( 'ct-woo-qt-change', CT_THEME_DIR_URI . '/woocommerce/js/qt-change.js', array( 'jquery' ), false, true );
            wp_enqueue_script( 'ct-woo-qt-change' );
        }

        if (is_product()) {
            wp_register_script('ct-woo-check-gallery', CT_THEME_DIR_URI . '/woocommerce/js/check-gallery.js', array('wc-add-to-cart-variation'), false, true);
            wp_enqueue_script('ct-woo-check-gallery');

            wp_register_script('ct-woo-selectord', CT_THEME_DIR_URI . '/woocommerce/js/select-single-product.js', array('wc-add-to-cart-variation'), false, true);
            wp_enqueue_script('ct-woo-selectord');
        }
    }
}

add_action('wp_enqueue_scripts', 'ct_woo_scripts');

/* edit price html */

//add_filter('woocommerce_get_price_html', 'wpa83367_price_html', 100, 2);
function wpa83367_price_html($price, $product)
{

    return '<span class="price-wrapper">' . $price . '</span>';
}


/* remove woocommerce title */
add_filter('woocommerce_show_page_title', '__return_false');


/* custom class for next / prev links */
add_filter('next_posts_link_attributes', 'posts_link_attributes');
add_filter('previous_posts_link_attributes', 'posts_link_attributes');

function posts_link_attributes()
{
    return 'class="navigation-blog"';
}


/* custom product search widget */
add_filter('get_product_search_form', 'woo_custom_product_searchform');

function woo_custom_product_searchform($form)
{

    $form = '<form class="search-form" role="search" method="get" id="searchform" action="' . esc_url(home_url('/')) . '">
			<input type="text" class="form-control" value="' . get_search_query() . '" name="s" id="s" placeholder="' . __('My search form', 'woocommerce') . '" />
			<input type="submit" class="btn btn-primary" id="searchsubmit" value="' . esc_attr__('Search', 'woocommerce') . '" />
			<input type="hidden" name="post_type" value="product" />
	</form>';

    return $form;

}


remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
add_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 25);

/* move up sells */

remove_action('woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15);
add_action('woocommerce_after_single_product', 'woocommerce_output_upsells', 15);

if (!function_exists('woocommerce_output_upsells')) {
    function woocommerce_output_upsells()
    {
        woocommerce_upsell_display(3, 3); // Display 3 products in rows of 3
    }
}

/* move related products */

function woocommerce_remove_related_produts()
{
    remove_action(
        'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);
}

add_action('woocommerce_after_single_product_summary', 'woocommerce_remove_related_produts');


add_action('woocommerce_after_single_product', 'woocommerce_output_related_products', 20);

function woocommerce_output_related_products()
{
    $args = array(
        'posts_per_page' => 3,
        'columns' => 3,
        'orderby' => 'rand'
    );
    woocommerce_related_products($args);
}

// Change number or products per page
add_filter('loop_shop_per_page', create_function('$cols', 'return 6;'));

// Override theme default specification for product # per row
function loop_columns()
{
    if (ct_get_option('shop_index_sidebar', 'left')=='left') {
        return 2;
    } else {
        return 3;
    }
}

add_filter('loop_shop_columns', 'loop_columns',4);

/* remove headers from tabs */
add_filter('woocommerce_product_additional_information_heading', '__return_false');
add_filter('woocommerce_product_description_heading', '__return_false');


/* remove prettyphoto call, move to woocommerce.js */
add_action('wp_enqueue_scripts', 'remove_woo_js_calls', 99);

function remove_woo_js_calls()
{
    wp_dequeue_script('prettyPhoto-init');
}


function enable_prettyphoto_js()
{
    global $post, $wp;

    $suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
    $lightbox_en = get_option('woocommerce_enable_lightbox') == 'yes' ? true : false;
    $assets_path = str_replace(array('http:', 'https:'), '', WC()->plugin_url()) . '/assets/';

    if ($lightbox_en && (is_product() || (!empty($post->post_content) && strstr($post->post_content, '[product_page')))) {
        wp_enqueue_script('prettyPhoto-init2', CT_THEME_DIR_URI . '/woocommerce/js/jquery.prettyPhoto.init.js', array('jquery', 'prettyPhoto'), WC_VERSION, true);
    }
}

add_action('wp_enqueue_scripts', 'enable_prettyphoto_js', 99);
/*
WP roots issue with - fix
http://wordpress.stackexchange.com/questions/95293/wp-enqueue-style-will-not-let-me-enforce-screen-only
    'media'   => 'only screen and (max-width: ' . apply_filters( 'woocommerce_style_smallscreen_breakpoint', $breakpoint = '768px' ) . ')'
*/
remove_filter('style_loader_tag', 'roots_clean_style_tag');

// Add excerpt description to product list

add_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_single_excerpt', 5);

//Display product category descriptions under category image/title on woocommerce shop page */

function wpa89819_wc_single_product()
{

    $product_cats = wp_get_post_terms(get_the_ID(), 'product_cat');

    if ($product_cats && !is_wp_error($product_cats)) {

        $single_cat = array_shift($product_cats);
        $thumbnail_id = get_woocommerce_term_meta($single_cat->term_id, 'thumbnail_id', true);
        $image = wp_get_attachment_url($thumbnail_id); ?>


        <span class="product_category">
                <span class="wrapper">
                    <?php if (!empty($image)):?>
                        <img class="product_category_image" alt="<?php echo $single_cat->name; ?>"
                         src="<?php echo $image; ?>">
                    <?php endif?>

                    <span itemprop="name"
                          class="product_category_title"><span><?php echo $single_cat->name; ?></span></span>
                </span>
            </span>


    <?php
    }
}

add_action('woocommerce_before_shop_loop_item_title', 'wpa89819_wc_single_product', 1);


//override_woocommerce_widgets:

add_action('widgets_init', 'override_woocommerce_widgets', 15);
function override_woocommerce_widgets()
{
    if (class_exists('WC_Widget_Product_Categories')) {
        unregister_widget('WC_Widget_Product_Categories');
        //include_once(__DIR__ . '/widgets/class-wc-widget-product-categories.php');
        ctThemeLoader::getFilesLoader()->includeOnce( CT_THEME_SETTINGS_MAIN_DIR.'/plugin/woocommerce/widgets/class-wc-widget-product-categories.php');
        register_widget('Custom_WC_Widget_Product_Categories');
    }
    if (class_exists('WC_Widget_Cart')) {
        unregister_widget('WC_Widget_Cart');
        //include_once(__DIR__ . '/widgets/class-wc-widget-cart.php');
        ctThemeLoader::getFilesLoader()->includeOnce( CT_THEME_SETTINGS_MAIN_DIR.'/plugin/woocommerce/widgets/class-wc-widget-cart.php');
        register_widget('Custom_WooCommerce_Widget_Cart');
    }
    if (class_exists('WC_Widget_Layered_Nav')) {
        unregister_widget('WC_Widget_Layered_Nav');
        //include_once(__DIR__ . '/widgets/class-wc-widget-layered-nav.php');
        ctThemeLoader::getFilesLoader()->includeOnce( CT_THEME_SETTINGS_MAIN_DIR.'/plugin/woocommerce/widgets/class-wc-widget-layered-nav.php');
        register_widget('Custom_WC_Widget_Layered_Nav');
    }
}


// redirect to page on login

add_filter('woocommerce_login_redirect', 'ras_login_redirect');

function ras_login_redirect($redirect_to)
{
    $redirect_to = home_url();

    return $redirect_to;
}

// Ensure cart contents update when products are added to the cart via AJAX (place the following in functions.php)
add_filter('add_to_cart_fragments', 'woocommerce_header_add_to_cart_fragment');

function woocommerce_header_add_to_cart_fragment($fragments)
{
    global $woocommerce;

    ob_start();
    ?>
    <a class="cart-contents" href="<?php echo $woocommerce->cart->get_cart_url(); ?>"
       title="<?php _e('View your shopping cart', 'woothemes'); ?>" id="showCart"><i class="fa fa-shopping-cart"></i>
        <span>(<?php echo $woocommerce->cart->cart_contents_count ?>)</span></a>
    <?php

    $fragments['a.cart-contents'] = ob_get_clean();

    return $fragments;

}

;

function woocommerce_remove_breadcrumb()
{
    remove_action(
        'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
}

add_action(
    'woocommerce_before_main_content', 'woocommerce_remove_breadcrumb'
);


add_action('ct_woocommerce_share', 'ct_wooshare');
function ct_wooshare()
{
    global $post;
    echo '<div class="clearfix"></div>';
    echo '
        <strong>'.__('Share this product','ct_theme').'</strong>
        <ul class="list-unstyled smallSocials clearfix soc_list soc-small">
            <li>
                <a href="http://www.facebook.com/sharer.php?u=' . get_permalink() . '" target="_blank" data-toggle="tooltip" data-placement="top" title="" data-original-title="facebook"><i class="fa fa-facebook"></i></a>
            </li>
            <li>
                <a href="https://twitter.com/share?url=' . get_permalink() . '" target="_blank" data-toggle="tooltip" data-placement="top" title="" data-original-title="twitter"><i class="fa fa-twitter"></i></a>
            </li>
            <li>
                <a href="https://plus.google.com/share?url=' . get_permalink() . '" target="_blank" data-toggle="tooltip" data-placement="top" title="" data-original-title="google"><i class="fa fa-google-plus"></i></a>
            </li>
            <li>
                <a href="//pinterest.com/pin/create/button/?url=' . get_permalink() . '&media=' . urlencode(wp_get_attachment_url(get_post_thumbnail_id())) . '&description=' . get_the_title() . apply_filters('woocommerce_short_description', $post->post_excerpt) . '" target="_blank" data-toggle="tooltip" data-placement="top" title="" data-original-title="pinterest"><i class="fa fa-pinterest"></i></a>
            </li>
            <li>
                <a href="mailto:enteryour@addresshere.com?subject=' . get_the_title() . '&body=' . apply_filters('woocommerce_short_description', $post->post_excerpt) . get_permalink() . '" data-toggle="tooltip" data-placement="top" title="" data-original-title="mail"><i class="fa fa-envelope"></i></a>
            </li>
        </ul>';
}


add_action('woocommerce_before_cart_table', 'ct_cart_title');
function ct_cart_title()
{
    echo '<h3 class="hdr1 cart-title">' . __('Your cart contains', 'ct_theme') . '</h3>';
}

add_filter('woocommerce_layered_nav_link', 'woocommerce_layered_nav_link_change');

function woocommerce_layered_nav_link_change($redirect_to)
{
    return $redirect_to;
}


/*** return min and max price from grouped wc product type***/
function ctGetGroupedProdPriceRange($product)
{
    if (!is_object($product)) {
        return false;
    } else {
        $all_prices = array();

        foreach ($product->get_children() as $child_id) {
            $all_prices[] = get_post_meta($child_id, '_price', true);

        }
        if (!empty($all_prices)) {
            $max_price = max($all_prices);
            $min_price = min($all_prices);
            return array('min_price' => $min_price, 'max_price' => $max_price);
        } else {
            return false;
        }
    }
}


/*** return min and max price from variable wc product type***/
function ctGetVariableProdPriceRange($available_variations = array())
{
    if (!is_array($available_variations)) {
        return false;
    } else {
        $all_prices = array();
        foreach ($available_variations as $key => $value) {
            $variable_product = new WC_Product_Variation($value['variation_id']);
            $regular_price = $variable_product->regular_price;
            $all_prices[] = $regular_price;
        }
        if (!empty($all_prices)) {
            $max_price = max($all_prices);
            $min_price = min($all_prices);
            return array('min_price' => $min_price, 'max_price' => $max_price);
        } else {
            return false;
        }
    }
}

function addPriceSuffix($format, $currency_pos) {
    switch ( $currency_pos ) {
        case 'left' :
            $currency = get_woocommerce_currency();
            $format = '<span class="wc_price"><em>%1$s</em>%2$s</span>';
            break;

        case 'left_space' :
            $currency = get_woocommerce_currency();
            $format = '<span class="wc_price"><em>%1$s</em> %2$s</span>';
            break;

        case 'right_space' :
            $currency = get_woocommerce_currency();
            $format = '<span class="wc_price">%2$s<em>%1$s</em></span>';
            break;
    }

    return $format;
}

add_action('woocommerce_price_format', 'addPriceSuffix', 1, 2);



add_action('woocommerce_before_shop_loop_item_title','ct_add_custom_thumb',10);
function ct_add_custom_thumb(){
    if (ct_get_option('shop_index_prod_box_type','wc')=='clean'){
        echo woocommerce_get_product_thumbnail('shop_thumb_2');
    }
}
