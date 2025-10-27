<?php
/**
 * Settings View
 *
 * @package ShiftZoneRAdmin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="wrap szr-admin-wrap">
    <h1 class="szr-admin-title">
        <span class="dashicons dashicons-admin-settings"></span>
        <?php _e( 'Paramètres ShiftZoneR', 'shiftzoner-admin' ); ?>
    </h1>

    <form id="szr-settings-form" class="szr-settings-form">
        <div class="szr-settings-container">
            <!-- Upload Settings -->
            <div class="szr-settings-card">
                <h2><?php _e( 'Paramètres d\'Upload', 'shiftzoner-admin' ); ?></h2>

                <div class="szr-form-group">
                    <label for="upload_max_size"><?php _e( 'Taille maximale (Mo)', 'shiftzoner-admin' ); ?></label>
                    <input type="number" id="upload_max_size" name="upload_max_size" value="<?php echo esc_attr( $settings['upload_max_size'] ); ?>" min="1" max="50">
                </div>

                <div class="szr-form-group">
                    <label for="upload_allowed_types"><?php _e( 'Types de fichiers autorisés', 'shiftzoner-admin' ); ?></label>
                    <input type="text" id="upload_allowed_types" name="upload_allowed_types" value="<?php echo esc_attr( $settings['upload_allowed_types'] ); ?>">
                    <p class="description"><?php _e( 'Séparés par des virgules (ex: jpg,jpeg,png)', 'shiftzoner-admin' ); ?></p>
                </div>

                <div class="szr-form-group">
                    <label>
                        <input type="checkbox" name="upload_require_moderation" value="1" <?php checked( $settings['upload_require_moderation'], '1' ); ?>>
                        <?php _e( 'Modération requise pour les nouvelles photos', 'shiftzoner-admin' ); ?>
                    </label>
                </div>

                <div class="szr-form-group">
                    <label for="upload_min_dimensions"><?php _e( 'Dimensions minimales', 'shiftzoner-admin' ); ?></label>
                    <input type="text" id="upload_min_dimensions" name="upload_min_dimensions" value="<?php echo esc_attr( $settings['upload_min_dimensions'] ); ?>">
                    <p class="description"><?php _e( 'Format: 800x600', 'shiftzoner-admin' ); ?></p>
                </div>
            </div>

            <!-- Map Settings -->
            <div class="szr-settings-card">
                <h2><?php _e( 'Paramètres de la Carte', 'shiftzoner-admin' ); ?></h2>

                <div class="szr-form-group">
                    <label for="map_default_zoom"><?php _e( 'Zoom par défaut', 'shiftzoner-admin' ); ?></label>
                    <input type="number" id="map_default_zoom" name="map_default_zoom" value="<?php echo esc_attr( $settings['map_default_zoom'] ); ?>" min="1" max="18">
                </div>

                <div class="szr-form-group">
                    <label for="map_default_center_lat"><?php _e( 'Latitude du centre', 'shiftzoner-admin' ); ?></label>
                    <input type="text" id="map_default_center_lat" name="map_default_center_lat" value="<?php echo esc_attr( $settings['map_default_center_lat'] ); ?>">
                </div>

                <div class="szr-form-group">
                    <label for="map_default_center_lng"><?php _e( 'Longitude du centre', 'shiftzoner-admin' ); ?></label>
                    <input type="text" id="map_default_center_lng" name="map_default_center_lng" value="<?php echo esc_attr( $settings['map_default_center_lng'] ); ?>">
                </div>
            </div>

            <!-- Theme Settings -->
            <div class="szr-settings-card">
                <h2><?php _e( 'Paramètres du Thème', 'shiftzoner-admin' ); ?></h2>

                <div class="szr-form-group">
                    <label for="theme_primary_color"><?php _e( 'Couleur primaire', 'shiftzoner-admin' ); ?></label>
                    <input type="color" id="theme_primary_color" name="theme_primary_color" value="<?php echo esc_attr( $settings['theme_primary_color'] ); ?>">
                </div>

                <div class="szr-form-group">
                    <label for="theme_secondary_color"><?php _e( 'Couleur secondaire', 'shiftzoner-admin' ); ?></label>
                    <input type="color" id="theme_secondary_color" name="theme_secondary_color" value="<?php echo esc_attr( $settings['theme_secondary_color'] ); ?>">
                </div>

                <div class="szr-form-group">
                    <label for="theme_footer_text"><?php _e( 'Texte du pied de page', 'shiftzoner-admin' ); ?></label>
                    <input type="text" id="theme_footer_text" name="theme_footer_text" value="<?php echo esc_attr( $settings['theme_footer_text'] ); ?>">
                </div>
            </div>

            <!-- Community Settings -->
            <div class="szr-settings-card">
                <h2><?php _e( 'Paramètres de la Communauté', 'shiftzoner-admin' ); ?></h2>

                <div class="szr-form-group">
                    <label>
                        <input type="checkbox" name="community_allow_registration" value="1" <?php checked( $settings['community_allow_registration'], '1' ); ?>>
                        <?php _e( 'Autoriser les inscriptions', 'shiftzoner-admin' ); ?>
                    </label>
                </div>

                <div class="szr-form-group">
                    <label>
                        <input type="checkbox" name="community_require_approval" value="1" <?php checked( $settings['community_require_approval'], '1' ); ?>>
                        <?php _e( 'Approbation requise pour les nouveaux membres', 'shiftzoner-admin' ); ?>
                    </label>
                </div>

                <div class="szr-form-group">
                    <label>
                        <input type="checkbox" name="community_points_enabled" value="1" <?php checked( $settings['community_points_enabled'], '1' ); ?>>
                        <?php _e( 'Activer le système de points', 'shiftzoner-admin' ); ?>
                    </label>
                </div>
            </div>
        </div>

        <div class="szr-form-actions">
            <button type="submit" class="button button-primary button-large"><?php _e( 'Enregistrer les Paramètres', 'shiftzoner-admin' ); ?></button>
        </div>
    </form>
</div>
