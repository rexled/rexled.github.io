<?php

/**
 * Upgrades FoodTruck
 * @author alex
 */
class ctFoodtruckUpgrader {

	/**
	 * Initializes upgrader
	 */

	public function __construct() {
		add_action( 'admin_print_styles', array( $this, 'addNotices' ) );
		add_action( 'admin_init', array( $this, 'installActions' ) );
		add_action( 'ct.theme_activation', array( $this, 'updateVersion' ), 10, 3 );
	}

	/**
	 * Adds required notices
	 */

	public function addNotices() {
		if ( ! $this->isUpdated( CT_PROJECT_NAME, '3.0' ) ) {
			wp_enqueue_style( 'ct-activation', CT_THEME_SETTINGS_MAIN_DIR_URI . '/plugin/upgrader/assets/css/notices.css' );
			add_action( 'admin_notices', array( $this, 'installUpdateVersion30' ) );
		}
	}

	/**
	 * Tries to update
	 *
	 * @param $prevVersion
	 * @param $currentVersion
	 * @param $projectName
	 */

	public function updateVersion( $prevVersion, $currentVersion, $projectName ) {
		$this->updateVersion30( $projectName );
	}

	/**
	 * Add notice for version 3.0
	 */

	public function installUpdateVersion30() {
		echo $this->getNoticeHtml( ucfirst( CT_PROJECT_NAME ) . ' ' . __( 'Data Update Required', 'ct_theme' ), __( 'We just need to update your install to the latest version', 'ct_theme' ), 'do_update_30_ct', __( 'Run Updater', 'ct_theme' ) );
	}

	/**
	 * Install stuff
	 */

	public function installActions() {
		if ( isset( $_GET['do_update_30_ct'] ) ) {
			$this->updateVersion30( CT_PROJECT_NAME );
			// What's new redirect
			wp_redirect( admin_url( 'themes.php' ) );
			exit;
		}
	}

	/**
	 * Update version to 3.0
	 *
	 * @param $projectName
	 */

	protected function updateVersion30( $projectName ) {
		$version = '3.0';
		if ( $this->isUpdated( $projectName, $version ) ) {
			return;
		}

		global $wpdb;
		//due to WooCommerce integration, our internal product was renamed to ct_product
		$wpdb->query( sprintf( "UPDATE  %s SET  post_type =  'ct_product' WHERE  post_type = 'product'", $wpdb->posts ) );
		$wpdb->query( sprintf( "UPDATE  %s SET taxonomy =  'ct_product_category' WHERE  taxonomy = 'product_category'", $wpdb->term_taxonomy ) );

		//let's refactor shortcode names
		$replacements = array(
			'[product '   => '[ct_product ',
			'[/product]'  => '[/ct_product]',
			'[products '  => '[ct_products ',
			'[/products]' => '[/ct_products]'
		);

		foreach ( $replacements as $from => $to ) {
			$wpdb->query( sprintf( "UPDATE  %s SET  post_content =  REPLACE(post_content,'%s','%s')", $wpdb->posts, $from, $to ) );
		}
		$this->markAsUpdated( $projectName, $version );
	}

	/**
	 * Remove updated check
	 *
	 * @param $projectName
	 * @param $name
	 */

	protected function removeUpdated( $projectName, $name ) {
		delete_option( $this->getUpdateKey( $projectName, $name ) );
	}

	/**
	 * Is this updated completed?
	 *
	 * @param $projectName
	 * @param $name
	 *
	 * @return bool
	 */

	protected function isUpdated( $projectName, $name ) {
		//return false;

		return get_option( $this->getUpdateKey( $projectName, $name ) ) == 1;
	}

	/**
	 * Mark as updated
	 *
	 * @param $projectName
	 * @param $name
	 */

	protected function markAsUpdated( $projectName, $name ) {
		update_option( $this->getUpdateKey( $projectName, $name ), 1 );
	}

	/**
	 * Get update key
	 *
	 * @param $projectName
	 * @param $name
	 *
	 * @return string
	 */

	protected function getUpdateKey( $projectName, $name ) {
		return 'updated_' . $projectName . '_' . $name;
	}

	/**
	 * Returns HTML notice
	 *
	 * @param $title
	 * @param $message
	 * @param $btn
	 *
	 * @return string
	 */

	protected function getNoticeHtml( $title, $message, $action, $btn ) {
		return '<div class="ct-notice updated">
            <img src="'.CT_THEME_SETTINGS_MAIN_DIR_URI . '/img/ct.png" alt="createIT">
        	<p><strong>' . esc_html( $title ) . '</strong> &#8211; ' . $message . '</p>
        	<p class="submit"><a href="' . esc_url(add_query_arg( $action, 'true', admin_url( 'themes.php' ) )) . '" class="wc-update-now button-primary">' . esc_html( $btn ) . '</a></p>
        </div>';
	}

}

new ctFoodtruckUpgrader();