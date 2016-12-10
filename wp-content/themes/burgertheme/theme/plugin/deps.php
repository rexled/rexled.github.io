<?php
/**
 * Array of plugin arrays. Required keys are name and slug.
 * If the source is NOT from the .org repo, then source is also required.
 */
$plugins = array(
    array(
        'name' => 'Multiple Featured Images', // The plugin name
        'slug' => 'multiple-featured-images', // The plugin slug (typically the folder name)
        'source' => CT_THEME_DIR . '/vendor/multiple-featured-images/multiple-featured-images.zip', // The plugin source
        'required' => false, // If false, the plugin is only 'recommended' instead of required
        'version' => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
        'force_activation' => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
        'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
    ),
    array(
        'name' => 'Custom Sidebars (included)', // The plugin name
        'slug' => 'custom-sidebars', // The plugin slug (typically the folder name)
        'source' => CT_THEME_DIR . '/vendor/custom-sidebars/custom-sidebars.zip', // The plugin source
        'required' => false, // If false, the plugin is only 'recommended' instead of required
        'version' => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
        'force_activation' => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
        'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
    ),

    array(
        'name' => 'WooCommerce Product Size Guide (included)', // The plugin name
        'slug' => 'sizeguide', // The plugin slug (typically the folder name)
        'source' => CT_THEME_DIR . '/vendor/ct-size-guide/sizeguide.zip', // The plugin source
        'required' => false, // If false, the plugin is only 'recommended' instead of required
        'force_activation' => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
        'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
    ),

    array(
        'name' => 'Catalog Mode for WooCommerce (included)', // The plugin name
        'slug' => 'ct-catalog', // The plugin slug (typically the folder name)
        'source' => CT_THEME_DIR . '/vendor/ct-catalog/ct-catalog.zip', // The plugin source
        'required' => false, // If false, the plugin is only 'recommended' instead of required
        'force_activation' => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
        'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
    ),

    array(
        'name' => 'View360 (included)', // The plugin name
        'slug' => 'ct-view-360', // The plugin slug (typically the folder name)
        'source' => CT_THEME_DIR . '/vendor/ct-view-360/ct-view-360.zip', // The plugin source
        'required' => false, // If false, the plugin is only 'recommended' instead of required
        'force_activation' => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
        'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
    ),

);
