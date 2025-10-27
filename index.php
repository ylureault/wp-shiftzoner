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
<style>
body{
    font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif;
    background:#ffffff;
    color:#0f172a;
    margin:0;
    padding:20px;
    line-height:1.6;
}
.site-main{
    max-width:800px;
    margin:0 auto;
}
.entry-title{
    font-size:2em;
    margin-bottom:0.5em;
}
.entry-title a{
    color:#0f172a;
    text-decoration:none;
}
.entry-title a:hover{
    color:#2563eb;
}
article{
    margin-bottom:3em;
}
.entry-content{
    margin-top:1em;
}
@media (prefers-color-scheme:dark){
    body{
        background:#0f172a;
        color:#e5e7eb;
    }
    .entry-title a{
        color:#e5e7eb;
    }
}
</style>

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
