<?php
/**
 *
 * @author alex
 */

class ctCustomizeManagerHandler {

	/**
	 *
	 */
	public function __construct() {
		add_action('customize_register', array($this, 'customizeRegister'), 20);
		add_theme_support('custom-background', array('wp-head-callback' => array($this, 'wpHeadCallback')));
	}

	/**
	 * Customize theme preview
	 * @param WP_Customize_Manager $wp_manager
	 * @return \WP_Customize_Manager
	 */

	public function customizeRegister($wp_manager) {

        $wp_manager->add_section( 'backgrounds_section' , array(
            'title'       => __( 'Foodtruck Header and Footer backgrounds', 'themeslug' ),
            'priority'    => 30,
            'description' => 'Upload a custom background',
        ) );


        $wp_manager->add_setting( 'background_image', array('type' => 'theme_mod'));
        $wp_manager->add_control( new WP_Customize_Image_Control( $wp_manager, 'background_image', array(
            'label'    => __( 'Main background', 'themeslug' ),
            'section'  => 'backgrounds_section',
            'settings' => 'background_image',

        ) ) );


        $wp_manager->add_setting( 'top_bg1', array('type' => 'theme_mod'));
        $wp_manager->add_control( new WP_Customize_Image_Control( $wp_manager, 'top_bg1', array(
            'label'    => __( 'Top background 1', 'themeslug' ),
            'section'  => 'backgrounds_section',
            'settings' => 'top_bg1',

        ) ) );

        $wp_manager->add_setting( 'top_bg2', array('type' => 'theme_mod') );
        $wp_manager->add_control( new WP_Customize_Image_Control( $wp_manager, 'top_bg2', array(
            'label'    => __( 'Top background 2', 'themeslug' ),
            'section'  => 'backgrounds_section',
            'settings' => 'top_bg2',

        ) ) );

        $wp_manager->add_setting( 'top_bg3', array('type' => 'theme_mod') );
        $wp_manager->add_control( new WP_Customize_Image_Control( $wp_manager, 'top_bg3', array(
            'label'    => __( 'Top background 3', 'themeslug' ),
            'section'  => 'backgrounds_section',
            'settings' => 'top_bg3',

        ) ) );

        $wp_manager->add_setting( 'top_bg4', array('type' => 'theme_mod') );
        $wp_manager->add_control( new WP_Customize_Image_Control( $wp_manager, 'top_bg4', array(
            'label'    => __( 'Top background 4', 'themeslug' ),
            'section'  => 'backgrounds_section',
            'settings' => 'top_bg4',

        ) ) );

        $wp_manager->add_setting( 'footer_bg1', array('type' => 'theme_mod') );
        $wp_manager->add_control( new WP_Customize_Image_Control( $wp_manager, 'footer_bg1', array(
            'label'    => __( 'Footer background 1', 'themeslug' ),
            'section'  => 'backgrounds_section',
            'settings' => 'footer_bg1',

        ) ) );

        $wp_manager->add_setting( 'footer_bg2', array('type' => 'theme_mod') );
        $wp_manager->add_control( new WP_Customize_Image_Control( $wp_manager, 'footer_bg2', array(
            'label'    => __( 'Footer background 2', 'themeslug' ),
            'section'  => 'backgrounds_section',
            'settings' => 'footer_bg2',

        ) ) );

        if (ct_is_woocommerce_active()) {
            $wp_manager->add_section('shop_backgrounds_section', array(
                'title' => __('Foodtruck Shop styles', 'themeslug'),
                'priority' => 30,
                'description' => 'Upload a shop background',
            ));

            $wp_manager->add_setting( 'top_bg_woo', array('type' => 'theme_mod'));
            $wp_manager->add_control( new WP_Customize_Image_Control( $wp_manager, 'top_bg_woo', array(
                'label'    => __( 'Shop topbar background', 'themeslug' ),
                'section'  => 'backgrounds_section',
                'settings' => 'top_bg_woo',
            ) ) );

            $wp_manager->add_setting('shop_bg', array('type' => 'theme_mod'));
            $wp_manager->add_control(new WP_Customize_Image_Control($wp_manager, 'shop_bg', array(
                'label' => __('Shop content background', 'themeslug'),
                'section' => 'shop_backgrounds_section',
                'settings' => 'shop_bg',

            )));
        }

        $wp_manager->add_section('socials_customize_section', array(
            'title' => __('Foodtruck Social styles', 'themeslug'),
            'priority' => 30,
            'description' => 'Upload a shop background',
        ));

        $wp_manager->add_setting( 'socials_customize_bg', array('type' => 'theme_mod'));
        $wp_manager->add_control( new WP_Customize_Image_Control( $wp_manager, 'socials_customize_bg', array(
            'label'    => __( 'Social icons background', 'themeslug' ),
            'section'  => 'socials_customize_section',
            'settings' => 'socials_customize_bg',
        ) ) );

		return $wp_manager;
	}

	public function wpHeadCallback() {
		require_once CT_THEME_SETTINGS_MAIN_DIR . '/custom_style.php';

	}

}?>
