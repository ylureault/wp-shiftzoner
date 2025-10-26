<?php
/**
 * The main template file
 *
 * @package ShiftZoneR
 */

get_header();
?>

<main id="main-content" class="site-main">
    <div class="container">
        <?php
        if ( have_posts() ) :
            ?>
            <div class="posts-grid">
                <?php
                while ( have_posts() ) :
                    the_post();
                    ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card' ); ?>>
                        <?php if ( has_post_thumbnail() ) : ?>
                            <div class="post-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail( 'large' ); ?>
                                </a>
                            </div>
                        <?php endif; ?>

                        <div class="post-content">
                            <h2 class="post-title">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_title(); ?>
                                </a>
                            </h2>

                            <div class="post-meta">
                                <span class="post-author">
                                    <?php
                                    $author_id = get_the_author_meta( 'ID' );
                                    $user_color = get_user_meta( $author_id, '_szr_user_color', true );
                                    if ( ! $user_color ) {
                                        $user_color = '#888888';
                                    }
                                    ?>
                                    <span class="author-badge" style="background: <?php echo esc_attr( $user_color ); ?>"></span>
                                    <?php the_author(); ?>
                                </span>
                                <span class="post-date"><?php echo get_the_date(); ?></span>
                            </div>

                            <div class="post-excerpt">
                                <?php the_excerpt(); ?>
                            </div>

                            <a href="<?php the_permalink(); ?>" class="read-more">
                                Voir plus →
                            </a>
                        </div>
                    </article>
                    <?php
                endwhile;
                ?>
            </div>

            <?php
            // Pagination
            the_posts_pagination(
                array(
                    'mid_size'  => 2,
                    'prev_text' => '← Précédent',
                    'next_text' => 'Suivant →',
                )
            );
            ?>

        <?php
        else :
            ?>
            <div class="no-posts">
                <h2>Aucun contenu trouvé</h2>
                <p>Il n'y a pas encore de contenu à afficher.</p>
            </div>
            <?php
        endif;
        ?>
    </div>
</main>

<style>
.posts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 2rem;
    margin: 3rem 0;
}

.post-card {
    background: var(--dark-gray);
    border-radius: 20px;
    overflow: hidden;
    border: 1px solid rgba(255, 0, 85, 0.1);
    transition: all 0.3s ease;
}

.post-card:hover {
    transform: translateY(-10px);
    border-color: var(--primary);
    box-shadow: 0 20px 60px rgba(255, 0, 85, 0.2);
}

.post-thumbnail {
    position: relative;
    aspect-ratio: 4/3;
    overflow: hidden;
}

.post-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.post-card:hover .post-thumbnail img {
    transform: scale(1.1);
}

.post-content {
    padding: 1.5rem;
}

.post-title {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
}

.post-title a {
    color: var(--text);
    text-decoration: none;
}

.post-title a:hover {
    color: var(--primary);
}

.post-meta {
    display: flex;
    gap: 1rem;
    align-items: center;
    color: var(--text-muted);
    font-size: 0.9rem;
    margin-bottom: 1rem;
}

.post-author {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.author-badge {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    display: inline-block;
}

.post-excerpt {
    color: var(--text-muted);
    line-height: 1.6;
    margin-bottom: 1rem;
}

.read-more {
    color: var(--primary);
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
}

.read-more:hover {
    color: var(--secondary);
    transform: translateX(5px);
    display: inline-block;
}

.no-posts {
    text-align: center;
    padding: 4rem 2rem;
}

.no-posts h2 {
    font-size: 2rem;
    margin-bottom: 1rem;
}

.no-posts p {
    color: var(--text-muted);
}

/* Pagination */
.pagination {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
    margin: 3rem 0;
}

.pagination .page-numbers {
    padding: 0.8rem 1.2rem;
    background: var(--dark-gray);
    border: 1px solid rgba(255, 0, 85, 0.1);
    border-radius: 10px;
    color: var(--text);
    text-decoration: none;
    transition: all 0.3s ease;
}

.pagination .page-numbers:hover,
.pagination .page-numbers.current {
    background: var(--primary);
    border-color: var(--primary);
    transform: translateY(-2px);
}

@media (max-width: 768px) {
    .posts-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php
get_footer();
