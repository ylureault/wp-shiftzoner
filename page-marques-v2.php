<?php
/**
 * Template Name: Marques & Modèles V2
 * Description: Navigation simple et fonctionnelle marques → modèles → photos (DARK THEME)
 * Version: 2.1.0 - DARK & SEO OPTIMIZED
 *
 * @package ShiftZoneR
 */

get_header();

// Get all brands
$brands = get_terms( array(
    'taxonomy'   => 'car_brand',
    'hide_empty' => false,
    'orderby'    => 'name',
    'order'      => 'ASC',
) );
?>

<div class="brands-page-v2">
    <div class="container">
        <div class="page-header">
            <h1 id="page-main-title">Marques & Modèles Automobiles</h1>
            <p id="page-subtitle">Explorez notre collection de voitures par marque et modèle</p>
        </div>

        <!-- Navigation breadcrumb -->
        <div id="breadcrumb" class="breadcrumb">
            <span class="breadcrumb-item active">Marques</span>
        </div>

        <!-- View: Brands -->
        <div id="brands-view" class="view-active">
            <div class="brands-grid">
                <?php foreach ( $brands as $brand ) : ?>
                    <?php
                    $logo_id = get_term_meta( $brand->term_id, '_szr_brand_logo_id', true );
                    $logo_url = $logo_id ? wp_get_attachment_image_url( $logo_id, 'medium' ) : '';
                    ?>
                    <div class="brand-card" data-brand-id="<?php echo esc_attr( $brand->term_id ); ?>" data-brand-name="<?php echo esc_attr( $brand->name ); ?>" data-brand-slug="<?php echo esc_attr( $brand->slug ); ?>">
                        <div class="brand-card-inner">
                            <?php if ( $logo_url ) : ?>
                                <img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $brand->name ); ?>" class="brand-logo">
                            <?php else : ?>
                                <div class="brand-logo-placeholder"><?php echo esc_html( mb_substr( $brand->name, 0, 1 ) ); ?></div>
                            <?php endif; ?>
                            <h3><?php echo esc_html( $brand->name ); ?></h3>
                            <p class="brand-count"><?php echo esc_html( $brand->count ); ?> photo<?php echo $brand->count > 1 ? 's' : ''; ?></p>
                            <span class="brand-arrow">→</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- View: Models -->
        <div id="models-view" class="view-hidden">
            <button class="back-button" onclick="showBrands()">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                </svg>
                Retour aux marques
            </button>
            <div id="models-content"></div>
        </div>

        <!-- View: Photos -->
        <div id="photos-view" class="view-hidden">
            <button class="back-button" onclick="showModels()">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                </svg>
                Retour aux modèles
            </button>
            <div id="photos-content"></div>
        </div>

        <!-- Loading -->
        <div id="loading" class="loading" style="display:none;">
            <div class="spinner"></div>
            <p>Chargement...</p>
        </div>
    </div>

    <!-- Upload Modal -->
    <?php if ( is_user_logged_in() ) : ?>
    <div id="upload-modal" class="modal" style="display:none;">
        <div class="modal-overlay" onclick="closeModal()"></div>
        <div class="modal-content">
            <div class="modal-header">
                <h3>Ajouter une photo</h3>
                <button onclick="closeModal()" class="modal-close">×</button>
            </div>
            <form id="upload-form" enctype="multipart/form-data">
                <?php wp_nonce_field( 'szr_ajax_upload', 'ajax_upload_nonce' ); ?>
                <input type="hidden" id="brand_id" name="brand_id">
                <input type="hidden" id="model_id" name="model_id">

                <div class="form-group">
                    <label>Photo *</label>
                    <input type="file" name="photo" accept="image/*" required>
                </div>

                <div class="form-group">
                    <label>Titre (optionnel)</label>
                    <input type="text" name="title" placeholder="ex: BMW M3 au coucher du soleil">
                </div>

                <div class="form-group">
                    <label>Description (optionnelle)</label>
                    <textarea name="description" rows="3" placeholder="Décrivez votre photo..."></textarea>
                </div>

                <button type="submit" class="btn-primary">
                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                    </svg>
                    Publier
                </button>
            </form>
            <div id="upload-progress" style="display:none;">
                <div class="progress-bar"><div class="progress-fill"></div></div>
                <p id="progress-text">Upload en cours...</p>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
/* Dark Theme Variables */
.brands-page-v2 {
    padding: 40px 0;
    background: var(--dark);
    min-height: calc(100vh - 85px);
}

.page-header {
    text-align: center;
    margin-bottom: 40px;
}

.page-header h1 {
    font-size: 3rem;
    font-weight: 900;
    margin: 0 0 15px;
    background: linear-gradient(135deg, var(--text), var(--secondary));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    transition: all 0.3s ease;
}

.page-header p {
    color: var(--text-muted);
    font-size: 1.2rem;
}

.breadcrumb {
    margin-bottom: 30px;
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    padding: 1rem 1.5rem;
    background: var(--dark-gray);
    border-radius: 15px;
    border: 1px solid rgba(255, 0, 85, 0.1);
}

.breadcrumb-item {
    color: var(--text-muted);
    cursor: pointer;
    transition: color 0.3s ease;
}

.breadcrumb-item:hover {
    color: var(--primary);
}

.breadcrumb-item.active {
    color: var(--text);
    font-weight: 700;
}

.breadcrumb-separator {
    color: var(--text-muted);
    opacity: 0.5;
}

.view-active {
    display: block;
    animation: fadeIn 0.3s ease;
}

.view-hidden {
    display: none;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Brands Grid - DARK THEME */
.brands-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 2rem;
}

.brand-card {
    background: var(--dark-gray);
    border: 2px solid rgba(255, 0, 85, 0.1);
    border-radius: 20px;
    padding: 0;
    cursor: pointer;
    transition: all 0.3s ease;
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
    border-color: var(--primary);
    transform: translateY(-10px);
    box-shadow: 0 20px 60px rgba(255, 0, 85, 0.3);
}

.brand-card-inner {
    padding: 2rem 1.5rem;
    text-align: center;
    position: relative;
}

.brand-logo {
    width: 100px;
    height: 100px;
    object-fit: contain;
    margin: 0 auto 20px;
    display: block;
    filter: brightness(1.2);
}

.brand-logo-placeholder {
    width: 100px;
    height: 100px;
    margin: 0 auto 20px;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    font-weight: 900;
    color: white;
}

.brand-card h3 {
    margin: 0 0 10px;
    font-size: 1.4rem;
    font-weight: 900;
    color: var(--text);
}

.brand-count {
    margin: 0;
    color: var(--text-muted);
    font-size: 0.95rem;
}

.brand-arrow {
    display: block;
    margin-top: 15px;
    font-size: 1.5rem;
    color: var(--primary);
    transition: transform 0.3s ease;
}

.brand-card:hover .brand-arrow {
    transform: translateX(10px);
}

/* Models Grid - DARK THEME */
.models-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}

.model-card {
    background: var(--dark-gray);
    border: 2px solid rgba(255, 0, 85, 0.1);
    border-radius: 20px;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
}

.model-card:hover {
    border-color: var(--primary);
    transform: translateY(-10px);
    box-shadow: 0 20px 60px rgba(255, 0, 85, 0.3);
}

.model-card img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.model-card:hover img {
    transform: scale(1.1);
}

.model-card-content {
    padding: 1.5rem;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.model-card h3 {
    margin: 0 0 10px;
    font-size: 1.3rem;
    font-weight: 900;
    color: var(--text);
}

.model-description {
    color: var(--text-muted);
    font-size: 0.95rem;
    line-height: 1.6;
    margin-bottom: 15px;
    flex: 1;
}

.model-count {
    margin: 0 0 15px;
    color: var(--text-muted);
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.model-groups {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
    padding-top: 1rem;
    border-top: 1px solid rgba(255, 255, 255, 0.05);
}

.group-badge {
    padding: 0.5rem 1rem;
    background: rgba(255, 0, 85, 0.1);
    border: 1px solid var(--primary);
    border-radius: 50px;
    font-size: 0.85rem;
    color: var(--primary);
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.group-badge:hover {
    background: var(--primary);
    color: white;
}

/* Photos Grid - DARK THEME */
.photos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}

.photo-card {
    background: var(--dark-gray);
    border-radius: 20px;
    overflow: hidden;
    border: 2px solid rgba(255, 0, 85, 0.1);
    transition: all 0.3s ease;
}

.photo-card:hover {
    transform: translateY(-10px);
    border-color: var(--primary);
    box-shadow: 0 20px 60px rgba(255, 0, 85, 0.3);
}

.photo-card a {
    display: block;
    text-decoration: none;
    color: inherit;
}

.photo-card img {
    width: 100%;
    height: 250px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.photo-card:hover img {
    transform: scale(1.05);
}

.photo-card-content {
    padding: 1.5rem;
}

.photo-card h3 {
    margin: 0 0 10px;
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--text);
}

.photo-card-meta {
    display: flex;
    gap: 15px;
    font-size: 0.9rem;
    color: var(--text-muted);
}

/* Back Button - DARK THEME */
.back-button {
    margin-bottom: 30px;
    padding: 12px 24px;
    background: var(--dark-gray);
    border: 2px solid rgba(255, 0, 85, 0.2);
    border-radius: 12px;
    cursor: pointer;
    font-size: 1rem;
    color: var(--text);
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
}

.back-button:hover {
    background: rgba(255, 0, 85, 0.1);
    border-color: var(--primary);
    transform: translateX(-5px);
}

/* Loading - DARK THEME */
.loading {
    text-align: center;
    padding: 80px 20px;
}

.loading p {
    color: var(--text-muted);
}

.spinner {
    width: 60px;
    height: 60px;
    margin: 0 auto 20px;
    border: 4px solid rgba(255, 0, 85, 0.1);
    border-top: 4px solid var(--primary);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Modal - DARK THEME */
.modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.85);
    backdrop-filter: blur(10px);
}

.modal-content {
    position: relative;
    background: var(--dark-gray);
    border: 2px solid rgba(255, 0, 85, 0.2);
    border-radius: 20px;
    padding: 2rem;
    max-width: 550px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    padding-bottom: 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.modal-header h3 {
    margin: 0;
    font-size: 1.75rem;
    font-weight: 900;
    color: var(--text);
}

.modal-close {
    background: none;
    border: none;
    font-size: 2.5rem;
    cursor: pointer;
    color: var(--text-muted);
    line-height: 1;
    transition: color 0.3s ease;
}

.modal-close:hover {
    color: var(--primary);
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: var(--text);
}

.form-group input, .form-group textarea {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    font-size: 1rem;
    background: var(--dark);
    color: var(--text);
    transition: all 0.3s ease;
}

.form-group input:focus, .form-group textarea:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(255, 0, 85, 0.1);
}

.btn-primary {
    width: 100%;
    padding: 14px 24px;
    background: linear-gradient(135deg, var(--primary), #ff3377);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 1.05rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(255, 0, 85, 0.4);
}

.upload-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 14px 28px;
    background: linear-gradient(135deg, var(--primary), #ff3377);
    color: white;
    border: none;
    border-radius: 50px;
    font-size: 1.05rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    margin: 25px 0;
}

.upload-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(255, 0, 85, 0.4);
}

.no-data {
    text-align: center;
    padding: 80px 20px;
    color: var(--text-muted);
    background: var(--dark-gray);
    border-radius: 20px;
    border: 2px dashed rgba(255, 0, 85, 0.2);
}

.no-data p {
    font-size: 1.1rem;
    margin-bottom: 1rem;
}

.progress-bar {
    width: 100%;
    height: 8px;
    background: var(--dark);
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 15px;
}

.progress-fill {
    height: 100%;
    width: 0%;
    background: linear-gradient(90deg, var(--primary), var(--secondary));
    animation: progress 2s ease-in-out infinite;
}

@keyframes progress {
    0% { width: 0%; }
    50% { width: 70%; }
    100% { width: 100%; }
}

#progress-text {
    text-align: center;
    color: var(--text-muted);
}

/* Responsive */
@media (max-width: 768px) {
    .page-header h1 {
        font-size: 2rem;
    }

    .brands-grid,
    .models-grid,
    .photos-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    .breadcrumb {
        padding: 0.75rem 1rem;
        font-size: 0.9rem;
    }
}
</style>

<script>
let currentBrand = null;
let currentModel = null;
const ajaxUrl = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
const siteName = '<?php echo esc_js( get_bloginfo( 'name' ) ); ?>';

// Update page title for SEO
function updatePageTitle(title, subtitle) {
    const h1 = document.getElementById('page-main-title');
    const subEl = document.getElementById('page-subtitle');

    if (h1) {
        h1.textContent = title;
        h1.style.animation = 'none';
        setTimeout(() => h1.style.animation = '', 10);
    }

    if (subEl && subtitle) {
        subEl.textContent = subtitle;
    }

    // Update document title for SEO
    document.title = title + ' | ' + siteName;

    // Update meta description if exists
    const metaDesc = document.querySelector('meta[name="description"]');
    if (metaDesc && subtitle) {
        metaDesc.setAttribute('content', subtitle);
    }
}

// Show brands view
function showBrands() {
    document.getElementById('brands-view').className = 'view-active';
    document.getElementById('models-view').className = 'view-hidden';
    document.getElementById('photos-view').className = 'view-hidden';
    currentBrand = null;
    currentModel = null;
    updateBreadcrumb();
    updatePageTitle('Marques & Modèles Automobiles', 'Explorez notre collection de voitures par marque et modèle');
}

// Show models view
function showModels() {
    document.getElementById('brands-view').className = 'view-hidden';
    document.getElementById('models-view').className = 'view-active';
    document.getElementById('photos-view').className = 'view-hidden';
    currentModel = null;
    updateBreadcrumb();

    if (currentBrand) {
        updatePageTitle(
            currentBrand.name + ' - Modèles',
            'Découvrez tous les modèles ' + currentBrand.name
        );
    }
}

// Update breadcrumb
function updateBreadcrumb() {
    let html = '<span class="breadcrumb-item';
    if (!currentBrand) html += ' active';
    html += '" onclick="showBrands()">Marques</span>';

    if (currentBrand) {
        html += '<span class="breadcrumb-separator">→</span>';
        html += '<span class="breadcrumb-item';
        if (!currentModel) html += ' active';
        html += '" onclick="showModels()">' + currentBrand.name + '</span>';
    }

    if (currentModel) {
        html += '<span class="breadcrumb-separator">→</span>';
        html += '<span class="breadcrumb-item active">' + currentModel.name + '</span>';
    }

    document.getElementById('breadcrumb').innerHTML = html;
}

// Load brand's models
function loadBrand(brandId, brandName, brandSlug) {
    console.log('Loading brand:', brandId, brandName);

    currentBrand = { id: brandId, name: brandName, slug: brandSlug };
    showLoading(true);

    fetch(ajaxUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=szr_get_brand_models&brand_id=' + brandId
    })
    .then(res => res.json())
    .then(data => {
        console.log('Brand models response:', data);
        showLoading(false);

        if (data.success) {
            displayModels(data.data.models);
        } else {
            alert('Erreur: ' + (data.data?.message || 'Impossible de charger les modèles'));
        }
    })
    .catch(err => {
        console.error('Error loading models:', err);
        showLoading(false);
        alert('Erreur réseau lors du chargement des modèles');
    });
}

// Display models
function displayModels(models) {
    const container = document.getElementById('models-content');

    if (!models || models.length === 0) {
        container.innerHTML = '<div class="no-data"><p>Aucun modèle disponible pour cette marque.</p></div>';
        showModels();
        updateBreadcrumb();
        return;
    }

    let html = '';

    models.forEach(model => {
        html += '<div class="model-card" onclick="loadModel(' + model.id + ', \'' + model.name.replace(/'/g, "\\'") + '\')">';
        if (model.thumbnail) {
            html += '<img src="' + model.thumbnail + '" alt="' + model.name + '" loading="lazy">';
        }
        html += '<div class="model-card-content">';
        html += '<h3>' + model.name + '</h3>';

        // Description
        if (model.description) {
            html += '<p class="model-description">' + model.description + '</p>';
        }

        html += '<p class="model-count">';
        html += '<svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">';
        html += '<path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>';
        html += '</svg>';
        html += model.count + ' photo' + (model.count > 1 ? 's' : '') + '</p>';

        // Groups badges
        if (model.groups && model.groups.length > 0) {
            html += '<div class="model-groups">';
            model.groups.forEach(group => {
                html += '<span class="group-badge">';
                html += '<svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20">';
                html += '<path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>';
                html += '</svg>';
                html += group.member_count + ' membres';
                html += '</span>';
            });
            html += '</div>';
        }

        html += '</div></div>';
    });

    container.innerHTML = '<div class="models-grid">' + html + '</div>';
    showModels();
    updateBreadcrumb();
}

// Load model's photos
function loadModel(modelId, modelName) {
    console.log('Loading model:', modelId, modelName);

    currentModel = { id: modelId, name: modelName };
    showLoading(true);

    fetch(ajaxUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=szr_get_model_photos&model_id=' + modelId + '&brand_id=' + currentBrand.id
    })
    .then(res => res.json())
    .then(data => {
        console.log('Model photos response:', data);
        showLoading(false);

        if (data.success) {
            displayPhotos(data.data.photos);
        } else {
            alert('Erreur: ' + (data.data?.message || 'Impossible de charger les photos'));
        }
    })
    .catch(err => {
        console.error('Error loading photos:', err);
        showLoading(false);
        alert('Erreur réseau lors du chargement des photos');
    });
}

// Display photos
function displayPhotos(photos) {
    const container = document.getElementById('photos-content');
    const isLoggedIn = <?php echo is_user_logged_in() ? 'true' : 'false'; ?>;

    let html = '<h2 style="color:var(--text);font-size:2rem;margin-bottom:20px;">' + currentBrand.name + ' ' + currentModel.name + '</h2>';

    if (isLoggedIn) {
        html += '<button class="upload-btn" onclick="openUploadModal()">';
        html += '<svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">';
        html += '<path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>';
        html += '</svg>';
        html += '+ Ajouter une photo</button>';
    }

    if (!photos || photos.length === 0) {
        html += '<div class="no-data"><p>Aucune photo pour ce modèle.</p>';
        if (isLoggedIn) {
            html += '<p>Soyez le premier à en publier une !</p>';
        }
        html += '</div>';
    } else {
        html += '<div class="photos-grid">';
        photos.forEach(photo => {
            html += '<div class="photo-card">';
            html += '<a href="' + photo.url + '">';
            if (photo.thumbnail) {
                html += '<img src="' + photo.thumbnail + '" alt="' + photo.title + '" loading="lazy">';
            }
            html += '<div class="photo-card-content">';
            html += '<h3>' + photo.title + '</h3>';
            html += '<div class="photo-card-meta">';
            html += '<span>👤 ' + photo.author + '</span>';
            html += '<span>❤️ ' + (photo.votes || 0) + '</span>';
            html += '</div></div></a></div>';
        });
        html += '</div>';
    }

    container.innerHTML = html;
    document.getElementById('brands-view').className = 'view-hidden';
    document.getElementById('models-view').className = 'view-hidden';
    document.getElementById('photos-view').className = 'view-active';
    updateBreadcrumb();

    // Update SEO title
    updatePageTitle(
        currentBrand.name + ' ' + currentModel.name + ' - Photos',
        'Découvrez ' + (photos ? photos.length : 0) + ' photo' + ((photos && photos.length > 1) ? 's' : '') + ' de ' + currentBrand.name + ' ' + currentModel.name
    );
}

// Upload modal
function openUploadModal() {
    document.getElementById('brand_id').value = currentBrand.id;
    document.getElementById('model_id').value = currentModel.id;
    document.getElementById('upload-modal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('upload-modal').style.display = 'none';
}

// Show/hide loading
function showLoading(show) {
    document.getElementById('loading').style.display = show ? 'block' : 'none';
}

// Handle form submit
document.addEventListener('DOMContentLoaded', function() {
    // Brand cards click
    document.querySelectorAll('.brand-card').forEach(card => {
        card.addEventListener('click', function() {
            const brandId = this.dataset.brandId;
            const brandName = this.dataset.brandName;
            const brandSlug = this.dataset.brandSlug;
            loadBrand(brandId, brandName, brandSlug);
        });
    });

    <?php if ( is_user_logged_in() ) : ?>
    // Upload form submit
    const form = document.getElementById('upload-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            formData.append('action', 'szr_ajax_upload_photo');

            document.getElementById('upload-progress').style.display = 'block';
            this.style.display = 'none';

            fetch(ajaxUrl, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Photo publiée avec succès !');
                    closeModal();
                    form.reset();
                    loadModel(currentModel.id, currentModel.name);
                } else {
                    alert('Erreur: ' + (data.data || 'Échec de l\'upload'));
                }
                document.getElementById('upload-progress').style.display = 'none';
                form.style.display = 'block';
            })
            .catch(err => {
                console.error('Upload error:', err);
                alert('Erreur réseau lors de l\'upload');
                document.getElementById('upload-progress').style.display = 'none';
                form.style.display = 'block';
            });
        });
    }
    <?php endif; ?>
});
</script>

<?php
get_footer();
