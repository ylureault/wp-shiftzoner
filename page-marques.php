<?php
/**
 * Template Name: Marques & Modèles
 * Description: Page recensant toutes les marques et modèles avec logos, navigation fluide et SEO optimisé
 * Version: 2.0.0 - Refactored
 *
 * @package ShiftZoneR
 */

get_header();

// Récupérer les paramètres d'URL
$current_brand_slug = isset( $_GET['brand'] ) ? sanitize_title( $_GET['brand'] ) : '';
$current_model_slug = isset( $_GET['model'] ) ? sanitize_title( $_GET['model'] ) : '';
$search_query       = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '';

// Déterminer la vue actuelle
$current_view = 'brands'; // Par défaut : liste des marques
$current_brand = null;
$current_model = null;

if ( $current_brand_slug ) {
    $current_brand = get_term_by( 'slug', $current_brand_slug, 'car_brand' );
    if ( $current_brand && ! is_wp_error( $current_brand ) ) {
        $current_view = 'models'; // Vue modèles d'une marque

        if ( $current_model_slug ) {
            $current_model = get_term_by( 'slug', $current_model_slug, 'car_model' );
            if ( $current_model && ! is_wp_error( $current_model ) ) {
                $current_view = 'photos'; // Vue photos d'un modèle
            }
        }
    }
}

// SEO selon la vue
$page_title = '';
$page_description = '';

if ( $current_view === 'photos' && $current_model ) {
    $page_title = $current_brand->name . ' ' . $current_model->name . ' - Photos';
    $page_description = 'Découvrez toutes les photos de ' . $current_brand->name . ' ' . $current_model->name . ' partagées sur ShiftZoneR.';
} elseif ( $current_view === 'models' && $current_brand ) {
    $page_title = 'Modèles ' . $current_brand->name;
    $page_description = 'Explorez tous les modèles ' . $current_brand->name . ' avec photos de la communauté ShiftZoneR.';
} else {
    $page_title = 'Marques Automobiles';
    $page_description = 'Découvrez toutes les marques automobiles et leurs modèles sur ShiftZoneR.';
}
?>

<!-- SEO Meta Tags -->
<?php if ( $current_view !== 'brands' ) : ?>
<meta name="description" content="<?php echo esc_attr( $page_description ); ?>">
<meta property="og:title" content="<?php echo esc_attr( $page_title . ' - ' . get_bloginfo( 'name' ) ); ?>">
<meta property="og:description" content="<?php echo esc_attr( $page_description ); ?>">
<meta property="og:type" content="website">
<meta name="twitter:card" content="summary_large_image">
<?php endif; ?>

<div class="brands-page">
    <!-- Breadcrumb Navigation -->
    <div class="brands-breadcrumb">
        <div class="container">
            <nav aria-label="Fil d'Ariane">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Accueil</a>
                <span class="separator">/</span>

                <?php if ( $current_view === 'brands' ) : ?>
                    <span class="current">Marques</span>

                <?php elseif ( $current_view === 'models' && $current_brand ) : ?>
                    <a href="<?php echo esc_url( get_permalink() ); ?>">Marques</a>
                    <span class="separator">/</span>
                    <span class="current"><?php echo esc_html( $current_brand->name ); ?></span>

                <?php elseif ( $current_view === 'photos' && $current_brand && $current_model ) : ?>
                    <a href="<?php echo esc_url( get_permalink() ); ?>">Marques</a>
                    <span class="separator">/</span>
                    <a href="<?php echo esc_url( add_query_arg( 'brand', $current_brand->slug, get_permalink() ) ); ?>"><?php echo esc_html( $current_brand->name ); ?></a>
                    <span class="separator">/</span>
                    <span class="current"><?php echo esc_html( $current_model->name ); ?></span>
                <?php endif; ?>
            </nav>
        </div>
    </div>

    <!-- Header Section -->
    <div class="brands-header">
        <div class="container">
            <h1 class="page-title fade-in"><?php echo esc_html( $page_title ); ?></h1>
            <p class="page-description fade-in"><?php echo esc_html( $page_description ); ?></p>

            <!-- Recherche -->
            <div class="brands-search fade-in">
                <form method="get" action="<?php echo esc_url( get_permalink() ); ?>" class="search-form">
                    <?php if ( $current_brand_slug ) : ?>
                        <input type="hidden" name="brand" value="<?php echo esc_attr( $current_brand_slug ); ?>">
                    <?php endif; ?>
                    <?php if ( $current_model_slug ) : ?>
                        <input type="hidden" name="model" value="<?php echo esc_attr( $current_model_slug ); ?>">
                    <?php endif; ?>
                    <input type="text" name="s" placeholder="🔍 Rechercher une marque, un modèle..." value="<?php echo esc_attr( $search_query ); ?>" class="search-input">
                    <button type="submit" class="search-button">Rechercher</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Contenu Principal -->
    <div class="brands-content">
        <div class="container">

            <?php if ( $current_view === 'brands' ) : ?>
                <!-- ========== VUE: LISTE DES MARQUES ========== -->
                <?php
                $brands = get_terms( array(
                    'taxonomy'   => 'car_brand',
                    'hide_empty' => false,
                    'orderby'    => 'name',
                    'order'      => 'ASC',
                ) );

                if ( $search_query ) {
                    $brands = array_filter( $brands, function( $brand ) use ( $search_query ) {
                        return stripos( $brand->name, $search_query ) !== false;
                    } );
                }

                if ( ! empty( $brands ) && ! is_wp_error( $brands ) ) :
                    ?>
                    <div class="brands-grid">
                        <?php foreach ( $brands as $brand ) :
                            $logo = get_term_meta( $brand->term_id, '_szr_brand_logo_id', true );
                            $logo_url = $logo ? wp_get_attachment_image_url( $logo, 'medium' ) : '';
                            $photo_count = $brand->count;
                            ?>
                            <a href="<?php echo esc_url( add_query_arg( 'brand', $brand->slug, get_permalink() ) ); ?>" class="brand-card fade-in">
                                <div class="brand-logo">
                                    <?php if ( $logo_url ) : ?>
                                        <img src="<?php echo esc_url( $logo_url ); ?>" alt="Logo <?php echo esc_attr( $brand->name ); ?>" loading="lazy">
                                    <?php else : ?>
                                        <div class="brand-logo-placeholder">
                                            <span><?php echo esc_html( substr( $brand->name, 0, 1 ) ); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="brand-info">
                                    <h3 class="brand-name"><?php echo esc_html( $brand->name ); ?></h3>
                                    <p class="brand-count"><?php echo esc_html( $photo_count ); ?> photo<?php echo $photo_count > 1 ? 's' : ''; ?></p>
                                </div>
                                <div class="brand-arrow">→</div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <div class="no-results">
                        <h3>Aucune marque trouvée</h3>
                        <p>Essayez une autre recherche ou <a href="<?php echo esc_url( get_permalink() ); ?>">voir toutes les marques</a>.</p>
                    </div>
                <?php endif; ?>

            <?php elseif ( $current_view === 'models' && $current_brand ) : ?>
                <!-- ========== VUE: MODÈLES D'UNE MARQUE ========== -->

                <!-- En-tête de la marque -->
                <div class="brand-header">
                    <?php
                    $logo = get_term_meta( $current_brand->term_id, '_szr_brand_logo_id', true );
                    $logo_url = $logo ? wp_get_attachment_image_url( $logo, 'large' ) : '';
                    ?>
                    <?php if ( $logo_url ) : ?>
                        <div class="brand-header-logo">
                            <img src="<?php echo esc_url( $logo_url ); ?>" alt="Logo <?php echo esc_attr( $current_brand->name ); ?>" loading="lazy">
                        </div>
                    <?php endif; ?>
                    <h2><?php echo esc_html( $current_brand->name ); ?></h2>
                    <?php if ( $current_brand->description ) : ?>
                        <p class="brand-description"><?php echo esc_html( $current_brand->description ); ?></p>
                    <?php endif; ?>
                </div>

                <?php
                // Récupérer les modèles de cette marque
                $models = get_terms( array(
                    'taxonomy'   => 'car_model',
                    'hide_empty' => false,
                    'parent'     => $current_brand->term_id,
                    'orderby'    => 'name',
                    'order'      => 'ASC',
                ) );

                // Si pas de modèles en tant qu'enfants, chercher via meta
                if ( empty( $models ) || is_wp_error( $models ) ) {
                    $models = get_terms( array(
                        'taxonomy'   => 'car_model',
                        'hide_empty' => false,
                        'meta_query' => array(
                            array(
                                'key'     => '_szr_model_brand',
                                'value'   => $current_brand->term_id,
                                'compare' => '=',
                            ),
                        ),
                        'orderby'    => 'name',
                        'order'      => 'ASC',
                    ) );
                }

                if ( $search_query ) {
                    $models = array_filter( $models, function( $model ) use ( $search_query ) {
                        return stripos( $model->name, $search_query ) !== false;
                    } );
                }

                if ( ! empty( $models ) && ! is_wp_error( $models ) ) :
                    ?>
                    <div class="models-grid">
                        <?php foreach ( $models as $model ) :
                            $photo_count = $model->count;
                            // Récupérer une photo d'exemple
                            $sample_photo = get_posts( array(
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
                            $thumbnail = $sample_photo ? get_the_post_thumbnail_url( $sample_photo[0]->ID, 'medium' ) : '';
                            wp_reset_postdata();
                            ?>
                            <a href="<?php echo esc_url( add_query_arg( array( 'brand' => $current_brand->slug, 'model' => $model->slug ), get_permalink() ) ); ?>" class="model-card fade-in">
                                <div class="model-thumbnail">
                                    <?php if ( $thumbnail ) : ?>
                                        <img src="<?php echo esc_url( $thumbnail ); ?>" alt="<?php echo esc_attr( $model->name ); ?>" loading="lazy">
                                    <?php else : ?>
                                        <div class="model-placeholder">📷</div>
                                    <?php endif; ?>
                                </div>
                                <div class="model-info">
                                    <h3 class="model-name"><?php echo esc_html( $model->name ); ?></h3>
                                    <p class="model-count"><?php echo esc_html( $photo_count ); ?> photo<?php echo $photo_count > 1 ? 's' : ''; ?></p>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <div class="no-results">
                        <h3>Aucun modèle trouvé</h3>
                        <p>Cette marque n'a pas encore de modèles enregistrés.</p>
                    </div>
                <?php endif; ?>

            <?php elseif ( $current_view === 'photos' && $current_brand && $current_model ) : ?>
                <!-- ========== VUE: PHOTOS D'UN MODÈLE ========== -->

                <!-- En-tête du modèle -->
                <div class="model-header">
                    <div class="model-header-brand">
                        <?php
                        $logo = get_term_meta( $current_brand->term_id, '_szr_brand_logo_id', true );
                        $logo_url = $logo ? wp_get_attachment_image_url( $logo, 'medium' ) : '';
                        ?>
                        <?php if ( $logo_url ) : ?>
                            <img src="<?php echo esc_url( $logo_url ); ?>" alt="Logo <?php echo esc_attr( $current_brand->name ); ?>" class="model-header-logo" loading="lazy">
                        <?php endif; ?>
                        <div>
                            <h2><?php echo esc_html( $current_brand->name . ' ' . $current_model->name ); ?></h2>
                            <?php if ( $current_model->description ) : ?>
                                <p class="model-description"><?php echo esc_html( $current_model->description ); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ( is_user_logged_in() ) : ?>
                        <div class="model-actions">
                            <a href="<?php echo esc_url( home_url( '/soumettre-photo/?brand=' . $current_brand->term_id . '&model=' . $current_model->term_id ) ); ?>" class="cta-button">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19 7v2.99s-1.99.01-2 0V7h-3s.01-1.99 0-2h3V2h2v3h3v2h-3zm-3 4V8h-3V5H5c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2v-8h-3zM5 19l3-4 2 3 3-4 4 5H5z"/>
                                </svg>
                                Ajouter une photo
                            </a>
                        </div>
                    <?php else : ?>
                        <div class="model-actions">
                            <a href="<?php echo esc_url( wp_login_url( get_permalink() ) ); ?>" class="secondary-button">
                                Connectez-vous pour ajouter une photo
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <?php
                // Récupérer les photos de ce modèle
                $photos_query = new WP_Query( array(
                    'post_type'      => 'car_photo',
                    'posts_per_page' => 24,
                    'paged'          => get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1,
                    'tax_query'      => array(
                        'relation' => 'AND',
                        array(
                            'taxonomy' => 'car_brand',
                            'field'    => 'term_id',
                            'terms'    => $current_brand->term_id,
                        ),
                        array(
                            'taxonomy' => 'car_model',
                            'field'    => 'term_id',
                            'terms'    => $current_model->term_id,
                        ),
                    ),
                    'orderby'        => 'date',
                    'order'          => 'DESC',
                ) );

                if ( $photos_query->have_posts() ) :
                    ?>
                    <div class="photos-grid">
                        <?php
                        while ( $photos_query->have_posts() ) :
                            $photos_query->the_post();
                            get_template_part( 'template-parts/content', 'photo-card' );
                        endwhile;
                        ?>
                    </div>

                    <?php
                    // Pagination
                    if ( $photos_query->max_num_pages > 1 ) :
                        ?>
                        <div class="pagination">
                            <?php
                            echo paginate_links( array(
                                'total'     => $photos_query->max_num_pages,
                                'current'   => max( 1, get_query_var( 'paged' ) ),
                                'prev_text' => '← Précédent',
                                'next_text' => 'Suivant →',
                            ) );
                            ?>
                        </div>
                    <?php endif; ?>

                    <?php wp_reset_postdata(); ?>
                <?php else : ?>
                    <div class="no-results">
                        <div class="no-results-icon">📸</div>
                        <h3>Aucune photo pour ce modèle</h3>
                        <p>Soyez le premier à partager une photo de <?php echo esc_html( $current_brand->name . ' ' . $current_model->name ); ?> !</p>
                        <?php if ( is_user_logged_in() ) : ?>
                            <a href="<?php echo esc_url( home_url( '/soumettre-photo/?brand=' . $current_brand->term_id . '&model=' . $current_model->term_id ) ); ?>" class="cta-button">
                                Ajouter la première photo
                            </a>
                        <?php else : ?>
                            <a href="<?php echo esc_url( wp_login_url( get_permalink() ) ); ?>" class="cta-button">
                                Se connecter pour ajouter une photo
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            <?php endif; ?>

        </div>
    </div>
</div>

<?php
get_footer();
