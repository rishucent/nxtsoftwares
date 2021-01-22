<?php

add_filter('pt-ocdi/after_all_import_execution', 'azexo_ocdi_after_import', 10, 3);

function azexo_ocdi_after_import($import_files, $predefined_import_files, $predefined_index) {
    $demo = $predefined_import_files[$predefined_index];
    if (isset($demo['header_url'])) {
        $response = wp_remote_get($demo['header_url']);
        if (is_array($response)) {
            $header = azh_uri_replace($response['body']);
            $azh_wxr_importer_url_remap = get_option('azh_wxr_importer_url_remap', array());
            $header = str_replace(array_keys($azh_wxr_importer_url_remap), $azh_wxr_importer_url_remap, $header);
            
            if (!preg_match('/ data-section=[\'"]([^\'"]+)[\'"]/i', $header)) {
                $header = '<div data-section="header">' . $header . '</div>';
            }

            $header = azexo_create_azh_widget('header', $header);
            if ($header) {
                $sidebars = get_option('sidebars_widgets');
                if (!$sidebars) {
                    $sidebars = array();
                }
                $sidebars['header_sidebar'] = array();
                update_option('sidebars_widgets', $sidebars);


                azexo_pre_set_widget('header_sidebar', 'azh_widget', array('post' => $header));
                $options = get_option(AZEXO_FRAMEWORK);
                $options['header'] = array();
                $options['show_page_title'] = false;
                update_option(AZEXO_FRAMEWORK, $options);
                update_option('azexo_header_footer_installed', true);
            }
        }
    }
    if (isset($demo['footer_url'])) {
        $response = wp_remote_get($demo['footer_url']);
        if (is_array($response)) {
            $footer = azh_uri_replace($response['body']);
            $azh_wxr_importer_url_remap = get_option('azh_wxr_importer_url_remap', array());
            $footer = str_replace(array_keys($azh_wxr_importer_url_remap), $azh_wxr_importer_url_remap, $footer);
            
            if (!preg_match('/ data-section=[\'"]([^\'"]+)[\'"]/i', $footer)) {
                $footer = '<div data-section="footer">' . $footer . '</div>';
            }
            
            $footer = azexo_create_azh_widget('footer', $footer);
            if ($footer) {
                $sidebars = get_option('sidebars_widgets');
                if (!$sidebars) {
                    $sidebars = array();
                }
                $sidebars['footer_sidebar'] = array();
                update_option('sidebars_widgets', $sidebars);

                azexo_pre_set_widget('footer_sidebar', 'azh_widget', array('post' => $footer));
                update_option('azexo_header_footer_installed', true);
            }
        }
    }
}

