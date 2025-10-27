<?php
/**
 * Community Manager Class
 *
 * Handles user and community management
 *
 * @package ShiftZoneRAdmin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SZR_Community {

    public static function init() {
        add_action( 'wp_ajax_szr_admin_get_users', array( __CLASS__, 'ajax_get_users' ) );
        add_action( 'wp_ajax_szr_admin_get_user_stats', array( __CLASS__, 'ajax_get_user_stats' ) );
        add_action( 'wp_ajax_szr_admin_ban_user', array( __CLASS__, 'ajax_ban_user' ) );
        add_action( 'wp_ajax_szr_admin_unban_user', array( __CLASS__, 'ajax_unban_user' ) );
    }

    public static function render_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'Vous n\'avez pas les permissions nécessaires.', 'shiftzoner-admin' ) );
        }

        // Get user stats
        $total_users = count_users();
        $contributor_count = 0;
        if ( isset( $total_users['avail_roles']['contributor'] ) ) {
            $contributor_count = $total_users['avail_roles']['contributor'];
        }

        // Get top contributors
        $top_contributors = self::get_top_contributors( 10 );

        include SZR_ADMIN_PATH . 'views/community.php';
    }

    /**
     * Get top contributors
     */
    public static function get_top_contributors( $limit = 10 ) {
        global $wpdb;

        $results = $wpdb->get_results( $wpdb->prepare(
            "SELECT p.post_author, COUNT(*) as photo_count, u.display_name, u.user_email
            FROM {$wpdb->posts} p
            INNER JOIN {$wpdb->users} u ON p.post_author = u.ID
            WHERE p.post_type = 'car_photo'
            AND p.post_status = 'publish'
            GROUP BY p.post_author
            ORDER BY photo_count DESC
            LIMIT %d",
            $limit
        ) );

        return $results;
    }

    /**
     * AJAX: Get users
     */
    public static function ajax_get_users() {
        check_ajax_referer( 'szr_admin_nonce', 'nonce' );

        $page = intval( $_POST['page'] ?? 1 );
        $per_page = 20;
        $search = sanitize_text_field( $_POST['search'] ?? '' );

        $args = array(
            'number' => $per_page,
            'paged'  => $page,
            'orderby' => 'registered',
            'order'   => 'DESC',
        );

        if ( ! empty( $search ) ) {
            $args['search'] = '*' . $search . '*';
            $args['search_columns'] = array( 'user_login', 'user_email', 'display_name' );
        }

        $user_query = new WP_User_Query( $args );
        $users = $user_query->get_results();
        $users_data = array();

        foreach ( $users as $user ) {
            // Count user photos
            $photo_count = count_user_posts( $user->ID, 'car_photo' );

            $users_data[] = array(
                'id'         => $user->ID,
                'name'       => $user->display_name,
                'email'      => $user->user_email,
                'registered' => date( 'd/m/Y', strtotime( $user->user_registered ) ),
                'photos'     => $photo_count,
                'role'       => implode( ', ', $user->roles ),
                'banned'     => get_user_meta( $user->ID, 'szr_banned', true ) === '1',
            );
        }

        wp_send_json_success( array(
            'users' => $users_data,
            'total' => $user_query->get_total(),
            'pages' => ceil( $user_query->get_total() / $per_page ),
        ) );
    }

    /**
     * AJAX: Get user stats
     */
    public static function ajax_get_user_stats() {
        check_ajax_referer( 'szr_admin_nonce', 'nonce' );

        $user_id = intval( $_POST['user_id'] ?? 0 );

        if ( $user_id <= 0 ) {
            wp_send_json_error( array( 'message' => 'ID utilisateur invalide' ) );
        }

        $user = get_userdata( $user_id );
        if ( ! $user ) {
            wp_send_json_error( array( 'message' => 'Utilisateur non trouvé' ) );
        }

        // Get user photos by status
        $published = count_user_posts( $user_id, 'car_photo' );
        $pending = get_posts( array(
            'post_type'   => 'car_photo',
            'post_status' => 'pending',
            'author'      => $user_id,
            'fields'      => 'ids',
        ) );
        $draft = get_posts( array(
            'post_type'   => 'car_photo',
            'post_status' => 'draft',
            'author'      => $user_id,
            'fields'      => 'ids',
        ) );

        // Get recent photos
        $recent_photos = get_posts( array(
            'post_type'      => 'car_photo',
            'author'         => $user_id,
            'posts_per_page' => 5,
            'post_status'    => 'any',
        ) );

        $recent_data = array();
        foreach ( $recent_photos as $photo ) {
            $recent_data[] = array(
                'id'     => $photo->ID,
                'title'  => $photo->post_title,
                'image'  => get_the_post_thumbnail_url( $photo->ID, 'thumbnail' ),
                'status' => $photo->post_status,
                'date'   => get_the_date( 'd/m/Y', $photo->ID ),
            );
        }

        wp_send_json_success( array(
            'stats' => array(
                'published' => $published,
                'pending'   => count( $pending ),
                'draft'     => count( $draft ),
                'total'     => $published + count( $pending ) + count( $draft ),
            ),
            'recent' => $recent_data,
        ) );
    }

    /**
     * AJAX: Ban user
     */
    public static function ajax_ban_user() {
        check_ajax_referer( 'szr_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Permission refusée' ) );
        }

        $user_id = intval( $_POST['user_id'] ?? 0 );

        if ( $user_id <= 0 ) {
            wp_send_json_error( array( 'message' => 'ID utilisateur invalide' ) );
        }

        // Don't allow banning admins
        $user = get_userdata( $user_id );
        if ( in_array( 'administrator', $user->roles ) ) {
            wp_send_json_error( array( 'message' => 'Impossible de bannir un administrateur' ) );
        }

        update_user_meta( $user_id, 'szr_banned', '1' );

        wp_send_json_success( array(
            'message' => 'Utilisateur banni avec succès',
        ) );
    }

    /**
     * AJAX: Unban user
     */
    public static function ajax_unban_user() {
        check_ajax_referer( 'szr_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Permission refusée' ) );
        }

        $user_id = intval( $_POST['user_id'] ?? 0 );

        if ( $user_id <= 0 ) {
            wp_send_json_error( array( 'message' => 'ID utilisateur invalide' ) );
        }

        delete_user_meta( $user_id, 'szr_banned' );

        wp_send_json_success( array(
            'message' => 'Utilisateur débanni avec succès',
        ) );
    }
}

SZR_Community::init();
