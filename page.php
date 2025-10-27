<?php
/**
 * Default Page Template
 *
 * @package ShiftZoneR
 */

get_header();
?>

<div class="default-page">
    <?php while ( have_posts() ) : the_post(); ?>

        <!-- Page Header -->
        <div class="page-header-section">
            <div class="container">
                <h1 class="page-main-title"><?php the_title(); ?></h1>

                <?php if ( has_excerpt() ) : ?>
                    <p class="page-intro"><?php the_excerpt(); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Page Content -->
        <article id="post-<?php the_ID(); ?>" <?php post_class( 'page-content-section' ); ?>>
            <div class="container">
                <div class="page-content-wrapper">

                    <!-- Breadcrumb -->
                    <div class="page-breadcrumb">
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Accueil</a>
                        <span class="separator">›</span>
                        <span class="current"><?php the_title(); ?></span>
                    </div>

                    <!-- Content -->
                    <div class="page-main-content">
                        <?php the_content(); ?>
                    </div>

                    <!-- Featured Image if exists -->
                    <?php if ( has_post_thumbnail() ) : ?>
                        <div class="page-featured-image">
                            <?php the_post_thumbnail( 'large' ); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Page Meta Info -->
                    <div class="page-meta-info">
                        <div class="meta-item">
                            <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                            </svg>
                            Mis à jour le <?php echo get_the_modified_date(); ?>
                        </div>
                    </div>

                    <!-- Comments if enabled -->
                    <?php
                    if ( comments_open() || get_comments_number() ) :
                        comments_template();
                    endif;
                    ?>
                </div>
            </div>
        </article>

    <?php endwhile; ?>
</div>

<style>
.default-page {
    background: var(--dark);
    min-height: calc(100vh - 85px);
}

.page-header-section {
    background: linear-gradient(135deg, var(--dark-gray) 0%, var(--dark) 100%);
    padding: 4rem 0 3rem;
    text-align: center;
    border-bottom: 2px solid rgba(255, 0, 85, 0.2);
    position: relative;
    overflow: hidden;
}

.page-header-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(circle at 50% 50%, rgba(255, 0, 85, 0.1) 0%, transparent 70%);
    pointer-events: none;
}

.page-main-title {
    font-size: 3.5rem;
    font-weight: 900;
    margin-bottom: 1rem;
    background: linear-gradient(135deg, var(--text) 0%, var(--secondary) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    position: relative;
    z-index: 1;
}

.page-intro {
    font-size: 1.3rem;
    color: var(--text-muted);
    max-width: 800px;
    margin: 0 auto;
    line-height: 1.6;
    position: relative;
    z-index: 1;
}

.page-content-section {
    padding: 3rem 0;
}

.page-content-wrapper {
    max-width: 900px;
    margin: 0 auto;
}

.page-breadcrumb {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    margin-bottom: 2rem;
    padding: 1rem 1.5rem;
    background: rgba(255, 255, 255, 0.03);
    border-radius: 15px;
    border: 1px solid rgba(255, 255, 255, 0.05);
}

.page-breadcrumb a {
    color: var(--text-muted);
    transition: color 0.3s ease;
}

.page-breadcrumb a:hover {
    color: var(--primary);
}

.page-breadcrumb .separator {
    color: var(--text-muted);
    opacity: 0.5;
}

.page-breadcrumb .current {
    color: var(--text);
    font-weight: 600;
}

.page-main-content {
    background: var(--dark-gray);
    padding: 3rem;
    border-radius: 20px;
    border: 1px solid rgba(255, 0, 85, 0.1);
    margin-bottom: 2rem;
    line-height: 1.8;
    font-size: 1.1rem;
}

.page-main-content h2 {
    font-size: 2rem;
    margin: 2rem 0 1rem;
    color: var(--text);
    position: relative;
    padding-bottom: 0.5rem;
}

.page-main-content h2::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 60px;
    height: 3px;
    background: linear-gradient(90deg, var(--primary), var(--secondary));
    border-radius: 3px;
}

.page-main-content h3 {
    font-size: 1.5rem;
    margin: 1.5rem 0 1rem;
    color: var(--text);
}

.page-main-content p {
    margin-bottom: 1.5rem;
    color: var(--text);
}

.page-main-content ul,
.page-main-content ol {
    margin-bottom: 1.5rem;
    padding-left: 2rem;
}

.page-main-content li {
    margin-bottom: 0.75rem;
    color: var(--text);
}

.page-main-content a {
    color: var(--primary);
    text-decoration: underline;
    transition: color 0.3s ease;
}

.page-main-content a:hover {
    color: var(--secondary);
}

.page-main-content blockquote {
    border-left: 4px solid var(--primary);
    padding-left: 1.5rem;
    margin: 2rem 0;
    font-style: italic;
    color: var(--text-muted);
}

.page-main-content img {
    max-width: 100%;
    height: auto;
    border-radius: 15px;
    margin: 2rem 0;
}

.page-main-content code {
    background: var(--dark);
    padding: 0.2rem 0.5rem;
    border-radius: 5px;
    font-family: 'Courier New', monospace;
    color: var(--secondary);
}

.page-main-content pre {
    background: var(--dark);
    padding: 1.5rem;
    border-radius: 10px;
    overflow-x: auto;
    margin: 2rem 0;
}

.page-main-content pre code {
    background: none;
    padding: 0;
}

.page-featured-image {
    margin-bottom: 2rem;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
}

.page-featured-image img {
    width: 100%;
    height: auto;
    display: block;
    margin: 0;
}

.page-meta-info {
    display: flex;
    align-items: center;
    gap: 2rem;
    padding: 1.5rem;
    background: rgba(255, 255, 255, 0.03);
    border-radius: 15px;
    border: 1px solid rgba(255, 255, 255, 0.05);
    margin-bottom: 2rem;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--text-muted);
    font-size: 0.95rem;
}

.meta-item svg {
    opacity: 0.7;
}

/* Responsive */
@media (max-width: 768px) {
    .page-main-title {
        font-size: 2.5rem;
    }

    .page-intro {
        font-size: 1.1rem;
    }

    .page-main-content {
        padding: 2rem 1.5rem;
        font-size: 1rem;
    }

    .page-main-content h2 {
        font-size: 1.75rem;
    }

    .page-main-content h3 {
        font-size: 1.3rem;
    }

    .page-breadcrumb {
        padding: 0.875rem 1rem;
        font-size: 0.85rem;
    }
}

@media (max-width: 480px) {
    .page-header-section {
        padding: 3rem 0 2rem;
    }

    .page-main-title {
        font-size: 2rem;
    }

    .page-intro {
        font-size: 1rem;
    }

    .page-main-content {
        padding: 1.5rem 1rem;
    }

    .page-meta-info {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
}
</style>

<?php
get_footer();
