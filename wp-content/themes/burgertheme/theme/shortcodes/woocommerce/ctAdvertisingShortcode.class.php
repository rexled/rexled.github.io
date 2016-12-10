<?php
/**
 * Shop Advertising shortcode
 */
class ctAdvertisingShortcode extends ctShortcode {

	/**
	 * Returns name
	 * @return string|void
	 */
	public function getName() {
		return 'Advertising';
	}

	/**
	 * Shortcode name
	 * @return string
	 */
	public function getShortcodeName() {
		return 'advertising';
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

        $html='
        <div '.$this->buildContainerAttributes($mainContainerAtts,$atts).'>
        <h2 class="hdr8">'.$header.'</h2>
            <span class="advertising">
            '.$content.'
            </span></div>';
        return do_shortcode($html);
	}




	/**
	 * Returns config
	 * @return null
	 */
	public function getAttributes() {
		return array(
            'header' => array('label' => __('Header', 'ct_theme'), 'default' => '', 'type' => 'input', 'help' => __("Header", 'ct_theme')),
			'content' => array('label' => __('content', 'ct_theme'), 'default' => '', 'type' => "textarea", 'help' => __("Text, HTML or shortcode.", 'ct_theme')),
            'class' => array('label' => __("Custom class", 'ct_theme'), 'default' => '', 'type' => 'input'),
		);
	}
}

new ctAdvertisingShortcode();