<?php
/**
 * Plugin Name: ShiftZoneR Admin
 * Plugin URI: https://shiftzoner.fr
 * Description: Plugin complet de gestion pour ShiftZoneR - Marques, Modèles, Logos, Photos, Communauté et Paramètres
 * Version: 1.0.0
 * Author: ShiftZoneR Team
 * Author URI: https://shiftzoner.fr
 * Text Domain: shiftzoner-admin
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 *
 * @package ShiftZoneRAdmin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Constants
define( 'SZR_ADMIN_VERSION', '1.0.0' );
define( 'SZR_ADMIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'SZR_ADMIN_URL', plugin_dir_url( __FILE__ ) );
define( 'SZR_TAX_BRAND', 'car_brand' );
define( 'SZR_TAX_MODEL', 'car_model' );
define( 'SZR_META_BRAND_LOGO', '_szr_brand_logo_id' );
define( 'SZR_META_MODEL_BRAND', '_szr_model_brand' );

/**
 * Main Plugin Class
 */
class ShiftZoneR_Admin {

    private static $instance = null;

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->includes();
        $this->hooks();
    }

    private function includes() {
        require_once SZR_ADMIN_PATH . 'includes/class-brands-manager.php';
        require_once SZR_ADMIN_PATH . 'includes/class-models-manager.php';
        require_once SZR_ADMIN_PATH . 'includes/class-photos-manager.php';
        require_once SZR_ADMIN_PATH . 'includes/class-settings.php';
        require_once SZR_ADMIN_PATH . 'includes/class-community.php';
        require_once SZR_ADMIN_PATH . 'includes/class-stats.php';
    }

    private function hooks() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_action( 'admin_init', array( $this, 'ensure_hierarchical_taxonomies' ) );
    }

    public function ensure_hierarchical_taxonomies() {
        // S'assurer que car_model est hiérarchique
        if ( taxonomy_exists( SZR_TAX_MODEL ) ) {
            global $wp_taxonomies;
            if ( isset( $wp_taxonomies[ SZR_TAX_MODEL ] ) ) {
                $wp_taxonomies[ SZR_TAX_MODEL ]->hierarchical = true;
            }
        }
    }

    public function add_admin_menu() {
        add_menu_page(
            __( 'ShiftZoneR Admin', 'shiftzoner-admin' ),
            __( 'ShiftZoneR', 'shiftzoner-admin' ),
            'manage_options',
            'shiftzoner-admin',
            array( $this, 'render_dashboard' ),
            'dashicons-car',
            3
        );

        add_submenu_page(
            'shiftzoner-admin',
            __( 'Dashboard', 'shiftzoner-admin' ),
            __( 'Dashboard', 'shiftzoner-admin' ),
            'manage_options',
            'shiftzoner-admin',
            array( $this, 'render_dashboard' )
        );

        add_submenu_page(
            'shiftzoner-admin',
            __( 'Marques & Modèles', 'shiftzoner-admin' ),
            __( 'Marques & Modèles', 'shiftzoner-admin' ),
            'manage_options',
            'shiftzoner-brands',
            array( 'SZR_Brands_Manager', 'render_page' )
        );

        add_submenu_page(
            'shiftzoner-admin',
            __( 'Photos', 'shiftzoner-admin' ),
            __( 'Photos', 'shiftzoner-admin' ),
            'manage_options',
            'shiftzoner-photos',
            array( 'SZR_Photos_Manager', 'render_page' )
        );

        add_submenu_page(
            'shiftzoner-admin',
            __( 'Communauté', 'shiftzoner-admin' ),
            __( 'Communauté', 'shiftzoner-admin' ),
            'manage_options',
            'shiftzoner-community',
            array( 'SZR_Community', 'render_page' )
        );

        add_submenu_page(
            'shiftzoner-admin',
            __( 'Paramètres', 'shiftzoner-admin' ),
            __( 'Paramètres', 'shiftzoner-admin' ),
            'manage_options',
            'shiftzoner-settings',
            array( 'SZR_Settings', 'render_page' )
        );
    }

    public function enqueue_assets( $hook ) {
        if ( strpos( $hook, 'shiftzoner' ) === false ) {
            return;
        }

        wp_enqueue_style(
            'shiftzoner-admin',
            SZR_ADMIN_URL . 'assets/css/admin.css',
            array(),
            SZR_ADMIN_VERSION
        );

        wp_enqueue_script(
            'shiftzoner-admin',
            SZR_ADMIN_URL . 'assets/js/admin.js',
            array( 'jquery' ),
            SZR_ADMIN_VERSION,
            true
        );

        wp_localize_script(
            'shiftzoner-admin',
            'szrAdmin',
            array(
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'nonce'   => wp_create_nonce( 'szr_admin_nonce' ),
            )
        );
    }

    public function render_dashboard() {
        include SZR_ADMIN_PATH . 'views/dashboard.php';
    }
}

// Initialize plugin
function szr_admin() {
    return ShiftZoneR_Admin::instance();
}

szr_admin();
