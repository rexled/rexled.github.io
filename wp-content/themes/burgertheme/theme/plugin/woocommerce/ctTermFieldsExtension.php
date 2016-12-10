<?php

/**
 * Class ct_term_fields_extension
 * ver 1.0
 * TODO:
 * add wordpress alerts on errors,
 * style image in terms table (size limit),
 * integrity with upload plugin
 */
class ctTermFieldsExtension
{
    /**
     *
     */
    public function __construct()
    {

        //creates fields and modify terms table
        $this->updateWcTerms();

        //hook to created term
        add_action('create_term', array($this, 'createTermOption'), 10, 3);

        //hook to edited term
        add_action('edited_term', array($this, 'updateEditedTermOption'), 10, 3);

        //hook to deleted term
        add_action('delete_term_taxonomy', array($this, 'onDeleteTermTaxonomy'), 9, 1);
    }

    /**
     * Cleanups on term deletion
     *
     * @param $ttId
     */

    public function onDeleteTermTaxonomy($ttId)
    {
        self::removeTermOption($ttId);
    }

    /**
     *
     */
    protected function updateWcTerms()
    {

        $attribute_taxonomies = $this->getWcTaxonomies();

        if (is_array($attribute_taxonomies) && !empty($attribute_taxonomies)) {
            foreach ($attribute_taxonomies as $tax) {
                $this->addWcTermField($tax->attribute_name);
            }
        }
    }

    /**
     * @param null $posType
     *
     * @return object
     */
    protected function getWcTaxonomies($posType = null)
    {
        return wc_get_attribute_taxonomies();
    }

    /**
     * @param $taxonomySlug
     */
    protected function addWcTermField($taxonomySlug)
    {
        //hooks to new and edited term
        add_action(wc_attribute_taxonomy_name($taxonomySlug) . '_add_form_fields',
            array($this, 'addNewTermFieldHtml'));
        add_action(wc_attribute_taxonomy_name($taxonomySlug) . '_edit_form_fields',
            array($this, 'addEditTermFieldHtml'));
        //hooks to term table
        add_filter('manage_edit-' . wc_attribute_taxonomy_name($taxonomySlug) . '_columns',
            array($this, 'addColumnHeaderHtml'));
        add_filter('manage_' . wc_attribute_taxonomy_name($taxonomySlug) . '_custom_column',
            array($this, 'addRowsHtml'),
            10,
            3);
    }

    /**
     *
     */
    public function addNewTermFieldHtml()
    {
        ?>
        <div class="form-field">
            <label><?php _e('Thumbnail', 'ct_theme'); ?></label>

            <div id="ct_custom_thumbnail" style="float:left;margin-right:10px;"><img
                    src="" width="60px" height="60px"/></div>
            <div style="line-height:60px;">
                <input type="hidden" id="ct_custom_thumbnail_id" name="tag-image"/>
                <button type="button"
                        class="upload_image_button button"><?php _e('Upload/Add image', 'ct_theme'); ?></button>
                <button type="button"
                        class="remove_image_button button"><?php _e('Remove image', 'ct_theme'); ?></button>
            </div>
            <?php $this->includeJsScript() ?>
            <div class="clear"></div>
        </div>
    <?php
    }

    /**
     * @param $tag
     */
    public function addEditTermFieldHtml($tag)
    {
        $value = esc_attr(self::getTermOption($tag->term_id, 'img_url'));
        ?>
        <tr class="form-field">
        <th scope="row" valign="top"><label><?php _e('Thumbnail', 'ct_theme'); ?></label></th>
        <td>
            <div id="ct_custom_thumbnail" style="float:left;margin-right:10px;"><img src="<?php echo $value; ?>"
                                                                                        width="60px" height="60px"/>
            </div>
            <div style="line-height:60px;">
                <input type="hidden" id="ct_custom_thumbnail_id" name="tag-image"
                       value="<?php echo $value; ?>"/>
                <button type="submit"
                        class="upload_image_button button"><?php _e('Upload/Add image', 'ct_theme'); ?></button>
                <button type="submit"
                        class="remove_image_button button"><?php _e('Remove image', 'ct_theme'); ?></button>
            </div>
            <?php $this->includeJsScript() ?>
            <div class="clear"></div>
        </td>
        </tr><?php
    }

    protected function includeJsScript()
    {
        wp_enqueue_media();
        ?>
        <script type="text/javascript">

            // Only show the "remove image" button when needed
            if (!jQuery('#ct_custom_thumbnail_id').val()){
                jQuery('.remove_image_button').hide();
            }

            // Uploading files
            var file_frame;

            jQuery(document).on('click', '.upload_image_button', function (event) {

                event.preventDefault();

                // If the media frame already exists, reopen it.
                if (file_frame) {
                    file_frame.open();
                    return;
                }

                // Create the media frame.
                file_frame = wp.media.frames.downloadable_file = wp.media({
                    title: '<?php _e( 'Choose an image', 'ct_theme' ); ?>',
                    button: {
                        text: '<?php _e( 'Use image', 'ct_theme' ); ?>'
                    },
                    multiple: false
                });

                // When an image is selected, run a callback.
                file_frame.on('select', function () {
                    attachment = file_frame.state().get('selection').first().toJSON();

                    jQuery('#ct_custom_thumbnail_id').val(attachment.url);
                    jQuery('#ct_custom_thumbnail img').attr('src', attachment.url);
                    jQuery('.remove_image_button').show();
                });

                // Finally, open the modal.
                file_frame.open();
            });

            jQuery(document).on('click', '.remove_image_button', function (event) {
                jQuery('#ct_custom_thumbnail img').attr('src', '');
                jQuery('#ct_custom_thumbnail_id').val('');
                jQuery('.remove_image_button').hide();
                return false;
            });

        </script>
    <?php
    }

    /**
     * @param $columns
     *
     * @return mixed
     */
    public function addColumnHeaderHtml($columns)
    {
        $columns['Image'] = __('Image', 'ct_theme');

        return $columns;
    }

    /**
     * @param $content
     * @param $value
     * @param $tag_id
     *
     * @return string
     */
    public function addRowsHtml($content, $value, $tag_id)
    {
        if (self::isImgPath(self::getTermOption($tag_id, 'img_url'))) {
            $content = '<img width="60px" height="60px" class="admin_term_img" src="' . self::getTermOption($tag_id, 'img_url') . '" alt="">';
        } else {
            $content .= '';
        }

        return $content;
    }

    /**
     * @param $term_id
     *
     * @return bool
     */
    static function  updateEditedTermOption($term_id)
    {

        if (!isset($_POST['tag-image']) || !self::isImgPath($_POST['tag-image'])) {
            return false;
        } else {
            self::updateTermOption($term_id, 'img_url', $_POST['tag-image']);
        }
    }

    /**
     * @param $term_id
     *
     * @return bool
     */
    public static function  createTermOption($term_id)
    {
        if (!isset($_POST['tag-image']) || !self::isImgPath($_POST['tag-image'])) {
            return false;
        } else {
            self::updateTermOption($term_id, 'img_url', $_POST['tag-image']);
        }
    }

    /**
     * @return mixed|void
     */
    public static function  getTermOptions()
    {
        return get_option('ctTerms');
    }

    /**
     * @param $options
     *
     * @return bool
     */
    public static function  updateTermOptions($options)
    {
        //repair before update
        //$options = self::rebuild($options);

        return update_option('ctTerms', $options);
    }

    /**
     * @param $termID
     * @param $name
     * @param null $default
     *
     * @return null
     */
    public static function  getTermOption($termID, $name, $default = false)
    {
        $options = self::getTermOptions();
        if ($options == false || (!isset($options[$termID]) || !is_array($options[$termID]) || !array_key_exists($name,
                    $options[$termID]))
        ) {
            return $default;
        } else {
            return $options[$termID][$name];
        }
    }

    /**
     * @param null $termID
     * @param null $name
     * @param string $value
     *
     * @return bool
     */
    public static function  updateTermOption($termID, $name, $value = '')
    {
        $options = self::getTermOptions();


        if (!is_numeric($termID) || !is_string($name)) {
            return false;
        }
        if (!is_array($options)) {
            $options = array();
        }

        $options[$termID][$name] = $value;

        self::updateTermOptions($options);
    }

    /**
     * @param null $termID
     *
     * @return bool
     */
    public static function  removeTermOption($termID)
    {
        $options = self::getTermOptions();
        if (!is_numeric($termID) || !array_key_exists($termID, $options)) {
            return false;
        } else {
            unset($options[$termID]);
        }
        self::updateTermOptions($options);
    }

    /**
     * @param array $options
     *
     * @return array|bool
     */
    public static function  rebuild($options = array())
    {
        if (is_array($options)) {
            foreach ($options as $key => $value) {
                if (!is_array($value) || empty($value) || !is_int($key)) {
                    unset($options[$key]);
                    continue;
                }
                $value = array_unique($value);
                foreach ($value as $key2 => $value2) {

                    if (!is_string($key2) || empty($key2)) {
                        unset($options[$key][$key2]);
                        continue;
                    }
                }
            }

            return $options;
        }

        return false;
    }

    /**
     * @return bool
     */
    static function  repair()
    {
        return self::updateTermOptions(self::rebuild(self::getTermOptions()));
    }

    /**
     *
     */
    static function  cleanOptions()
    {
        $options = array();
        self::updateTermOptions($options);
    }

    /**
     * @param $path
     *
     * @return bool
     */
    static function  isImgPath($path)
    {
        if (preg_match('/(\.jpg|\.png|\.bmp|\.gif|\.JPG|\.PNG|\.BMP|\.GIF)$/', $path)) {
            return true;
        } else {
            return false;
        }
    }
}

if (ct_is_woocommerce_active()) {
    new ctTermFieldsExtension();
}
