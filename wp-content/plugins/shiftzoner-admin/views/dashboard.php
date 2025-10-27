<?php
/**
 * Dashboard View
 *
 * @package ShiftZoneRAdmin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$stats = SZR_Stats::get_dashboard_stats();
?>

<div class="wrap szr-admin-wrap">
    <h1 class="szr-admin-title">
        <span class="dashicons dashicons-car"></span>
        <?php _e( 'ShiftZoneR Dashboard', 'shiftzoner-admin' ); ?>
    </h1>

    <div class="szr-dashboard">
        <!-- Stats Cards -->
        <div class="szr-stats-grid">
            <div class="szr-stat-card">
                <div class="szr-stat-icon">
                    <span class="dashicons dashicons-tag"></span>
                </div>
                <div class="szr-stat-content">
                    <div class="szr-stat-value"><?php echo esc_html( $stats['totals']['brands'] ); ?></div>
                    <div class="szr-stat-label"><?php _e( 'Marques', 'shiftzoner-admin' ); ?></div>
                </div>
            </div>

            <div class="szr-stat-card">
                <div class="szr-stat-icon">
                    <span class="dashicons dashicons-car"></span>
                </div>
                <div class="szr-stat-content">
                    <div class="szr-stat-value"><?php echo esc_html( $stats['totals']['models'] ); ?></div>
                    <div class="szr-stat-label"><?php _e( 'Modèles', 'shiftzoner-admin' ); ?></div>
                </div>
            </div>

            <div class="szr-stat-card">
                <div class="szr-stat-icon">
                    <span class="dashicons dashicons-camera"></span>
                </div>
                <div class="szr-stat-content">
                    <div class="szr-stat-value"><?php echo esc_html( $stats['totals']['published_photos'] ); ?></div>
                    <div class="szr-stat-label"><?php _e( 'Photos Publiées', 'shiftzoner-admin' ); ?></div>
                </div>
            </div>

            <div class="szr-stat-card">
                <div class="szr-stat-icon">
                    <span class="dashicons dashicons-clock"></span>
                </div>
                <div class="szr-stat-content">
                    <div class="szr-stat-value"><?php echo esc_html( $stats['totals']['pending_photos'] ); ?></div>
                    <div class="szr-stat-label"><?php _e( 'En Attente', 'shiftzoner-admin' ); ?></div>
                </div>
            </div>

            <div class="szr-stat-card">
                <div class="szr-stat-icon">
                    <span class="dashicons dashicons-groups"></span>
                </div>
                <div class="szr-stat-content">
                    <div class="szr-stat-value"><?php echo esc_html( $stats['totals']['users'] ); ?></div>
                    <div class="szr-stat-label"><?php _e( 'Utilisateurs', 'shiftzoner-admin' ); ?></div>
                </div>
            </div>

            <div class="szr-stat-card">
                <div class="szr-stat-icon">
                    <span class="dashicons dashicons-images-alt2"></span>
                </div>
                <div class="szr-stat-content">
                    <div class="szr-stat-value"><?php echo esc_html( $stats['totals']['total_photos'] ); ?></div>
                    <div class="szr-stat-label"><?php _e( 'Total Photos', 'shiftzoner-admin' ); ?></div>
                </div>
            </div>
        </div>

        <div class="szr-dashboard-grid">
            <!-- Top Brands -->
            <div class="szr-dashboard-card">
                <h2><?php _e( 'Top Marques', 'shiftzoner-admin' ); ?></h2>
                <div class="szr-top-brands-list">
                    <?php if ( ! empty( $stats['top_brands'] ) ) : ?>
                        <?php foreach ( $stats['top_brands'] as $brand ) : ?>
                            <div class="szr-top-brand-item">
                                <?php if ( $brand['logo_url'] ) : ?>
                                    <img src="<?php echo esc_url( $brand['logo_url'] ); ?>" alt="<?php echo esc_attr( $brand['name'] ); ?>" class="szr-brand-logo-thumb">
                                <?php else : ?>
                                    <div class="szr-brand-logo-placeholder">
                                        <span class="dashicons dashicons-car"></span>
                                    </div>
                                <?php endif; ?>
                                <div class="szr-brand-info">
                                    <div class="szr-brand-name"><?php echo esc_html( $brand['name'] ); ?></div>
                                    <div class="szr-brand-count"><?php echo esc_html( $brand['count'] ); ?> photos</div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <p class="szr-no-data"><?php _e( 'Aucune donnée disponible', 'shiftzoner-admin' ); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Photos -->
            <div class="szr-dashboard-card">
                <h2><?php _e( 'Photos Récentes', 'shiftzoner-admin' ); ?></h2>
                <div class="szr-recent-photos-list">
                    <?php if ( ! empty( $stats['recent_photos'] ) ) : ?>
                        <?php foreach ( $stats['recent_photos'] as $photo ) : ?>
                            <div class="szr-recent-photo-item">
                                <?php if ( has_post_thumbnail( $photo->ID ) ) : ?>
                                    <?php echo get_the_post_thumbnail( $photo->ID, 'thumbnail', array( 'class' => 'szr-photo-thumb' ) ); ?>
                                <?php else : ?>
                                    <div class="szr-photo-placeholder">
                                        <span class="dashicons dashicons-camera"></span>
                                    </div>
                                <?php endif; ?>
                                <div class="szr-photo-info">
                                    <div class="szr-photo-title"><?php echo esc_html( $photo->post_title ?: __( 'Sans titre', 'shiftzoner-admin' ) ); ?></div>
                                    <div class="szr-photo-meta">
                                        <span class="szr-photo-status szr-status-<?php echo esc_attr( $photo->post_status ); ?>">
                                            <?php echo esc_html( ucfirst( $photo->post_status ) ); ?>
                                        </span>
                                        <span class="szr-photo-date"><?php echo get_the_date( 'd/m/Y', $photo->ID ); ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <p class="szr-no-data"><?php _e( 'Aucune photo récente', 'shiftzoner-admin' ); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Photos by Month Chart -->
            <div class="szr-dashboard-card szr-card-full">
                <h2><?php _e( 'Photos par Mois', 'shiftzoner-admin' ); ?></h2>
                <div class="szr-chart-container">
                    <?php if ( ! empty( $stats['photos_by_month'] ) ) : ?>
                        <div class="szr-simple-chart">
                            <?php $max_count = max( array_column( $stats['photos_by_month'], 'count' ) ); ?>
                            <?php foreach ( $stats['photos_by_month'] as $data ) : ?>
                                <?php $height = $max_count > 0 ? ( $data['count'] / $max_count ) * 100 : 0; ?>
                                <div class="szr-chart-bar-wrapper">
                                    <div class="szr-chart-bar" style="height: <?php echo esc_attr( $height ); ?>%;" title="<?php echo esc_attr( $data['count'] ); ?> photos">
                                        <span class="szr-chart-value"><?php echo esc_html( $data['count'] ); ?></span>
                                    </div>
                                    <div class="szr-chart-label"><?php echo esc_html( $data['month'] ); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <p class="szr-no-data"><?php _e( 'Aucune donnée disponible', 'shiftzoner-admin' ); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="szr-quick-actions">
            <h2><?php _e( 'Actions Rapides', 'shiftzoner-admin' ); ?></h2>
            <div class="szr-actions-grid">
                <a href="<?php echo admin_url( 'admin.php?page=shiftzoner-brands' ); ?>" class="szr-action-button">
                    <span class="dashicons dashicons-tag"></span>
                    <?php _e( 'Gérer les Marques', 'shiftzoner-admin' ); ?>
                </a>
                <a href="<?php echo admin_url( 'admin.php?page=shiftzoner-photos' ); ?>" class="szr-action-button">
                    <span class="dashicons dashicons-camera"></span>
                    <?php _e( 'Modérer les Photos', 'shiftzoner-admin' ); ?>
                </a>
                <a href="<?php echo admin_url( 'admin.php?page=shiftzoner-community' ); ?>" class="szr-action-button">
                    <span class="dashicons dashicons-groups"></span>
                    <?php _e( 'Gérer la Communauté', 'shiftzoner-admin' ); ?>
                </a>
                <a href="<?php echo admin_url( 'admin.php?page=shiftzoner-settings' ); ?>" class="szr-action-button">
                    <span class="dashicons dashicons-admin-settings"></span>
                    <?php _e( 'Paramètres', 'shiftzoner-admin' ); ?>
                </a>
            </div>
        </div>
    </div>
</div>
