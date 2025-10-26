<?php
/**
 * Single Car Photo Template
 *
 * @package ShiftZoneR
 */

get_header();

while ( have_posts() ) :
    the_post();

    // Récupérer les métadonnées
    $author_id = get_the_author_meta( 'ID' );
    $user_color = get_user_meta( $author_id, '_szr_user_color', true );
    if ( ! $user_color ) {
        $user_color = '#888888';
    }

    $brands = wp_get_post_terms( get_the_ID(), 'car_brand' );
    $models = wp_get_post_terms( get_the_ID(), 'car_model' );
    $years = wp_get_post_terms( get_the_ID(), 'car_year' );
    $tags = wp_get_post_terms( get_the_ID(), 'photo_tag' );

    $votes = (int) get_post_meta( get_the_ID(), '_szr_vote_score', true );
    $gps_lat = get_post_meta( get_the_ID(), '_szr_gps_lat', true );
    $gps_lng = get_post_meta( get_the_ID(), '_szr_gps_lng', true );
    $taken_at = get_post_meta( get_the_ID(), '_szr_taken_at', true );

    $is_owner = ( get_current_user_id() === 1 );
    ?>

    <article id="post-<?php the_ID(); ?>" <?php post_class( 'single-photo' ); ?>>
        <div class="photo-header">
            <div class="container">
                <div class="photo-breadcrumb">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Accueil</a>
                    <span>/</span>
                    <?php if ( ! empty( $brands ) ) : ?>
                        <a href="<?php echo esc_url( get_term_link( $brands[0] ) ); ?>"><?php echo esc_html( $brands[0]->name ); ?></a>
                        <span>/</span>
                    <?php endif; ?>
                    <?php if ( ! empty( $models ) ) : ?>
                        <span><?php echo esc_html( $models[0]->name ); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="photo-main">
            <div class="container">
                <div class="photo-grid">
                    <!-- Colonne gauche : Image -->
                    <div class="photo-display">
                        <?php if ( has_post_thumbnail() ) : ?>
                            <div class="photo-image">
                                <?php the_post_thumbnail( 'full' ); ?>
                                <?php if ( $is_owner ) : ?>
                                    <div class="owner-badge">
                                        <span>📸 Photo de Rafael</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Votes -->
                        <div class="photo-votes">
                            <button class="vote-btn vote-up" data-post-id="<?php the_ID(); ?>" data-vote="up">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 4l-8 8h5v8h6v-8h5z"/>
                                </svg>
                            </button>
                            <span class="vote-count"><?php echo $votes; ?></span>
                            <button class="vote-btn vote-down" data-post-id="<?php the_ID(); ?>" data-vote="down">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 20l8-8h-5V4H9v8H4z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Colonne droite : Infos -->
                    <div class="photo-sidebar">
                        <div class="photo-title-section">
                            <h1 class="photo-title"><?php the_title(); ?></h1>

                            <div class="photo-author">
                                <span class="author-badge" style="background: <?php echo esc_attr( $user_color ); ?>"></span>
                                <span class="author-name"><?php the_author(); ?></span>
                                <span class="author-date"><?php echo get_the_date(); ?></span>
                            </div>
                        </div>

                        <!-- Informations véhicule -->
                        <?php if ( ! empty( $brands ) || ! empty( $models ) || ! empty( $years ) ) : ?>
                            <div class="photo-meta-box">
                                <h3>Informations</h3>
                                <div class="meta-grid">
                                    <?php if ( ! empty( $brands ) ) : ?>
                                        <div class="meta-item">
                                            <span class="meta-label">Marque</span>
                                            <span class="meta-value">
                                                <a href="<?php echo esc_url( get_term_link( $brands[0] ) ); ?>">
                                                    <?php echo esc_html( $brands[0]->name ); ?>
                                                </a>
                                            </span>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ( ! empty( $models ) ) : ?>
                                        <div class="meta-item">
                                            <span class="meta-label">Modèle</span>
                                            <span class="meta-value">
                                                <a href="<?php echo esc_url( get_term_link( $models[0] ) ); ?>">
                                                    <?php echo esc_html( $models[0]->name ); ?>
                                                </a>
                                            </span>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ( ! empty( $years ) ) : ?>
                                        <div class="meta-item">
                                            <span class="meta-label">Année</span>
                                            <span class="meta-value"><?php echo esc_html( $years[0]->name ); ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ( $taken_at ) : ?>
                                        <div class="meta-item">
                                            <span class="meta-label">Prise le</span>
                                            <span class="meta-value"><?php echo esc_html( date( 'd/m/Y', strtotime( $taken_at ) ) ); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Description -->
                        <?php if ( get_the_content() ) : ?>
                            <div class="photo-description">
                                <h3>Description</h3>
                                <div class="description-content">
                                    <?php the_content(); ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Tags -->
                        <?php if ( ! empty( $tags ) ) : ?>
                            <div class="photo-tags">
                                <h3>Tags</h3>
                                <div class="tags-list">
                                    <?php foreach ( $tags as $tag ) : ?>
                                        <a href="<?php echo esc_url( get_term_link( $tag ) ); ?>" class="tag">
                                            #<?php echo esc_html( $tag->name ); ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Localisation -->
                        <?php if ( $gps_lat && $gps_lng ) : ?>
                            <div class="photo-location">
                                <h3>📍 Localisation</h3>
                                <a href="<?php echo esc_url( home_url( '/carte/?photo=' . get_the_ID() ) ); ?>" class="location-link">
                                    Voir sur la carte
                                </a>
                            </div>
                        <?php endif; ?>

                        <!-- Actions -->
                        <div class="photo-actions">
                            <button class="action-btn share-btn">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92 1.61 0 2.92-1.31 2.92-2.92s-1.31-2.92-2.92-2.92z"/>
                                </svg>
                                Partager
                            </button>

                            <button class="action-btn report-btn" data-post-id="<?php the_ID(); ?>">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M14.4 6L14 4H5v17h2v-7h5.6l.4 2h7V6z"/>
                                </svg>
                                Signaler
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Commentaires -->
        <div class="photo-comments">
            <div class="container">
                <h2 class="comments-title">Commentaires</h2>
                <?php
                if ( comments_open() || get_comments_number() ) :
                    comments_template();
                endif;
                ?>
            </div>
        </div>

        <!-- Photos similaires -->
        <?php
        $similar_args = array(
            'post_type'      => 'car_photo',
            'posts_per_page' => 4,
            'post__not_in'   => array( get_the_ID() ),
            'orderby'        => 'rand',
        );

        if ( ! empty( $brands ) ) {
            $similar_args['tax_query'] = array(
                array(
                    'taxonomy' => 'car_brand',
                    'field'    => 'term_id',
                    'terms'    => $brands[0]->term_id,
                ),
            );
        }

        $similar = new WP_Query( $similar_args );

        if ( $similar->have_posts() ) :
            ?>
            <div class="similar-photos">
                <div class="container">
                    <h2 class="section-title">Photos similaires</h2>
                    <div class="gallery-grid">
                        <?php
                        while ( $similar->have_posts() ) :
                            $similar->the_post();
                            ?>
                            <div class="gallery-item">
                                <a href="<?php the_permalink(); ?>">
                                    <?php if ( has_post_thumbnail() ) : ?>
                                        <?php the_post_thumbnail( 'medium' ); ?>
                                    <?php endif; ?>
                                    <div class="gallery-overlay">
                                        <div class="gallery-info">
                                            <h4><?php the_title(); ?></h4>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <?php
                        endwhile;
                        wp_reset_postdata();
                        ?>
                    </div>
                </div>
            </div>
            <?php
        endif;
        ?>
    </article>

    <?php
endwhile;
?>

<style>
/* Single Photo Styles */
.single-photo {
    background: var(--dark);
}

.photo-header {
    background: var(--dark-gray);
    padding: 2rem 0 1rem;
    border-bottom: 1px solid rgba(255, 0, 85, 0.2);
}

.photo-breadcrumb {
    display: flex;
    gap: 0.5rem;
    align-items: center;
    color: var(--text-muted);
    font-size: 0.9rem;
}

.photo-breadcrumb a {
    color: var(--text-muted);
    transition: color 0.3s ease;
}

.photo-breadcrumb a:hover {
    color: var(--primary);
}

.photo-main {
    padding: 3rem 0;
}

.photo-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 3rem;
}

.photo-display {
    position: relative;
}

.photo-image {
    position: relative;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
}

.photo-image img {
    width: 100%;
    height: auto;
    display: block;
}

.owner-badge {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: linear-gradient(135deg, var(--primary), #ff3377);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 50px;
    font-weight: 700;
    font-size: 0.9rem;
    box-shadow: 0 4px 20px rgba(255, 0, 85, 0.4);
}

.photo-votes {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
    margin-top: 2rem;
    padding: 1.5rem;
    background: var(--dark-gray);
    border-radius: 15px;
}

.vote-btn {
    background: transparent;
    border: 2px solid var(--text-muted);
    color: var(--text-muted);
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.vote-btn:hover {
    border-color: var(--primary);
    color: var(--primary);
    transform: scale(1.1);
}

.vote-btn.voted {
    border-color: var(--primary);
    color: var(--primary);
    background: rgba(255, 0, 85, 0.1);
}

.vote-count {
    font-size: 2rem;
    font-weight: 900;
    min-width: 60px;
    text-align: center;
}

.photo-sidebar {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.photo-title-section {
    padding-bottom: 2rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.photo-title {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    line-height: 1.2;
}

.photo-author {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--text-muted);
}

.author-badge {
    width: 16px;
    height: 16px;
    border-radius: 50%;
}

.author-name {
    font-weight: 600;
}

.photo-meta-box,
.photo-description,
.photo-tags,
.photo-location {
    background: var(--dark-gray);
    padding: 1.5rem;
    border-radius: 15px;
    border: 1px solid rgba(255, 0, 85, 0.1);
}

.photo-meta-box h3,
.photo-description h3,
.photo-tags h3,
.photo-location h3 {
    font-size: 1.3rem;
    margin-bottom: 1rem;
}

.meta-grid {
    display: grid;
    gap: 1rem;
}

.meta-item {
    display: flex;
    justify-content: space-between;
    padding: 0.8rem 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.meta-item:last-child {
    border-bottom: none;
}

.meta-label {
    color: var(--text-muted);
    font-size: 0.9rem;
}

.meta-value {
    font-weight: 600;
}

.meta-value a {
    color: var(--primary);
    transition: color 0.3s ease;
}

.meta-value a:hover {
    color: var(--secondary);
}

.description-content {
    color: var(--text-muted);
    line-height: 1.8;
}

.tags-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.tag {
    background: rgba(255, 0, 85, 0.1);
    color: var(--primary);
    padding: 0.5rem 1rem;
    border-radius: 50px;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.tag:hover {
    background: var(--primary);
    color: white;
}

.location-link {
    display: block;
    background: linear-gradient(135deg, var(--primary), #ff3377);
    color: white;
    padding: 0.8rem 1.5rem;
    border-radius: 50px;
    text-align: center;
    font-weight: 600;
    transition: all 0.3s ease;
}

.location-link:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(255, 0, 85, 0.4);
}

.photo-actions {
    display: flex;
    gap: 1rem;
}

.action-btn {
    flex: 1;
    background: var(--dark-gray);
    border: 1px solid rgba(255, 0, 85, 0.1);
    color: var(--text-muted);
    padding: 0.8rem 1rem;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.action-btn:hover {
    border-color: var(--primary);
    color: var(--primary);
}

.photo-comments {
    padding: 4rem 0;
    background: var(--dark-gray);
    border-top: 1px solid rgba(255, 0, 85, 0.2);
}

.comments-title {
    font-size: 2rem;
    margin-bottom: 2rem;
}

.similar-photos {
    padding: 4rem 0;
}

.similar-photos .section-title {
    margin-bottom: 3rem;
}

/* Responsive */
@media (max-width: 968px) {
    .photo-grid {
        grid-template-columns: 1fr;
    }

    .photo-title {
        font-size: 2rem;
    }

    .photo-votes {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 100;
        margin-top: 0;
        border-radius: 0;
        border-top: 1px solid rgba(255, 0, 85, 0.2);
    }

    body {
        padding-bottom: 80px;
    }
}
</style>

<script>
// Vote AJAX
(function() {
    const voteButtons = document.querySelectorAll('.vote-btn');

    voteButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const postId = this.dataset.postId;
            const voteType = this.dataset.vote;

            fetch('<?php echo admin_url( 'admin-ajax.php' ); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=szr_vote&post_id=${postId}&vote=${voteType}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.querySelector('.vote-count').textContent = data.data.score;

                    voteButtons.forEach(b => b.classList.remove('voted'));

                    if (data.data.user_vote === 'up') {
                        document.querySelector('.vote-up').classList.add('voted');
                    } else if (data.data.user_vote === 'down') {
                        document.querySelector('.vote-down').classList.add('voted');
                    }
                }
            });
        });
    });

    // Report button
    const reportBtn = document.querySelector('.report-btn');
    if (reportBtn) {
        reportBtn.addEventListener('click', function() {
            const postId = this.dataset.postId;
            const reason = prompt('Raison du signalement :');

            if (reason) {
                fetch('<?php echo admin_url( 'admin-ajax.php' ); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=szr_report&post_id=${postId}&reason=${encodeURIComponent(reason)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Merci pour votre signalement.');
                    }
                });
            }
        });
    }

    // Share button
    const shareBtn = document.querySelector('.share-btn');
    if (shareBtn) {
        shareBtn.addEventListener('click', function() {
            if (navigator.share) {
                navigator.share({
                    title: document.title,
                    url: window.location.href
                });
            } else {
                navigator.clipboard.writeText(window.location.href);
                alert('Lien copié dans le presse-papier !');
            }
        });
    }
})();
</script>

<?php
get_footer();
