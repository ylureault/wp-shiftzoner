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
                <?php
                if ( has_custom_logo() ) {
                    the_custom_logo();
                } else {
                    ?>
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="logo-text">
                        <span class="screen-reader-text"><?php bloginfo( 'name' ); ?></span>
                        SHIFTZONER
                    </a>
                    <?php
                }
                ?>
            </div>

            <?php
            wp_nav_menu( array(
                'theme_location' => 'primary',
                'menu_class'     => 'nav-links',
                'container'      => false,
                'fallback_cb'    => 'shiftzoner_fallback_menu',
            ) );
            ?>

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
            <button class="mobile-menu-toggle" aria-label="Menu" aria-expanded="false" id="mobile-menu-toggle">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </nav>

    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu-overlay" id="mobile-menu-overlay">
        <div class="mobile-menu-content">
            <div class="mobile-menu-header">
                <div class="mobile-menu-logo">
                    <?php
                    if ( has_custom_logo() ) {
                        the_custom_logo();
                    } else {
                        echo 'SHIFTZONER';
                    }
                    ?>
                </div>
                <button class="mobile-menu-close" id="mobile-menu-close" aria-label="Fermer le menu">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <nav class="mobile-nav-links">
                <?php
                if ( has_nav_menu( 'primary' ) ) {
                    $menu_items = wp_get_nav_menu_items( get_nav_menu_locations()['primary'] );
                    if ( $menu_items ) {
                        foreach ( $menu_items as $item ) {
                            $icon = shiftzoner_mobile_menu_icons( $item->ID, $item->url, $item->title );
                            echo '<a href="' . esc_url( $item->url ) . '" class="mobile-nav-link">';
                            echo $icon;
                            echo esc_html( $item->title );
                            echo '</a>';
                        }
                    }
                } else {
                    // Fallback si pas de menu
                    ?>
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="mobile-nav-link">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
                        Accueil
                    </a>
                    <a href="<?php echo esc_url( home_url( '/galerie/' ) ); ?>" class="mobile-nav-link">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/></svg>
                        Explorer
                    </a>
                    <?php if ( function_exists( 'bp_is_active' ) ) : ?>
                    <a href="<?php echo esc_url( bp_get_groups_directory_permalink() ); ?>" class="mobile-nav-link">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/></svg>
                        Communauté
                    </a>
                    <?php endif; ?>
                    <a href="<?php echo esc_url( home_url( '/carte/' ) ); ?>" class="mobile-nav-link">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                        Carte
                    </a>
                    <?php if ( is_user_logged_in() && function_exists( 'bp_core_get_user_domain' ) ) : ?>
                    <a href="<?php echo esc_url( bp_core_get_user_domain( get_current_user_id() ) ); ?>" class="mobile-nav-link">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
                        Mon Profil
                    </a>
                    <?php endif; ?>
                <?php } ?>
            </nav>

            <div class="mobile-nav-actions">
                <?php if ( is_user_logged_in() ) : ?>
                    <a href="<?php echo esc_url( home_url( '/soumettre-photo/' ) ); ?>" class="mobile-cta-button">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/></svg>
                        Publier une photo
                    </a>
                    <a href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>" class="mobile-logout-button">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"/></svg>
                        Déconnexion
                    </a>
                <?php else : ?>
                    <a href="<?php echo esc_url( wp_login_url() ); ?>" class="mobile-secondary-button">
                        Connexion
                    </a>
                    <a href="<?php echo esc_url( wp_registration_url() ); ?>" class="mobile-cta-button">
                        Rejoindre ShiftZoneR
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
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

.logo .custom-logo-link {
    display: inline-block;
    line-height: 0;
    transition: transform 0.3s ease;
}

.logo .custom-logo-link:hover {
    transform: scale(1.05);
}

.logo .custom-logo {
    max-height: 60px;
    width: auto;
    display: block;
}

.logo .logo-text {
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

.logo .logo-text:hover {
    transform: scale(1.05);
}

.logo .screen-reader-text {
    position: absolute;
    left: -9999px;
    top: -9999px;
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
    gap: 5px;
    background: none;
    border: none;
    cursor: pointer;
    padding: 0.5rem;
    z-index: 1002;
    position: relative;
}

.mobile-menu-toggle span {
    width: 28px;
    height: 3px;
    background: var(--text);
    border-radius: 3px;
    transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    display: block;
}

.mobile-menu-toggle.active span:nth-child(1) {
    transform: rotate(45deg) translateY(11px);
}

.mobile-menu-toggle.active span:nth-child(2) {
    opacity: 0;
    transform: translateX(20px);
}

.mobile-menu-toggle.active span:nth-child(3) {
    transform: rotate(-45deg) translateY(-11px);
}

/* Mobile Menu Overlay */
.mobile-menu-overlay {
    position: fixed;
    top: 0;
    right: 0;
    width: 100%;
    height: 100vh;
    background: rgba(0, 0, 0, 0.95);
    backdrop-filter: blur(10px);
    z-index: 1001;
    transform: translateX(100%);
    transition: transform 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    overflow-y: auto;
}

.mobile-menu-overlay.active {
    transform: translateX(0);
}

.mobile-menu-content {
    padding: 2rem 1.5rem;
    max-width: 500px;
    margin-left: auto;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.mobile-menu-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 3rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid rgba(255, 0, 85, 0.2);
}

.mobile-menu-logo {
    font-size: 1.5rem;
    font-weight: 900;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.mobile-menu-logo .custom-logo {
    max-height: 50px;
    width: auto;
}

.mobile-menu-close {
    background: rgba(255, 0, 85, 0.1);
    border: 1px solid rgba(255, 0, 85, 0.3);
    width: 44px;
    height: 44px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    color: var(--text);
}

.mobile-menu-close:hover {
    background: var(--primary);
    border-color: var(--primary);
    transform: rotate(90deg);
}

.mobile-nav-links {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-bottom: 3rem;
}

.mobile-nav-link {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.5rem;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-radius: 15px;
    color: var(--text);
    text-decoration: none;
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.mobile-nav-link::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 0, 85, 0.1), transparent);
    transition: left 0.5s ease;
}

.mobile-nav-link:active::before {
    left: 100%;
}

.mobile-nav-link:hover {
    background: rgba(255, 0, 85, 0.1);
    border-color: rgba(255, 0, 85, 0.3);
    transform: translateX(5px);
}

.mobile-nav-link svg {
    flex-shrink: 0;
    opacity: 0.7;
}

.mobile-nav-actions {
    margin-top: auto;
    display: flex;
    flex-direction: column;
    gap: 1rem;
    padding-top: 2rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.mobile-cta-button,
.mobile-secondary-button,
.mobile-logout-button {
    padding: 1rem 1.5rem;
    border-radius: 15px;
    text-align: center;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    font-size: 1rem;
}

.mobile-cta-button {
    background: linear-gradient(135deg, var(--primary), #ff3377);
    color: white;
    border: none;
}

.mobile-cta-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(255, 0, 85, 0.4);
}

.mobile-secondary-button {
    background: transparent;
    color: var(--text);
    border: 2px solid rgba(255, 255, 255, 0.2);
}

.mobile-secondary-button:hover {
    border-color: var(--secondary);
    background: rgba(0, 174, 239, 0.1);
}

.mobile-logout-button {
    background: transparent;
    color: var(--text-muted);
    border: 1px solid rgba(255, 255, 255, 0.1);
    font-size: 0.95rem;
}

.mobile-logout-button:hover {
    color: var(--primary);
    border-color: var(--primary);
}

/* Prevent body scroll when menu is open */
body.mobile-menu-open {
    overflow: hidden;
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

    .logo .logo-text {
        font-size: 1.5rem;
    }

    .logo .custom-logo {
        max-height: 50px;
    }

    .nav-container {
        padding: 0 1rem;
    }

    .main-nav {
        padding: 1rem 0;
    }
}

@media (max-width: 480px) {
    .mobile-menu-content {
        padding: 1.5rem 1rem;
    }

    .mobile-nav-link {
        padding: 0.875rem 1.25rem;
        font-size: 1rem;
    }

    .mobile-cta-button,
    .mobile-secondary-button {
        padding: 0.875rem 1.25rem;
    }
}

/* Body padding to account for fixed header */
body {
    padding-top: 85px;
}

@media (max-width: 968px) {
    body {
        padding-top: 70px;
    }
}
</style>

<script>
(function() {
    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');
    const mobileMenuClose = document.getElementById('mobile-menu-close');
    const body = document.body;

    function openMobileMenu() {
        mobileMenuOverlay.classList.add('active');
        mobileMenuToggle.classList.add('active');
        body.classList.add('mobile-menu-open');
        mobileMenuToggle.setAttribute('aria-expanded', 'true');
    }

    function closeMobileMenu() {
        mobileMenuOverlay.classList.remove('active');
        mobileMenuToggle.classList.remove('active');
        body.classList.remove('mobile-menu-open');
        mobileMenuToggle.setAttribute('aria-expanded', 'false');
    }

    // Toggle menu
    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', function() {
            if (mobileMenuOverlay.classList.contains('active')) {
                closeMobileMenu();
            } else {
                openMobileMenu();
            }
        });
    }

    // Close button
    if (mobileMenuClose) {
        mobileMenuClose.addEventListener('click', closeMobileMenu);
    }

    // Close on link click
    const mobileNavLinks = document.querySelectorAll('.mobile-nav-link, .mobile-cta-button, .mobile-secondary-button');
    mobileNavLinks.forEach(link => {
        link.addEventListener('click', function() {
            setTimeout(closeMobileMenu, 200);
        });
    });

    // Close on overlay click (outside menu content)
    if (mobileMenuOverlay) {
        mobileMenuOverlay.addEventListener('click', function(e) {
            if (e.target === mobileMenuOverlay) {
                closeMobileMenu();
            }
        });
    }

    // Close on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && mobileMenuOverlay.classList.contains('active')) {
            closeMobileMenu();
        }
    });

    // Header scroll effect
    window.addEventListener('scroll', function() {
        const header = document.getElementById('site-header');
        if (header) {
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        }
    });
})();
</script>
