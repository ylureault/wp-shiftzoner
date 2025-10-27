<?php
/**
 * Template Name: Diagnostic Marques & Modèles
 * Description: Affiche la structure exacte de vos données pour debug
 *
 * @package ShiftZoneR
 */

// Security check
if ( ! current_user_can( 'manage_options' ) ) {
    wp_die( 'Vous n\'avez pas les permissions nécessaires.' );
}

get_header();
?>

<div class="diagnostic-page" style="padding: 40px 20px; font-family: monospace;">
    <div class="container" style="max-width: 1200px; margin: 0 auto;">
        <h1 style="margin-bottom: 30px; color: #1a1a1a;">🔍 Diagnostic Marques & Modèles</h1>

        <?php
        // Get all brands
        $brands = get_terms( array(
            'taxonomy'   => 'car_brand',
            'hide_empty' => false,
            'orderby'    => 'name',
            'order'      => 'ASC',
        ) );

        echo '<div style="background: #f5f5f5; padding: 20px; border-radius: 8px; margin-bottom: 30px;">';
        echo '<h2 style="margin: 0 0 15px;">📊 RÉSUMÉ</h2>';
        echo '<p><strong>Marques (car_brand):</strong> ' . count( $brands ) . '</p>';

        $all_models = get_terms( array(
            'taxonomy'   => 'car_model',
            'hide_empty' => false,
        ) );
        echo '<p><strong>Modèles (car_model):</strong> ' . count( $all_models ) . '</p>';

        $photos = wp_count_posts( 'car_photo' );
        echo '<p><strong>Photos publiées:</strong> ' . ( $photos->publish ?? 0 ) . '</p>';
        echo '</div>';

        // Analyze each brand
        foreach ( $brands as $brand ) :
            $logo_id = get_term_meta( $brand->term_id, '_szr_brand_logo_id', true );

            echo '<div style="background: #fff; border: 2px solid #e0e0e0; border-radius: 8px; padding: 20px; margin-bottom: 20px;">';
            echo '<h2 style="margin: 0 0 15px; color: #ff6b35;">🚗 ' . esc_html( $brand->name ) . '</h2>';

            echo '<div style="background: #f9f9f9; padding: 15px; border-radius: 4px; margin-bottom: 15px;">';
            echo '<p><strong>ID:</strong> ' . $brand->term_id . '</p>';
            echo '<p><strong>Slug:</strong> ' . $brand->slug . '</p>';
            echo '<p><strong>Count:</strong> ' . $brand->count . ' photos</p>';
            echo '<p><strong>Logo ID:</strong> ' . ( $logo_id ? $logo_id : 'Aucun' ) . '</p>';

            if ( $logo_id ) {
                $logo_url = wp_get_attachment_image_url( $logo_id, 'thumbnail' );
                if ( $logo_url ) {
                    echo '<p><strong>Logo:</strong> <img src="' . esc_url( $logo_url ) . '" style="width: 60px; height: 60px; object-fit: contain;"></p>';
                }
            }
            echo '</div>';

            // MÉTHODE 1: Hiérarchique
            echo '<h3 style="margin: 20px 0 10px; color: #333;">📁 MÉTHODE 1: Hiérarchique (car_model)</h3>';

            $parent_term = get_term_by( 'slug', $brand->slug, 'car_model' );
            if ( ! $parent_term ) {
                $parent_term = get_term_by( 'name', $brand->name, 'car_model' );
            }

            if ( $parent_term && ! is_wp_error( $parent_term ) ) {
                echo '<div style="background: #e8f5e9; padding: 10px; border-left: 4px solid #4caf50; margin-bottom: 15px;">';
                echo '<p>✅ Parent term trouvé: <strong>' . $parent_term->name . '</strong> (ID: ' . $parent_term->term_id . ')</p>';
                echo '</div>';

                // Get children
                $models = get_terms( array(
                    'taxonomy'   => 'car_model',
                    'hide_empty' => false,
                    'parent'     => $parent_term->term_id,
                    'orderby'    => 'name',
                    'order'      => 'ASC',
                ) );

                if ( ! is_wp_error( $models ) && ! empty( $models ) ) {
                    echo '<p><strong>Modèles trouvés (' . count( $models ) . '):</strong></p>';
                    echo '<ul style="margin: 10px 0; padding-left: 20px;">';
                    foreach ( $models as $model ) {
                        // Count photos for this model
                        $model_photos = get_posts( array(
                            'post_type'      => 'car_photo',
                            'posts_per_page' => -1,
                            'tax_query'      => array(
                                array(
                                    'taxonomy' => 'car_model',
                                    'terms'    => $model->term_id,
                                ),
                            ),
                            'fields'         => 'ids',
                        ) );

                        echo '<li>' . esc_html( $model->name ) . ' (ID: ' . $model->term_id . ', Count DB: ' . $model->count . ', Photos réelles: ' . count( $model_photos ) . ')</li>';
                    }
                    echo '</ul>';
                } else {
                    echo '<div style="background: #fff3cd; padding: 10px; border-left: 4px solid #ffc107; margin-bottom: 15px;">';
                    echo '<p>⚠️ Aucun modèle enfant trouvé</p>';
                    echo '</div>';
                }
            } else {
                echo '<div style="background: #ffebee; padding: 10px; border-left: 4px solid #f44336; margin-bottom: 15px;">';
                echo '<p>❌ Aucun parent term dans car_model</p>';
                echo '<p style="font-size: 12px; color: #666;">Le plugin ShiftZoneR Admin devrait créer automatiquement un parent term lors de la création d\'une marque.</p>';
                echo '</div>';
            }

            // MÉTHODE 2: Par meta
            echo '<h3 style="margin: 20px 0 10px; color: #333;">🔖 MÉTHODE 2: Par meta _szr_model_brand</h3>';

            $models_by_meta = array();
            foreach ( $all_models as $model ) {
                $model_brand_id = get_term_meta( $model->term_id, '_szr_model_brand', true );
                if ( intval( $model_brand_id ) === $brand->term_id ) {
                    $models_by_meta[] = $model;
                }
            }

            if ( ! empty( $models_by_meta ) ) {
                echo '<div style="background: #e8f5e9; padding: 10px; border-left: 4px solid #4caf50; margin-bottom: 15px;">';
                echo '<p>✅ Modèles trouvés par meta (' . count( $models_by_meta ) . '):</p>';
                echo '<ul style="margin: 10px 0; padding-left: 20px;">';
                foreach ( $models_by_meta as $model ) {
                    echo '<li>' . esc_html( $model->name ) . ' (ID: ' . $model->term_id . ')</li>';
                }
                echo '</ul>';
                echo '</div>';
            } else {
                echo '<div style="background: #fff3cd; padding: 10px; border-left: 4px solid #ffc107; margin-bottom: 15px;">';
                echo '<p>⚠️ Aucun modèle trouvé par meta</p>';
                echo '</div>';
            }

            // MÉTHODE 3: Photos assignées à cette marque
            echo '<h3 style="margin: 20px 0 10px; color: #333;">📸 MÉTHODE 3: Depuis les photos</h3>';

            $brand_photos = get_posts( array(
                'post_type'      => 'car_photo',
                'posts_per_page' => -1,
                'tax_query'      => array(
                    array(
                        'taxonomy' => 'car_brand',
                        'terms'    => $brand->term_id,
                    ),
                ),
                'fields'         => 'ids',
            ) );

            if ( ! empty( $brand_photos ) ) {
                echo '<div style="background: #e8f5e9; padding: 10px; border-left: 4px solid #4caf50; margin-bottom: 15px;">';
                echo '<p>✅ Photos de cette marque: ' . count( $brand_photos ) . '</p>';

                // Extract unique models from these photos
                $model_ids = array();
                foreach ( $brand_photos as $photo_id ) {
                    $photo_models = wp_get_post_terms( $photo_id, 'car_model', array( 'fields' => 'ids' ) );
                    $model_ids = array_merge( $model_ids, $photo_models );
                }
                $model_ids = array_unique( $model_ids );

                if ( ! empty( $model_ids ) ) {
                    echo '<p>Modèles uniques trouvés (' . count( $model_ids ) . '):</p>';
                    echo '<ul style="margin: 10px 0; padding-left: 20px;">';
                    foreach ( $model_ids as $model_id ) {
                        $model = get_term( $model_id, 'car_model' );
                        if ( $model && ! is_wp_error( $model ) ) {
                            echo '<li>' . esc_html( $model->name ) . ' (ID: ' . $model_id . ')</li>';
                        }
                    }
                    echo '</ul>';
                } else {
                    echo '<p>⚠️ Photos sans modèle assigné</p>';
                }
                echo '</div>';
            } else {
                echo '<div style="background: #fff3cd; padding: 10px; border-left: 4px solid #ffc107;">';
                echo '<p>⚠️ Aucune photo pour cette marque</p>';
                echo '</div>';
            }

            // RECOMMANDATION
            $has_hierarchical = isset( $models ) && ! empty( $models );
            $has_meta = ! empty( $models_by_meta );
            $has_photos = ! empty( $brand_photos );

            echo '<div style="background: #e3f2fd; padding: 15px; border-radius: 4px; margin-top: 20px;">';
            echo '<h3 style="margin: 0 0 10px; color: #1976d2;">💡 RECOMMANDATION</h3>';

            if ( $has_hierarchical ) {
                echo '<p style="color: #2e7d32;">✅ Structure hiérarchique OK - Les modèles devraient s\'afficher</p>';
            } elseif ( $has_meta ) {
                echo '<p style="color: #f57c00;">⚠️ Structure meta OK mais pas hiérarchique - Créer des parents dans car_model</p>';
            } elseif ( $has_photos ) {
                echo '<p style="color: #f57c00;">⚠️ Photos existent mais modèles mal organisés - Réorganiser via le plugin admin</p>';
            } else {
                echo '<p style="color: #d32f2f;">❌ Aucune donnée - Créer des modèles via le plugin ShiftZoneR Admin</p>';
            }
            echo '</div>';

            echo '</div>'; // End brand card
        endforeach;

        // SOLUTION
        echo '<div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; padding: 30px; border-radius: 8px; margin-top: 30px;">';
        echo '<h2 style="margin: 0 0 15px;">🔧 COMMENT CORRIGER</h2>';
        echo '<ol style="margin: 0; padding-left: 20px; line-height: 1.8;">';
        echo '<li><strong>Activer le plugin ShiftZoneR Admin</strong> (Extensions → Activer "ShiftZoneR Admin")</li>';
        echo '<li><strong>Aller dans ShiftZoneR → Marques & Modèles</strong> dans le menu admin</li>';
        echo '<li><strong>Pour chaque marque:</strong> Cliquer dessus et ajouter des modèles</li>';
        echo '<li><strong>Le plugin crée automatiquement</strong> la structure hiérarchique correcte</li>';
        echo '<li><strong>Tester la page</strong> /marques-et-modeles/ (utiliser template V2)</li>';
        echo '</ol>';
        echo '<p style="margin-top: 20px; font-size: 14px; opacity: 0.9;">Le plugin gère automatiquement la création des parent terms dans car_model et les relations hiérarchiques.</p>';
        echo '</div>';
        ?>

    </div>
</div>

<?php
get_footer();
