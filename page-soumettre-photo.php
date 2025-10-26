<?php
/**
 * Template Name: soumettre une photo (marque -> modèle + logo + exif)
 * Description: formulaire front-end avec sélection dépendante (d’abord la marque, puis les modèles filtrés), affichage du logo de marque, upload robuste et EXIF (gps/date).
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

/* ====== réglages ====== */
$CPT           = 'car_photo';
$TAX_BRAND     = 'car_brand';
$TAX_MODEL     = 'car_model';
$ALLOWED_MIMES = ['image/jpeg','image/png','image/gif','image/webp','image/tiff'];
$MAX_BYTES     = wp_max_upload_size();

/* ====== helpers ====== */
function szr_brand_logo_url($term_id, $size='medium'){
    $att_id = (int) get_term_meta($term_id, '_szr_brand_logo_id', true);
    if (!$att_id) return '';
    $img = wp_get_attachment_image_src($att_id, $size);
    return $img ? $img[0] : '';
}
function szr_exif_frac_to_float($value){
    if (is_string($value) && strpos($value,'/') !== false){
        list($n,$d) = array_map('floatval', explode('/',$value,2));
        return $d != 0 ? $n/$d : 0.0;
    }
    return (float)$value;
}
function szr_exif_gps_to_decimal($components,$ref){
    if (!is_array($components) || count($components)<3) return null;
    $deg = szr_exif_frac_to_float($components[0]);
    $min = szr_exif_frac_to_float($components[1]);
    $sec = szr_exif_frac_to_float($components[2]);
    $decimal = $deg + ($min/60.0) + ($sec/3600.0);
    $ref = strtoupper(trim((string)$ref));
    if ($ref==='S' || $ref==='W') $decimal *= -1;
    return $decimal;
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
        $lat = szr_exif_gps_to_decimal($gps['GPSLatitude'],$gps['GPSLatitudeRef']);
        $lng = szr_exif_gps_to_decimal($gps['GPSLongitude'],$gps['GPSLongitudeRef']);
        if ($lat!==null && $lng!==null){ $out['lat']=$lat; $out['lng']=$lng; }
    }
    if (!empty($gps['GPSAltitude'])) $out['alt'] = szr_exif_frac_to_float($gps['GPSAltitude']);

    $exifData = $exif['EXIF'] ?? [];
    $taken = $exifData['DateTimeOriginal'] ?? $exifData['DateTimeDigitized'] ?? null;
    if ($taken){
        $taken_std = str_replace(':','-',substr($taken,0,10)).substr($taken,10);
        $ts = strtotime($taken_std);
        if ($ts) $out['taken_at'] = date('c',$ts);
    }
    return $out;
}

/**
 * récupère les modèles liés à une marque (ordre: parent car_model = slug/nom marque → meta _szr_model_brand → posts)
 */
function szr_models_for_brand($brand_term){
    if (!$brand_term || is_wp_error($brand_term)) return [];

    // 1) enfants d’un parent car_model correspondant à la marque
    $parent = get_term_by('slug',$brand_term->slug,'car_model');
    if (!$parent) $parent = get_term_by('name',$brand_term->name,'car_model');
    if ($parent && !is_wp_error($parent)){
        $children = get_terms([
            'taxonomy'   => 'car_model',
            'hide_empty' => false,
            'parent'     => (int)$parent->term_id,
            'orderby'    => 'name',
            'order'      => 'ASC',
        ]);
        if (!is_wp_error($children) && !empty($children)) return $children;
    }

    // 2) meta _szr_model_brand
    $by_meta = get_terms([
        'taxonomy'   => 'car_model',
        'hide_empty' => false,
        'meta_query' => [[
            'key'     => '_szr_model_brand',
            'value'   => (int)$brand_term->term_id,
            'compare' => '=',
        ]],
        'orderby'    => 'name',
        'order'      => 'ASC',
    ]);
    if (!is_wp_error($by_meta) && !empty($by_meta)) return $by_meta;

    // 3) fallback via posts
    $post_ids = get_posts([
        'post_type'      => 'car_photo',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'no_found_rows'  => true,
        'tax_query'      => [[
            'taxonomy' => 'car_brand',
            'field'    => 'term_id',
            'terms'    => (int)$brand_term->term_id,
        ]],
    ]);
    if (!empty($post_ids)){
        $models = get_terms([
            'taxonomy'   => 'car_model',
            'hide_empty' => true,
            'orderby'    => 'name',
            'order'      => 'ASC',
            'object_ids' => $post_ids,
        ]);
        if (!is_wp_error($models) && !empty($models)) return $models;
    }
    return [];
}

/* ====== sécurité connexion ====== */
if ( ! is_user_logged_in() ){
    echo '<div class="szr-wrap"><p>vous devez être connecté·e pour publier une photo.</p></div>';
    get_footer(); return;
}

/* ====== traitement POST ====== */
$errors = [];
$created_post_id = 0;

if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['szr_submit'])) {

    if ( empty($_POST['szr_submit_nonce']) || !wp_verify_nonce($_POST['szr_submit_nonce'],'szr_submit_photo') ){
        $errors[] = 'vérification de sécurité invalide (nonce).';
    }

    $brand_id = (int)($_POST['brand'] ?? 0);
    $model_id = (int)($_POST['model'] ?? 0);
    $title    = sanitize_text_field($_POST['title'] ?? '');
    $desc     = wp_kses_post($_POST['description'] ?? '');

    if ($brand_id<=0) $errors[] = 'merci de choisir une marque.';
    if ($model_id<=0) $errors[] = 'merci de choisir un modèle.';

    if ( empty($_FILES['photo']) || !isset($_FILES['photo']['tmp_name']) ){
        $errors[] = 'aucun fichier reçu.';
    } else {
        $f = $_FILES['photo'];
        if ($f['error'] !== UPLOAD_ERR_OK){
            $map = [
                UPLOAD_ERR_INI_SIZE=>'fichier trop lourd (ini).',
                UPLOAD_ERR_FORM_SIZE=>'fichier trop lourd (form).',
                UPLOAD_ERR_PARTIAL=>'téléversement partiel.',
                UPLOAD_ERR_NO_FILE=>'aucun fichier.',
                UPLOAD_ERR_NO_TMP_DIR=>'répertoire temporaire manquant.',
                UPLOAD_ERR_CANT_WRITE=>'écriture impossible sur le disque.',
                UPLOAD_ERR_EXTENSION=>'téléversement bloqué par une extension.',
            ];
            $errors[] = $map[$f['error']] ?? ('erreur inconnue (code '.$f['error'].')');
        } else {
            $mime = @mime_content_type($f['tmp_name']);
            if (!$mime || !in_array($mime,$ALLOWED_MIMES,true))
                $errors[] = 'format non supporté. (jpg, png, gif, webp, tiff)';
            if ($f['size'] > $MAX_BYTES)
                $errors[] = 'fichier trop volumineux (limite: '.size_format($MAX_BYTES).').';
        }
    }

    if (empty($errors)) {
        if (!function_exists('wp_handle_upload'))                require_once ABSPATH.'wp-admin/includes/file.php';
        if (!function_exists('wp_generate_attachment_metadata'))  require_once ABSPATH.'wp-admin/includes/image.php';
        if (!function_exists('media_handle_upload'))             require_once ABSPATH.'wp-admin/includes/media.php';

        $post_id = wp_insert_post([
            'post_type'    => $CPT,
            'post_status'  => 'publish',
            'post_title'   => $title !== '' ? $title : ('photo — '.current_time('Y-m-d H:i')),
            'post_content' => $desc,
            'post_author'  => get_current_user_id(),
        ], true);

        if (is_wp_error($post_id)){
            $errors[] = 'erreur création du post: '.$post_id->get_error_message();
        } else {
            wp_set_post_terms($post_id, [$brand_id], $TAX_BRAND, false);
            wp_set_post_terms($post_id, [$model_id], $TAX_MODEL, false);

            $handled = wp_handle_upload($_FILES['photo'], ['test_form'=>false]);
            if (isset($handled['error'])){
                $errors[] = 'upload impossible: '.$handled['error'];
            } else {
                $filetype = wp_check_filetype(basename($handled['file']), null);
                $attachment = [
                    'post_mime_type' => $filetype['type'],
                    'post_title'     => preg_replace('/\.[^.]+$/','',basename($handled['file'])),
                    'post_content'   => '',
                    'post_status'    => 'inherit',
                ];
                $attach_id = wp_insert_attachment($attachment, $handled['file'], $post_id);
                if (is_wp_error($attach_id)){
                    $errors[] = 'erreur attachement: '.$attach_id->get_error_message();
                } else {
                    $attach_meta = wp_generate_attachment_metadata($attach_id, $handled['file']);
                    wp_update_attachment_metadata($attach_id, $attach_meta);
                    set_post_thumbnail($post_id, $attach_id);

                    // exif
                    $exif = szr_extract_exif_gps($handled['file']);
                    if (!empty($exif)){
                        foreach($exif as $k=>$v) update_post_meta($attach_id, '_szr_exif_'.$k, $v);
                        if (isset($exif['lat'])) update_post_meta($post_id, '_szr_gps_lat', $exif['lat']);
                        if (isset($exif['lng'])) update_post_meta($post_id, '_szr_gps_lng', $exif['lng']);
                        if (isset($exif['alt'])) update_post_meta($post_id, '_szr_gps_alt', $exif['alt']);
                        if (isset($exif['taken_at'])) update_post_meta($post_id, '_szr_taken_at', $exif['taken_at']);
                        if (isset($exif['lat'],$exif['lng'])) update_post_meta($post_id, '_szr_gps', $exif['lat'].','.$exif['lng']);
                    }

                    $created_post_id = $post_id;
                }
            }
        }
    }
}

/* ====== données pour le formulaire (marques + mapping modèles) ====== */
$brands = get_terms(['taxonomy'=>$TAX_BRAND,'hide_empty'=>false,'orderby'=>'name','order'=>'ASC']);

/* on prépare côté serveur un mapping {brand_id: [{id,name}], ...} + logos */
$brand_payload = [];
$models_by_brand = [];
foreach ($brands as $b){
    $brand_payload[] = [
        'id'   => (int)$b->term_id,
        'name' => $b->name,
        'logo' => szr_brand_logo_url($b->term_id,'thumbnail'),
    ];
    $models = szr_models_for_brand($b);
    $models_by_brand[$b->term_id] = array_map(function($m){
        return ['id'=>(int)$m->term_id,'name'=>$m->name];
    }, $models);
}
?>
<style>
.szr-wrap{max-width:960px;margin:24px auto;padding:20px}
.szr-card{background:#fff;border:1px solid #e5e7eb;border-radius:16px;padding:18px}
.szr-title{font-size:24px;font-weight:800;margin:0 0 4px}
.szr-sub{color:#6b7280;margin:0 0 16px}
.szr-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.szr-field{display:flex;flex-direction:column;gap:6px}
.szr-field label{font-weight:600}
.szr-row{display:flex;align-items:center;gap:12px}
.szr-select, .szr-input, .szr-text{border:1px solid #d1d5db;border-radius:10px;padding:10px;width:100%}
.szr-text{min-height:96px}
.szr-brand-logo{width:44px;height:44px;border-radius:10px;border:1px solid #e5e7eb;background:#fff;object-fit:contain}
.szr-drop{border:2px dashed #d1d5db;border-radius:14px;padding:18px;text-align:center;background:#fafafa}
.szr-drop.drag{background:#eef6ff;border-color:#93c5fd}
.szr-actions{display:flex;gap:10px;align-items:center;margin-top:14px}
.szr-button{background:#2563eb;border:1px solid #1d4ed8;color:#fff;border-radius:10px;padding:10px 14px}
.szr-help{color:#6b7280;font-size:12px}
.szr-alert{margin:10px 0;padding:10px 12px;border:1px solid #fecaca;background:#fff1f2;color:#991b1b;border-radius:10px}
.szr-success{margin:10px 0;padding:10px 12px;border:1px solid #bbf7d0;background:#f0fdf4;color:#166534;border-radius:10px}
@media (max-width:768px){ .szr-grid{grid-template-columns:1fr} .szr-row{align-items:flex-start} }
</style>

<div class="szr-wrap">
  <div class="szr-card">
    <h1 class="szr-title">publier une photo</h1>
    <p class="szr-sub">sélectionne d’abord la marque (le logo s’affiche), puis choisis le modèle associé.</p>

    <?php if (!empty($errors)): ?>
      <div class="szr-alert">
        <strong>erreurs :</strong>
        <ul style="margin:6px 0 0 18px">
          <?php foreach ($errors as $e) echo '<li>'.esc_html($e).'</li>'; ?>
        </ul>
      </div>
    <?php endif; ?>

    <?php if ($created_post_id): ?>
      <div class="szr-success">
        ✅ photo publiée. <a href="<?php echo esc_url(get_permalink($created_post_id)); ?>">voir la publication</a>
      </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
      <?php wp_nonce_field('szr_submit_photo','szr_submit_nonce'); ?>
      <input type="hidden" name="szr_submit" value="1">

      <div class="szr-grid">
        <!-- marque (avec logo à côté) -->
        <div class="szr-field">
          <label for="brand">marque</label>
          <div class="szr-row">
            <img id="brandLogo" class="szr-brand-logo" alt="logo marque" src="" aria-hidden="true">
            <select class="szr-select" id="brand" name="brand" required>
              <option value="">— choisir —</option>
              <?php foreach ($brands as $b): 
                $logo = szr_brand_logo_url($b->term_id,'thumbnail'); ?>
                <option value="<?php echo (int)$b->term_id; ?>" data-logo="<?php echo esc_url($logo); ?>">
                  <?php echo esc_html($b->name); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="szr-help">le logo s’affiche à gauche (l’image dans la liste n’est pas supportée par les navigateurs).</div>
        </div>

        <!-- modèle (rempli dynamiquement selon la marque) -->
        <div class="szr-field">
          <label for="model">modèle</label>
          <select class="szr-select" id="model" name="model" required disabled>
            <option value="">— choisir une marque d’abord —</option>
          </select>
        </div>

        <div class="szr-field">
          <label for="title">titre (optionnel)</label>
          <input class="szr-input" type="text" id="title" name="title" placeholder="ex: alpine a110 à deauville">
        </div>

        <div class="szr-field">
          <label for="description">description (optionnel)</label>
          <textarea class="szr-text" id="description" name="description" placeholder="détails, contexte…"></textarea>
        </div>

        <div class="szr-field" style="grid-column:1/-1">
          <label>image</label>
          <div class="szr-drop" id="szr-drop">
            <input type="file" id="photo" name="photo" accept="image/*" style="display:none" required>
            <button type="button" class="szr-button" id="pick">choisir un fichier</button>
            <div class="szr-help">formats: jpg, png, gif, webp, tiff • max <?php echo esc_html( size_format($MAX_BYTES) ); ?></div>
            <div id="preview" style="margin-top:10px"></div>
          </div>
        </div>
      </div>

      <div class="szr-actions">
        <button type="submit" class="szr-button">publier</button>
        <span class="szr-help">l’upload peut prendre quelques secondes selon la taille du fichier</span>
      </div>
    </form>
  </div>
</div>

<script>
(function(){
  // données passées par PHP (marques + mapping modèles)
  const SZR_BRANDS = <?php echo wp_json_encode($brand_payload, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE); ?>;
  const SZR_MODELS_BY_BRAND = <?php echo wp_json_encode($models_by_brand, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE); ?>;

  const brandSel = document.getElementById('brand');
  const modelSel = document.getElementById('model');
  const brandLogo = document.getElementById('brandLogo');

  // maj logo + modèles au changement de marque
  function updateBrandUI(){
    const brandId = brandSel.value;
    const opt = brandSel.options[brandSel.selectedIndex];
    const logo = opt ? opt.getAttribute('data-logo') : '';
    if (logo){
      brandLogo.src = logo;
      brandLogo.removeAttribute('aria-hidden');
      brandLogo.style.visibility = 'visible';
    } else {
      brandLogo.src = '';
      brandLogo.setAttribute('aria-hidden','true');
      brandLogo.style.visibility = 'hidden';
    }

    // modèles
    modelSel.innerHTML = '';
    if (!brandId){
      modelSel.disabled = true;
      const o = document.createElement('option');
      o.value = '';
      o.textContent = '— choisir une marque d’abord —';
      modelSel.appendChild(o);
      return;
    }
    const list = SZR_MODELS_BY_BRAND[brandId] || [];
    if (!list.length){
      modelSel.disabled = true;
      const o = document.createElement('option');
      o.value = '';
      o.textContent = 'aucun modèle défini pour cette marque';
      modelSel.appendChild(o);
      return;
    }
    modelSel.disabled = false;
    const first = document.createElement('option');
    first.value = '';
    first.textContent = '— choisir —';
    modelSel.appendChild(first);
    list.forEach(m=>{
      const opt = document.createElement('option');
      opt.value = m.id;
      opt.textContent = m.name;
      modelSel.appendChild(opt);
    });
  }

  brandSel.addEventListener('change', updateBrandUI);
  // init si le navigateur a gardé l’état du formulaire
  updateBrandUI();

  // drag & drop fichier + preview
  const drop   = document.getElementById('szr-drop');
  const pick   = document.getElementById('pick');
  const input  = document.getElementById('photo');
  const prev   = document.getElementById('preview');

  function showPreview(file){
    prev.innerHTML = '';
    if (!file || !file.type.startsWith('image/')) return;
    const img = document.createElement('img');
    img.style.maxWidth = '100%';
    img.style.borderRadius = '10px';
    img.style.boxShadow = '0 6px 16px rgba(0,0,0,.08)';
    const reader = new FileReader();
    reader.onload = e => { img.src = e.target.result; };
    reader.readAsDataURL(file);
    prev.appendChild(img);
  }

  pick.addEventListener('click', () => input.click());
  input.addEventListener('change', e => showPreview(e.target.files[0]));

  ['dragenter','dragover'].forEach(ev => drop.addEventListener(ev, e => {
    e.preventDefault(); e.stopPropagation(); drop.classList.add('drag');
  }));
  ['dragleave','drop'].forEach(ev => drop.addEventListener(ev, e => {
    e.preventDefault(); e.stopPropagation(); drop.classList.remove('drag');
  }));
  drop.addEventListener('drop', e => {
    const files = e.dataTransfer.files;
    if (files && files[0]) {
      input.files = files;
      showPreview(files[0]);
    }
  });
})();
</script>

<?php get_footer(); ?>
