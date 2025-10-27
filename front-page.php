<?php
/**
 * Template Name: Page d'accueil ShiftZoneR
 * The front page template
 *
 * @package ShiftZoneR
 */

get_header();

// Récupérer les photos de Rafael (admin)
$rafael_photos = new WP_Query( array(
    'post_type'      => 'car_photo',
    'posts_per_page' => 8,
    'author'         => 1, // Supposant que l'admin est l'utilisateur ID 1
    'orderby'        => 'date',
    'order'          => 'DESC',
) );

// Récupérer les photos de la communauté
$community_photos = new WP_Query( array(
    'post_type'      => 'car_photo',
    'posts_per_page' => 8,
    'author__not_in' => array( 1 ),
    'orderby'        => 'meta_value_num',
    'meta_key'       => '_szr_vote_score',
    'order'          => 'DESC',
) );
?>

<!-- Hero Section -->
<section class="hero" id="accueil">
    <div class="hero-content">
        <div class="hero-text">
            <h1><?php echo esc_html( get_theme_mod( 'shiftzoner_hero_title', 'Partagez Votre Passion Automobile' ) ); ?></h1>
            <p><?php echo esc_html( get_theme_mod( 'shiftzoner_hero_subtitle', 'Rejoignez la communauté ShiftZoneR. Partagez vos plus beaux clichés automobiles, découvrez des passionnés et échangez autour de votre passion.' ) ); ?></p>
            <div class="hero-buttons">
                <?php if ( is_user_logged_in() ) : ?>
                    <a href="<?php echo esc_url( home_url( '/soumettre-ma-photo/' ) ); ?>" class="cta-button">Publier une photo</a>
                    <a href="<?php echo esc_url( home_url( '/galerie/' ) ); ?>" class="secondary-button">Explorer la Galerie</a>
                <?php else : ?>
                    <a href="<?php echo esc_url( wp_registration_url() ); ?>" class="cta-button">Rejoindre Maintenant</a>
                    <a href="<?php echo esc_url( home_url( '/galerie/' ) ); ?>" class="secondary-button">Explorer la Galerie</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="hero-grid">
            <?php
            $hero_photos = get_posts( array(
                'post_type'      => 'car_photo',
                'posts_per_page' => 4,
                'orderby'        => 'rand',
            ) );

            foreach ( $hero_photos as $index => $photo ) :
                $thumbnail = get_the_post_thumbnail_url( $photo->ID, 'medium' );
                if ( $thumbnail ) :
                    ?>
                    <div class="photo-card">
                        <img src="<?php echo esc_url( $thumbnail ); ?>" alt="<?php echo esc_attr( $photo->post_title ); ?>">
                    </div>
                    <?php
                endif;
            endforeach;
            wp_reset_postdata();
            ?>
        </div>
    </div>
</section>

<!-- Section Dernières Photos -->
<section class="latest-photos-section section-spacing" id="dernieres">
    <div class="container">
        <h2 class="section-title fade-in">Dernières Photos</h2>
        <p class="section-subtitle fade-in">Découvrez les toutes dernières photos partagées par la communauté</p>

        <?php
        $latest_photos = new WP_Query( array(
            'post_type'      => 'car_photo',
            'posts_per_page' => 12,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'post_status'    => 'publish',
        ) );

        if ( $latest_photos->have_posts() ) : ?>
            <div class="gallery-grid">
                <?php
                while ( $latest_photos->have_posts() ) :
                    $latest_photos->the_post();
                    $brands = wp_get_post_terms( get_the_ID(), 'car_brand' );
                    $models = wp_get_post_terms( get_the_ID(), 'car_model' );
                    $votes = (int) get_post_meta( get_the_ID(), '_szr_vote_score', true );
                    $comments_count = get_comments_number();
                    $author_id = get_the_author_meta( 'ID' );
                    $user_color = get_user_meta( $author_id, '_szr_user_color', true );
                    ?>
                    <div class="gallery-item fade-in">
                        <a href="<?php the_permalink(); ?>">
                            <?php if ( has_post_thumbnail() ) : ?>
                                <?php the_post_thumbnail( 'large' ); ?>
                            <?php endif; ?>
                            <div class="gallery-overlay">
                                <div class="gallery-info">
                                    <div class="author-info">
                                        <span class="author-badge" style="background: <?php echo esc_attr( $user_color ? $user_color : '#888888' ); ?>"></span>
                                        <span><?php the_author(); ?></span>
                                    </div>
                                    <h4>
                                        <?php
                                        if ( ! empty( $brands ) && ! empty( $models ) ) {
                                            echo esc_html( $brands[0]->name . ' ' . $models[0]->name );
                                        } else {
                                            the_title();
                                        }
                                        ?>
                                    </h4>
                                    <div class="gallery-stats">
                                        <span>❤️ <?php echo $votes; ?></span>
                                        <span>💬 <?php echo $comments_count; ?></span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php
                endwhile;
                wp_reset_postdata();
                ?>
            </div>
        <?php else : ?>
            <p class="no-content">Aucune photo disponible pour le moment.</p>
        <?php endif; ?>

        <div class="section-cta">
            <a href="<?php echo esc_url( home_url( '/galerie/' ) ); ?>" class="secondary-button">Voir toutes les photos</a>
        </div>
    </div>
</section>

<!-- Section Photos de Rafael -->
<section class="rafael-section section-spacing" id="rafael">
    <div class="container">
        <h2 class="section-title fade-in"><?php echo esc_html( get_theme_mod( 'shiftzoner_rafael_title', 'Photos de Rafael' ) ); ?></h2>
        <p class="section-subtitle fade-in"><?php echo esc_html( get_theme_mod( 'shiftzoner_rafael_subtitle', 'Découvrez les photos exclusives du créateur de ShiftZoneR' ) ); ?></p>

        <?php if ( $rafael_photos->have_posts() ) : ?>
            <div class="gallery-grid">
                <?php
                while ( $rafael_photos->have_posts() ) :
                    $rafael_photos->the_post();
                    $brands = wp_get_post_terms( get_the_ID(), 'car_brand' );
                    $models = wp_get_post_terms( get_the_ID(), 'car_model' );
                    $votes = (int) get_post_meta( get_the_ID(), '_szr_vote_score', true );
                    $comments_count = get_comments_number();
                    ?>
                    <div class="gallery-item fade-in">
                        <a href="<?php the_permalink(); ?>">
                            <?php if ( has_post_thumbnail() ) : ?>
                                <?php the_post_thumbnail( 'large' ); ?>
                            <?php endif; ?>
                            <div class="gallery-overlay">
                                <div class="gallery-info">
                                    <h4>
                                        <?php
                                        if ( ! empty( $brands ) && ! empty( $models ) ) {
                                            echo esc_html( $brands[0]->name . ' ' . $models[0]->name );
                                        } else {
                                            the_title();
                                        }
                                        ?>
                                    </h4>
                                    <div class="gallery-stats">
                                        <span>❤️ <?php echo $votes; ?></span>
                                        <span>💬 <?php echo $comments_count; ?></span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php
                endwhile;
                wp_reset_postdata();
                ?>
            </div>
        <?php else : ?>
            <p class="no-content">Aucune photo disponible pour le moment.</p>
        <?php endif; ?>

        <div class="section-cta">
            <a href="<?php echo esc_url( home_url( '/galerie/' ) ); ?>" class="secondary-button">Voir toutes les photos de Rafael</a>
        </div>
    </div>
</section>

<!-- Section Communauté -->
<section class="community-section section-spacing" id="communaute">
    <div class="container">
        <h2 class="section-title fade-in"><?php echo esc_html( get_theme_mod( 'shiftzoner_community_title', 'La Communauté ShiftZoneR' ) ); ?></h2>
        <p class="section-subtitle fade-in"><?php echo esc_html( get_theme_mod( 'shiftzoner_community_subtitle', 'Les plus belles photos partagées par notre communauté de passionnés' ) ); ?></p>

        <?php if ( $community_photos->have_posts() ) : ?>
            <div class="gallery-grid">
                <?php
                while ( $community_photos->have_posts() ) :
                    $community_photos->the_post();
                    $brands = wp_get_post_terms( get_the_ID(), 'car_brand' );
                    $models = wp_get_post_terms( get_the_ID(), 'car_model' );
                    $votes = (int) get_post_meta( get_the_ID(), '_szr_vote_score', true );
                    $comments_count = get_comments_number();
                    $author_id = get_the_author_meta( 'ID' );
                    $user_color = get_user_meta( $author_id, '_szr_user_color', true );
                    ?>
                    <div class="gallery-item fade-in">
                        <a href="<?php the_permalink(); ?>">
                            <?php if ( has_post_thumbnail() ) : ?>
                                <?php the_post_thumbnail( 'large' ); ?>
                            <?php endif; ?>
                            <div class="gallery-overlay">
                                <div class="gallery-info">
                                    <div class="author-info">
                                        <span class="author-badge" style="background: <?php echo esc_attr( $user_color ? $user_color : '#888888' ); ?>"></span>
                                        <span><?php the_author(); ?></span>
                                    </div>
                                    <h4>
                                        <?php
                                        if ( ! empty( $brands ) && ! empty( $models ) ) {
                                            echo esc_html( $brands[0]->name . ' ' . $models[0]->name );
                                        } else {
                                            the_title();
                                        }
                                        ?>
                                    </h4>
                                    <div class="gallery-stats">
                                        <span>❤️ <?php echo $votes; ?></span>
                                        <span>💬 <?php echo $comments_count; ?></span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php
                endwhile;
                wp_reset_postdata();
                ?>
            </div>
        <?php else : ?>
            <p class="no-content">Aucune photo de la communauté disponible pour le moment.</p>
        <?php endif; ?>

        <div class="section-cta">
            <a href="<?php echo esc_url( home_url( '/galerie/' ) ); ?>" class="secondary-button">Voir toute la galerie</a>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features" id="fonctionnalites">
    <div class="container">
        <h2 class="section-title fade-in">Pourquoi ShiftZoneR ?</h2>
        <p class="section-subtitle fade-in">Une plateforme conçue pour les passionnés par des passionnés</p>
        <div class="features-grid">
            <div class="feature-card fade-in">
                <div class="feature-icon">📸</div>
                <h3>Partagez Sans Limites</h3>
                <p>Uploadez vos photos automobiles en haute résolution. Organisez vos collections par marque et modèle.</p>
            </div>
            <div class="feature-card fade-in">
                <div class="feature-icon">💬</div>
                <h3>Discussions Passionnées</h3>
                <p>Échangez avec la communauté, recevez des feedbacks et partagez vos techniques photographiques.</p>
            </div>
            <div class="feature-card fade-in">
                <div class="feature-icon">🔥</div>
                <h3>Trending & Votes</h3>
                <p>Découvrez les photos les plus populaires et votez pour vos créations préférées.</p>
            </div>
            <div class="feature-card fade-in">
                <div class="feature-icon">🗺️</div>
                <h3>Carte Interactive</h3>
                <p>Explorez les lieux de prise de vue sur une carte interactive avec géolocalisation.</p>
            </div>
            <div class="feature-card fade-in">
                <div class="feature-icon">⚡</div>
                <h3>Interface Moderne</h3>
                <p>Une expérience utilisateur fluide et responsive pour tous vos appareils.</p>
            </div>
            <div class="feature-card fade-in">
                <div class="feature-icon">🏆</div>
                <h3>Système de Karma</h3>
                <p>Gagnez en réputation grâce à vos contributions et devenez un membre reconnu.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section" id="rejoindre">
    <div class="cta-content">
        <h2 class="fade-in"><?php echo esc_html( get_theme_mod( 'shiftzoner_cta_title', 'Prêt À Rejoindre La Communauté ?' ) ); ?></h2>
        <p class="fade-in"><?php echo esc_html( get_theme_mod( 'shiftzoner_cta_subtitle', 'Créez votre compte gratuitement et commencez à partager vos plus belles créations dès aujourd\'hui.' ) ); ?></p>
        <?php if ( is_user_logged_in() ) : ?>
            <a href="<?php echo esc_url( home_url( '/soumettre-ma-photo/' ) ); ?>" class="cta-button">Publier une photo</a>
        <?php else : ?>
            <a href="<?php echo esc_url( wp_registration_url() ); ?>" class="cta-button">S'inscrire Gratuitement</a>
        <?php endif; ?>
    </div>
</section>

<style>
/* Hero Section */
.hero {
    min-height: 80vh;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    padding: 4rem 2rem 2rem;
    overflow: hidden;
    background: var(--dark);
}

.hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(circle at 50% 50%, rgba(255, 0, 85, 0.1), transparent 70%);
    animation: pulse 4s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 0.5; }
    50% { opacity: 1; }
}

.hero-content {
    max-width: 1400px;
    width: 100%;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4rem;
    align-items: center;
    position: relative;
    z-index: 1;
}

.hero-text h1 {
    font-size: 4.5rem;
    font-weight: 900;
    line-height: 1.1;
    margin-bottom: 1.5rem;
    background: linear-gradient(135deg, #ffffff, var(--secondary));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    animation: slideInLeft 1s ease-out;
}

@keyframes slideInLeft {
    from { opacity: 0; transform: translateX(-50px); }
    to { opacity: 1; transform: translateX(0); }
}

.hero-text p {
    font-size: 1.3rem;
    color: var(--text-muted);
    margin-bottom: 2.5rem;
    animation: slideInLeft 1s ease-out 0.2s backwards;
}

.hero-buttons {
    display: flex;
    gap: 1.5rem;
    animation: slideInLeft 1s ease-out 0.4s backwards;
}

/* Photo Grid Animation */
.hero-grid {
    position: relative;
    height: 600px;
    animation: slideInRight 1s ease-out;
}

@keyframes slideInRight {
    from { opacity: 0; transform: translateX(50px); }
    to { opacity: 1; transform: translateX(0); }
}

.photo-card {
    position: absolute;
    width: 250px;
    height: 250px;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
    transition: all 0.3s ease;
    cursor: pointer;
    border: 2px solid transparent;
}

.photo-card:hover {
    transform: scale(1.05) rotate(2deg);
    border-color: var(--primary);
    box-shadow: 0 20px 60px rgba(255, 0, 85, 0.3);
    z-index: 10;
}

.photo-card img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.photo-card:hover img {
    transform: scale(1.1);
}

.photo-card:nth-child(1) {
    top: 50px;
    left: 50px;
    animation: float 6s ease-in-out infinite;
}

.photo-card:nth-child(2) {
    top: 100px;
    right: 100px;
    animation: float 6s ease-in-out infinite 1s;
}

.photo-card:nth-child(3) {
    bottom: 150px;
    left: 100px;
    animation: float 6s ease-in-out infinite 2s;
}

.photo-card:nth-child(4) {
    bottom: 80px;
    right: 50px;
    animation: float 6s ease-in-out infinite 3s;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

/* Sections */
.section-spacing {
    padding: 6rem 2rem;
}

.latest-photos-section {
    background: var(--dark);
}

.rafael-section {
    background: var(--dark-gray);
    position: relative;
}

.rafael-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, var(--primary), transparent);
}

.community-section {
    background: var(--dark);
}

/* Gallery Grid */
.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-top: 4rem;
}

.gallery-item {
    position: relative;
    border-radius: 15px;
    overflow: hidden;
    aspect-ratio: 1;
    cursor: pointer;
    transition: all 0.3s ease;
}

.gallery-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.gallery-item:hover img {
    transform: scale(1.1);
}

.gallery-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(to top, rgba(0, 0, 0, 0.9), transparent);
    opacity: 0;
    transition: opacity 0.3s ease;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    padding: 2rem;
}

.gallery-item:hover .gallery-overlay {
    opacity: 1;
}

.gallery-info h4 {
    font-size: 1.2rem;
    margin-bottom: 0.5rem;
}

.gallery-stats {
    display: flex;
    gap: 1.5rem;
    color: var(--text-muted);
    font-size: 0.9rem;
}

.author-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
    color: var(--text-muted);
}

.author-badge {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    display: inline-block;
}

.section-cta {
    text-align: center;
    margin-top: 3rem;
}

.no-content {
    text-align: center;
    color: var(--text-muted);
    padding: 3rem;
}

/* Features Section */
.features {
    padding: 6rem 2rem;
    background: var(--dark-gray);
    position: relative;
}

.features::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, var(--primary), transparent);
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 3rem;
}

.feature-card {
    background: var(--light-gray);
    padding: 3rem;
    border-radius: 20px;
    border: 1px solid rgba(255, 0, 85, 0.1);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.feature-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 0, 85, 0.1), transparent);
    transition: left 0.5s ease;
}

.feature-card:hover::before {
    left: 100%;
}

.feature-card:hover {
    transform: translateY(-10px);
    border-color: var(--primary);
    box-shadow: 0 20px 60px rgba(255, 0, 85, 0.2);
}

.feature-icon {
    font-size: 3rem;
    margin-bottom: 1.5rem;
    display: inline-block;
}

.feature-card h3 {
    font-size: 1.8rem;
    margin-bottom: 1rem;
    color: var(--text);
}

.feature-card p {
    color: var(--text-muted);
    line-height: 1.8;
}

/* CTA Section */
.cta-section {
    padding: 6rem 2rem;
    background: linear-gradient(135deg, var(--dark-gray), var(--dark));
    position: relative;
    overflow: hidden;
}

.cta-section::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 800px;
    height: 800px;
    background: radial-gradient(circle, rgba(255, 0, 85, 0.2), transparent 70%);
    animation: rotate 20s linear infinite;
}

@keyframes rotate {
    from { transform: translate(-50%, -50%) rotate(0deg); }
    to { transform: translate(-50%, -50%) rotate(360deg); }
}

.cta-content {
    max-width: 800px;
    margin: 0 auto;
    text-align: center;
    position: relative;
    z-index: 1;
}

.cta-content h2 {
    font-size: 3.5rem;
    font-weight: 900;
    margin-bottom: 1.5rem;
    background: linear-gradient(135deg, var(--text), var(--secondary));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.cta-content p {
    font-size: 1.3rem;
    color: var(--text-muted);
    margin-bottom: 3rem;
}

/* Responsive */
@media (max-width: 968px) {
    .hero-content {
        grid-template-columns: 1fr;
        text-align: center;
    }

    .hero-text h1 {
        font-size: 3rem;
    }

    .hero-grid {
        display: none;
    }

    .hero-buttons {
        justify-content: center;
        flex-wrap: wrap;
    }

    .features-grid,
    .gallery-grid {
        grid-template-columns: 1fr;
    }

    .cta-content h2 {
        font-size: 2.5rem;
    }
}
</style>

<?php
get_footer();
