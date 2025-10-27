<?php
/**
 * Template Name: Page Inscription
 * Description: Page d'inscription personnalisée pour ShiftZoneR
 *
 * @package ShiftZoneR
 */

// Rediriger si déjà connecté
if ( is_user_logged_in() ) {
    wp_redirect( home_url( '/' ) );
    exit;
}

get_header();
?>

<div class="registration-page">
    <div class="registration-container">
        <div class="registration-split">
            <!-- Left Side - Branding -->
            <div class="registration-branding">
                <div class="branding-content">
                    <div class="brand-logo-section">
                        <h1 class="brand-title">SHIFTZONER</h1>
                        <p class="brand-tagline">La communauté des passionnés d'automobile</p>
                    </div>

                    <div class="features-list">
                        <div class="feature-item">
                            <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <h3>Partagez vos photos</h3>
                                <p>Publiez vos plus belles prises de vue automobiles</p>
                            </div>
                        </div>

                        <div class="feature-item">
                            <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>
                            </svg>
                            <div>
                                <h3>Rejoignez la communauté</h3>
                                <p>Connectez-vous avec d'autres passionnés</p>
                            </div>
                        </div>

                        <div class="feature-item">
                            <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <h3>Explorez la carte</h3>
                                <p>Découvrez les spots de shooting près de chez vous</p>
                            </div>
                        </div>

                        <div class="feature-item">
                            <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <div>
                                <h3>Gagnez des points</h3>
                                <p>Montez dans le classement et devenez Top Contributor</p>
                            </div>
                        </div>
                    </div>

                    <div class="stats-preview">
                        <div class="stat-box">
                            <div class="stat-number"><?php echo number_format_i18n( wp_count_posts( 'car_photo' )->publish ); ?></div>
                            <div class="stat-label">Photos</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-number"><?php echo number_format_i18n( count( get_users() ) ); ?></div>
                            <div class="stat-label">Membres</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-number"><?php echo number_format_i18n( wp_count_terms( 'car_brand' ) ); ?></div>
                            <div class="stat-label">Marques</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side - Registration Form -->
            <div class="registration-form-side">
                <div class="form-container">
                    <div class="form-header">
                        <h2>Créer un compte</h2>
                        <p>Rejoignez ShiftZoneR et commencez à partager</p>
                    </div>

                    <div id="registration-messages"></div>

                    <form id="custom-registration-form" class="registration-form">
                        <?php wp_nonce_field( 'szr_register', 'szr_register_nonce' ); ?>

                        <div class="form-group">
                            <label for="reg_username">
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                </svg>
                                Nom d'utilisateur
                            </label>
                            <input type="text" id="reg_username" name="username" required autocomplete="username">
                            <small>Lettres, chiffres, tirets et underscores uniquement</small>
                        </div>

                        <div class="form-group">
                            <label for="reg_email">
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                </svg>
                                Adresse e-mail
                            </label>
                            <input type="email" id="reg_email" name="email" required autocomplete="email">
                        </div>

                        <div class="form-group">
                            <label for="reg_password">
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                </svg>
                                Mot de passe
                            </label>
                            <div class="password-input-wrapper">
                                <input type="password" id="reg_password" name="password" required autocomplete="new-password">
                                <button type="button" class="toggle-password" aria-label="Afficher/Masquer le mot de passe">
                                    <svg class="eye-open" width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <svg class="eye-closed" width="20" height="20" fill="currentColor" viewBox="0 0 20 20" style="display:none;">
                                        <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd"/>
                                        <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="password-strength">
                                <div class="strength-meter">
                                    <div class="strength-bar" id="strength-bar"></div>
                                </div>
                                <small id="strength-text">Minimum 8 caractères</small>
                            </div>
                        </div>

                        <div class="form-group checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="terms" required>
                                <span>J'accepte les <a href="<?php echo esc_url( home_url( '/conditions-utilisation/' ) ); ?>" target="_blank">conditions d'utilisation</a> et la <a href="<?php echo esc_url( home_url( '/politique-confidentialite/' ) ); ?>" target="_blank">politique de confidentialité</a></span>
                            </label>
                        </div>

                        <button type="submit" class="submit-btn" id="register-btn">
                            <span class="btn-text">Créer mon compte</span>
                            <span class="btn-loader" style="display:none;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10" opacity="0.25"/>
                                    <path d="M12 2 A10 10 0 0 1 22 12" stroke-linecap="round">
                                        <animateTransform attributeName="transform" type="rotate" from="0 12 12" to="360 12 12" dur="1s" repeatCount="indefinite"/>
                                    </path>
                                </svg>
                                Création en cours...
                            </span>
                        </button>
                    </form>

                    <div class="form-footer">
                        <p>Vous avez déjà un compte ? <a href="<?php echo esc_url( wp_login_url() ); ?>">Se connecter</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.registration-page {
    background: var(--dark);
    min-height: calc(100vh - 85px);
    display: flex;
    align-items: center;
    padding: 2rem 0;
}

.registration-container {
    width: 100%;
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 2rem;
}

.registration-split {
    display: grid;
    grid-template-columns: 1fr 1fr;
    background: var(--dark-gray);
    border-radius: 30px;
    overflow: hidden;
    box-shadow: 0 20px 80px rgba(0, 0, 0, 0.5);
    border: 1px solid rgba(255, 0, 85, 0.1);
}

/* Left Branding Side */
.registration-branding {
    background: linear-gradient(135deg, var(--primary) 0%, #c50812 100%);
    padding: 4rem 3rem;
    display: flex;
    flex-direction: column;
    justify-content: center;
    position: relative;
    overflow: hidden;
}

.registration-branding::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
    pointer-events: none;
}

.branding-content {
    position: relative;
    z-index: 1;
}

.brand-logo-section {
    margin-bottom: 3rem;
}

.brand-title {
    font-size: 3.5rem;
    font-weight: 900;
    color: white;
    margin-bottom: 0.5rem;
    letter-spacing: -2px;
}

.brand-tagline {
    font-size: 1.2rem;
    color: rgba(255, 255, 255, 0.9);
    font-weight: 300;
}

.features-list {
    display: flex;
    flex-direction: column;
    gap: 2rem;
    margin-bottom: 3rem;
}

.feature-item {
    display: flex;
    gap: 1.5rem;
    color: white;
}

.feature-item svg {
    flex-shrink: 0;
    width: 40px;
    height: 40px;
    opacity: 0.9;
}

.feature-item h3 {
    font-size: 1.2rem;
    margin-bottom: 0.25rem;
    font-weight: 700;
}

.feature-item p {
    font-size: 0.95rem;
    color: rgba(255, 255, 255, 0.8);
    line-height: 1.5;
}

.stats-preview {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
}

.stat-box {
    text-align: center;
    padding: 1.5rem 1rem;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 15px;
    backdrop-filter: blur(10px);
}

.stat-number {
    font-size: 2rem;
    font-weight: 900;
    color: white;
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.8);
}

/* Right Form Side */
.registration-form-side {
    background: var(--dark);
    padding: 4rem 3rem;
    display: flex;
    align-items: center;
}

.form-container {
    width: 100%;
    max-width: 500px;
    margin: 0 auto;
}

.form-header {
    text-align: center;
    margin-bottom: 2.5rem;
}

.form-header h2 {
    font-size: 2.5rem;
    font-weight: 900;
    color: var(--text);
    margin-bottom: 0.5rem;
}

.form-header p {
    color: var(--text-muted);
    font-size: 1.1rem;
}

.registration-form {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-group label {
    font-weight: 600;
    color: var(--text);
    font-size: 0.95rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-group label svg {
    opacity: 0.7;
}

.form-group input[type="text"],
.form-group input[type="email"],
.form-group input[type="password"] {
    padding: 1rem 1.25rem;
    background: var(--dark-gray);
    border: 2px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    color: var(--text);
    font-size: 1rem;
    transition: all 0.3s ease;
}

.form-group input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(255, 0, 85, 0.1);
}

.form-group small {
    color: var(--text-muted);
    font-size: 0.85rem;
}

.password-input-wrapper {
    position: relative;
}

.toggle-password {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: var(--text-muted);
    cursor: pointer;
    padding: 0.5rem;
    display: flex;
    align-items: center;
    transition: color 0.3s ease;
}

.toggle-password:hover {
    color: var(--text);
}

.password-strength {
    margin-top: 0.5rem;
}

.strength-meter {
    height: 4px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 0.5rem;
}

.strength-bar {
    height: 100%;
    width: 0%;
    transition: all 0.3s ease;
    border-radius: 4px;
}

.strength-bar.weak {
    width: 33%;
    background: #ff4444;
}

.strength-bar.medium {
    width: 66%;
    background: #ffaa00;
}

.strength-bar.strong {
    width: 100%;
    background: #00c851;
}

#strength-text {
    font-size: 0.85rem;
}

.checkbox-group {
    margin: 0.5rem 0;
}

.checkbox-label {
    display: flex !important;
    align-items: flex-start !important;
    gap: 0.75rem !important;
    cursor: pointer;
    font-weight: 400 !important;
}

.checkbox-label input[type="checkbox"] {
    margin-top: 0.25rem;
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.checkbox-label span {
    flex: 1;
    line-height: 1.5;
    color: var(--text-muted);
}

.checkbox-label a {
    color: var(--primary);
    text-decoration: underline;
}

.submit-btn {
    background: linear-gradient(135deg, var(--primary), #ff3377);
    color: white;
    border: none;
    padding: 1.2rem 2rem;
    border-radius: 12px;
    font-size: 1.1rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 0.5rem;
}

.submit-btn:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(255, 0, 85, 0.4);
}

.submit-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn-loader {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.form-footer {
    text-align: center;
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.form-footer p {
    color: var(--text-muted);
}

.form-footer a {
    color: var(--primary);
    font-weight: 600;
    text-decoration: none;
    transition: color 0.3s ease;
}

.form-footer a:hover {
    color: var(--secondary);
}

#registration-messages {
    margin-bottom: 1.5rem;
}

.message {
    padding: 1rem 1.25rem;
    border-radius: 12px;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.message.error {
    background: rgba(255, 68, 68, 0.1);
    border: 1px solid rgba(255, 68, 68, 0.3);
    color: #ff4444;
}

.message.success {
    background: rgba(0, 200, 81, 0.1);
    border: 1px solid rgba(0, 200, 81, 0.3);
    color: #00c851;
}

/* Responsive */
@media (max-width: 968px) {
    .registration-split {
        grid-template-columns: 1fr;
    }

    .registration-branding {
        padding: 3rem 2rem;
    }

    .brand-title {
        font-size: 2.5rem;
    }

    .stats-preview {
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
    }

    .stat-box {
        padding: 1rem 0.5rem;
    }

    .stat-number {
        font-size: 1.5rem;
    }

    .registration-form-side {
        padding: 3rem 2rem;
    }
}

@media (max-width: 480px) {
    .registration-container {
        padding: 0 1rem;
    }

    .registration-branding {
        padding: 2rem 1.5rem;
    }

    .brand-title {
        font-size: 2rem;
    }

    .feature-item {
        gap: 1rem;
    }

    .feature-item svg {
        width: 32px;
        height: 32px;
    }

    .stats-preview {
        gap: 0.75rem;
    }

    .form-header h2 {
        font-size: 2rem;
    }

    .registration-form-side {
        padding: 2rem 1.5rem;
    }
}
</style>

<script>
(function() {
    const form = document.getElementById('custom-registration-form');
    const passwordInput = document.getElementById('reg_password');
    const strengthBar = document.getElementById('strength-bar');
    const strengthText = document.getElementById('strength-text');
    const togglePassword = document.querySelector('.toggle-password');
    const registerBtn = document.getElementById('register-btn');
    const messagesDiv = document.getElementById('registration-messages');

    // Toggle password visibility
    if (togglePassword) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            const eyeOpen = this.querySelector('.eye-open');
            const eyeClosed = this.querySelector('.eye-closed');
            eyeOpen.style.display = type === 'password' ? 'block' : 'none';
            eyeClosed.style.display = type === 'password' ? 'none' : 'block';
        });
    }

    // Password strength checker
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;

            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;

            strengthBar.className = 'strength-bar';
            if (strength === 0 || strength === 1) {
                strengthBar.classList.add('weak');
                strengthText.textContent = 'Mot de passe faible';
                strengthText.style.color = '#ff4444';
            } else if (strength === 2 || strength === 3) {
                strengthBar.classList.add('medium');
                strengthText.textContent = 'Mot de passe moyen';
                strengthText.style.color = '#ffaa00';
            } else {
                strengthBar.classList.add('strong');
                strengthText.textContent = 'Mot de passe fort';
                strengthText.style.color = '#00c851';
            }
        });
    }

    // Form submission
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            formData.append('action', 'szr_custom_registration');

            registerBtn.disabled = true;
            document.querySelector('.btn-text').style.display = 'none';
            document.querySelector('.btn-loader').style.display = 'flex';
            messagesDiv.innerHTML = '';

            fetch('<?php echo admin_url( 'admin-ajax.php' ); ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    messagesDiv.innerHTML = '<div class="message success">' + data.data.message + '</div>';
                    form.reset();

                    // Redirect after 2 seconds
                    setTimeout(function() {
                        window.location.href = data.data.redirect || '<?php echo home_url( '/' ); ?>';
                    }, 2000);
                } else {
                    messagesDiv.innerHTML = '<div class="message error">' + (data.data?.message || 'Une erreur est survenue') + '</div>';
                    registerBtn.disabled = false;
                    document.querySelector('.btn-text').style.display = 'block';
                    document.querySelector('.btn-loader').style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                messagesDiv.innerHTML = '<div class="message error">Une erreur est survenue. Veuillez réessayer.</div>';
                registerBtn.disabled = false;
                document.querySelector('.btn-text').style.display = 'block';
                document.querySelector('.btn-loader').style.display = 'none';
            });
        });
    }
})();
</script>

<?php
get_footer();
