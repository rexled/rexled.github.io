<?php
/**
 * Helper functions for theme
 */


/**
 * second product featured image
 */
if ( class_exists( 'kdMultipleFeaturedImages' ) ) {

	$args = array(
		'id'        => 'featured-image-product-2',
		'post_type' => 'product', // Set this to post or page
		'labels'    => array(
			'name'   => __( 'Product thumbnail', 'ct_theme' ),
			'set'    => __( 'Set product thumbnail', 'ct_theme' ),
			'remove' => __( 'Remove product thumbnail', 'ct_theme' ),
			'use'    => __( 'Use as product thumbnail', 'ct_theme' ),
		)
	);

	new kdMultipleFeaturedImages( $args );
}

/**
 * second timeline featured image
 */
if (class_exists('kdMultipleFeaturedImages')) {

    $args = array(
        'id' => 'featured-image-timeline-2',
        'post_type' => 'timeline', // Set this to post or page
        'labels' => array(
            'name' => __('Timeline thumbnail', 'ct_theme'),
            'set' => __('Set Timeline thumbnail', 'ct_theme'),
            'remove' => __('Remove Timeline thumbnail', 'ct_theme'),
            'use' => __('Use as Timeline thumbnail', 'ct_theme'),
        )
    );

    new kdMultipleFeaturedImages($args);
}


if (!function_exists('ct_timeline_featured_image2_src')) {
    /**
     * Returns product image
     * @param $id
     * @param string $name
     * @param string $size
     * @return string
     */

    function ct_timeline_featured_image2_src($id, $name = 'featured-image-timeline-2', $size = 'timeline_thumbnail')
    {
        $imageUrl = '';
        if (class_exists('kdMultipleFeaturedImages')) {

            $imageUrl = kd_mfi_get_featured_image_url($name, 'timeline', $size, $id);
        }

        if ($imageUrl == '') {
            $imageUrl = ct_get_feature_image_src($id, $size);
        }

        return $imageUrl;
    }
}




if ( ! function_exists( 'ct_product_featured_image2_src' ) ) {
	/**
	 * Returns product image
	 *
	 * @param $id
	 * @param string $name
	 * @param string $size
	 *
	 * @return stringt
	 */

	function ct_product_featured_image2_src( $id, $name = 'featured-image-product-2', $size = 'product_thumb' ) {
		$imageUrl = '';
		if ( class_exists( 'kdMultipleFeaturedImages' ) ) {

			$imageUrl = kd_mfi_get_featured_image_url( $name, 'product', $size, $id );

		}

		if ( $imageUrl == '' ) {
			$imageUrl = ct_get_feature_image_src( $id, $size );
		}

		return $imageUrl;
	}
}

if ( ! function_exists( 'ct_child_theme_less' ) ) {
	/**
	 * Enqueue less file if style.css not compiled
	 */
	function ct_child_theme_less() {
		if ( is_child_theme() ) {
			$theme_data = wp_get_theme();
			$root       = $theme_data->get_theme_root_uri();

			$f = explode( '/', $root );
			foreach ( $f as $k => $e ) {
				if ( $e != 'wp-content' ) {
					unset( $f[ $k ] );
				} else {
					break;
				}
			}
			$f    = '/' . implode( '/', $f );
			$main = ABSPATH . $f . '/' . $theme_data->get_stylesheet() . '/assets/css/style.css';
			if ( ! file_exists( $main ) ) {
				//compile less file
				wp_enqueue_style( 'ct-child-theme', $theme_data->get_stylesheet_directory_uri() . '/ct/css.php?less=' . $f . '/' . $theme_data->get_stylesheet() . '/assets/less/style.less', array( 'ct_theme' ), null );
			}
		}
	}
}

add_action( 'wp_enqueue_scripts', 'ct_child_theme_less' );

/**
 * Enqueue scripts
 */
if ( ! function_exists( 'ct_theme_scripts' ) ) {
	function ct_theme_scripts() {

		wp_register_style( 'ct-awesome-social', CT_THEME_ASSETS . '/css/font-awesome-social.css' );
		wp_enqueue_style( 'ct-awesome-social' );

		wp_register_style( 'ct-awesome', CT_THEME_ASSETS . '/css/font-awesome.css' );
		wp_enqueue_style( 'ct-awesome' );

		wp_register_script( 'ct-prettyPhoto', CT_THEME_ASSETS . '/js/prettyPhoto/js/jquery.prettyPhoto.js', array( 'jquery' ), false, true );
		wp_enqueue_script( 'ct-prettyPhoto' );

		wp_register_script( 'ct-sticky', CT_THEME_ASSETS . '/js/jquery.sticky.js', array( 'jquery' ), false, true );
		wp_enqueue_script( 'ct-sticky' );

		wp_register_script( 'ct-placeholder', CT_THEME_ASSETS . '/js/jquery.placeholder.js', array( 'jquery' ), false, true );
		wp_enqueue_script( 'ct-placeholder' );

		wp_register_script( 'ct-bxslider', CT_THEME_ASSETS . '/js/bxslider/jquery.bxslider.js', array( 'jquery' ), false, true );
		wp_enqueue_script( 'ct-bxslider' );

		wp_register_script( 'ct-queryloader2', CT_THEME_ASSETS . '/js/jquery.queryloader2.min.js', array( 'jquery' ), false, true );
		wp_enqueue_script( 'ct-queryloader2' );

		wp_register_script( 'ct-magnific-popup', CT_THEME_ASSETS . '/js/jquery.magnific-popup.min.js', array( 'jquery' ), false, true );
		wp_enqueue_script( 'ct-magnific-popup' );

		wp_register_script( 'ct-stellar', CT_THEME_ASSETS . '/js/jquery.stellar.min.js', array( 'jquery' ), false, true );
		wp_enqueue_script( 'ct-stellar' );


	}
}

add_action( 'wp_enqueue_scripts', 'ct_theme_scripts' );

/**
 * FoodTruck Locator - setup random value on theme activation
 */

function ct_foodtruck_locator_init() {
	if ( ct_get_option( 'general_location_secret' ) == '' ) {
		ct_set_option( 'general_location_secret', md5( time() . rand( 100, 10000 ) ) );
	}
}

add_action( 'after_switch_theme', 'ct_foodtruck_locator_init', 10, 2 );


/**
 * Theme activation
 */

function theme_activation() {
	$theme_data = wp_get_theme();
	//add crop option
	if ( ! get_option( "medium_crop" ) ) {
		add_option( "medium_crop", "1" );
	} else {
		update_option( "medium_crop", "1" );
	}

	//add current version
	add_option( 'foodtruck_theme_version', $theme_data->get( 'Version' ) );
}

theme_activation();

/**
 * returns video html for video format post
 */
if ( ! function_exists( 'ct_post_video' ) ) {
	function ct_post_video( $postid, $width = 500, $height = 300 ) {
		$m4v    = get_post_meta( $postid, 'videoM4V', true );
		$ogv    = get_post_meta( $postid, 'videoOGV', true );
		$direct = get_post_meta( $postid, 'videoDirect', true );
		echo do_shortcode( '[video  width="' . $width . '" height="' . $height . '" link="' . $direct . '" m4v="' . $m4v . '" ogv="' . $ogv . '"]' );
	}
}

/**
 * returns audio html for audio format post
 */
if ( ! function_exists( 'ct_post_audio' ) ) {
	function ct_post_audio( $postid, $width = 500, $height = 300 ) {
		$mp3    = get_post_meta( $postid, 'audioMP3', true );
		$ogg    = get_post_meta( $postid, 'audioOGA', true );
		$poster = get_post_meta( $postid, 'audioPoster', true );
		$height = get_post_meta( $postid, 'audioPosterHeight', true );

		// Calc $height for small images; large will return same value
		$height = $height * $width / 580;

		echo do_shortcode( '[audio width="' . $width . '" mp3="' . $mp3 . '" ogg="' . $ogg . '" poster="' . $poster . '" posterheight="' . $height . '"]' );
	}
}

if ( ! function_exists( 'ct_get_the_post_audio' ) ) {
	function ct_get_the_post_audio( $postid, $width = 500, $height = 300 ) {
		$mp3               = get_post_meta( $postid, 'audioMP3', true );
		$ogg               = get_post_meta( $postid, 'audioOGA', true );
		$poster            = get_post_meta( $postid, 'audioPoster', true );
		$audioPosterHeight = get_post_meta( $postid, 'audioPosterHeight', true );

		// Calc $height for small images; large will return same value
		//$height = $height * $width / 580;

		return do_shortcode( '[audio height="' . $height . '" width="' . $width . '" mp3="' . $mp3 . '" ogg="' . $ogg . '" poster="' . $poster . '" posterheight="' . $audioPosterHeight . '"]' );
	}
}


/**
 * show single post title?
 */
if ( ! function_exists( 'ct_get_single_post_title' ) ) {
	function ct_get_single_post_title( $postType = 'page' ) {
		$show = get_post_meta( get_post() ? get_the_ID() : null, 'show_title', true );
		if ( $show == 'global' || $show == '' ) {
			if ( $postType == 'page' && ct_get_option( 'pages_single_show_title', 1 ) ) {
				return get_the_title();
			}
			if ( $postType == 'post' && ct_get_option( 'posts_single_page_title', '' ) ) {
				return ct_get_option( 'posts_single_page_title', '' );
			}
			if ( $postType == 'portfolio' && ct_get_option( 'portfolio_single_page_title', '' ) ) {
				return ct_get_option( 'portfolio_single_page_title', '' );
			}
			if ( $postType == 'event' && ct_get_option( 'event_single_page_title', '' ) ) {
				return ct_get_option( 'event_single_page_title', '' );
			}

		}
		if ( $show == "yes" ) {
			if ( $postType == 'post' && ct_get_option( 'posts_single_page_title', '' ) ) {
				return ct_get_option( 'posts_single_page_title', '' );
			}
			if ( $postType == 'portfolio' && ct_get_option( 'portfolio_single_page_title', '' ) ) {
				return ct_get_option( 'portfolio_single_page_title', '' );
			}
			if ( $postType == 'event' && ct_get_option( 'event_single_page_title', '' ) ) {
				return ct_get_option( 'event_single_page_title', '' );
			}
		}

		return $show == "yes" ? get_the_title() : '';
	}
}

/**
 * single post/page subtitle?
 */
if ( ! function_exists( 'ct_get_single_post_subtitle' ) ) {
	function ct_get_single_post_subtitle( $postType = 'page' ) {
		$subtitle = get_post_meta( get_post() ? get_the_ID() : null, 'subtitle', true );

		return $subtitle;
	}
}

/**
 * show single post breadcrumbs?
 */
if ( ! function_exists( 'ct_show_single_post_breadcrumbs' ) ) {
	function ct_show_single_post_breadcrumbs( $postType = 'page' ) {
		$show = get_post_meta( get_post() ? get_the_ID() : null, 'show_breadcrumbs', true );
		if ( $show == 'global' || $show == '' ) {
			if ( $postType == 'page' ) {
				return ct_get_option( 'pages_single_show_breadcrumbs', 1 );
			}
			if ( $postType == 'post' ) {
				return ct_get_option( 'posts_single_show_breadcrumbs', 1 );
			}
			if ( $postType == 'portfolio' ) {
				return ct_get_option( 'portfolio_single_show_breadcrumbs', 1 );
			}
			if ( $postType == 'event' ) {
				return ct_get_option( 'event_single_show_breadcrumbs', 1 );
			}
		}

		return $show == "yes";
	}
}

/**
 * show index post title?
 */
if ( ! function_exists( 'ct_get_index_post_title' ) ) {
	function ct_get_index_post_title( $postType = 'page' ) {
		$show = get_post_meta( get_post() ? get_the_ID() : null, 'show_title', true );
		if ( is_search() ) {
			return __( 'Search results', 'ct_theme' );
		}
		if ( $show == 'global' || $show == '' ) {
			if ( $postType == 'post' && ct_get_option( 'posts_index_show_p_title', 1 ) ) {
				$id   = ct_get_option( 'posts_index_page' );
				$page = get_post( $id );

				return $page ? $page->post_title : '';
			}
			if ( $postType == 'portfolio' && ct_get_option( 'portfolio_index_show_p_title', 1 ) ) {
				$id   = ct_get_option( 'portfolio_index_page' );
				$page = get_post( $id );

				return $page ? $page->post_title : '';
			}
			if ( $postType == 'faq' && ct_get_option( 'faq_index_show_title', 1 ) ) {
				$id   = ct_get_option( 'faq_index_page' );
				$page = get_post( $id );

				return $page ? $page->post_title : '';
			}
		}

		return $show == "yes" ? get_the_title() : '';
	}
}

/**
 * single post/page subtitle?
 */
if ( ! function_exists( 'ct_get_index_post_subtitle' ) ) {
	function ct_get_index_post_subtitle( $postType = 'page' ) {
		if ( is_search() ) {
			return '';
		}
		if ( $postType == 'post' && ct_get_option( 'posts_index_show_p_title', 1 ) ) {
			$id       = ct_get_option( 'posts_index_page' );
			$subtitle = $id ? get_post_meta( $id, 'subtitle', true ) : '';

			return $subtitle;
		}

		$subtitle = get_post_meta( get_post() ? get_the_ID() : null, 'subtitle', true );

		return $subtitle;
	}
}


/**
 * show index post breadcrumbs?
 */
if ( ! function_exists( 'ct_show_index_post_breadcrumbs' ) ) {
	function ct_show_index_post_breadcrumbs( $postType = 'page' ) {
		$show = get_post_meta( get_post() ? get_the_ID() : null, 'show_breadcrumbs', true );
		if ( $show == 'global' || $show == '' ) {
			if ( $postType == 'post' ) {
				return ct_get_option( 'posts_index_show_breadcrumbs', 1 );
			}
			if ( $postType == 'portfolio' ) {
				return ct_get_option( 'portfolio_index_show_breadcrumbs', 1 );
			}
			if ( $postType == 'faq' ) {
				return ct_get_option( 'faq_index_show_breadcrumbs', 1 );
			}
		}

		return $show == "yes";
	}
}


/**
 * add menu shadow ?
 */
if ( ! function_exists( 'ct_add_menu_shadow' ) ) {
	function ct_add_menu_shadow() {
		return ct_get_option( 'style_menu_shadow', 'no' ) == 'yes';
	}
}


/**
 * slider code
 */
if ( ! function_exists( 'ct_slider_code' ) ) {
	function ct_slider_code() {
		if ( is_home() ) {
			$id     = ct_get_option( 'posts_index_page' );
			$slider = $id ? get_post_meta( $id, 'slider', true ) : '';
		} else {
			$slider = get_post_meta( get_post() ? get_the_ID() : null, 'slider', true );
		}

		return $slider;
	}
}

/**
 * image size for posts
 */
if ( ! function_exists( 'ct_show_single_post_image_size' ) ) {
	function ct_show_single_post_image_size() {
		$show = get_post_meta( get_post() ? get_the_ID() : null, 'image_size', true );
		if ( $show == 'global' || $show == '' ) {
			return ct_get_option( 'post_featured_image_type', 'full' );
		}

		return $show;
	}
}

/**
 * socials code
 */
if ( ! function_exists( 'ct_socials_code' ) ) {
	function ct_socials_code( $size = null, $tooltip_placement = null, $label = false, $show_title_as = 'header' ) {
		$socials = array(
			'bitbucket',
			'dribbble',
			'dropbox',
			'facebook',
			'flickr',
			'foursquare',
			'github',
			'gittip',
			'google',
			'instagram',
			'linkedin',
			'pinterest',
			'Renren',
			'rss',
			'skype',
			'stack_exchange',
			'stack_overf',
			'tumblr',
			'twitter',
			'vimeo',
			'vkontakte',
			'Weibo',
			'xing',
			'yelp',
			'youtube',
		);

		if ( $show_title_as == 'header' || $show_title_as == 'label' ) {
			$show_title_as = 'show_title_as="' . $show_title_as . '"';
		} else {
			$show_title_as = '';
		}


		$header = ( $label == true && ct_get_option( 'socials_label', '' ) != '' ) ? 'header="' . ct_get_option( 'socials_label', '' ) . '"' : '';
		$params = $size ? 'size="' . $size . '" ' : '';
		$params .= $tooltip_placement ? 'tooltip_placement="' . $tooltip_placement . '" ' : '';
		foreach ( $socials as $key ) {

			if ( ct_get_option( $key ) ) {
				$value = ct_get_option( $key );
				$value = ( $key == 'rss' && ct_get_option( $key ) == '1' ) ? 'yes' : $value;
				$value = ( $key == 'rss' && ct_get_option( $key ) == '0' ) ? 'no' : $value;
				$params .= $key . '="' . ct_get_option( $key ) . '" ';
			}
		}

		$socials = '[socials ' . $params . ' ' . $header . ' ' . $show_title_as . ']';

		return $socials;
	}
}


if ( ! function_exists( 'ct_get_post_short_month_name' ) ) {
	function ct_get_post_short_month_name() {
		//$time             = mktime( 0, 0, 0, get_the_time( 'm' ) );
		//$short_month_name = strftime( "%b", $time );

		/*
		 * translated date fix
		 */
		$timestamp = get_the_time( 'U' );
		return date_i18n('M', $timestamp);
	}
}

if ( ! function_exists( 'ct_get_post_day' ) ) {
	function ct_get_post_day() {
		$day = get_the_time( 'd' );

		return $day;
	}
}

if ( ! function_exists( 'ct_get_comments_count' ) ) {
	function ct_get_comments_count() {
		$commentsCount = wp_count_comments( get_the_ID() )->approved;

		return $commentsCount;
	}
}

if ( ! function_exists( 'ct_get_blog_item_title' ) ) {
	function ct_get_blog_item_title() {
		$postTitle = get_the_title();
		$custom    = get_post_custom( get_the_ID() );

		if ( ! isset( $custom['show_title'] ) || $custom['show_title'][0] == 'global' ) {
			if ( ct_get_option( "posts_single_show_title", 1 ) ) {
				return $postTitle;
			} else {
				return '';
			}
		}

		if ( $custom['show_title'][0] == 'yes' ) {
			return $postTitle;
		} else if ( $custom['show_title'][0] == 'no' ) {
			return '';
		}

		return '';
	}
}

if ( ! function_exists( 'new_excerpt_more' ) ) {
	function new_excerpt_more( $more ) {
		return '';
	}
}
add_filter( 'excerpt_more', 'new_excerpt_more' );


if ( ! function_exists( 'ct_is_location_contains_menu' ) ) {
	function ct_is_location_contains_menu( $location = null ) {
		$menus = ( get_nav_menu_locations() );

		return isset( $menus[ $location ] ) && is_object( wp_get_nav_menu_object( $menus[ $location ] ) );
	}
}


if ( ! function_exists( 'ct_is_browser_type' ) ) {
	function ct_is_browser_type( $type = null ) {
		$user_agent = strtolower( $_SERVER['HTTP_USER_AGENT'] );
		if ( $type == 'bot' ) {
			if ( preg_match( "/googlebot|adsbot|yahooseeker|yahoobot|msnbot|watchmouse|pingdom\.com|feedfetcher-google/", $user_agent ) ) {
				return true;
			}
		} else if ( $type == 'browser' ) {
			if ( preg_match( "/mozilla\/|opera\//", $user_agent ) ) {
				return true;
			}
		} else if ( $type == 'mobile' ) {
			if ( preg_match( "/phone|iphone|itouch|ipod|symbian|android|htc_|htc-|palmos|blackberry|opera mini|iemobile|windows ce|nokia|fennec|hiptop|kindle|mot |mot-|webos\/|samsung|sonyericsson|^sie-|nintendo/", $user_agent ) ) {
				return true;
			} else if ( preg_match( "/mobile|pda;|avantgo|eudoraweb|minimo|netfront|brew|teleca|lg;|lge |wap;| wap /", $user_agent ) ) {
				return true;
			}
		}

		return false;
	}
}

if ( ! function_exists( 'ct_shop_promo_text' ) ) {
	function ct_shop_promo_text( $location = null ) {

		if ( $location == 'single' || $location == 'index' ) {
			return do_shortcode( '<div class="inner">[title_box title = "' . ct_get_option( 'shop_' . $location . '_bottom_promo_text_title', '' ) . '" subtitle = "' . ct_get_option( 'shop_' . $location . '_bottom_promo_text_subtitle', '' ) . '" class = "' . ct_get_option( 'shop_' . $location . '_bottom_promo_text_class', '' ) . '"]</div>' );

		} else {
			return '';
		}
	}
}


print apply_filters( 'taxonomy-images-list-the-terms', '', array(
	'image_size'   => 'detail',
	'taxonomy'     => 'pa_spices',
	'after'        => '</div>',
	'after_image'  => '</span>',
	'before'       => '<div class="icon">',
	'before_image' => '<span>',
) );

/*
function ct_load_custom_wc_admin_attributes(){
    include_once( __DIR__ . '/plugin/woocommerce/admin/ct-custom-class-wc-admin-attributes.php' );
}

ct_load_custom_wc_admin_attributes();
*/

//add_action( 'woocommerce', 'ct_load_custom_wc_admin_attributes' );

require_once dirname( __FILE__ ) . '/plugin/woocommerce/woocommerce.php';

if ( ! function_exists( 'ct_get_excerpt_by_id' ) ) {
	function ct_get_excerpt_by_id( $post_id ) {
		$the_post       = get_post( $post_id );
		$the_excerpt    = $the_post->post_content;
		$excerpt_length = 40;
		$the_excerpt    = strip_tags( strip_shortcodes( $the_excerpt ) );
		$words          = explode( ' ', $the_excerpt, $excerpt_length + 1 );

		if ( count( $words ) > $excerpt_length ) :
			array_pop( $words );
			array_push( $words, '&#8230;' );
			$the_excerpt = implode( ' ', $words );
		endif;

		$the_excerpt = '<p>' . $the_excerpt . '</p>';

		return $the_excerpt;
	}
}

if ( ! function_exists( 'ct_import_demo_blocks' ) ) {

	/**
	 * Blocks names
	 *
	 * @param $dirs
	 *
	 * @return array
	 */

	function ct_import_demo_blocks( $blocks, $dirs ) {
		$logoBase = CT_THEME_ASSETS . '/shortcode/1click';

		$groups = array();
		foreach ( $dirs as $d ) {
			$name = basename( $d );
			$ex   = explode( '-', $name );
			if ( ! isset( $ex[1] ) ) {
				continue;
			}

			if ( ! isset( $groups[ $ex[1] ] ) ) {
				$groups[ $ex[1] ] = array(
					'name'    => ucfirst( $ex[1] ),
					'logo'    => $logoBase . '/' . $ex[1] . '.png',
					'buttons' => array()
				);
			}

			$groups[ $ex[1] ]['buttons'][] = array( 'dir' => $name, 'label' => $ex[0] );
		}

		return array_merge( $blocks, $groups );
	}

}
add_filter( 'ct_import.options.demos.blocks', 'ct_import_demo_blocks', 10, 2 );



//theme has been renamed so we try to automatically import required stuff
if ( ! get_option( 'burger_new_name_migration', 0 ) ) {
	$newname     = 'burgertheme';
	$style       = get_stylesheet();
	$previousKey = str_replace( $newname, 'burger', $style );

	//store previous value for safety
	update_option( 'theme_mods_' . $style . '_backup', get_option( 'theme_mods_' . $style ) );

	if ( $preMod = get_option( 'theme_mods_' . $previousKey ) ) {
		update_option( 'theme_mods_' . $style, $preMod );
	}
	update_option( 'burger_new_name_migration', 1 );
}