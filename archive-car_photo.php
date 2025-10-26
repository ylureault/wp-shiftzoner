<?php
/**
 * Archive Car Photo Template - Galerie
 *
 * @package ShiftZoneR
 */

get_header();

// Récupérer les termes pour les filtres
$brands = get_terms( array(
    'taxonomy'   => 'car_brand',
    'hide_empty' => true,
    'orderby'    => 'name',
    'order'      => 'ASC',
) );

$years = get_terms( array(
    'taxonomy'   => 'car_year',
    'hide_empty' => true,
    'orderby'    => 'name',
    'order'      => 'DESC',
) );
?>

<div class="gallery-archive">
    <!-- Header de la galerie -->
    <div class="gallery-header">
        <div class="container">
            <h1 class="gallery-title">Galerie Photos</h1>
            <p class="gallery-subtitle">Explorez <?php echo wp_count_posts( 'car_photo' )->publish; ?> photos automobiles</p>
        </div>
    </div>

    <!-- Filtres -->
    <div class="gallery-filters">
        <div class="container">
            <div class="filters-grid">
                <!-- Recherche -->
                <div class="filter-group">
                    <input
                        type="text"
                        id="search-input"
                        class="filter-input"
                        placeholder="🔍 Rechercher..."
                    >
                </div>

                <!-- Marque -->
                <div class="filter-group">
                    <select id="brand-filter" class="filter-select">
                        <option value="">Toutes les marques</option>
                        <?php foreach ( $brands as $brand ) : ?>
                            <option value="<?php echo esc_attr( $brand->term_id ); ?>">
                                <?php echo esc_html( $brand->name ); ?> (<?php echo $brand->count; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Modèle -->
                <div class="filter-group">
                    <select id="model-filter" class="filter-select" disabled>
                        <option value="">Sélectionnez d'abord une marque</option>
                    </select>
                </div>

                <!-- Année -->
                <div class="filter-group">
                    <select id="year-filter" class="filter-select">
                        <option value="">Toutes les années</option>
                        <?php foreach ( $years as $year ) : ?>
                            <option value="<?php echo esc_attr( $year->term_id ); ?>">
                                <?php echo esc_html( $year->name ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Tri -->
                <div class="filter-group">
                    <select id="sort-filter" class="filter-select">
                        <option value="date">Plus récentes</option>
                        <option value="votes">Plus votées</option>
                        <option value="comments">Plus commentées</option>
                        <option value="views">Plus vues</option>
                    </select>
                </div>

                <!-- Bouton reset -->
                <div class="filter-group">
                    <button id="reset-filters" class="filter-reset">
                        ✕ Réinitialiser
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Grille de photos -->
    <div class="gallery-main">
        <div class="container">
            <div id="photos-container" class="photos-masonry">
                <?php
                if ( have_posts() ) :
                    while ( have_posts() ) :
                        the_post();
                        get_template_part( 'template-parts/content', 'photo-card' );
                    endwhile;
                else :
                    ?>
                    <div class="no-results">
                        <h3>Aucune photo trouvée</h3>
                        <p>Essayez de modifier vos filtres ou de rechercher autre chose.</p>
                    </div>
                    <?php
                endif;
                ?>
            </div>

            <!-- Loading spinner -->
            <div id="loading-spinner" class="loading-spinner" style="display: none;">
                <div class="spinner"></div>
                <p>Chargement...</p>
            </div>

            <!-- Bouton Load More -->
            <?php if ( $wp_query->max_num_pages > 1 ) : ?>
                <div class="load-more-wrapper">
                    <button id="load-more" class="load-more-btn" data-page="1" data-max="<?php echo $wp_query->max_num_pages; ?>">
                        Charger plus de photos
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
/* Gallery Archive Styles */
.gallery-archive {
    background: var(--dark);
    min-height: calc(100vh - 85px);
}

.gallery-header {
    background: linear-gradient(135deg, var(--dark-gray), var(--dark));
    padding: 4rem 0 3rem;
    text-align: center;
    border-bottom: 1px solid rgba(255, 0, 85, 0.2);
}

.gallery-title {
    font-size: 3.5rem;
    font-weight: 900;
    margin-bottom: 1rem;
    background: linear-gradient(135deg, var(--text), var(--secondary));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.gallery-subtitle {
    color: var(--text-muted);
    font-size: 1.2rem;
}

.gallery-filters {
    background: var(--dark-gray);
    padding: 2rem 0;
    position: sticky;
    top: 85px;
    z-index: 100;
    border-bottom: 1px solid rgba(255, 0, 85, 0.1);
}

.filters-grid {
    display: grid;
    grid-template-columns: 2fr repeat(4, 1fr) auto;
    gap: 1rem;
    align-items: center;
}

.filter-input,
.filter-select {
    width: 100%;
    padding: 0.8rem 1rem;
    background: var(--dark);
    border: 1px solid rgba(255, 0, 85, 0.2);
    border-radius: 10px;
    color: var(--text);
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

.filter-input:focus,
.filter-select:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(255, 0, 85, 0.1);
}

.filter-select option {
    background: var(--dark);
    color: var(--text);
}

.filter-reset {
    padding: 0.8rem 1.5rem;
    background: transparent;
    border: 2px solid rgba(255, 0, 85, 0.3);
    border-radius: 10px;
    color: var(--text-muted);
    cursor: pointer;
    transition: all 0.3s ease;
    white-space: nowrap;
}

.filter-reset:hover {
    border-color: var(--primary);
    color: var(--primary);
    background: rgba(255, 0, 85, 0.1);
}

.gallery-main {
    padding: 3rem 0;
}

.photos-masonry {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.photo-card {
    background: var(--dark-gray);
    border-radius: 20px;
    overflow: hidden;
    border: 1px solid rgba(255, 0, 85, 0.1);
    transition: all 0.3s ease;
    position: relative;
}

.photo-card:hover {
    transform: translateY(-10px);
    border-color: var(--primary);
    box-shadow: 0 20px 60px rgba(255, 0, 85, 0.2);
}

.photo-card-image {
    position: relative;
    aspect-ratio: 4/3;
    overflow: hidden;
}

.photo-card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.photo-card:hover .photo-card-image img {
    transform: scale(1.1);
}

.photo-card-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(to top, rgba(0, 0, 0, 0.9), transparent);
    opacity: 0;
    transition: opacity 0.3s ease;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    padding: 1.5rem;
}

.photo-card:hover .photo-card-overlay {
    opacity: 1;
}

.photo-card-content {
    padding: 1.5rem;
}

.photo-card-title {
    font-size: 1.3rem;
    margin-bottom: 0.5rem;
}

.photo-card-title a {
    color: var(--text);
}

.photo-card-title a:hover {
    color: var(--primary);
}

.photo-card-meta {
    display: flex;
    align-items: center;
    gap: 1rem;
    color: var(--text-muted);
    font-size: 0.9rem;
    margin-bottom: 1rem;
}

.photo-card-author {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.author-badge {
    width: 10px;
    height: 10px;
    border-radius: 50%;
}

.photo-card-stats {
    display: flex;
    gap: 1.5rem;
    padding-top: 1rem;
    border-top: 1px solid rgba(255, 255, 255, 0.05);
}

.stat {
    display: flex;
    align-items: center;
    gap: 0.3rem;
    color: var(--text-muted);
    font-size: 0.9rem;
}

.loading-spinner {
    text-align: center;
    padding: 3rem;
}

.spinner {
    width: 50px;
    height: 50px;
    border: 4px solid rgba(255, 0, 85, 0.1);
    border-top-color: var(--primary);
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 1rem;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.load-more-wrapper {
    text-align: center;
    padding: 2rem 0;
}

.load-more-btn {
    background: linear-gradient(135deg, var(--primary), #ff3377);
    color: white;
    padding: 1rem 3rem;
    border: none;
    border-radius: 50px;
    font-weight: 700;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.load-more-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(255, 0, 85, 0.4);
}

.load-more-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.no-results {
    grid-column: 1 / -1;
    text-align: center;
    padding: 4rem 2rem;
}

.no-results h3 {
    font-size: 2rem;
    margin-bottom: 1rem;
}

.no-results p {
    color: var(--text-muted);
}

/* Responsive */
@media (max-width: 1200px) {
    .filters-grid {
        grid-template-columns: repeat(3, 1fr);
    }

    .filter-group:first-child {
        grid-column: 1 / -1;
    }
}

@media (max-width: 768px) {
    .gallery-title {
        font-size: 2.5rem;
    }

    .filters-grid {
        grid-template-columns: 1fr;
    }

    .filter-group:first-child {
        grid-column: auto;
    }

    .photos-masonry {
        grid-template-columns: 1fr;
    }

    .gallery-filters {
        position: static;
    }
}
</style>

<script>
(function() {
    const searchInput = document.getElementById('search-input');
    const brandFilter = document.getElementById('brand-filter');
    const modelFilter = document.getElementById('model-filter');
    const yearFilter = document.getElementById('year-filter');
    const sortFilter = document.getElementById('sort-filter');
    const resetBtn = document.getElementById('reset-filters');
    const container = document.getElementById('photos-container');
    const loadMoreBtn = document.getElementById('load-more');
    const spinner = document.getElementById('loading-spinner');

    let debounceTimer;

    // Fonction de filtrage
    function filterPhotos() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            loadPhotos(1);
        }, 300);
    }

    // Chargement des photos via AJAX
    function loadPhotos(page = 1, append = false) {
        spinner.style.display = 'block';
        if (loadMoreBtn) loadMoreBtn.disabled = true;

        const params = new URLSearchParams({
            action: 'szr_filter_photos',
            search: searchInput.value,
            brand: brandFilter.value,
            model: modelFilter.value,
            year: yearFilter.value,
            sort: sortFilter.value,
            page: page
        });

        fetch('<?php echo admin_url( 'admin-ajax.php' ); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: params
        })
        .then(response => response.json())
        .then(data => {
            spinner.style.display = 'none';

            if (data.success) {
                if (append) {
                    container.insertAdjacentHTML('beforeend', data.data.html);
                } else {
                    container.innerHTML = data.data.html;
                }

                if (loadMoreBtn) {
                    if (data.data.has_more) {
                        loadMoreBtn.style.display = 'block';
                        loadMoreBtn.dataset.page = page;
                        loadMoreBtn.disabled = false;
                    } else {
                        loadMoreBtn.style.display = 'none';
                    }
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            spinner.style.display = 'none';
        });
    }

    // Charger les modèles selon la marque
    function loadModels() {
        const brandId = brandFilter.value;

        if (!brandId) {
            modelFilter.innerHTML = '<option value="">Sélectionnez d\'abord une marque</option>';
            modelFilter.disabled = true;
            return;
        }

        fetch('<?php echo admin_url( 'admin-ajax.php' ); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=szr_get_models&brand_id=${brandId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                modelFilter.innerHTML = '<option value="">Tous les modèles</option>';
                data.data.models.forEach(model => {
                    const option = document.createElement('option');
                    option.value = model.id;
                    option.textContent = model.name;
                    modelFilter.appendChild(option);
                });
                modelFilter.disabled = false;
            }
        });
    }

    // Event listeners
    searchInput.addEventListener('input', filterPhotos);
    brandFilter.addEventListener('change', () => {
        loadModels();
        filterPhotos();
    });
    modelFilter.addEventListener('change', filterPhotos);
    yearFilter.addEventListener('change', filterPhotos);
    sortFilter.addEventListener('change', filterPhotos);

    resetBtn.addEventListener('click', () => {
        searchInput.value = '';
        brandFilter.value = '';
        modelFilter.value = '';
        modelFilter.disabled = true;
        modelFilter.innerHTML = '<option value="">Sélectionnez d\'abord une marque</option>';
        yearFilter.value = '';
        sortFilter.value = 'date';
        filterPhotos();
    });

    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', () => {
            const currentPage = parseInt(loadMoreBtn.dataset.page);
            const nextPage = currentPage + 1;
            loadPhotos(nextPage, true);
        });
    }
})();
</script>

<?php
get_footer();
