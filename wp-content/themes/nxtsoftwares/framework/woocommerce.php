<?php

add_action('after_setup_theme', 'azexo_woo_after_setup_theme');

function azexo_woo_after_setup_theme() {
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
    add_theme_support('woocommerce');
    add_filter('woocommerce_show_page_title', '__return_false');
    remove_filter('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
}
