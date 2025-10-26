<?php
/**
 * Template Name: Carte Interactive
 * Description: Carte interactive affichant les lieux de prise de vue avec OpenStreetMap
 * Version: 2.1.0 - Functional Map
 *
 * @package ShiftZoneR
 */

get_header();
?>

<div class="map-page">
    <div class="map-header">
        <div class="container">
            <div class="map-header-content">
                <div>
                    <h1 class="map-title">Carte Interactive</h1>
                    <p class="map-subtitle">Explorez les lieux de prise de vue des photos ShiftZoneR</p>
                </div>
                <?php if ( is_user_logged_in() ) : ?>
                <a href="<?php echo esc_url( home_url( '/soumettre-photo/' ) ); ?>" class="map-upload-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                    </svg>
                    Publier une photo
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="map-container-wrapper">
        <div class="map-sidebar" id="map-sidebar">
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
                        if ( ! is_wp_error( $brands ) ) :
                            foreach ( $brands as $brand ) :
                                ?>
                                <option value="<?php echo esc_attr( $brand->term_id ); ?>">
                                    <?php echo esc_html( $brand->name ); ?>
                                </option>
                            <?php endforeach;
                        endif;
                        ?>
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
                        <?php
                        $authors = get_users( array(
                            'who'     => 'authors',
                            'orderby' => 'display_name',
                        ) );
                        foreach ( $authors as $author ) :
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
                <button id="toggle-sidebar" class="control-btn" aria-label="Afficher/Masquer les filtres" title="Afficher/Masquer les filtres">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/>
                    </svg>
                </button>

                <button id="locate-me" class="control-btn" aria-label="Me localiser" title="Me localiser">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm8.94 3A8.994 8.994 0 0013 3.06V1h-2v2.06A8.994 8.994 0 003.06 11H1v2h2.06A8.994 8.994 0 0011 20.94V23h2v-2.06A8.994 8.994 0 0020.94 13H23v-2h-2.06zM12 19c-3.87 0-7-3.13-7-7s3.13-7 7-7 7 3.13 7 7-3.13 7-7 7z"/>
                    </svg>
                </button>

                <button id="fullscreen-map" class="control-btn" aria-label="Plein écran" title="Plein écran">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z"/>
                    </svg>
                </button>
            </div>

            <div class="map-stats">
                <span id="photos-count">Chargement...</span>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    console.log('=== Initialisation page carte ===');

    // Vérifier que Leaflet est chargé
    if (typeof L === 'undefined') {
        console.error('ERREUR: Leaflet n\'est pas chargé - vérifier functions.php');
        const mapEl = document.getElementById('shiftzoner-map');
        if (mapEl) {
            mapEl.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100%;flex-direction:column;padding:2rem;text-align:center;"><svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-bottom:1rem;opacity:0.5;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg><h3 style="color:var(--text);margin-bottom:0.5rem;">Erreur de chargement</h3><p style="color:var(--text-muted);">La bibliothèque Leaflet n\'est pas disponible</p></div>';
        }
        document.getElementById('photos-count').textContent = 'Erreur: Leaflet non disponible';
        return;
    }

    console.log('✓ Leaflet chargé, version:', L.version);

    // Variables
    let map = null;
    let markers = [];
    let photosData = [];

    // Initialiser la carte
    function initMap() {
        console.log('Initialisation de la carte...');

        // Centre de la France par défaut
        const defaultCenter = [46.603354, 1.888334];
        const defaultZoom = 6;

        try {
            const mapElement = document.getElementById('shiftzoner-map');
            if (!mapElement) {
                throw new Error('Élément #shiftzoner-map introuvable');
            }

            console.log('Création de la carte Leaflet...');
            map = L.map('shiftzoner-map', {
                center: defaultCenter,
                zoom: defaultZoom,
                zoomControl: false
            });

            console.log('✓ Carte créée');

            // Ajouter les tuiles OpenStreetMap
            console.log('Chargement des tuiles OpenStreetMap...');
            const tileLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19,
                minZoom: 3
            });

            tileLayer.on('tileerror', (error) => {
                console.warn('Erreur chargement tuile:', error);
            });

            tileLayer.addTo(map);
            console.log('✓ Tuiles ajoutées');

            // Ajouter le contrôle de zoom
            L.control.zoom({
                position: 'topright'
            }).addTo(map);

            console.log('✓ Carte initialisée avec succès');

            // Charger les photos avec GPS
            loadMapPhotos();
        } catch (error) {
            console.error('ERREUR lors de l\'initialisation de la carte:', error);
            document.getElementById('photos-count').textContent = 'Erreur d\'initialisation';
            const mapEl = document.getElementById('shiftzoner-map');
            if (mapEl) {
                mapEl.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100%;padding:2rem;text-align:center;"><div><h3 style="color:var(--text);">Erreur d\'initialisation</h3><p style="color:var(--text-muted);">' + error.message + '</p></div></div>';
            }
        }
    }

    // Charger les photos
    function loadMapPhotos() {
        if (!map) {
            console.error('Carte non initialisée');
            return;
        }

        const brandId = document.getElementById('map-brand-filter').value;
        const modelId = document.getElementById('map-model-filter').value;
        const authorId = document.getElementById('map-author-filter').value;

        console.log('Chargement photos - Filtres:', {brand: brandId, model: modelId, author: authorId});
        document.getElementById('photos-count').textContent = 'Chargement...';

        fetch('<?php echo admin_url( 'admin-ajax.php' ); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=szr_map_photos&brand=${brandId}&model=${modelId}&author=${authorId}`
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Réponse AJAX:', data);
            if (data.success && data.data && data.data.photos) {
                photosData = data.data.photos;
                console.log(`✓ ${photosData.length} photo(s) chargée(s)`);
                updateMarkers();
                updateStats();
            } else {
                console.warn('Aucune photo trouvée ou erreur:', data);
                photosData = [];
                updateMarkers();
                updateStats();
            }
        })
        .catch(error => {
            console.error('ERREUR lors du chargement des photos:', error);
            document.getElementById('photos-count').textContent = 'Erreur de chargement';
        });
    }

    // Mettre à jour les marqueurs
    function updateMarkers() {
        // Supprimer les anciens marqueurs
        markers.forEach(marker => map.removeLayer(marker));
        markers = [];

        if (!photosData || photosData.length === 0) {
            console.log('Aucune photo avec GPS');
            return;
        }

        console.log(`Affichage de ${photosData.length} photo(s)`);

        // Ajouter les nouveaux
        photosData.forEach(photo => {
            if (!photo.lat || !photo.lng) return;

            // Créer le marqueur avec icône personnalisée
            const markerIcon = L.divIcon({
                className: 'custom-marker',
                html: `<div class="marker-pin" style="background: ${photo.user_color || '#E50914'}"></div>`,
                iconSize: [30, 40],
                iconAnchor: [15, 40],
                popupAnchor: [0, -40]
            });

            const marker = L.marker([photo.lat, photo.lng], {
                icon: markerIcon
            }).addTo(map);

            // Ajouter le popup
            const popupContent = `
                <div class="map-popup">
                    ${photo.thumbnail ? `<img src="${photo.thumbnail}" alt="${photo.title}" style="width:100%;border-radius:8px;margin-bottom:8px;">` : ''}
                    <h4 style="margin:0 0 4px;font-size:14px;font-weight:700;">${photo.title}</h4>
                    <p style="margin:0;font-size:12px;color:#999;">Par ${photo.author} • ${photo.date}</p>
                    <a href="${photo.url}" style="display:inline-block;margin-top:8px;color:#E50914;font-weight:600;font-size:13px;">Voir la photo →</a>
                </div>
            `;

            marker.bindPopup(popupContent, {
                maxWidth: 250,
                className: 'custom-popup'
            });

            marker.on('click', () => showPhotoDetails(photo));
            markers.push(marker);
        });

        // Ajuster la vue pour montrer tous les marqueurs
        if (markers.length > 0) {
            const group = L.featureGroup(markers);
            map.fitBounds(group.getBounds().pad(0.1));
        }
    }

    // Afficher les détails d'une photo dans la sidebar
    function showPhotoDetails(photo) {
        const container = document.getElementById('nearby-photos');
        container.innerHTML = `
            <div class="nearby-item" onclick="window.location.href='${photo.url}'" style="cursor:pointer;">
                ${photo.thumbnail ? `<img src="${photo.thumbnail}" alt="${photo.title}" loading="lazy">` : ''}
                <div class="nearby-item-title">${photo.title}</div>
                <div class="nearby-item-meta">Par ${photo.author} • ${photo.date}</div>
            </div>
        `;
    }

    // Mettre à jour les stats
    function updateStats() {
        const count = photosData.length;
        const text = count === 0 ? 'Aucune photo' :
                     count === 1 ? '1 photo sur la carte' :
                     `${count} photos sur la carte`;
        document.getElementById('photos-count').textContent = text;
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
            if (data.success && data.data && data.data.models) {
                modelSelect.innerHTML = '<option value="">Tous les modèles</option>';
                data.data.models.forEach(model => {
                    const option = document.createElement('option');
                    option.value = model.id;
                    option.textContent = model.name;
                    modelSelect.appendChild(option);
                });
                modelSelect.disabled = false;
            }
        })
        .catch(error => console.error('Erreur chargement modèles:', error));
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
        document.getElementById('map-model-filter').innerHTML = '<option value="">Sélectionnez une marque</option>';
        document.getElementById('map-author-filter').value = '';
        loadMapPhotos();
    });

    // Toggle sidebar
    document.getElementById('toggle-sidebar').addEventListener('click', () => {
        document.getElementById('map-sidebar').classList.toggle('hidden');
        setTimeout(() => map.invalidateSize(), 300);
    });

    document.getElementById('close-sidebar').addEventListener('click', () => {
        document.getElementById('map-sidebar').classList.add('hidden');
        setTimeout(() => map.invalidateSize(), 300);
    });

    // Locate me
    document.getElementById('locate-me').addEventListener('click', () => {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                position => {
                    map.setView([position.coords.latitude, position.coords.longitude], 13);
                    L.marker([position.coords.latitude, position.coords.longitude])
                        .addTo(map)
                        .bindPopup('Vous êtes ici')
                        .openPopup();
                },
                error => {
                    console.error('Erreur de géolocalisation:', error);
                    alert('Impossible de vous localiser');
                }
            );
        } else {
            alert('La géolocalisation n\'est pas supportée par votre navigateur');
        }
    });

    // Fullscreen
    document.getElementById('fullscreen-map').addEventListener('click', () => {
        const elem = document.querySelector('.map-container-wrapper');
        if (!document.fullscreenElement) {
            if (elem.requestFullscreen) {
                elem.requestFullscreen();
            } else if (elem.webkitRequestFullscreen) {
                elem.webkitRequestFullscreen();
            } else if (elem.msRequestFullscreen) {
                elem.msRequestFullscreen();
            }
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            }
        }
    });

    // Initialiser quand la page est prête
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMap);
    } else {
        initMap();
    }
})();
</script>

<?php
get_footer();
