<?php
/**
 * Brands Manager View
 *
 * @package ShiftZoneRAdmin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="wrap szr-admin-wrap">
    <h1 class="szr-admin-title">
        <span class="dashicons dashicons-tag"></span>
        <?php _e( 'Marques & Modèles', 'shiftzoner-admin' ); ?>
    </h1>

    <!-- Stats Bar -->
    <div class="szr-stats-bar">
        <div class="szr-stat-item">
            <strong><?php echo esc_html( $total_brands ); ?></strong> <?php _e( 'Marques', 'shiftzoner-admin' ); ?>
        </div>
        <div class="szr-stat-item">
            <strong><?php echo esc_html( $total_models ); ?></strong> <?php _e( 'Modèles', 'shiftzoner-admin' ); ?>
        </div>
        <div class="szr-stat-item">
            <strong><?php echo esc_html( $published_photos ); ?></strong> <?php _e( 'Photos', 'shiftzoner-admin' ); ?>
        </div>
    </div>

    <!-- Toolbar -->
    <div class="szr-toolbar">
        <button class="button button-primary" id="szr-add-brand">
            <span class="dashicons dashicons-plus-alt"></span>
            <?php _e( 'Ajouter une Marque', 'shiftzoner-admin' ); ?>
        </button>
        <button class="button" id="szr-add-model" disabled>
            <span class="dashicons dashicons-plus-alt"></span>
            <?php _e( 'Ajouter un Modèle', 'shiftzoner-admin' ); ?>
        </button>
        <div class="szr-search-box">
            <input type="text" id="szr-search-brands" placeholder="<?php esc_attr_e( 'Rechercher une marque...', 'shiftzoner-admin' ); ?>" />
        </div>
    </div>

    <!-- Main Content -->
    <div class="szr-brands-container">
        <!-- Brands List -->
        <div class="szr-brands-list">
            <h2><?php _e( 'Marques', 'shiftzoner-admin' ); ?></h2>
            <div id="szr-brands-items">
                <?php foreach ( $brands as $brand ) : ?>
                    <?php
                    $logo_id = get_term_meta( $brand->term_id, SZR_META_BRAND_LOGO, true );
                    $logo_url = $logo_id ? wp_get_attachment_image_url( $logo_id, 'thumbnail' ) : '';
                    ?>
                    <div class="szr-brand-item" data-brand-id="<?php echo esc_attr( $brand->term_id ); ?>">
                        <?php if ( $logo_url ) : ?>
                            <img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $brand->name ); ?>" class="szr-brand-logo">
                        <?php else : ?>
                            <div class="szr-brand-logo-empty">
                                <span class="dashicons dashicons-car"></span>
                            </div>
                        <?php endif; ?>
                        <div class="szr-brand-details">
                            <div class="szr-brand-name"><?php echo esc_html( $brand->name ); ?></div>
                            <div class="szr-brand-count"><?php echo esc_html( $brand->count ); ?> photos</div>
                        </div>
                        <div class="szr-brand-actions">
                            <button class="button button-small szr-edit-brand" data-brand-id="<?php echo esc_attr( $brand->term_id ); ?>" title="<?php esc_attr_e( 'Modifier', 'shiftzoner-admin' ); ?>">
                                <span class="dashicons dashicons-edit"></span>
                            </button>
                            <button class="button button-small szr-delete-brand" data-brand-id="<?php echo esc_attr( $brand->term_id ); ?>" title="<?php esc_attr_e( 'Supprimer', 'shiftzoner-admin' ); ?>">
                                <span class="dashicons dashicons-trash"></span>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Models List -->
        <div class="szr-models-list">
            <h2><?php _e( 'Modèles', 'shiftzoner-admin' ); ?></h2>
            <div id="szr-models-placeholder" class="szr-placeholder">
                <span class="dashicons dashicons-car"></span>
                <p><?php _e( 'Sélectionnez une marque pour voir ses modèles', 'shiftzoner-admin' ); ?></p>
            </div>
            <div id="szr-models-items" style="display:none;"></div>
        </div>
    </div>

    <!-- Add/Edit Brand Modal -->
    <div id="szr-brand-modal" class="szr-modal" style="display:none;">
        <div class="szr-modal-overlay"></div>
        <div class="szr-modal-content">
            <div class="szr-modal-header">
                <h2 id="szr-brand-modal-title"><?php _e( 'Ajouter une Marque', 'shiftzoner-admin' ); ?></h2>
                <button class="szr-modal-close">&times;</button>
            </div>
            <div class="szr-modal-body">
                <form id="szr-brand-form">
                    <input type="hidden" id="szr-brand-id" name="brand_id" value="">

                    <div class="szr-form-group">
                        <label for="szr-brand-name"><?php _e( 'Nom de la marque', 'shiftzoner-admin' ); ?> *</label>
                        <input type="text" id="szr-brand-name" name="name" required>
                    </div>

                    <div class="szr-form-group">
                        <label for="szr-brand-slug"><?php _e( 'Slug', 'shiftzoner-admin' ); ?></label>
                        <input type="text" id="szr-brand-slug" name="slug">
                        <p class="description"><?php _e( 'Laissez vide pour générer automatiquement', 'shiftzoner-admin' ); ?></p>
                    </div>

                    <div class="szr-form-group">
                        <label><?php _e( 'Logo', 'shiftzoner-admin' ); ?></label>
                        <div class="szr-logo-upload">
                            <input type="hidden" id="szr-brand-logo-id" name="logo_id" value="">
                            <div id="szr-logo-preview" class="szr-logo-preview">
                                <span class="dashicons dashicons-format-image"></span>
                            </div>
                            <button type="button" class="button" id="szr-upload-logo"><?php _e( 'Choisir un logo', 'shiftzoner-admin' ); ?></button>
                            <button type="button" class="button" id="szr-remove-logo" style="display:none;"><?php _e( 'Supprimer', 'shiftzoner-admin' ); ?></button>
                        </div>
                    </div>

                    <div class="szr-form-actions">
                        <button type="submit" class="button button-primary"><?php _e( 'Enregistrer', 'shiftzoner-admin' ); ?></button>
                        <button type="button" class="button szr-modal-cancel"><?php _e( 'Annuler', 'shiftzoner-admin' ); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add/Edit Model Modal -->
    <div id="szr-model-modal" class="szr-modal" style="display:none;">
        <div class="szr-modal-overlay"></div>
        <div class="szr-modal-content">
            <div class="szr-modal-header">
                <h2 id="szr-model-modal-title"><?php _e( 'Ajouter un Modèle', 'shiftzoner-admin' ); ?></h2>
                <button class="szr-modal-close">&times;</button>
            </div>
            <div class="szr-modal-body">
                <form id="szr-model-form">
                    <input type="hidden" id="szr-model-id" name="model_id" value="">
                    <input type="hidden" id="szr-model-brand-id" name="brand_id" value="">

                    <div class="szr-form-group">
                        <label for="szr-model-name"><?php _e( 'Nom du modèle', 'shiftzoner-admin' ); ?> *</label>
                        <input type="text" id="szr-model-name" name="name" required>
                    </div>

                    <div class="szr-form-group">
                        <label for="szr-model-slug"><?php _e( 'Slug', 'shiftzoner-admin' ); ?></label>
                        <input type="text" id="szr-model-slug" name="slug">
                        <p class="description"><?php _e( 'Laissez vide pour générer automatiquement', 'shiftzoner-admin' ); ?></p>
                    </div>

                    <div class="szr-form-actions">
                        <button type="submit" class="button button-primary"><?php _e( 'Enregistrer', 'shiftzoner-admin' ); ?></button>
                        <button type="button" class="button szr-modal-cancel"><?php _e( 'Annuler', 'shiftzoner-admin' ); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
