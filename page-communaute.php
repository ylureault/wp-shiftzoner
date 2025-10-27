<?php
/**
 * Template Name: Communauté
 * Description: Page communauté avec classements, profils et statistiques
 *
 * @package ShiftZoneR
 */

get_header();

// Get community stats
$total_users = count_users();
$total_members = $total_users['total_users'];
$total_photos = wp_count_posts( 'car_photo' )->publish;

// Top contributors (users with most photos)
global $wpdb;
$top_contributors = $wpdb->get_results( "
    SELECT
        p.post_author,
        COUNT(*) as photo_count,
        u.display_name,
        u.user_login
    FROM {$wpdb->posts} p
    INNER JOIN {$wpdb->users} u ON p.post_author = u.ID
    WHERE p.post_type = 'car_photo'
    AND p.post_status = 'publish'
    GROUP BY p.post_author
    ORDER BY photo_count DESC
    LIMIT 10
", ARRAY_A );

// Recent contributors (users who posted recently)
$recent_contributors = $wpdb->get_results( "
    SELECT DISTINCT
        p.post_author,
        u.display_name,
        u.user_login,
        MAX(p.post_date) as last_post
    FROM {$wpdb->posts} p
    INNER JOIN {$wpdb->users} u ON p.post_author = u.ID
    WHERE p.post_type = 'car_photo'
    AND p.post_status = 'publish'
    AND p.post_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    GROUP BY p.post_author
    ORDER BY last_post DESC
    LIMIT 12
", ARRAY_A );

// Top voted photos
$top_voted = get_posts( array(
    'post_type'      => 'car_photo',
    'posts_per_page' => 6,
    'post_status'    => 'publish',
    'meta_key'       => '_szr_votes',
    'orderby'        => 'meta_value_num',
    'order'          => 'DESC',
) );
?>

<div class="community-page">
    <!-- Hero Section -->
    <div class="community-hero">
        <div class="container">
            <h1 class="community-hero-title" data-animate="fade-up">
                Rejoignez la Communauté ShiftZoneR
            </h1>
            <p class="community-hero-subtitle" data-animate="fade-up" data-delay="100">
                Une communauté passionnée, des milliers de photos, une seule passion
            </p>

            <!-- Stats Cards -->
            <div class="community-stats" data-animate="fade-up" data-delay="200">
                <div class="stat-card">
                    <div class="stat-icon">
                        <svg width="32" height="32" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                        </svg>
                    </div>
                    <div class="stat-number" data-count="<?php echo $total_members; ?>">0</div>
                    <div class="stat-label">Membres</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <svg width="32" height="32" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="stat-number" data-count="<?php echo $total_photos; ?>">0</div>
                    <div class="stat-label">Photos Partagées</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <svg width="32" height="32" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z"/>
                        </svg>
                    </div>
                    <div class="stat-number" data-count="<?php echo $wpdb->get_var( "SELECT SUM(meta_value) FROM {$wpdb->postmeta} WHERE meta_key = '_szr_votes'" ); ?>">0</div>
                    <div class="stat-label">Votes Total</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <svg width="32" height="32" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5zM15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z"/>
                        </svg>
                    </div>
                    <div class="stat-number" data-count="<?php echo wp_count_comments()->approved; ?>">0</div>
                    <div class="stat-label">Commentaires</div>
                </div>
            </div>

            <?php if ( ! is_user_logged_in() ) : ?>
            <div class="community-cta" data-animate="fade-up" data-delay="300">
                <a href="<?php echo esc_url( wp_registration_url() ); ?>" class="cta-button-large">
                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"/>
                    </svg>
                    Rejoindre la Communauté
                </a>
                <p class="cta-note">C'est gratuit et ça prend 30 secondes</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="container">
        <!-- Top Contributors Leaderboard -->
        <section class="community-section">
            <div class="section-header" data-animate="fade-up">
                <div>
                    <h2 class="section-title">
                        <svg width="28" height="28" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        Top Contributeurs
                    </h2>
                    <p class="section-subtitle">Les membres les plus actifs de la communauté</p>
                </div>
            </div>

            <div class="leaderboard">
                <?php if ( ! empty( $top_contributors ) ) : ?>
                    <?php foreach ( $top_contributors as $index => $contributor ) : ?>
                        <?php
                        $rank = $index + 1;
                        $user_id = $contributor['post_author'];
                        $rank_class = '';
                        if ( $rank === 1 ) $rank_class = 'rank-gold';
                        elseif ( $rank === 2 ) $rank_class = 'rank-silver';
                        elseif ( $rank === 3 ) $rank_class = 'rank-bronze';

                        // Get user's latest photo
                        $latest_photo = get_posts( array(
                            'post_type'      => 'car_photo',
                            'posts_per_page' => 1,
                            'author'         => $user_id,
                            'post_status'    => 'publish',
                        ) );
                        ?>
                        <div class="leaderboard-item <?php echo esc_attr( $rank_class ); ?>" data-animate="fade-up" data-delay="<?php echo $index * 50; ?>">
                            <div class="leaderboard-rank">
                                <?php if ( $rank <= 3 ) : ?>
                                    <svg width="32" height="32" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    <span class="rank-badge"><?php echo $rank; ?></span>
                                <?php else : ?>
                                    <span class="rank-number">#<?php echo $rank; ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="leaderboard-avatar">
                                <?php echo get_avatar( $user_id, 60 ); ?>
                            </div>

                            <div class="leaderboard-info">
                                <h3 class="leaderboard-name"><?php echo esc_html( $contributor['display_name'] ); ?></h3>
                                <div class="leaderboard-stats">
                                    <span class="stat-item">
                                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                        </svg>
                                        <?php echo number_format( $contributor['photo_count'] ); ?> photos
                                    </span>
                                </div>
                            </div>

                            <?php if ( ! empty( $latest_photo ) ) : ?>
                                <div class="leaderboard-preview">
                                    <?php echo get_the_post_thumbnail( $latest_photo[0]->ID, 'thumbnail' ); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p class="no-data">Aucun contributeur pour le moment. Soyez le premier !</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- Recent Active Members -->
        <section class="community-section">
            <div class="section-header" data-animate="fade-up">
                <h2 class="section-title">
                    <svg width="28" height="28" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>
                    </svg>
                    Membres Actifs
                </h2>
                <p class="section-subtitle">Membres ayant partagé récemment (30 derniers jours)</p>
            </div>

            <div class="members-grid">
                <?php if ( ! empty( $recent_contributors ) ) : ?>
                    <?php foreach ( $recent_contributors as $index => $member ) : ?>
                        <?php
                        $user_id = $member['post_author'];
                        $user_data = get_userdata( $user_id );
                        $photo_count = count_user_posts( $user_id, 'car_photo' );
                        ?>
                        <div class="member-card" data-animate="fade-up" data-delay="<?php echo $index * 30; ?>">
                            <div class="member-avatar">
                                <?php echo get_avatar( $user_id, 80 ); ?>
                            </div>
                            <h3 class="member-name"><?php echo esc_html( $member['display_name'] ); ?></h3>
                            <p class="member-stats"><?php echo $photo_count; ?> photos</p>
                            <p class="member-last-active">Actif il y a <?php echo human_time_diff( strtotime( $member['last_post'] ), current_time( 'timestamp' ) ); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p class="no-data">Aucun membre actif récemment.</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- Top Voted Photos -->
        <section class="community-section">
            <div class="section-header" data-animate="fade-up">
                <h2 class="section-title">
                    <svg width="28" height="28" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z"/>
                    </svg>
                    Photos les Plus Aimées
                </h2>
                <p class="section-subtitle">Les photos préférées de la communauté</p>
            </div>

            <div class="photos-showcase">
                <?php if ( ! empty( $top_voted ) ) : ?>
                    <?php foreach ( $top_voted as $index => $photo ) : ?>
                        <?php
                        $votes = intval( get_post_meta( $photo->ID, '_szr_votes', true ) );
                        $views = intval( get_post_meta( $photo->ID, '_szr_views', true ) );
                        ?>
                        <div class="showcase-photo" data-animate="fade-up" data-delay="<?php echo $index * 50; ?>">
                            <a href="<?php echo get_permalink( $photo->ID ); ?>" class="showcase-link">
                                <?php echo get_the_post_thumbnail( $photo->ID, 'medium', array( 'loading' => 'lazy' ) ); ?>
                                <div class="showcase-overlay">
                                    <div class="showcase-stats">
                                        <span class="stat-item">
                                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z"/>
                                            </svg>
                                            <?php echo $votes; ?>
                                        </span>
                                        <span class="stat-item">
                                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                            </svg>
                                            <?php echo number_format( $views ); ?>
                                        </span>
                                    </div>
                                    <div class="showcase-author">
                                        <?php echo get_avatar( $photo->post_author, 32 ); ?>
                                        <span><?php echo get_the_author_meta( 'display_name', $photo->post_author ); ?></span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p class="no-data">Aucune photo votée pour le moment.</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- Call to Action -->
        <?php if ( is_user_logged_in() ) : ?>
        <section class="community-cta-section" data-animate="fade-up">
            <div class="cta-box">
                <h2>Partagez Votre Passion</h2>
                <p>Rejoignez les meilleurs contributeurs en partageant vos plus belles photos automobiles</p>
                <a href="<?php echo esc_url( home_url( '/soumettre-ma-photo/' ) ); ?>" class="cta-button-large">
                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                    </svg>
                    Publier une Photo
                </a>
            </div>
        </section>
        <?php endif; ?>
    </div>
</div>

<script>
(function() {
    // Animation au scroll
    function animateOnScroll() {
        const elements = document.querySelectorAll('[data-animate]:not(.animated)');
        elements.forEach(el => {
            const rect = el.getBoundingClientRect();
            if (rect.top < window.innerHeight - 100) {
                const delay = el.dataset.delay || 0;
                setTimeout(() => {
                    el.classList.add('animated');
                }, delay);
            }
        });
    }

    // Compteurs animés
    function animateCounters() {
        document.querySelectorAll('.stat-number[data-count]').forEach(counter => {
            const target = parseInt(counter.dataset.count);
            const duration = 2000;
            const step = target / (duration / 16);
            let current = 0;

            const timer = setInterval(() => {
                current += step;
                if (current >= target) {
                    counter.textContent = target.toLocaleString();
                    clearInterval(timer);
                } else {
                    counter.textContent = Math.floor(current).toLocaleString();
                }
            }, 16);
        });
    }

    // Init
    window.addEventListener('scroll', animateOnScroll);
    animateOnScroll();
    setTimeout(() => animateCounters(), 500);
})();
</script>

<?php
get_footer();
