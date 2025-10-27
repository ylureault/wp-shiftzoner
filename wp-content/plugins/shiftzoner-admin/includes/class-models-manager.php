<?php
/**
 * Models Manager Class
 *
 * Handles model-specific operations
 *
 * @package ShiftZoneRAdmin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SZR_Models_Manager {

    public static function init() {
        add_action( 'wp_ajax_szr_admin_bulk_delete_models', array( __CLASS__, 'ajax_bulk_delete_models' ) );
        add_action( 'wp_ajax_szr_admin_bulk_move_models', array( __CLASS__, 'ajax_bulk_move_models' ) );
        add_action( 'wp_ajax_szr_admin_import_models', array( __CLASS__, 'ajax_import_models' ) );
    }

    public static function render_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'Vous n\'avez pas les permissions nécessaires.', 'shiftzoner-admin' ) );
        }

        // Get all models
        $models = get_terms( array(
            'taxonomy'   => SZR_TAX_MODEL,
            'hide_empty' => false,
            'orderby'    => 'name',
            'order'      => 'ASC',
            'parent'     => 0, // Only parent terms
        ) );

        include SZR_ADMIN_PATH . 'views/models-manager.php';
    }

    /**
     * AJAX: Bulk delete models
     */
    public static function ajax_bulk_delete_models() {
        check_ajax_referer( 'szr_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Permission refusée' ) );
        }

        $model_ids = isset( $_POST['model_ids'] ) ? array_map( 'intval', $_POST['model_ids'] ) : array();

        if ( empty( $model_ids ) ) {
            wp_send_json_error( array( 'message' => 'Aucun modèle sélectionné' ) );
        }

        $deleted = 0;
        foreach ( $model_ids as $model_id ) {
            $result = wp_delete_term( $model_id, SZR_TAX_MODEL );
            if ( ! is_wp_error( $result ) ) {
                $deleted++;
            }
        }

        wp_send_json_success( array(
            'message' => sprintf( '%d modèle(s) supprimé(s) avec succès', $deleted ),
            'deleted' => $deleted,
        ) );
    }

    /**
     * AJAX: Bulk move models
     */
    public static function ajax_bulk_move_models() {
        check_ajax_referer( 'szr_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Permission refusée' ) );
        }

        $model_ids = isset( $_POST['model_ids'] ) ? array_map( 'intval', $_POST['model_ids'] ) : array();
        $new_brand_id = intval( $_POST['new_brand_id'] ?? 0 );

        if ( empty( $model_ids ) || $new_brand_id <= 0 ) {
            wp_send_json_error( array( 'message' => 'Données invalides' ) );
        }

        // Get new brand
        $new_brand = get_term( $new_brand_id, SZR_TAX_BRAND );
        if ( is_wp_error( $new_brand ) ) {
            wp_send_json_error( array( 'message' => 'Marque non trouvée' ) );
        }

        // Find parent term
        $new_parent = get_term_by( 'slug', $new_brand->slug, SZR_TAX_MODEL );
        if ( ! $new_parent ) {
            $parent_result = wp_insert_term( $new_brand->name, SZR_TAX_MODEL, array(
                'slug' => $new_brand->slug,
            ) );
            if ( is_wp_error( $parent_result ) ) {
                wp_send_json_error( array( 'message' => $parent_result->get_error_message() ) );
            }
            $new_parent = get_term( $parent_result['term_id'], SZR_TAX_MODEL );
        }

        $moved = 0;
        foreach ( $model_ids as $model_id ) {
            $result = wp_update_term( $model_id, SZR_TAX_MODEL, array(
                'parent' => $new_parent->term_id,
            ) );
            if ( ! is_wp_error( $result ) ) {
                update_term_meta( $model_id, SZR_META_MODEL_BRAND, $new_brand_id );
                $moved++;
            }
        }

        wp_send_json_success( array(
            'message' => sprintf( '%d modèle(s) déplacé(s) avec succès', $moved ),
            'moved'   => $moved,
        ) );
    }

    /**
     * AJAX: Import models from CSV
     */
    public static function ajax_import_models() {
        check_ajax_referer( 'szr_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Permission refusée' ) );
        }

        if ( empty( $_FILES['file'] ) ) {
            wp_send_json_error( array( 'message' => 'Aucun fichier fourni' ) );
        }

        $file = $_FILES['file'];
        $file_content = file_get_contents( $file['tmp_name'] );
        $lines = explode( "\n", $file_content );

        $imported = 0;
        $errors = array();

        foreach ( $lines as $line ) {
            $line = trim( $line );
            if ( empty( $line ) ) {
                continue;
            }

            $parts = str_getcsv( $line );
            if ( count( $parts ) < 2 ) {
                $errors[] = "Ligne invalide: $line";
                continue;
            }

            $brand_name = trim( $parts[0] );
            $model_name = trim( $parts[1] );

            // Find brand
            $brand = get_term_by( 'name', $brand_name, SZR_TAX_BRAND );
            if ( ! $brand ) {
                $errors[] = "Marque non trouvée: $brand_name";
                continue;
            }

            // Find parent in car_model
            $parent_term = get_term_by( 'slug', $brand->slug, SZR_TAX_MODEL );
            if ( ! $parent_term ) {
                $parent_result = wp_insert_term( $brand->name, SZR_TAX_MODEL, array(
                    'slug' => $brand->slug,
                ) );
                if ( is_wp_error( $parent_result ) ) {
                    $errors[] = "Erreur création parent pour $brand_name: " . $parent_result->get_error_message();
                    continue;
                }
                $parent_term = get_term( $parent_result['term_id'], SZR_TAX_MODEL );
            }

            // Create model
            $model = wp_insert_term( $model_name, SZR_TAX_MODEL, array(
                'slug'   => sanitize_title( $model_name ),
                'parent' => $parent_term->term_id,
            ) );

            if ( is_wp_error( $model ) ) {
                $errors[] = "Erreur création modèle $model_name: " . $model->get_error_message();
                continue;
            }

            update_term_meta( $model['term_id'], SZR_META_MODEL_BRAND, $brand->term_id );
            $imported++;
        }

        wp_send_json_success( array(
            'message'  => sprintf( '%d modèle(s) importé(s) avec succès', $imported ),
            'imported' => $imported,
            'errors'   => $errors,
        ) );
    }
}

SZR_Models_Manager::init();
