<?php
if (!class_exists('ctTimelineType')) {
    require_once CT_THEME_LIB_DIR . '/types/ctTypeBase.class.php';


    /**
     * Custom type - event
     */
    class ctTimelineType extends ctTypeBase
    {


        /**
         * Slug option name
         */

        const OPTION_SLUG = 'timeline_index_slug';

        /**
         * Initializes timeline
         * @return mixed|void
         */

        public function init()
        {
            add_action('template_redirect', array($this, 'timelineContextFixer'));

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
            add_meta_box("timeline-meta", __("Timeline settings", 'ct_theme'), array($this, "timelineMeta"), "timeline", "normal", "high");
            add_action('save_post', array($this, 'saveDetails'));
        }

        /**
         * Fixes proper menu state
         */

        public function timelineContextFixer()
        {
            if (get_query_var('post_type') == 'event') {
                global $wp_query;
                $wp_query->is_home = false;
            }
            if (get_query_var('taxonomy') == 'timeline_category') {
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
                    'name' => _x('Timeline', 'post type general name', 'ct_theme'),
                    'singular_name' => _x('Timeline Item', 'post type singular name', 'ct_theme'),
                    'add_new' => _x('Add New', 'Timeline Item', 'ct_theme'),
                    'add_new_item' => __('Add New Timeline Item', 'ct_theme'),
                    'edit_item' => __('Edit Timeline Item', 'ct_theme'),
                    'new_item' => __('New Timeline Item', 'ct_theme'),
                    'view_item' => __('View Timeline Item', 'ct_theme'),
                    'search_items' => __('Search Timeline Items', 'ct_theme'),
                    'not_found' => __('No Timeline item found', 'ct_theme'),
                    'not_found_in_trash' => __('No Timeline items found in Trash', 'ct_theme'),
                    'parent_item_colon' => '',
                    'menu_name' => __('Timeline', 'ct_theme'),
                ),
                'singular_label' => __('timeline', 'ct_theme'),
                'public' => true,
                'publicly_queryable' => true,
                'exclude_from_search' => false,
                'show_ui' => true,
                'show_in_menu' => true,
                //'menu_position' => 20,
                'menu_icon' => 'dashicons-slides',
                'capability_type' => 'post',
                'hierarchical' => false,
                'supports' => array('title', 'editor', 'thumbnail', 'page-attributes'),
                'has_archive' => false,
                'rewrite' => array('slug' => $this->getPermalinkSlug(), 'with_front' => true, 'pages' => true, 'feeds' => false),
                'query_var' => false,
                'can_export' => true,
                'show_in_nav_menus' => true,
                'taxonomies' => array('post_tag')
            ));

            register_post_type('timeline', $typeData);
            $this->callHook('post_register_type');
        }

        /**
         * Returns permalink slug
         * @return string
         */

        protected function getPermalinkSlug()
        {
            // Rewriting Permalink Slug
            $permalink_slug = ct_get_option('timeline', 'timeline');
            if (empty($permalink_slug)) {
                $permalink_slug = 'timeline';
            }

            return $permalink_slug;
        }

        /**
         * Gets hook name
         * @return string
         */
        protected function getHookBaseName()
        {
            return 'ct_timeline';
        }

        /**
         * Creates taxonomies
         */

        protected function registerTaxonomies()
        {
            $data = $this->callFilter('pre_register_taxonomies', array(
                'hierarchical' => true,
                'labels' => array(
                    'name' => _x('Timeline Categories', 'taxonomy general name', 'ct_theme'),
                    'singular_name' => _x('Timeline Category', 'taxonomy singular name', 'ct_theme'),
                    'search_items' => __('Search Categories', 'ct_theme'),
                    'popular_items' => __('Popular Categories', 'ct_theme'),
                    'all_items' => __('All Categories', 'ct_theme'),
                    'parent_item' => null,
                    'parent_item_colon' => null,
                    'edit_item' => __('Edit Timeline Category', 'ct_theme'),
                    'update_item' => __('Update Timeline Category', 'ct_theme'),
                    'add_new_item' => __('Add New Timeline Category', 'ct_theme'),
                    'new_item_name' => __('New Timeline Category Name', 'ct_theme'),
                    'separate_items_with_commas' => __('Separate Timeline category with commas', 'ct_theme'),
                    'add_or_remove_items' => __('Add or remove timeline category', 'ct_theme'),
                    'choose_from_most_used' => __('Choose from the most used timeline category', 'ct_theme'),
                    'menu_name' => __('Categories', 'ct_theme'),
                ),
                'public' => false,
                'show_in_nav_menus' => false,
                'show_ui' => true,
                'show_tagcloud' => false,
                'query_var' => 'timeline_category',
                'rewrite' => false,

            ));
            register_taxonomy('timeline_category', 'timeline', $data);
            $this->callHook('post_register_taxonomies');
        }


        /**
         * Gets display method for event
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
         * Draw s timeline meta
         */

        public function timelineMeta()
        {
            global $post;
            $currentDate = getDate();
            $custom = get_post_custom($post->ID);

            $start_date = isset($custom["start_date"][0]) ? $custom["start_date"][0] : "";
            $end_date = isset($custom["end_date"][0]) ? $custom["end_date"][0] : "";
            $video_url = isset($custom["video_url"][0]) ? $custom["video_url"][0] : "";
            $map_url = isset($custom["map_url"][0]) ? $custom["map_url"][0] : "";
            $wikipedia_url = isset($custom["wikipedia_url"][0]) ? $custom["wikipedia_url"][0] : "";
            $media_caption = isset($custom["media_caption"][0]) ? $custom["media_caption"][0] : "";
            $media_credit = isset($custom["media_credit"][0]) ? $custom["media_credit"][0] : "";
            ?>


            <p>
                <label for="start_date"><?php _e('Start date', 'ct_theme') ?>: </label>
                <input type="date" id="start_date" class="regular-text" name="start_date"
                       value="<?php echo $start_date; ?>"/>
            <p class="howto"><?php _e("Enter start date (not for title element)", 'ct_theme') ?></p>

            <p>
                <label for="end_date"><?php _e('End date', 'ct_theme') ?>: </label>
                <input type="date" id="end_date" class="regular-text" name="end_date" value="<?php echo $end_date; ?>"/>
            <p class="howto"><?php _e("Enter end date (not for title element)", 'ct_theme') ?></p>

            <p>
                <label for="video_url"><?php _e('Video URL', 'ct_theme') ?>: </label>
                <input type="input" id="video_url" class="regular-text" name="video_url"
                       value="<?php echo $video_url; ?>"/>
            <p class="howto"><?php _e("Enter video URL (Vimeo or YouTube)", 'ct_theme') ?></p>

            <p>
                <label for="map_url"><?php _e('Map URL', 'ct_theme') ?>: </label>
                <input type="input" id="map_url" class="regular-text" name="map_url" value="<?php echo $map_url; ?>"/>
            <p class="howto"><?php _e("Enter link to map", 'ct_theme') ?></p>

            <p>

            <p>
                <label for="wikipedia_url"><?php _e('Wikipedia URL', 'ct_theme') ?>: </label>
                <input type="input" id="wikipedia_url" class="regular-text" name="wikipedia_url"
                       value="<?php echo $wikipedia_url; ?>"/>
            <p class="howto"><?php _e("Enter wikipedia article URL", 'ct_theme') ?></p>

            <p>
                <label for="media_caption"><?php _e('Media caption', 'ct_theme') ?>: </label>
                <input type="input" id="media_caption" class="regular-text" name="media_caption"
                       value="<?php echo $media_caption; ?>"/>
            <p class="howto"><?php _e("Enter wikipedia media caption", 'ct_theme') ?></p>

            <p>
                <label for="media_credit"><?php _e('media credit', 'ct_theme') ?>: </label>
                <input type="input" id="media_credit" class="regular-text" name="media_credit"
                       value="<?php echo $media_credit; ?>"/>
            <p class="howto"><?php _e("Enter media credit", 'ct_theme') ?></p>

        <?php


        }

        /**
         * timeline template settings
         */


        public function saveDetails()
        {
            global $post;

            $fields = array('media_caption', 'media_credit', 'wikipedia_url', 'start_date', 'end_date', 'video_url', 'map_url');
            foreach ($fields as $field) {
                if (isset($_POST[$field])) {
                    update_post_meta($post->ID, $field, $_POST[$field]);
                }
            }
        }
    }

    new ctTimelineType();
}
