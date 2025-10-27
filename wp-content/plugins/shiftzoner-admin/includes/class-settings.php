<?php
/**
 * Settings Class
 *
 * Handles site and theme settings
 *
 * @package ShiftZoneRAdmin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SZR_Settings {

    public static function init() {
        add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
        add_action( 'wp_ajax_szr_admin_save_settings', array( __CLASS__, 'ajax_save_settings' ) );
    }

    public static function render_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'Vous n\'avez pas les permissions nécessaires.', 'shiftzoner-admin' ) );
        }

        // Get current settings
        $settings = self::get_settings();

        include SZR_ADMIN_PATH . 'views/settings.php';
    }

    /**
     * Register settings
     */
    public static function register_settings() {
        // Upload settings
        register_setting( 'szr_settings', 'szr_upload_max_size' );
        register_setting( 'szr_settings', 'szr_upload_allowed_types' );
        register_setting( 'szr_settings', 'szr_upload_require_moderation' );
        register_setting( 'szr_settings', 'szr_upload_min_dimensions' );

        // Photo settings
        register_setting( 'szr_settings', 'szr_photo_watermark_enabled' );
        register_setting( 'szr_settings', 'szr_photo_watermark_text' );
        register_setting( 'szr_settings', 'szr_photo_compression' );

        // Map settings
        register_setting( 'szr_settings', 'szr_map_default_zoom' );
        register_setting( 'szr_settings', 'szr_map_default_center_lat' );
        register_setting( 'szr_settings', 'szr_map_default_center_lng' );
        register_setting( 'szr_settings', 'szr_map_tile_provider' );

        // Community settings
        register_setting( 'szr_settings', 'szr_community_allow_registration' );
        register_setting( 'szr_settings', 'szr_community_require_approval' );
        register_setting( 'szr_settings', 'szr_community_points_enabled' );

        // Theme settings
        register_setting( 'szr_settings', 'szr_theme_primary_color' );
        register_setting( 'szr_settings', 'szr_theme_secondary_color' );
        register_setting( 'szr_settings', 'szr_theme_logo' );
        register_setting( 'szr_settings', 'szr_theme_footer_text' );
    }

    /**
     * Get all settings with defaults
     */
    public static function get_settings() {
        return array(
            // Upload settings
            'upload_max_size'           => get_option( 'szr_upload_max_size', '10' ), // MB
            'upload_allowed_types'      => get_option( 'szr_upload_allowed_types', 'jpg,jpeg,png' ),
            'upload_require_moderation' => get_option( 'szr_upload_require_moderation', '1' ),
            'upload_min_dimensions'     => get_option( 'szr_upload_min_dimensions', '800x600' ),

            // Photo settings
            'photo_watermark_enabled' => get_option( 'szr_photo_watermark_enabled', '0' ),
            'photo_watermark_text'    => get_option( 'szr_photo_watermark_text', 'ShiftZoneR' ),
            'photo_compression'       => get_option( 'szr_photo_compression', '85' ),

            // Map settings
            'map_default_zoom'       => get_option( 'szr_map_default_zoom', '6' ),
            'map_default_center_lat' => get_option( 'szr_map_default_center_lat', '46.603354' ),
            'map_default_center_lng' => get_option( 'szr_map_default_center_lng', '1.888334' ),
            'map_tile_provider'      => get_option( 'szr_map_tile_provider', 'openstreetmap' ),

            // Community settings
            'community_allow_registration' => get_option( 'szr_community_allow_registration', '1' ),
            'community_require_approval'   => get_option( 'szr_community_require_approval', '0' ),
            'community_points_enabled'     => get_option( 'szr_community_points_enabled', '0' ),

            // Theme settings
            'theme_primary_color'   => get_option( 'szr_theme_primary_color', '#1a1a1a' ),
            'theme_secondary_color' => get_option( 'szr_theme_secondary_color', '#ff6b35' ),
            'theme_logo'            => get_option( 'szr_theme_logo', '' ),
            'theme_footer_text'     => get_option( 'szr_theme_footer_text', 'ShiftZoneR - La communauté automobile' ),
        );
    }

    /**
     * AJAX: Save settings
     */
    public static function ajax_save_settings() {
        check_ajax_referer( 'szr_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Permission refusée' ) );
        }

        $settings = isset( $_POST['settings'] ) ? $_POST['settings'] : array();

        if ( empty( $settings ) ) {
            wp_send_json_error( array( 'message' => 'Aucune donnée fournie' ) );
        }

        // Save each setting
        foreach ( $settings as $key => $value ) {
            $option_key = 'szr_' . $key;
            update_option( $option_key, sanitize_text_field( $value ) );
        }

        wp_send_json_success( array(
            'message' => 'Paramètres enregistrés avec succès',
        ) );
    }
}

SZR_Settings::init();
