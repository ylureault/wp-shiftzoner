<?php
/**
 * Template Name: Marques & Modèles
 * Description: Page mémorable avec AJAX, animations et expérience fluide
 * Version: 3.0.0 - Memorable Experience
 *
 * @package ShiftZoneR
 */

get_header();

// Récupérer toutes les marques pour la vue initiale
$brands = get_terms( array(
    'taxonomy'   => 'car_brand',
    'hide_empty' => false,
    'orderby'    => 'name',
    'order'      => 'ASC',
) );

$total_photos = wp_count_posts( 'car_photo' )->publish;
?>

<div class="brands-page" id="brands-app">
    <!-- Hero Header avec effet parallaxe -->
    <div class="brands-hero">
        <div class="hero-bg"></div>
        <div class="container">
            <h1 class="hero-title" data-animate="fade-up">
                Explorez les Marques Automobiles
            </h1>
            <p class="hero-subtitle" data-animate="fade-up" data-delay="100">
                <?php echo count( $brands ); ?> marques • <?php echo number_format( $total_photos ); ?> photos • La passion automobile
            </p>

            <!-- Recherche en temps réel -->
            <div class="hero-search" data-animate="fade-up" data-delay="200">
                <div class="search-box">
                    <svg class="search-icon" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                    </svg>
                    <input
                        type="text"
                        id="live-search"
                        placeholder="Rechercher une marque ou un modèle..."
                        autocomplete="off"
                    >
                    <div id="search-results" class="search-results"></div>
                </div>
            </div>

            <!-- Stats animées -->
            <div class="hero-stats" data-animate="fade-up" data-delay="300">
                <div class="stat-item">
                    <span class="stat-number" data-count="<?php echo count( $brands ); ?>">0</span>
                    <span class="stat-label">Marques</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" data-count="<?php echo wp_count_terms( 'car_model' ); ?>">0</span>
                    <span class="stat-label">Modèles</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" data-count="<?php echo $total_photos; ?>">0</span>
                    <span class="stat-label">Photos</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Breadcrumb -->
    <div class="brands-breadcrumb">
        <div class="container">
            <nav id="breadcrumb-nav" aria-label="Fil d'Ariane">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Accueil</a>
                <span class="separator">/</span>
                <span class="current">Marques</span>
            </nav>
        </div>
    </div>

    <!-- Contenu dynamique -->
    <div class="brands-content">
        <div class="container">
            <!-- Loading overlay -->
            <div id="loading-overlay" class="loading-overlay" style="display:none;">
                <div class="loading-spinner">
                    <div class="spinner-ring"></div>
                    <div class="spinner-ring"></div>
                    <div class="spinner-ring"></div>
                </div>
                <p>Chargement...</p>
            </div>

            <!-- Vue marques (par défaut) -->
            <div id="brands-view" class="view-container">
                <div class="view-header">
                    <h2 class="view-title">Toutes les marques</h2>
                    <div class="view-controls">
                        <button id="grid-view" class="view-btn active" title="Vue grille">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M4 11h5V5H4v6zm0 7h5v-6H4v6zm6 0h5v-6h-5v6zm6 0h5v-6h-5v6zm-6-7h5V5h-5v6zm6-6v6h5V5h-5z"/>
                            </svg>
                        </button>
                        <button id="list-view" class="view-btn" title="Vue liste">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M3 13h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm4 4h14v-2H7v2zm0 4h14v-2H7v2zM7 7v2h14V7H7z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div id="brands-grid" class="brands-grid">
                    <?php
                    $delay = 0;
                    foreach ( $brands as $brand ) :
                        $logo = get_term_meta( $brand->term_id, '_szr_brand_logo_id', true );
                        $logo_url = $logo ? wp_get_attachment_image_url( $logo, 'medium' ) : '';
                        ?>
                        <div class="brand-card" data-animate="fade-up" data-delay="<?php echo $delay; ?>" data-brand-id="<?php echo $brand->term_id; ?>">
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
                                <p class="brand-count"><?php echo $brand->count; ?> photo<?php echo $brand->count > 1 ? 's' : ''; ?></p>
                            </div>
                            <div class="brand-arrow">→</div>
                        </div>
                        <?php
                        $delay += 50;
                        if ( $delay > 500 ) $delay = 0;
                    endforeach;
                    ?>
                </div>
            </div>

            <!-- Vue modèles (chargée via AJAX) -->
            <div id="models-view" class="view-container" style="display:none;">
                <div class="back-button" id="back-to-brands">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
                    </svg>
                    Retour aux marques
                </div>
                <div id="models-content"></div>
            </div>

            <!-- Vue photos (chargée via AJAX) -->
            <div id="photos-view" class="view-container" style="display:none;">
                <div class="back-button" id="back-to-models">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
                    </svg>
                    Retour aux modèles
                </div>
                <div id="photos-content"></div>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    // Variables globales
    let currentBrand = null;
    let currentModel = null;
    let searchTimeout = null;
    let isGridView = true;

    // Animation au scroll
    function animateOnScroll() {
        const elements = document.querySelectorAll('[data-animate]:not(.animated)');
        elements.forEach(el => {
            const rect = el.getBoundingClientRect();
            if (rect.top < window.innerHeight - 100) {
                const delay = el.dataset.delay || 0;
                setTimeout(() => {
                    el.classList.add('animated');
                }, delay);
            }
        });
    }

    // Compteurs animés
    function animateCounters() {
        document.querySelectorAll('.stat-number[data-count]').forEach(counter => {
            const target = parseInt(counter.dataset.count);
            const duration = 2000;
            const step = target / (duration / 16);
            let current = 0;

            const timer = setInterval(() => {
                current += step;
                if (current >= target) {
                    counter.textContent = target.toLocaleString();
                    clearInterval(timer);
                } else {
                    counter.textContent = Math.floor(current).toLocaleString();
                }
            }, 16);
        });
    }

    // Recherche en temps réel
    document.getElementById('live-search').addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        const query = e.target.value.trim();

        if (query.length < 2) {
            document.getElementById('search-results').innerHTML = '';
            return;
        }

        searchTimeout = setTimeout(() => {
            fetch('<?php echo admin_url( 'admin-ajax.php' ); ?>', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `action=szr_search_brands&query=${encodeURIComponent(query)}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    displaySearchResults(data.data);
                }
            });
        }, 300);
    });

    function displaySearchResults(results) {
        const container = document.getElementById('search-results');
        if (!results || results.length === 0) {
            container.innerHTML = '<div class="no-results">Aucun résultat</div>';
            return;
        }

        container.innerHTML = results.map(item => `
            <div class="search-result-item" onclick="loadBrand(${item.id})">
                ${item.logo ? `<img src="${item.logo}" alt="${item.name}">` : `<div class="result-placeholder">${item.name[0]}</div>`}
                <div class="result-info">
                    <strong>${item.name}</strong>
                    <span>${item.count} photo${item.count > 1 ? 's' : ''}</span>
                </div>
            </div>
        `).join('');
    }

    // Charger une marque
    window.loadBrand = function(brandId, updateHistory = true) {
        showLoading();

        fetch('<?php echo admin_url( 'admin-ajax.php' ); ?>', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `action=szr_get_brand_models&brand_id=${brandId}`
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                currentBrand = data.data.brand;
                displayModels(data.data);
                updateBreadcrumb('models');
                hideLoading();

                // Mettre à jour l'URL
                if (updateHistory && currentBrand.slug) {
                    const url = new URL(window.location);
                    url.searchParams.set('brand', currentBrand.slug);
                    url.searchParams.delete('model');
                    window.history.pushState({view: 'models', brandId: brandId}, '', url);
                }
            } else {
                alert('Marque introuvable');
                hideLoading();
            }
        })
        .catch(err => {
            console.error(err);
            alert('Erreur lors du chargement de la marque');
            hideLoading();
        });

        // Fermer les résultats de recherche
        document.getElementById('search-results').innerHTML = '';
        document.getElementById('live-search').value = '';
    };

    // Afficher les modèles
    function displayModels(data) {
        const brand = data.brand;
        const models = data.models;

        document.getElementById('brands-view').style.display = 'none';
        document.getElementById('models-view').style.display = 'block';
        document.getElementById('photos-view').style.display = 'none';

        let html = `
            <div class="brand-header" data-animate="fade-up">
                ${brand.logo ? `<img src="${brand.logo}" alt="${brand.name}" class="brand-header-logo">` : ''}
                <h2>${brand.name}</h2>
                <p class="brand-description">${brand.description || ''}</p>
            </div>
            <div class="models-grid">
        `;

        models.forEach((model, index) => {
            html += `
                <div class="model-card" data-animate="fade-up" data-delay="${index * 50}" onclick="loadModel(${model.id}, '${model.name}')">
                    <div class="model-thumbnail">
                        ${model.thumbnail ? `<img src="${model.thumbnail}" alt="${model.name}" loading="lazy">` : '<div class="model-placeholder">📷</div>'}
                    </div>
                    <div class="model-info">
                        <h3 class="model-name">${model.name}</h3>
                        <p class="model-count">${model.count} photo${model.count > 1 ? 's' : ''}</p>
                    </div>
                </div>
            `;
        });

        html += '</div>';
        document.getElementById('models-content').innerHTML = html;

        setTimeout(() => animateOnScroll(), 100);
        window.scrollTo({top: 0, behavior: 'smooth'});
    }

    // Charger un modèle
    window.loadModel = function(modelId, modelName, updateHistory = true) {
        showLoading();
        currentModel = {id: modelId, name: modelName};

        fetch('<?php echo admin_url( 'admin-ajax.php' ); ?>', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `action=szr_get_model_photos&model_id=${modelId}&brand_id=${currentBrand.id}`
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                displayPhotos(data.data);
                updateBreadcrumb('photos');
                hideLoading();

                // Mettre à jour l'URL
                if (updateHistory && currentBrand.slug && data.data.model.slug) {
                    const url = new URL(window.location);
                    url.searchParams.set('brand', currentBrand.slug);
                    url.searchParams.set('model', data.data.model.slug);
                    window.history.pushState({view: 'photos', brandId: currentBrand.id, modelId: modelId}, '', url);
                }
            } else {
                alert('Modèle introuvable');
                hideLoading();
            }
        })
        .catch(err => {
            console.error(err);
            alert('Erreur lors du chargement du modèle');
            hideLoading();
        });
    };

    // Afficher les photos
    function displayPhotos(photos) {
        document.getElementById('models-view').style.display = 'none';
        document.getElementById('photos-view').style.display = 'block';

        let html = `
            <div class="model-header" data-animate="fade-up">
                ${currentBrand.logo ? `<img src="${currentBrand.logo}" alt="${currentBrand.name}" class="model-header-logo">` : ''}
                <div>
                    <h2>${currentBrand.name} ${currentModel.name}</h2>
                    <p class="model-description">${photos.length} photo${photos.length > 1 ? 's' : ''} disponible${photos.length > 1 ? 's' : ''}</p>
                </div>
            </div>
            <div class="photos-grid">
        `;

        photos.forEach((photo, index) => {
            html += `
                <div class="photo-card" data-animate="fade-up" data-delay="${index * 30}">
                    <div class="photo-card-image">
                        <img src="${photo.thumbnail}" alt="${photo.title}" loading="lazy">
                    </div>
                    <div class="photo-card-content">
                        <h3 class="photo-card-title"><a href="${photo.url}">${photo.title}</a></h3>
                        <div class="photo-card-meta">
                            <span>Par ${photo.author}</span>
                            <span>${photo.date}</span>
                        </div>
                    </div>
                </div>
            `;
        });

        html += '</div>';
        document.getElementById('photos-content').innerHTML = html;

        setTimeout(() => animateOnScroll(), 100);
        window.scrollTo({top: 0, behavior: 'smooth'});
    }

    // Navigation
    document.getElementById('back-to-brands').addEventListener('click', () => {
        document.getElementById('brands-view').style.display = 'block';
        document.getElementById('models-view').style.display = 'none';
        currentBrand = null;
        currentModel = null;
        updateBreadcrumb('brands');
        window.scrollTo({top: 0, behavior: 'smooth'});

        // Mettre à jour l'URL
        const url = new URL(window.location);
        url.searchParams.delete('brand');
        url.searchParams.delete('model');
        window.history.pushState({view: 'brands'}, '', url);
    });

    document.getElementById('back-to-models').addEventListener('click', () => {
        document.getElementById('models-view').style.display = 'block';
        document.getElementById('photos-view').style.display = 'none';
        currentModel = null;
        updateBreadcrumb('models');
        window.scrollTo({top: 0, behavior: 'smooth'});

        // Mettre à jour l'URL
        if (currentBrand && currentBrand.slug) {
            const url = new URL(window.location);
            url.searchParams.set('brand', currentBrand.slug);
            url.searchParams.delete('model');
            window.history.pushState({view: 'models', brandId: currentBrand.id}, '', url);
        }
    });

    // Breadcrumb
    function updateBreadcrumb(view) {
        const nav = document.getElementById('breadcrumb-nav');
        let html = '<a href="<?php echo home_url(); ?>">Accueil</a><span class="separator">/</span>';

        if (view === 'brands') {
            html += '<span class="current">Marques</span>';
        } else if (view === 'models' && currentBrand) {
            html += '<a href="#" onclick="document.getElementById(\'back-to-brands\').click(); return false;">Marques</a>';
            html += '<span class="separator">/</span>';
            html += `<span class="current">${currentBrand.name}</span>`;
        } else if (view === 'photos' && currentBrand && currentModel) {
            html += '<a href="#" onclick="document.getElementById(\'back-to-brands\').click(); return false;">Marques</a>';
            html += '<span class="separator">/</span>';
            html += `<a href="#" onclick="document.getElementById('back-to-models').click(); return false;">${currentBrand.name}</a>`;
            html += '<span class="separator">/</span>';
            html += `<span class="current">${currentModel.name}</span>`;
        }

        nav.innerHTML = html;
    }

    // Toggle vue grille/liste
    document.getElementById('grid-view').addEventListener('click', () => {
        isGridView = true;
        document.getElementById('grid-view').classList.add('active');
        document.getElementById('list-view').classList.remove('active');
        document.getElementById('brands-grid').classList.remove('list-layout');
    });

    document.getElementById('list-view').addEventListener('click', () => {
        isGridView = false;
        document.getElementById('list-view').classList.add('active');
        document.getElementById('grid-view').classList.remove('active');
        document.getElementById('brands-grid').classList.add('list-layout');
    });

    // Loading
    function showLoading() {
        document.getElementById('loading-overlay').style.display = 'flex';
    }

    function hideLoading() {
        document.getElementById('loading-overlay').style.display = 'none';
    }

    // Event listeners cards
    document.querySelectorAll('.brand-card').forEach(card => {
        card.addEventListener('click', function() {
            const brandId = this.dataset.brandId;
            loadBrand(brandId);
        });
    });

    // Gestion de l'historique (boutons précédent/suivant du navigateur)
    window.addEventListener('popstate', (e) => {
        if (e.state) {
            if (e.state.view === 'brands') {
                document.getElementById('brands-view').style.display = 'block';
                document.getElementById('models-view').style.display = 'none';
                document.getElementById('photos-view').style.display = 'none';
                currentBrand = null;
                currentModel = null;
                updateBreadcrumb('brands');
            } else if (e.state.view === 'models' && e.state.brandId) {
                loadBrand(e.state.brandId, false);
            } else if (e.state.view === 'photos' && e.state.brandId && e.state.modelId) {
                loadBrand(e.state.brandId, false);
                setTimeout(() => {
                    if (currentBrand) {
                        loadModel(e.state.modelId, '', false);
                    }
                }, 500);
            }
        }
    });

    // Charger depuis l'URL au démarrage
    function loadFromURL() {
        const params = new URLSearchParams(window.location.search);
        const brandSlug = params.get('brand');
        const modelSlug = params.get('model');

        if (brandSlug) {
            // Trouver l'ID de la marque depuis le slug
            const brandCards = document.querySelectorAll('.brand-card');
            let brandId = null;

            brandCards.forEach(card => {
                const brandName = card.querySelector('.brand-name').textContent.trim();
                if (brandName.toLowerCase().replace(/\s+/g, '-') === brandSlug.toLowerCase()) {
                    brandId = card.dataset.brandId;
                }
            });

            if (brandId) {
                setTimeout(() => {
                    loadBrand(brandId, false);

                    // Si on a aussi un modèle, le charger après la marque
                    if (modelSlug) {
                        setTimeout(() => {
                            // Chercher le modèle dans les données chargées
                            const modelCards = document.querySelectorAll('.model-card');
                            modelCards.forEach(card => {
                                const modelNameEl = card.querySelector('.model-name');
                                if (modelNameEl) {
                                    const modelName = modelNameEl.textContent.trim();
                                    if (modelName.toLowerCase().replace(/\s+/g, '-') === modelSlug.toLowerCase()) {
                                        card.click();
                                    }
                                }
                            });
                        }, 1000);
                    }
                }, 500);
            }
        }
    }

    // Init
    window.addEventListener('scroll', animateOnScroll);
    animateOnScroll();
    setTimeout(() => animateCounters(), 500);

    // Charger depuis l'URL après un court délai
    setTimeout(() => loadFromURL(), 300);

    // Fermer les résultats quand on clique ailleurs
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.hero-search')) {
            document.getElementById('search-results').innerHTML = '';
        }
    });
})();
</script>

<?php
get_footer();
