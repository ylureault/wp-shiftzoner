<?php
/**
 * Template part for displaying photo cards
 *
 * @package ShiftZoneR
 */

$author_id = get_the_author_meta( 'ID' );
$user_color = get_user_meta( $author_id, '_szr_user_color', true );
if ( ! $user_color ) {
    $user_color = '#888888';
}

$brands = wp_get_post_terms( get_the_ID(), 'car_brand' );
$models = wp_get_post_terms( get_the_ID(), 'car_model' );
$votes = (int) get_post_meta( get_the_ID(), '_szr_vote_score', true );
$comments_count = get_comments_number();
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'photo-card' ); ?>>
    <a href="<?php the_permalink(); ?>" class="photo-card-link">
        <div class="photo-card-image">
            <?php if ( has_post_thumbnail() ) : ?>
                <?php the_post_thumbnail( 'large' ); ?>
            <?php else : ?>
                <div class="no-image">📸</div>
            <?php endif; ?>

            <div class="photo-card-overlay">
                <div class="overlay-content">
                    <h3 class="overlay-title">
                        <?php
                        if ( ! empty( $brands ) && ! empty( $models ) ) {
                            echo esc_html( $brands[0]->name . ' ' . $models[0]->name );
                        } else {
                            the_title();
                        }
                        ?>
                    </h3>
                    <div class="overlay-stats">
                        <span>❤️ <?php echo $votes; ?></span>
                        <span>💬 <?php echo $comments_count; ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="photo-card-content">
            <div class="photo-card-meta">
                <span class="photo-card-author">
                    <span class="author-badge" style="background: <?php echo esc_attr( $user_color ); ?>"></span>
                    <?php the_author(); ?>
                </span>
                <span class="photo-card-date"><?php echo human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ); ?> ago</span>
            </div>

            <h2 class="photo-card-title">
                <?php
                if ( ! empty( $brands ) && ! empty( $models ) ) {
                    echo esc_html( $brands[0]->name . ' ' . $models[0]->name );
                } else {
                    the_title();
                }
                ?>
            </h2>

            <?php if ( has_excerpt() ) : ?>
                <div class="photo-card-excerpt">
                    <?php echo wp_trim_words( get_the_excerpt(), 15 ); ?>
                </div>
            <?php endif; ?>

            <div class="photo-card-stats">
                <span class="stat">
                    ❤️ <?php echo $votes; ?>
                </span>
                <span class="stat">
                    💬 <?php echo $comments_count; ?>
                </span>
                <?php
                $views = get_post_meta( get_the_ID(), '_szr_views', true );
                if ( $views ) :
                    ?>
                    <span class="stat">
                        👁️ <?php echo number_format_i18n( $views ); ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </a>
</article>
