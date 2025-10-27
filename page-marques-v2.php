<?php
/**
 * Template Name: Marques & Modèles V2
 * Description: Navigation simple et fonctionnelle marques → modèles → photos
 * Version: 2.0.0 - SIMPLE & FUNCTIONAL
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
            <h1>Marques & Modèles</h1>
            <p>Explorez notre collection de voitures par marque et modèle</p>
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
                        <?php if ( $logo_url ) : ?>
                            <img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $brand->name ); ?>" class="brand-logo">
                        <?php else : ?>
                            <div class="brand-logo-placeholder"><?php echo esc_html( mb_substr( $brand->name, 0, 1 ) ); ?></div>
                        <?php endif; ?>
                        <h3><?php echo esc_html( $brand->name ); ?></h3>
                        <p><?php echo esc_html( $brand->count ); ?> photo<?php echo $brand->count > 1 ? 's' : ''; ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- View: Models -->
        <div id="models-view" class="view-hidden">
            <button class="back-button" onclick="showBrands()">← Retour aux marques</button>
            <div id="models-content"></div>
        </div>

        <!-- View: Photos -->
        <div id="photos-view" class="view-hidden">
            <button class="back-button" onclick="showModels()">← Retour aux modèles</button>
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
                    <input type="text" name="title">
                </div>

                <div class="form-group">
                    <label>Description (optionnelle)</label>
                    <textarea name="description" rows="3"></textarea>
                </div>

                <button type="submit" class="btn-primary">Publier</button>
            </form>
            <div id="upload-progress" style="display:none;">
                <div class="progress-bar"><div class="progress-fill"></div></div>
                <p id="progress-text"></p>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
.brands-page-v2 { padding: 40px 0; }
.page-header { text-align: center; margin-bottom: 40px; }
.page-header h1 { font-size: 2.5rem; margin: 0 0 10px; }
.page-header p { color: #666; font-size: 1.1rem; }

.breadcrumb { margin-bottom: 30px; display: flex; gap: 10px; flex-wrap: wrap; }
.breadcrumb-item { color: #666; cursor: pointer; }
.breadcrumb-item.active { color: #1a1a1a; font-weight: 600; }
.breadcrumb-separator { color: #999; }

.view-active { display: block; }
.view-hidden { display: none; }

.brands-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; }
.brand-card {
    background: #fff;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    padding: 30px 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
}
.brand-card:hover {
    border-color: #ff6b35;
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}
.brand-logo { width: 80px; height: 80px; object-fit: contain; margin: 0 auto 15px; }
.brand-logo-placeholder {
    width: 80px;
    height: 80px;
    margin: 0 auto 15px;
    background: linear-gradient(135deg, #ff6b35, #f7931e);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    font-weight: 700;
    color: #fff;
}
.brand-card h3 { margin: 0 0 10px; font-size: 1.25rem; }
.brand-card p { margin: 0; color: #666; font-size: 0.95rem; }

.models-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; }
.model-card {
    background: #fff;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.3s;
}
.model-card:hover {
    border-color: #ff6b35;
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}
.model-card img { width: 100%; height: 180px; object-fit: cover; }
.model-card-content { padding: 20px; }
.model-card h3 { margin: 0 0 10px; font-size: 1.1rem; }
.model-card p { margin: 0; color: #666; font-size: 0.9rem; }

.photos-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
.photo-card {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: all 0.3s;
}
.photo-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}
.photo-card img { width: 100%; height: 250px; object-fit: cover; }
.photo-card-content { padding: 15px; }
.photo-card h3 { margin: 0 0 10px; font-size: 1rem; }
.photo-card-meta { display: flex; gap: 15px; font-size: 0.85rem; color: #666; }

.back-button {
    margin-bottom: 20px;
    padding: 10px 20px;
    background: #f5f5f5;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 1rem;
    transition: all 0.3s;
}
.back-button:hover { background: #e0e0e0; }

.loading { text-align: center; padding: 60px 20px; }
.spinner {
    width: 50px;
    height: 50px;
    margin: 0 auto 20px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #ff6b35;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}
@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

.modal { position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 9999; display: flex; align-items: center; justify-content: center; }
.modal-overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); }
.modal-content { position: relative; background: #fff; border-radius: 12px; padding: 30px; max-width: 500px; width: 90%; max-height: 90vh; overflow-y: auto; }
.modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.modal-header h3 { margin: 0; }
.modal-close { background: none; border: none; font-size: 2rem; cursor: pointer; color: #666; line-height: 1; }
.form-group { margin-bottom: 20px; }
.form-group label { display: block; margin-bottom: 8px; font-weight: 600; }
.form-group input, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem; }
.btn-primary { width: 100%; padding: 12px; background: linear-gradient(135deg, #ff6b35, #f7931e); color: #fff; border: none; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: all 0.3s; }
.btn-primary:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(255,107,53,0.3); }
.btn-secondary { padding: 10px 20px; background: #f5f5f5; border: none; border-radius: 8px; cursor: pointer; margin-top: 15px; }

.upload-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: linear-gradient(135deg, #ff6b35, #f7931e);
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    margin: 20px 0;
}
.upload-btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(255,107,53,0.3); }

.no-data { text-align: center; padding: 60px 20px; color: #999; }
</style>

<script>
let currentBrand = null;
let currentModel = null;
const ajaxUrl = '<?php echo admin_url( 'admin-ajax.php' ); ?>';

// Show brands view
function showBrands() {
    document.getElementById('brands-view').className = 'view-active';
    document.getElementById('models-view').className = 'view-hidden';
    document.getElementById('photos-view').className = 'view-hidden';
    currentBrand = null;
    currentModel = null;
    updateBreadcrumb();
}

// Show models view
function showModels() {
    document.getElementById('brands-view').className = 'view-hidden';
    document.getElementById('models-view').className = 'view-active';
    document.getElementById('photos-view').className = 'view-hidden';
    currentModel = null;
    updateBreadcrumb();
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
        container.innerHTML = '<div class="no-data"><p>Aucun modèle pour cette marque.</p></div>';
        showModels();
        updateBreadcrumb();
        return;
    }

    let html = '<h2>' + currentBrand.name + ' - Modèles</h2><div class="models-grid">';

    models.forEach(model => {
        html += '<div class="model-card" onclick="loadModel(' + model.id + ', \'' + model.name.replace(/'/g, "\\'") + '\')">';
        if (model.thumbnail) {
            html += '<img src="' + model.thumbnail + '" alt="' + model.name + '">';
        }
        html += '<div class="model-card-content">';
        html += '<h3>' + model.name + '</h3>';
        html += '<p>' + model.count + ' photo' + (model.count > 1 ? 's' : '') + '</p>';
        html += '</div></div>';
    });

    html += '</div>';
    container.innerHTML = html;
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

    let html = '<h2>' + currentBrand.name + ' ' + currentModel.name + '</h2>';

    if (isLoggedIn) {
        html += '<button class="upload-btn" onclick="openUploadModal()">+ Ajouter une photo</button>';
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
                html += '<img src="' + photo.thumbnail + '" alt="' + photo.title + '">';
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
                    loadModel(currentModel.id, currentModel.name);
                } else {
                    alert('Erreur: ' + (data.data || 'Échec de l\'upload'));
                }
                document.getElementById('upload-progress').style.display = 'none';
                this.style.display = 'block';
            })
            .catch(err => {
                console.error('Upload error:', err);
                alert('Erreur réseau lors de l\'upload');
                document.getElementById('upload-progress').style.display = 'none';
                this.style.display = 'block';
            });
        });
    }
    <?php endif; ?>
});
</script>

<?php
get_footer();
