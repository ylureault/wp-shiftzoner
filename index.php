<?php
/**
 * The main template file
 *
 * @package ShiftZoneR
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<main id="main" class="site-main">
    <?php
    if (have_posts()) :
        while (have_posts()) :
            the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="entry-header">
                    <?php
                    if (is_singular()) :
                        the_title('<h1 class="entry-title">', '</h1>');
                    else :
                        the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '">', '</a></h2>');
                    endif;
                    ?>
                </header>

                <div class="entry-content">
                    <?php
                    the_content();

                    wp_link_pages(array(
                        'before' => '<div class="page-links">' . esc_html__('Pages:', 'shiftzoner'),
                        'after'  => '</div>',
                    ));
                    ?>
                </div>
            </article>
            <?php
        endwhile;

        the_posts_navigation();
    else :
        ?>
        <section class="no-results not-found">
            <header class="page-header">
                <h1 class="page-title"><?php esc_html_e('Aucun contenu trouvé', 'shiftzoner'); ?></h1>
            </header>
            <div class="page-content">
                <p><?php esc_html_e('Aucun contenu n\'a été trouvé à cet emplacement.', 'shiftzoner'); ?></p>
            </div>
        </section>
        <?php
    endif;
    ?>
</main>

<?php
get_footer();
