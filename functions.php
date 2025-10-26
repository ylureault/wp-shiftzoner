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

    // Search
    if ( ! empty( $_POST['search'] ) ) {
        $args['s'] = sanitize_text_field( $_POST['search'] );
    }

    // Tax queries
    $tax_query = array();
    if ( ! empty( $_POST['brand'] ) ) {
        $tax_query[] = array(
            'taxonomy' => 'car_brand',
            'field' => 'term_id',
            'terms' => intval( $_POST['brand'] ),
        );
    }
    if ( ! empty( $_POST['model'] ) ) {
        $tax_query[] = array(
            'taxonomy' => 'car_model',
            'field' => 'term_id',
            'terms' => intval( $_POST['model'] ),
        );
    }
    if ( ! empty( $_POST['year'] ) ) {
        $tax_query[] = array(
            'taxonomy' => 'car_year',
            'field' => 'term_id',
            'terms' => intval( $_POST['year'] ),
        );
    }
    if ( ! empty( $tax_query ) ) {
        $args['tax_query'] = $tax_query;
    }

    // Sorting
    $sort = sanitize_text_field( $_POST['sort'] ?? 'date' );
    switch ( $sort ) {
        case 'votes':
            $args['meta_key'] = '_szr_vote_score';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'DESC';
            break;
        case 'comments':
            $args['orderby'] = 'comment_count';
            $args['order'] = 'DESC';
            break;
        case 'views':
            $args['meta_key'] = '_szr_views';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'DESC';
            break;
        default: // date
            $args['orderby'] = 'date';
            $args['order'] = 'DESC';
    }

    $query = new WP_Query( $args );
    ob_start();
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            get_template_part( 'template-parts/content', 'photo-card' );
        }
    } else {
        echo '<div class="no-results"><h3>Aucune photo trouvée</h3><p>Essayez de modifier vos filtres.</p></div>';
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

    $brand = get_term( $brand_id, 'car_brand' );
    if ( ! $brand || is_wp_error( $brand ) ) wp_send_json_error( 'Invalid brand' );

    // Try parent hierarchy first
    $parent = get_term_by( 'slug', $brand->slug, 'car_model' );
    if ( ! $parent ) $parent = get_term_by( 'name', $brand->name, 'car_model' );

    $models = array();
    if ( $parent && ! is_wp_error( $parent ) ) {
        $models = get_terms( array(
            'taxonomy' => 'car_model',
            'hide_empty' => false,
            'parent' => (int) $parent->term_id,
            'orderby' => 'name',
            'order' => 'ASC',
        ) );
    }

    // Fallback to meta
    if ( empty( $models ) || is_wp_error( $models ) ) {
        $models = get_terms( array(
            'taxonomy' => 'car_model',
            'hide_empty' => false,
            'meta_query' => array(
                array(
                    'key' => '_szr_model_brand',
                    'value' => $brand_id,
                    'compare' => '=',
                ),
            ),
            'orderby' => 'name',
            'order' => 'ASC',
        ) );
    }

    $result = array();
    if ( ! is_wp_error( $models ) ) {
        foreach ( $models as $model ) {
            $result[] = array( 'id' => $model->term_id, 'name' => $model->name );
        }
    }

    wp_send_json_success( array( 'models' => $result ) );
}

add_action( 'wp_ajax_szr_map_photos', 'szr_map_photos_ajax' );
add_action( 'wp_ajax_nopriv_szr_map_photos', 'szr_map_photos_ajax' );
function szr_map_photos_ajax() {
    $args = array(
        'post_type' => 'car_photo',
        'posts_per_page' => -1,
        'meta_query' => array(
            array( 'key' => '_szr_gps_lat', 'compare' => 'EXISTS' ),
            array( 'key' => '_szr_gps_lng', 'compare' => 'EXISTS' ),
        ),
    );

    // Filters
    $tax_query = array();
    if ( ! empty( $_POST['brand'] ) ) {
        $tax_query[] = array(
            'taxonomy' => 'car_brand',
            'field' => 'term_id',
            'terms' => intval( $_POST['brand'] ),
        );
    }
    if ( ! empty( $_POST['model'] ) ) {
        $tax_query[] = array(
            'taxonomy' => 'car_model',
            'field' => 'term_id',
            'terms' => intval( $_POST['model'] ),
        );
    }
    if ( ! empty( $tax_query ) ) {
        $args['tax_query'] = $tax_query;
    }

    if ( ! empty( $_POST['author'] ) ) {
        $args['author'] = intval( $_POST['author'] );
    }

    $query = new WP_Query( $args );

    $photos = array();
    while ( $query->have_posts() ) {
        $query->the_post();
        $lat = get_post_meta( get_the_ID(), '_szr_gps_lat', true );
        $lng = get_post_meta( get_the_ID(), '_szr_gps_lng', true );
        if ( $lat && $lng ) {
            $brands = wp_get_post_terms( get_the_ID(), 'car_brand' );
            $models = wp_get_post_terms( get_the_ID(), 'car_model' );
            $car_title = '';
            if ( ! empty( $brands ) && ! empty( $models ) ) {
                $car_title = $brands[0]->name . ' ' . $models[0]->name;
            } else {
                $car_title = get_the_title();
            }

            $photos[] = array(
                'id' => get_the_ID(),
                'title' => $car_title,
                'url' => get_permalink(),
                'thumbnail' => get_the_post_thumbnail_url( get_the_ID(), 'shiftzoner-thumb' ),
                'lat' => floatval( $lat ),
                'lng' => floatval( $lng ),
                'author' => get_the_author(),
                'date' => human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) . ' ago',
                'user_color' => get_user_meta( get_the_author_meta( 'ID' ), '_szr_user_color', true ) ?: '#E50914',
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

// AJAX: Live search for brands page
add_action( 'wp_ajax_szr_search_brands', 'szr_search_brands_ajax' );
add_action( 'wp_ajax_nopriv_szr_search_brands', 'szr_search_brands_ajax' );
function szr_search_brands_ajax() {
    $query = sanitize_text_field( $_POST['query'] ?? '' );

    if ( strlen( $query ) < 2 ) {
        wp_send_json_success( array( 'results' => array() ) );
        return;
    }

    $results = array();

    // Search brands
    $brands = get_terms( array(
        'taxonomy'   => 'car_brand',
        'hide_empty' => true,
        'search'     => $query,
        'number'     => 5,
    ) );

    if ( ! is_wp_error( $brands ) ) {
        foreach ( $brands as $brand ) {
            $logo_id = get_term_meta( $brand->term_id, '_szr_brand_logo_id', true );
            $logo_url = $logo_id ? wp_get_attachment_image_url( $logo_id, 'medium' ) : '';
            $results[] = array(
                'type'  => 'brand',
                'id'    => $brand->term_id,
                'name'  => $brand->name,
                'count' => $brand->count,
                'logo'  => $logo_url,
            );
        }
    }

    // Search models
    $models = get_terms( array(
        'taxonomy'   => 'car_model',
        'hide_empty' => true,
        'search'     => $query,
        'number'     => 5,
    ) );

    if ( ! is_wp_error( $models ) ) {
        foreach ( $models as $model ) {
            $brand_id = get_term_meta( $model->term_id, 'brand_id', true );
            $brand = $brand_id ? get_term( $brand_id, 'car_brand' ) : null;
            $results[] = array(
                'type'       => 'model',
                'id'         => $model->term_id,
                'name'       => $model->name,
                'brand_name' => $brand && ! is_wp_error( $brand ) ? $brand->name : '',
                'count'      => $model->count,
            );
        }
    }

    wp_send_json_success( array( 'results' => $results ) );
}

// AJAX: Get brand models for brands page
add_action( 'wp_ajax_szr_get_brand_models', 'szr_get_brand_models_ajax' );
add_action( 'wp_ajax_nopriv_szr_get_brand_models', 'szr_get_brand_models_ajax' );
function szr_get_brand_models_ajax() {
    $brand_id = intval( $_POST['brand_id'] ?? 0 );

    if ( ! $brand_id ) {
        wp_send_json_error( 'Invalid brand ID' );
        return;
    }

    $brand = get_term( $brand_id, 'car_brand' );

    if ( is_wp_error( $brand ) || ! $brand ) {
        wp_send_json_error( 'Brand not found' );
        return;
    }

    // Get brand info
    $brand_logo_id = get_term_meta( $brand_id, '_szr_brand_logo_id', true );
    $brand_logo = $brand_logo_id ? wp_get_attachment_image_url( $brand_logo_id, 'medium' ) : '';
    $brand_description = term_description( $brand_id, 'car_brand' );

    // Get models for this brand
    $models = get_terms( array(
        'taxonomy'   => 'car_model',
        'hide_empty' => true,
        'meta_query' => array(
            array(
                'key'   => 'brand_id',
                'value' => $brand_id,
            ),
        ),
        'orderby'    => 'name',
        'order'      => 'ASC',
    ) );

    $models_data = array();

    if ( ! is_wp_error( $models ) ) {
        foreach ( $models as $model ) {
            // Get first photo for thumbnail
            $photo_query = new WP_Query( array(
                'post_type'      => 'car_photo',
                'posts_per_page' => 1,
                'tax_query'      => array(
                    array(
                        'taxonomy' => 'car_model',
                        'field'    => 'term_id',
                        'terms'    => $model->term_id,
                    ),
                ),
            ) );

            $thumbnail = '';
            if ( $photo_query->have_posts() ) {
                $photo_query->the_post();
                $thumbnail = get_the_post_thumbnail_url( get_the_ID(), 'medium' );
                wp_reset_postdata();
            }

            $models_data[] = array(
                'id'        => $model->term_id,
                'name'      => $model->name,
                'count'     => $model->count,
                'thumbnail' => $thumbnail,
            );
        }
    }

    wp_send_json_success( array(
        'brand'  => array(
            'id'          => $brand_id,
            'name'        => $brand->name,
            'slug'        => $brand->slug,
            'logo'        => $brand_logo,
            'description' => $brand_description,
            'count'       => $brand->count,
        ),
        'models' => $models_data,
    ) );
}

// AJAX: Get model photos for brands page
add_action( 'wp_ajax_szr_get_model_photos', 'szr_get_model_photos_ajax' );
add_action( 'wp_ajax_nopriv_szr_get_model_photos', 'szr_get_model_photos_ajax' );
function szr_get_model_photos_ajax() {
    $model_id = intval( $_POST['model_id'] ?? 0 );
    $page = intval( $_POST['page'] ?? 1 );
    $per_page = 12;

    if ( ! $model_id ) {
        wp_send_json_error( 'Invalid model ID' );
        return;
    }

    $model = get_term( $model_id, 'car_model' );

    if ( is_wp_error( $model ) || ! $model ) {
        wp_send_json_error( 'Model not found' );
        return;
    }

    // Get brand info
    $brand_id = get_term_meta( $model_id, 'brand_id', true );
    $brand = $brand_id ? get_term( $brand_id, 'car_brand' ) : null;

    // Get photos
    $query = new WP_Query( array(
        'post_type'      => 'car_photo',
        'posts_per_page' => $per_page,
        'paged'          => $page,
        'tax_query'      => array(
            array(
                'taxonomy' => 'car_model',
                'field'    => 'term_id',
                'terms'    => $model_id,
            ),
        ),
        'orderby'        => 'date',
        'order'          => 'DESC',
    ) );

    $photos = array();

    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();

            $votes = intval( get_post_meta( get_the_ID(), '_szr_votes', true ) );
            $views = intval( get_post_meta( get_the_ID(), '_szr_views', true ) );

            $photos[] = array(
                'id'        => get_the_ID(),
                'title'     => get_the_title(),
                'url'       => get_permalink(),
                'thumbnail' => get_the_post_thumbnail_url( get_the_ID(), 'medium' ),
                'author'    => get_the_author(),
                'date'      => get_the_date(),
                'votes'     => $votes,
                'views'     => $views,
                'comments'  => get_comments_number(),
            );
        }
        wp_reset_postdata();
    }

    wp_send_json_success( array(
        'model'    => array(
            'id'         => $model_id,
            'name'       => $model->name,
            'slug'       => $model->slug,
            'brand_name' => $brand && ! is_wp_error( $brand ) ? $brand->name : '',
            'brand_id'   => $brand_id,
            'count'      => $model->count,
        ),
        'photos'   => $photos,
        'has_more' => $page < $query->max_num_pages,
        'page'     => $page,
    ) );
}

// AJAX: Upload photo from brands page
add_action( 'wp_ajax_szr_ajax_upload_photo', 'szr_ajax_upload_photo_ajax' );
function szr_ajax_upload_photo_ajax() {
    // Security check
    if ( ! is_user_logged_in() ) {
        wp_send_json_error( 'Vous devez être connecté pour uploader une photo' );
        return;
    }

    if ( ! isset( $_POST['ajax_upload_nonce'] ) || ! wp_verify_nonce( $_POST['ajax_upload_nonce'], 'szr_ajax_upload' ) ) {
        wp_send_json_error( 'Vérification de sécurité échouée' );
        return;
    }

    // Validate inputs
    $brand_id = intval( $_POST['brand_id'] ?? 0 );
    $model_id = intval( $_POST['model_id'] ?? 0 );
    $title    = sanitize_text_field( $_POST['title'] ?? '' );
    $desc     = wp_kses_post( $_POST['description'] ?? '' );

    if ( ! $brand_id || ! $model_id ) {
        wp_send_json_error( 'Marque et modèle requis' );
        return;
    }

    // Check file upload
    if ( empty( $_FILES['photo'] ) || ! isset( $_FILES['photo']['tmp_name'] ) ) {
        wp_send_json_error( 'Aucun fichier reçu' );
        return;
    }

    $file = $_FILES['photo'];
    if ( $file['error'] !== UPLOAD_ERR_OK ) {
        wp_send_json_error( 'Erreur lors du téléchargement du fichier' );
        return;
    }

    // Validate file type
    $allowed_mimes = array( 'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/tiff' );
    $mime = mime_content_type( $file['tmp_name'] );
    if ( ! in_array( $mime, $allowed_mimes, true ) ) {
        wp_send_json_error( 'Format de fichier non supporté. Utilisez JPG, PNG, GIF, WEBP ou TIFF.' );
        return;
    }

    // Validate file size
    $max_size = wp_max_upload_size();
    if ( $file['size'] > $max_size ) {
        wp_send_json_error( 'Fichier trop volumineux. Taille maximale: ' . size_format( $max_size ) );
        return;
    }

    // Load required files
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';

    // Create post
    $post_title = ! empty( $title ) ? $title : 'Photo — ' . current_time( 'Y-m-d H:i' );
    $post_id = wp_insert_post( array(
        'post_type'    => 'car_photo',
        'post_status'  => 'publish',
        'post_title'   => $post_title,
        'post_content' => $desc,
        'post_author'  => get_current_user_id(),
    ), true );

    if ( is_wp_error( $post_id ) ) {
        wp_send_json_error( 'Erreur lors de la création de la publication' );
        return;
    }

    // Set taxonomies
    wp_set_post_terms( $post_id, array( $brand_id ), 'car_brand', false );
    wp_set_post_terms( $post_id, array( $model_id ), 'car_model', false );

    // Handle file upload
    $upload = wp_handle_upload( $file, array( 'test_form' => false ) );

    if ( isset( $upload['error'] ) ) {
        wp_delete_post( $post_id, true );
        wp_send_json_error( 'Erreur d\'upload: ' . $upload['error'] );
        return;
    }

    // Create attachment
    $filetype = wp_check_filetype( basename( $upload['file'] ), null );
    $attachment = array(
        'post_mime_type' => $filetype['type'],
        'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $upload['file'] ) ),
        'post_content'   => '',
        'post_status'    => 'inherit',
    );

    $attach_id = wp_insert_attachment( $attachment, $upload['file'], $post_id );

    if ( ! is_wp_error( $attach_id ) ) {
        $attach_meta = wp_generate_attachment_metadata( $attach_id, $upload['file'] );
        wp_update_attachment_metadata( $attach_id, $attach_meta );
        set_post_thumbnail( $post_id, $attach_id );

        // Extract EXIF GPS data if available
        if ( function_exists( 'exif_read_data' ) ) {
            $exif = @exif_read_data( $upload['file'], 'EXIF,GPS', true, false );
            if ( $exif && isset( $exif['GPS'] ) ) {
                $gps = $exif['GPS'];
                if ( ! empty( $gps['GPSLatitude'] ) && ! empty( $gps['GPSLongitude'] ) ) {
                    // Parse GPS coordinates (simplified)
                    $lat = $gps['GPSLatitude'][0] + ( $gps['GPSLatitude'][1] / 60 ) + ( $gps['GPSLatitude'][2] / 3600 );
                    if ( $gps['GPSLatitudeRef'] === 'S' ) $lat *= -1;

                    $lng = $gps['GPSLongitude'][0] + ( $gps['GPSLongitude'][1] / 60 ) + ( $gps['GPSLongitude'][2] / 3600 );
                    if ( $gps['GPSLongitudeRef'] === 'W' ) $lng *= -1;

                    update_post_meta( $post_id, '_szr_gps_lat', $lat );
                    update_post_meta( $post_id, '_szr_gps_lng', $lng );
                }
            }
        }
    }

    wp_send_json_success( array(
        'post_id'   => $post_id,
        'post_url'  => get_permalink( $post_id ),
        'message'   => 'Photo publiée avec succès !',
    ) );
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

// 13. WATERMARKING
// Désactivé si Easy Watermark est actif pour éviter les conflits
function shiftzoner_apply_watermark( $metadata, $attachment_id ) {
    // Désactiver si Easy Watermark est actif
    if ( class_exists( 'Easy_Watermark' ) || function_exists( 'easy_watermark_init' ) ) {
        return $metadata;
    }

    $post_parent = wp_get_post_parent_id( $attachment_id );
    if ( ! $post_parent || get_post_type( $post_parent ) !== 'car_photo' ) return $metadata;

    $upload_dir = wp_upload_dir();
    $file = get_attached_file( $attachment_id );
    if ( ! file_exists( $file ) ) return $metadata;

    $image_editor = wp_get_image_editor( $file );
    if ( is_wp_error( $image_editor ) ) return $metadata;

    $size = $image_editor->get_size();
    $text = '© ' . get_bloginfo( 'name' );
    $font_size = max( 12, intval( $size['width'] * 0.02 ) );

    // GD library watermark
    if ( function_exists( 'imagettftext' ) && extension_loaded( 'gd' ) ) {
        $image_type = wp_check_filetype( $file );
        switch ( $image_type['type'] ) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg( $file );
                break;
            case 'image/png':
                $image = imagecreatefrompng( $file );
                break;
            default:
                return $metadata;
        }

        if ( ! $image ) return $metadata;

        $text_color = imagecolorallocatealpha( $image, 255, 255, 255, 40 );
        $x = $size['width'] - ( strlen( $text ) * $font_size * 0.6 ) - 20;
        $y = $size['height'] - 20;

        imagestring( $image, 5, $x, $y, $text, $text_color );

        switch ( $image_type['type'] ) {
            case 'image/jpeg':
                imagejpeg( $image, $file, 90 );
                break;
            case 'image/png':
                imagepng( $image, $file, 9 );
                break;
        }

        imagedestroy( $image );
    }

    return $metadata;
}
add_filter( 'wp_generate_attachment_metadata', 'shiftzoner_apply_watermark', 10, 2 );

// Note admin si Easy Watermark est actif
function shiftzoner_watermark_notice() {
    if ( class_exists( 'Easy_Watermark' ) || function_exists( 'easy_watermark_init' ) ) {
        echo '<div class="notice notice-info"><p><strong>ShiftZoneR :</strong> Easy Watermark est actif. Le watermark intégré du thème est automatiquement désactivé pour éviter les conflits. Configurez Easy Watermark dans Réglages > Easy Watermark.</p></div>';
    }
}
add_action( 'admin_notices', 'shiftzoner_watermark_notice' );

// 14. RATE LIMITING & UPLOAD VALIDATION
function shiftzoner_check_upload_limit() {
    if ( ! is_user_logged_in() ) return;

    $user_id = get_current_user_id();

    // Check daily limit
    $today_start = strtotime( 'today midnight' );
    $uploads_today = get_posts( array(
        'post_type' => 'car_photo',
        'author' => $user_id,
        'date_query' => array(
            array(
                'after' => date( 'Y-m-d H:i:s', $today_start ),
            ),
        ),
        'posts_per_page' => -1,
        'fields' => 'ids',
    ) );

    if ( count( $uploads_today ) >= 100 ) {
        wp_die( 'Limite d\'upload atteinte (100 photos par jour). Revenez demain !' );
    }

    // Captcha after 5 uploads (can be extended with reCAPTCHA)
    if ( count( $uploads_today ) >= 5 ) {
        $last_captcha = get_user_meta( $user_id, '_szr_last_captcha', true );
        if ( time() - intval( $last_captcha ) > 3600 ) {
            // Here you would verify captcha, for now just update timestamp
            update_user_meta( $user_id, '_szr_last_captcha', time() );
        }
    }
}

// 15. BUDDYPRESS DEEP INTEGRATION
function shiftzoner_buddypress_init() {
    if ( ! function_exists( 'bp_is_active' ) ) return;

    // Create groups for each brand automatically
    add_action( 'created_car_brand', 'shiftzoner_create_brand_group', 10, 2 );

    // Post activity when photo is uploaded
    add_action( 'transition_post_status', 'shiftzoner_photo_activity', 10, 3 );

    // Add photos tab to profile
    add_action( 'bp_setup_nav', 'shiftzoner_profile_photos_tab', 100 );

    // Notification on vote
    add_action( 'szr_after_vote', 'shiftzoner_vote_notification', 10, 3 );

    // Notification on comment
    add_action( 'comment_post', 'shiftzoner_comment_notification', 10, 3 );
}
add_action( 'bp_include', 'shiftzoner_buddypress_init' );

// Create group for brand
function shiftzoner_create_brand_group( $term_id, $tt_id ) {
    if ( ! function_exists( 'groups_create_group' ) ) return;

    $term = get_term( $term_id, 'car_brand' );
    if ( is_wp_error( $term ) ) return;

    $existing = groups_get_groups( array(
        'search_terms' => $term->name,
        'per_page' => 1,
    ) );

    if ( ! empty( $existing['groups'] ) ) return;

    $group_id = groups_create_group( array(
        'creator_id' => 1, // Admin/Rafael
        'name' => $term->name,
        'description' => 'Groupe dédié aux photos de ' . $term->name,
        'slug' => $term->slug,
        'status' => 'public',
        'enable_forum' => 1,
    ) );

    if ( $group_id ) {
        update_term_meta( $term_id, '_szr_bp_group_id', $group_id );
    }
}

// Post activity when photo uploaded
function shiftzoner_photo_activity( $new_status, $old_status, $post ) {
    if ( $post->post_type !== 'car_photo' ) return;
    if ( $new_status !== 'publish' || $old_status === 'publish' ) return;
    if ( ! function_exists( 'bp_activity_add' ) ) return;

    $brands = wp_get_post_terms( $post->ID, 'car_brand' );
    $models = wp_get_post_terms( $post->ID, 'car_model' );

    $car_name = '';
    if ( ! empty( $brands ) && ! empty( $models ) ) {
        $car_name = $brands[0]->name . ' ' . $models[0]->name;
    } else {
        $car_name = $post->post_title;
    }

    $activity_id = bp_activity_add( array(
        'user_id' => $post->post_author,
        'action' => sprintf(
            '%s a publié une photo de %s',
            bp_core_get_userlink( $post->post_author ),
            '<a href="' . get_permalink( $post->ID ) . '">' . esc_html( $car_name ) . '</a>'
        ),
        'content' => $post->post_content,
        'primary_link' => get_permalink( $post->ID ),
        'component' => 'activity',
        'type' => 'new_car_photo',
        'item_id' => $post->ID,
    ) );

    // Post to brand group if exists
    if ( ! empty( $brands ) ) {
        $group_id = get_term_meta( $brands[0]->term_id, '_szr_bp_group_id', true );
        if ( $group_id && function_exists( 'groups_post_update' ) ) {
            groups_post_update( array(
                'user_id' => $post->post_author,
                'group_id' => $group_id,
                'content' => sprintf(
                    'Nouvelle photo de %s : <a href="%s">%s</a>',
                    $car_name,
                    get_permalink( $post->ID ),
                    $post->post_title
                ),
            ) );
        }
    }
}

// Add photos tab to profile
function shiftzoner_profile_photos_tab() {
    if ( ! bp_is_user() ) return;

    bp_core_new_nav_item( array(
        'name' => 'Mes Photos',
        'slug' => 'photos',
        'screen_function' => 'shiftzoner_profile_photos_screen',
        'position' => 30,
        'default_subnav_slug' => 'all',
    ) );
}

function shiftzoner_profile_photos_screen() {
    add_action( 'bp_template_content', 'shiftzoner_profile_photos_content' );
    bp_core_load_template( 'members/single/plugins' );
}

function shiftzoner_profile_photos_content() {
    $user_id = bp_displayed_user_id();
    $photos = new WP_Query( array(
        'post_type' => 'car_photo',
        'author' => $user_id,
        'posts_per_page' => 20,
    ) );

    echo '<div class="profile-photos-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1.5rem; margin-top: 2rem;">';
    if ( $photos->have_posts() ) {
        while ( $photos->have_posts() ) {
            $photos->the_post();
            get_template_part( 'template-parts/content', 'photo-card' );
        }
    } else {
        echo '<p>Aucune photo publiée.</p>';
    }
    echo '</div>';
    wp_reset_postdata();
}

// Send notification on vote
function shiftzoner_vote_notification( $post_id, $user_id, $vote_type ) {
    if ( ! function_exists( 'bp_notifications_add_notification' ) ) return;
    if ( $vote_type !== 'up' ) return; // Only notify on upvotes

    $author_id = get_post_field( 'post_author', $post_id );
    if ( $author_id == $user_id ) return; // Don't notify self

    bp_notifications_add_notification( array(
        'user_id' => $author_id,
        'item_id' => $post_id,
        'secondary_item_id' => $user_id,
        'component_name' => 'shiftzoner',
        'component_action' => 'new_vote',
        'date_notified' => bp_core_current_time(),
        'is_new' => 1,
    ) );
}

// Send notification on comment
function shiftzoner_comment_notification( $comment_id, $approved, $commentdata ) {
    if ( ! function_exists( 'bp_notifications_add_notification' ) ) return;

    $comment = get_comment( $comment_id );
    $post = get_post( $comment->comment_post_ID );

    if ( $post->post_type !== 'car_photo' ) return;
    if ( $post->post_author == $comment->user_id ) return; // Don't notify self

    bp_notifications_add_notification( array(
        'user_id' => $post->post_author,
        'item_id' => $comment->comment_post_ID,
        'secondary_item_id' => $comment_id,
        'component_name' => 'shiftzoner',
        'component_action' => 'new_comment',
        'date_notified' => bp_core_current_time(),
        'is_new' => 1,
    ) );
}

// Register custom BP notification formats
function shiftzoner_format_notifications( $action, $item_id, $secondary_item_id, $total_items, $format = 'string' ) {
    if ( $action === 'new_vote' ) {
        $post = get_post( $item_id );
        $voter = get_userdata( $secondary_item_id );
        $text = $voter->display_name . ' a aimé votre photo';
        $link = get_permalink( $item_id );

        if ( 'string' === $format ) {
            return '<a href="' . esc_url( $link ) . '">' . esc_html( $text ) . '</a>';
        } else {
            return array(
                'text' => $text,
                'link' => $link,
            );
        }
    }

    if ( $action === 'new_comment' ) {
        $post = get_post( $item_id );
        $comment = get_comment( $secondary_item_id );
        $text = $comment->comment_author . ' a commenté votre photo';
        $link = get_comment_link( $comment );

        if ( 'string' === $format ) {
            return '<a href="' . esc_url( $link ) . '">' . esc_html( $text ) . '</a>';
        } else {
            return array(
                'text' => $text,
                'link' => $link,
            );
        }
    }

    return $action;
}
add_filter( 'bp_notifications_get_notifications_for_user', 'shiftzoner_format_notifications', 10, 5 );

// Trigger vote hook for notifications
add_action( 'wp_ajax_szr_vote', 'shiftzoner_trigger_vote_hook', 11 );
function shiftzoner_trigger_vote_hook() {
    if ( isset( $_POST['post_id'], $_POST['vote'] ) && $_POST['vote'] === 'up' ) {
        do_action( 'szr_after_vote', intval( $_POST['post_id'] ), get_current_user_id(), 'up' );
    }
}

// Initialize groups for existing brands (run once)
function shiftzoner_init_brand_groups() {
    if ( ! function_exists( 'groups_create_group' ) ) return;

    $brands = get_terms( array(
        'taxonomy' => 'car_brand',
        'hide_empty' => false,
    ) );

    $created = 0;
    foreach ( $brands as $brand ) {
        $existing_group = get_term_meta( $brand->term_id, '_szr_bp_group_id', true );
        if ( $existing_group ) continue;

        $existing = groups_get_groups( array(
            'search_terms' => $brand->name,
            'per_page' => 1,
        ) );

        if ( ! empty( $existing['groups'] ) ) {
            update_term_meta( $brand->term_id, '_szr_bp_group_id', $existing['groups'][0]->id );
            continue;
        }

        $group_id = groups_create_group( array(
            'creator_id' => 1,
            'name' => $brand->name,
            'description' => sprintf( 'Groupe communautaire dédié aux passionnés de %s. Partagez vos photos, discutez et échangez autour de cette marque emblématique.', $brand->name ),
            'slug' => $brand->slug,
            'status' => 'public',
            'enable_forum' => 1,
        ) );

        if ( $group_id ) {
            update_term_meta( $brand->term_id, '_szr_bp_group_id', $group_id );
            $created++;
        }
    }

    return $created;
}

// Auto-join user to brand group when uploading photo
function shiftzoner_auto_join_brand_group( $post_id, $user_id, $vote_type ) {
    if ( ! function_exists( 'groups_join_group' ) ) return;

    $brands = wp_get_post_terms( $post_id, 'car_brand' );
    if ( empty( $brands ) ) return;

    $group_id = get_term_meta( $brands[0]->term_id, '_szr_bp_group_id', true );
    if ( ! $group_id ) return;

    if ( ! groups_is_user_member( $user_id, $group_id ) ) {
        groups_join_group( $group_id, $user_id );
    }
}
add_action( 'transition_post_status', 'shiftzoner_auto_join_on_publish', 10, 3 );
function shiftzoner_auto_join_on_publish( $new_status, $old_status, $post ) {
    if ( $post->post_type !== 'car_photo' ) return;
    if ( $new_status !== 'publish' ) return;
    if ( ! function_exists( 'groups_join_group' ) ) return;

    $brands = wp_get_post_terms( $post->ID, 'car_brand' );
    if ( empty( $brands ) ) return;

    $group_id = get_term_meta( $brands[0]->term_id, '_szr_bp_group_id', true );
    if ( ! $group_id ) return;

    if ( ! groups_is_user_member( $post->post_author, $group_id ) ) {
        groups_join_group( $group_id, $post->post_author );
    }
}

// Widget: Top Contributors
class ShiftZoneR_Top_Contributors_Widget extends WP_Widget {
    function __construct() {
        parent::__construct( 'szr_top_contributors', 'ShiftZoneR - Top Contributeurs', array( 'description' => 'Affiche les meilleurs contributeurs' ) );
    }

    function widget( $args, $instance ) {
        echo $args['before_widget'];
        echo $args['before_title'] . 'Top Contributeurs' . $args['after_title'];

        $top_users = new WP_User_Query( array(
            'orderby' => 'meta_value_num',
            'meta_key' => '_szr_karma',
            'order' => 'DESC',
            'number' => 5,
        ) );

        if ( ! empty( $top_users->results ) ) {
            echo '<ul class="szr-top-contributors">';
            foreach ( $top_users->results as $user ) {
                $karma = get_user_meta( $user->ID, '_szr_karma', true ) ?: 0;
                $color = get_user_meta( $user->ID, '_szr_user_color', true ) ?: '#E50914';
                $photo_count = count_user_posts( $user->ID, 'car_photo' );

                echo '<li style="padding: 10px; margin-bottom: 8px; background: #1a1a1a; border-radius: 8px; border-left: 3px solid ' . esc_attr( $color ) . ';">';
                echo '<strong>' . esc_html( $user->display_name ) . '</strong><br>';
                echo '<small>' . $photo_count . ' photos • ' . $karma . ' karma</small>';
                echo '</li>';
            }
            echo '</ul>';
        }

        echo $args['after_widget'];
    }
}
add_action( 'widgets_init', function() {
    register_widget( 'ShiftZoneR_Top_Contributors_Widget' );
} );

// Shortcode: User Stats
function shiftzoner_user_stats_shortcode( $atts ) {
    if ( ! is_user_logged_in() ) return '<p>Connectez-vous pour voir vos statistiques.</p>';

    $user_id = get_current_user_id();
    $karma = get_user_meta( $user_id, '_szr_karma', true ) ?: 0;
    $color = get_user_meta( $user_id, '_szr_user_color', true ) ?: '#E50914';
    $photo_count = count_user_posts( $user_id, 'car_photo' );

    $total_votes = 0;
    $photos = get_posts( array(
        'post_type' => 'car_photo',
        'author' => $user_id,
        'posts_per_page' => -1,
        'fields' => 'ids',
    ) );

    foreach ( $photos as $photo_id ) {
        $votes = get_post_meta( $photo_id, '_szr_vote_score', true ) ?: 0;
        $total_votes += intval( $votes );
    }

    ob_start();
    ?>
    <div class="szr-user-stats" style="background: linear-gradient(135deg, #1a1a1a, #2a2a2a); border-radius: 20px; padding: 2rem; border: 1px solid <?php echo esc_attr( $color ); ?>;">
        <h3 style="margin-top: 0; color: <?php echo esc_attr( $color ); ?>;">Vos Statistiques</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-top: 1.5rem;">
            <div style="text-align: center; padding: 1rem; background: rgba(255,255,255,0.05); border-radius: 10px;">
                <div style="font-size: 2rem; font-weight: 900; color: <?php echo esc_attr( $color ); ?>;"><?php echo $photo_count; ?></div>
                <div style="font-size: 0.9rem; color: #a0a0a0;">Photos</div>
            </div>
            <div style="text-align: center; padding: 1rem; background: rgba(255,255,255,0.05); border-radius: 10px;">
                <div style="font-size: 2rem; font-weight: 900; color: <?php echo esc_attr( $color ); ?>;"><?php echo $karma; ?></div>
                <div style="font-size: 0.9rem; color: #a0a0a0;">Karma</div>
            </div>
            <div style="text-align: center; padding: 1rem; background: rgba(255,255,255,0.05); border-radius: 10px;">
                <div style="font-size: 2rem; font-weight: 900; color: <?php echo esc_attr( $color ); ?>;"><?php echo $total_votes; ?></div>
                <div style="font-size: 0.9rem; color: #a0a0a0;">Votes reçus</div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'shiftzoner_stats', 'shiftzoner_user_stats_shortcode' );

// 16. OPTIMISATIONS PERFORMANCES & MOBILE

// Lazy loading automatique pour toutes les images
add_filter( 'wp_lazy_loading_enabled', '__return_true' );

// Ajouter loading="lazy" à toutes les images
function shiftzoner_add_lazy_load( $attr, $attachment, $size ) {
    $attr['loading'] = 'lazy';
    $attr['decoding'] = 'async';
    return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'shiftzoner_add_lazy_load', 10, 3 );

// Defer JavaScript pour performance
function shiftzoner_defer_scripts( $tag, $handle, $src ) {
    // Skip jQuery and admin scripts
    if ( is_admin() || strpos( $handle, 'jquery' ) !== false ) {
        return $tag;
    }

    // Defer non-essential scripts
    $defer_scripts = array( 'comment-reply', 'wp-embed' );
    if ( in_array( $handle, $defer_scripts ) ) {
        return str_replace( '<script ', '<script defer ', $tag );
    }

    return $tag;
}
add_filter( 'script_loader_tag', 'shiftzoner_defer_scripts', 10, 3 );

// Optimiser base de données
function shiftzoner_optimize_queries() {
    // Désactiver emojis (économise 2 requêtes HTTP)
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );

    // Désactiver embeds
    remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
    remove_action( 'wp_head', 'wp_oembed_add_host_js' );

    // Désactiver WordPress generator
    remove_action( 'wp_head', 'wp_generator' );

    // Désactiver RSD link
    remove_action( 'wp_head', 'rsd_link' );

    // Désactiver Windows Live Writer
    remove_action( 'wp_head', 'wlwmanifest_link' );

    // Désactiver shortlink
    remove_action( 'wp_head', 'wp_shortlink_wp_head' );
}
add_action( 'init', 'shiftzoner_optimize_queries' );

// Précharger les ressources critiques
function shiftzoner_preload_resources() {
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">';
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';
    echo '<link rel="dns-prefetch" href="//fonts.googleapis.com">';
    echo '<link rel="dns-prefetch" href="//fonts.gstatic.com">';

    // Preload Leaflet for map pages
    if ( is_page_template( 'page-carte.php' ) ) {
        echo '<link rel="preload" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" as="style">';
        echo '<link rel="preload" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" as="script">';
    }
}
add_action( 'wp_head', 'shiftzoner_preload_resources', 1 );

// Responsive images avec srcset
function shiftzoner_responsive_images() {
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'align-wide' );
    add_theme_support( 'editor-styles' );
}
add_action( 'after_setup_theme', 'shiftzoner_responsive_images' );

// Compression GZIP
function shiftzoner_enable_gzip() {
    if ( ! ini_get( 'output_buffering' ) ) {
        ob_start( 'ob_gzhandler' );
    }
}
add_action( 'init', 'shiftzoner_enable_gzip' );

// Cache browser (via headers)
function shiftzoner_browser_cache() {
    if ( ! is_admin() ) {
        header( 'Cache-Control: public, max-age=31536000' );
    }
}
add_action( 'send_headers', 'shiftzoner_browser_cache' );

// Optimiser images uploadées automatiquement
function shiftzoner_optimize_uploaded_images( $file ) {
    if ( ! function_exists( 'wp_get_image_editor' ) ) {
        require_once ABSPATH . 'wp-admin/includes/image.php';
    }

    $editor = wp_get_image_editor( $file['tmp_name'] );
    if ( ! is_wp_error( $editor ) ) {
        $editor->set_quality( 85 ); // Qualité optimale
        $editor->save( $file['tmp_name'] );
    }

    return $file;
}
add_filter( 'wp_handle_upload_prefilter', 'shiftzoner_optimize_uploaded_images' );

// Touch-friendly tables
function shiftzoner_responsive_tables( $content ) {
    $content = str_replace( '<table', '<div class="table-responsive"><table', $content );
    $content = str_replace( '</table>', '</table></div>', $content );
    return $content;
}
add_filter( 'the_content', 'shiftzoner_responsive_tables' );

// Viewport meta pour PWA
function shiftzoner_pwa_meta() {
    echo '<meta name="theme-color" content="#0a0a0a">';
    echo '<meta name="mobile-web-app-capable" content="yes">';
    echo '<meta name="apple-mobile-web-app-capable" content="yes">';
    echo '<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">';
    echo '<meta name="apple-mobile-web-app-title" content="ShiftZoneR">';
}
add_action( 'wp_head', 'shiftzoner_pwa_meta', 0 );

// 17. MENU FALLBACK
function shiftzoner_fallback_menu() {
    echo '<ul class="nav-links">';
    echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">Accueil</a></li>';
    echo '<li><a href="' . esc_url( home_url( '/galerie/' ) ) . '">Explorer</a></li>';
    if ( function_exists( 'bp_is_active' ) ) {
        echo '<li><a href="' . esc_url( bp_get_groups_directory_permalink() ) . '">Communauté</a></li>';
    }
    echo '<li><a href="' . esc_url( home_url( '/carte/' ) ) . '">Carte</a></li>';
    if ( function_exists( 'bbp_is_active' ) ) {
        echo '<li><a href="' . esc_url( home_url( '/discussion/' ) ) . '">Discussion</a></li>';
    }
    if ( is_user_logged_in() && function_exists( 'bp_core_get_user_domain' ) ) {
        echo '<li><a href="' . esc_url( bp_core_get_user_domain( get_current_user_id() ) ) . '">Mon Profil</a></li>';
    }
    echo '</ul>';
}

// Menu mobile avec icônes
function shiftzoner_mobile_menu_icons( $item_id, $url, $text ) {
    $icons = array(
        'accueil' => '<svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>',
        'explorer' => '<svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/></svg>',
        'galerie' => '<svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/></svg>',
        'communauté' => '<svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/></svg>',
        'groupes' => '<svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/></svg>',
        'carte' => '<svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>',
        'discussion' => '<svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"/></svg>',
        'forums' => '<svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"/></svg>',
        'profil' => '<svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>',
        'mon profil' => '<svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>',
    );

    $text_lower = strtolower( $text );
    $icon = '';

    foreach ( $icons as $keyword => $svg ) {
        if ( stripos( $text_lower, $keyword ) !== false ) {
            $icon = $svg;
            break;
        }
    }

    if ( ! $icon ) {
        $icon = '<svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/></svg>';
    }

    return $icon;
}
