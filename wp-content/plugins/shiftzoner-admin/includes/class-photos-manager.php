<?php
/**
 * Photos Manager Class
 *
 * Handles photo management and moderation
 *
 * @package ShiftZoneRAdmin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SZR_Photos_Manager {

    public static function init() {
        add_action( 'wp_ajax_szr_admin_get_photos', array( __CLASS__, 'ajax_get_photos' ) );
        add_action( 'wp_ajax_szr_admin_approve_photo', array( __CLASS__, 'ajax_approve_photo' ) );
        add_action( 'wp_ajax_szr_admin_reject_photo', array( __CLASS__, 'ajax_reject_photo' ) );
        add_action( 'wp_ajax_szr_admin_delete_photo', array( __CLASS__, 'ajax_delete_photo' ) );
        add_action( 'wp_ajax_szr_admin_bulk_approve', array( __CLASS__, 'ajax_bulk_approve' ) );
        add_action( 'wp_ajax_szr_admin_bulk_delete', array( __CLASS__, 'ajax_bulk_delete' ) );
    }

    public static function render_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'Vous n\'avez pas les permissions nécessaires.', 'shiftzoner-admin' ) );
        }

        // Get stats
        $pending_count = wp_count_posts( 'car_photo' )->pending ?? 0;
        $published_count = wp_count_posts( 'car_photo' )->publish ?? 0;
        $draft_count = wp_count_posts( 'car_photo' )->draft ?? 0;

        include SZR_ADMIN_PATH . 'views/photos-manager.php';
    }

    /**
     * AJAX: Get photos
     */
    public static function ajax_get_photos() {
        check_ajax_referer( 'szr_admin_nonce', 'nonce' );

        $status = sanitize_text_field( $_POST['status'] ?? 'pending' );
        $page = intval( $_POST['page'] ?? 1 );
        $per_page = 20;

        $args = array(
            'post_type'      => 'car_photo',
            'post_status'    => $status,
            'posts_per_page' => $per_page,
            'paged'          => $page,
            'orderby'        => 'date',
            'order'          => 'DESC',
        );

        $query = new WP_Query( $args );
        $photos = array();

        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $post_id = get_the_ID();

                $brand_terms = wp_get_post_terms( $post_id, SZR_TAX_BRAND );
                $model_terms = wp_get_post_terms( $post_id, SZR_TAX_MODEL );

                $photos[] = array(
                    'id'          => $post_id,
                    'title'       => get_the_title(),
                    'image'       => get_the_post_thumbnail_url( $post_id, 'medium' ),
                    'author'      => get_the_author(),
                    'date'        => get_the_date(),
                    'brand'       => ! empty( $brand_terms ) ? $brand_terms[0]->name : '',
                    'model'       => ! empty( $model_terms ) ? $model_terms[0]->name : '',
                    'description' => get_the_excerpt(),
                );
            }
        }
        wp_reset_postdata();

        wp_send_json_success( array(
            'photos'     => $photos,
            'total'      => $query->found_posts,
            'pages'      => $query->max_num_pages,
            'current'    => $page,
        ) );
    }

    /**
     * AJAX: Approve photo
     */
    public static function ajax_approve_photo() {
        check_ajax_referer( 'szr_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Permission refusée' ) );
        }

        $photo_id = intval( $_POST['photo_id'] ?? 0 );

        if ( $photo_id <= 0 ) {
            wp_send_json_error( array( 'message' => 'ID de photo invalide' ) );
        }

        $result = wp_update_post( array(
            'ID'          => $photo_id,
            'post_status' => 'publish',
        ) );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 'message' => $result->get_error_message() ) );
        }

        wp_send_json_success( array(
            'message' => 'Photo approuvée avec succès',
        ) );
    }

    /**
     * AJAX: Reject photo
     */
    public static function ajax_reject_photo() {
        check_ajax_referer( 'szr_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Permission refusée' ) );
        }

        $photo_id = intval( $_POST['photo_id'] ?? 0 );

        if ( $photo_id <= 0 ) {
            wp_send_json_error( array( 'message' => 'ID de photo invalide' ) );
        }

        $result = wp_update_post( array(
            'ID'          => $photo_id,
            'post_status' => 'draft',
        ) );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 'message' => $result->get_error_message() ) );
        }

        wp_send_json_success( array(
            'message' => 'Photo rejetée avec succès',
        ) );
    }

    /**
     * AJAX: Delete photo
     */
    public static function ajax_delete_photo() {
        check_ajax_referer( 'szr_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Permission refusée' ) );
        }

        $photo_id = intval( $_POST['photo_id'] ?? 0 );

        if ( $photo_id <= 0 ) {
            wp_send_json_error( array( 'message' => 'ID de photo invalide' ) );
        }

        $result = wp_delete_post( $photo_id, true );

        if ( ! $result ) {
            wp_send_json_error( array( 'message' => 'Erreur lors de la suppression' ) );
        }

        wp_send_json_success( array(
            'message' => 'Photo supprimée avec succès',
        ) );
    }

    /**
     * AJAX: Bulk approve photos
     */
    public static function ajax_bulk_approve() {
        check_ajax_referer( 'szr_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Permission refusée' ) );
        }

        $photo_ids = isset( $_POST['photo_ids'] ) ? array_map( 'intval', $_POST['photo_ids'] ) : array();

        if ( empty( $photo_ids ) ) {
            wp_send_json_error( array( 'message' => 'Aucune photo sélectionnée' ) );
        }

        $approved = 0;
        foreach ( $photo_ids as $photo_id ) {
            $result = wp_update_post( array(
                'ID'          => $photo_id,
                'post_status' => 'publish',
            ) );
            if ( ! is_wp_error( $result ) ) {
                $approved++;
            }
        }

        wp_send_json_success( array(
            'message'  => sprintf( '%d photo(s) approuvée(s) avec succès', $approved ),
            'approved' => $approved,
        ) );
    }

    /**
     * AJAX: Bulk delete photos
     */
    public static function ajax_bulk_delete() {
        check_ajax_referer( 'szr_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Permission refusée' ) );
        }

        $photo_ids = isset( $_POST['photo_ids'] ) ? array_map( 'intval', $_POST['photo_ids'] ) : array();

        if ( empty( $photo_ids ) ) {
            wp_send_json_error( array( 'message' => 'Aucune photo sélectionnée' ) );
        }

        $deleted = 0;
        foreach ( $photo_ids as $photo_id ) {
            $result = wp_delete_post( $photo_id, true );
            if ( $result ) {
                $deleted++;
            }
        }

        wp_send_json_success( array(
            'message' => sprintf( '%d photo(s) supprimée(s) avec succès', $deleted ),
            'deleted' => $deleted,
        ) );
    }
}

SZR_Photos_Manager::init();
