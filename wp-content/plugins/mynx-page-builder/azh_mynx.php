<?php
/*
  Plugin Name: Mynx Page Builder
  Description: Mynx Page Builder by AZEXO
  Author: azexo
  Author URI: http://azexo.com
  Version: 1.27.8
  Text Domain: azh
 */


register_activation_hook( __FILE__, 'azh_mynx_activate' );

function azh_mynx_activate() {
    update_option('azh-library', array());
    update_option('azh-all-settings', array());
    update_option('azh-get-content-scripts', array());
    update_option('azh-content-settings', array());
}

add_filter('upload_mimes', 'azh_mynx_upload_mimes');

function azh_mynx_upload_mimes($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}

add_action('admin_notices', 'azh_mynx_notices');

function azh_mynx_notices() {
    $plugin_data = get_plugin_data(__FILE__);
    if (defined('AZH_VERSION')) {
        $plugin_version = explode('.', $plugin_data['Version']);
        $plugin_version = $plugin_version[1];
        $azh_version = explode('.', AZH_VERSION);
        $azh_version = $azh_version[1];
        if ((int) $plugin_version !== (int) $azh_version) {
            print '<div id="azh-version" class="notice-error settings-error notice is-dismissible"><p>' . __('AZEXO Builder version does not correspond with library version. Please update library plugin or builder plugin', 'azh') . '</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">' . __('Dismiss this notice.', 'azh') . '</span></button></div>';
        }
    } else {
        print '<div class="updated notice error is-dismissible"><p>' . $plugin_data['Name'] . ': ' . __('please install <a href="https://codecanyon.net/item/azexo-html-customizer/16350601">Page Builder by AZEXO</a> plugin.', 'azh') . '</p><button class="notice-dismiss" type="button"><span class="screen-reader-text">' . esc_html__('Dismiss this notice.', 'azh') . '</span></button></div>';
    }
}

add_action('admin_init', 'azh_mynx_options', 11);

function azh_mynx_options() {
    if (file_exists(dirname(__FILE__) . '/azh_settings.json')) {
        $settings = get_option('azh-settings');
        if ((!is_array($settings) || !isset($settings['azh-uri'])) && function_exists('azh_filesystem')) {
            azh_filesystem();
            global $wp_filesystem;
            $extension_settings = $wp_filesystem->get_contents(dirname(__FILE__) . '/azh_settings.json');
            $extension_settings = json_decode($extension_settings, true);
            $settings = array_merge((array)$settings, $extension_settings);
            update_option('azh-settings', $settings);
        }
    }
    if (class_exists('WPLessPlugin')) {
        if (!defined('AZEXO_FRAMEWORK')) {
            add_settings_field(
                    'brand-color', // Field ID
                    esc_html__('Brand color', 'azh'), // Label to the left
                    'azh_textfield', // Name of function that renders options on the page
                    'azh-settings', // Page to show on
                    'azh_general_options_section', // Associate with which settings section?
                    array(
                'id' => 'brand-color',
                'type' => 'color',
                'default' => '#FF0000',
                    )
            );
            add_settings_field(
                    'accent-1-color', // Field ID
                    esc_html__('Accent 1 color', 'azh'), // Label to the left
                    'azh_textfield', // Name of function that renders options on the page
                    'azh-settings', // Page to show on
                    'azh_general_options_section', // Associate with which settings section?
                    array(
                'id' => 'accent-1-color',
                'type' => 'color',
                'default' => '#00FF00',
                    )
            );
            add_settings_field(
                    'accent-2-color', // Field ID
                    esc_html__('Accent 2 color', 'azh'), // Label to the left
                    'azh_textfield', // Name of function that renders options on the page
                    'azh-settings', // Page to show on
                    'azh_general_options_section', // Associate with which settings section?
                    array(
                'id' => 'accent-2-color',
                'type' => 'color',
                'default' => '#0000FF',
                    )
            );
        }

        global $azh_google_fonts;
        if(!isset($azh_google_fonts)) {
            $azh_google_fonts = array();
        }
        add_settings_field(
                'main-google-font', // Field ID
                esc_html__('Main google font', 'azh'), // Label to the left
                'azh_select', // Name of function that renders options on the page
                'azh-settings', // Page to show on
                'azh_general_options_section', // Associate with which settings section?
                array(
            'id' => 'main-google-font',
            'options' => array_combine($azh_google_fonts, $azh_google_fonts),
            'default' => 'Open Sans',
                )
        );
        add_settings_field(
                'main-border-color', // Field ID
                esc_html__('Main border color', 'azh'), // Label to the left
                'azh_textfield', // Name of function that renders options on the page
                'azh-settings', // Page to show on
                'azh_general_options_section', // Associate with which settings section?
                array(
            'id' => 'main-border-color',
            'class' => 'azh-wp-color-picker',
            'default' => '',
            'desc' => __('Select for override. Clear for use default color.', 'azh'),
                )
        );
        add_settings_field(
                'main-border-width', // Field ID
                esc_html__('Main border width (px)', 'azh'), // Label to the left
                'azh_textfield', // Name of function that renders options on the page
                'azh-settings', // Page to show on
                'azh_general_options_section', // Associate with which settings section?
                array(
            'id' => 'main-border-width',
            'default' => '1',
            'type' => 'number',
                )
        );
        add_settings_field(
                'control-border-width', // Field ID
                esc_html__('Control border width (px)', 'azh'), // Label to the left
                'azh_textfield', // Name of function that renders options on the page
                'azh-settings', // Page to show on
                'azh_general_options_section', // Associate with which settings section?
                array(
            'id' => 'control-border-width',
            'default' => '1',
            'type' => 'number',
                )
        );
        add_settings_field(
                'main-border-radius', // Field ID
                esc_html__('Main border radius (px)', 'azh'), // Label to the left
                'azh_textfield', // Name of function that renders options on the page
                'azh-settings', // Page to show on
                'azh_general_options_section', // Associate with which settings section?
                array(
            'id' => 'main-border-radius',
            'default' => '4',
            'type' => 'number',
                )
        );
        add_settings_field(
                'main-shadow-color', // Field ID
                esc_html__('Main shadow color', 'azh'), // Label to the left
                'azh_textfield', // Name of function that renders options on the page
                'azh-settings', // Page to show on
                'azh_general_options_section', // Associate with which settings section?
                array(
            'id' => 'main-shadow-color',
            'class' => 'azh-wp-color-picker',
            'default' => '',
            'desc' => __('Select for override. Clear for use default color.', 'azh'),
                )
        );
        add_settings_field(
                'menu-height', // Field ID
                esc_html__('Menu height (px)', 'azh'), // Label to the left
                'azh_textfield', // Name of function that renders options on the page
                'azh-settings', // Page to show on
                'azh_general_options_section', // Associate with which settings section?
                array(
            'id' => 'menu-height',
            'default' => '120',
            'type' => 'number',
                )
        );
        add_settings_field(
                'menu-google-font', // Field ID
                esc_html__('Menu google font', 'azh'), // Label to the left
                'azh_select', // Name of function that renders options on the page
                'azh-settings', // Page to show on
                'azh_general_options_section', // Associate with which settings section?
                array(
            'id' => 'menu-google-font',
            'options' => array_combine($azh_google_fonts, $azh_google_fonts),
            'default' => 'Open Sans',
                )
        );
        add_settings_field(
                'menu-font-color', // Field ID
                esc_html__('Menu font color', 'azh'), // Label to the left
                'azh_textfield', // Name of function that renders options on the page
                'azh-settings', // Page to show on
                'azh_general_options_section', // Associate with which settings section?
                array(
            'id' => 'menu-font-color',
            'class' => 'azh-wp-color-picker',
            'default' => '',
            'desc' => __('Select for override. Clear for use default color.', 'azh'),
                )
        );
        add_settings_field(
                'menu-font-size', // Field ID
                esc_html__('Menu font size (px)', 'azh'), // Label to the left
                'azh_textfield', // Name of function that renders options on the page
                'azh-settings', // Page to show on
                'azh_general_options_section', // Associate with which settings section?
                array(
            'id' => 'menu-font-size',
            'default' => '14',
            'type' => 'number',
                )
        );
        add_settings_field(
                'menu-font-weight', // Field ID
                esc_html__('Menu font weight', 'azh'), // Label to the left
                'azh_select', // Name of function that renders options on the page
                'azh-settings', // Page to show on
                'azh_general_options_section', // Associate with which settings section?
                array(
            'id' => 'menu-font-weight',
            'options' => array(
                '100' => '100',
                '200' => '200',
                '300' => '300',
                '400' => '400',
                '500' => '500',
                '600' => '600',
                '700' => '700',
                '800' => '800',
                '900' => '900',
            ),
            'default' => '400',
                )
        );

        add_settings_field(
                'header-google-font', // Field ID
                esc_html__('Header google font', 'azh'), // Label to the left
                'azh_select', // Name of function that renders options on the page
                'azh-settings', // Page to show on
                'azh_general_options_section', // Associate with which settings section?
                array(
            'id' => 'header-google-font',
            'options' => array_combine($azh_google_fonts, $azh_google_fonts),
            'default' => 'Open Sans',
                )
        );
        add_settings_field(
                'header-color', // Field ID
                esc_html__('Header color', 'azh'), // Label to the left
                'azh_textfield', // Name of function that renders options on the page
                'azh-settings', // Page to show on
                'azh_general_options_section', // Associate with which settings section?
                array(
            'id' => 'header-color',
            'class' => 'azh-wp-color-picker',
            'default' => '',
            'desc' => __('Select for override. Clear for use default color.', 'azh'),
                )
        );
        add_settings_field(
                'header-font-size', // Field ID
                esc_html__('Header font size (px)', 'azh'), // Label to the left
                'azh_textfield', // Name of function that renders options on the page
                'azh-settings', // Page to show on
                'azh_general_options_section', // Associate with which settings section?
                array(
            'id' => 'header-font-size',
            'default' => '32',
            'type' => 'number',
                )
        );
        add_settings_field(
                'header-line-height', // Field ID
                esc_html__('Header line height', 'azh'), // Label to the left
                'azh_textfield', // Name of function that renders options on the page
                'azh-settings', // Page to show on
                'azh_general_options_section', // Associate with which settings section?
                array(
            'id' => 'header-line-height',
            'default' => '1.45',
            'type' => 'number',
            'step' => '0.01',
                )
        );
        add_settings_field(
                'header-font-weight', // Field ID
                esc_html__('Header font weight', 'azh'), // Label to the left
                'azh_select', // Name of function that renders options on the page
                'azh-settings', // Page to show on
                'azh_general_options_section', // Associate with which settings section?
                array(
            'id' => 'header-font-weight',
            'options' => array(
                '100' => '100',
                '200' => '200',
                '300' => '300',
                '400' => '400',
                '500' => '500',
                '600' => '600',
                '700' => '700',
                '800' => '800',
                '900' => '900',
            ),
            'default' => '400',
                )
        );


        add_settings_field(
                'paragraph-color', // Field ID
                esc_html__('Paragraph color', 'azh'), // Label to the left
                'azh_textfield', // Name of function that renders options on the page
                'azh-settings', // Page to show on
                'azh_general_options_section', // Associate with which settings section?
                array(
            'id' => 'paragraph-color',
            'class' => 'azh-wp-color-picker',
            'default' => '',
            'desc' => __('Select for override. Clear for use default color.', 'azh'),
                )
        );
        add_settings_field(
                'paragraph-font-size', // Field ID
                esc_html__('Paragraph font size (px)', 'azh'), // Label to the left
                'azh_textfield', // Name of function that renders options on the page
                'azh-settings', // Page to show on
                'azh_general_options_section', // Associate with which settings section?
                array(
            'id' => 'paragraph-font-size',
            'default' => '18',
            'type' => 'number',
                )
        );
        add_settings_field(
                'paragraph-line-height', // Field ID
                esc_html__('Paragraph line height', 'azh'), // Label to the left
                'azh_textfield', // Name of function that renders options on the page
                'azh-settings', // Page to show on
                'azh_general_options_section', // Associate with which settings section?
                array(
            'id' => 'paragraph-line-height',
            'default' => '1.45',
            'type' => 'number',
            'step' => '0.01',
                )
        );
        add_settings_field(
                'paragraph-font-weight', // Field ID
                esc_html__('Paragraph font weight', 'azh'), // Label to the left
                'azh_select', // Name of function that renders options on the page
                'azh-settings', // Page to show on
                'azh_general_options_section', // Associate with which settings section?
                array(
            'id' => 'paragraph-font-weight',
            'options' => array(
                '100' => '100',
                '200' => '200',
                '300' => '300',
                '400' => '400',
                '500' => '500',
                '600' => '600',
                '700' => '700',
                '800' => '800',
                '900' => '900',
            ),
            'default' => '300',
                )
        );
        add_settings_field(
                'paragraph-bold-weight', // Field ID
                esc_html__('Paragraph bold weight', 'azh'), // Label to the left
                'azh_select', // Name of function that renders options on the page
                'azh-settings', // Page to show on
                'azh_general_options_section', // Associate with which settings section?
                array(
            'id' => 'paragraph-bold-weight',
            'options' => array(
                '100' => '100',
                '200' => '200',
                '300' => '300',
                '400' => '400',
                '500' => '500',
                '600' => '600',
                '700' => '700',
                '800' => '800',
                '900' => '900',
            ),
            'default' => '600',
                )
        );


        add_settings_field(
                'google-fonts', // Field ID
                esc_html__('Google fonts families', 'azh'), // Label to the left
                'azh_textarea', // Name of function that renders options on the page
                'azh-settings', // Page to show on
                'azh_general_options_section', // Associate with which settings section?
                array(
            'id' => 'google-fonts',
            'default' => "Open+Sans:300,400,500,600,700\n"
            . "Montserrat:400,700\n"
            . "Droid+Serif:400,700",
                )
        );
    } else {
        add_settings_section(
                'azh_wp_less', // Section ID
                esc_html__('Install WP Less plugin for global styles settings', 'azh'), // Title above settings section
                'azh_mynx_wp_less', // Name of function that renders a description of the settings section
                'azh-settings'                     // Page to show on
        );
    }

//    add_settings_field(
//            'css-provided', // Field ID
//            esc_html__('CSS provided', 'azh'), // Label to the left
//            'azh_checkbox', // Name of function that renders options on the page
//            'azh-settings', // Page to show on
//            'azh_general_options_section', // Associate with which settings section?
//            array(
//        'id' => 'css-provided',
//        'default' => array(
//        ),
//        'options' => array(
//        ),
//            )
//    );

    add_settings_field(
            'prefix', // Field ID
            esc_html__('Prefix', 'azh'), // Label to the left
            'azh_textfield', // Name of function that renders options on the page
            'azh-settings', // Page to show on
            'azh_general_options_section', // Associate with which settings section?
            array(
        'id' => 'prefix',
        'default' => "azen",
            )
    );

    add_settings_field(
            'azh-uri', // Field ID
            esc_html__('AZH folder URI', 'azh'), // Label to the left
            'azh_textfield', // Name of function that renders options on the page
            'azh-settings', // Page to show on
            'azh_general_options_section', // Associate with which settings section?
            array(
        'id' => 'azh-uri',
            )
    );
}

function azh_mynx_wp_less() {
    ?>
    <a href="https://wordpress.org/plugins/wp-less/" target="_blank"><?php echo esc_html_e('WP-LESS plugin', 'azh'); ?></a>
    <?php
}

add_action('azh_load', 'azh_mynx_admin_load', 10, 2);

function azh_mynx_admin_load($post_type, $post) {
    wp_enqueue_script('azh_extension_admin', plugins_url('js/admin.js', __FILE__), array('azexo_html'), false, true);
}

add_filter('wp-less_stylesheet_compute_target_path', 'azh_mynx_target_path');

function azh_mynx_target_path($target_path) {
    $target_path = preg_replace('#^' . plugin_dir_url('') . '#U', '/', $target_path);
    return $target_path;
}

function azh_mynx_init_less() {
    $less = WPLessPlugin::getInstance();    
    $less->getConfiguration()->setCompilationStrategy('legacy');
    azh_mynx_set_less_variables();

    WPLessStylesheet::$upload_dir = $less->getConfiguration()->getUploadDir();
    WPLessStylesheet::$upload_uri = $less->getConfiguration()->getUploadUrl();

    if (!wp_mkdir_p(WPLessStylesheet::$upload_dir)) {
        throw new WPLessException(sprintf('The upload dir folder (`%s`) is not writable from %s.', WPLessStylesheet::$upload_dir, get_class($less)));
    }
    return $less;
}

function azh_mynx_get_file_scripts(&$scripts, $path, &$projects) {
    $folders = explode('/', $path);
    $project = reset($folders);
    if (class_exists('WPLessPlugin')) {
        static $less = false;
        if (empty($less)) {
            $less = azh_mynx_init_less();
        }
        if (isset($projects[$project])) {
            $scripts[$path]['css'][] = $projects[$project];
        } else {
            if (file_exists(untrailingslashit(dirname(__FILE__)) . '/less/' . $project . '-skin.less')) {
                wp_register_style('azh-mynx-skin-' . $project, plugins_url('', __FILE__) . '/less/' . $project . '-skin.less', array('azh-extension-skin'));
                $stylesheet = $less->processStylesheet('azh-mynx-skin-' . $project);
                $scripts[$path]['css'][] = $stylesheet->getTargetUri();
                $projects[$project] = $stylesheet->getTargetUri();
                wp_deregister_style('azh-mynx-skin-' . $project);
            }
        }
    } else {
        if (isset($projects[$project])) {
            $scripts[$path]['css'][] = $projects[$project];
        } else {
            if (file_exists(untrailingslashit(dirname(__FILE__)) . '/css/' . $project . '-skin.css')) {
                $url = plugins_url('', __FILE__) . '/css/' . $project . '-skin.css';
                $scripts[$path]['css'][] = $url;
                $projects[$project] = $url;
            }
        }
    }
}

add_filter('azh_get_files_scripts', 'azh_mynx_get_files_scripts', 10, 2);

function azh_mynx_get_files_scripts($scripts, $library) {
    $projects = array();
    foreach ($library['elements'] as $abs_path => $name) {
        $path = ltrim(str_replace($library['elements_dir'][$abs_path], '', $abs_path), '/');
        azh_mynx_get_file_scripts($scripts, $path, $projects);
    }
    foreach ($library['sections'] as $abs_path => $name) {
        $path = ltrim(str_replace($library['sections_dir'][$abs_path], '', $abs_path), '/');
        azh_mynx_get_file_scripts($scripts, $path, $projects);
    }
    return $scripts;
}

add_filter('azh_get_content_scripts', 'azh_mynx_get_content_scripts');

function azh_mynx_get_content_scripts($post_scripts) {
    static $projects_enqueued = array();
    if (isset($post_scripts['paths'])) {
        $projects = array();
        if (file_exists(untrailingslashit(dirname(__FILE__)) . '/less/' . get_template() . '-skin.less')) {
            $projects[get_template()] = true;
        }
        if (function_exists('azexo_get_skin')) {
            if (file_exists(untrailingslashit(dirname(__FILE__)) . '/less/' . azexo_get_skin() . '-skin.less')) {
                $projects[azexo_get_skin()] = true;
            }
        }
        foreach ($post_scripts['paths'] as $section_element => $path) {
            $folders = explode('/', $section_element);
            $project = reset($folders);
            if (file_exists(untrailingslashit(dirname(__FILE__)) . '/less/' . $project . '-skin.less')) {
                $projects[$project] = true;
            }
        }
        if (!empty($projects)) {
            if (class_exists('WPLessPlugin')) {
                $less = azh_mynx_init_less();
                foreach ($projects as $project => $flag) {
                    if (!isset($projects_enqueued[$project])) {
                        if (file_exists(untrailingslashit(dirname(__FILE__)) . '/less/' . $project . '-skin.less')) {
                            wp_register_style('azh-mynx-skin-' . $project, plugins_url('', __FILE__) . '/less/' . $project . '-skin.less', array('azh-mynx-skin'));
                            $stylesheet = $less->processStylesheet('azh-mynx-skin-' . $project);
                            $post_scripts['css'][] = $stylesheet->getTargetUri();
                            $projects_enqueued[$project] = true;
                            wp_deregister_style('azh-extension-skin-' . $project);
                        }
                    }
                }
            } else {
                foreach ($projects as $project => $flag) {
                    if (!isset($projects_enqueued[$project])) {
                        if (file_exists(untrailingslashit(dirname(__FILE__)) . '/css/' . $project . '-skin.css')) {
                            $url = plugins_url('', __FILE__) . '/css/' . $project . '-skin.css';
                            //wp_enqueue_style('azh-mynx-skin-' . $project, $url, array('azh-mynx-skin'));
                            $post_scripts['css'][] = $url;
                            $projects_enqueued[$project] = true;
                        }
                    }
                }
            }
        }
    }

    return $post_scripts;
}

function azh_mynx_get_colors() {
    global $post;
    $brand_color = '#FF0000';
    $accent_1_color = '#00FF00';
    $accent_2_color = '#0000FF';
    
    
    if (defined('AZEXO_FRAMEWORK')) {
        $options = get_option(AZEXO_FRAMEWORK);
        if (!empty($options['brand-color'])) {
            $brand_color = $options['brand-color'];
        }
        if (!empty($options['accent-1-color'])) {
            $accent_1_color = $options['accent-1-color'];
        }
        if (!empty($options['accent-2-color'])) {
            $accent_2_color = $options['accent-2-color'];
        }
    } else {
        $settings = get_option('azh-settings');
        if (!empty($settings['brand-color'])) {
            $brand_color = $settings['brand-color'];
        }
        if (!empty($settings['accent-1-color'])) {
            $accent_1_color = $settings['accent-1-color'];
        }
        if (!empty($settings['accent-2-color'])) {
            $accent_2_color = $settings['accent-2-color'];
        }
    }
    if ($post) {
        $color = sanitize_hex_color(get_post_meta($post->ID, '_brand-color', true));
        if (!empty($color)) {
            $brand_color = $color;
        }
        $color = sanitize_hex_color(get_post_meta($post->ID, '_accent-1-color', true));
        if (!empty($color)) {
            $accent_1_color = $color;
        }
        $color = sanitize_hex_color(get_post_meta($post->ID, '_accent-2-color', true));
        if (!empty($color)) {
            $accent_2_color = $color;
        }
    }
    return array(
        'brand_color' => $brand_color,
        'accent_1_color' => $accent_1_color,
        'accent_2_color' => $accent_2_color
    );
}

function azh_mynx_add_less_variable($less, $name, $default = '', $units = '', $validation = false) {
    global $post;
    $settings = get_option('azh-settings');
    $value = $default;
    if (isset($settings[$name])) {
        $value = $settings[$name];
    }
    if ($post) {
        if (get_post_meta($post->ID, '_' . $name, true)) {
            $value = get_post_meta($post->ID, '_' . $name, true);
        }
    }
    if ($validation !== false) {
        $value = $validation($value);
    }
    $less->addVariable($name, $value . $units);
}

add_filter('azh_post_google_fonts', 'azh_mynx_post_google_fonts', 10, 2);

function azh_mynx_post_google_fonts($google_fonts, $post) {
    if (get_post_meta($post->ID, '_main-google-font', true)) {
        $google_fonts[] = get_post_meta($post->ID, '_main-google-font', true);
    }
    if (get_post_meta($post->ID, '_menu-google-font', true)) {
        $google_fonts[] = get_post_meta($post->ID, '_menu-google-font', true);
    }
    if (get_post_meta($post->ID, '_header-google-font', true)) {
        $google_fonts[] = get_post_meta($post->ID, '_header-google-font', true);
    }
    return $google_fonts;
}


function azh_mynx_set_less_variables() {
    if (class_exists('WPLessPlugin')) {
        global $post;
        $settings = get_option('azh-settings');
        $less = WPLessPlugin::getInstance();
        $colors = azh_mynx_get_colors();
        extract($colors);
        if ($brand_color) {
            $less->addVariable('brand-color', $brand_color);
        }
        if ($accent_1_color) {
            $less->addVariable('accent-1-color', $accent_1_color);
        }
        if ($accent_2_color) {
            $less->addVariable('accent-2-color', $accent_2_color);
        }

        azh_mynx_add_less_variable($less, 'main-google-font', 'Work Sans');
        azh_mynx_add_less_variable($less, 'main-border-color');
        azh_mynx_add_less_variable($less, 'main-border-radius', '4', 'px');
        azh_mynx_add_less_variable($less, 'main-border-width', '1', 'px');
        azh_mynx_add_less_variable($less, 'control-border-width', '2', 'px');
        azh_mynx_add_less_variable($less, 'main-shadow-color');

        azh_mynx_add_less_variable($less, 'menu-height', '120', 'px');
        azh_mynx_add_less_variable($less, 'menu-google-font', 'Work Sans');
        azh_mynx_add_less_variable($less, 'menu-font-color', '#000000');
        azh_mynx_add_less_variable($less, 'menu-font-size', '32', 'px');
        azh_mynx_add_less_variable($less, 'menu-font-weight', '600');
        
        azh_mynx_add_less_variable($less, 'header-google-font', 'Work Sans');
        azh_mynx_add_less_variable($less, 'header-color');
        azh_mynx_add_less_variable($less, 'header-font-size', '32', 'px');
        azh_mynx_add_less_variable($less, 'header-line-height', '1.45', '', function($value) {
            if ($value > 3) {
                return '1.45';
            }
            return $value;
        });
        azh_mynx_add_less_variable($less, 'header-font-weight', '400');

        azh_mynx_add_less_variable($less, 'paragraph-color');
        azh_mynx_add_less_variable($less, 'paragraph-font-size', '18', 'px');
        azh_mynx_add_less_variable($less, 'paragraph-line-height', '1.45', '', function($value) {
            if ($value > 3) {
                return '1.45';
            }
            return $value;
        });
        azh_mynx_add_less_variable($less, 'paragraph-font-weight', '300');
        azh_mynx_add_less_variable($less, 'paragraph-bold-weight', '600');


        if (function_exists('azh_get_google_fonts')) {
            $google_fonts = azh_get_google_fonts(azh_get_all_settings());
            if (is_array($google_fonts)) {
                foreach ($google_fonts as $font_family => $weights) {
                    $less->addVariable('google-font-family-' . str_replace('+', '-', $font_family), str_replace('+', ' ', $font_family));
                }
            }
        }
        if (class_exists('Less_Colors')) {
            Less_Colors::$colors = array();
        }
    }
}

add_action('admin_enqueue_scripts', 'azh_mynx_admin_scripts');

function azh_mynx_admin_scripts() {
    if (isset($_GET['azh']) && $_GET['azh'] == 'customize') {
        wp_enqueue_style('azh-extension-admin-frontend', plugins_url('css/admin-frontend.css', __FILE__));
        wp_enqueue_script('azh-extension-admin-frontend', plugins_url('js/admin-frontend.js', __FILE__), array('azh_admin_frontend'), false, true);
        wp_enqueue_script('azh-extension-frontend-customization-options', plugins_url('frontend-customization-options.js', __FILE__), array(), false, true);
    }
}

add_action('wp_enqueue_scripts', 'azh_mynx_scripts', 1000);

function azh_mynx_scripts() {
    $skin_style = false;
    if (file_exists(untrailingslashit(dirname(__FILE__)) . '/css/skin.css')) {
        $skin_style = plugins_url('', __FILE__) . '/css/skin.css';
    }


    if (class_exists('WPLessPlugin')) {
        $less = WPLessPlugin::getInstance();
        azh_mynx_set_less_variables();
        $less->dispatch();
        if (file_exists(untrailingslashit(dirname(__FILE__)) . '/less/skin.less')) {
            $skin_style = plugins_url('', __FILE__) . '/less/skin.less';
        }
    }

    if (!empty($skin_style)) {
        wp_enqueue_style('azh-mynx-skin', $skin_style);
    }


    wp_enqueue_script('flexslider', plugins_url('js/jquery.flexslider.js', __FILE__), array('jquery'), false, true);
    wp_enqueue_script('azh-owl.carousel', plugins_url('js/owl.carousel.js', __FILE__), array('jquery'), false, true);
    wp_enqueue_script('knob', plugins_url('js/jquery.knob.js', __FILE__), array('jquery'), false, true);
    wp_enqueue_script('jquery-fitvids', plugins_url('js/jquery.fitvids.js', __FILE__), array('jquery'), false, true);
    wp_enqueue_script('azh-extension-frontend', plugins_url('js/frontend.js', __FILE__), array('jquery', 'flexslider', 'isotope', 'azh-owl.carousel', 'imagesloaded'), false, true);
    if (isset($_GET['azh']) && $_GET['azh'] == 'customize') {
        wp_enqueue_style('azh-extension-admin-frontend', plugins_url('css/admin-frontend.css', __FILE__));
        wp_enqueue_script('azh-extension-admin-frontend', plugins_url('js/admin-frontend.js', __FILE__), array('azh_admin_frontend'), false, true);
        wp_enqueue_script('azh-extension-frontend-customization-options', plugins_url('frontend-customization-options.js', __FILE__), array(), false, true);
    }
    if (isset($_GET['azh']) && $_GET['azh'] == 'fullpage') {
        wp_enqueue_style('fullpage', plugins_url('css/jquery.fullpage.css', __FILE__), array(), null);
        wp_enqueue_script('fullpage', plugins_url('js/jquery.fullpage.js', __FILE__), array('jquery', 'jquery-effects-core'), false, true);
    }
}

add_filter('azh_directory', 'azh_mynx_directory');

function azh_mynx_directory($dir) {
    $settings = get_option('azh-settings');
    if (empty($settings['azh-uri'])) {
        $dir[untrailingslashit(dirname(__FILE__)) . '/azh'] = plugins_url('', __FILE__) . '/azh';
    } else {
        $dir[untrailingslashit(dirname(__FILE__)) . '/azh'] = $settings['azh-uri'];
    }
    return $dir;
}

add_filter('azh_replaces', 'azh_mynx_replaces');

function azh_mynx_replaces($replaces) {
    return $replaces;
}

add_filter('azh_settings_sanitize_callback', 'azh_mynx_settings_sanitize_callback');

function azh_mynx_settings_sanitize_callback($input) {
    if (!file_exists(dirname(__FILE__) . '/azh_settings.json') && function_exists('azh_filesystem')) {
        azh_filesystem();
        global $wp_filesystem;
        $wp_filesystem->put_contents(dirname(__FILE__) . '/azh_settings.json', json_encode($input), FS_CHMOD_FILE);
    }
    return $input;
}

add_filter('azh_get_object', 'azh_mynx_get_object');

function azh_mynx_get_object($azh) {
    global $post;
    $colors = azh_mynx_get_colors();
    extract($colors);

    $azh['brand_color'] = $brand_color;
    $azh['accent_1_color'] = $accent_1_color;
    $azh['accent_2_color'] = $accent_2_color;
    $azh['cloneable_refresh'][] = '.az-slides';
    $azh['cloneable_refresh'][] = '.az-flex-thumbnails';
    $azh['cloneable_refresh'][] = '.az-carousel';
    $azh['cloneable_refresh'][] = '[data-masonry-items]';
    $azh['cloneable_refresh'][] = '[data-isotope-items]';
    $azh['cloneable_refresh'][] = '[data-isotope-filters]';
    $azh['i18n']['please_wait_page_reload'] = esc_html__('Please wait page reload', 'azh');
    $azh['i18n']['accent_colors'] = esc_html__('Accent colors', 'azh');
    $azh['i18n']['brand_color'] = esc_html__('Brand color', 'azh');
    $azh['i18n']['accent_1_color'] = esc_html__('Accent 1 color', 'azh');
    $azh['i18n']['accent_2_color'] = esc_html__('Accent 2 color', 'azh');
    return $azh;
}

add_filter('azh_default_category', 'azh_mynx_default_category', 11);

function azh_mynx_default_category($default_category) {
    return 'mynx';
}

function azh_mynx_parse_css($css) {
    $results = array();
    preg_match_all("/([\w-]+)\s*:\s*([^;]+)\s*;?/", $css, $matches, PREG_SET_ORDER);
    foreach ($matches as $match) {
        $results[$match[1]] = $match[2];
    }

    return $results;
}

function azh_mynx_parse_hex_color($string) {
    $hex = str_replace("#", "", $string);
    if (strlen($hex) == 3) {
        $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
        $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
        $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
    } else {
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
    }
    $rgba = array($r, $g, $b, 1);
    return $rgba;
}

function azh_mynx_parse_rgba_color($string) {
    $rgba = array(0 => 0, 1 => 0, 2 => 0, 3 => 1);
    $i = 0;
    preg_match_all('/[0-9.]+/', $string, $color);
    foreach ($rgba as $key => $value) {
        $rgba[$key] = $color[0][$i++] ?: 1;
    }
    return $rgba;
}

function azh_mynx_parse_color($string) {
    $rgba = false;
    if (strpos($string, 'rgb') !== false) {
        $rgba = azh_mynx_parse_rgba_color($string);
    }
    if (strpos($string, '#') !== false) {
        $rgba = azh_mynx_parse_hex_color($string);
    }
    return $rgba;
}

function azh_mynx_rgb2hsl($rgb) {
    $clrR = ($rgb[0]);
    $clrG = ($rgb[1]);
    $clrB = ($rgb[2]);

    $clrMin = min($clrR, $clrG, $clrB);
    $clrMax = max($clrR, $clrG, $clrB);
    $deltaMax = $clrMax - $clrMin;

    $L = ($clrMax + $clrMin) / 510;

    if (0 == $deltaMax) {
        $H = 0;
        $S = 0;
    } else {
        if (0.5 > $L) {
            $S = $deltaMax / ($clrMax + $clrMin);
        } else {
            $S = $deltaMax / (510 - $clrMax - $clrMin);
        }

        if ($clrMax == $clrR) {
            $H = ($clrG - $clrB) / (6.0 * $deltaMax);
        } else if ($clrMax == $clrG) {
            $H = 1 / 3 + ($clrB - $clrR) / (6.0 * $deltaMax);
        } else {
            $H = 2 / 3 + ($clrR - $clrG) / (6.0 * $deltaMax);
        }

        if (0 > $H)
            $H += 1;
        if (1 < $H)
            $H -= 1;
    }
    return array($H, $S, $L);
}

function azh_mynx_colors_distance($rgba1, $rgba2) {
    if ($rgba1 && $rgba2) {
        $hsl1 = azh_mynx_rgb2hsl($rgba1);
        $hsl2 = azh_mynx_rgb2hsl($rgba2);
        return abs($hsl1[0] - $hsl2[0]) * 3 + abs($hsl1[1] - $hsl2[1]) + abs($hsl1[2] - $hsl2[2]) + abs($rgba1[3] - $rgba2[3]) * 2;
    }
    return false;
}

function azh_mynx_get_closest_color($rgba1, $colors) {
    $min_name = false;
    $min_distance = false;
    foreach ($colors as $name => $rgba2) {
        $distance = azh_mynx_colors_distance(azh_mynx_parse_color($rgba1), azh_mynx_parse_color($rgba2));
        if ($distance !== false && $distance < 1) {
            if ($min_distance === false) {
                $min_distance = $distance;
                $min_name = $name;
            } else {
                if ($min_distance > $distance) {
                    $min_distance = $distance;
                    $min_name = $name;
                }
            }
        }
    }
    return $min_name;
}

function azh_mynx_add_class($tag, $class) {
    $classes = '';
    if ($tag->class) {
        $classes = $tag->class;
    }
    $classes = explode(' ', $classes);
    $classes[] = $class;
    $classes = array_unique($classes);
    $classes = implode(' ', $classes);
    $tag->class = $classes;
}

function azh_mynx_remove_class($tag, $class) {
    $classes = '';
    if ($tag->class) {
        $classes = $tag->class;
    }
    $classes = explode(' ', $classes);
    $classes = array_diff($classes, array($class));
    $classes = implode(' ', $classes);
    $tag->class = $classes;
}

function azh_mynx_process_colors($tag, &$styles, &$fail_counters) {
    $output = '';
    $properties_colors = array(
        'background-color' => array(
            'azen-mynx-bg-color' => '#ebefff',
        ),
        'border-color' => array(
            'azen-mynx-border-color' => '#ebedf5',
            'azen-mynx-border-alpha-color' => 'rgba(164, 178, 234, 0.15)',
            'azen-mynx-border-body-alpha-color' => 'rgba(113, 120, 148, 0.15)',
        ),
        'color' => array(
            'azen-white-darker' => '#e5e5e5',
            'azen-white-dark' => '#f0f0f0',
            'azen-white' => '#f5f5f5',
            'azen-white-light' => '#f8f8f8',
            'azen-white-lighter' => '#ffffff',
            'azen-gray-darker' => '#999999',
            'azen-gray-dark' => '#aaaaaa',
            'azen-gray' => '#bbbbbb',
            'azen-gray-light' => '#cccccc',
            'azen-gray-lighter' => '#dddddd',
            'azen-black-darker' => '#000000',
            'azen-black-dark' => '#222222',
            'azen-black' => '#333333',
            'azen-black-light' => '#666666',
            'azen-black-lighter' => '#888888',
            'azen-mynx-lighten1-color' => '#707070',
            'azen-mynx-lighten2-color' => '#7691f2',
            'azen-mynx-lighten3-color' => '#96aaf5',
            'azen-mynx-lighten5-color' => '#f7f9fe',
            'azen-mynx-heading-color' => '#1f2333',
            'azen-mynx-body-color' => '#717894',
            'azen-mynx-caret-color' => '#9ca5c9',
        )
    );
    foreach ($properties_colors as $property => $colors) {
        if (isset($styles[$property])) {
            $output .= '<td>' . $property . '</td>';
            $output .= '<td>' . $styles[$property] . '</td>';
            $closest_color = azh_mynx_get_closest_color($styles[$property], $colors);
            if ($closest_color !== false) {
                azh_mynx_add_class($tag, $closest_color);
                unset($styles[$property]);

                $output .= '<td>' . $colors[$closest_color] . '</td>';
                $output .= '<td style="color: white; background-color: green">' . $closest_color . '</td>';
            } else {
                $fail_counters[$property][$styles[$property]] ++;
                $output .= '<td></td>';
                $output .= '<td style="background-color: red"></td>';
            }
        }
    }
    return $output;
}

function azh_mynx_process_font_family($tag, &$styles) {
    $font_family_class = 'azen-mynx-font-family';
    if (isset($styles['font-family'])) {
        azh_mynx_add_class($tag, $font_family_class);
        unset($styles['font-family']);
    }
}

function azh_mynx_process_header_tag($html) {
    $output = '';
    foreach ($html->find('div.azen-header') as $tag) {
        if (isset($tag->class)) {
            if (in_array('az-text', explode(' ', $tag->class))) {
                $output .= '<td>' . $tag->innertext . '</td>';
                $tag->outertext = str_replace('</div>', '</h2>', $tag->outertext);
                $tag->outertext = str_replace('<div', '<h2', $tag->outertext);
            }
        }
    }
    return $output;
}

function azh_mynx_process_header($tag, &$styles) {
    $output = '';
    if (isset($styles['font-size']) && isset($styles['line-height']) && isset($styles['font-weight'])) {
        if (intval($styles['font-size']) > 30 && intval($styles['font-size']) < 40) {
            $output .= '<td>' . $tag->innertext . '</td>';

            azh_mynx_add_class($tag, 'azen-header');
            unset($styles['font-size']);
            unset($styles['line-height']);
            unset($styles['font-weight']);
        }
    }
    return $output;
}

function azh_mynx_process_first_header($tag, &$styles) {
    $output = '';
    if (isset($styles['font-size']) && isset($styles['line-height']) && isset($styles['font-weight'])) {
        if (intval($styles['font-size']) >= 40) {
            azh_mynx_add_class($tag, 'azen-header');
            azh_mynx_remove_class($tag, 'azen-mynx-font-family');
            $tag->outertext = str_replace('</div>', '</h1>', $tag->outertext);
            $tag->outertext = str_replace('<div', '<h1', $tag->outertext);
            $output .= '<td>' . $tag->outertext . '</td>';
        }
    }
    return $output;
}

function azh_mynx_process_paragraph($tag, &$styles) {
    $output = '';
    $header_class = 'azen-paragraph';
    if (isset($styles['font-size']) && isset($styles['line-height']) && isset($styles['font-weight'])) {
        if (intval($styles['font-size']) > 15 && intval($styles['font-size']) < 20 && intval($styles['font-weight']) == 300) {
            $output .= '<td>' . $tag->innertext . '</td>';

            azh_mynx_add_class($tag, $header_class);
            unset($styles['font-size']);
            unset($styles['line-height']);
            unset($styles['font-weight']);
        }
    }
    return $output;
}

function azh_mynx_process_styles($html) {
    $output = '';
    foreach ($html->find('[style]') as $tag) {
        $style = (string) $tag->style;
        $styles = azh_mynx_parse_css($style);

        //azh_mynx_process_font_family($tag, $styles)
        //$output .= azh_mynx_process_header($tag, $styles);
        //$output .= azh_mynx_process_paragraph($tag, $styles);
        //$output .= azh_mynx_process_colors($tag, $styles, $fail_counters);
        $output .= azh_mynx_process_first_header($tag, $styles);

        $style = '';
        foreach ($styles as $property => $value) {
            $style .= $property . ': ' . $value . '; ';
        }
        $tag->style = $style;
    }
    return $output;
}

function azh_mynx_process_img_alt($html) {
    foreach ($html->find('img') as $tag) {
        $tag->alt = '';
    }
}

add_shortcode('azh_mynx_process_library', 'azh_mynx_process_library');

function azh_mynx_process_library($atts, $content = null, $tag = null) {
    if (function_exists('azh_filesystem') && isset($atts['project'])) {
        azh_filesystem();
        global $wp_filesystem;
        include_once(AZH_DIR . 'simple_html_dom.php' );
        $dirs = apply_filters('azh_directory', array_combine(array(get_template_directory() . '/azh'), array(get_template_directory_uri() . '/azh')));
        $output = '<table style="width: 100%; table-layout: auto;">';
        if (is_array($dirs)) {
            foreach ($dirs as $dir => $uri) {
                if (is_dir($dir)) {
                    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir), RecursiveIteratorIterator::SELF_FIRST);
                    foreach ($iterator as $fileInfo) {
                        if (strpos($fileInfo->getPath(), $atts['project']) !== false) {
                            if ($fileInfo->isFile() && ($fileInfo->getExtension() == 'html' || $fileInfo->getExtension() == 'htm')) {
                                $source = $wp_filesystem->get_contents($fileInfo->getPathname());
                                $html = str_get_html($source, true, true, 'UTF-8', false);
                                if ($html) {
                                    $output .= '<tr>';
                                    $output .= '<td><a href="http://azexo.com/?azh=library&files=mynx/' . $fileInfo->getBasename() . '" target="_blank">' . $fileInfo->getBasename() . '</a></td>';

                                    //$output .= azh_mynx_process_styles($html);

                                    $source = $html->save();
                                    $output .= '</tr>';
                                    if ($atts['save']) {
                                        $source = $wp_filesystem->put_contents($fileInfo->getPathname(), $source);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    $output .= '</table>';
    return $output;
}

function azh_mynx_process_html5($html) {
    //CSS: background-type: Property background-type doesn't exist.
    foreach ($html->find('[style]') as $tag) {
        $style = (string) $tag->style;
        $styles = azh_mynx_parse_css($style);

        unset($styles['background-type']);

        $style = '';
        foreach ($styles as $property => $value) {
            $style .= $property . ': ' . $value . '; ';
        }
        $tag->style = $style;
    }
    //The font element is obsolete. Use CSS instead.
    foreach ($html->find('font') as $tag) {
        $style = '';
        if ($tag->color) {
            $style .= 'color: ' . $tag->color . '; ';
            unset($tag->color);
        }
        if ($tag->size) {
            unset($tag->size);
        }
        $tag->style = $style;
        $tag->outertext = str_replace('</font>', '</span>', $tag->outertext);
        $tag->outertext = str_replace('<font', '<span', $tag->outertext);
    }
    //An img element must have an alt attribute, except under certain conditions. For details, consult guidance on providing text alternatives for images.
    foreach ($html->find('img') as $tag) {
        $tag->alt = '';
    }
    //Bad value  for attribute maxlength on element input: The empty string is not a valid non-negative integer.
    foreach ($html->find('[maxlength]') as $tag) {
        $tag->maxlength = '100';
    }
    //Bad value  for attribute max on element input: The empty string is not a valid floating point number.
    foreach ($html->find('[max]') as $tag) {
        $tag->max = '100';
    }
}

function azh_mynx_process_pages() {
    $pages = get_posts(array(
        'post_type' => 'page',
        'post_status' => 'publish',
        'ignore_sticky_posts' => 1,
        'no_found_rows' => 1,
        'posts_per_page' => -1,
        'numberposts' => -1,
        'meta_query' => array(
            array(
                'key' => '_azh_content',
                'compare' => 'EXISTS',
            ),
            array(
                'key' => 'azh',
                'value' => 'azh',
            )
        )
    ));
    include_once(AZH_DIR . 'simple_html_dom.php' );
    foreach ($pages as $page) {
        $content = azh_get_post_content($page);
        $html = str_get_html($content, true, true, 'UTF-8', false);
        if ($html) {
            azh_mynx_process_html5($html);
            $content = $html->save();
            azh_set_post_content($content, $page->ID);
        }
    }
}

add_filter('pt-ocdi/import_files', 'azh_mynx_one_click_demo_import');

function azh_mynx_one_click_demo_import($import_files) {
    $response = wp_remote_get('http://www.azexo.com/mynx/demos.json');
    if (is_array($response)) {
        $demos = json_decode($response['body'], true);
        if($demos) {
            $import_files = array_merge($import_files, $demos);
        }
    }
    $response = wp_remote_get('http://www.azexo.com/' . get_template() . '/demos.json');
    if (is_array($response)) {
        $demos = json_decode($response['body'], true);
        if ($demos) {
            $import_files = $demos;
        }
    }
    return $import_files;
}