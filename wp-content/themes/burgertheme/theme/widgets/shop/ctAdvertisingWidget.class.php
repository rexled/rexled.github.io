<?php
require_once CT_THEME_LIB_WIDGETS.'/ctShortcodeWidget.class.php';

/**
 * Recent posts widget
 * @author hc
 */

class ctAdvertisingWidget extends ctShortcodeWidget {
	/**
	 * Creates wordpress
	 */
	function __construct() {
		$widget_ops = array('classname' => 'advertising', 'description' => __('Displays advertising.', 'ct_theme'));
		parent::__construct('advertising', 'CT - ' . __('Advertising', 'ct_theme'), $widget_ops);
	}

	/**
	 * Returns shortcode class
	 * @return mixed
	 */
	protected function getShortcodeName() {
		return 'advertising';
	}
}

register_widget('ctAdvertisingWidget');
