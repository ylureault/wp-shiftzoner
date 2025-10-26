<?php
/**
 * ShiftZoneR Theme Functions
 * 
 * @package ShiftZoneR
 * @version 1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// 1. CONFIGURATION DU THÈME
function shiftzoner_theme_setup() {
    add_theme_support( 'custom-logo', array( 'height' => 100, 'width' => 400, 'flex-height' => true, 'flex-width' => true ) );
    add_theme_support( 'post-thumbnails' );
    set_post_thumbnail_size( 1200, 800, true );
    add_image_size( 'shiftzoner-large', 1920, 1080, true );
    add_image_size( 'shiftzoner-medium', 800, 600, true );
    add_image_size( 'shiftzoner-thumb', 400, 300, true );
    add_theme_support( 'title-tag' );
    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'custom-background' );
    add_theme_support( 'custom-header' );
    register_nav_menus( array( 'primary' => __( 'Menu Principal', 'shiftzoner' ), 'footer' => __( 'Menu Footer', 'shiftzoner' ) ) );
    if ( function_exists( 'bp_is_active' ) ) add_theme_support( 'buddypress' );
}
add_action( 'after_setup_theme', 'shiftzoner_theme_setup' );

if ( ! isset( $content_width ) ) $content_width = 1200;

// 2. SCRIPTS & STYLES
function shiftzoner_enqueue_scripts() {
    wp_enqueue_style( 'shiftzoner-style', get_stylesheet_uri(), array(), '1.1.0' );
    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) wp_enqueue_script( 'comment-reply' );
    if ( is_page_template( 'page-carte.php' ) ) {
        wp_enqueue_style( 'leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', array(), '1.9.4' );
        wp_enqueue_script( 'leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', array(), '1.9.4', true );
    }
    wp_localize_script( 'jquery', 'shiftzoner_ajax', array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'nonce' => wp_create_nonce( 'shiftzoner_nonce' ) ) );
}
add_action( 'wp_enqueue_scripts', 'shiftzoner_enqueue_scripts' );

// 3. TAXONOMIES
function shiftzoner_register_taxonomies() {
    register_taxonomy( 'car_brand', 'car_photo', array( 'label' => 'Marques', 'public' => true, 'hierarchical' => true, 'show_ui' => true, 'show_in_rest' => true, 'show_admin_column' => true, 'rewrite' => array( 'slug' => 'marque' ) ) );
    register_taxonomy( 'car_model', 'car_photo', array( 'label' => 'Modèles', 'public' => true, 'hierarchical' => true, 'show_ui' => true, 'show_in_rest' => true, 'show_admin_column' => true, 'rewrite' => array( 'slug' => 'modele' ) ) );
    register_taxonomy( 'car_year', 'car_photo', array( 'label' => 'Années', 'public' => true, 'hierarchical' => false, 'show_ui' => true, 'show_in_rest' => true, 'show_admin_column' => true, 'rewrite' => array( 'slug' => 'annee' ) ) );
    register_taxonomy( 'photo_tag', 'car_photo', array( 'label' => 'Tags Photos', 'public' => true, 'hierarchical' => false, 'show_ui' => true, 'show_in_rest' => true, 'show_admin_column' => true, 'rewrite' => array( 'slug' => 'tag' ) ) );
}
add_action( 'init', 'shiftzoner_register_taxonomies' );

// 4. POST TYPES
function shiftzoner_register_post_types() {
    register_post_type( 'car_photo', array( 'label' => 'Photos Auto', 'public' => true, 'has_archive' => true, 'menu_icon' => 'dashicons-camera', 'supports' => array( 'title', 'editor', 'thumbnail', 'author', 'comments', 'excerpt' ), 'show_in_rest' => true, 'rewrite' => array( 'slug' => 'photo' ), 'taxonomies' => array( 'car_brand', 'car_model', 'car_year', 'photo_tag' ) ) );
}
add_action( 'init', 'shiftzoner_register_post_types' );

// 5. SYSTÈME DE VOTES
add_action( 'wp_ajax_szr_vote', 'shiftzoner_handle_vote' );
add_action( 'wp_ajax_nopriv_szr_vote', 'shiftzoner_handle_vote' );
function shiftzoner_handle_vote() {
    check_ajax_referer( 'shiftzoner_nonce', 'nonce' );
    if ( ! is_user_logged_in() ) wp_send_json_error( array( 'message' => 'Connexion requise' ) );
    
    $post_id = intval( $_POST['post_id'] ?? 0 );
    $vote_type = sanitize_text_field( $_POST['vote'] ?? '' );
    $user_id = get_current_user_id();
    
    if ( ! $post_id || ! in_array( $vote_type, array( 'up', 'down' ) ) ) wp_send_json_error( array( 'message' => 'Données invalides' ) );
    
    $user_votes = get_post_meta( $post_id, '_szr_user_votes', true ) ?: array();
    
    if ( isset( $user_votes[ $user_id ] ) ) {
        $old_vote = $user_votes[ $user_id ];
        unset( $user_votes[ $user_id ] );
        update_post_meta( $post_id, '_szr_user_votes', $user_votes );
        $current_score = (int) get_post_meta( $post_id, '_szr_vote_score', true );
        $new_score = $old_vote === 'up' ? $current_score - 1 : $current_score + 1;
        update_post_meta( $post_id, '_szr_vote_score', $new_score );
        wp_send_json_success( array( 'score' => $new_score, 'user_vote' => null ) );
    }
    
    $user_votes[ $user_id ] = $vote_type;
    update_post_meta( $post_id, '_szr_user_votes', $user_votes );
    $current_score = (int) get_post_meta( $post_id, '_szr_vote_score', true );
    $new_score = $vote_type === 'up' ? $current_score + 1 : $current_score - 1;
    update_post_meta( $post_id, '_szr_vote_score', $new_score );
    
    $author_id = get_post_field( 'post_author', $post_id );
    $karma = (int) get_user_meta( $author_id, '_szr_karma', true );
    $new_karma = $vote_type === 'up' ? $karma + 1 : $karma - 1;
    update_user_meta( $author_id, '_szr_karma', $new_karma );
    
    wp_send_json_success( array( 'score' => $new_score, 'user_vote' => $vote_type, 'karma' => $new_karma ) );
}

// 6. COULEUR UTILISATEUR
add_action( 'show_user_profile', 'shiftzoner_user_color_field' );
add_action( 'edit_user_profile', 'shiftzoner_user_color_field' );
function shiftzoner_user_color_field( $user ) {
    $color = get_user_meta( $user->ID, '_szr_user_color', true ) ?: '#E50914';
    echo '<h3>ShiftZoneR</h3><table class="form-table"><tr><th><label for="szr_user_color">Couleur personnelle</label></th><td><input type="color" name="szr_user_color" id="szr_user_color" value="' . esc_attr( $color ) . '" /><p class="description">Votre couleur personnelle sur le site.</p></td></tr></table>';
}

add_action( 'personal_options_update', 'shiftzoner_save_user_color' );
add_action( 'edit_user_profile_update', 'shiftzoner_save_user_color' );
function shiftzoner_save_user_color( $user_id ) {
    if ( current_user_can( 'edit_user', $user_id ) && isset( $_POST['szr_user_color'] ) ) {
        update_user_meta( $user_id, '_szr_user_color', sanitize_hex_color( $_POST['szr_user_color'] ) );
    }
}

add_action( 'user_register', 'shiftzoner_assign_random_color' );
function shiftzoner_assign_random_color( $user_id ) {
    $colors = array( '#E50914', '#00AEEF', '#667eea', '#764ba2', '#f093fb', '#4facfe', '#43e97b', '#fa709a' );
    update_user_meta( $user_id, '_szr_user_color', $colors[ array_rand( $colors ) ] );
}

// 7. CUSTOMIZER
function shiftzoner_customize_register( $wp_customize ) {
    $wp_customize->add_section( 'shiftzoner_homepage', array( 'title' => 'Page d\'accueil ShiftZoneR', 'priority' => 30 ) );
    $wp_customize->add_setting( 'shiftzoner_hero_title', array( 'default' => 'Partagez Votre Passion Automobile', 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'postMessage' ) );
    $wp_customize->add_control( 'shiftzoner_hero_title', array( 'label' => 'Titre Hero', 'section' => 'shiftzoner_homepage', 'type' => 'text' ) );
    $wp_customize->add_setting( 'shiftzoner_hero_subtitle', array( 'default' => 'Rejoignez la communauté ShiftZoneR.', 'sanitize_callback' => 'sanitize_textarea_field', 'transport' => 'postMessage' ) );
    $wp_customize->add_control( 'shiftzoner_hero_subtitle', array( 'label' => 'Sous-titre Hero', 'section' => 'shiftzoner_homepage', 'type' => 'textarea' ) );
    $wp_customize->add_setting( 'shiftzoner_rafael_title', array( 'default' => 'Photos de Rafael', 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'postMessage' ) );
    $wp_customize->add_control( 'shiftzoner_rafael_title', array( 'label' => 'Titre Section Rafael', 'section' => 'shiftzoner_homepage', 'type' => 'text' ) );
    $wp_customize->add_setting( 'shiftzoner_rafael_subtitle', array( 'default' => 'Photos exclusives du créateur', 'sanitize_callback' => 'sanitize_textarea_field', 'transport' => 'postMessage' ) );
    $wp_customize->add_control( 'shiftzoner_rafael_subtitle', array( 'label' => 'Sous-titre Rafael', 'section' => 'shiftzoner_homepage', 'type' => 'textarea' ) );
    $wp_customize->add_setting( 'shiftzoner_community_title', array( 'default' => 'La Communauté ShiftZoneR', 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'postMessage' ) );
    $wp_customize->add_control( 'shiftzoner_community_title', array( 'label' => 'Titre Communauté', 'section' => 'shiftzoner_homepage', 'type' => 'text' ) );
    $wp_customize->add_setting( 'shiftzoner_community_subtitle', array( 'default' => 'Photos de notre communauté', 'sanitize_callback' => 'sanitize_textarea_field', 'transport' => 'postMessage' ) );
    $wp_customize->add_control( 'shiftzoner_community_subtitle', array( 'label' => 'Sous-titre Communauté', 'section' => 'shiftzoner_homepage', 'type' => 'textarea' ) );
    $wp_customize->add_setting( 'shiftzoner_cta_title', array( 'default' => 'Prêt À Rejoindre ?', 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'postMessage' ) );
    $wp_customize->add_control( 'shiftzoner_cta_title', array( 'label' => 'Titre CTA', 'section' => 'shiftzoner_homepage', 'type' => 'text' ) );
    $wp_customize->add_setting( 'shiftzoner_cta_subtitle', array( 'default' => 'Créez votre compte gratuitement.', 'sanitize_callback' => 'sanitize_textarea_field', 'transport' => 'postMessage' ) );
    $wp_customize->add_control( 'shiftzoner_cta_subtitle', array( 'label' => 'Sous-titre CTA', 'section' => 'shiftzoner_homepage', 'type' => 'textarea' ) );
    
    $wp_customize->add_section( 'shiftzoner_social', array( 'title' => 'Réseaux Sociaux', 'priority' => 31 ) );
    $wp_customize->add_setting( 'shiftzoner_instagram', array( 'default' => '', 'sanitize_callback' => 'esc_url_raw' ) );
    $wp_customize->add_control( 'shiftzoner_instagram', array( 'label' => 'Instagram URL', 'section' => 'shiftzoner_social', 'type' => 'url' ) );
    $wp_customize->add_setting( 'shiftzoner_facebook', array( 'default' => '', 'sanitize_callback' => 'esc_url_raw' ) );
    $wp_customize->add_control( 'shiftzoner_facebook', array( 'label' => 'Facebook URL', 'section' => 'shiftzoner_social', 'type' => 'url' ) );
    $wp_customize->add_setting( 'shiftzoner_twitter', array( 'default' => '', 'sanitize_callback' => 'esc_url_raw' ) );
    $wp_customize->add_control( 'shiftzoner_twitter', array( 'label' => 'Twitter URL', 'section' => 'shiftzoner_social', 'type' => 'url' ) );
}
add_action( 'customize_register', 'shiftzoner_customize_register' );

// 8. SEO
function shiftzoner_add_seo_meta() {
    if ( is_singular( 'car_photo' ) ) {
        global $post;
        $title = get_the_title();
        $description = wp_trim_words( get_the_excerpt(), 30 );
        $image = get_the_post_thumbnail_url( $post->ID, 'full' );
        $url = get_permalink();
        
        $brands = wp_get_post_terms( $post->ID, 'car_brand' );
        $models = wp_get_post_terms( $post->ID, 'car_model' );
        $years = wp_get_post_terms( $post->ID, 'car_year' );
        
        $car_info = '';
        if ( ! empty( $brands ) ) $car_info .= $brands[0]->name . ' ';
        if ( ! empty( $models ) ) $car_info .= $models[0]->name . ' ';
        if ( ! empty( $years ) ) $car_info .= $years[0]->name;
        if ( $car_info ) $title = trim( $car_info ) . ' - ' . get_bloginfo( 'name' );
        
        echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
        echo '<meta property="og:type" content="article">' . "\n";
        echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
        echo '<meta property="og:description" content="' . esc_attr( $description ) . '">' . "\n";
        echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
        if ( $image ) {
            echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
            echo '<meta property="og:image:width" content="1200">' . "\n";
            echo '<meta property="og:image:height" content="630">' . "\n";
        }
        echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
        echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '">' . "\n";
        if ( $image ) echo '<meta name="twitter:image" content="' . esc_url( $image ) . '">' . "\n";
    }
}
add_action( 'wp_head', 'shiftzoner_add_seo_meta', 1 );

function shiftzoner_add_schema_org() {
    if ( is_singular( 'car_photo' ) ) {
        global $post;
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'ImageObject',
            'name' => get_the_title(),
            'url' => get_permalink(),
            'datePublished' => get_the_date( 'c' ),
            'author' => array( '@type' => 'Person', 'name' => get_the_author() ),
        );
        echo '<script type="application/ld+json">' . wp_json_encode( $schema ) . '</script>' . "\n";
    }
    if ( is_front_page() ) {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => get_bloginfo( 'name' ),
            'url' => home_url(),
        );
        echo '<script type="application/ld+json">' . wp_json_encode( $schema ) . '</script>' . "\n";
    }
}
add_action( 'wp_head', 'shiftzoner_add_schema_org', 2 );

// 9. AJAX FILTERS
add_action( 'wp_ajax_szr_filter_photos', 'szr_filter_photos_ajax' );
add_action( 'wp_ajax_nopriv_szr_filter_photos', 'szr_filter_photos_ajax' );
function szr_filter_photos_ajax() {
    $args = array(
        'post_type' => 'car_photo',
        'posts_per_page' => 12,
        'paged' => intval( $_POST['page'] ?? 1 ),
    );
    if ( ! empty( $_POST['search'] ) ) $args['s'] = sanitize_text_field( $_POST['search'] );
    
    $query = new WP_Query( $args );
    ob_start();
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            get_template_part( 'template-parts/content', 'photo-card' );
        }
    }
    $html = ob_get_clean();
    wp_reset_postdata();
    wp_send_json_success( array( 'html' => $html, 'has_more' => $query->max_num_pages > intval( $_POST['page'] ?? 1 ) ) );
}

add_action( 'wp_ajax_szr_get_models', 'szr_get_models_ajax' );
add_action( 'wp_ajax_nopriv_szr_get_models', 'szr_get_models_ajax' );
function szr_get_models_ajax() {
    $brand_id = intval( $_POST['brand_id'] ?? 0 );
    if ( ! $brand_id ) wp_send_json_error( 'Brand ID required' );
    
    $models = get_terms( array( 'taxonomy' => 'car_model', 'hide_empty' => false, 'parent' => $brand_id ) );
    $result = array();
    foreach ( $models as $model ) {
        $result[] = array( 'id' => $model->term_id, 'name' => $model->name );
    }
    wp_send_json_success( array( 'models' => $result ) );
}

add_action( 'wp_ajax_szr_map_photos', 'szr_map_photos_ajax' );
add_action( 'wp_ajax_nopriv_szr_map_photos', 'szr_map_photos_ajax' );
function szr_map_photos_ajax() {
    $query = new WP_Query( array(
        'post_type' => 'car_photo',
        'posts_per_page' => -1,
        'meta_query' => array(
            array( 'key' => '_szr_gps_lat', 'compare' => 'EXISTS' ),
            array( 'key' => '_szr_gps_lng', 'compare' => 'EXISTS' ),
        ),
    ) );
    
    $photos = array();
    while ( $query->have_posts() ) {
        $query->the_post();
        $lat = get_post_meta( get_the_ID(), '_szr_gps_lat', true );
        $lng = get_post_meta( get_the_ID(), '_szr_gps_lng', true );
        if ( $lat && $lng ) {
            $photos[] = array(
                'id' => get_the_ID(),
                'title' => get_the_title(),
                'url' => get_permalink(),
                'thumbnail' => get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' ),
                'lat' => floatval( $lat ),
                'lng' => floatval( $lng ),
                'author' => get_the_author(),
                'user_color' => get_user_meta( get_the_author_meta( 'ID' ), '_szr_user_color', true ) ?: '#888',
            );
        }
    }
    wp_reset_postdata();
    wp_send_json_success( array( 'photos' => $photos ) );
}

add_action( 'wp_ajax_szr_report', 'szr_report_ajax' );
add_action( 'wp_ajax_nopriv_szr_report', 'szr_report_ajax' );
function szr_report_ajax() {
    if ( ! is_user_logged_in() ) wp_send_json_error( 'Connexion requise' );
    $post_id = intval( $_POST['post_id'] ?? 0 );
    $reason = sanitize_text_field( $_POST['reason'] ?? '' );
    if ( ! $post_id || ! $reason ) wp_send_json_error( 'Données invalides' );
    
    $reports = get_post_meta( $post_id, '_szr_reports', true ) ?: array();
    $reports[] = array( 'user_id' => get_current_user_id(), 'reason' => $reason, 'date' => current_time( 'mysql' ) );
    update_post_meta( $post_id, '_szr_reports', $reports );
    
    if ( count( $reports ) >= 5 ) {
        wp_update_post( array( 'ID' => $post_id, 'post_status' => 'pending' ) );
        wp_mail( get_option( 'admin_email' ), 'Contenu signalé', 'Contenu masqué : ' . get_permalink( $post_id ) );
    }
    wp_send_json_success( array( 'message' => 'Merci' ) );
}

// 10. COMPTEUR VUES
function shiftzoner_increment_views() {
    if ( is_singular( 'car_photo' ) ) {
        global $post;
        $views = get_post_meta( $post->ID, '_szr_views', true );
        update_post_meta( $post->ID, '_szr_views', $views ? intval( $views ) + 1 : 1 );
    }
}
add_action( 'wp_head', 'shiftzoner_increment_views' );

// 11. BUDDYPRESS
add_filter( 'login_redirect', 'shiftzoner_login_redirect', 10, 3 );
function shiftzoner_login_redirect( $redirect_to, $request, $user ) {
    if ( isset( $user->ID ) && function_exists( 'bp_core_get_user_domain' ) ) return bp_core_get_user_domain( $user->ID );
    return $redirect_to;
}

// 12. PERMISSIONS
function shiftzoner_allow_subscriber_uploads() {
    $subscriber = get_role( 'subscriber' );
    if ( $subscriber ) $subscriber->add_cap( 'upload_files' );
}
add_action( 'init', 'shiftzoner_allow_subscriber_uploads' );

function shiftzoner_increase_upload_size( $size ) {
    return 10485760; // 10MB
}
add_filter( 'upload_size_limit', 'shiftzoner_increase_upload_size' );
