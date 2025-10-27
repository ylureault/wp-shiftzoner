<?php
/**
 * Template Name: garage — tout en une seule page (brands + modèles + upload + filtre)
 * Description: page unique qui gère l’accueil (grille de marques + filtre), la vue marque (modèles filtrés), la vue modèle (photos + upload). navigation par paramètres ?brand=…&model=… et transitions fluides. aucune modif de functions.php requise.
 */

if (!defined('ABSPATH')) exit;

/* ========= réglages ========= */
$CPT         = 'car_photo';
$TAX_BRAND   = 'car_brand';
$TAX_MODEL   = 'car_model';
$ALLOWED_MIMES = ['image/jpeg','image/png','image/gif','image/webp','image/tiff'];
$MAX_BYTES     = wp_max_upload_size();

/* ========= helpers visuels ========= */
function szr_brand_logo_url($term_id, $size = 'medium'){
  $att_id = (int) get_term_meta($term_id, '_szr_brand_logo_id', true);
  if (!$att_id) return '';
  $img = wp_get_attachment_image_src($att_id, $size);
  return $img ? $img[0] : '';
}
function szr_number($n){ return number_format_i18n((int)$n); }

/* ========= exif helpers ========= */
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

/* ========= relations marque ↔ modèles ========= */
/**
 * modèles strictement associés à une marque (jamais “tous” si relation absente)
 * 1) enfants d’un parent car_model dont le slug/nom == marque
 * 2) meta _szr_model_brand == id marque
 * 3) modèles présents via posts car_photo de cette marque
 */
function szr_models_for_brand($brand_term){
  if (!$brand_term || is_wp_error($brand_term)) return [];

  // 1) parent car_model = marque
  $parent = get_term_by('slug', $brand_term->slug, 'car_model');
  if (!$parent) $parent = get_term_by('name', $brand_term->name, 'car_model');
  if ($parent && !is_wp_error($parent)){
    $children = get_terms([
      'taxonomy'=>'car_model','hide_empty'=>false,'parent'=>(int)$parent->term_id,
      'orderby'=>'name','order'=>'ASC'
    ]);
    if (!is_wp_error($children) && !empty($children)) return $children;
  }

  // 2) meta
  $by_meta = get_terms([
    'taxonomy'=>'car_model','hide_empty'=>false,
    'meta_query'=>[[ 'key'=>'_szr_model_brand','value'=>(int)$brand_term->term_id,'compare'=>'=' ]],
    'orderby'=>'name','order'=>'ASC'
  ]);
  if (!is_wp_error($by_meta) && !empty($by_meta)) return $by_meta;

  // 3) via posts
  $post_ids = get_posts([
    'post_type'=>'car_photo','posts_per_page'=>-1,'fields'=>'ids','no_found_rows'=>true,
    'tax_query'=>[[ 'taxonomy'=>'car_brand','field'=>'term_id','terms'=>(int)$brand_term->term_id ]]
  ]);
  if (!empty($post_ids)){
    $models = get_terms([
      'taxonomy'=>'car_model','hide_empty'=>true,'orderby'=>'name','order'=>'ASC','object_ids'=>$post_ids
    ]);
    if (!is_wp_error($models) && !empty($models)) return $models;
  }

  return [];
}

/* ========= routing (même page) ========= */
$brand_slug = isset($_GET['brand']) ? sanitize_title(wp_unslash($_GET['brand'])) : '';
$model_slug = isset($_GET['model']) ? sanitize_title(wp_unslash($_GET['model'])) : '';

$brand_term = $brand_slug ? get_term_by('slug', $brand_slug, $TAX_BRAND) : null;
$model_term = $model_slug ? get_term_by('slug', $model_slug, $TAX_MODEL) : null;

/* si modèle sans marque, on essaye de récupérer la marque pour le fil d’ariane */
if (!$brand_term && $model_term){
  // via meta
  $brand_id = (int) get_term_meta($model_term->term_id, '_szr_model_brand', true);
  if ($brand_id) $brand_term = get_term($brand_id, $TAX_BRAND);
  // sinon via un post
  if (!$brand_term || is_wp_error($brand_term)){
    $q = new WP_Query([
      'post_type'=>$CPT,'posts_per_page'=>1,'fields'=>'ids',
      'tax_query'=>[[ 'taxonomy'=>$TAX_MODEL,'field'=>'term_id','terms'=>$model_term->term_id ]]
    ]);
    if ($q->have_posts()){
      $bts = wp_get_post_terms($q->posts[0], $TAX_BRAND);
      if (!empty($bts) && !is_wp_error($bts)) $brand_term = $bts[0];
    }
    wp_reset_postdata();
  }
  if ($brand_term && !is_wp_error($brand_term)) $brand_slug = $brand_term->slug;
}

/* ========= traitement upload (toujours disponible en vue marque/modèle) ========= */
$errors = [];
$created_post_id = 0;

if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['szr_submit'])) {
  if (!is_user_logged_in()){
    $errors[] = 'il faut être connecté pour publier.';
  }
  if ( empty($_POST['szr_submit_nonce']) || !wp_verify_nonce($_POST['szr_submit_nonce'],'szr_submit_photo') ){
    $errors[] = 'sécurité (nonce) invalide.';
  }

  $chosen_brand = (int)($_POST['brand'] ?? 0);
  $chosen_model = (int)($_POST['model'] ?? 0);
  $title        = sanitize_text_field($_POST['title'] ?? '');
  $desc         = wp_kses_post($_POST['description'] ?? '');

  if ($chosen_brand<=0) $errors[] = 'choisis une marque.';
  if ($chosen_model<=0) $errors[] = 'choisis un modèle.';

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
      if (!$mime || !in_array($mime,$ALLOWED_MIMES,true)) $errors[] = 'format non supporté (jpg, png, gif, webp, tiff).';
      if ($f['size'] > $MAX_BYTES) $errors[] = 'fichier trop volumineux (max '.size_format($MAX_BYTES).').';
    }
  }

  if (empty($errors)){
    if (!function_exists('wp_handle_upload'))               require_once ABSPATH.'wp-admin/includes/file.php';
    if (!function_exists('wp_generate_attachment_metadata')) require_once ABSPATH.'wp-admin/includes/image.php';
    if (!function_exists('media_handle_upload'))            require_once ABSPATH.'wp-admin/includes/media.php';

    $post_id = wp_insert_post([
      'post_type'    => $CPT,
      'post_status'  => 'publish',
      'post_title'   => $title !== '' ? $title : ('photo — '.current_time('Y-m-d H:i')),
      'post_content' => $desc,
      'post_author'  => get_current_user_id(),
    ], true);

    if (is_wp_error($post_id)){
      $errors[] = 'erreur création de la publication: '.$post_id->get_error_message();
    } else {
      wp_set_post_terms($post_id, [$chosen_brand], $TAX_BRAND, false);
      wp_set_post_terms($post_id, [$chosen_model], $TAX_MODEL, false);

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

          // après upload, on “reste” dans la vue actuelle (brand/model) via paramètres actuels
          $redir = add_query_arg(array_filter([
            'brand' => $brand_slug ?: ( $chosen_brand ? get_term($chosen_brand, $TAX_BRAND)->slug : '' ),
            'model' => $model_slug ?: ( $chosen_model ? get_term($chosen_model, $TAX_MODEL)->slug : '' )
          ]), get_permalink());
          wp_safe_redirect($redir); exit;
        }
      }
    }
  }
}

/* ========= données accueil ========= */
$all_brands = get_terms([
  'taxonomy'=>$TAX_BRAND,'hide_empty'=>false,'orderby'=>'name','order'=>'ASC'
]);

/* ========= données vue marque ========= */
$brand_models = [];
if ($brand_term && !is_wp_error($brand_term)) {
  $brand_models = szr_models_for_brand($brand_term);
}

/* ========= données vue modèle ========= */
$model_posts = [];
if ($model_term && !is_wp_error($model_term)) {
  $pq = new WP_Query([
    'post_type'      => $CPT,
    'posts_per_page' => 24,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'tax_query'      => [
      ['taxonomy'=>$TAX_MODEL,'field'=>'term_id','terms'=>$model_term->term_id]
    ],
  ]);
  if ($pq->have_posts()) $model_posts = $pq->posts;
  wp_reset_postdata();
}

get_header();
?>
<style>
:root{
  --bg: radial-gradient(1200px 800px at 0% -10%, #f5f7ff 0%, rgba(255,255,255,0) 60%),
        radial-gradient(900px 700px at 100% 0%, #fff7f7 0%, rgba(255,255,255,0) 60%),
        linear-gradient(180deg, #ffffff 0%, #fafbff 100%);
  --card: #ffffff;
  --text: #0f172a;
  --muted:#64748b;
  --ring: #2563eb;
  --ok: #0ea5e9;
  --shadow: 0 12px 30px rgba(2,6,23,.08), 0 2px 6px rgba(2,6,23,.06);
  --radius-xl: 22px;
}
@media (prefers-color-scheme: dark){
  :root{
    --bg: radial-gradient(1200px 800px at 0% -10%, #0b1220 0%, rgba(14,20,35,0) 60%),
          radial-gradient(900px 700px at 100% 0%, #1b1020 0%, rgba(14,20,35,0) 60%),
          linear-gradient(180deg, #0b1120 0%, #0f172a 100%);
    --card:#0b1220; --text:#e5e7eb; --muted:#9aa4b2; --ring:#60a5fa; --ok:#22d3ee;
    --shadow: 0 12px 30px rgba(0,0,0,.45), 0 2px 6px rgba(0,0,0,.25);
  }
}
.szr-wrap{max-width:1200px;margin:0 auto;padding:20px}

/* description de la marque */
.szr-brand-desc{margin:10px 0;color:var(--text);line-height:1.6;font-size:15px}
.szr-brand-desc p{margin:6px 0}
.szr-head{display:flex;gap:16px;align-items:flex-start;margin-bottom:20px}
.szr-head .szr-logo{flex-shrink:0}

/* barre filtres (visible uniquement à l'accueil) */
.szr-toolbar{position:sticky;top:0;z-index:20;background:var(--bg);backdrop-filter: blur(10px);border-bottom:1px solid rgba(100,116,139,.12)}
.szr-tool-head{display:flex;align-items:center;justify-content:space-between;padding:12px 4px}
.szr-title{font-size:clamp(24px,2.4vw,36px);font-weight:900;margin:0;color:var(--text);letter-spacing:-.01em}
.szr-toggle{display:inline-flex;align-items:center;gap:8px;border:1px solid rgba(100,116,139,.28);background:var(--card);border-radius:999px;padding:8px 12px;box-shadow:var(--shadow);cursor:pointer}
.szr-tool-body{display:none;padding:12px 0 14px;animation: slideDown .25s ease}
.szr-tool-body.open{display:block}
@keyframes slideDown{from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:none}}

.szr-tools{display:grid;grid-template-columns:1fr;gap:10px}
@media(min-width:900px){.szr-tools{grid-template-columns:1.4fr .6fr}}
.szr-input{flex:1;display:flex;align-items:center;gap:8px;background:var(--card);border:1px solid rgba(100,116,139,.25);border-radius:14px;padding:10px 12px;box-shadow: var(--shadow)}
.szr-input input{border:0;outline:0;background:transparent;width:100%;color:var(--text)}
.szr-chip{display:inline-flex;align-items:center;gap:8px;padding:8px 12px;border:1px solid rgba(100,116,139,.2);border-radius:999px;background:var(--card);box-shadow: var(--shadow);cursor:pointer;font-size:14px;color:var(--text)}
.szr-chip[aria-pressed="true"]{border-color:var(--ring);box-shadow:0 0 0 2px rgba(37,99,235,.15), var(--shadow)}
.szr-az{display:flex;gap:6px;overflow:auto;padding:8px 2px 4px;-webkit-overflow-scrolling:touch;scrollbar-width:none}
.szr-az::-webkit-scrollbar{display:none}
.szr-az a{display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:10px;border:1px dashed rgba(100,116,139,.35);color:var(--muted);text-decoration:none}
.szr-az a:hover{border-style:solid;color:var(--text)}
.szr-az a.is-active{border-color: var(--ring); color: var(--text); box-shadow: 0 0 0 2px rgba(37,99,235,.15);}

/* panneaux résultats recherche */
.szr-results{position:relative;margin-top:10px}
.szr-results-inner{position:absolute;left:0;right:0;background:var(--card);border:1px solid rgba(100,116,139,.22);border-radius:16px;box-shadow:var(--shadow);padding:10px;max-height:70vh;overflow:auto;display:none}
.szr-results-inner.show{display:block}
.szr-res-section{padding:6px 6px 2px;color:var(--muted);font-size:12px;text-transform:lowercase}
.szr-res-list{display:grid;grid-template-columns:1fr;gap:8px}
@media(min-width:720px){.szr-res-list{grid-template-columns:1fr 1fr}}
.szr-res-item{display:flex;gap:10px;align-items:center;border:1px solid rgba(100,116,139,.2);border-radius:12px;padding:8px;background:#fff;transition:transform .15s ease, box-shadow .15s ease}
@media(prefers-color-scheme:dark){.szr-res-item{background:#0b1220}}
.szr-res-item:hover{transform:translateY(-2px);box-shadow:0 10px 24px rgba(2,6,23,.10)}
.szr-res-thumb{width:64px;height:64px;border-radius:10px;border:1px solid rgba(100,116,139,.25);object-fit:cover;background:#f3f4f6;flex:none}
.szr-res-title{font-weight:700;color:var(--text)}
.szr-res-sub{font-size:12px;color:var(--muted)}

/* grilles */
.szr-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:16px;margin-top:16px}
@media(min-width:720px){.szr-grid{grid-template-columns:repeat(3,1fr)}}
@media(min-width:1040px){.szr-grid{grid-template-columns:repeat(4,1fr)}}

/* cartes marque / modèle */
.szr-card{position:relative;isolation:isolate;background:var(--card);border:1px solid rgba(100,116,139,.18);border-radius:var(--radius-xl);padding:16px;box-shadow: var(--shadow);transition: transform .25s cubic-bezier(.2,.8,.2,1), box-shadow .25s, border-color .25s}
.szr-card:hover{transform:translateY(-4px);box-shadow:0 16px 40px rgba(2,6,23,.10), 0 2px 10px rgba(2,6,23,.08);border-color:rgba(37,99,235,.35)}
.szr-card:after{content:"";position:absolute;inset:0;border-radius:inherit;background:linear-gradient(135deg, rgba(37,99,235,.08), rgba(14,165,233,.08) 60%, rgba(255,255,255,0) 100%);opacity:0;transition:opacity .35s;pointer-events:none}
.szr-logo{width:72px;height:72px;border-radius:16px;border:1px solid rgba(100,116,139,.18);background:#fff;object-fit:contain;display:block;box-shadow: inset 0 0 0 4px rgba(255,255,255,.6)}
@media(prefers-color-scheme:dark){.szr-logo{background:#0b1220}}
.szr-name{font-weight:800;margin:10px 0 2px;color:var(--text)}
.szr-name mark{background: linear-gradient(90deg, rgba(37,99,235,.18), rgba(14,165,233,.18)); border-radius: 6px; padding: 0 .2em;}
.szr-count{font-size:13px;color:var(--muted)}
.szr-go{position:absolute;right:14px;bottom:14px;display:inline-flex;align-items:center;gap:8px;padding:8px 12px;border-radius:12px;background:linear-gradient(135deg, var(--ring), var(--ok));color:#fff;text-decoration:none;box-shadow: 0 6px 18px rgba(37,99,235,.35);z-index:2}
.szr-tag{position:absolute;left:12px;top:12px;padding:6px 10px;border-radius:999px;background:rgba(37,99,235,.1);color:#1e3a8a;font-size:12px;border:1px solid rgba(37,99,235,.25)}
.szr-linkfill{position:absolute; inset:0; z-index:1}

/* breadcrumb (affiché en vue marque / modèle) */
.szr-bc{display:flex;flex-wrap:wrap;gap:8px;align-items:center;margin:8px 0 16px}
.szr-bc a{color:#111827;text-decoration:none;border-bottom:1px dashed #d1d5db}
.szr-bc .sep{opacity:.5}

/* upload bloc */
.szr-upload{border:2px dashed #d1d5db;border-radius:16px;padding:16px;background:#fff;margin:10px 0}
.szr-row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
@media(max-width:800px){.szr-row{grid-template-columns:1fr}}
.szr-field{display:flex;flex-direction:column;gap:6px}
.szr-select,.szr-input,.szr-text{border:1px solid #d1d5db;border-radius:10px;padding:10px;width:100%}
.szr-text{min-height:96px}
.szr-drop{border:2px dashed #d1d5db;border-radius:14px;padding:12px;text-align:center;background:#fafafa}
.szr-button{background:#2563eb;border:1px solid #1d4ed8;color:#fff;border-radius:10px;padding:10px 14px}
.szr-alert{margin:10px 0;padding:10px;border:1px solid #fecaca;background:#fff1f2;color:#991b1b;border-radius:10px}
.szr-success{margin:10px 0;padding:10px;border:1px solid #bbf7d0;background:#f0fdf4;color:#166534;border-radius:10px}

/* listage photos modèle */
.szr-photos{display:grid;grid-template-columns:repeat(2,1fr);gap:8px;margin-top:12px}
@media(min-width:760px){.szr-photos{grid-template-columns:repeat(3,1fr)}}
@media(min-width:1024px){.szr-photos{grid-template-columns:repeat(4,1fr)}}
.szr-ph{display:block;position:relative;border-radius:12px;overflow:hidden;border:1px solid #e5e7eb}
.szr-ph img{width:100%;height:180px;object-fit:cover;display:block}
.szr-empty{grid-column:1 / -1;text-align:center;color:var(--muted);border:1px dashed rgba(100,116,139,.35);border-radius:16px;padding:18px}

/* animations utilitaires */
.skeleton{animation: shimmer 1.2s infinite linear;background:linear-gradient(90deg, rgba(148,163,184,.15), rgba(148,163,184,.25), rgba(148,163,184,.15));background-size:200% 100%}
@keyframes shimmer{0%{background-position:200% 0}100%{background-position:-200% 0}}
.szr-reveal{opacity:0;transform:translateY(10px);transition:opacity .6s ease, transform .6s ease}
.szr-reveal.is-in{opacity:1;transform:none}
</style>

<div class="szr-wrap">

  <?php
  /* ====== vue accueil (pas de brand) → toolbar + grille des marques ====== */
  if (!$brand_term && !$model_term):
  ?>
  <div class="szr-toolbar" id="toolbar">
    <div class="szr-tool-head">
      <h1 class="szr-title">marques automobiles</h1>
      <button id="szr-toggle" class="szr-toggle" aria-expanded="false">filtres</button>
    </div>
    <div id="szr-tool-body" class="szr-tool-body">
      <div class="szr-tools">
        <label class="szr-input" aria-label="recherche">
          <input id="szr-search" type="search" placeholder="recherche marque ou modèle… (ex: alpine, a110)" autocomplete="off">
        </label>
        <button class="szr-chip" id="szr-sort" aria-pressed="false" data-mode="az"><span>tri: a → z</span></button>
      </div>
      <div class="szr-az" id="szr-az"></div>
      <div class="szr-results">
        <div id="szr-results" class="szr-results-inner"></div>
      </div>
    </div>
  </div>

  <div id="szr-grid" class="szr-grid" aria-live="polite">
    <?php foreach ($all_brands as $b):
      $logo  = szr_brand_logo_url($b->term_id,'medium');
      $count = (int) wp_count_posts($CPT)->publish; // on ne calcule pas par marque ici (perf). on affiche générique ou remplace si besoin.
      $href  = add_query_arg(['brand'=> $b->slug], get_permalink());
      $letter = mb_strtoupper(mb_substr($b->name,0,1));
    ?>
      <article class="szr-card szr-reveal" data-name="<?php echo esc_attr(mb_strtolower($b->name)); ?>" data-letter="<?php echo esc_attr($letter); ?>">
        <a class="szr-linkfill" href="<?php echo esc_url($href); ?>"><span class="screen-reader-text">voir <?php echo esc_html($b->name); ?></span></a>
        <?php if ($logo): ?>
          <img class="szr-logo" src="<?php echo esc_url($logo); ?>" alt="logo <?php echo esc_attr($b->name); ?>" loading="lazy" decoding="async">
        <?php else: ?>
          <div class="szr-logo skeleton" aria-hidden="true"></div>
        <?php endif; ?>
        <div class="szr-name"><?php echo esc_html($b->name); ?></div>
        <div class="szr-count"><?php echo szr_number($count); ?> photos</div>
        <a class="szr-go" href="<?php echo esc_url($href); ?>">voir</a>
      </article>
    <?php endforeach; ?>
  </div>

  <?php
  /* ====== vue marque ====== */
  elseif ($brand_term && !$model_term):
    $brand_logo = szr_brand_logo_url($brand_term->term_id,'medium');
  ?>
  <nav class="szr-bc">
    <a href="<?php echo esc_url( remove_query_arg(['brand','model']) ); ?>">toutes les marques</a>
    <span class="sep">›</span>
    <span><?php echo esc_html($brand_term->name); ?></span>
  </nav>

  <div class="szr-head">
    <?php if ($brand_logo): ?><img src="<?php echo esc_url($brand_logo); ?>" class="szr-logo" alt="" /><?php endif; ?>
    <div>
      <h2 class="szr-title"><?php echo esc_html($brand_term->name); ?></h2>
      <?php
        $brand_description = term_description($brand_term->term_id, $TAX_BRAND);
        if ($brand_description): ?>
          <div class="szr-brand-desc"><?php echo wp_kses_post($brand_description); ?></div>
      <?php endif; ?>
      <p class="szr-sub">choisis un modèle de la marque. uniquement les modèles reliés s'affichent.</p>
    </div>
  </div>

  <div class="szr-grid">
    <?php if (!empty($brand_models)): foreach ($brand_models as $m):
      $href = add_query_arg(['brand'=>$brand_term->slug,'model'=>$m->slug], get_permalink()); ?>
      <article class="szr-card">
        <a class="szr-linkfill" href="<?php echo esc_url($href); ?>"><span class="screen-reader-text"><?php echo esc_html($m->name); ?></span></a>
        <div class="szr-name"><?php echo esc_html($m->name); ?></div>
        <div class="szr-count">explorer</div>
        <a class="szr-go" href="<?php echo esc_url($href); ?>">voir</a>
      </article>
    <?php endforeach; else: ?>
      <p class="szr-empty">aucun modèle rattaché n’a été trouvé pour cette marque.</p>
    <?php endif; ?>
  </div>

  <?php
  /* ====== vue modèle (avec photos + upload) ====== */
  else:
    $brand_logo = $brand_term && !is_wp_error($brand_term) ? szr_brand_logo_url($brand_term->term_id,'thumbnail') : '';
  ?>
  <nav class="szr-bc">
    <a href="<?php echo esc_url( remove_query_arg(['brand','model']) ); ?>">toutes les marques</a>
    <?php if ($brand_term && !is_wp_error($brand_term)): ?>
      <span class="sep">›</span>
      <a href="<?php echo esc_url( add_query_arg(['brand'=>$brand_term->slug], get_permalink()) ); ?>"><?php echo esc_html($brand_term->name); ?></a>
    <?php endif; ?>
    <?php if ($model_term && !is_wp_error($model_term)): ?>
      <span class="sep">›</span>
      <span><?php echo esc_html($model_term->name); ?></span>
    <?php endif; ?>
  </nav>

  <div class="szr-head">
    <?php if ($brand_logo): ?><img src="<?php echo esc_url($brand_logo); ?>" class="szr-logo" alt="" /><?php endif; ?>
    <div>
      <h2 class="szr-title">
        <?php echo esc_html($brand_term ? $brand_term->name : ''); ?>
        <?php if ($model_term): ?> — <?php echo esc_html($model_term->name); ?><?php endif; ?>
      </h2>
      <?php
        // Afficher la description de la marque si disponible
        if ($brand_term && !is_wp_error($brand_term)) {
          $brand_description = term_description($brand_term->term_id, $TAX_BRAND);
          if ($brand_description): ?>
            <div class="szr-brand-desc"><?php echo wp_kses_post($brand_description); ?></div>
          <?php endif;
        }
        // Afficher la description du modèle si disponible
        if ($model_term && !is_wp_error($model_term)) {
          $model_description = term_description($model_term->term_id, $TAX_MODEL);
          if ($model_description): ?>
            <div class="szr-brand-desc"><?php echo wp_kses_post($model_description); ?></div>
          <?php endif;
        }
      ?>
      <p class="szr-sub">dernières photos du modèle. ajoute la tienne juste en dessous.</p>
    </div>
  </div>

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

  <!-- upload (toujours visible en vue modèle; si accès direct à la marque sans modèle, on affiche un sélecteur modèle) -->
  <div class="szr-upload">
    <form method="post" enctype="multipart/form-data">
      <?php wp_nonce_field('szr_submit_photo','szr_submit_nonce'); ?>
      <input type="hidden" name="szr_submit" value="1">
      <div class="szr-row">
        <div class="szr-field">
          <label>marque</label>
          <select class="szr-select" name="brand" id="up_brand" required <?php echo $brand_term ? '' : ''; ?>>
            <option value="">— choisir —</option>
            <?php foreach ($all_brands as $b) {
              $sel = ($brand_term && $brand_term->term_id === $b->term_id) ? 'selected' : '';
              echo '<option value="'.(int)$b->term_id.'" '.$sel.'>'.esc_html($b->name).'</option>';
            } ?>
          </select>
        </div>
        <div class="szr-field">
          <label>modèle</label>
          <select class="szr-select" name="model" id="up_model" required>
            <?php
              $prefill_models = [];
              if ($brand_term) $prefill_models = szr_models_for_brand($brand_term);
              if ($prefill_models){
                echo '<option value="">— choisir —</option>';
                foreach ($prefill_models as $m){
                  $sel = ($model_term && $model_term->term_id === $m->term_id) ? 'selected' : '';
                  echo '<option value="'.(int)$m->term_id.'" '.$sel.'>'.esc_html($m->name).'</option>';
                }
              } elseif ($model_term){
                echo '<option value="'.(int)$model_term->term_id.'" selected>'.esc_html($model_term->name).'</option>';
              } else {
                echo '<option value="">— choisir une marque d’abord —</option>';
              }
            ?>
          </select>
        </div>
        <div class="szr-field">
          <label>titre (optionnel)</label>
          <input class="szr-input" type="text" name="title" placeholder="ex: a110 à deauville">
        </div>
        <div class="szr-field">
          <label>description (optionnel)</label>
          <textarea class="szr-text" name="description" placeholder="détails, contexte…"></textarea>
        </div>
        <div class="szr-field" style="grid-column:1/-1">
          <label>image</label>
          <div class="szr-drop" id="szr-drop">
            <input type="file" id="photo" name="photo" accept="image/*" style="display:none" required>
            <button type="button" class="szr-button" id="pick">choisir un fichier</button>
            <div class="szr-sub">formats: jpg, png, gif, webp, tiff • max <?php echo esc_html( size_format($MAX_BYTES) ); ?></div>
            <div id="preview" style="margin-top:10px"></div>
          </div>
        </div>
      </div>
      <div style="margin-top:10px;display:flex;gap:10px;align-items:center">
        <button class="szr-button" type="submit">publier</button>
        <span class="szr-sub">l’extraction EXIF (gps/date) est automatique si disponible</span>
      </div>
    </form>
  </div>

  <!-- photos du modèle -->
  <div class="szr-photos">
    <?php if (!empty($model_posts)):
      foreach ($model_posts as $p):
        $thumb = get_the_post_thumbnail_url($p->ID,'medium_large'); ?>
        <a class="szr-ph" href="<?php echo esc_url(get_permalink($p->ID)); ?>">
          <?php if ($thumb): ?><img src="<?php echo esc_url($thumb); ?>" alt=""><?php else: ?><div class="skeleton" style="height:180px"></div><?php endif; ?>
        </a>
      <?php endforeach;
    else: ?>
      <p class="szr-empty">aucune photo publiée sur ce modèle pour le moment.</p>
    <?php endif; ?>
  </div>

  <?php endif; // fin vues ?>

</div>

<?php if (!$brand_term && !$model_term): // js du filtre seulement à l’accueil ?>
<script>
(function(){
  const grid   = document.getElementById('szr-grid');
  const search = document.getElementById('szr-search');
  const sortBtn= document.getElementById('szr-sort');
  const azBar  = document.getElementById('szr-az');
  const resultsWrap = document.getElementById('szr-results');
  const toggleBtn = document.getElementById('szr-toggle');
  const toolBody  = document.getElementById('szr-tool-body');
  const cards  = Array.from(grid.querySelectorAll('.szr-card'));

  // ouvre/ferme filtres (mobile fermé par défaut)
  const mediaMobile = window.matchMedia('(max-width: 899px)');
  function setToolbarInitial(){
    const isMobile = mediaMobile.matches;
    toolBody.classList.toggle('open', !isMobile);
    toggleBtn.setAttribute('aria-expanded', String(!isMobile));
  }
  setToolbarInitial();
  toggleBtn.addEventListener('click', ()=>{
    const open = toolBody.classList.toggle('open');
    toggleBtn.setAttribute('aria-expanded', String(open));
  });
  mediaMobile.addEventListener?.('change', setToolbarInitial);

  // utilitaires
  const dia = /[\u0300-\u036f]/g;
  const norm = s => (s||'').toString().normalize('NFD').replace(dia,'').toLowerCase().trim();
  function highlight(card, qRaw){
    const el = card.querySelector('.szr-name');
    const raw = el.dataset.raw || el.textContent;
    el.dataset.raw = raw;
    const q = norm(qRaw);
    if (!q){ el.innerHTML = raw; return; }
    const plain = norm(raw);
    const i = plain.indexOf(q);
    if (i === -1){ el.innerHTML = raw; return; }
    el.innerHTML = raw.slice(0,i)+'<mark>'+raw.slice(i,i+q.length)+'</mark>'+raw.slice(i+q.length);
  }

  // pré-calc
  cards.forEach(c=>{
    const name = c.dataset.name || c.querySelector('.szr-name').textContent;
    c.dataset.name = name;
    c.dataset.nameNorm = norm(name);
    c.dataset.letter = (name[0] || '').toUpperCase();
  });

  // lettres
  const letters = [...new Set(cards.map(c => c.dataset.letter).filter(Boolean))].sort();
  letters.forEach(L=>{
    const a = document.createElement('a');
    a.href = '#'; a.textContent = L;
    a.addEventListener('click', e=>{
      e.preventDefault();
      state.letter = (state.letter===L) ? '' : L;
      if (state.letter){
        search.value=''; state.q='';
        azBar.querySelectorAll('.is-active').forEach(x=>x.classList.remove('is-active'));
        a.classList.add('is-active');
        resultsWrap.classList.remove('show'); resultsWrap.innerHTML='';
      } else { azBar.querySelectorAll('.is-active').forEach(x=>x.classList.remove('is-active')); }
      apply();
    });
    azBar.appendChild(a);
  });

  let state = { q:'', letter:'', mode:'az' };
  let emptyEl = document.createElement('div');
  emptyEl.className='szr-empty'; emptyEl.style.display='none'; emptyEl.textContent='aucun résultat';
  grid.appendChild(emptyEl);

  function apply(){
    const hasQ = state.q.trim().length>0;
    const q = norm(state.q);
    const L = state.letter;
    let visible=0;

    cards.forEach(card=>{
      const matchQ = hasQ ? card.dataset.nameNorm.includes(q) : true;
      const matchL = hasQ ? true : (L ? (card.dataset.letter===L) : true);
      const show = matchQ && matchL;
      card.style.display = show ? '' : 'none';
      if (show) visible++;
      highlight(card, hasQ ? state.q : '');
    });

    emptyEl.style.display = visible ? 'none' : '';

    const vis = cards.filter(c=>c.style.display!=='none');
    vis.sort((a,b)=> state.mode==='az'
      ? a.dataset.nameNorm.localeCompare(b.dataset.nameNorm)
      : 0);
    vis.forEach(c=>grid.appendChild(c));

    sortBtn.setAttribute('aria-pressed', state.mode!=='az');
    sortBtn.querySelector('span').textContent = state.mode==='az' ? 'tri: a → z' : 'tri: personnalisé';
  }

  // reveal
  const io = new IntersectionObserver((entries)=>{
    entries.forEach(en=>{ if(en.isIntersecting){ en.target.classList.add('is-in'); io.unobserve(en.target); }});
  }, {threshold:.12, rootMargin:'80px'});
  cards.forEach(c=>io.observe(c));

  // recherche (marques + modèles) avec suggestions et dernière photo par modèle
  let t;
  search.addEventListener('input', e=>{
    clearTimeout(t);
    const v = e.target.value;
    t = setTimeout(()=>{ state.q=v; if (v&&state.letter){ state.letter=''; azBar.querySelectorAll('.is-active').forEach(x=>x.classList.remove('is-active')); } apply(); renderResults(v); }, 80);
  });

  sortBtn.addEventListener('click', ()=>{ state.mode = (state.mode==='az')?'custom':'az'; apply(); });

  // dataset PHP → JS
  const BRANDS = <?php
    echo wp_json_encode(array_map(function($b){
      return ['id'=>(int)$b->term_id,'name'=>$b->name,'slug'=>$b->slug,'logo'=>szr_brand_logo_url($b->term_id,'thumbnail')];
    }, $all_brands ?: []), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
  ?>;
  const MODELS = <?php
    $models_for_search = get_terms(['taxonomy'=>$TAX_MODEL,'hide_empty'=>true,'orderby'=>'name','order'=>'ASC']);
    echo wp_json_encode(array_map(function($m){
      // essaie d’inférer la marque pour le lien
      $brand_id = (int) get_term_meta($m->term_id, '_szr_model_brand', true);
      $brand_slug = ''; $brand_name = '';
      if ($brand_id){
        $bt = get_term($brand_id,'car_brand'); if ($bt && !is_wp_error($bt)){ $brand_slug=$bt->slug; $brand_name=$bt->name; }
      }
      return ['id'=>(int)$m->term_id,'name'=>$m->name,'slug'=>$m->slug,'brand_slug'=>$brand_slug,'brand'=>$brand_name];
    }, $models_for_search ?: []), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
  ?>;

  function renderResults(qRaw){
    const q = norm(qRaw);
    if (!q){ resultsWrap.classList.remove('show'); resultsWrap.innerHTML=''; return; }

    const brandHits = BRANDS.map(b=>({...b,_n:norm(b.name)})).filter(b=>b._n.includes(q)).slice(0,8);
    const modelHits = MODELS.map(m=>({...m,_n:norm(m.name),_bn:norm(m.brand)})).filter(m=>m._n.includes(q)|| (m._bn && m._bn.includes(q))).slice(0,8);

    const parts=[];
    if (brandHits.length){
      parts.push('<div class="szr-res-section">marques</div><div class="szr-res-list">');
      brandHits.forEach(b=>{
        const u = new URL(window.location.href);
        u.searchParams.set('brand', b.slug); u.searchParams.delete('model');
        parts.push(`<a class="szr-res-item" href="${u.toString()}">
          <img class="szr-res-thumb" src="${b.logo||''}" alt="" onerror="this.style.opacity=.2">
          <div><div class="szr-res-title">${escapeHtml(b.name)}</div><div class="szr-res-sub">voir la marque</div></div>
        </a>`);
      }); parts.push('</div>');
    }
    if (modelHits.length){
      parts.push('<div class="szr-res-section">modèles</div><div class="szr-res-list">');
      modelHits.forEach(m=>{
        const u = new URL(window.location.href);
        if (m.brand_slug) u.searchParams.set('brand', m.brand_slug);
        u.searchParams.set('model', m.slug);
        const id = `mdl-${m.id}`;
        parts.push(`<a class="szr-res-item" id="${id}" href="${u.toString()}">
          <img class="szr-res-thumb" data-model="${m.id}" alt="">
          <div><div class="szr-res-title">${escapeHtml(m.name)}</div><div class="szr-res-sub">${escapeHtml(m.brand||'')}</div></div>
        </a>`);
      }); parts.push('</div>');
    }

    resultsWrap.innerHTML = parts.join('') || '<div class="szr-res-section">aucun résultat</div>';
    resultsWrap.classList.add('show');

    // vignettes: dernière photo
    resultsWrap.querySelectorAll('img[data-model]').forEach(img=>{
      const id = img.getAttribute('data-model');
      const url = new URL(window.location.href);
      url.searchParams.set('szr_latest','1'); url.searchParams.set('model_id', id);
      fetch(url.toString()).then(r=>r.json()).then(j=>{ if (j && j.success && j.img) img.src=j.img; else img.classList.add('skeleton'); })
      .catch(()=>img.classList.add('skeleton'));
    });
  }
  function escapeHtml(s){ return (s||'').replace(/[&<>"']/g, m=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m])); }

  // init
  apply();
})();
</script>
<?php endif; ?>

<script>
/* upload: d&d + preview + cascade modèles selon marque (en vue modèle/marque) */
(function(){
  const pick = document.getElementById('pick');
  const input = document.getElementById('photo');
  const preview = document.getElementById('preview');
  const drop = document.getElementById('szr-drop');
  if (pick && input){
    pick.addEventListener('click', ()=>input.click());
    input.addEventListener('change', e=>{
      const f=e.target.files[0]; preview.innerHTML='';
      if (!f || !f.type.startsWith('image/')) return;
      const img=new Image(); img.style.maxWidth='100%'; img.style.borderRadius='10px'; img.style.boxShadow='0 6px 16px rgba(0,0,0,.08)';
      const r=new FileReader(); r.onload=ev=>img.src=ev.target.result; r.readAsDataURL(f); preview.appendChild(img);
    });
  }
  if (drop){
    ['dragenter','dragover'].forEach(ev=>drop.addEventListener(ev, e=>{e.preventDefault();drop.classList.add('drag');}));
    ['dragleave','drop'].forEach(ev=>drop.addEventListener(ev, e=>{e.preventDefault();drop.classList.remove('drag');}));
    drop.addEventListener('drop', e=>{
      const f=e.dataTransfer.files?.[0]; if (!f) return;
      input.files = e.dataTransfer.files;
      const img=new Image(); img.style.maxWidth='100%'; img.style.borderRadius='10px'; img.style.boxShadow='0 6px 16px rgba(0,0,0,.08)';
      const r=new FileReader(); r.onload=ev=>img.src=ev.target.result; r.readAsDataURL(f);
      preview.innerHTML=''; preview.appendChild(img);
    });
  }

  // cascade modèles selon marque (upload form)
  const upBrand = document.getElementById('up_brand');
  const upModel = document.getElementById('up_model');
  if (upBrand && upModel){
    // prépare mapping côté client
    const MAP = {};
    <?php
      $map = [];
      foreach ($all_brands as $b){
        $ms = szr_models_for_brand($b);
        $map[$b->term_id] = array_map(function($m){ return ['id'=>$m->term_id,'name'=>$m->name]; }, $ms);
      }
      echo 'Object.assign(MAP,'.wp_json_encode($map, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES).');';
    ?>

    function refreshModels(){
      const bid = upBrand.value;
      upModel.innerHTML='';
      if (!bid || !MAP[bid] || MAP[bid].length===0){
        upModel.appendChild(new Option('— choisir une marque d’abord —',''));
        upModel.disabled = true;
        return;
      }
      upModel.disabled = false;
      upModel.appendChild(new Option('— choisir —',''));
      MAP[bid].forEach(m=> upModel.appendChild(new Option(m.name, m.id)) );
    }
    upBrand.addEventListener('change', refreshModels);
  }
})();
</script>

<?php get_footer(); ?>
