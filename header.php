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
    <div class="header-container">
        <div class="header-logo">
            <?php
            if ( has_custom_logo() ) {
                the_custom_logo();
            } else {
                ?>
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="logo-link">
                    <span class="logo-text">SHIFTZONER</span>
                </a>
                <?php
            }
            ?>
        </div>

        <nav class="header-nav" id="header-nav">
            <?php
            wp_nav_menu( array(
                'theme_location' => 'primary',
                'menu_class'     => 'menu-list',
                'container'      => false,
                'fallback_cb'    => 'shiftzoner_fallback_menu',
            ) );
            ?>
        </nav>

        <div class="header-actions">
            <?php if ( is_user_logged_in() ) : ?>
                <a href="<?php echo esc_url( home_url( '/soumettre-ma-photo/' ) ); ?>" class="btn-upload">
                    <svg width="18" height="18" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                    </svg>
                    <span class="btn-text">Publier</span>
                </a>
                <a href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>" class="btn-logout" title="Déconnexion">
                    <svg width="18" height="18" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"/>
                    </svg>
                </a>
            <?php else : ?>
                <a href="<?php echo esc_url( wp_login_url() ); ?>" class="btn-login">Connexion</a>
                <a href="<?php echo esc_url( wp_registration_url() ); ?>" class="btn-join">Rejoindre</a>
            <?php endif; ?>
        </div>

        <button class="menu-toggle" id="menu-toggle" aria-label="Menu" aria-expanded="false">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>
</header>

<!-- Mobile Menu -->
<div class="mobile-menu" id="mobile-menu">
    <div class="mobile-menu-inner">
        <div class="mobile-menu-header">
            <span class="mobile-logo">SHIFTZONER</span>
            <button class="menu-close" id="menu-close" aria-label="Fermer">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <nav class="mobile-nav">
            <?php
            if ( has_nav_menu( 'primary' ) ) {
                $menu_items = wp_get_nav_menu_items( get_nav_menu_locations()['primary'] );
                if ( $menu_items ) {
                    echo '<div class="mobile-menu-items">';
                    foreach ( $menu_items as $item ) {
                        $icon = shiftzoner_get_menu_icon( $item->title, $item->url );
                        echo '<a href="' . esc_url( $item->url ) . '" class="mobile-menu-item">';
                        echo $icon;
                        echo '<span>' . esc_html( $item->title ) . '</span>';
                        echo '</a>';
                    }
                    echo '</div>';
                }
            } else {
                // Menu de fallback
                echo '<div class="mobile-menu-items">';
                echo '<a href="' . esc_url( home_url( '/' ) ) . '" class="mobile-menu-item">';
                echo '<svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>';
                echo '<span>Accueil</span></a>';

                echo '<a href="' . esc_url( home_url( '/galerie/' ) ) . '" class="mobile-menu-item">';
                echo '<svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/></svg>';
                echo '<span>Galerie</span></a>';

                echo '<a href="' . esc_url( home_url( '/marques/' ) ) . '" class="mobile-menu-item">';
                echo '<svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/></svg>';
                echo '<span>Marques</span></a>';

                if ( function_exists( 'bp_is_active' ) ) {
                    echo '<a href="' . esc_url( bp_get_groups_directory_permalink() ) . '" class="mobile-menu-item">';
                    echo '<svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/></svg>';
                    echo '<span>Groupes</span></a>';
                }

                echo '<a href="' . esc_url( home_url( '/carte/' ) ) . '" class="mobile-menu-item">';
                echo '<svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>';
                echo '<span>Carte</span></a>';

                echo '</div>';
            }
            ?>
        </nav>

        <div class="mobile-actions">
            <?php if ( is_user_logged_in() ) : ?>
                <a href="<?php echo esc_url( home_url( '/soumettre-ma-photo/' ) ); ?>" class="mobile-btn-primary">
                    <svg width="18" height="18" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                    </svg>
                    Publier une photo
                </a>
                <a href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>" class="mobile-btn-secondary">Déconnexion</a>
            <?php else : ?>
                <a href="<?php echo esc_url( wp_login_url() ); ?>" class="mobile-btn-secondary">Connexion</a>
                <a href="<?php echo esc_url( wp_registration_url() ); ?>" class="mobile-btn-primary">Rejoindre</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
/* ============================================
   HEADER - Modern Clean Design
   ============================================ */

.site-header {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    background: rgba(10, 10, 10, 0.95);
    backdrop-filter: blur(20px);
    border-bottom: 1px solid rgba(255, 0, 85, 0.15);
    transition: all 0.3s ease;
}

.site-header.scrolled {
    background: rgba(10, 10, 10, 0.98);
    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
}

.header-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 2rem;
    height: 70px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 2rem;
}

/* Logo */
.header-logo {
    flex-shrink: 0;
}

.header-logo .custom-logo-link {
    display: block;
    line-height: 0;
}

.header-logo .custom-logo {
    height: 45px;
    width: auto;
    transition: transform 0.3s ease;
}

.header-logo .custom-logo:hover {
    transform: scale(1.05);
}

.logo-link {
    display: block;
    text-decoration: none;
}

.logo-text {
    font-size: 1.5rem;
    font-weight: 900;
    background: linear-gradient(135deg, #E50914, #00AEEF);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    letter-spacing: 1px;
}

/* Navigation */
.header-nav {
    flex: 1;
    display: flex;
    justify-content: center;
}

.menu-list {
    display: flex;
    align-items: center;
    gap: 2.5rem;
    list-style: none;
    margin: 0;
    padding: 0;
}

.menu-list li {
    margin: 0;
}

.menu-list a {
    color: var(--text);
    font-size: 0.95rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    position: relative;
    padding: 0.5rem 0;
}

.menu-list a::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 0;
    height: 2px;
    background: linear-gradient(90deg, #E50914, #00AEEF);
    transition: width 0.3s ease;
}

.menu-list a:hover {
    color: var(--primary);
}

.menu-list a:hover::after {
    width: 100%;
}

.menu-list .current-menu-item a,
.menu-list .current_page_item a {
    color: var(--primary);
}

.menu-list .current-menu-item a::after,
.menu-list .current_page_item a::after {
    width: 100%;
}

/* Header Actions */
.header-actions {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-shrink: 0;
}

.btn-upload {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.6rem 1.25rem;
    background: linear-gradient(135deg, #E50914, #ff3377);
    color: white;
    font-size: 0.9rem;
    font-weight: 700;
    border-radius: 25px;
    text-decoration: none;
    transition: all 0.3s ease;
}

.btn-upload:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(229, 9, 20, 0.4);
}

.btn-logout {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.05);
    color: var(--text-muted);
    transition: all 0.3s ease;
    text-decoration: none;
}

.btn-logout:hover {
    background: rgba(229, 9, 20, 0.1);
    color: var(--primary);
}

.btn-login {
    padding: 0.6rem 1.25rem;
    color: var(--text);
    font-size: 0.9rem;
    font-weight: 600;
    text-decoration: none;
    transition: color 0.3s ease;
}

.btn-login:hover {
    color: var(--primary);
}

.btn-join {
    padding: 0.6rem 1.5rem;
    background: linear-gradient(135deg, #E50914, #ff3377);
    color: white;
    font-size: 0.9rem;
    font-weight: 700;
    border-radius: 25px;
    text-decoration: none;
    transition: all 0.3s ease;
}

.btn-join:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(229, 9, 20, 0.4);
}

/* Menu Toggle (Mobile) */
.menu-toggle {
    display: none;
    flex-direction: column;
    gap: 5px;
    width: 40px;
    height: 40px;
    align-items: center;
    justify-content: center;
    background: none;
    border: none;
    cursor: pointer;
    padding: 0;
    z-index: 1001;
}

.menu-toggle span {
    width: 24px;
    height: 2px;
    background: var(--text);
    border-radius: 2px;
    transition: all 0.3s ease;
}

.menu-toggle.active span:nth-child(1) {
    transform: rotate(45deg) translate(6px, 6px);
}

.menu-toggle.active span:nth-child(2) {
    opacity: 0;
}

.menu-toggle.active span:nth-child(3) {
    transform: rotate(-45deg) translate(6px, -6px);
}

/* Mobile Menu */
.mobile-menu {
    position: fixed;
    top: 0;
    right: 0;
    width: 100%;
    max-width: 400px;
    height: 100vh;
    background: rgba(10, 10, 10, 0.98);
    backdrop-filter: blur(20px);
    z-index: 1000;
    transform: translateX(100%);
    transition: transform 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    overflow-y: auto;
}

.mobile-menu.active {
    transform: translateX(0);
}

.mobile-menu-inner {
    display: flex;
    flex-direction: column;
    height: 100%;
    padding: 1.5rem;
}

.mobile-menu-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid rgba(255, 0, 85, 0.15);
}

.mobile-logo {
    font-size: 1.3rem;
    font-weight: 900;
    background: linear-gradient(135deg, #E50914, #00AEEF);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.menu-close {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.05);
    border: none;
    border-radius: 50%;
    color: var(--text);
    cursor: pointer;
    transition: all 0.3s ease;
}

.menu-close:hover {
    background: rgba(229, 9, 20, 0.2);
    color: var(--primary);
    transform: rotate(90deg);
}

.mobile-nav {
    flex: 1;
    overflow-y: auto;
}

.mobile-menu-items {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.mobile-menu-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.25rem;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-radius: 12px;
    color: var(--text);
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
}

.mobile-menu-item:hover {
    background: rgba(229, 9, 20, 0.1);
    border-color: rgba(229, 9, 20, 0.3);
    transform: translateX(5px);
}

.mobile-menu-item svg {
    flex-shrink: 0;
    opacity: 0.7;
}

.mobile-actions {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    padding-top: 1.5rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.mobile-btn-primary {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 1rem;
    background: linear-gradient(135deg, #E50914, #ff3377);
    color: white;
    font-weight: 700;
    border-radius: 12px;
    text-decoration: none;
    transition: all 0.3s ease;
}

.mobile-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(229, 9, 20, 0.4);
}

.mobile-btn-secondary {
    padding: 1rem;
    background: rgba(255, 255, 255, 0.05);
    color: var(--text);
    font-weight: 600;
    border-radius: 12px;
    text-align: center;
    text-decoration: none;
    transition: all 0.3s ease;
}

.mobile-btn-secondary:hover {
    background: rgba(255, 255, 255, 0.1);
}

/* Body Offset for Fixed Header */
body {
    padding-top: 70px;
}

body.mobile-menu-open {
    overflow: hidden;
}

/* Responsive */
@media (max-width: 968px) {
    .header-nav,
    .btn-login,
    .btn-text {
        display: none;
    }

    .menu-toggle {
        display: flex;
    }

    .header-container {
        height: 60px;
        padding: 0 1.5rem;
    }

    .header-logo .custom-logo {
        height: 40px;
    }

    .logo-text {
        font-size: 1.25rem;
    }

    body {
        padding-top: 60px;
    }
}

@media (max-width: 480px) {
    .header-container {
        padding: 0 1rem;
    }

    .mobile-menu {
        max-width: 100%;
    }

    .mobile-menu-item {
        padding: 0.875rem 1rem;
        font-size: 0.95rem;
    }
}
</style>

<script>
(function() {
    const menuToggle = document.getElementById('menu-toggle');
    const menuClose = document.getElementById('menu-close');
    const mobileMenu = document.getElementById('mobile-menu');
    const body = document.body;

    function openMenu() {
        mobileMenu.classList.add('active');
        menuToggle.classList.add('active');
        body.classList.add('mobile-menu-open');
        menuToggle.setAttribute('aria-expanded', 'true');
    }

    function closeMenu() {
        mobileMenu.classList.remove('active');
        menuToggle.classList.remove('active');
        body.classList.remove('mobile-menu-open');
        menuToggle.setAttribute('aria-expanded', 'false');
    }

    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            if (mobileMenu.classList.contains('active')) {
                closeMenu();
            } else {
                openMenu();
            }
        });
    }

    if (menuClose) {
        menuClose.addEventListener('click', closeMenu);
    }

    // Close on menu item click
    const menuItems = document.querySelectorAll('.mobile-menu-item, .mobile-btn-primary, .mobile-btn-secondary');
    menuItems.forEach(item => {
        item.addEventListener('click', function() {
            setTimeout(closeMenu, 200);
        });
    });

    // Close on outside click
    mobileMenu.addEventListener('click', function(e) {
        if (e.target === mobileMenu) {
            closeMenu();
        }
    });

    // Close on escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && mobileMenu.classList.contains('active')) {
            closeMenu();
        }
    });

    // Scroll effect
    let lastScroll = 0;
    window.addEventListener('scroll', function() {
        const header = document.getElementById('site-header');
        const currentScroll = window.pageYOffset;

        if (currentScroll > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }

        lastScroll = currentScroll;
    });
})();
</script>
