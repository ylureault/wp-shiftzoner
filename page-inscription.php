<?php
/**
 * Template Name: Inscription
 * Description: Page d'inscription irrésistible et engageante
 *
 * @package ShiftZoneR
 */

// Rediriger si déjà connecté
if (is_user_logged_in()) {
    wp_redirect(home_url('/mon-compte'));
    exit;
}

get_header();
?>

<style>
:root {
    --primary: #ff0055;
    --secondary: #00d4ff;
    --dark: #0f172a;
    --dark-gray: #1e293b;
    --text: #f8fafc;
    --text-muted: #cbd5e1;
}

.signup-page {
    min-height: 100vh;
    background: linear-gradient(135deg, var(--dark) 0%, #1e1b4b 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    position: relative;
    overflow: hidden;
}

/* Animated background */
.signup-bg {
    position: absolute;
    inset: 0;
    opacity: 0.1;
    background-image:
        radial-gradient(circle at 20% 50%, var(--primary) 0%, transparent 50%),
        radial-gradient(circle at 80% 80%, var(--secondary) 0%, transparent 50%),
        radial-gradient(circle at 40% 20%, #667eea 0%, transparent 50%);
    animation: bgShift 20s ease-in-out infinite alternate;
}

@keyframes bgShift {
    0%, 100% { transform: translate(0, 0) scale(1); }
    50% { transform: translate(5%, 5%) scale(1.1); }
}

.signup-container {
    max-width: 900px;
    width: 100%;
    position: relative;
    z-index: 1;
}

.signup-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0;
    background: rgba(30, 41, 59, 0.9);
    backdrop-filter: blur(20px);
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

/* Left side - Benefits */
.signup-benefits {
    background: linear-gradient(135deg, rgba(255, 0, 85, 0.1) 0%, rgba(0, 212, 255, 0.1) 100%);
    padding: 3rem;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.benefits-logo {
    font-size: 2rem;
    font-weight: 900;
    margin-bottom: 1rem;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.benefits-title {
    font-size: 1.75rem;
    font-weight: 800;
    margin-bottom: 0.5rem;
    color: var(--text);
}

.benefits-subtitle {
    color: var(--text-muted);
    margin-bottom: 2rem;
    font-size: 1.05rem;
}

.benefit-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1.5rem;
    opacity: 0;
    animation: slideInLeft 0.6s ease-out forwards;
}

.benefit-item:nth-child(1) { animation-delay: 0.1s; }
.benefit-item:nth-child(2) { animation-delay: 0.2s; }
.benefit-item:nth-child(3) { animation-delay: 0.3s; }
.benefit-item:nth-child(4) { animation-delay: 0.4s; }
.benefit-item:nth-child(5) { animation-delay: 0.5s; }

@keyframes slideInLeft {
    from { opacity: 0; transform: translateX(-20px); }
    to { opacity: 1; transform: translateX(0); }
}

.benefit-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.benefit-content h4 {
    margin: 0 0 0.25rem 0;
    color: var(--text);
    font-weight: 700;
    font-size: 1.05rem;
}

.benefit-content p {
    margin: 0;
    color: var(--text-muted);
    font-size: 0.9rem;
    line-height: 1.5;
}

/* Right side - Form */
.signup-form-section {
    padding: 3rem;
    background: var(--dark-gray);
}

.form-header {
    text-align: center;
    margin-bottom: 2rem;
}

.form-header h2 {
    font-size: 1.75rem;
    font-weight: 800;
    margin-bottom: 0.5rem;
    color: var(--text);
}

.form-header p {
    color: var(--text-muted);
    font-size: 0.95rem;
}

.signup-form {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.form-group {
    position: relative;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: var(--text);
    font-weight: 600;
    font-size: 0.9rem;
}

.form-input {
    width: 100%;
    padding: 0.875rem 1rem;
    background: rgba(15, 23, 42, 0.6);
    border: 2px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    color: var(--text);
    font-size: 1rem;
    transition: all 0.3s ease;
}

.form-input:focus {
    outline: none;
    border-color: var(--primary);
    background: rgba(15, 23, 42, 0.8);
    box-shadow: 0 0 0 4px rgba(255, 0, 85, 0.1);
}

.form-input::placeholder {
    color: var(--text-muted);
    opacity: 0.5;
}

.password-strength {
    margin-top: 0.5rem;
    height: 4px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 2px;
    overflow: hidden;
    display: none;
}

.password-strength.active {
    display: block;
}

.password-strength-bar {
    height: 100%;
    width: 0;
    transition: width 0.3s, background 0.3s;
    border-radius: 2px;
}

.password-strength.weak .password-strength-bar {
    width: 33%;
    background: #ef4444;
}

.password-strength.medium .password-strength-bar {
    width: 66%;
    background: #f59e0b;
}

.password-strength.strong .password-strength-bar {
    width: 100%;
    background: #10b981;
}

.form-checkbox {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-checkbox input[type="checkbox"] {
    width: 18px;
    height: 18px;
    accent-color: var(--primary);
    cursor: pointer;
}

.form-checkbox label {
    margin: 0;
    font-size: 0.875rem;
    color: var(--text-muted);
    cursor: pointer;
}

.form-checkbox a {
    color: var(--primary);
    text-decoration: none;
}

.form-checkbox a:hover {
    text-decoration: underline;
}

.submit-btn {
    width: 100%;
    padding: 1rem;
    background: linear-gradient(135deg, var(--primary), #e91e63);
    border: none;
    border-radius: 12px;
    color: white;
    font-size: 1.05rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 14px 0 rgba(255, 0, 85, 0.4);
}

.submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px 0 rgba(255, 0, 85, 0.6);
}

.submit-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

.form-divider {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin: 1rem 0;
}

.form-divider span {
    color: var(--text-muted);
    font-size: 0.875rem;
}

.form-divider::before,
.form-divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: rgba(255, 255, 255, 0.1);
}

.social-login {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
}

.social-btn {
    padding: 0.75rem;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    color: var(--text);
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.social-btn:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.2);
}

.form-footer {
    text-align: center;
    margin-top: 1.5rem;
    color: var(--text-muted);
    font-size: 0.9rem;
}

.form-footer a {
    color: var(--primary);
    text-decoration: none;
    font-weight: 600;
}

.form-footer a:hover {
    text-decoration: underline;
}

.alert {
    padding: 1rem;
    border-radius: 10px;
    margin-bottom: 1rem;
    font-size: 0.9rem;
}

.alert-error {
    background: rgba(239, 68, 68, 0.1);
    border: 1px solid rgba(239, 68, 68, 0.3);
    color: #fca5a5;
}

.alert-success {
    background: rgba(16, 185, 129, 0.1);
    border: 1px solid rgba(16, 185, 129, 0.3);
    color: #6ee7b7;
}

/* Responsive */
@media (max-width: 768px) {
    .signup-grid {
        grid-template-columns: 1fr;
    }

    .signup-benefits {
        padding: 2rem;
    }

    .signup-form-section {
        padding: 2rem;
    }

    .social-login {
        grid-template-columns: 1fr;
    }
}

/* Loading spinner */
.loading-spinner {
    display: inline-block;
    width: 16px;
    height: 16px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-top-color: white;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>

<div class="signup-page">
    <div class="signup-bg"></div>

    <div class="signup-container">
        <div class="signup-grid">
            <!-- Left side - Benefits -->
            <div class="signup-benefits">
                <div class="benefits-logo">SHIFTZONER</div>
                <h1 class="benefits-title">Rejoignez la plus grande communauté automobile</h1>
                <p class="benefits-subtitle">Partagez votre passion, découvrez des voitures exceptionnelles</p>

                <div class="benefit-item">
                    <div class="benefit-icon">📸</div>
                    <div class="benefit-content">
                        <h4>Partagez vos photos</h4>
                        <p>Publiez vos plus belles photos de voitures et recevez des likes de passionnés</p>
                    </div>
                </div>

                <div class="benefit-item">
                    <div class="benefit-icon">🏆</div>
                    <div class="benefit-content">
                        <h4>Gagnez des badges</h4>
                        <p>Débloquez des récompenses exclusives en participant activement</p>
                    </div>
                </div>

                <div class="benefit-item">
                    <div class="benefit-icon">🌍</div>
                    <div class="benefit-content">
                        <h4>Carte interactive</h4>
                        <p>Découvrez où les photos ont été prises avec notre carte GPS</p>
                    </div>
                </div>

                <div class="benefit-item">
                    <div class="benefit-icon">👥</div>
                    <div class="benefit-content">
                        <h4>Communauté active</h4>
                        <p>Échangez avec des milliers de passionnés automobiles</p>
                    </div>
                </div>

                <div class="benefit-item">
                    <div class="benefit-icon">⚡</div>
                    <div class="benefit-content">
                        <h4>100% Gratuit</h4>
                        <p>Accès illimité à toutes les fonctionnalités, sans abonnement</p>
                    </div>
                </div>
            </div>

            <!-- Right side - Form -->
            <div class="signup-form-section">
                <div class="form-header">
                    <h2>Créer mon compte</h2>
                    <p>C'est rapide et gratuit</p>
                </div>

                <div id="signup-alert" style="display:none;"></div>

                <form id="signup-form" class="signup-form">
                    <?php wp_nonce_field('shiftzoner_signup', 'signup_nonce'); ?>

                    <div class="form-group">
                        <label for="username">Nom d'utilisateur</label>
                        <input type="text" id="username" name="username" class="form-input" placeholder="votrepseudo" required autocomplete="username">
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-input" placeholder="vous@exemple.com" required autocomplete="email">
                    </div>

                    <div class="form-group">
                        <label for="password">Mot de passe</label>
                        <input type="password" id="password" name="password" class="form-input" placeholder="••••••••" required autocomplete="new-password">
                        <div id="password-strength" class="password-strength">
                            <div class="password-strength-bar"></div>
                        </div>
                    </div>

                    <div class="form-checkbox">
                        <input type="checkbox" id="terms" name="terms" required>
                        <label for="terms">J'accepte les <a href="/conditions" target="_blank">conditions d'utilisation</a></label>
                    </div>

                    <button type="submit" class="submit-btn" id="submit-btn">
                        <span id="btn-text">Créer mon compte</span>
                    </button>
                </form>

                <div class="form-divider">
                    <span>ou continuer avec</span>
                </div>

                <div class="social-login">
                    <button class="social-btn" onclick="alert('Bientôt disponible!')">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                        </svg>
                        Google
                    </button>
                    <button class="social-btn" onclick="alert('Bientôt disponible!')">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                        Facebook
                    </button>
                </div>

                <div class="form-footer">
                    Déjà membre ? <a href="<?php echo wp_login_url(); ?>">Se connecter</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    const form = document.getElementById('signup-form');
    const passwordInput = document.getElementById('password');
    const passwordStrength = document.getElementById('password-strength');
    const submitBtn = document.getElementById('submit-btn');
    const btnText = document.getElementById('btn-text');
    const alertDiv = document.getElementById('signup-alert');

    // Password strength checker
    passwordInput.addEventListener('input', function() {
        const password = this.value;

        if (password.length === 0) {
            passwordStrength.classList.remove('active', 'weak', 'medium', 'strong');
            return;
        }

        passwordStrength.classList.add('active');

        let strength = 0;
        if (password.length >= 8) strength++;
        if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
        if (password.match(/\d/)) strength++;
        if (password.match(/[^a-zA-Z\d]/)) strength++;

        passwordStrength.classList.remove('weak', 'medium', 'strong');
        if (strength <= 2) {
            passwordStrength.classList.add('weak');
        } else if (strength === 3) {
            passwordStrength.classList.add('medium');
        } else {
            passwordStrength.classList.add('strong');
        }
    });

    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(form);
        formData.append('action', 'shiftzoner_register');

        submitBtn.disabled = true;
        btnText.innerHTML = '<span class="loading-spinner"></span> Création en cours...';
        alertDiv.style.display = 'none';

        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alertDiv.className = 'alert alert-success';
                alertDiv.textContent = 'Inscription réussie ! Redirection...';
                alertDiv.style.display = 'block';

                setTimeout(() => {
                    window.location.href = data.data.redirect || '<?php echo home_url('/mon-compte'); ?>';
                }, 1500);
            } else {
                alertDiv.className = 'alert alert-error';
                alertDiv.textContent = data.data || 'Une erreur est survenue';
                alertDiv.style.display = 'block';

                submitBtn.disabled = false;
                btnText.textContent = 'Créer mon compte';
            }
        })
        .catch(error => {
            alertDiv.className = 'alert alert-error';
            alertDiv.textContent = 'Erreur de connexion';
            alertDiv.style.display = 'block';

            submitBtn.disabled = false;
            btnText.textContent = 'Créer mon compte';
        });
    });
})();
</script>

<?php get_footer(); ?>
