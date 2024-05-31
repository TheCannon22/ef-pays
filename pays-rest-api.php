<?php
/*
Plugin Name: Pays REST API
Description: Plugin pour générer un menu de pays et afficher les destinations via une API REST.
Version: 1.0
Author: Votre Nom
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Enqueue scripts and styles
function pays_enqueue_scripts() {
    wp_enqueue_style('pays-styles', plugin_dir_url(__FILE__) . 'css/styles.css');
    wp_enqueue_script('pays-script', plugin_dir_url(__FILE__) . 'js/pays.js', array('jquery'), null, true);

    // Pass API endpoint to JavaScript
    wp_localize_script('pays-script', 'paysApi', array(
        'root' => esc_url_raw(rest_url()),
        'nonce' => wp_create_nonce('wp_rest')
    ));
}
add_action('wp_enqueue_scripts', 'pays_enqueue_scripts');

// Register REST API endpoint
function register_pays_routes() {
    register_rest_route('pays/v1', '/destinations/', array(
        'methods' => 'GET',
        'callback' => 'get_pays_destinations',
    ));
}
add_action('rest_api_init', 'register_pays_routes');

function get_pays_destinations(WP_REST_Request $request) {
    $country = $request->get_param('country');
    $args = array(
        'post_type' => 'destination',
        'meta_query' => array(
            array(
                'key' => 'country',
                'value' => $country,
                'compare' => '='
            )
        )
    );
    $query = new WP_Query($args);
    $destinations = array();

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $destinations[] = array(
                'title' => get_the_title(),
                'content' => get_the_content(),
                'image' => get_the_post_thumbnail_url(get_the_ID(), 'full') ?: 'https://via.placeholder.com/150'
            );
        }
        wp_reset_postdata();
    }
    
    return new WP_REST_Response($destinations, 200);
}

// Shortcode for country menu and destinations
function pays_shortcode() {
    ob_start();
    ?>
    <div id="country-menu"></div>
    <div id="country-destinations"></div>
    <?php
    return ob_get_clean();
}
add_shortcode('pays_destinations', 'pays_shortcode');
?>
