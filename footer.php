<!-- Footer -->
<footer class="site-footer">
    <div class="footer-content">
        <div class="footer-section">
            <h4>SHIFTZONER</h4>
            <p class="footer-description">La plateforme de partage photo pour passionnés d'automobile.</p>
            <div class="footer-social">
                <?php if ( get_theme_mod( 'shiftzoner_instagram' ) ) : ?>
                    <a href="<?php echo esc_url( get_theme_mod( 'shiftzoner_instagram' ) ); ?>" target="_blank" rel="noopener" aria-label="Instagram">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    </a>
                <?php endif; ?>
                <?php if ( get_theme_mod( 'shiftzoner_facebook' ) ) : ?>
                    <a href="<?php echo esc_url( get_theme_mod( 'shiftzoner_facebook' ) ); ?>" target="_blank" rel="noopener" aria-label="Facebook">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                <?php endif; ?>
                <?php if ( get_theme_mod( 'shiftzoner_twitter' ) ) : ?>
                    <a href="<?php echo esc_url( get_theme_mod( 'shiftzoner_twitter' ) ); ?>" target="_blank" rel="noopener" aria-label="Twitter / X">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="footer-section">
            <h4>Navigation</h4>
            <ul class="footer-menu">
                <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Accueil</a></li>
                <li><a href="<?php echo esc_url( home_url( '/galerie/' ) ); ?>">Explorer</a></li>
                <li><a href="<?php echo esc_url( home_url( '/marques/' ) ); ?>">Marques</a></li>
                <li><a href="<?php echo esc_url( home_url( '/carte/' ) ); ?>">Carte</a></li>
            </ul>
        </div>

        <div class="footer-section">
            <h4>Communauté</h4>
            <ul class="footer-menu">
                <?php if ( function_exists( 'bbp_is_active' ) ) : ?>
                <li><a href="<?php echo esc_url( get_post_type_archive_link( 'forum' ) ); ?>">Forum</a></li>
                <?php endif; ?>
                <li><a href="<?php echo esc_url( home_url( '/soumettre-ma-photo/' ) ); ?>">Publier une photo</a></li>
                <?php if ( function_exists( 'bp_is_active' ) ) : ?>
                <li><a href="<?php echo esc_url( bp_get_members_directory_permalink() ); ?>">Membres</a></li>
                <?php endif; ?>
                <li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">Contact</a></li>
            </ul>
        </div>

        <div class="footer-section">
            <h4>Légal</h4>
            <ul class="footer-menu">
                <li><a href="<?php echo esc_url( home_url( '/mentions-legales/' ) ); ?>">Mentions Légales</a></li>
                <li><a href="<?php echo esc_url( home_url( '/confidentialite/' ) ); ?>">Confidentialité</a></li>
                <li><a href="<?php echo esc_url( home_url( '/conditions/' ) ); ?>">Conditions d'Utilisation</a></li>
                <li><a href="<?php echo esc_url( home_url( '/cookies/' ) ); ?>">Cookies</a></li>
            </ul>
        </div>
    </div>

    <div class="footer-bottom">
        <p>&copy; <?php echo date( 'Y' ); ?> <?php bloginfo( 'name' ); ?>. Tous droits réservés.</p>
        <p class="footer-credits">Thème créé avec passion pour la communauté automobile</p>
    </div>
</footer>

<style>
/* Footer Styles */
.site-footer {
    background: var(--dark);
    padding: 4rem 2rem 2rem;
    border-top: 1px solid rgba(255, 0, 85, 0.2);
    margin-top: 4rem;
}

.footer-content {
    max-width: 1400px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 3rem;
    margin-bottom: 3rem;
}

.footer-section h4 {
    font-size: 1.3rem;
    margin-bottom: 1.5rem;
    color: var(--text);
}

.footer-description {
    color: var(--text-muted);
    line-height: 1.6;
    margin-bottom: 1rem;
}

.footer-social {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
}

.footer-social a {
    color: var(--text-muted);
    transition: color 0.3s ease;
}

.footer-social a:hover {
    color: var(--primary);
}

.footer-menu {
    list-style: none;
    margin: 0;
    padding: 0;
}

.footer-menu li {
    margin-bottom: 0.8rem;
}

.footer-menu a {
    color: var(--text-muted);
    text-decoration: none;
    transition: color 0.3s ease;
}

.footer-menu a:hover {
    color: var(--primary);
}

.footer-bottom {
    max-width: 1400px;
    margin: 0 auto;
    padding-top: 2rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    text-align: center;
    color: var(--text-muted);
}

.footer-bottom p {
    margin-bottom: 0.5rem;
}

.footer-credits {
    font-size: 0.9rem;
    opacity: 0.7;
}

@media (max-width: 768px) {
    .footer-content {
        grid-template-columns: 1fr;
        gap: 2rem;
    }

    .site-footer {
        padding: 2rem 1rem 1rem;
    }
}
</style>

<?php wp_footer(); ?>

<script>
// Header scroll effect
(function() {
    const header = document.getElementById('site-header');
    if (!header) return;

    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });

    // Intersection Observer for fade-in animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, observerOptions);

    document.querySelectorAll('.fade-in').forEach(el => {
        observer.observe(el);
    });

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href === '#') return;

            const target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Mobile menu toggle
    const mobileToggle = document.querySelector('.mobile-menu-toggle');
    if (mobileToggle) {
        mobileToggle.addEventListener('click', () => {
            const navLinks = document.querySelector('.nav-links');
            if (navLinks) {
                navLinks.classList.toggle('mobile-active');
                const isExpanded = mobileToggle.getAttribute('aria-expanded') === 'true';
                mobileToggle.setAttribute('aria-expanded', !isExpanded);
            }
        });
    }
})();
</script>

</body>
</html>
