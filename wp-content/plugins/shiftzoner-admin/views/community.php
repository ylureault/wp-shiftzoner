<?php
/**
 * Community Manager View
 *
 * @package ShiftZoneRAdmin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="wrap szr-admin-wrap">
    <h1 class="szr-admin-title">
        <span class="dashicons dashicons-groups"></span>
        <?php _e( 'Gestion de la Communauté', 'shiftzoner-admin' ); ?>
    </h1>

    <!-- Stats Bar -->
    <div class="szr-stats-bar">
        <div class="szr-stat-item">
            <strong><?php echo esc_html( $total_users['total_users'] ?? 0 ); ?></strong> <?php _e( 'Utilisateurs', 'shiftzoner-admin' ); ?>
        </div>
        <div class="szr-stat-item">
            <strong><?php echo esc_html( $contributor_count ); ?></strong> <?php _e( 'Contributeurs', 'shiftzoner-admin' ); ?>
        </div>
    </div>

    <div class="szr-dashboard-grid">
        <!-- Top Contributors -->
        <div class="szr-dashboard-card">
            <h2><?php _e( 'Top Contributeurs', 'shiftzoner-admin' ); ?></h2>
            <div class="szr-contributors-list">
                <?php if ( ! empty( $top_contributors ) ) : ?>
                    <?php foreach ( $top_contributors as $contributor ) : ?>
                        <div class="szr-contributor-item">
                            <div class="szr-contributor-avatar">
                                <?php echo get_avatar( $contributor->post_author, 40 ); ?>
                            </div>
                            <div class="szr-contributor-info">
                                <div class="szr-contributor-name"><?php echo esc_html( $contributor->display_name ); ?></div>
                                <div class="szr-contributor-stats"><?php echo esc_html( $contributor->photo_count ); ?> photos</div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p class="szr-no-data"><?php _e( 'Aucun contributeur pour le moment', 'shiftzoner-admin' ); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Search Users -->
        <div class="szr-dashboard-card">
            <h2><?php _e( 'Rechercher un Utilisateur', 'shiftzoner-admin' ); ?></h2>
            <div class="szr-search-box">
                <input type="text" id="szr-search-users" placeholder="<?php esc_attr_e( 'Nom, email...', 'shiftzoner-admin' ); ?>" />
            </div>
            <div id="szr-users-results" class="szr-users-results"></div>
        </div>
    </div>
</div>
