<?php
/**
 * Shop single product footnote shortcode
 */
class ctProductFootnoteShortcode extends ctShortcode {

	/**
	 * Returns name
	 * @return string|void
	 */
	public function getName() {
		return 'Product footnote';
	}

	/**
	 * Shortcode name
	 * @return string
	 */
	public function getShortcodeName() {
		return 'product_footnote';
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
                $class,
                )
        );

        if ($new_window == 'true' || $new_window == 'yes'){
            $new_window = 'target="_blank"';
        }else{
            $new_window='';
        }

        $linkShortcode = $link ? '<br> [link '.$new_window.' link="'.$link.'"]'.$link_text.'[/link]' : '';


        $html='[easy_box][icon_box header="'.$header.'" icon="'.$icon.'"]'.$content.$linkShortcode.'[/icon_box][/easy_box]';
        return '<div '.$this->buildContainerAttributes($mainContainerAtts,$atts).'>'.do_shortcode($html).'</div>';
	}




	/**
	 * Returns config
	 * @return null
	 */
	public function getAttributes() {
		return array(
            'header' => array('label' => __('Header', 'ct_theme'), 'default' => '', 'type' => 'input', 'help' => __("Header", 'ct_theme')),
            'icon' => array('label' => __('icon', 'ct_theme'), 'type' => "icon", 'default' => '', 'link' => CT_THEME_ASSETS . '/shortcode/awesome/index.html'),
            'content' => array('label' => __('content', 'ct_theme'), 'default' => '', 'type' => "textarea", 'help' => __("Text, HTML or shortcode.", 'ct_theme')),
            'link' => array('label' => __('link', 'ct_theme'), 'default' => '', 'type' => 'input', 'help' => __("leave empty for show content only", 'ct_theme')),
            'link_text' => array('label' => __('link text', 'ct_theme'), 'default' => '', 'type' => 'input', 'help' => __('label', 'ct_theme')),
            'new_window' => array('label' => __('Open link in new Window?', 'ct_theme'), 'default' => 'no', 'type' => 'select', 'choices' => array('yes' => __('yes', 'ct_theme'), 'no' => __('no', 'ct_theme'))),
            'class' => array('label' => __("Custom class", 'ct_theme'), 'default' => '', 'type' => 'input'),
		);
	}
}

new ctProductFootnoteShortcode();