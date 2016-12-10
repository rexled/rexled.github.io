<?php
require_once CT_THEME_LIB_WIDGETS.'/ctShortcodeWidget.class.php';

/**
 * Recent posts widget
 * @author hc
 */

class ctShopTestimonialWidget extends ctShortcodeWidget {
	/**
	 * Creates wordpress
	 */
	function __construct() {
		$widget_ops = array('classname' => 'widget_shop_testimonial', 'description' => __('Displays shop testimonial.', 'ct_theme'));
		parent::__construct('shop_testimonial', 'CT - ' . __('Shop testimonial', 'ct_theme'), $widget_ops);
	}

	/**
	 * Returns shortcode class
	 * @return mixed
	 */
	protected function getShortcodeName() {
		return 'shop_testimonial';
	}
}

register_widget('ctShopTestimonialWidget');
