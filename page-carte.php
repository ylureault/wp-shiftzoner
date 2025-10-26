<?php
/**
 * Template Name: Carte Interactive
 * Description: Carte interactive affichant les lieux de prise de vue
 * Version: 2.0.0 - Refactored
 *
 * @package ShiftZoneR
 */

get_header();
?>

<div class="map-page">
    <div class="map-header">
        <div class="container">
            <h1 class="map-title">Carte Interactive</h1>
            <p class="map-subtitle">Explorez les lieux de prise de vue des photos ShiftZoneR</p>
        </div>
    </div>

    <div class="map-container-wrapper">
        <div class="map-sidebar">
            <div class="sidebar-header">
                <h3>Filtres</h3>
                <button id="close-sidebar" class="close-btn" aria-label="Fermer">✕</button>
            </div>

            <div class="sidebar-content">
                <!-- Filtres -->
                <div class="filter-section">
                    <label for="map-brand-filter">Marque</label>
                    <select id="map-brand-filter" class="filter-select">
                        <option value="">Toutes les marques</option>
                        <?php
                        $brands = get_terms( array(
                            'taxonomy'   => 'car_brand',
                            'hide_empty' => true,
                        ) );
                        foreach ( $brands as $brand ) :
                            ?>
                            <option value="<?php echo esc_attr( $brand->term_id ); ?>">
                                <?php echo esc_html( $brand->name ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-section">
                    <label for="map-model-filter">Modèle</label>
                    <select id="map-model-filter" class="filter-select" disabled>
                        <option value="">Sélectionnez une marque</option>
                    </select>
                </div>

                <div class="filter-section">
                    <label for="map-author-filter">Contributeur</label>
                    <select id="map-author-filter" class="filter-select">
                        <option value="">Tous les contributeurs</option>
                        <option value="1">Rafael</option>
                        <?php
                        $authors = get_users( array(
                            'who'     => 'authors',
                            'orderby' => 'display_name',
                        ) );
                        foreach ( $authors as $author ) :
                            if ( $author->ID == 1 ) {
                                continue;
                            }
                            ?>
                            <option value="<?php echo esc_attr( $author->ID ); ?>">
                                <?php echo esc_html( $author->display_name ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button id="reset-map-filters" class="filter-reset-btn">
                    Réinitialiser les filtres
                </button>
            </div>

            <!-- Liste des photos -->
            <div class="sidebar-photos" id="sidebar-photos">
                <h4>Photos à proximité</h4>
                <div id="nearby-photos" class="nearby-list">
                    <p class="no-selection">Cliquez sur un marqueur pour voir les détails</p>
                </div>
            </div>
        </div>

        <div class="map-main">
            <div id="shiftzoner-map" class="interactive-map"></div>

            <div class="map-controls">
                <button id="toggle-sidebar" class="control-btn" aria-label="Afficher/Masquer les filtres">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/>
                    </svg>
                </button>

                <button id="locate-me" class="control-btn" aria-label="Me localiser">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm8.94 3A8.994 8.994 0 0013 3.06V1h-2v2.06A8.994 8.994 0 003.06 11H1v2h2.06A8.994 8.994 0 0011 20.94V23h2v-2.06A8.994 8.994 0 0020.94 13H23v-2h-2.06zM12 19c-3.87 0-7-3.13-7-7s3.13-7 7-7 7 3.13 7 7-3.13 7-7 7z"/>
                    </svg>
                </button>

                <button id="fullscreen-map" class="control-btn" aria-label="Plein écran">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z"/>
                    </svg>
                </button>
            </div>

            <div class="map-stats">
                <span id="photos-count">0 photos</span>
                <span id="locations-count">sur la carte</span>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    // Variables
    let map;
    let markers = [];
    let photosData = [];

    // Initialiser la carte
    function initMap() {
        // Centre de la France par défaut
        const defaultCenter = [46.603354, 1.888334];

        map = L.map('shiftzoner-map').setView(defaultCenter, 6);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        // Charger les photos avec GPS
        loadMapPhotos();
    }

    // Charger les photos
    function loadMapPhotos() {
        const brandId = document.getElementById('map-brand-filter').value;
        const modelId = document.getElementById('map-model-filter').value;
        const authorId = document.getElementById('map-author-filter').value;

        fetch('<?php echo admin_url( 'admin-ajax.php' ); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=szr_map_photos&brand=${brandId}&model=${modelId}&author=${authorId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                photosData = data.data.photos;
                updateMarkers();
                updateStats();
            }
        });
    }

    // Mettre à jour les marqueurs
    function updateMarkers() {
        // Supprimer les anciens marqueurs
        markers.forEach(marker => map.removeLayer(marker));
        markers = [];

        // Ajouter les nouveaux
        photosData.forEach(photo => {
            const marker = L.marker([photo.lat, photo.lng], {
                icon: L.divIcon({
                    className: 'custom-marker',
                    html: `<div class="marker-pin" style="background: ${photo.user_color}"></div>`,
                    iconSize: [30, 40],
                    iconAnchor: [15, 40]
                })
            }).addTo(map);

            marker.on('click', () => showPhotoDetails(photo));
            markers.push(marker);
        });

        // Ajuster la vue
        if (markers.length > 0) {
            const group = L.featureGroup(markers);
            map.fitBounds(group.getBounds().pad(0.1));
        }
    }

    // Afficher les détails d'une photo
    function showPhotoDetails(photo) {
        const container = document.getElementById('nearby-photos');
        container.innerHTML = `
            <div class="nearby-item" onclick="window.location.href='${photo.url}'">
                <img src="${photo.thumbnail}" alt="${photo.title}" loading="lazy">
                <div class="nearby-item-title">${photo.title}</div>
                <div class="nearby-item-meta">Par ${photo.author} • ${photo.date}</div>
            </div>
        `;
    }

    // Mettre à jour les stats
    function updateStats() {
        document.getElementById('photos-count').textContent = `${photosData.length} photo${photosData.length > 1 ? 's' : ''}`;
    }

    // Charger les modèles par marque
    function loadModels() {
        const brandId = document.getElementById('map-brand-filter').value;
        const modelSelect = document.getElementById('map-model-filter');

        if (!brandId) {
            modelSelect.innerHTML = '<option value="">Sélectionnez une marque</option>';
            modelSelect.disabled = true;
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
                modelSelect.innerHTML = '<option value="">Tous les modèles</option>';
                data.data.models.forEach(model => {
                    const option = document.createElement('option');
                    option.value = model.id;
                    option.textContent = model.name;
                    modelSelect.appendChild(option);
                });
                modelSelect.disabled = false;
            }
        });
    }

    // Event listeners
    document.getElementById('map-brand-filter').addEventListener('change', () => {
        loadModels();
        loadMapPhotos();
    });
    document.getElementById('map-model-filter').addEventListener('change', loadMapPhotos);
    document.getElementById('map-author-filter').addEventListener('change', loadMapPhotos);

    document.getElementById('reset-map-filters').addEventListener('click', () => {
        document.getElementById('map-brand-filter').value = '';
        document.getElementById('map-model-filter').value = '';
        document.getElementById('map-model-filter').disabled = true;
        document.getElementById('map-author-filter').value = '';
        loadMapPhotos();
    });

    // Toggle sidebar
    document.getElementById('toggle-sidebar').addEventListener('click', () => {
        document.querySelector('.map-sidebar').classList.toggle('hidden');
    });

    document.getElementById('close-sidebar').addEventListener('click', () => {
        document.querySelector('.map-sidebar').classList.add('hidden');
    });

    // Locate me
    document.getElementById('locate-me').addEventListener('click', () => {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(position => {
                map.setView([position.coords.latitude, position.coords.longitude], 13);
            });
        }
    });

    // Fullscreen
    document.getElementById('fullscreen-map').addEventListener('click', () => {
        const elem = document.querySelector('.map-container-wrapper');
        if (!document.fullscreenElement) {
            elem.requestFullscreen();
        } else {
            document.exitFullscreen();
        }
    });

    // Init quand Leaflet est chargé
    if (typeof L !== 'undefined') {
        initMap();
    } else {
        console.error('Leaflet not loaded');
    }
})();
</script>

<?php
get_footer();
