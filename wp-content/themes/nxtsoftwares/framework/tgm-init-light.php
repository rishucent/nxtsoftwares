<?php

function azexo_tgmpa_register() {
    $plugins[] = array(
        'name' => esc_html__('Core theme plugin', 'mynx'),
        'slug' => 'mynx-page-builder',
        'required' => true,
    );
    $plugins[] = array(
        'name' => esc_html__('Page Builder by AZEXO', 'mynx'),
        'slug' => 'page-builder-by-azexo',
        'required' => true,
    );
    $plugins[] = array(
        'name' => esc_html__('Redux Framework', 'mynx'),
        'slug' => 'redux-framework',
        'required' => true,
    );
    $plugins[] = array(
        'name' => esc_html__('JP Widget Visibility', 'mynx'),
        'slug' => 'jetpack-widget-visibility',
    );
    $plugins[] = array(
        'name' => esc_html__('WP-LESS', 'mynx'),
        'slug' => 'wp-less',
    );

    $plugins = apply_filters('azexo_plugins', $plugins);
    if (!empty($plugins)) {
        tgmpa($plugins, array());
    }
}

add_action('tgmpa_register', 'azexo_tgmpa_register');
