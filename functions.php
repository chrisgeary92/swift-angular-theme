<?php

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
        wp_enqueue_script('angularjs-router', '//ajax.googleapis.com/ajax/libs/angularjs/1.4.9/angular-route.min.js');

        wp_enqueue_script('swift-app', $theme.'/assets/js/app.js', ['angularjs', 'angular-route']);
        
        wp_localize_script('swift-app', 'swift', [
            'root' => esc_url(home_url()), 
            'templates' => $theme.'/templates/'
        ]);

        wp_enqueue_style('swift-css', get_stylesheet_uri());
    }
    add_action('wp_enqueue_scripts', 'swift_enqueue_assets');
}
