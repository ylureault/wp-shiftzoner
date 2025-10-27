<?php
/**
 * Brands Manager Class
 *
 * Handles all brand and model management operations
 *
 * @package ShiftZoneRAdmin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SZR_Brands_Manager {

    public static function init() {
        add_action( 'wp_ajax_szr_admin_add_brand', array( __CLASS__, 'ajax_add_brand' ) );
        add_action( 'wp_ajax_szr_admin_edit_brand', array( __CLASS__, 'ajax_edit_brand' ) );
        add_action( 'wp_ajax_szr_admin_delete_brand', array( __CLASS__, 'ajax_delete_brand' ) );
        add_action( 'wp_ajax_szr_admin_add_model', array( __CLASS__, 'ajax_add_model' ) );
        add_action( 'wp_ajax_szr_admin_edit_model', array( __CLASS__, 'ajax_edit_model' ) );
        add_action( 'wp_ajax_szr_admin_delete_model', array( __CLASS__, 'ajax_delete_model' ) );
        add_action( 'wp_ajax_szr_admin_move_model', array( __CLASS__, 'ajax_move_model' ) );
        add_action( 'wp_ajax_szr_admin_get_models', array( __CLASS__, 'ajax_get_models' ) );
    }

    public static function render_page() {
        // Check permissions
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'Vous n\'avez pas les permissions nécessaires.', 'shiftzoner-admin' ) );
        }

        // Get all brands
        $brands = get_terms( array(
            'taxonomy'   => SZR_TAX_BRAND,
            'hide_empty' => false,
            'orderby'    => 'name',
            'order'      => 'ASC',
        ) );

        // Get stats
        $total_brands = count( $brands );
        $total_models = wp_count_terms( array(
            'taxonomy'   => SZR_TAX_MODEL,
            'hide_empty' => false,
        ) );
        $total_photos = wp_count_posts( 'car_photo' );
        $published_photos = $total_photos->publish ?? 0;

        include SZR_ADMIN_PATH . 'views/brands-manager.php';
    }

    /**
     * AJAX: Add new brand
     */
    public static function ajax_add_brand() {
        check_ajax_referer( 'szr_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Permission refusée' ) );
        }

        $name = sanitize_text_field( $_POST['name'] ?? '' );
        $slug = sanitize_title( $_POST['slug'] ?? $name );
        $logo_id = intval( $_POST['logo_id'] ?? 0 );

        if ( empty( $name ) ) {
            wp_send_json_error( array( 'message' => 'Le nom de la marque est requis' ) );
        }

        // Create brand in car_brand taxonomy
        $brand = wp_insert_term( $name, SZR_TAX_BRAND, array(
            'slug' => $slug,
        ) );

        if ( is_wp_error( $brand ) ) {
            wp_send_json_error( array( 'message' => $brand->get_error_message() ) );
        }

        $brand_id = $brand['term_id'];

        // Save logo
        if ( $logo_id > 0 ) {
            update_term_meta( $brand_id, SZR_META_BRAND_LOGO, $logo_id );
        }

        // Create parent term in car_model taxonomy (for hierarchical models)
        wp_insert_term( $name, SZR_TAX_MODEL, array(
            'slug' => $slug,
        ) );

        // Get the created brand with all data
        $brand_term = get_term( $brand_id, SZR_TAX_BRAND );
        $logo_url = $logo_id ? wp_get_attachment_image_url( $logo_id, 'medium' ) : '';

        wp_send_json_success( array(
            'message' => 'Marque créée avec succès',
            'brand'   => array(
                'id'       => $brand_id,
                'name'     => $brand_term->name,
                'slug'     => $brand_term->slug,
                'count'    => $brand_term->count,
                'logo_url' => $logo_url,
            ),
        ) );
    }

    /**
     * AJAX: Edit brand
     */
    public static function ajax_edit_brand() {
        check_ajax_referer( 'szr_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Permission refusée' ) );
        }

        $brand_id = intval( $_POST['brand_id'] ?? 0 );
        $name = sanitize_text_field( $_POST['name'] ?? '' );
        $slug = sanitize_title( $_POST['slug'] ?? $name );
        $logo_id = intval( $_POST['logo_id'] ?? 0 );

        if ( $brand_id <= 0 || empty( $name ) ) {
            wp_send_json_error( array( 'message' => 'Données invalides' ) );
        }

        // Update brand
        $result = wp_update_term( $brand_id, SZR_TAX_BRAND, array(
            'name' => $name,
            'slug' => $slug,
        ) );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 'message' => $result->get_error_message() ) );
        }

        // Update logo
        if ( $logo_id > 0 ) {
            update_term_meta( $brand_id, SZR_META_BRAND_LOGO, $logo_id );
        } else {
            delete_term_meta( $brand_id, SZR_META_BRAND_LOGO );
        }

        // Update corresponding parent term in car_model
        $parent_term = get_term_by( 'slug', $slug, SZR_TAX_MODEL );
        if ( $parent_term ) {
            wp_update_term( $parent_term->term_id, SZR_TAX_MODEL, array(
                'name' => $name,
                'slug' => $slug,
            ) );
        }

        $brand_term = get_term( $brand_id, SZR_TAX_BRAND );
        $logo_url = $logo_id ? wp_get_attachment_image_url( $logo_id, 'medium' ) : '';

        wp_send_json_success( array(
            'message' => 'Marque mise à jour avec succès',
            'brand'   => array(
                'id'       => $brand_id,
                'name'     => $brand_term->name,
                'slug'     => $brand_term->slug,
                'count'    => $brand_term->count,
                'logo_url' => $logo_url,
            ),
        ) );
    }

    /**
     * AJAX: Delete brand
     */
    public static function ajax_delete_brand() {
        check_ajax_referer( 'szr_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Permission refusée' ) );
        }

        $brand_id = intval( $_POST['brand_id'] ?? 0 );

        if ( $brand_id <= 0 ) {
            wp_send_json_error( array( 'message' => 'ID de marque invalide' ) );
        }

        $brand = get_term( $brand_id, SZR_TAX_BRAND );
        if ( is_wp_error( $brand ) ) {
            wp_send_json_error( array( 'message' => 'Marque non trouvée' ) );
        }

        // Delete parent term in car_model
        $parent_term = get_term_by( 'slug', $brand->slug, SZR_TAX_MODEL );
        if ( $parent_term ) {
            // Get all child models
            $models = get_terms( array(
                'taxonomy'   => SZR_TAX_MODEL,
                'hide_empty' => false,
                'parent'     => $parent_term->term_id,
            ) );

            // Delete all models
            foreach ( $models as $model ) {
                wp_delete_term( $model->term_id, SZR_TAX_MODEL );
            }

            // Delete parent
            wp_delete_term( $parent_term->term_id, SZR_TAX_MODEL );
        }

        // Delete brand
        $result = wp_delete_term( $brand_id, SZR_TAX_BRAND );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 'message' => $result->get_error_message() ) );
        }

        wp_send_json_success( array(
            'message' => 'Marque supprimée avec succès',
        ) );
    }

    /**
     * AJAX: Add new model
     */
    public static function ajax_add_model() {
        check_ajax_referer( 'szr_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Permission refusée' ) );
        }

        $brand_id = intval( $_POST['brand_id'] ?? 0 );
        $name = sanitize_text_field( $_POST['name'] ?? '' );
        $slug = sanitize_title( $_POST['slug'] ?? $name );

        if ( $brand_id <= 0 || empty( $name ) ) {
            wp_send_json_error( array( 'message' => 'Données invalides' ) );
        }

        // Get brand
        $brand = get_term( $brand_id, SZR_TAX_BRAND );
        if ( is_wp_error( $brand ) ) {
            wp_send_json_error( array( 'message' => 'Marque non trouvée' ) );
        }

        // Find parent term in car_model
        $parent_term = get_term_by( 'slug', $brand->slug, SZR_TAX_MODEL );
        if ( ! $parent_term ) {
            // Create parent if doesn't exist
            $parent_result = wp_insert_term( $brand->name, SZR_TAX_MODEL, array(
                'slug' => $brand->slug,
            ) );
            if ( is_wp_error( $parent_result ) ) {
                wp_send_json_error( array( 'message' => $parent_result->get_error_message() ) );
            }
            $parent_term = get_term( $parent_result['term_id'], SZR_TAX_MODEL );
        }

        // Create model as child
        $model = wp_insert_term( $name, SZR_TAX_MODEL, array(
            'slug'   => $slug,
            'parent' => $parent_term->term_id,
        ) );

        if ( is_wp_error( $model ) ) {
            wp_send_json_error( array( 'message' => $model->get_error_message() ) );
        }

        $model_id = $model['term_id'];

        // Save brand reference
        update_term_meta( $model_id, SZR_META_MODEL_BRAND, $brand_id );

        $model_term = get_term( $model_id, SZR_TAX_MODEL );

        wp_send_json_success( array(
            'message' => 'Modèle créé avec succès',
            'model'   => array(
                'id'    => $model_id,
                'name'  => $model_term->name,
                'slug'  => $model_term->slug,
                'count' => $model_term->count,
            ),
        ) );
    }

    /**
     * AJAX: Edit model
     */
    public static function ajax_edit_model() {
        check_ajax_referer( 'szr_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Permission refusée' ) );
        }

        $model_id = intval( $_POST['model_id'] ?? 0 );
        $name = sanitize_text_field( $_POST['name'] ?? '' );
        $slug = sanitize_title( $_POST['slug'] ?? $name );

        if ( $model_id <= 0 || empty( $name ) ) {
            wp_send_json_error( array( 'message' => 'Données invalides' ) );
        }

        $result = wp_update_term( $model_id, SZR_TAX_MODEL, array(
            'name' => $name,
            'slug' => $slug,
        ) );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 'message' => $result->get_error_message() ) );
        }

        $model_term = get_term( $model_id, SZR_TAX_MODEL );

        wp_send_json_success( array(
            'message' => 'Modèle mis à jour avec succès',
            'model'   => array(
                'id'    => $model_id,
                'name'  => $model_term->name,
                'slug'  => $model_term->slug,
                'count' => $model_term->count,
            ),
        ) );
    }

    /**
     * AJAX: Delete model
     */
    public static function ajax_delete_model() {
        check_ajax_referer( 'szr_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Permission refusée' ) );
        }

        $model_id = intval( $_POST['model_id'] ?? 0 );

        if ( $model_id <= 0 ) {
            wp_send_json_error( array( 'message' => 'ID de modèle invalide' ) );
        }

        $result = wp_delete_term( $model_id, SZR_TAX_MODEL );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 'message' => $result->get_error_message() ) );
        }

        wp_send_json_success( array(
            'message' => 'Modèle supprimé avec succès',
        ) );
    }

    /**
     * AJAX: Move model to another brand
     */
    public static function ajax_move_model() {
        check_ajax_referer( 'szr_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Permission refusée' ) );
        }

        $model_id = intval( $_POST['model_id'] ?? 0 );
        $new_brand_id = intval( $_POST['new_brand_id'] ?? 0 );

        if ( $model_id <= 0 || $new_brand_id <= 0 ) {
            wp_send_json_error( array( 'message' => 'Données invalides' ) );
        }

        // Get new brand
        $new_brand = get_term( $new_brand_id, SZR_TAX_BRAND );
        if ( is_wp_error( $new_brand ) ) {
            wp_send_json_error( array( 'message' => 'Marque non trouvée' ) );
        }

        // Find parent term in car_model for new brand
        $new_parent = get_term_by( 'slug', $new_brand->slug, SZR_TAX_MODEL );
        if ( ! $new_parent ) {
            // Create parent if doesn't exist
            $parent_result = wp_insert_term( $new_brand->name, SZR_TAX_MODEL, array(
                'slug' => $new_brand->slug,
            ) );
            if ( is_wp_error( $parent_result ) ) {
                wp_send_json_error( array( 'message' => $parent_result->get_error_message() ) );
            }
            $new_parent = get_term( $parent_result['term_id'], SZR_TAX_MODEL );
        }

        // Update model parent
        $result = wp_update_term( $model_id, SZR_TAX_MODEL, array(
            'parent' => $new_parent->term_id,
        ) );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 'message' => $result->get_error_message() ) );
        }

        // Update brand reference
        update_term_meta( $model_id, SZR_META_MODEL_BRAND, $new_brand_id );

        wp_send_json_success( array(
            'message' => 'Modèle déplacé avec succès',
        ) );
    }

    /**
     * AJAX: Get models for a brand
     */
    public static function ajax_get_models() {
        check_ajax_referer( 'szr_admin_nonce', 'nonce' );

        $brand_id = intval( $_POST['brand_id'] ?? 0 );

        if ( $brand_id <= 0 ) {
            wp_send_json_error( array( 'message' => 'ID de marque invalide' ) );
        }

        $brand = get_term( $brand_id, SZR_TAX_BRAND );
        if ( is_wp_error( $brand ) ) {
            wp_send_json_error( array( 'message' => 'Marque non trouvée' ) );
        }

        // Find parent term in car_model
        $parent_term = get_term_by( 'slug', $brand->slug, SZR_TAX_MODEL );
        if ( ! $parent_term ) {
            $parent_term = get_term_by( 'name', $brand->name, SZR_TAX_MODEL );
        }

        $models_data = array();

        if ( $parent_term && ! is_wp_error( $parent_term ) ) {
            $models = get_terms( array(
                'taxonomy'   => SZR_TAX_MODEL,
                'hide_empty' => false,
                'parent'     => $parent_term->term_id,
                'orderby'    => 'name',
                'order'      => 'ASC',
            ) );

            foreach ( $models as $model ) {
                $models_data[] = array(
                    'id'    => $model->term_id,
                    'name'  => $model->name,
                    'slug'  => $model->slug,
                    'count' => $model->count,
                );
            }
        }

        wp_send_json_success( array(
            'models' => $models_data,
        ) );
    }
}

SZR_Brands_Manager::init();
