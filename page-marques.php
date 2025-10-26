<?php
/**
 * Template Name: Marques & Modèles
 * Description: Page recensant toutes les marques et modèles avec logos, navigation fluide et SEO optimisé
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
                                        <img src="<?php echo esc_url( $logo_url ); ?>" alt="Logo <?php echo esc_attr( $brand->name ); ?>">
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
                            <img src="<?php echo esc_url( $logo_url ); ?>" alt="Logo <?php echo esc_attr( $current_brand->name ); ?>">
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
                                        <img src="<?php echo esc_url( $thumbnail ); ?>" alt="<?php echo esc_attr( $model->name ); ?>">
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
                            <img src="<?php echo esc_url( $logo_url ); ?>" alt="Logo <?php echo esc_attr( $current_brand->name ); ?>" class="model-header-logo">
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

<style>
/* Brands Page Styles */
.brands-page {
    background: var(--dark);
    min-height: calc(100vh - 85px);
}

/* Breadcrumb */
.brands-breadcrumb {
    background: var(--dark-gray);
    padding: 1rem 0;
    border-bottom: 1px solid rgba(255, 0, 85, 0.1);
}

.brands-breadcrumb nav {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
}

.brands-breadcrumb a {
    color: var(--text-muted);
    transition: color 0.3s ease;
}

.brands-breadcrumb a:hover {
    color: var(--primary);
}

.brands-breadcrumb .separator {
    color: var(--text-muted);
}

.brands-breadcrumb .current {
    color: var(--text);
    font-weight: 600;
}

/* Header */
.brands-header {
    background: linear-gradient(135deg, var(--dark-gray), var(--dark));
    padding: 4rem 0 3rem;
    text-align: center;
    border-bottom: 1px solid rgba(255, 0, 85, 0.2);
}

.page-title {
    font-size: 3rem;
    font-weight: 900;
    margin-bottom: 1rem;
    background: linear-gradient(135deg, var(--text), var(--secondary));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.page-description {
    color: var(--text-muted);
    font-size: 1.2rem;
    margin-bottom: 2rem;
}

.brands-search {
    max-width: 600px;
    margin: 0 auto;
}

.search-form {
    display: flex;
    gap: 0.5rem;
}

.search-input {
    flex: 1;
    padding: 1rem 1.5rem;
    background: var(--dark);
    border: 2px solid rgba(255, 0, 85, 0.2);
    border-radius: 50px;
    color: var(--text);
    font-size: 1rem;
    transition: all 0.3s ease;
}

.search-input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(255, 0, 85, 0.1);
}

.search-button {
    padding: 1rem 2rem;
    background: linear-gradient(135deg, var(--primary), #ff3377);
    border: none;
    border-radius: 50px;
    color: white;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
}

.search-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(255, 0, 85, 0.4);
}

/* Content */
.brands-content {
    padding: 4rem 0;
}

/* Brands Grid */
.brands-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 2rem;
}

.brand-card {
    background: var(--dark-gray);
    border-radius: 20px;
    padding: 2rem;
    border: 2px solid rgba(255, 0, 85, 0.1);
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.brand-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 0, 85, 0.1), transparent);
    transition: left 0.5s ease;
}

.brand-card:hover::before {
    left: 100%;
}

.brand-card:hover {
    transform: translateY(-10px);
    border-color: var(--primary);
    box-shadow: 0 20px 60px rgba(255, 0, 85, 0.2);
}

.brand-logo {
    width: 120px;
    height: 120px;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--light-gray);
    border-radius: 15px;
    padding: 1rem;
}

.brand-logo img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.brand-logo-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    font-weight: 900;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.brand-info {
    flex: 1;
}

.brand-name {
    font-size: 1.5rem;
    font-weight: 900;
    margin-bottom: 0.5rem;
    color: var(--text);
}

.brand-count {
    color: var(--text-muted);
    font-size: 0.9rem;
}

.brand-arrow {
    font-size: 1.5rem;
    color: var(--primary);
    margin-top: 1rem;
    transition: transform 0.3s ease;
}

.brand-card:hover .brand-arrow {
    transform: translateX(5px);
}

/* Brand Header */
.brand-header {
    text-align: center;
    margin-bottom: 3rem;
}

.brand-header-logo {
    max-width: 200px;
    height: auto;
    margin: 0 auto 2rem;
    display: block;
}

.brand-header h2 {
    font-size: 3rem;
    font-weight: 900;
    margin-bottom: 1rem;
}

.brand-description {
    color: var(--text-muted);
    font-size: 1.1rem;
    max-width: 800px;
    margin: 0 auto;
}

/* Models Grid */
.models-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
}

.model-card {
    background: var(--dark-gray);
    border-radius: 20px;
    overflow: hidden;
    border: 2px solid rgba(255, 0, 85, 0.1);
    transition: all 0.3s ease;
}

.model-card:hover {
    transform: translateY(-10px);
    border-color: var(--primary);
    box-shadow: 0 20px 60px rgba(255, 0, 85, 0.2);
}

.model-thumbnail {
    aspect-ratio: 16/9;
    overflow: hidden;
    background: var(--light-gray);
}

.model-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.model-card:hover .model-thumbnail img {
    transform: scale(1.1);
}

.model-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 4rem;
    color: var(--text-muted);
}

.model-info {
    padding: 1.5rem;
}

.model-name {
    font-size: 1.3rem;
    font-weight: 900;
    margin-bottom: 0.5rem;
    color: var(--text);
}

.model-count {
    color: var(--text-muted);
    font-size: 0.9rem;
}

/* Model Header */
.model-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 3rem;
    padding: 2rem;
    background: var(--dark-gray);
    border-radius: 20px;
    border: 1px solid rgba(255, 0, 85, 0.1);
}

.model-header-brand {
    display: flex;
    align-items: center;
    gap: 2rem;
}

.model-header-logo {
    max-width: 80px;
    height: auto;
}

.model-header h2 {
    font-size: 2rem;
    font-weight: 900;
    margin-bottom: 0.5rem;
}

.model-description {
    color: var(--text-muted);
}

.model-actions {
    flex-shrink: 0;
}

/* Photos Grid */
.photos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

/* No Results */
.no-results {
    text-align: center;
    padding: 4rem 2rem;
    background: var(--dark-gray);
    border-radius: 20px;
    border: 2px dashed rgba(255, 0, 85, 0.2);
}

.no-results-icon {
    font-size: 5rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.no-results h3 {
    font-size: 2rem;
    margin-bottom: 1rem;
}

.no-results p {
    color: var(--text-muted);
    font-size: 1.1rem;
    margin-bottom: 2rem;
}

.no-results a {
    color: var(--primary);
    text-decoration: underline;
}

/* Pagination */
.pagination {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
    margin-top: 3rem;
}

.pagination a,
.pagination span {
    padding: 0.8rem 1.2rem;
    background: var(--dark-gray);
    border: 1px solid rgba(255, 0, 85, 0.1);
    border-radius: 10px;
    color: var(--text);
    text-decoration: none;
    transition: all 0.3s ease;
}

.pagination a:hover,
.pagination .current {
    background: var(--primary);
    border-color: var(--primary);
    transform: translateY(-2px);
}

/* Responsive */
@media (max-width: 968px) {
    .page-title {
        font-size: 2rem;
    }

    .brands-grid,
    .models-grid,
    .photos-grid {
        grid-template-columns: 1fr;
    }

    .model-header {
        flex-direction: column;
        gap: 2rem;
        text-align: center;
    }

    .model-header-brand {
        flex-direction: column;
        gap: 1rem;
    }

    .brand-header h2,
    .model-header h2 {
        font-size: 2rem;
    }
}
</style>

<?php
get_footer();
