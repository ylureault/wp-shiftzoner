<?php
/**
 * Single Car Group Template
 *
 * @package ShiftZoneR
 */

get_header();

while ( have_posts() ) : the_post();
    $group_id = get_the_ID();
    $brand_id = get_post_meta( $group_id, '_szr_group_brand_id', true );
    $model_id = get_post_meta( $group_id, '_szr_group_model_id', true );
    $members = get_post_meta( $group_id, '_szr_group_members', true ) ?: array();
    $member_count = count( $members );
    $is_member = is_user_logged_in() && in_array( get_current_user_id(), $members );

    $brand = $brand_id ? get_term( $brand_id, 'car_brand' ) : null;
    $model = $model_id ? get_term( $model_id, 'car_model' ) : null;

    // Get photos related to this model
    $photos_args = array(
        'post_type' => 'car_photo',
        'posts_per_page' => 12,
        'post_status' => 'publish'
    );

    if ( $model_id ) {
        $photos_args['tax_query'] = array(
            array(
                'taxonomy' => 'car_model',
                'field' => 'term_id',
                'terms' => $model_id
            )
        );
    }

    $photos_query = new WP_Query( $photos_args );
    ?>

    <div class="single-group-page">
        <!-- Group Header -->
        <div class="group-hero">
            <?php if ( has_post_thumbnail() ) : ?>
                <div class="hero-background" style="background-image: url('<?php echo esc_url( get_the_post_thumbnail_url( null, 'full' ) ); ?>');"></div>
                <div class="hero-overlay"></div>
            <?php endif; ?>

            <div class="container">
                <div class="hero-content">
                    <?php if ( $brand && $model ) : ?>
                        <div class="group-badge-large">
                            <span class="badge-brand"><?php echo esc_html( $brand->name ); ?></span>
                            <span class="badge-separator">•</span>
                            <span class="badge-model"><?php echo esc_html( $model->name ); ?></span>
                        </div>
                    <?php endif; ?>

                    <h1 class="group-hero-title"><?php the_title(); ?></h1>

                    <div class="group-stats">
                        <div class="stat-item">
                            <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                            </svg>
                            <span><strong><?php echo $member_count; ?></strong> membre<?php echo $member_count > 1 ? 's' : ''; ?></span>
                        </div>

                        <div class="stat-item">
                            <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                            </svg>
                            <span><strong><?php echo $photos_query->found_posts; ?></strong> photo<?php echo $photos_query->found_posts > 1 ? 's' : ''; ?></span>
                        </div>

                        <div class="stat-item">
                            <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                            </svg>
                            <span>Créé par <strong><?php echo esc_html( get_the_author() ); ?></strong></span>
                        </div>
                    </div>

                    <div class="hero-actions">
                        <?php if ( is_user_logged_in() ) : ?>
                            <button
                                class="join-group-btn-hero <?php echo $is_member ? 'is-member' : ''; ?>"
                                id="toggle-membership"
                                data-group-id="<?php echo esc_attr( $group_id ); ?>"
                                data-is-member="<?php echo $is_member ? '1' : '0'; ?>">
                                <svg class="icon-join" width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"/>
                                </svg>
                                <svg class="icon-leave" width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M11 6a3 3 0 11-6 0 3 3 0 016 0zM14 17a6 6 0 00-12 0h12zM13 8a1 1 0 100 2h4a1 1 0 100-2h-4z"/>
                                </svg>
                                <span class="btn-text-join">Rejoindre le groupe</span>
                                <span class="btn-text-leave">Quitter le groupe</span>
                                <span class="btn-loader">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10" opacity="0.25"/>
                                        <path d="M12 2 A10 10 0 0 1 22 12" stroke-linecap="round">
                                            <animateTransform attributeName="transform" type="rotate" from="0 12 12" to="360 12 12" dur="1s" repeatCount="indefinite"/>
                                        </path>
                                    </svg>
                                </span>
                            </button>
                        <?php else : ?>
                            <a href="<?php echo esc_url( wp_login_url( get_permalink() ) ); ?>" class="join-group-btn-hero">
                                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 3a1 1 0 011 1v12a1 1 0 11-2 0V4a1 1 0 011-1zm7.707 3.293a1 1 0 010 1.414L9.414 9H17a1 1 0 110 2H9.414l1.293 1.293a1 1 0 01-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Connexion pour rejoindre
                            </a>
                        <?php endif; ?>

                        <a href="<?php echo esc_url( home_url( '/groupes/' ) ); ?>" class="secondary-btn-hero">
                            Voir tous les groupes
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Group Content -->
        <div class="group-main-content">
            <div class="container">
                <div class="group-layout">
                    <!-- Main Column -->
                    <div class="group-main-col">
                        <!-- Description -->
                        <?php if ( has_excerpt() || get_the_content() ) : ?>
                            <div class="group-section">
                                <h2 class="section-title">À propos du groupe</h2>
                                <div class="group-description">
                                    <?php the_content(); ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Photos -->
                        <?php if ( $photos_query->have_posts() ) : ?>
                            <div class="group-section">
                                <h2 class="section-title">Photos du groupe</h2>
                                <div class="group-photos-grid">
                                    <?php while ( $photos_query->have_posts() ) : $photos_query->the_post(); ?>
                                        <a href="<?php the_permalink(); ?>" class="group-photo-item">
                                            <?php if ( has_post_thumbnail() ) : ?>
                                                <?php the_post_thumbnail( 'medium' ); ?>
                                            <?php else : ?>
                                                <div class="no-thumbnail">📸</div>
                                            <?php endif; ?>
                                            <div class="photo-overlay">
                                                <span class="photo-title"><?php the_title(); ?></span>
                                            </div>
                                        </a>
                                    <?php endwhile; ?>
                                    <?php wp_reset_postdata(); ?>
                                </div>

                                <?php if ( $model ) : ?>
                                    <div class="view-all-photos">
                                        <a href="<?php echo esc_url( get_term_link( $model, 'car_model' ) ); ?>" class="view-all-btn">
                                            Voir toutes les photos de <?php echo esc_html( $model->name ); ?> →
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Comments -->
                        <div class="group-section">
                            <h2 class="section-title">Discussion du groupe</h2>
                            <?php comments_template(); ?>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="group-sidebar-col">
                        <!-- Members List -->
                        <div class="sidebar-widget">
                            <h3 class="widget-title">
                                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                                </svg>
                                Membres (<?php echo $member_count; ?>)
                            </h3>
                            <div class="members-list">
                                <?php
                                $displayed_members = array_slice( $members, 0, 10 );
                                foreach ( $displayed_members as $member_id ) :
                                    $user = get_userdata( $member_id );
                                    if ( ! $user ) continue;
                                    $user_color = get_user_meta( $member_id, '_szr_user_color', true ) ?: '#888888';
                                    $karma = get_user_meta( $member_id, '_szr_karma', true ) ?: 0;
                                    ?>
                                    <div class="member-item">
                                        <div class="member-avatar" style="background: <?php echo esc_attr( $user_color ); ?>">
                                            <?php echo esc_html( strtoupper( substr( $user->display_name, 0, 1 ) ) ); ?>
                                        </div>
                                        <div class="member-info">
                                            <div class="member-name"><?php echo esc_html( $user->display_name ); ?></div>
                                            <div class="member-karma"><?php echo $karma; ?> points</div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>

                                <?php if ( $member_count > 10 ) : ?>
                                    <div class="view-all-members">
                                        <button class="view-all-link" id="show-all-members">
                                            + <?php echo $member_count - 10; ?> autre<?php echo ( $member_count - 10 ) > 1 ? 's' : ''; ?> membre<?php echo ( $member_count - 10 ) > 1 ? 's' : ''; ?>
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Group Info -->
                        <div class="sidebar-widget">
                            <h3 class="widget-title">Informations</h3>
                            <div class="group-info-list">
                                <?php if ( $brand ) : ?>
                                    <div class="info-item">
                                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V4a2 2 0 00-2-2H6zm1 2a1 1 0 000 2h6a1 1 0 100-2H7zm6 7a1 1 0 011 1v3a1 1 0 11-2 0v-3a1 1 0 011-1zm-3 3a1 1 0 100 2h.01a1 1 0 100-2H10zm-4 1a1 1 0 011-1h.01a1 1 0 110 2H7a1 1 0 01-1-1zm1-4a1 1 0 100 2h.01a1 1 0 100-2H7zm2 1a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1zm4-4a1 1 0 100 2h.01a1 1 0 100-2H13zM9 9a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1zM7 8a1 1 0 000 2h.01a1 1 0 000-2H7z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>Marque: <strong><?php echo esc_html( $brand->name ); ?></strong></span>
                                    </div>
                                <?php endif; ?>

                                <?php if ( $model ) : ?>
                                    <div class="info-item">
                                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
                                            <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z"/>
                                        </svg>
                                        <span>Modèle: <strong><?php echo esc_html( $model->name ); ?></strong></span>
                                    </div>
                                <?php endif; ?>

                                <div class="info-item">
                                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>Créé le <strong><?php echo get_the_date(); ?></strong></span>
                                </div>

                                <div class="info-item">
                                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zM7 8H5v2h2V8zm2 0h2v2H9V8zm6 0h-2v2h2V8z" clip-rule="evenodd"/>
                                    </svg>
                                    <span><strong><?php echo get_comments_number(); ?></strong> discussion<?php echo get_comments_number() > 1 ? 's' : ''; ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Share Widget -->
                        <div class="sidebar-widget">
                            <h3 class="widget-title">Partager</h3>
                            <div class="share-buttons">
                                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode( get_permalink() ); ?>" target="_blank" class="share-btn facebook">
                                    Facebook
                                </a>
                                <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode( get_permalink() ); ?>&text=<?php echo urlencode( get_the_title() ); ?>" target="_blank" class="share-btn twitter">
                                    Twitter
                                </a>
                                <button class="share-btn copy-link" data-url="<?php echo esc_url( get_permalink() ); ?>">
                                    Copier le lien
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
    <?php include( get_template_directory() . '/styles/single-car-group-styles.css' ); ?>
    </style>

    <script>
    (function() {
        const toggleBtn = document.getElementById('toggle-membership');
        const memberCountElements = document.querySelectorAll('.stat-item strong:first-of-type, .widget-title strong');

        if (toggleBtn) {
            toggleBtn.addEventListener('click', function() {
                const groupId = this.dataset.groupId;
                const isMember = this.dataset.isMember === '1';

                this.disabled = true;
                this.classList.add('loading');

                fetch('<?php echo admin_url( 'admin-ajax.php' ); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=szr_toggle_group_membership&group_id=${groupId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.dataset.isMember = data.data.is_member ? '1' : '0';
                        this.classList.toggle('is-member');

                        // Update member count
                        memberCountElements.forEach(el => {
                            el.textContent = data.data.member_count;
                        });

                        // Optionally reload to show/hide user in members list
                        if (data.data.action === 'joined') {
                            setTimeout(() => location.reload(), 1500);
                        }
                    } else {
                        alert(data.data?.message || 'Une erreur est survenue');
                    }
                    this.disabled = false;
                    this.classList.remove('loading');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Une erreur est survenue');
                    this.disabled = false;
                    this.classList.remove('loading');
                });
            });
        }

        // Copy link button
        const copyLinkBtn = document.querySelector('.copy-link');
        if (copyLinkBtn) {
            copyLinkBtn.addEventListener('click', function() {
                const url = this.dataset.url;
                navigator.clipboard.writeText(url).then(() => {
                    this.textContent = '✓ Copié !';
                    setTimeout(() => {
                        this.textContent = 'Copier le lien';
                    }, 2000);
                });
            });
        }
    })();
    </script>

<?php endwhile; ?>

<?php
get_footer();
