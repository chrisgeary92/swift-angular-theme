<?php

if (!function_exists('swift_theme_support')) {
    /**
     * Register theme support for HTML5, <title> & feed links
     *
     * @return void
     */
    function swift_theme_support()
    {
        add_theme_support('title-tag');
        add_theme_support('automatic-feed-links');
        add_theme_support('html5', ['comment-list', 'comment-form', 'search-form', 'gallery', 'caption']);
    }
    add_action('after_setup_theme', 'swift_theme_support');
}

if (!function_exists('swift_enqueue_assets')) {
    /**
     * Enqueue JavaScript & CSS assets. The WordPress way.
     *
     * @return void
     */
    function swift_enqueue_assets()
    {
        $theme = untrailingslashit(get_template_directory_uri());

        wp_enqueue_script('angularjs', '//ajax.googleapis.com/ajax/libs/angularjs/1.4.9/angular.min.js');
        wp_enqueue_script('angularjs-route', '//ajax.googleapis.com/ajax/libs/angularjs/1.4.9/angular-route.min.js');
        wp_enqueue_script('angularjs-sanitize', '//ajax.googleapis.com/ajax/libs/angularjs/1.4.9/angular-sanitize.min.js');

        wp_enqueue_script('swift-app', $theme.'/assets/js/app.js', ['angularjs', 'angularjs-route', 'angularjs-sanitize']);

        wp_localize_script('swift-app', 'swift', [
            'root' => esc_url(home_url()),
            'templates' => $theme.'/templates',
            'site_name' => get_bloginfo('name')
        ]);

        wp_enqueue_style('swift-css', get_stylesheet_uri());
    }
    add_action('wp_enqueue_scripts', 'swift_enqueue_assets');
}
