<?php
/**
 * Template Name: soumettre une photo (marque -> modèle + logo + exif)
 * Description: Formulaire d'upload moderne avec drag & drop, preview et design cohérent
 * Version: 3.0.0 - Complete Redesign
 *
 * @package ShiftZoneR
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

/* ====== Réglages ====== */
$CPT           = 'car_photo';
$TAX_BRAND     = 'car_brand';
$TAX_MODEL     = 'car_model';
$ALLOWED_MIMES = ['image/jpeg','image/png','image/gif','image/webp','image/tiff'];
$MAX_BYTES     = wp_max_upload_size();

/* ====== Helpers ====== */
function szr_brand_logo_url($term_id, $size='medium'){
    $att_id = (int) get_term_meta($term_id, '_szr_brand_logo_id', true);
    if (!$att_id) return '';
    $img = wp_get_attachment_image_src($att_id, $size);
    return $img ? $img[0] : '';
}

function szr_extract_exif_gps($file_path){
    $out = [];
    if (!function_exists('exif_read_data')) return $out;
    @ini_set('exif.decode_unicode_intel','UCS-2LE');
    @ini_set('exif.decode_unicode_motorola','UCS-2BE');
    @ini_set('exif.encode_unicode','UTF-8');
    $exif = @exif_read_data($file_path,'EXIF,IFD0,COMPUTED,ANY',true,false);
    if (!$exif || !is_array($exif)) return $out;

    $gps = $exif['GPS'] ?? [];
    if (!empty($gps['GPSLatitude']) && !empty($gps['GPSLatitudeRef']) && !empty($gps['GPSLongitude']) && !empty($gps['GPSLongitudeRef'])) {
        $lat_deg = is_string($gps['GPSLatitude'][0]) && strpos($gps['GPSLatitude'][0],'/') ? eval('return '.$gps['GPSLatitude'][0].';') : $gps['GPSLatitude'][0];
        $lat_min = is_string($gps['GPSLatitude'][1]) && strpos($gps['GPSLatitude'][1],'/') ? eval('return '.$gps['GPSLatitude'][1].';') : $gps['GPSLatitude'][1];
        $lat_sec = is_string($gps['GPSLatitude'][2]) && strpos($gps['GPSLatitude'][2],'/') ? eval('return '.$gps['GPSLatitude'][2].';') : $gps['GPSLatitude'][2];
        $lat = $lat_deg + ($lat_min/60) + ($lat_sec/3600);
        if ($gps['GPSLatitudeRef'] === 'S') $lat *= -1;

        $lng_deg = is_string($gps['GPSLongitude'][0]) && strpos($gps['GPSLongitude'][0],'/') ? eval('return '.$gps['GPSLongitude'][0].';') : $gps['GPSLongitude'][0];
        $lng_min = is_string($gps['GPSLongitude'][1]) && strpos($gps['GPSLongitude'][1],'/') ? eval('return '.$gps['GPSLongitude'][1].';') : $gps['GPSLongitude'][1];
        $lng_sec = is_string($gps['GPSLongitude'][2]) && strpos($gps['GPSLongitude'][2],'/') ? eval('return '.$gps['GPSLongitude'][2].';') : $gps['GPSLongitude'][2];
        $lng = $lng_deg + ($lng_min/60) + ($lng_sec/3600);
        if ($gps['GPSLongitudeRef'] === 'W') $lng *= -1;

        $out['lat'] = $lat;
        $out['lng'] = $lng;
    }

    $exifData = $exif['EXIF'] ?? [];
    $taken = $exifData['DateTimeOriginal'] ?? $exifData['DateTimeDigitized'] ?? null;
    if ($taken){
        $taken_std = str_replace(':','-',substr($taken,0,10)).substr($taken,10);
        $ts = strtotime($taken_std);
        if ($ts) $out['taken_at'] = date('c',$ts);
    }
    return $out;
}

function szr_models_for_brand($brand_term){
    if (!$brand_term || is_wp_error($brand_term)) return [];

    $parent = get_term_by('slug',$brand_term->slug,'car_model');
    if (!$parent) $parent = get_term_by('name',$brand_term->name,'car_model');
    if ($parent && !is_wp_error($parent)){
        $children = get_terms(['taxonomy'=>'car_model','hide_empty'=>false,'parent'=>(int)$parent->term_id,'orderby'=>'name','order'=>'ASC']);
        if (!is_wp_error($children) && !empty($children)) return $children;
    }

    $by_meta = get_terms(['taxonomy'=>'car_model','hide_empty'=>false,'meta_query'=>[['key'=>'_szr_model_brand','value'=>(int)$brand_term->term_id,'compare'=>'=']],'orderby'=>'name','order'=>'ASC']);
    if (!is_wp_error($by_meta) && !empty($by_meta)) return $by_meta;

    return [];
}

/* ====== Sécurité connexion ====== */
if ( ! is_user_logged_in() ){
    ?>
    <div class="upload-page">
        <div class="upload-container">
            <div class="upload-login-required">
                <div class="login-icon">🔒</div>
                <h2>Connexion requise</h2>
                <p>Vous devez être connecté pour partager vos photos automobiles sur ShiftZoneR.</p>
                <a href="<?php echo wp_login_url( get_permalink() ); ?>" class="cta-button">Se connecter</a>
                <a href="<?php echo wp_registration_url(); ?>" class="secondary-button">Créer un compte</a>
            </div>
        </div>
    </div>
    <?php
    get_footer();
    return;
}

/* ====== Vérification limite upload ====== */
$user_id = get_current_user_id();
$today_start = strtotime( 'today midnight' );
$uploads_today = get_posts( array(
    'post_type'      => 'car_photo',
    'author'         => $user_id,
    'date_query'     => array( array( 'after' => date( 'Y-m-d H:i:s', $today_start ) ) ),
    'posts_per_page' => -1,
    'fields'         => 'ids',
) );
$upload_count = count( $uploads_today );
$upload_limit = 100;

if ( $upload_count >= $upload_limit ) {
    ?>
    <div class="upload-page">
        <div class="upload-container">
            <div class="upload-limit-reached">
                <div class="limit-icon">⚠️</div>
                <h2>Limite quotidienne atteinte</h2>
                <p>Vous avez publié <strong><?php echo $upload_limit; ?> photos</strong> aujourd'hui.</p>
                <p>Votre compteur sera réinitialisé demain. Revenez pour partager plus de photos !</p>
                <a href="<?php echo home_url(); ?>" class="secondary-button">Retour à l'accueil</a>
            </div>
        </div>
    </div>
    <?php
    get_footer();
    return;
}

/* ====== Traitement POST ====== */
$errors = [];
$created_post_id = 0;

if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['szr_submit'])) {
    if ( empty($_POST['szr_submit_nonce']) || !wp_verify_nonce($_POST['szr_submit_nonce'],'szr_submit_photo') ){
        $errors[] = 'Vérification de sécurité invalide.';
    }

    $brand_id = (int)($_POST['brand'] ?? 0);
    $model_id = (int)($_POST['model'] ?? 0);
    $title    = sanitize_text_field($_POST['title'] ?? '');
    $desc     = wp_kses_post($_POST['description'] ?? '');

    if ($brand_id<=0) $errors[] = 'Merci de choisir une marque.';
    if ($model_id<=0) $errors[] = 'Merci de choisir un modèle.';

    if ( empty($_FILES['photo']) || !isset($_FILES['photo']['tmp_name']) ){
        $errors[] = 'Aucun fichier reçu.';
    } else {
        $f = $_FILES['photo'];
        if ($f['error'] !== UPLOAD_ERR_OK){
            $errors[] = 'Erreur lors du téléchargement du fichier.';
        } else {
            $mime = @mime_content_type($f['tmp_name']);
            if (!$mime || !in_array($mime,$ALLOWED_MIMES,true))
                $errors[] = 'Format non supporté. Utilisez JPG, PNG, GIF, WEBP ou TIFF.';
            if ($f['size'] > $MAX_BYTES)
                $errors[] = 'Fichier trop volumineux (limite: '.size_format($MAX_BYTES).').';
        }
    }

    if (empty($errors)) {
        require_once ABSPATH.'wp-admin/includes/file.php';
        require_once ABSPATH.'wp-admin/includes/image.php';
        require_once ABSPATH.'wp-admin/includes/media.php';

        $post_id = wp_insert_post([
            'post_type'    => $CPT,
            'post_status'  => 'publish',
            'post_title'   => $title !== '' ? $title : ('Photo — '.current_time('Y-m-d H:i')),
            'post_content' => $desc,
            'post_author'  => get_current_user_id(),
        ], true);

        if (is_wp_error($post_id)){
            $errors[] = 'Erreur lors de la création de la publication.';
        } else {
            wp_set_post_terms($post_id, [$brand_id], $TAX_BRAND, false);
            wp_set_post_terms($post_id, [$model_id], $TAX_MODEL, false);

            $handled = wp_handle_upload($_FILES['photo'], ['test_form'=>false]);
            if (isset($handled['error'])){
                $errors[] = 'Erreur d\'upload: '.$handled['error'];
            } else {
                $filetype = wp_check_filetype(basename($handled['file']), null);
                $attachment = [
                    'post_mime_type' => $filetype['type'],
                    'post_title'     => preg_replace('/\.[^.]+$/','',basename($handled['file'])),
                    'post_content'   => '',
                    'post_status'    => 'inherit',
                ];
                $attach_id = wp_insert_attachment($attachment, $handled['file'], $post_id);
                if (!is_wp_error($attach_id)){
                    $attach_meta = wp_generate_attachment_metadata($attach_id, $handled['file']);
                    wp_update_attachment_metadata($attach_id, $attach_meta);
                    set_post_thumbnail($post_id, $attach_id);

                    $exif = szr_extract_exif_gps($handled['file']);
                    if (!empty($exif)){
                        foreach($exif as $k=>$v) update_post_meta($attach_id, '_szr_exif_'.$k, $v);
                        if (isset($exif['lat'])) update_post_meta($post_id, '_szr_gps_lat', $exif['lat']);
                        if (isset($exif['lng'])) update_post_meta($post_id, '_szr_gps_lng', $exif['lng']);
                        if (isset($exif['taken_at'])) update_post_meta($post_id, '_szr_taken_at', $exif['taken_at']);
                        if (isset($exif['lat'],$exif['lng'])) update_post_meta($post_id, '_szr_gps', $exif['lat'].','.$exif['lng']);
                    }

                    $created_post_id = $post_id;
                }
            }
        }
    }
}

/* ====== Données ====== */
$brands = get_terms(['taxonomy'=>$TAX_BRAND,'hide_empty'=>false,'orderby'=>'name','order'=>'ASC']);
$brand_payload = [];
$models_by_brand = [];
foreach ($brands as $b){
    $brand_payload[] = ['id'=>(int)$b->term_id,'name'=>$b->name,'logo'=>szr_brand_logo_url($b->term_id,'thumbnail')];
    $models = szr_models_for_brand($b);
    $models_by_brand[$b->term_id] = array_map(function($m){return ['id'=>(int)$m->term_id,'name'=>$m->name];}, $models);
}
?>

<div class="upload-page">
    <div class="upload-header">
        <div class="container">
            <h1 class="upload-title">Partager une photo</h1>
            <p class="upload-subtitle">Partagez vos plus belles photos automobiles avec la communauté ShiftZoneR</p>

            <?php if ( $upload_count > 0 ): ?>
            <div class="upload-stats">
                <span class="stats-count"><?php echo $upload_count; ?></span>
                <span class="stats-label">photo<?php echo $upload_count > 1 ? 's' : ''; ?> publiée<?php echo $upload_count > 1 ? 's' : ''; ?> aujourd'hui</span>
                <span class="stats-remaining"><?php echo ($upload_limit - $upload_count); ?> restante<?php echo ($upload_limit - $upload_count) > 1 ? 's' : ''; ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="upload-container">
        <?php if (!empty($errors)): ?>
        <div class="upload-alert upload-alert-error">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
            </svg>
            <div>
                <strong>Erreurs détectées</strong>
                <ul>
                    <?php foreach ($errors as $e) echo '<li>'.esc_html($e).'</li>'; ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($created_post_id): ?>
        <div class="upload-alert upload-alert-success">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
            </svg>
            <div>
                <strong>Photo publiée avec succès!</strong>
                <p><a href="<?php echo esc_url(get_permalink($created_post_id)); ?>">Voir votre publication</a> ou <a href="<?php echo esc_url(get_permalink()); ?>">ajouter une autre photo</a></p>
            </div>
        </div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data" class="upload-form" id="upload-form">
            <?php wp_nonce_field('szr_submit_photo','szr_submit_nonce'); ?>
            <input type="hidden" name="szr_submit" value="1">
            <input type="file" id="photo-input" name="photo" accept="image/*" required style="display:none">

            <!-- Zone d'upload -->
            <div class="upload-section">
                <h2 class="section-title">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                    </svg>
                    Votre photo
                </h2>

                <div class="upload-drop-zone" id="drop-zone">
                    <div class="drop-zone-content" id="drop-content">
                        <svg class="drop-icon" width="64" height="64" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96zM14 13v4h-4v-4H7l5-5 5 5h-3z"/>
                        </svg>
                        <h3>Glissez votre photo ici</h3>
                        <p>ou cliquez pour parcourir</p>
                        <div class="drop-formats">JPG, PNG, GIF, WEBP, TIFF • Max <?php echo esc_html(size_format($MAX_BYTES)); ?></div>
                    </div>

                    <div class="drop-zone-preview" id="drop-preview" style="display:none;">
                        <img id="preview-image" src="" alt="Preview">
                        <div class="preview-overlay">
                            <button type="button" class="preview-remove" id="remove-photo">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                                </svg>
                                Changer
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations véhicule -->
            <div class="upload-section">
                <h2 class="section-title">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/>
                    </svg>
                    Véhicule
                </h2>

                <div class="form-grid">
                    <div class="form-field">
                        <label for="brand">Marque *</label>
                        <div class="brand-select-wrapper">
                            <div class="brand-logo-preview" id="brand-logo-preview"></div>
                            <select class="form-select" id="brand" name="brand" required>
                                <option value="">Choisir une marque</option>
                                <?php foreach ($brands as $b):
                                    $logo = szr_brand_logo_url($b->term_id,'thumbnail'); ?>
                                    <option value="<?php echo (int)$b->term_id; ?>" data-logo="<?php echo esc_url($logo); ?>">
                                        <?php echo esc_html($b->name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-field">
                        <label for="model">Modèle *</label>
                        <select class="form-select" id="model" name="model" required disabled>
                            <option value="">Choisir d'abord une marque</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Détails optionnels -->
            <div class="upload-section">
                <h2 class="section-title">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                    </svg>
                    Détails (optionnel)
                </h2>

                <div class="form-grid">
                    <div class="form-field">
                        <label for="title">Titre</label>
                        <input class="form-input" type="text" id="title" name="title" placeholder="Ex: Alpine A110 à Deauville">
                    </div>

                    <div class="form-field form-field-full">
                        <label for="description">Description</label>
                        <textarea class="form-textarea" id="description" name="description" rows="4" placeholder="Partagez l'histoire derrière cette photo..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Bouton submit -->
            <div class="upload-actions">
                <button type="submit" class="upload-submit-btn" id="submit-btn">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                    </svg>
                    Publier la photo
                </button>
                <p class="upload-help">La publication peut prendre quelques secondes selon la taille du fichier</p>
            </div>
        </form>
    </div>
</div>

<script>
(function(){
    const SZR_BRANDS = <?php echo wp_json_encode($brand_payload, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE); ?>;
    const SZR_MODELS_BY_BRAND = <?php echo wp_json_encode($models_by_brand, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE); ?>;

    const dropZone = document.getElementById('drop-zone');
    const dropContent = document.getElementById('drop-content');
    const dropPreview = document.getElementById('drop-preview');
    const photoInput = document.getElementById('photo-input');
    const previewImage = document.getElementById('preview-image');
    const removeBtn = document.getElementById('remove-photo');
    const brandSelect = document.getElementById('brand');
    const modelSelect = document.getElementById('model');
    const brandLogoPreview = document.getElementById('brand-logo-preview');
    const submitBtn = document.getElementById('submit-btn');

    // Click to browse
    dropZone.addEventListener('click', (e) => {
        if (!e.target.closest('.preview-remove')) {
            photoInput.click();
        }
    });

    // File selection
    photoInput.addEventListener('change', (e) => {
        if (e.target.files && e.target.files[0]) {
            handleFile(e.target.files[0]);
        }
    });

    // Drag & drop
    ['dragenter', 'dragover'].forEach(evt => {
        dropZone.addEventListener(evt, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropZone.classList.add('drag-over');
        });
    });

    ['dragleave', 'drop'].forEach(evt => {
        dropZone.addEventListener(evt, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropZone.classList.remove('drag-over');
        });
    });

    dropZone.addEventListener('drop', (e) => {
        const files = e.dataTransfer.files;
        if (files && files[0]) {
            photoInput.files = files;
            handleFile(files[0]);
        }
    });

    // Handle file preview
    function handleFile(file) {
        if (!file.type.startsWith('image/')) {
            alert('Veuillez sélectionner une image');
            return;
        }

        const reader = new FileReader();
        reader.onload = (e) => {
            previewImage.src = e.target.result;
            dropContent.style.display = 'none';
            dropPreview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }

    // Remove photo
    removeBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        photoInput.value = '';
        dropContent.style.display = 'flex';
        dropPreview.style.display = 'none';
        previewImage.src = '';
    });

    // Brand selection
    brandSelect.addEventListener('change', () => {
        const brandId = brandSelect.value;
        const opt = brandSelect.options[brandSelect.selectedIndex];
        const logo = opt ? opt.getAttribute('data-logo') : '';

        // Update logo preview
        if (logo) {
            brandLogoPreview.style.backgroundImage = `url(${logo})`;
            brandLogoPreview.style.display = 'block';
        } else {
            brandLogoPreview.style.display = 'none';
        }

        // Update models
        modelSelect.innerHTML = '';
        if (!brandId) {
            modelSelect.disabled = true;
            modelSelect.innerHTML = '<option value="">Choisir d\'abord une marque</option>';
            return;
        }

        const models = SZR_MODELS_BY_BRAND[brandId] || [];
        if (!models.length) {
            modelSelect.disabled = true;
            modelSelect.innerHTML = '<option value="">Aucun modèle pour cette marque</option>';
            return;
        }

        modelSelect.disabled = false;
        modelSelect.innerHTML = '<option value="">Choisir un modèle</option>';
        models.forEach(m => {
            const opt = document.createElement('option');
            opt.value = m.id;
            opt.textContent = m.name;
            modelSelect.appendChild(opt);
        });
    });

    // Form submit
    document.getElementById('upload-form').addEventListener('submit', () => {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<svg class="spinner-svg" width="24" height="24" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="4" opacity="0.25"/><path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg> Publication en cours...';
    });
})();
</script>

<?php get_footer(); ?>
