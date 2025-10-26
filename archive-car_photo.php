<?php
/**
 * Archive Car Photo Template - Galerie
 * Version: 2.0.0 - Refactored
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
