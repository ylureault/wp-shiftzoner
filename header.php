<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- Header -->
<header id="site-header" class="site-header">
    <nav class="main-nav">
        <div class="nav-container">
            <div class="logo">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                    SHIFTZONER
                </a>
            </div>

            <ul class="nav-links">
                <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Accueil</a></li>
                <li><a href="<?php echo esc_url( home_url( '/galerie/' ) ); ?>">Explorer</a></li>
                <?php if ( function_exists( 'bbp_is_active' ) ) : ?>
                <li><a href="<?php echo esc_url( get_post_type_archive_link( 'forum' ) ); ?>">Communauté</a></li>
                <?php endif; ?>
                <li><a href="<?php echo esc_url( home_url( '/carte/' ) ); ?>">Carte</a></li>
                <?php if ( is_user_logged_in() ) : ?>
                <li><a href="<?php echo esc_url( bp_core_get_user_domain( get_current_user_id() ) ); ?>">Profil</a></li>
                <?php endif; ?>
            </ul>

            <div class="nav-actions">
                <?php if ( is_user_logged_in() ) : ?>
                    <a href="<?php echo esc_url( home_url( '/soumettre-photo/' ) ); ?>" class="cta-button">
                        Publier une photo
                    </a>
                    <a href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>" class="nav-logout">
                        Déconnexion
                    </a>
                <?php else : ?>
                    <a href="<?php echo esc_url( wp_login_url() ); ?>" class="secondary-button">
                        Connexion
                    </a>
                    <a href="<?php echo esc_url( wp_registration_url() ); ?>" class="cta-button">
                        Rejoindre
                    </a>
                <?php endif; ?>
            </div>

            <!-- Mobile Menu Toggle -->
            <button class="mobile-menu-toggle" aria-label="Menu" aria-expanded="false">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </nav>
</header>

<style>
/* Header Styles */
.site-header {
    position: fixed;
    top: 0;
    width: 100%;
    z-index: 1000;
    background: rgba(10, 10, 10, 0.95);
    backdrop-filter: blur(10px);
    border-bottom: 1px solid rgba(255, 0, 85, 0.2);
    transition: all 0.3s ease;
}

.site-header.scrolled {
    background: rgba(10, 10, 10, 0.98);
    box-shadow: 0 4px 30px rgba(255, 0, 85, 0.1);
}

.main-nav {
    padding: 1.5rem 0;
}

.nav-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 2rem;
}

.logo a {
    font-size: 2rem;
    font-weight: 900;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    letter-spacing: -1px;
    transition: transform 0.3s ease;
    display: inline-block;
}

.logo a:hover {
    transform: scale(1.05);
}

.nav-links {
    display: flex;
    gap: 3rem;
    list-style: none;
    margin: 0;
    padding: 0;
}

.nav-links a {
    color: var(--text);
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    position: relative;
}

.nav-links a::after {
    content: '';
    position: absolute;
    bottom: -5px;
    left: 0;
    width: 0;
    height: 2px;
    background: var(--primary);
    transition: width 0.3s ease;
}

.nav-links a:hover::after {
    width: 100%;
}

.nav-actions {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.nav-logout {
    color: var(--text-muted);
    font-size: 0.9rem;
    transition: color 0.3s ease;
}

.nav-logout:hover {
    color: var(--primary);
}

/* Mobile Menu Toggle */
.mobile-menu-toggle {
    display: none;
    flex-direction: column;
    gap: 4px;
    background: none;
    border: none;
    cursor: pointer;
    padding: 0.5rem;
}

.mobile-menu-toggle span {
    width: 25px;
    height: 3px;
    background: var(--text);
    border-radius: 2px;
    transition: all 0.3s ease;
}

/* Responsive */
@media (max-width: 968px) {
    .nav-links {
        display: none;
    }

    .nav-actions {
        gap: 0.5rem;
    }

    .nav-actions .secondary-button,
    .nav-logout {
        display: none;
    }

    .mobile-menu-toggle {
        display: flex;
    }

    .logo a {
        font-size: 1.5rem;
    }

    .nav-container {
        padding: 0 1rem;
    }
}

/* Body padding to account for fixed header */
body {
    padding-top: 85px;
}
</style>
