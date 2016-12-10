<?php
/**
 * Shop Testimonial shortcode
 */
class ctShopTestimonialShortcode extends ctShortcode {

	/**
	 * Returns name
	 * @return string|void
	 */
	public function getName() {
		return 'Shop Testimonial';
	}

	/**
	 * Shortcode name
	 * @return string
	 */
	public function getShortcodeName() {
		return 'shop_testimonial';
	}

	/**
	 * Handles shortcode
	 * @param $atts
	 * @param null $content
	 * @return string
	 */

	public function handle($atts, $content = null) {
		extract(shortcode_atts($this->extractShortcodeAttributes($atts), $atts));

        $mainContainerAtts = array(
            'class' => array(
                'flatbox',
                )
        );

        $author_postscript = $author_postscript!=''?'<br>'.$author_postscript:'';
        $html='
        
        <div '.$this->buildContainerAttributes($mainContainerAtts,$atts).'>
                <h6>'.$header.'</h6>
                <p>'.$content.'</p>
                <h6>'.$sub_header.'</h6>
                '.do_shortcode('[testimonial type="'.$type.'" author="'.$author.$author_postscript.'" image="'.$image.'" link="'.$link.'"]'.$testimonial.'[/testimonial]').'
            </div>
        ';
        return do_shortcode($html);
	}




	/**
	 * Returns config
	 * @return null
	 */
	public function getAttributes() {
		return array(
            'header' => array('label' => __('Header', 'ct_theme'), 'default' => '', 'type' => 'input', 'help' => __("Header", 'ct_theme')),
			'content' => array('label' => __('content', 'ct_theme'), 'default' => '', 'type' => "textarea"),
            'sub_header' => array('label' => __('Sub Header', 'ct_theme'), 'default' => '', 'type' => 'input', 'help' => __("Testimonial Header", 'ct_theme')),
            'testimonial' => array('label' => __('Testimonial text', 'ct_theme'), 'default' => '', 'type' => "textarea"),
            'author' => array('label' => __('Author', 'ct_theme'), 'default' => '', 'type' => 'input', 'help' => __("Author", 'ct_theme')),
            'author_postscript' => array('label' => __('Author postscript', 'ct_theme'), 'default' => '', 'type' => 'input', 'help' => __("Author footnote", 'ct_theme')),
            'image' => array('label' => __("Image", 'ct_theme'), 'default' => '', 'type' => 'image', 'help' => __("Image source", 'ct_theme')),
            'link' => array('label' => __('Link', 'ct_theme'), 'default' => '', 'type' => 'input', 'help' => __("Link", 'ct_theme')),
            'type' => array('label' => __('Testimional type', 'ct_theme'), 'default' => 'dark', 'type' => 'select', 'options' => array(
                'light' => 'light',
                'dark' => 'dark',
            )),
		);
	}
}

new ctShopTestimonialShortcode();