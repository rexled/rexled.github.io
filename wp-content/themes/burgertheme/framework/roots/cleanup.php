<?php

/**
 * Clean up wp_head()
 *
 * Remove unnecessary <link>'s
 * Remove inline CSS used by Recent Comments widget
 * Remove inline CSS used by posts with galleries
 * Remove self-closing tag and change ''s to "'s on rel_canonical()
 */
function roots_head_cleanup()
{
	add_filter('show_recent_comments_widget_style','__return_false');
    add_filter('use_default_gallery_style', '__return_null');
}

add_action('init', 'roots_head_cleanup');

/**
 * Remove the WordPress version from RSS feeds
 */
add_filter('the_generator', '__return_false');

/**
 * Clean up language_attributes() used in <html> tag
 *
 * Change lang="en-US" to lang="en"
 * Remove dir="ltr"
 */
function roots_language_attributes()
{
    $attributes = array();
    $output = '';

    if (function_exists('is_rtl')) {
        if (is_rtl() == 'rtl') {
            $attributes[] = 'dir="rtl"';
        }
    }

    $lang = get_bloginfo('language');

    if ($lang && $lang !== 'en-US') {
        $attributes[] = "lang=\"$lang\"";
    } else {
        $attributes[] = 'lang="en"';
    }

    $output = implode(' ', $attributes);
    $output = apply_filters('roots_language_attributes', $output);

    return $output;
}

add_filter('language_attributes', 'roots_language_attributes');

/**
 * Clean up output of stylesheet <link> tags
 */
function roots_clean_style_tag($input)
{
    preg_match_all("!<link rel='stylesheet'\s?(id='[^']+')?\s+href='(.*)' type='text/css' media='(.*)' />!", $input, $matches);
    // Only display media if it's print
    $media = $matches[3][0] === 'print' ? ' media="print"' : '';

    return '<link rel="stylesheet" href="' . $matches[2][0] . '"' . $media . '>' . "\n";
}

add_filter('style_loader_tag', 'roots_clean_style_tag');

/**
 * Add and remove body_class() classes
 */
function roots_body_class($classes)
{
    // Add 'top-navbar' class if using Bootstrap's Navbar
    // Used to add styling to account for the WordPress admin bar
    if (current_theme_supports('bootstrap-top-navbar')) {
        $classes[] = 'top-navbar';
    }

    // Add post/page slug
    if (is_single() || is_page() && !is_front_page()) {
        $classes[] = basename(get_permalink());
    }

    // Remove unnecessary classes
    $home_id_class = 'page-id-' . get_option('page_on_front');
    $remove_classes = array(
            'page-template-default',
            $home_id_class
    );
    $classes = array_diff($classes, $remove_classes);

    return $classes;
}

add_filter('body_class', 'roots_body_class');

/**
 * Root relative URLs
 *
 * WordPress likes to use absolute URLs on everything - let's clean that up.
 * Inspired by http://www.456bereastreet.com/archive/201010/how_to_make_wordpress_urls_root_relative/
 *
 * You can enable/disable this feature in config.php:
 * current_theme_supports('root-relative-urls');
 *
 * @author Scott Walkinshaw <scott.walkinshaw@gmail.com>
 */
function roots_root_relative_url($input)
{
    // fix for site_url != home_url()
    if (!is_admin() && site_url() != home_url()) {
        $input = str_replace(site_url(), "", $input);
    }

    $output = preg_replace_callback(
            '!(https?://[^/|"]+)([^"]+)?!',
            create_function(
                    '$matches',
                    // If full URL is home_url("/") and this isn't a subdir install, return a slash for relative root
                    'if (isset($matches[0]) && $matches[0] === home_url("/") && str_replace("http://", "", home_url("/", "http"))==$_SERVER["HTTP_HOST"]) { return "/";' .
                    // If domain is equal to home_url("/"), then make URL relative
                    '} elseif (isset($matches[0]) && strpos($matches[0], home_url("/")) !== false) { return $matches[2];' .
                    // If domain is not equal to home_url("/"), do not make external link relative
                    '} else { return $matches[0]; };'
            ),
            $input
    );

    // detect and correct for subdir installs
    if ($subdir = parse_url(home_url(), PHP_URL_PATH)) {
        if (substr($output, 0, strlen($subdir)) == (substr($output, strlen($subdir), strlen($subdir)))) {
            $output = substr($output, strlen($subdir));
        }
    }

    return $output;
}

/**
 * Terrible workaround to remove the duplicate subfolder in the src of <script> and <link> tags
 * Example: /subfolder/subfolder/css/style.css
 */
function roots_fix_duplicate_subfolder_urls($input)
{
    $output = roots_root_relative_url($input);
    preg_match_all('!([^/]+)/([^/]+)!', $output, $matches);

    if (isset($matches[1]) && isset($matches[2]) && isset($matches[1][0]) && isset($matches[2][0])) {
        if ($matches[1][0] === $matches[2][0]) {
            $output = substr($output, strlen($matches[1][0]) + 1);
        }
    }

    return $output;
}

function roots_enable_root_relative_urls()
{
	return false;
    return !(is_admin() && in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'))) && current_theme_supports('root-relative-urls') && apply_filters('root-relative-urls', true);
}

if (roots_enable_root_relative_urls()) {
    $root_rel_filters = array(
            'bloginfo_url',
            'theme_root_uri',
            'stylesheet_directory_uri',
            'template_directory_uri',
            'plugins_url',
            'the_permalink',
            'wp_list_pages',
            'wp_list_categories',
            'wp_nav_menu',
            'the_content_more_link',
            'the_tags',
            'get_pagenum_link',
            'get_comment_link',
            'month_link',
            'day_link',
            'year_link',
            'tag_link',
            'the_author_posts_link'
    );

    add_filters($root_rel_filters, 'roots_root_relative_url');

    add_filter('script_loader_src', 'roots_fix_duplicate_subfolder_urls');
    add_filter('style_loader_src', 'roots_fix_duplicate_subfolder_urls');
}

/**
 * Wrap embedded media as suggested by Readability
 *
 * @link https://gist.github.com/965956
 * @link http://www.readability.com/publishers/guidelines#publisher
 */
function roots_embed_wrap($cache, $url, $attr = '', $post_ID = '')
{
    return '<div class="entry-content-asset">' . $cache . '</div>';
}

add_filter('embed_oembed_html', 'roots_embed_wrap', 10, 4);
add_filter('embed_googlevideo', 'roots_embed_wrap', 10, 2);

/**
 * Add class="thumbnail" to attachment items
 */
function roots_attachment_link_class($html)
{
    $postid = get_the_ID();
    $html = str_replace('<a', '<a class="thumbnail"', $html);

    return $html;
}

add_filter('wp_get_attachment_link', 'roots_attachment_link_class', 10, 1);

/**
 * Add Bootstrap thumbnail styling to images with captions
 * Use <figure> and <figcaption>
 *
 * @link http://justintadlock.com/archives/2011/07/01/captions-in-wordpress
 */
function roots_caption($output, $attr, $content)
{
    if (is_feed()) {
        return $output;
    }

    $defaults = array(
            'id' => '',
            'align' => 'alignnone',
            'width' => '',
            'caption' => ''
    );

    $attr = shortcode_atts($defaults, $attr);

    // If the width is less than 1 or there is no caption, return the content wrapped between the [caption] tags
    if ($attr['width'] < 1 || empty($attr['caption'])) {
        return $content;
    }

    // Set up the attributes for the caption <figure>
    $attributes = (!empty($attr['id']) ? ' id="' . esc_attr($attr['id']) . '"' : '');
    $attributes .= ' class="thumbnail wp-caption ' . esc_attr($attr['align']) . '"';
    $attributes .= ' style="width: ' . esc_attr($attr['width']) . 'px"';

    $output = '<figure' . $attributes . '>';
    $output .= do_shortcode($content);
    $output .= '<figcaption class="caption wp-caption-text">' . $attr['caption'] . '</figcaption>';
    $output .= '</figure>';

    return $output;
}

add_filter('img_caption_shortcode', 'roots_caption', 10, 3);


/**
 * Clean up gallery_shortcode()
 *
 * Re-create the [gallery] shortcode and use thumbnails styling from Bootstrap
 *
 * @link http://twitter.github.com/bootstrap/components.html#thumbnails
 */
function roots_gallery($attr)
{
    $post = get_post();

    static $instance = 0;
    $instance++;

    if (!empty($attr['ids'])) {
        if (empty($attr['orderby'])) {
            $attr['orderby'] = 'post__in';
        }
        $attr['include'] = $attr['ids'];
    }

    $output = apply_filters('post_gallery', '', $attr);

    if ($output != '') {
        return $output;
    }

    if (isset($attr['orderby'])) {
        $attr['orderby'] = sanitize_sql_orderby($attr['orderby']);
        if (!$attr['orderby']) {
            unset($attr['orderby']);
        }
    }

    extract(
            shortcode_atts(
                    array(
                            'order' => 'ASC',
                            'orderby' => 'menu_order ID',
                            'id' => $post->ID,
                            'itemtag' => '',
                            'icontag' => '',
                            'captiontag' => '',
                            'columns' => 3,
                            'size' => 'thumbnail',
                            'include' => '',
                            'exclude' => ''
                    ),
                    $attr
            )
    );

    $id = intval($id);

    if ($order === 'RAND') {
        $orderby = 'none';
    }

    if (!empty($include)) {
        $_attachments = get_posts(array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby));

        $attachments = array();
        foreach ($_attachments as $key => $val) {
            $attachments[$val->ID] = $_attachments[$key];
        }
    } elseif (!empty($exclude)) {
        $attachments = get_children(array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby));
    } else {
        $attachments = get_children(array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby));
    }

    if (empty($attachments)) {
        return '';
    }

    if (is_feed()) {
        $output = "\n";
        foreach ($attachments as $att_id => $attachment) {
            $output .= wp_get_attachment_link($att_id, $size, true) . "\n";
        }

        return $output;
    }

    $output = '<ul class="thumbnails gallery">';

    $i = 0;
    foreach ($attachments as $id => $attachment) {
        $link = isset($attr['link']) && 'file' == $attr['link'] ? wp_get_attachment_link($id, $size, false, false) : wp_get_attachment_link($id, $size, false, false);

        $output .= '<li>' . $link;
        if (trim($attachment->post_excerpt)) {
            $output .= '<div class="caption hidden">' . wptexturize($attachment->post_excerpt) . '</div>';
        }
        $output .= '</li>';
    }

    $output .= '</ul>';

    return $output;
}

remove_shortcode('gallery');
$prefix = "add_";
    //allow to dynamically reassign registration functions
    $call = $prefix . apply_filters('ct_roots_register_gallery', 'shortcode');
$call('gallery', 'roots_gallery');


/**
 * Remove unnecessary dashboard widgets
 *
 * @link http://www.deluxeblogtips.com/2011/01/remove-dashboard-widgets-in-wordpress.html
 */
function roots_remove_dashboard_widgets()
{
    remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal');
    remove_meta_box('dashboard_plugins', 'dashboard', 'normal');
    remove_meta_box('dashboard_primary', 'dashboard', 'normal');
    remove_meta_box('dashboard_secondary', 'dashboard', 'normal');
}

add_action('admin_init', 'roots_remove_dashboard_widgets');

/**
 * Clean up the_excerpt()
 */
function roots_excerpt_length($length)
{
    return POST_EXCERPT_LENGTH;
}

function roots_excerpt_more($more)
{
    return ' &hellip; <a href="' . get_permalink() . '">' . esc_html__('Continued', 'ct_theme') . '</a>';
}

add_filter('excerpt_length', 'roots_excerpt_length');
//add_filter('excerpt_more', 'roots_excerpt_more');

/**
 * Cleaner walker for wp_nav_menu()
 *
 * Walker_Nav_Menu (WordPress default) example output:
 *   <li id="menu-item-8" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-8"><a href="/">Home</a></li>
 *   <li id="menu-item-9" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-9"><a href="/sample-page/">Sample Page</a></l
 *
 * Roots_Nav_Walker example output:
 *   <li class="menu-home"><a href="/">Home</a></li>
 *   <li class="menu-sample-page"><a href="/sample-page/">Sample Page</a></li>
 */
class Roots_Nav_Walker extends Walker_Nav_Menu
{
    function check_current($classes)
    {
        return preg_match('/(current[-_])|active|dropdown/', $classes);
    }

    function start_lvl(&$output, $depth = 0, $args = array())
    {
        $output .= "\n<ul class=\"dropdown-menu unstyled\">\n";
    }

    function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0)
    {
        $item_html = '';
        parent::start_el($item_html, $item, $depth, $args);

        if (!is_array($item->classes)) {
            $item->classes = array();
        }

        if ($item->is_dropdown && ($depth === 0)) {
            $item_html = str_replace('<a', '<a class="dropdown-toggle" data-toggle="dropdown" data-target="#"', $item_html);
            //$item_html = str_replace('</a>', ' <b class="caret"></b></a>', $item_html);
        } elseif (in_array('divider-vertical', $item->classes)) {
            $item_html = '<li class="divider-vertical">';
        } elseif (in_array('divider', $item->classes)) {
            $item_html = '<li class="divider">';
        } elseif (in_array('nav-header', $item->classes)) {
            $item_html = '<li class="nav-header">' . $item->title;
        }
        $item_html = apply_filters('roots-menuelement-output',$item_html,$item,$depth);
        $output .= $item_html;
    }

    function display_element($element, &$children_elements, $max_depth, $depth = 0, $args, &$output)
    {
        $element->is_dropdown = !empty($children_elements[$element->ID]);

        if ($element->is_dropdown) {
            if ($depth === 0) {
                $element->classes[] = 'dropdown';
            } elseif ($depth >= 1 && $max_depth - 1 > 1) {
                $element->classes[] = 'dropdown-submenu';
            }
        }

        parent::display_element($element, $children_elements, $max_depth, $depth, $args, $output);
    }
}

/**
 * Remove the id="" on nav menu items
 * Return 'menu-slug' for nav menu classes
 */
function roots_nav_menu_css_class($classes, $item)
{
    $slug = sanitize_title($item->title);
    $classes = preg_replace('/(current(-menu-|[-_]page[-_])(item|parent|ancestor))/', is_404() ? '' : 'active', $classes);
    $classes = preg_replace('/((menu|page)[-_\w+]+)+/', '', $classes);

    $classes[] = 'menu-' . $slug;

    $classes = array_unique($classes);

    return array_filter($classes, 'is_element_empty');
}

add_filter('nav_menu_css_class', 'roots_nav_menu_css_class', 10, 2);
add_filter('nav_menu_item_id', '__return_null');

/**
 * Clean up wp_nav_menu_args
 *
 * Remove the container
 * Use Roots_Nav_Walker() by default
 */
function roots_nav_menu_args($args = '')
{
    $roots_nav_menu_args['container'] = false;

    if (!$args['items_wrap']) {
        $roots_nav_menu_args['items_wrap'] = '<ul class="%2$s">%3$s</ul>';
    }

    if (current_theme_supports('bootstrap-top-navbar')) {
        $roots_nav_menu_args['depth'] = ct_get_option('roots_navbar_depth', 99);
    }

    if (!$args['walker']) {
        $roots_nav_menu_args['walker'] = new Roots_Nav_Walker();
    }

    return array_merge($args, $roots_nav_menu_args);
}

add_filter('wp_nav_menu_args', 'roots_nav_menu_args');

/**
 * Remove unnecessary self-closing tags
 */
function roots_remove_self_closing_tags($input)
{
    return str_replace(' />', '>', $input);
}

add_filter('get_avatar', 'roots_remove_self_closing_tags'); // <img />
add_filter('comment_id_fields', 'roots_remove_self_closing_tags'); // <input />
add_filter('post_thumbnail_html', 'roots_remove_self_closing_tags'); // <img />

/**
 * Don't return the default description in the RSS feed if it hasn't been changed
 */
function roots_remove_default_description($bloginfo)
{
    $default_tagline = 'Just another WordPress site';

    return ($bloginfo === $default_tagline) ? '' : $bloginfo;
}

add_filter('get_bloginfo_rss', 'roots_remove_default_description');

/**
 * Allow more tags in TinyMCE including <iframe> and <script>
 */
function roots_change_mce_options($options)
{
    $ext = 'pre[id|name|class|style],iframe[align|longdesc|name|width|height|frameborder|scrolling|marginheight|marginwidth|src],script[charset|defer|language|src|type]';

    if (isset($initArray['extended_valid_elements'])) {
        $options['extended_valid_elements'] .= ',' . $ext;
    } else {
        $options['extended_valid_elements'] = $ext;
    }

    return $options;
}

add_filter('tiny_mce_before_init', 'roots_change_mce_options');

/**
 * Add additional classes onto widgets
 *
 * @link http://wordpress.org/support/topic/how-to-first-and-last-css-classes-for-sidebar-widgets
 */
function roots_widget_first_last_classes($params)
{
    global $my_widget_num;

    $this_id = $params[0]['id'];
    $arr_registered_widgets = wp_get_sidebars_widgets();

    if (!$my_widget_num) {
        $my_widget_num = array();
    }

    if (!isset($arr_registered_widgets[$this_id]) || !is_array($arr_registered_widgets[$this_id])) {
        return $params;
    }

    if (isset($my_widget_num[$this_id])) {
        $my_widget_num[$this_id]++;
    } else {
        $my_widget_num[$this_id] = 1;
    }

    $class = 'class="widget-' . $my_widget_num[$this_id] . ' ';

    if ($my_widget_num[$this_id] == 1) {
        $class .= 'widget-first ';
    } elseif ($my_widget_num[$this_id] == count($arr_registered_widgets[$this_id])) {
        $class .= 'widget-last ';
    }

    $params[0]['before_widget'] = preg_replace('/class=\"/', "$class", $params[0]['before_widget'], 1);

    return $params;
}

add_filter('dynamic_sidebar_params', 'roots_widget_first_last_classes');

/**
 * Redirects search results from /?s=query to /search/query/, converts %20 to +
 *
 * @link http://txfx.net/wordpress-plugins/nice-search/
 */
function roots_nice_search_redirect()
{
    global $wp_rewrite;
    if (!isset($wp_rewrite) || !is_object($wp_rewrite) || !$wp_rewrite->using_permalinks()) {
        return;
    }
    $search_base = $wp_rewrite->search_base;
    if (is_search() && !is_admin() && apply_filters('roots-nice-search',true) && strpos($_SERVER['REQUEST_URI'], "/{$search_base}/") === false) {
        wp_redirect(home_url("/{$search_base}/" . urlencode(get_query_var('s'))));
        exit();
    }
}

add_action('template_redirect', 'roots_nice_search_redirect');

/**
 * Fix for get_search_query() returning +'s between search terms
 */
function roots_search_query($escaped = true)
{
    $query = apply_filters('roots_search_query', get_query_var('s'));

    if ($escaped) {
        $query = esc_attr($query);
    }

    return urldecode($query);
}

add_filter('get_search_query', 'roots_search_query');

/**
 * Fix for empty search queries redirecting to home page
 *
 * @link http://wordpress.org/support/topic/blank-search-sends-you-to-the-homepage#post-1772565
 * @link http://core.trac.wordpress.org/ticket/11330
 */
function roots_request_filter($query_vars)
{
    if (isset($_GET['s']) && empty($_GET['s'])) {
        $query_vars['s'] = ' ';
    }

    return $query_vars;
}

add_filter('request', 'roots_request_filter');

/**
 * Tell WordPress to use searchform.php from the templates/ directory
 */
function roots_get_search_form($form)
{
    ob_start();
    locate_template('/templates/searchform.php', true, false);
    $form = ob_get_clean();

    return $form;
}

add_filter('get_search_form', 'roots_get_search_form');
