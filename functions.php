<?php
/**
 * Astra functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Astra
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Define Constants
 */
define( 'ASTRA_THEME_VERSION', '4.11.13' );
define( 'ASTRA_THEME_SETTINGS', 'astra-settings' );
define( 'ASTRA_THEME_DIR', trailingslashit( get_template_directory() ) );
define( 'ASTRA_THEME_URI', trailingslashit( esc_url( get_template_directory_uri() ) ) );
define( 'ASTRA_THEME_ORG_VERSION', file_exists( ASTRA_THEME_DIR . 'inc/w-org-version.php' ) );

/**
 * Minimum Version requirement of the Astra Pro addon.
 * This constant will be used to display the notice asking user to update the Astra addon to the version defined below.
 */
define( 'ASTRA_EXT_MIN_VER', '4.11.6' );

/**
 * Load in-house compatibility.
 */
if ( ASTRA_THEME_ORG_VERSION ) {
    require_once ASTRA_THEME_DIR . 'inc/w-org-version.php';
}

/**
 * Setup helper functions of Astra.
 */
require_once ASTRA_THEME_DIR . 'inc/core/class-astra-theme-options.php';
require_once ASTRA_THEME_DIR . 'inc/core/class-theme-strings.php';
require_once ASTRA_THEME_DIR . 'inc/core/common-functions.php';
require_once ASTRA_THEME_DIR . 'inc/core/class-astra-icons.php';

define( 'ASTRA_WEBSITE_BASE_URL', 'https://wpastra.com' );

/**
 * Deprecate constants in future versions as they are no longer used in the codebase.
 */
define( 'ASTRA_PRO_UPGRADE_URL', ASTRA_THEME_ORG_VERSION ? astra_get_pro_url( '/pricing/', 'free-theme', 'dashboard', 'upgrade' ) : 'https://woocommerce.com/products/astra-pro/' );
define( 'ASTRA_PRO_CUSTOMIZER_UPGRADE_URL', ASTRA_THEME_ORG_VERSION ? astra_get_pro_url( '/pricing/', 'free-theme', 'customizer', 'upgrade' ) : 'https://woocommerce.com/products/astra-pro/' );

/**
 * Update theme
 */
require_once ASTRA_THEME_DIR . 'inc/theme-update/astra-update-functions.php';
require_once ASTRA_THEME_DIR . 'inc/theme-update/class-astra-theme-background-updater.php';

/**
 * Fonts Files
 */
require_once ASTRA_THEME_DIR . 'inc/customizer/class-astra-font-families.php';
if ( is_admin() ) {
    require_once ASTRA_THEME_DIR . 'inc/customizer/class-astra-fonts-data.php';
}

require_once ASTRA_THEME_DIR . 'inc/lib/webfont/class-astra-webfont-loader.php';
require_once ASTRA_THEME_DIR . 'inc/lib/docs/class-astra-docs-loader.php';
require_once ASTRA_THEME_DIR . 'inc/customizer/class-astra-fonts.php';

require_once ASTRA_THEME_DIR . 'inc/dynamic-css/custom-menu-old-header.php';
require_once ASTRA_THEME_DIR . 'inc/dynamic-css/container-layouts.php';
require_once ASTRA_THEME_DIR . 'inc/dynamic-css/astra-icons.php';
require_once ASTRA_THEME_DIR . 'inc/core/class-astra-walker-page.php';
require_once ASTRA_THEME_DIR . 'inc/core/class-astra-enqueue-scripts.php';
require_once ASTRA_THEME_DIR . 'inc/core/class-gutenberg-editor-css.php';
require_once ASTRA_THEME_DIR . 'inc/core/class-astra-wp-editor-css.php';
require_once ASTRA_THEME_DIR . 'inc/dynamic-css/block-editor-compatibility.php';
require_once ASTRA_THEME_DIR . 'inc/dynamic-css/inline-on-mobile.php';
require_once ASTRA_THEME_DIR . 'inc/dynamic-css/content-background.php';
require_once ASTRA_THEME_DIR . 'inc/dynamic-css/dark-mode.php';
require_once ASTRA_THEME_DIR . 'inc/class-astra-dynamic-css.php';
require_once ASTRA_THEME_DIR . 'inc/class-astra-global-palette.php';

// Enable NPS Survey only if the starter templates version is < 4.3.7 or > 4.4.4 to prevent fatal error.
if ( ! defined( 'ASTRA_SITES_VER' ) || version_compare( ASTRA_SITES_VER, '4.3.7', '<' ) || version_compare( ASTRA_SITES_VER, '4.4.4', '>' ) ) {
    // NPS Survey Integration
    require_once ASTRA_THEME_DIR . 'inc/lib/class-astra-nps-notice.php';
    require_once ASTRA_THEME_DIR . 'inc/lib/class-astra-nps-survey.php';
}

/**
 * Custom template tags for this theme.
 */
require_once ASTRA_THEME_DIR . 'inc/core/class-astra-attr.php';
require_once ASTRA_THEME_DIR . 'inc/template-tags.php';

require_once ASTRA_THEME_DIR . 'inc/widgets.php';
require_once ASTRA_THEME_DIR . 'inc/core/theme-hooks.php';
require_once ASTRA_THEME_DIR . 'inc/admin-functions.php';
require_once ASTRA_THEME_DIR . 'inc/class-astra-memory-limit-notice.php';
require_once ASTRA_THEME_DIR . 'inc/core/sidebar-manager.php';

/**
 * Markup Functions
 */
require_once ASTRA_THEME_DIR . 'inc/markup-extras.php';
require_once ASTRA_THEME_DIR . 'inc/extras.php';
require_once ASTRA_THEME_DIR . 'inc/blog/blog-config.php';
require_once ASTRA_THEME_DIR . 'inc/blog/blog.php';
require_once ASTRA_THEME_DIR . 'inc/blog/single-blog.php';

/**
 * Markup Files
 */
require_once ASTRA_THEME_DIR . 'inc/template-parts.php';
require_once ASTRA_THEME_DIR . 'inc/class-astra-loop.php';
require_once ASTRA_THEME_DIR . 'inc/class-astra-mobile-header.php';

/**
 * Functions and definitions.
 */
require_once ASTRA_THEME_DIR . 'inc/class-astra-after-setup-theme.php';

// Required files.
require_once ASTRA_THEME_DIR . 'inc/core/class-astra-admin-helper.php';

require_once ASTRA_THEME_DIR . 'inc/schema/class-astra-schema.php';

/* Setup API */
require_once ASTRA_THEME_DIR . 'admin/includes/class-astra-api-init.php';

if ( is_admin() ) {
    /**
     * Admin Menu Settings
     */
    require_once ASTRA_THEME_DIR . 'inc/core/class-astra-admin-settings.php';
    require_once ASTRA_THEME_DIR . 'admin/class-astra-admin-loader.php';
    require_once ASTRA_THEME_DIR . 'inc/lib/astra-notices/class-astra-notices.php';
}

/**
 * Metabox additions.
 */
require_once ASTRA_THEME_DIR . 'inc/metabox/class-astra-meta-boxes.php';
require_once ASTRA_THEME_DIR . 'inc/metabox/class-astra-meta-box-operations.php';
require_once ASTRA_THEME_DIR . 'inc/metabox/class-astra-elementor-editor-settings.php';

/**
 * Customizer additions.
 */
require_once ASTRA_THEME_DIR . 'inc/customizer/class-astra-customizer.php';

/**
 * Astra Modules.
 */
require_once ASTRA_THEME_DIR . 'inc/modules/posts-structures/class-astra-post-structures.php';
require_once ASTRA_THEME_DIR . 'inc/modules/related-posts/class-astra-related-posts.php';

/**
 * Compatibility
 */
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-gutenberg.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-jetpack.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/woocommerce/class-astra-woocommerce.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/edd/class-astra-edd.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/lifterlms/class-astra-lifterlms.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/learndash/class-astra-learndash.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-beaver-builder.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-bb-ultimate-addon.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-contact-form-7.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-visual-composer.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-site-origin.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-gravity-forms.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-bne-flyout.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-ubermeu.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-divi-builder.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-amp.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-yoast-seo.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/surecart/class-astra-surecart.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-starter-content.php';
require_once ASTRA_THEME_DIR . 'inc/addons/transparent-header/class-astra-ext-transparent-header.php';
require_once ASTRA_THEME_DIR . 'inc/addons/breadcrumbs/class-astra-breadcrumbs.php';
require_once ASTRA_THEME_DIR . 'inc/addons/scroll-to-top/class-astra-scroll-to-top.php';
require_once ASTRA_THEME_DIR . 'inc/addons/heading-colors/class-astra-heading-colors.php';
require_once ASTRA_THEME_DIR . 'inc/builder/class-astra-builder-loader.php';

// Elementor Compatibility requires PHP 5.4 for namespaces.
if ( version_compare( PHP_VERSION, '5.4', '>=' ) ) {
    require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-elementor.php';
    require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-elementor-pro.php';
    require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-web-stories.php';
}

// Beaver Themer compatibility requires PHP 5.3 for anonymous functions.
if ( version_compare( PHP_VERSION, '5.3', '>=' ) ) {
    require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-beaver-themer.php';
}

require_once ASTRA_THEME_DIR . 'inc/core/markup/class-astra-markup.php';

/**
 * Load deprecated functions
 */
require_once ASTRA_THEME_DIR . 'inc/core/deprecated/deprecated-filters.php';
require_once ASTRA_THEME_DIR . 'inc/core/deprecated/deprecated-hooks.php';
require_once ASTRA_THEME_DIR . 'inc/core/deprecated/deprecated-functions.php';


/**
 * ShiftZoneR - Functions personnalisées
 * À ajouter dans le fichier functions.php de votre thème
 * Version: 1.0
 */

// Sécurité
if (!defined('ABSPATH')) {
    exit;
}

/**
 * ===============================================
 * 1. ENREGISTREMENT DES TAXONOMIES ET POST TYPES
 * ===============================================
 */

// Enregistrer les taxonomies et post types au démarrage
add_action('init', 'shiftzoner_register_taxonomies');
add_action('init', 'shiftzoner_register_post_types');

function shiftzoner_register_taxonomies() {
    // Marque
    register_taxonomy('car_brand', 'post', [
        'label' => 'Marques',
        'public' => true,
        'hierarchical' => true,
        'show_ui' => true,
        'show_in_rest' => true,
        'show_admin_column' => true,
        'rewrite' => ['slug' => 'marque'],
        'labels' => [
            'name' => 'Marques',
            'singular_name' => 'Marque',
            'search_items' => 'Rechercher des marques',
            'all_items' => 'Toutes les marques',
            'edit_item' => 'Modifier la marque',
            'add_new_item' => 'Ajouter une marque',
        ],
    ]);
    
    // Modèle
    register_taxonomy('car_model', 'post', [
        'label' => 'Modèles',
        'public' => true,
        'hierarchical' => true,
        'show_ui' => true,
        'show_in_rest' => true,
        'show_admin_column' => true,
        'rewrite' => ['slug' => 'modele'],
        'labels' => [
            'name' => 'Modèles',
            'singular_name' => 'Modèle',
            'search_items' => 'Rechercher des modèles',
            'all_items' => 'Tous les modèles',
            'parent_item' => 'Marque parente',
            'edit_item' => 'Modifier le modèle',
            'add_new_item' => 'Ajouter un modèle',
        ],
    ]);
    
    // Année
    register_taxonomy('car_year', 'post', [
        'label' => 'Années',
        'public' => true,
        'hierarchical' => false,
        'show_ui' => true,
        'show_in_rest' => true,
        'show_admin_column' => true,
        'rewrite' => ['slug' => 'annee'],
    ]);
    
    // Tags photos
    register_taxonomy('photo_tag', 'post', [
        'label' => 'Tags Photos',
        'public' => true,
        'hierarchical' => false,
        'show_ui' => true,
        'show_in_rest' => true,
        'show_admin_column' => true,
        'rewrite' => ['slug' => 'tag'],
    ]);
}

function shiftzoner_register_post_types() {
    register_post_type('car_photo', [
        'label' => 'Photos Auto',
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-camera',
        'supports' => ['title', 'editor', 'thumbnail', 'author', 'comments', 'excerpt'],
        'show_in_rest' => true,
        'rewrite' => ['slug' => 'photo'],
        'taxonomies' => ['car_brand', 'car_model', 'car_year', 'photo_tag'],
        'labels' => [
            'name' => 'Photos',
            'singular_name' => 'Photo',
            'add_new' => 'Ajouter une photo',
            'add_new_item' => 'Ajouter une nouvelle photo',
            'edit_item' => 'Modifier la photo',
            'search_items' => 'Rechercher des photos',
        ],
    ]);
}

/**
 * ===============================================
 * 2. SYSTÈME DE VOTES
 * ===============================================
 */

// Ajouter les meta boxes pour les votes
add_action('add_meta_boxes', 'shiftzoner_add_vote_metabox');

function shiftzoner_add_vote_metabox() {
    add_meta_box(
        'shiftzoner_votes',
        'Votes',
        'shiftzoner_votes_metabox_html',
        ['post', 'car_photo'],
        'side',
        'default'
    );
}

function shiftzoner_votes_metabox_html($post) {
    $votes = get_post_meta($post->ID, '_shiftzoner_votes', true) ?: 0;
    echo '<p><strong>Total votes:</strong> ' . $votes . '</p>';
}

// AJAX pour voter
add_action('wp_ajax_shiftzoner_vote', 'shiftzoner_handle_vote');
add_action('wp_ajax_nopriv_shiftzoner_vote', 'shiftzoner_handle_vote');

function shiftzoner_handle_vote() {
    check_ajax_referer('shiftzoner_vote_nonce', 'nonce');
    
    if (!is_user_logged_in()) {
        wp_send_json_error('Vous devez être connecté pour voter');
    }
    
    $post_id = intval($_POST['post_id']);
    $vote_type = sanitize_text_field($_POST['vote_type']); // 'up' ou 'down'
    $user_id = get_current_user_id();
    
    // Vérifier si l'utilisateur a déjà voté
    $user_votes = get_post_meta($post_id, '_shiftzoner_user_votes', true) ?: [];
    
    if (isset($user_votes[$user_id])) {
        wp_send_json_error('Vous avez déjà voté pour cette photo');
    }
    
    // Enregistrer le vote
    $current_votes = get_post_meta($post_id, '_shiftzoner_votes', true) ?: 0;
    $new_votes = $vote_type === 'up' ? $current_votes + 1 : $current_votes - 1;
    
    update_post_meta($post_id, '_shiftzoner_votes', $new_votes);
    
    $user_votes[$user_id] = $vote_type;
    update_post_meta($post_id, '_shiftzoner_user_votes', $user_votes);
    
    // Mettre à jour le karma de l'auteur
    $author_id = get_post_field('post_author', $post_id);
    $author_karma = get_user_meta($author_id, 'shiftzoner_karma', true) ?: 0;
    $new_karma = $vote_type === 'up' ? $author_karma + 1 : $author_karma - 1;
    update_user_meta($author_id, 'shiftzoner_karma', $new_karma);
    
    wp_send_json_success(['votes' => $new_votes, 'karma' => $new_karma]);
}

/**
 * ===============================================
 * 3. COULEUR UTILISATEUR
 * ===============================================
 */

// Ajouter un champ couleur au profil
add_action('show_user_profile', 'shiftzoner_user_color_field');
add_action('edit_user_profile', 'shiftzoner_user_color_field');

function shiftzoner_user_color_field($user) {
    $color = get_user_meta($user->ID, 'shiftzoner_user_color', true) ?: '#667eea';
    ?>
    <h3>ShiftZoneR</h3>
    <table class="form-table">
        <tr>
            <th><label for="shiftzoner_user_color">Couleur personnelle</label></th>
            <td>
                <input type="color" name="shiftzoner_user_color" id="shiftzoner_user_color" value="<?php echo esc_attr($color); ?>" />
                <p class="description">Choisissez votre couleur personnelle qui apparaîtra sur vos publications.</p>
            </td>
        </tr>
    </table>
    <?php
}

// Sauvegarder la couleur
add_action('personal_options_update', 'shiftzoner_save_user_color');
add_action('edit_user_profile_update', 'shiftzoner_save_user_color');

function shiftzoner_save_user_color($user_id) {
    if (current_user_can('edit_user', $user_id)) {
        $color = sanitize_hex_color($_POST['shiftzoner_user_color']);
        update_user_meta($user_id, 'shiftzoner_user_color', $color);
    }
}

// Assigner une couleur aléatoire lors de l'inscription
add_action('user_register', 'shiftzoner_assign_random_color');

function shiftzoner_assign_random_color($user_id) {
    $colors = ['#667eea', '#764ba2', '#f093fb', '#4facfe', '#43e97b', '#fa709a', '#fee140', '#30cfd0'];
    $random_color = $colors[array_rand($colors)];
    update_user_meta($user_id, 'shiftzoner_user_color', $random_color);
}

/**
 * ===============================================
 * 4. SHORTCODES
 * ===============================================
 */

// Shortcode galerie
add_shortcode('shiftzoner_gallery', 'shiftzoner_gallery_shortcode');

function shiftzoner_gallery_shortcode($atts) {
    $atts = shortcode_atts([
        'posts_per_page' => 20,
        'brand'  => '',
        'model'  => '',
        'year'   => '',
        'orderby'=> 'date',
    ], $atts);

    // si pas passé en atts, lire le GET
    foreach (['brand','model','year','orderby'] as $k) {
        if (empty($atts[$k]) && isset($_GET[$k])) {
            $atts[$k] = sanitize_text_field($_GET[$k]);
        }
    }
    
    $args = [
        'post_type' => ['post', 'car_photo'],
        'posts_per_page' => $atts['posts_per_page'],
        'post_status' => 'publish',
    ];
    
    // Filtres taxonomie
    $tax_query = [];
    if (!empty($atts['brand'])) {
        $tax_query[] = [
            'taxonomy' => 'car_brand',
            'field' => 'slug',
            'terms' => $atts['brand'],
        ];
    }
    if (!empty($atts['model'])) {
        $tax_query[] = [
            'taxonomy' => 'car_model',
            'field' => 'slug',
            'terms' => $atts['model'],
        ];
    }
    if (!empty($atts['year'])) {
        $tax_query[] = [
            'taxonomy' => 'car_year',
            'field' => 'slug',
            'terms' => $atts['year'],
        ];
    }
    
    if (!empty($tax_query)) {
        $args['tax_query'] = $tax_query;
    }
    
    $query = new WP_Query($args);
    
    ob_start();
    ?>
    <div class="shiftzoner-gallery" id="shiftzoner-gallery">
        <div class="shiftzoner-filters">
            <?php echo shiftzoner_get_filter_form(); ?>
        </div>
        
        <div class="shiftzoner-grid">
            <?php
            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();
                    shiftzoner_render_photo_card(get_the_ID());
                }
            } else {
                echo '<p>Aucune photo pour le moment.</p>';
            }
            wp_reset_postdata();
            ?>
        </div>
        
        <?php if ($query->max_num_pages > 1): ?>
        <div class="shiftzoner-load-more">
            <button class="btn-load-more" data-page="1" data-max="<?php echo $query->max_num_pages; ?>">
                Charger plus
            </button>
        </div>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}

// Shortcode carte
add_shortcode('shiftzoner_map', 'shiftzoner_map_shortcode');

function shiftzoner_map_shortcode() {
    ob_start();
    ?>
    <div class="shiftzoner-map-container">
        <div id="shiftzoner-map" style="height: 600px; width: 100%;"></div>
        <div id="shiftzoner-map-error" style="display:none; padding:20px; text-align:center; color:#991b1b; background:#fef2f2; border:1px solid #fecaca; border-radius:8px;">
            <strong>Erreur de chargement</strong><br>
            La bibliothèque Leaflet n'est pas disponible. Veuillez réessayer plus tard.
        </div>
        <div class="map-filters">
            <?php echo shiftzoner_get_filter_form(); ?>
        </div>
    </div>

    <script>
    (function() {
        function initLeafletMap() {
            // Vérifier que Leaflet est chargé
            if (typeof L === 'undefined') {
                console.error('Leaflet n\'est pas chargé, nouvelle tentative dans 100ms');
                setTimeout(initLeafletMap, 100);
                return;
            }

            var $ = jQuery;

            try {
                // Initialiser la carte Leaflet
                var map = L.map('shiftzoner-map').setView([48.8566, 2.3522], 6);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Charger les points GPS
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'shiftzoner_get_map_points',
                    nonce: '<?php echo wp_create_nonce('shiftzoner_map_nonce'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        response.data.forEach(function(point) {
                            var marker = L.marker([point.lat, point.lng]).addTo(map);
                            marker.bindPopup(`
                                <div class="map-popup">
                                    <img src="${point.thumbnail}" alt="${point.title}" />
                                    <h4>${point.title}</h4>
                                    <p>${point.brand} ${point.model} (${point.year})</p>
                                    <a href="${point.url}">Voir la photo</a>
                                </div>
                            `);
                        });
                    }
                },
                error: function() {
                    console.error('Erreur lors du chargement des points de la carte');
                }
            });
        } catch (error) {
            console.error('Erreur lors de l\'initialisation de la carte:', error);
            var $ = jQuery;
            $('#shiftzoner-map').hide();
            $('#shiftzoner-map-error').show();
        }
    }

    // Démarrer l'initialisation
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initLeafletMap);
    } else {
        initLeafletMap();
    }
    })();
    </script>
    <?php
    return ob_get_clean();
}

// AJAX pour charger les points de la carte
add_action('wp_ajax_shiftzoner_get_map_points', 'shiftzoner_get_map_points');
add_action('wp_ajax_nopriv_shiftzoner_get_map_points', 'shiftzoner_get_map_points');

function shiftzoner_get_map_points() {
    check_ajax_referer('shiftzoner_map_nonce', 'nonce');
    
    $args = [
        'post_type' => ['post', 'car_photo'],
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'meta_query' => [
            'relation' => 'AND',
            [
                'key' => 'gps_consent',
                'value' => '1',
                'compare' => '=',
            ],
            [
                'key' => 'gps_lat',
                'compare' => 'EXISTS',
            ],
            [
                'key' => 'gps_lng',
                'compare' => 'EXISTS',
            ],
        ],
    ];
    
    $query = new WP_Query($args);
    $points = [];
    
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();
            
            $lat = get_post_meta($post_id, 'gps_lat', true);
            $lng = get_post_meta($post_id, 'gps_lng', true);
            
            if ($lat && $lng) {
                $brand = wp_get_post_terms($post_id, 'car_brand', ['fields' => 'names']);
                $model = wp_get_post_terms($post_id, 'car_model', ['fields' => 'names']);
                $year = get_post_meta($post_id, 'car_year', true);
                
                $points[] = [
                    'lat' => floatval($lat),
                    'lng' => floatval($lng),
                    'title' => get_the_title(),
                    'thumbnail' => get_the_post_thumbnail_url($post_id, 'thumbnail'),
                    'url' => get_permalink(),
                    'brand' => !empty($brand) ? $brand[0] : '',
                    'model' => !empty($model) ? $model[0] : '',
                    'year' => $year,
                ];
            }
        }
        wp_reset_postdata();
    }
    
    wp_send_json_success($points);
}

/**
 * ===============================================
 * 5. AFFICHAGE DES CARTES PHOTO
 * ===============================================
 */

function shiftzoner_render_photo_card($post_id) {
    $author_id = get_post_field('post_author', $post_id);
    $author_name = get_the_author_meta('display_name', $author_id);
    $author_color = get_user_meta($author_id, 'shiftzoner_user_color', true) ?: '#667eea';
    $votes = get_post_meta($post_id, '_shiftzoner_votes', true) ?: 0;
    $comments_count = get_comments_number($post_id);
    
    $brand = wp_get_post_terms($post_id, 'car_brand', ['fields' => 'names']);
    $model = wp_get_post_terms($post_id, 'car_model', ['fields' => 'names']);
    $year = get_post_meta($post_id, 'car_year', true);
    
    $thumbnail = get_the_post_thumbnail_url($post_id, 'medium');
    ?>
    <div class="photo-card" data-id="<?php echo $post_id; ?>">
        <div class="photo-image">
            <a href="<?php echo get_permalink($post_id); ?>">
                <img src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo esc_attr(get_the_title($post_id)); ?>" loading="lazy" />
            </a>
        </div>
        
        <div class="photo-info">
            <div class="photo-meta">
                <span class="author" style="color: <?php echo esc_attr($author_color); ?>;">
                    <?php echo esc_html($author_name); ?>
                </span>
                
                <div class="car-details">
                    <?php if (!empty($brand)): ?>
                        <span class="brand"><?php echo esc_html($brand[0]); ?></span>
                    <?php endif; ?>
                    
                    <?php if (!empty($model)): ?>
                        <span class="model"><?php echo esc_html($model[0]); ?></span>
                    <?php endif; ?>
                    
                    <?php if ($year): ?>
                        <span class="year">(<?php echo esc_html($year); ?>)</span>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="photo-actions">
                <button class="vote-btn vote-up" data-post="<?php echo $post_id; ?>" data-vote="up">
                    ▲ <?php echo $votes; ?>
                </button>
                
                <a href="<?php echo get_permalink($post_id); ?>#comments" class="comments-link">
                    💬 <?php echo $comments_count; ?>
                </a>
            </div>
        </div>
    </div>
    <?php
}

/**
 * ===============================================
 * 6. FORMULAIRE DE FILTRES
 * ===============================================
 */

function shiftzoner_get_filter_form() {
    $brands = get_terms(['taxonomy' => 'car_brand', 'hide_empty' => false]);

    // on relit la sélection (GET) pour garder l'état du formulaire
    $sel_brand  = isset($_GET['brand'])  ? sanitize_text_field($_GET['brand'])  : '';
    $sel_model  = isset($_GET['model'])  ? sanitize_text_field($_GET['model'])  : '';
    $sel_year   = isset($_GET['year'])   ? sanitize_text_field($_GET['year'])   : '';
    $sel_order  = isset($_GET['orderby'])? sanitize_text_field($_GET['orderby']): 'date';

    ob_start(); ?>
    <form class="shiftzoner-filters-form" method="get" id="shiftzoner-filters-form">
        <div class="filter-group">
            <label>marque</label>
            <select name="brand" id="szr-brand">
                <option value="">toutes les marques</option>
                <?php foreach ($brands as $brand): ?>
                    <option value="<?php echo esc_attr($brand->slug); ?>" <?php selected($sel_brand, $brand->slug); ?>>
                        <?php echo esc_html($brand->name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="filter-group">
            <label>modèle</label>
            <select name="model" id="szr-model" <?php echo $sel_brand ? '' : 'disabled'; ?>>
                <option value=""><?php echo $sel_brand ? 'tous les modèles' : 'choisir une marque'; ?></option>
                <?php
                // si une marque est déjà sélectionnée au chargement, on peuple côté php
                if ($sel_brand) {
                    // réutilise la logique du nouvel endpoint (synchro côté serveur)
                    $brand_term = get_term_by('slug', $sel_brand, 'car_brand');
                    if ($brand_term && !is_wp_error($brand_term)) {
                        $posts = get_posts([
                            'post_type'   => ['post','car_photo'],
                            'post_status' => 'publish',
                            'numberposts' => -1,
                            'fields'      => 'ids',
                            'tax_query'   => [[
                                'taxonomy' => 'car_brand',
                                'field'    => 'term_id',
                                'terms'    => (int)$brand_term->term_id,
                            ]],
                        ]);
                        if (!empty($posts)) {
                            $models = get_terms([
                                'taxonomy'   => 'car_model',
                                'hide_empty' => true,
                                'orderby'    => 'name',
                                'order'      => 'ASC',
                                'object_ids' => $posts,
                            ]);
                            if (!is_wp_error($models) && !empty($models)) {
                                foreach ($models as $m) {
                                    printf(
                                        '<option value="%s" %s>%s</option>',
                                        esc_attr($m->slug),
                                        selected($sel_model, $m->slug, false),
                                        esc_html($m->name)
                                    );
                                }
                            }
                        }
                    }
                }
                ?>
            </select>
        </div>

        <div class="filter-group">
            <label>année</label>
            <input type="number" name="year" min="1900" max="<?php echo (int)date('Y') + 1; ?>" placeholder="année" value="<?php echo esc_attr($sel_year); ?>" />
        </div>

        <div class="filter-group">
            <label>tri</label>
            <select name="orderby">
                <option value="date"     <?php selected($sel_order,'date'); ?>>plus récent</option>
                <option value="popular"  <?php selected($sel_order,'popular'); ?>>plus populaire</option>
                <option value="comments" <?php selected($sel_order,'comments'); ?>>plus commenté</option>
            </select>
        </div>

        <button type="submit" class="btn-filter">filtrer</button>
    </form>
    <?php
    return ob_get_clean();
}


/**
 * ===============================================
 * 7. SCRIPTS ET STYLES
 * ===============================================
 */

add_action('wp_enqueue_scripts', 'shiftzoner_enqueue_assets');

function shiftzoner_enqueue_assets() {
    // Leaflet pour la carte (chargé dans le header pour être disponible immédiatement)
    wp_enqueue_style('leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', [], '1.9.4');
    wp_enqueue_script('leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', [], '1.9.4', false);

    // jQuery
    wp_enqueue_script('jquery');

    // Script personnalisé (dépend de jQuery et Leaflet)
    wp_enqueue_script('shiftzoner-main', get_template_directory_uri() . '/js/shiftzoner.js', ['jquery', 'leaflet'], '1.0', true);

    // Localiser le script
    wp_localize_script('shiftzoner-main', 'shiftzoner', [
        'ajax_url'      => admin_url('admin-ajax.php'),
        'vote_nonce'    => wp_create_nonce('shiftzoner_vote_nonce'),
        'map_nonce'     => wp_create_nonce('shiftzoner_map_nonce'),
        'filters_nonce' => wp_create_nonce('shiftzoner_filters_nonce'),
    ]);

    // Styles personnalisés
    wp_enqueue_style('shiftzoner-main', get_template_directory_uri() . '/css/shiftzoner.css', [], '1.0');
}

/**
 * ===============================================
 * 8. LIMITES UPLOAD
 * ===============================================
 */

add_filter('wp_handle_upload_prefilter', 'shiftzoner_upload_limits');

function shiftzoner_upload_limits($file) {
    $user_id = get_current_user_id();
    
    // Vérifier le nombre d'uploads aujourd'hui
    $uploads_today = get_user_meta($user_id, 'shiftzoner_uploads_today', true) ?: [];
    $today = date('Y-m-d');
    
    if (!isset($uploads_today[$today])) {
        $uploads_today = [$today => 0];
    }
    
    if ($uploads_today[$today] >= 100) {
        $file['error'] = 'Vous avez atteint la limite de 100 uploads par jour.';
        return $file;
    }
    
    // Incrémenter le compteur
    $uploads_today[$today]++;
    update_user_meta($user_id, 'shiftzoner_uploads_today', $uploads_today);
    
    return $file;
}

/**
 * ===============================================
 * 9. WATERMARK AUTOMATIQUE
 * ===============================================
 */

add_filter('wp_generate_attachment_metadata', 'shiftzoner_auto_watermark', 10, 2);

function shiftzoner_auto_watermark($metadata, $attachment_id) {
    // Vérifier si c'est une image
    if (strpos(get_post_mime_type($attachment_id), 'image') === false) {
        return $metadata;
    }
    
    // Appliquer le watermark via Easy Watermark si disponible
    if (function_exists('ew_apply_watermark')) {
        ew_apply_watermark($attachment_id);
    }
    
    return $metadata;
}

/**
 * ===============================================
 * 10. BADGE OWNER
 * ===============================================
 */

add_filter('get_comment_author', 'shiftzoner_add_owner_badge', 10, 3);
add_filter('the_author', 'shiftzoner_add_owner_badge_post');

function shiftzoner_add_owner_badge($author, $comment_id = null, $comment = null) {
    if ($comment && $comment->user_id == 1) {
        return $author . ' <span class="owner-badge">👑 Owner</span>';
    }
    return $author;
}

function shiftzoner_add_owner_badge_post($author) {
    $author_id = get_the_author_meta('ID');
    if ($author_id == 1) {
        return $author . ' <span class="owner-badge">👑 Owner</span>';
    }
    return $author;
}

/**
 * ===============================================
 * 11. MODÉRATION
 * ===============================================
 */

// Ajouter un bouton de signalement
add_action('comment_form_after', 'shiftzoner_add_report_button');

function shiftzoner_add_report_button() {
    ?>
    <button class="btn-report-comment" data-comment-id="">
        🚩 Signaler un commentaire
    </button>
    <?php
}

// AJAX pour signaler
add_action('wp_ajax_shiftzoner_report_content', 'shiftzoner_handle_report');

function shiftzoner_handle_report() {
    check_ajax_referer('shiftzoner_report_nonce', 'nonce');
    
    if (!is_user_logged_in()) {
        wp_send_json_error('Vous devez être connecté');
    }
    
    $content_id = intval($_POST['content_id']);
    $content_type = sanitize_text_field($_POST['content_type']); // 'post' ou 'comment'
    $reason = sanitize_textarea_field($_POST['reason']);
    
    // Enregistrer le signalement
    $reports = get_option('shiftzoner_reports', []);
    $reports[] = [
        'content_id' => $content_id,
        'content_type' => $content_type,
        'reason' => $reason,
        'reporter' => get_current_user_id(),
        'date' => current_time('mysql'),
    ];
    update_option('shiftzoner_reports', $reports);
    
    // Notifier l'admin
    wp_mail(
        get_option('admin_email'),
        'ShiftZoneR - Nouveau signalement',
        "Un contenu a été signalé.\nType: {$content_type}\nID: {$content_id}\nRaison: {$reason}"
    );
    
    wp_send_json_success('Signalement envoyé');
}

/**
 * ===============================================
 * 12. STATISTIQUES UTILISATEUR
 * ===============================================
 */

function shiftzoner_get_user_stats($user_id) {
    $stats = [
        'posts_count' => count_user_posts($user_id),
        'karma' => get_user_meta($user_id, 'shiftzoner_karma', true) ?: 0,
        'member_since' => get_user_meta($user_id, 'user_registered', true),
        'comments_count' => get_comments(['user_id' => $user_id, 'count' => true]),
    ];
    
    return $stats;
}

// Afficher les stats dans le profil
add_action('show_user_profile', 'shiftzoner_display_user_stats');
add_action('edit_user_profile', 'shiftzoner_display_user_stats');

function shiftzoner_display_user_stats($user) {
    $stats = shiftzoner_get_user_stats($user->ID);
    ?>
    <h3>Statistiques ShiftZoneR</h3>
    <table class="form-table">
        <tr>
            <th>Photos publiées</th>
            <td><?php echo $stats['posts_count']; ?></td>
        </tr>
        <tr>
            <th>Karma</th>
            <td><?php echo $stats['karma']; ?></td>
        </tr>
        <tr>
            <th>Commentaires</th>
            <td><?php echo $stats['comments_count']; ?></td>
        </tr>
    </table>
    <?php
} 

?>
<?php


add_action('wp_ajax_shiftzoner_load_more', 'shiftzoner_ajax_load_more');
add_action('wp_ajax_nopriv_shiftzoner_load_more', 'shiftzoner_ajax_load_more');

function shiftzoner_ajax_load_more() {
    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $filters = isset($_POST['filters']) ? $_POST['filters'] : [];
    
    $args = [
        'post_type' => ['post', 'car_photo'],
        'posts_per_page' => 20,
        'paged' => $page,
        'post_status' => 'publish',
    ];
    
    // Appliquer les filtres
    $tax_query = [];
    if (!empty($filters['brand'])) {
        $tax_query[] = [
            'taxonomy' => 'car_brand',
            'field' => 'slug',
            'terms' => sanitize_text_field($filters['brand']),
        ];
    }
    
    if (!empty($filters['model'])) {
        $tax_query[] = [
            'taxonomy' => 'car_model',
            'field' => 'slug',
            'terms' => sanitize_text_field($filters['model']),
        ];
    }
    
    if (!empty($filters['year'])) {
        $tax_query[] = [
            'taxonomy' => 'car_year',
            'field' => 'slug',
            'terms' => sanitize_text_field($filters['year']),
        ];
    }
    
    if (!empty($tax_query)) {
        $args['tax_query'] = $tax_query;
    }
    
    // Tri
    if (!empty($filters['orderby'])) {
        switch ($filters['orderby']) {
            case 'popular':
                $args['meta_key'] = '_shiftzoner_votes';
                $args['orderby'] = 'meta_value_num';
                $args['order'] = 'DESC';
                break;
            case 'comments':
                $args['orderby'] = 'comment_count';
                $args['order'] = 'DESC';
                break;
            default:
                $args['orderby'] = 'date';
                $args['order'] = 'DESC';
        }
    }
    
    $query = new WP_Query($args);
    
    ob_start();
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            shiftzoner_render_photo_card(get_the_ID());
        }
    }
    $html = ob_get_clean();
    wp_reset_postdata();
    
    if ($query->have_posts()) {
        wp_send_json_success([
            'html' => $html,
            'max_pages' => $query->max_num_pages,
        ]);
    } else {
        wp_send_json_error('Aucun résultat');
    }
}

/**
 * ===============================================
 * 2. FILTRER LES PHOTOS
 * ===============================================
 */

add_action('wp_ajax_shiftzoner_filter_photos', 'shiftzoner_ajax_filter_photos');
add_action('wp_ajax_nopriv_shiftzoner_filter_photos', 'shiftzoner_ajax_filter_photos');

function shiftzoner_ajax_filter_photos() {
    $filters = isset($_POST['filters']) ? $_POST['filters'] : [];
    
    $args = [
        'post_type' => ['post', 'car_photo'],
        'posts_per_page' => 20,
        'post_status' => 'publish',
    ];
    
    // Même logique de filtrage que load_more
    $tax_query = [];
    if (!empty($filters['brand'])) {
        $tax_query[] = [
            'taxonomy' => 'car_brand',
            'field' => 'slug',
            'terms' => sanitize_text_field($filters['brand']),
        ];
    }
    
    if (!empty($filters['model'])) {
        $tax_query[] = [
            'taxonomy' => 'car_model',
            'field' => 'slug',
            'terms' => sanitize_text_field($filters['model']),
        ];
    }
    
    if (!empty($filters['year'])) {
        $tax_query[] = [
            'taxonomy' => 'car_year',
            'field' => 'slug',
            'terms' => sanitize_text_field($filters['year']),
        ];
    }
    
    if (!empty($tax_query)) {
        $args['tax_query'] = $tax_query;
    }
    
    // Tri
    if (!empty($filters['orderby'])) {
        switch ($filters['orderby']) {
            case 'popular':
                $args['meta_key'] = '_shiftzoner_votes';
                $args['orderby'] = 'meta_value_num';
                $args['order'] = 'DESC';
                break;
            case 'comments':
                $args['orderby'] = 'comment_count';
                $args['order'] = 'DESC';
                break;
            default:
                $args['orderby'] = 'date';
                $args['order'] = 'DESC';
        }
    }
    
    $query = new WP_Query($args);
    
    ob_start();
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            shiftzoner_render_photo_card(get_the_ID());
        }
    }
    $html = ob_get_clean();
    wp_reset_postdata();
    
    if ($query->have_posts()) {
        wp_send_json_success([
            'html' => $html,
            'max_pages' => $query->max_num_pages,
        ]);
    } else {
        wp_send_json_error('Aucun résultat');
    }
}

/**
 * ===============================================
 * 3. OBTENIR LES MODÈLES PAR MARQUE
 * ===============================================
 */

// remplace entièrement shiftzoner_ajax_get_models par ceci
add_action('wp_ajax_shiftzoner_get_models', 'shiftzoner_ajax_get_models');
add_action('wp_ajax_nopriv_shiftzoner_get_models', 'shiftzoner_ajax_get_models');

function shiftzoner_ajax_get_models() {
    // sécurité
    if ( empty($_POST['nonce']) || ! wp_verify_nonce($_POST['nonce'], 'shiftzoner_filters_nonce') ) {
        wp_send_json_error('nonce invalide');
    }

    $brand_slug = isset($_POST['brand']) ? sanitize_text_field($_POST['brand']) : '';
    if ( $brand_slug === '' ) {
        wp_send_json_success([]); // aucune marque => pas de modèles
    }

    // 1) récupérer la marque
    $brand_term = get_term_by('slug', $brand_slug, 'car_brand');
    if ( ! $brand_term || is_wp_error($brand_term) ) {
        wp_send_json_success([]);
    }

    // 2) trouver des posts ayant cette marque (on récupère juste les IDs)
    $posts = get_posts([
        'post_type'      => ['post', 'car_photo'],
        'post_status'    => 'publish',
        'numberposts'    => -1,
        'fields'         => 'ids',
        'tax_query'      => [[
            'taxonomy' => 'car_brand',
            'field'    => 'term_id',
            'terms'    => (int) $brand_term->term_id,
        ]],
    ]);

    if ( empty($posts) ) {
        wp_send_json_success([]);
    }

    // 3) lister les modèles présents sur ces posts
    // get_terms accepte object_ids pour limiter aux objets donnés
    $models = get_terms([
        'taxonomy'   => 'car_model',
        'hide_empty' => true,
        'orderby'    => 'name',
        'order'      => 'ASC',
        'object_ids' => $posts,
    ]);

    if ( is_wp_error($models) || empty($models) ) {
        wp_send_json_success([]);
    }

    $out = [];
    foreach ($models as $m) {
        $out[] = ['slug' => $m->slug, 'name' => $m->name];
    }

    wp_send_json_success($out);
}


/**
 * ===============================================
 * 4. DÉTAILS COMPLETS D'UNE PHOTO (LIGHTBOX)
 * ===============================================
 */

add_action('wp_ajax_shiftzoner_get_photo_details', 'shiftzoner_ajax_get_photo_details');
add_action('wp_ajax_nopriv_shiftzoner_get_photo_details', 'shiftzoner_ajax_get_photo_details');

function shiftzoner_ajax_get_photo_details() {
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    
    if (!$post_id) {
        wp_send_json_error('Photo non trouvée');
    }
    
    $post = get_post($post_id);
    if (!$post) {
        wp_send_json_error('Photo non trouvée');
    }
    
    // Récupérer toutes les infos
    $author_id = $post->post_author;
    $author = get_userdata($author_id);
    $author_color = get_user_meta($author_id, 'shiftzoner_user_color', true) ?: '#667eea';
    
    $brand = wp_get_post_terms($post_id, 'car_brand', ['fields' => 'names']);
    $model = wp_get_post_terms($post_id, 'car_model', ['fields' => 'names']);
    $year = get_post_meta($post_id, 'car_year', true);
    $location = get_post_meta($post_id, 'location', true);
    $votes = get_post_meta($post_id, '_shiftzoner_votes', true) ?: 0;
    
    $gps_consent = get_post_meta($post_id, 'gps_consent', true);
    $gps_lat = get_post_meta($post_id, 'gps_lat', true);
    $gps_lng = get_post_meta($post_id, 'gps_lng', true);
    
    ob_start();
    ?>
    <div class="lightbox-full-info">
        <h2><?php echo esc_html($post->post_title); ?></h2>
        
        <div class="author-info">
            <span style="color: <?php echo esc_attr($author_color); ?>;">
                Par <strong><?php echo esc_html($author->display_name); ?></strong>
            </span>
            <?php if ($author_id == 1): ?>
                <span class="owner-badge">👑 Owner</span>
            <?php endif; ?>
        </div>
        
        <div class="car-info">
            <p><strong>Véhicule:</strong> 
                <?php 
                echo !empty($brand) ? esc_html($brand[0]) : '';
                echo !empty($model) ? ' ' . esc_html($model[0]) : '';
                echo $year ? ' (' . esc_html($year) . ')' : '';
                ?>
            </p>
            <?php if ($location): ?>
                <p><strong>Lieu:</strong> <?php echo esc_html($location); ?></p>
            <?php endif; ?>
        </div>
        
        <?php if ($post->post_content): ?>
        <div class="description">
            <h3>Description</h3>
            <?php echo wpautop($post->post_content); ?>
        </div>
        <?php endif; ?>
        
        <div class="photo-stats">
            <span>👍 <?php echo $votes; ?> votes</span>
            <span>💬 <?php echo get_comments_number($post_id); ?> commentaires</span>
            <span>👁️ Publié le <?php echo get_the_date('', $post_id); ?></span>
        </div>
        
        <?php if ($gps_consent && $gps_lat && $gps_lng): ?>
        <div class="gps-info">
            <a href="<?php echo esc_url(get_permalink(get_page_by_path('carte'))); ?>?photo=<?php echo $post_id; ?>" class="btn">
                📍 Voir sur la carte
            </a>
        </div>
        <?php endif; ?>
        
        <div class="actions">
            <a href="<?php echo get_permalink($post_id); ?>" class="btn">Voir la page complète</a>
        </div>
    </div>
    <?php
    $html = ob_get_clean();
    
    wp_send_json_success(['html' => $html]);
}

/**
 * ===============================================
 * 5. RECHERCHE GLOBALE (AUTOCOMPLETE)
 * ===============================================
 */

//add_action('wp_ajax_shiftzoner_search', 'shiftzoner_ajax_search');
//add_action('wp_ajax_nopriv_shiftzoner_search', 'shiftzoner_ajax_search');

function shiftzoner_ajax_search() {
    $query = isset($_POST['query']) ? sanitize_text_field($_POST['query']) : '';
    
    if (strlen($query) < 3) {
        wp_send_json_error('Requête trop courte');
    }
    
    // Recherche de photos
    $photos_query = new WP_Query([
        'post_type' => ['post', 'car_photo'],
        's' => $query,
        'posts_per_page' => 5,
    ]);
    
    $photos = [];
    if ($photos_query->have_posts()) {
        while ($photos_query->have_posts()) {
            $photos_query->the_post();
            $photos[] = [
                'title' => get_the_title(),
                'url' => get_permalink(),
                'thumbnail' => get_the_post_thumbnail_url(get_the_ID(), 'thumbnail'),
            ];
        }
        wp_reset_postdata();
    }
    
    // Recherche de topics forum
    $topics = [];
    if (function_exists('bbp_get_topic_post_type')) {
        $topics_query = new WP_Query([
            'post_type' => bbp_get_topic_post_type(),
            's' => $query,
            'posts_per_page' => 5,
        ]);
        
        if ($topics_query->have_posts()) {
            while ($topics_query->have_posts()) {
                $topics_query->the_post();
                $topics[] = [
                    'title' => get_the_title(),
                    'url' => get_permalink(),
                ];
            }
            wp_reset_postdata();
        }
    }
    
    wp_send_json_success([
        'photos' => $photos,
        'topics' => $topics,
    ]);
}

/**
 * ===============================================
 * 6. SIGNALER UN CONTENU
 * ===============================================
 */

add_action('wp_ajax_shiftzoner_report_content', 'shiftzoner_ajax_report_content');

function shiftzoner_ajax_report_content() {
    check_ajax_referer('shiftzoner_report_nonce', 'nonce');
    
    if (!is_user_logged_in()) {
        wp_send_json_error('Vous devez être connecté');
    }
    
    $content_id = isset($_POST['content_id']) ? intval($_POST['content_id']) : 0;
    $content_type = isset($_POST['content_type']) ? sanitize_text_field($_POST['content_type']) : '';
    $reason = isset($_POST['reason']) ? sanitize_textarea_field($_POST['reason']) : '';
    
    if (!$content_id || !$content_type || !$reason) {
        wp_send_json_error('Données incomplètes');
    }
    
    // Enregistrer le signalement
    $reports = get_option('shiftzoner_reports', []);
    $reports[] = [
        'content_id' => $content_id,
        'content_type' => $content_type,
        'reason' => $reason,
        'reporter' => get_current_user_id(),
        'date' => current_time('mysql'),
    ];
    update_option('shiftzoner_reports', $reports);
    
    // Compter les signalements pour ce contenu
    $count = 0;
    foreach ($reports as $report) {
        if ($report['content_id'] == $content_id && $report['content_type'] == $content_type) {
            $count++;
        }
    }
    
    // Auto-modération si trop de signalements
    if (defined('SHIFTZONER_REPORTS_AUTO_HIDE') && $count >= SHIFTZONER_REPORTS_AUTO_HIDE) {
        if ($content_type === 'post') {
            wp_update_post([
                'ID' => $content_id,
                'post_status' => 'pending',
            ]);
        } elseif ($content_type === 'comment') {
            wp_set_comment_status($content_id, 'hold');
        }
    }
    
    // Notifier l'admin par email
    if (defined('SHIFTZONER_REPORTS_EMAIL_ADMIN') && SHIFTZONER_REPORTS_EMAIL_ADMIN) {
        $admin_email = get_option('admin_email');
        $subject = '[ShiftZoneR] Nouveau signalement';
        $message = "Un contenu a été signalé.\n\n";
        $message .= "Type: {$content_type}\n";
        $message .= "ID: {$content_id}\n";
        $message .= "Raison: {$reason}\n";
        $message .= "Par: " . wp_get_current_user()->display_name . "\n";
        $message .= "\nNombre total de signalements pour ce contenu: {$count}";
        
        wp_mail($admin_email, $subject, $message);
    }
    
    wp_send_json_success('Signalement enregistré');
}

/**
 * ===============================================
 * 7. METTRE À JOUR LE KARMA UTILISATEUR
 * ===============================================
 */

function shiftzoner_update_user_karma($user_id, $points) {
    $current_karma = get_user_meta($user_id, 'shiftzoner_karma', true) ?: 0;
    $new_karma = $current_karma + $points;
    update_user_meta($user_id, 'shiftzoner_karma', $new_karma);
    
    // Vérifier si un palier est atteint
    if (function_exists('shiftzoner_get_karma_level')) {
        $old_level = shiftzoner_get_karma_level($current_karma);
        $new_level = shiftzoner_get_karma_level($new_karma);
        
        if ($old_level !== $new_level) {
            // Hook pour les paliers
            do_action('shiftzoner_karma_milestone', $user_id, $new_karma, $new_level);
            
            // Notification email (si activée)
            if (defined('SHIFTZONER_NOTIFY_KARMA_MILESTONE') && SHIFTZONER_NOTIFY_KARMA_MILESTONE) {
                $user = get_userdata($user_id);
                $subject = '[ShiftZoneR] Félicitations ! Nouveau niveau de karma';
                $message = "Bonjour {$user->display_name},\n\n";
                $message .= "Vous avez atteint {$new_karma} points de karma !\n";
                $message .= "Vous êtes maintenant un(e) {$new_level}.\n\n";
                $message .= "Continuez comme ça !\n\n";
                $message .= "L'équipe ShiftZoneR";
                
                wp_mail($user->user_email, $subject, $message);
            }
        }
    }
    
    return $new_karma;
}

/**
 * ===============================================
 * 8. LOGGER LES UPLOADS
 * ===============================================
 */

add_action('add_attachment', 'shiftzoner_log_upload');

function shiftzoner_log_upload($attachment_id) {
    if (!is_user_logged_in()) {
        return;
    }
    
    // Vérifier si c'est une image
    if (!wp_attachment_is_image($attachment_id)) {
        return;
    }
    
    $user_id = get_current_user_id();
    
    // Logger dans les métadonnées utilisateur
    $uploads_today = get_user_meta($user_id, 'shiftzoner_uploads_today', true) ?: [];
    $today = date('Y-m-d');
    
    if (!isset($uploads_today[$today])) {
        $uploads_today[$today] = 0;
    }
    
    $uploads_today[$today]++;
    update_user_meta($user_id, 'shiftzoner_uploads_today', $uploads_today);
    
    // Logger dans le fichier si activé
    if (defined('SHIFTZONER_LOG_UPLOADS') && SHIFTZONER_LOG_UPLOADS && function_exists('shiftzoner_log')) {
        shiftzoner_log("User {$user_id} uploaded attachment {$attachment_id}", 'upload');
    }
}

/**
 * ===============================================
 * 9. EXTRAIRE EXIF GPS
 * ===============================================
 */

add_filter('wp_generate_attachment_metadata', 'shiftzoner_extract_gps', 20, 2);

function shiftzoner_extract_gps($metadata, $attachment_id) {
    $file = get_attached_file($attachment_id);
    
    if (!function_exists('exif_read_data') || !file_exists($file)) {
        return $metadata;
    }
    
    $exif = @exif_read_data($file);
    
    if (!$exif || !isset($exif['GPSLatitude']) || !isset($exif['GPSLongitude'])) {
        return $metadata;
    }
    
    // Convertir les coordonnées GPS
    $lat = shiftzoner_gps_to_decimal($exif['GPSLatitude'], $exif['GPSLatitudeRef']);
    $lng = shiftzoner_gps_to_decimal($exif['GPSLongitude'], $exif['GPSLongitudeRef']);
    
    // Sauvegarder temporairement (l'utilisateur devra donner son consentement)
    update_post_meta($attachment_id, '_temp_gps_lat', $lat);
    update_post_meta($attachment_id, '_temp_gps_lng', $lng);
    
    return $metadata;
}

function shiftzoner_gps_to_decimal($coordinate, $hemisphere) {
    if (!is_array($coordinate) || count($coordinate) < 3) {
        return 0;
    }
    
    $degrees = $coordinate[0];
    $minutes = $coordinate[1];
    $seconds = $coordinate[2];
    
    if (strpos($degrees, '/') !== false) {
        list($num, $den) = explode('/', $degrees);
        $degrees = $num / $den;
    }
    
    if (strpos($minutes, '/') !== false) {
        list($num, $den) = explode('/', $minutes);
        $minutes = $num / $den;
    }
    
    if (strpos($seconds, '/') !== false) {
        list($num, $den) = explode('/', $seconds);
        $seconds = $num / $den;
    }
    
    $decimal = $degrees + ($minutes / 60) + ($seconds / 3600);
    
    if ($hemisphere === 'S' || $hemisphere === 'W') {
        $decimal *= -1;
    }
    
    return $decimal;
}

/**
 * ===============================================
 * 10. NETTOYER LES DONNÉES ANCIENNES
 * ===============================================
 */

add_action('shiftzoner_daily_cleanup', 'shiftzoner_cleanup_old_uploads_data');

function shiftzoner_cleanup_old_uploads_data() {
    global $wpdb;
    
    // Nettoyer les compteurs d'uploads de plus de 7 jours
    $cutoff_date = date('Y-m-d', strtotime('-7 days'));
    
    $users = get_users(['fields' => 'ID']);
    foreach ($users as $user_id) {
        $uploads_data = get_user_meta($user_id, 'shiftzoner_uploads_today', true);
        
        if (!is_array($uploads_data)) {
            continue;
        }
        
        $filtered_data = array_filter($uploads_data, function($date) use ($cutoff_date) {
            return $date >= $cutoff_date;
        }, ARRAY_FILTER_USE_KEY);
        
        update_user_meta($user_id, 'shiftzoner_uploads_today', $filtered_data);
    }
}

// Planifier le nettoyage quotidien
if (!wp_next_scheduled('shiftzoner_daily_cleanup')) {
    wp_schedule_event(time(), 'daily', 'shiftzoner_daily_cleanup');
}

// ============================================
// SÉLECTEUR DE VOITURE - SCRIPTS ET STYLES
// ============================================

function car_selector_enqueue_assets() {
    wp_enqueue_style('car-selector-style', get_template_directory_uri() . '/css/car-selector.css', array(), '1.0.1');
    wp_enqueue_script('car-selector-script', get_template_directory_uri() . '/js/car-selector.js', array('jquery'), '1.0.1', true);
    wp_localize_script('car-selector-script', 'carSelectorData', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('car_selector_nonce'),
    ));
}
add_action('wp_enqueue_scripts', 'car_selector_enqueue_assets');

// ============================================
// AJAX - RÉCUPÉRER MODÈLES PAR MARQUE
// ============================================

add_action('wp_ajax_shiftzoner_get_models_by_brand', 'shiftzoner_get_models_by_brand_cb');
add_action('wp_ajax_nopriv_shiftzoner_get_models_by_brand', 'shiftzoner_get_models_by_brand_cb');

function shiftzoner_get_models_by_brand_cb() {
    check_ajax_referer('car_selector_nonce', 'nonce');
    
    $brand_id = isset($_POST['brand_id']) ? absint($_POST['brand_id']) : 0;
    if ($brand_id <= 0) wp_send_json_success(array());

    $brand = get_term($brand_id, 'car_brand');
    if (!$brand || is_wp_error($brand)) wp_send_json_success(array());

    $models_list = array();
    $parent_term = get_term_by('slug', $brand->slug, 'car_model');
    if (!$parent_term) $parent_term = get_term_by('name', $brand->name, 'car_model');

    if ($parent_term && !is_wp_error($parent_term)) {
        $children = get_terms(array(
            'taxonomy'   => 'car_model',
            'hide_empty' => false,
            'parent'     => absint($parent_term->term_id),
            'orderby'    => 'name',
            'order'      => 'ASC',
        ));
        if (!is_wp_error($children) && !empty($children)) {
            foreach ($children as $child) {
                $models_list[] = array('id' => absint($child->term_id), 'name' => esc_html($child->name));
            }
        }
    }

    if (empty($models_list)) {
        $models = get_terms(array(
            'taxonomy'   => 'car_model',
            'hide_empty' => false,
            'meta_query' => array(array('key' => '_szr_model_brand', 'value' => $brand_id, 'compare' => '=')),
            'orderby'    => 'name',
            'order'      => 'ASC',
        ));
        if (!is_wp_error($models) && !empty($models)) {
            foreach ($models as $model) {
                $models_list[] = array('id' => absint($model->term_id), 'name' => esc_html($model->name));
            }
        }
    }

    wp_send_json_success($models_list);
}

// ============================================
// SHORTCODE SÉLECTEUR DE VOITURE
// ============================================

function car_selector_shortcode($atts) {
    $atts = shortcode_atts(array(
        'action_url' => '',
        'show_reset' => 'yes',
        'title'      => 'Trouvez votre voiture',
        'subtitle'   => 'Sélectionnez la marque et le modèle de votre véhicule',
    ), $atts);

    $brands = get_terms(array('taxonomy' => 'car_brand', 'hide_empty' => false, 'orderby' => 'name', 'order' => 'ASC'));
    if (is_wp_error($brands) || empty($brands)) return '<p>Aucune marque disponible.</p>';

    $form_action = !empty($atts['action_url']) ? esc_url($atts['action_url']) : esc_url(home_url('/'));

    ob_start(); ?>
    <div class="car-selector-container">
        <h2 class="car-selector-title"><?php echo esc_html($atts['title']); ?></h2>
        <p class="car-selector-subtitle"><?php echo esc_html($atts['subtitle']); ?></p>
        <form class="car-selector-form" method="get" action="<?php echo $form_action; ?>">
            <div class="car-selector-field">
                <label for="car-brand-select"><span>🚗</span> Marque de voiture</label>
                <select id="car-brand-select" name="car_brand" required>
                    <option value="">-- Sélectionnez une marque --</option>
                    <?php foreach ($brands as $brand) : ?>
                        <option value="<?php echo esc_attr($brand->term_id); ?>"><?php echo esc_html($brand->name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="car-selector-loader"><div class="car-selector-loader-spinner"></div></div>
            <div class="car-model-wrapper">
                <div class="car-selector-field">
                    <label for="car-model-select"><span>🏎️</span> Modèle de voiture</label>
                    <select id="car-model-select" name="car_model" disabled required>
                        <option value="">-- Sélectionnez d'abord une marque --</option>
                    </select>
                </div>
            </div>
            <div class="car-selector-actions">
                <button type="submit" class="car-selector-btn car-selector-submit">🔍 Rechercher</button>
                <?php if ($atts['show_reset'] === 'yes') : ?>
                    <button type="button" class="car-selector-btn car-selector-reset">🔄 Réinitialiser</button>
                <?php endif; ?>
            </div>
            <div class="car-selector-info"><strong>💡 Astuce :</strong> Sélectionnez d'abord la marque, puis le modèle apparaîtra automatiquement.</div>
        </form>
    </div>
    <?php return ob_get_clean();
}
add_shortcode('car_selector', 'car_selector_shortcode');

// ============================================
// TAXONOMIES
// ============================================

function car_selector_register_taxonomies() {
    register_taxonomy('car_brand', array('post', 'car_photo'), array(
        'labels' => array('name' => 'Marques de voiture', 'singular_name' => 'Marque', 'menu_name' => 'Marques'),
        'hierarchical' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'marque'),
    ));
    register_taxonomy('car_model', array('post', 'car_photo'), array(
        'labels' => array('name' => 'Modèles de voiture', 'singular_name' => 'Modèle', 'menu_name' => 'Modèles'),
        'hierarchical' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'modele'),
    ));
    register_taxonomy('car_year', array('post', 'car_photo'), array(
        'labels' => array('name' => 'Années', 'singular_name' => 'Année', 'menu_name' => 'Années'),
        'hierarchical' => false,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'annee'),
    ));
}
add_action('init', 'car_selector_register_taxonomies');

// ============================================
// NOTIFICATIONS - SHORTCODE
// ============================================

add_shortcode('shiftzoner_notifications', function($atts){
    $atts = shortcode_atts(array(
        'position' => 'bottom-left',
        'interval_min' => 8,
        'interval_max' => 18,
        'burst' => 0,
    ), $atts);

    $pos = in_array($atts['position'], array('bottom-left','bottom-right','top-left','top-right')) ? $atts['position'] : 'bottom-left';
    $interval_min = max(3, intval($atts['interval_min']));
    $interval_max = max($interval_min+1, intval($atts['interval_max']));
    $burst = max(0, intval($atts['burst']));

    wp_enqueue_style('szr-notif', get_template_directory_uri() . '/css/szr-notif.css', array(), '1.0');
    wp_enqueue_script('szr-notif', get_template_directory_uri() . '/js/szr-notif.js', array('jquery'), '1.0', true);
    wp_localize_script('szr-notif', 'SZR_NOTIF', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('szr_notif_nonce'),
        'position' => $pos,
        'interval_min' => $interval_min * 1000,
        'interval_max' => $interval_max * 1000,
        'burst' => $burst,
        'strings' => array('submitted' => 'a soumis une photo'),
    ));

    ob_start(); ?>
    <div id="szr-notif-root" class="szr-notif-root <?php echo esc_attr($pos); ?>" aria-live="polite" aria-atomic="true"></div>
    <?php return ob_get_clean();
});

// ============================================
// NOTIFICATIONS - AJAX
// ============================================

add_action('wp_ajax_szr_fake_notification', 'szr_fake_notification_cb');
add_action('wp_ajax_nopriv_szr_fake_notification', 'szr_fake_notification_cb');

function szr_fake_notification_cb(){
    if (empty($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'szr_notif_nonce')) {
        wp_send_json_error(array('message'=>'nonce invalide'), 403);
    }

    $post = szr_notif_pick_random_photo();
    if (!$post) {
        $data = array(
            'user' => array('name' => 'invité mystère', 'avatar' => szr_notif_placeholder_avatar(), 'url' => home_url('/')),
            'photo' => array('title' => 'belle inconnue', 'thumb' => szr_notif_placeholder_image(), 'url' => home_url('/')),
            'meta' => array('brand' => 'marque inconnue', 'model' => 'modèle inconnu', 'year' => ''),
        );
        wp_send_json_success($data);
    }

    $post_id = $post->ID;
    $author_id = (int) $post->post_author;
    $author = get_user_by('id', $author_id);
    $avatar = get_avatar_url($author_id, array('size'=>64));
    if (!$avatar) $avatar = szr_notif_placeholder_avatar();
    $thumb = get_the_post_thumbnail_url($post_id, 'thumbnail');
    if (!$thumb) $thumb = szr_notif_placeholder_image();
    $brand_terms = wp_get_post_terms($post_id, 'car_brand', array('fields'=>'names'));
    $model_terms = wp_get_post_terms($post_id, 'car_model', array('fields'=>'names'));
    $year_terms = wp_get_post_terms($post_id, 'car_year', array('fields'=>'names'));
    $meta_year = get_post_meta($post_id, 'car_year', true);

    $data = array(
        'user' => array('name' => $author ? $author->display_name : 'utilisateur', 'avatar' => $avatar, 'url' => $author ? get_author_posts_url($author_id) : home_url('/')),
        'photo' => array('title' => get_the_title($post_id), 'thumb' => $thumb, 'url' => get_permalink($post_id)),
        'meta' => array('brand' => !empty($brand_terms) ? $brand_terms[0] : '', 'model' => !empty($model_terms) ? $model_terms[0] : '', 'year' => !empty($year_terms) ? $year_terms[0] : $meta_year),
    );
    wp_send_json_success($data);
}

function szr_notif_pick_random_photo(){
    $post = szr_notif_get_random_post(array('car_photo'));
    if (!$post) $post = szr_notif_get_random_post(array('post'));
    return $post;
}

function szr_notif_get_random_post($post_types){
    $q = new WP_Query(array('post_type' => $post_types, 'posts_per_page' => 1, 'post_status' => 'publish', 'orderby' => 'rand', 'no_found_rows' => true, 'ignore_sticky_posts' => true));
    return $q->have_posts() ? $q->posts[0] : null;
}

function szr_notif_placeholder_avatar(){
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64"><circle cx="32" cy="32" r="32" fill="#ddd"/><circle cx="32" cy="26" r="12" fill="#bbb"/><rect x="12" y="42" width="40" height="16" rx="8" fill="#bbb"/></svg>';
    return 'data:image/svg+xml;base64,' . base64_encode($svg);
}

function szr_notif_placeholder_image(){
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="96" height="64"><rect width="96" height="64" fill="#eee"/><path d="M0 56 L28 32 L44 46 L64 28 L96 56 L0 56Z" fill="#ddd"/><circle cx="20" cy="20" r="8" fill="#ccc"/></svg>';
    return 'data:image/svg+xml;base64,' . base64_encode($svg);
}

// ============================================
// UPLOAD D'IMAGES - CORRECTION DROITS
// ============================================

function allow_subscriber_uploads() {
    $subscriber = get_role('subscriber');
    if ($subscriber) {
        $subscriber->add_cap('upload_files');
        $subscriber->add_cap('edit_posts');
    }
    
    $contributor = get_role('contributor');
    if ($contributor) {
        $contributor->add_cap('upload_files');
    }
}
add_action('init', 'allow_subscriber_uploads');

function increase_upload_size_limit($size) {
    return 10485760;
}
add_filter('upload_size_limit', 'increase_upload_size_limit');

function custom_upload_mimes($mimes) {
    $mimes['jpg|jpeg|jpe'] = 'image/jpeg';
    $mimes['png'] = 'image/png';
    $mimes['gif'] = 'image/gif';
    $mimes['webp'] = 'image/webp';
    return $mimes;
}
add_filter('upload_mimes', 'custom_upload_mimes');

function fix_upload_permissions() {
    if (!current_user_can('upload_files') && is_user_logged_in()) {
        $user = wp_get_current_user();
        $user->add_cap('upload_files');
    }
}
add_action('admin_init', 'fix_upload_permissions');
add_action('wp_loaded', 'fix_upload_permissions');