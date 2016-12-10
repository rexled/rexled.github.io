<?php
require_once CT_THEME_LIB_WIDGETS.'/ctShortcodeWidget.class.php';

/**
 * Recent posts widget
 * @author hc
 */

class ctProductFootnoteWidget extends ctShortcodeWidget {
	/**
	 * Creates wordpress
	 */
	function __construct() {
		$widget_ops = array('classname' => 'product_footnote', 'description' => __('Displays product footnote.', 'ct_theme'));
		parent::__construct('product_footnote', 'CT - ' . __('product_footnote', 'ct_theme'), $widget_ops);
	}

	/**
	 * Returns shortcode class
	 * @return mixed
	 */
	protected function getShortcodeName() {
		return 'product_footnote';
	}
}

register_widget('ctProductFootnoteWidget');
