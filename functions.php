<?php

if (!function_exists('swift_theme_support')) {
    /**
     * Register theme support for HTML5, <title> & feed links
     *
     * @return void
     */
    function swift_theme_support()
    {
        add_image_size('banner', 920, 280, true);

        add_theme_support('title-tag');
        add_theme_support('post-thumbnails');
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

        wp_enqueue_script('swift-app', $theme.'/assets/js/app.min.js', ['angularjs', 'angularjs-route', 'angularjs-sanitize']);

        wp_localize_script('swift-app', 'swift', [
            'root' => esc_url(home_url()),
            'templates' => $theme.'/templates',
            'site_name' => get_bloginfo('name')
        ]);

        wp_enqueue_style('swift-fonts', '//fonts.googleapis.com/css?family=Bitter:700|Lato');
        wp_enqueue_style('swift-css', get_stylesheet_uri());
    }
    add_action('wp_enqueue_scripts', 'swift_enqueue_assets');
}

if (!function_exists('swift_register_rest_fields')) {
    /**
     * Register extra field(s) for our REST endpoints
     *
     * @return void
     */
    function swift_register_rest_fields()
    {
        register_rest_field('post', 'swift', [
            'get_callback' => 'swift_register_rest_field_for_post',
            'schema' => null
        ]);
    }
    add_action('init', 'swift_register_rest_fields', 12);
}

if (!function_exists('swift_register_rest_field_for_post')) {
    /**
     * Register extra API field(s) for the post post-type
     *
     * @param array $object
     * @param string $field_name
     * @param WP_REST_Request $request
     * @return array
     */
    function swift_register_rest_field_for_post($object, $field_name, $request)
    {
        if (empty($object['featured_media']) || (!$image = get_post($object['featured_media']))) {
            return null;
        }

        $featured = [];

        $featured['id'] = (int)$image->ID;
        $featured['caption'] = $image->post_excerpt;
        $featured['description'] = $image->post_content;
        $featured['alt_text'] = get_post_meta($image->ID, '_wp_attachment_image_alt', true);

        $image = wp_get_attachment_image_src($object['featured_media'], 'banner');

        $featured['sizes']['banner'] = [
            'src' => $image[0],
            'width' => $image[1],
            'height' => $image[2]
        ];

        return ['featured_media' => $featured];
    }
}

























/**
 * Better REST API Featured Images
 *
 * @package             Better_REST_API_Featured_Images
 * @author              Braad Martin <wordpress@braadmartin.com>
 * @license             GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name:         Better REST API Featured Images
 * Plugin URI:          https://wordpress.org/plugins/better-rest-api-featured-images/
 * Description:         Enhances the featured image data returned on the post object by the REST API to include urls for all available sizes and other useful image data.
 * Version:             1.1.1
 * Author:              Braad Martin
 * Author URI:          http://braadmartin.com
 * License:             GPL-2.0+
 * License URI:         http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:         better-rest-api-featured-images
 * Domain Path:         /languages
 */

add_action( 'init', 'better_rest_api_featured_images_init', 12 );
/**
 * Register our enhanced better_featured_image field to all public post types
 * that support post thumbnails.
 *
 * @since  1.0.0
 */
function better_rest_api_featured_images_init() {

    $post_types = get_post_types( array( 'public' => true ), 'objects' );

    foreach ( $post_types as $post_type ) {

        $post_type_name     = $post_type->name;
        $show_in_rest       = ( isset( $post_type->show_in_rest ) && $post_type->show_in_rest ) ? true : false;
        $supports_thumbnail = post_type_supports( $post_type_name, 'thumbnail' );

        // Only proceed if the post type is set to be accessible over the REST API
        // and supports featured images.
        if ( $show_in_rest && $supports_thumbnail ) {

            // Compatibility with the REST API v2 beta 9+
            if ( function_exists( 'register_rest_field' ) ) {
                register_rest_field( $post_type_name,
                    'better_featured_image',
                    array(
                        'get_callback' => 'better_rest_api_featured_images_get_field',
                        'schema'       => null,
                    )
                );
            } elseif ( function_exists( 'register_api_field' ) ) {
                register_rest_field( $post_type_name,
                    'better_featured_image',
                    array(
                        'get_callback' => 'better_rest_api_featured_images_get_field',
                        'schema'       => null,
                    )
                );
            }
        }
    }
}

/**
 * Return the better_featured_image field.
 *
 * @since   1.0.0
 *
 * @return  object|null
 */
function better_rest_api_featured_images_get_field( $object, $field_name, $request ) {

    // Only proceed if the post has a featured image.
    if ( ! empty( $object['featured_media'] ) ) {
        $image_id = (int)$object['featured_media'];
    } elseif ( ! empty( $object['featured_image'] ) ) {
        $image_id = (int)$object['featured_image'];
    } else {
        return null;
    }

    $image = get_post( $image_id );

    if ( ! $image ) {
        return null;
    }

    // This is taken from WP_REST_Attachments_Controller::prepare_item_for_response().
    $featured_image['id']            = $image_id;
    $featured_image['alt_text']      = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
    $featured_image['caption']       = $image->post_excerpt;
    $featured_image['description']   = $image->post_content;
    $featured_image['media_type']    = wp_attachment_is_image( $image_id ) ? 'image' : 'file';
    $featured_image['media_details'] = wp_get_attachment_metadata( $image_id );
    $featured_image['post']          = ! empty( $image->post_parent ) ? (int) $image->post_parent : null;
    $featured_image['source_url']    = wp_get_attachment_url( $image_id );

    if ( empty( $featured_image['media_details'] ) ) {
        $featured_image['media_details'] = new stdClass;
    } elseif ( ! empty( $featured_image['media_details']['sizes'] ) ) {
        $img_url_basename = wp_basename( $featured_image['source_url'] );
        foreach ( $featured_image['media_details']['sizes'] as $size => &$size_data ) {
            $image_src = wp_get_attachment_image_src( $image_id, $size );
            if ( ! $image_src ) {
                continue;
            }
            $size_data['source_url'] = $image_src[0];
        }
    } else {
        $featured_image['media_details']['sizes'] = new stdClass;
    }

    return apply_filters( 'better_rest_api_featured_image', $featured_image, $image_id );
}
