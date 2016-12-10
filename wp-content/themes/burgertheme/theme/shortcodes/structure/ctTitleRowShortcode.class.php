<?php
/**
 * Title row shortcode
 */
class ctTitleRowShortcode extends ctShortcode {

	/**
	 * Returns name
	 * @return string|void
	 */
	public function getName() {
		return 'Title row';
	}

	/**
	 * Shortcode name
	 * @return string
	 */
	public function getShortcodeName() {
		return 'title_row';
	}

	/**
	 * Handles shortcode
	 * @param $atts
	 * @param null $content
	 * @return string
	 */

	public function handle($atts, $content = null) {
		extract(shortcode_atts($this->extractShortcodeAttributes($atts), $atts));

		$breadcrumbsHtml = '';
		if($breadcrumbs == 'yes'){
			$breadcrumbsHtml = wp_nav_menu( array(
								    'container' => 'none',
								    'theme_location' => 'breadcrumbs',
								    'walker'=> new ctBreadCrumbWalker,
									'echo' => false,
								    'items_wrap' => '%3$s'
								 ) );

			$breadcrumbsHtml = $breadcrumbsHtml ? '<div class="breadcrumbs">

													            <div class="breadcrumbs-inner">
													                ' . $breadcrumbsHtml . '
													            </div>

													</div>' : '';
		}

		$id = $id ? (' id="' . $id . '"') : '';
		$html = $header ? '<header' . $id . ' style="background-image: url(' . get_header_image() . ')">



							                <h3 class="hdr1">' . $header . '</h3>



							</header>' : '';
		$html .= $breadcrumbsHtml;

		return $html;
	}

	/**
	 * is template with sidebar?
	 * @return bool
	 */
	protected function isSidebar() {
		return is_page_template('page-custom.php') || is_page_template('page-custom-left.php');
	}

	/**
	 * Returns config
	 * @return null
	 */
	public function getAttributes() {
		return array(
			'id' => array('label' => __('header id', 'ct_theme'), 'default' => 'BlogHeader', 'type' => 'input', 'help' => __("html id attribute", 'ct_theme')),
			'header' => array('label' => __('header', 'ct_theme'), 'default' => '', 'type' => 'input', 'help' => __("Header text", 'ct_theme')),
			'breadcrumbs' => array('label' => __('breadcrumbs', 'ct_theme'), 'default' => 'no', 'type' => 'select', 'choices' => array('yes' => __('yes', 'ct_theme'), 'no' => __('no', 'ct_theme')), 'help' => __("Show breadcrumbs path?", 'ct_theme')),
		);
	}
}

new ctTitleRowShortcode();