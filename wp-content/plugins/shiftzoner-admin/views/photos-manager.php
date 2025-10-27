<?php
/**
 * Photos Manager View
 *
 * @package ShiftZoneRAdmin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="wrap szr-admin-wrap">
    <h1 class="szr-admin-title">
        <span class="dashicons dashicons-camera"></span>
        <?php _e( 'Gestion des Photos', 'shiftzoner-admin' ); ?>
    </h1>

    <!-- Stats Bar -->
    <div class="szr-stats-bar">
        <div class="szr-stat-item">
            <strong><?php echo esc_html( $pending_count ); ?></strong> <?php _e( 'En attente', 'shiftzoner-admin' ); ?>
        </div>
        <div class="szr-stat-item">
            <strong><?php echo esc_html( $published_count ); ?></strong> <?php _e( 'Publiées', 'shiftzoner-admin' ); ?>
        </div>
        <div class="szr-stat-item">
            <strong><?php echo esc_html( $draft_count ); ?></strong> <?php _e( 'Brouillons', 'shiftzoner-admin' ); ?>
        </div>
    </div>

    <!-- Toolbar -->
    <div class="szr-toolbar">
        <div class="szr-tabs">
            <button class="szr-tab active" data-status="pending"><?php _e( 'En attente', 'shiftzoner-admin' ); ?></button>
            <button class="szr-tab" data-status="publish"><?php _e( 'Publiées', 'shiftzoner-admin' ); ?></button>
            <button class="szr-tab" data-status="draft"><?php _e( 'Brouillons', 'shiftzoner-admin' ); ?></button>
        </div>
    </div>

    <!-- Photos Grid -->
    <div id="szr-photos-grid" class="szr-photos-grid">
        <p style="text-align:center; padding:40px;">
            <span class="szr-loading"></span>
        </p>
    </div>

    <!-- Pagination -->
    <div id="szr-pagination" class="szr-pagination" style="display:none;"></div>
</div>
