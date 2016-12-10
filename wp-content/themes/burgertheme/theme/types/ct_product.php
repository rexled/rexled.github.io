<?php
if (!class_exists('ctProductType')) {
    require_once CT_THEME_LIB_DIR . '/types/ctTypeBase.class.php';


    /**
     * Custom type - product
     */
    class ctProductType extends ctTypeBase
    {


        /**
         * Slug option name
         */

        const OPTION_SLUG = 'ct_product_index_slug';

        /**
         * Initializes products
         * @return mixed|void
         */

        public function init()
        {
            add_action('template_redirect', array($this, 'productContextFixer'));

            $this->registerType();
            $this->registerTaxonomies();

            add_action("admin_init", array($this, "addMetaBox"));

            /** @var $NHP_Options NHP_Options */
            global $NHP_Options;
            //add options listener for license
            add_action('nhp-opts-options-validate-' . $NHP_Options->args['opt_name'], array($this, 'handleSlugOptionSaved'));
        }

        /**
         * Adds meta box
         */

        public function addMetaBox()
        {
            add_meta_box("ct-product-meta", __("Menu item settings", 'ct_theme'), array($this, "ctProductMeta"), "ct_product", "normal", "high");
            add_action('save_post', array($this, 'saveDetails'));
        }

        /**
         * Fixes proper menu state
         */

        public function productContextFixer()
        {
            if (get_query_var('post_type') == 'ct_product') {
                global $wp_query;
                $wp_query->is_home = false;
            }
            if (get_query_var('taxonomy') == 'ct_product_category') {
                global $wp_query;
                $wp_query->is_404 = true;
                $wp_query->is_tax = false;
                $wp_query->is_archive = false;
            }
        }

        /**
         * Register type
         */

        protected function registerType()
        {
            $typeData = $this->callFilter('pre_register_type', array(
                'labels' => array(
                    'name' => _x('Menu', 'post type general name', 'ct_theme'),
                    'singular_name' => _x('Menu Item', 'post type singular name', 'ct_theme'),
                    'add_new' => _x('Add New', 'menu items', 'ct_theme'),
                    'add_new_item' => __('Add New Menu Item', 'ct_theme'),
                    'edit_item' => __('Edit Menu Item', 'ct_theme'),
                    'new_item' => __('New Menu Item', 'ct_theme'),
                    'view_item' => __('View Menu Item', 'ct_theme'),
                    'search_items' => __('Search Menu Items', 'ct_theme'),
                    'not_found' => __('No Menu item found', 'ct_theme'),
                    'not_found_in_trash' => __('No Menu items found in Trash', 'ct_theme'),
                    'parent_item_colon' => '',
                    'menu_name' => __('Menu', 'ct_theme'),
                ),
                'singular_label' => __('Menu item', 'ct_theme'),
                'public' => true,
                'publicly_queryable' => true,
                'exclude_from_search' => false,
                'show_ui' => true,
                'show_in_menu' => true,
                //'menu_position' => 20,
                'menu_icon' => 'dashicons-clipboard',
                'capability_type' => 'post',
                'hierarchical' => false,
                'supports' => array('title', 'editor', 'thumbnail', 'page-attributes'),
                'has_archive' => false,
                'rewrite' => array('slug' => 'menu', 'with_front' => true, 'pages' => true, 'feeds' => false),
                'query_var' => false,
                'can_export' => true,
                'show_in_nav_menus' => true,
                'taxonomies' => array('post_tag')
            ));


            register_post_type('ct_product', $typeData);
            $this->callHook('post_register_type');

        }

        /**
         * Returns permalink slug
         * @return string
         */

        protected function getPermalinkSlug()
        {
            // Rewriting Permalink Slug
            $permalink_slug = ct_get_option('ct_product', 'ct_product');
            if (empty($permalink_slug)) {
                $permalink_slug = 'menu';
            }

            return $permalink_slug;
        }

        /**
         * Gets hook name
         * @return string
         */
        protected function getHookBaseName()
        {
            return 'ct_product';
        }

        /**
         * Creates taxonomies
         */

        protected function registerTaxonomies()
        {
            $data = $this->callFilter('pre_register_taxonomies', array(
                'hierarchical' => true,
                'labels' => array(
                    'name' => _x('Menu Categories', 'taxonomy general name', 'ct_theme'),
                    'singular_name' => _x('Menu Category', 'taxonomy singular name', 'ct_theme'),
                    'search_items' => __('Search Menu Categories', 'ct_theme'),
                    'popular_items' => __('Popular Menu Categories', 'ct_theme'),
                    'all_items' => __('All Menu Categories', 'ct_theme'),
                    'parent_item' => null,
                    'parent_item_colon' => null,
                    'edit_item' => __('Edit Menu Category', 'ct_theme'),
                    'update_item' => __('Update Menu Category', 'ct_theme'),
                    'add_new_item' => __('Add New Menu Category', 'ct_theme'),
                    'new_item_name' => __('New Menu Category Name', 'ct_theme'),
                    'separate_items_with_commas' => __('Separate Menu category with commas', 'ct_theme'),
                    'add_or_remove_items' => __('Add or remove Menu category', 'ct_theme'),
                    'choose_from_most_used' => __('Choose from the most used Menu category', 'ct_theme'),
                    'menu_name' => __('Menu Categories', 'ct_theme'),
                ),
                'public' => false,
                'show_in_nav_menus' => false,
                'show_ui' => true,
                'show_tagcloud' => false,
                'query_var' => 'ct_product_category',
                'rewrite' => false,

            ));


            register_taxonomy('ct_product_category', 'ct_product', $data);

            $this->callHook('post_register_taxonomies');


        }


        /**
         * Gets display method for ct_product
         * @param array $meta - post meta
         * @return null|string
         */
        public static function getMethodFromMeta($meta)
        {
            $method = isset($meta['display_method']) ? $meta['display_method'][0] : null;
            if (!$method) {
                $method = isset($meta['video'][0]) && trim($meta['video'][0]) ? 'video' : 'image';
            }
            return $method;
        }

        /**
         * Handles rebuild
         */

        public function handleSlugOptionSaved($newValues)
        {
            $currentSlug = $this->getPermalinkSlug();
            //rebuild rewrite if new slug
            if (isset($newValues[self::OPTION_SLUG]) && ($currentSlug != $newValues[self::OPTION_SLUG])) {
                $this->callHook('pre_slug_option_saved', array('current_slug' => $currentSlug, 'new_slug' => $newValues[self::OPTION_SLUG]));

                //clean rewrite to refresh it
                delete_option('rewrite_rules');
            }
        }


        /**
         * Draw s product meta
         */

        public function ctProductMeta()
        {
            global $post;
            $custom = get_post_custom($post->ID);


            $price = isset($custom["price"][0]) ? $custom["price"][0] : "";
            $currency = isset($custom["currency"][0]) ? $custom["currency"][0] : ct_get_option('products_index_currency', '$');
            $postscript = isset($custom["postscript"][0]) ? $custom["postscript"][0] : "";



            ?>
            <p>
                <label for="price"><?php _e('Price', 'ct_theme') ?>: </label>
                <input id="price" class="regular-text" name="price" value="<?php echo $price; ?>"/>
            <p class="howto"><?php _e("Product price", 'ct_theme') ?></p>

            <p>
                <label for="currency"><?php _e('Currency', 'ct_theme') ?>: </label>
                <input id="currency" class="regular-text" name="currency" value="<?php echo $currency; ?>"/>
            <p class="howto"><?php _e("Product currency. Default value can be changed in Appearance - Theme Options - Products tab", 'ct_theme') ?></p>

            <p>
                <label for="postscript"><?php _e('Postscript', 'ct_theme') ?>: </label>
                <input id="postscript" class="regular-text" name="postscript" value="<?php echo $postscript; ?>"/>
            <p class="howto"><?php _e("Smaller text below product description", 'ct_theme') ?></p>



        <?php
        }

        /**
         * product template settings
         */

        public function ctProductTemplateMeta()
        {
            global $post;
            $custom = get_post_custom($post->ID);

        }


        public function saveDetails()
        {
            global $post;

            $fields = array('price', 'currency', 'postscript');
            foreach ($fields as $field) {
                if (isset($_POST[$field])) {
                    update_post_meta($post->ID, $field, $_POST[$field]);
                }
            }
        }
    }

    new ctProductType();
}