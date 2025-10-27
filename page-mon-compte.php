<?php
/**
 * Template Name: Mon Compte
 * Description: Page de gestion du profil utilisateur
 *
 * @package ShiftZoneR
 */

// Rediriger si non connecté
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url());
    exit;
}

$current_user = wp_get_current_user();
$user_id = $current_user->ID;

get_header();
?>

<style>
:root {
    --primary: #ff0055;
    --secondary: #00d4ff;
    --dark: #0f172a;
    --dark-gray: #1e293b;
    --card: #334155;
    --text: #f8fafc;
    --text-muted: #cbd5e1;
}

.account-page {
    min-height: 100vh;
    background: linear-gradient(135deg, var(--dark) 0%, #1e1b4b 100%);
    padding: 2rem 1rem;
}

.account-container {
    max-width: 1200px;
    margin: 0 auto;
}

.account-header {
    background: rgba(30, 41, 59, 0.6);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 2rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    align-items: center;
    gap: 2rem;
}

.profile-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    font-weight: 900;
    color: white;
    flex-shrink: 0;
    position: relative;
    overflow: hidden;
    cursor: pointer;
    transition: transform 0.3s;
}

.profile-avatar:hover {
    transform: scale(1.05);
}

.profile-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.profile-avatar-upload {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s;
    color: white;
    font-size: 2rem;
}

.profile-avatar:hover .profile-avatar-upload {
    opacity: 1;
}

.profile-info h1 {
    margin: 0 0 0.5rem 0;
    font-size: 2rem;
    font-weight: 900;
    color: var(--text);
}

.profile-stats {
    display: flex;
    gap: 2rem;
    margin-top: 1rem;
}

.stat-item {
    display: flex;
    flex-direction: column;
}

.stat-value {
    font-size: 1.75rem;
    font-weight: 900;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.stat-label {
    color: var(--text-muted);
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

.account-grid {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 2rem;
}

.account-sidebar {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.sidebar-card {
    background: rgba(30, 41, 59, 0.6);
    backdrop-filter: blur(20px);
    border-radius: 16px;
    padding: 1.5rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.sidebar-card h3 {
    margin: 0 0 1rem 0;
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--text);
}

.nav-menu {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.nav-item {
    padding: 0.875rem 1rem;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    color: var(--text);
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    transition: all 0.3s;
    cursor: pointer;
}

.nav-item:hover,
.nav-item.active {
    background: rgba(255, 0, 85, 0.1);
    border-color: var(--primary);
    color: var(--primary);
}

.account-main {
    background: rgba(30, 41, 59, 0.6);
    backdrop-filter: blur(20px);
    border-radius: 16px;
    padding: 2rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.section {
    display: none;
}

.section.active {
    display: block;
}

.section-header {
    margin-bottom: 2rem;
}

.section-header h2 {
    margin: 0 0 0.5rem 0;
    font-size: 1.75rem;
    font-weight: 800;
    color: var(--text);
}

.section-header p {
    margin: 0;
    color: var(--text-muted);
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group.full-width {
    grid-column: 1 / -1;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: var(--text);
    font-weight: 600;
    font-size: 0.9rem;
}

.form-input,
.form-textarea {
    width: 100%;
    padding: 0.875rem 1rem;
    background: rgba(15, 23, 42, 0.6);
    border: 2px solid rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    color: var(--text);
    font-size: 1rem;
    transition: all 0.3s ease;
}

.form-input:focus,
.form-textarea:focus {
    outline: none;
    border-color: var(--primary);
    background: rgba(15, 23, 42, 0.8);
    box-shadow: 0 0 0 4px rgba(255, 0, 85, 0.1);
}

.form-textarea {
    resize: vertical;
    min-height: 120px;
}

.btn {
    padding: 0.875rem 1.5rem;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    border: none;
    font-size: 1rem;
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary), #e91e63);
    color: white;
    box-shadow: 0 4px 14px 0 rgba(255, 0, 85, 0.4);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px 0 rgba(255, 0, 85, 0.6);
}

.btn-secondary {
    background: rgba(255, 255, 255, 0.1);
    color: var(--text);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.btn-secondary:hover {
    background: rgba(255, 255, 255, 0.15);
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.alert {
    padding: 1rem;
    border-radius: 10px;
    margin-bottom: 1.5rem;
    font-size: 0.9rem;
}

.alert-success {
    background: rgba(16, 185, 129, 0.1);
    border: 1px solid rgba(16, 185, 129, 0.3);
    color: #6ee7b7;
}

.alert-error {
    background: rgba(239, 68, 68, 0.1);
    border: 1px solid rgba(239, 68, 68, 0.3);
    color: #fca5a5;
}

.photos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-top: 2rem;
}

.photo-card {
    background: rgba(15, 23, 42, 0.6);
    border-radius: 12px;
    overflow: hidden;
    transition: transform 0.3s;
}

.photo-card:hover {
    transform: translateY(-4px);
}

.photo-card img {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.photo-card-content {
    padding: 1rem;
}

.photo-card-title {
    margin: 0 0 0.5rem 0;
    font-weight: 700;
    color: var(--text);
    font-size: 0.95rem;
}

.photo-card-meta {
    display: flex;
    gap: 1rem;
    font-size: 0.85rem;
    color: var(--text-muted);
}

@media (max-width: 768px) {
    .account-grid {
        grid-template-columns: 1fr;
    }

    .form-grid {
        grid-template-columns: 1fr;
    }

    .profile-stats {
        flex-direction: column;
        gap: 1rem;
    }
}
</style>

<div class="account-page">
    <div class="account-container">
        <!-- Header -->
        <div class="account-header">
            <div class="profile-avatar" onclick="document.getElementById('avatar-upload').click()">
                <?php
                $avatar = get_avatar($user_id, 120);
                echo $avatar;
                ?>
                <div class="profile-avatar-upload">📷</div>
            </div>
            <input type="file" id="avatar-upload" accept="image/*" style="display:none;">

            <div class="profile-info">
                <h1><?php echo esc_html($current_user->display_name); ?></h1>
                <p style="color:var(--text-muted);margin:0;">@<?php echo esc_html($current_user->user_login); ?></p>

                <div class="profile-stats">
                    <div class="stat-item">
                        <span class="stat-value"><?php echo count_user_posts($user_id, 'car_photo', true); ?></span>
                        <span class="stat-label">Photos</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value"><?php echo get_user_meta($user_id, 'shiftzoner_karma', true) ?: 0; ?></span>
                        <span class="stat-label">Karma</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value"><?php
                            $badges = get_user_meta($user_id, 'shiftzoner_badges', true) ?: array();
                            echo count($badges);
                        ?></span>
                        <span class="stat-label">Badges</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grid -->
        <div class="account-grid">
            <!-- Sidebar -->
            <div class="account-sidebar">
                <div class="sidebar-card">
                    <h3>Navigation</h3>
                    <div class="nav-menu">
                        <a href="#" class="nav-item active" data-section="profile">
                            <span>👤</span> Profil
                        </a>
                        <a href="#" class="nav-item" data-section="photos">
                            <span>📸</span> Mes Photos
                        </a>
                        <a href="#" class="nav-item" data-section="badges">
                            <span>🏆</span> Badges
                        </a>
                        <a href="#" class="nav-item" data-section="security">
                            <span>🔒</span> Sécurité
                        </a>
                    </div>
                </div>

                <div class="sidebar-card">
                    <h3>Mes Badges</h3>
                    <?php echo shiftzoner_display_user_badges($user_id); ?>
                </div>

                <div class="sidebar-card">
                    <a href="<?php echo wp_logout_url(home_url()); ?>" class="btn btn-secondary" style="width:100%;text-align:center;display:block;text-decoration:none;">
                        🚪 Déconnexion
                    </a>
                </div>
            </div>

            <!-- Main content -->
            <div class="account-main">
                <!-- Section Profile -->
                <div class="section active" data-section="profile">
                    <div class="section-header">
                        <h2>Informations du profil</h2>
                        <p>Gérez vos informations personnelles</p>
                    </div>

                    <div id="profile-alert"></div>

                    <form id="profile-form">
                        <?php wp_nonce_field('update_profile', 'profile_nonce'); ?>

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="display_name">Nom affiché</label>
                                <input type="text" id="display_name" name="display_name" class="form-input" value="<?php echo esc_attr($current_user->display_name); ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="user_email">Email</label>
                                <input type="email" id="user_email" name="user_email" class="form-input" value="<?php echo esc_attr($current_user->user_email); ?>" required>
                            </div>

                            <div class="form-group full-width">
                                <label for="description">Bio</label>
                                <textarea id="description" name="description" class="form-textarea" placeholder="Parlez-nous de votre passion automobile..."><?php echo esc_textarea(get_user_meta($user_id, 'description', true)); ?></textarea>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                    </form>
                </div>

                <!-- Section Photos -->
                <div class="section" data-section="photos">
                    <div class="section-header">
                        <h2>Mes Photos</h2>
                        <p>Gérez toutes vos photos publiées</p>
                    </div>

                    <?php
                    $user_photos = get_posts(array(
                        'post_type' => 'car_photo',
                        'author' => $user_id,
                        'posts_per_page' => 12,
                        'orderby' => 'date',
                        'order' => 'DESC'
                    ));

                    if ($user_photos): ?>
                        <div class="photos-grid">
                            <?php foreach ($user_photos as $photo):
                                $votes = get_post_meta($photo->ID, '_shiftzoner_votes', true) ?: 0;
                                $comments = get_comments_number($photo->ID);
                            ?>
                                <div class="photo-card">
                                    <a href="<?php echo get_permalink($photo->ID); ?>">
                                        <?php echo get_the_post_thumbnail($photo->ID, 'medium'); ?>
                                    </a>
                                    <div class="photo-card-content">
                                        <h4 class="photo-card-title"><?php echo get_the_title($photo->ID); ?></h4>
                                        <div class="photo-card-meta">
                                            <span>❤️ <?php echo $votes; ?></span>
                                            <span>💬 <?php echo $comments; ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p style="color:var(--text-muted);text-align:center;padding:3rem 0;">Vous n'avez pas encore publié de photos</p>
                    <?php endif; ?>
                </div>

                <!-- Section Badges -->
                <div class="section" data-section="badges">
                    <div class="section-header">
                        <h2>Mes Badges</h2>
                        <p>Débloquez des badges en participant activement</p>
                    </div>

                    <?php
                    $user_badges = get_user_meta($user_id, 'shiftzoner_badges', true) ?: array();
                    $all_badges = shiftzoner_get_badges();
                    ?>

                    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:1.5rem;margin-top:2rem;">
                        <?php foreach ($all_badges as $badge_id => $badge):
                            $unlocked = in_array($badge_id, $user_badges);
                        ?>
                            <div style="background:rgba(15,23,42,0.6);border-radius:12px;padding:1.5rem;border:2px solid <?php echo $unlocked ? $badge['color'] : 'rgba(255,255,255,0.1)'; ?>;opacity:<?php echo $unlocked ? '1' : '0.5'; ?>;">
                                <div style="font-size:3rem;margin-bottom:0.5rem;"><?php echo $badge['icon']; ?></div>
                                <h3 style="margin:0 0 0.25rem 0;color:var(--text);"><?php echo $badge['name']; ?></h3>
                                <p style="margin:0;color:var(--text-muted);font-size:0.9rem;"><?php echo $badge['description']; ?></p>
                                <?php if ($unlocked): ?>
                                    <div style="margin-top:0.75rem;color:#10b981;font-size:0.85rem;font-weight:600;">✓ Débloqué</div>
                                <?php else: ?>
                                    <div style="margin-top:0.75rem;color:var(--text-muted);font-size:0.85rem;">🔒 Verrouillé</div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Section Security -->
                <div class="section" data-section="security">
                    <div class="section-header">
                        <h2>Sécurité</h2>
                        <p>Modifiez votre mot de passe</p>
                    </div>

                    <div id="security-alert"></div>

                    <form id="password-form">
                        <?php wp_nonce_field('update_password', 'password_nonce'); ?>

                        <div class="form-group">
                            <label for="current_password">Mot de passe actuel</label>
                            <input type="password" id="current_password" name="current_password" class="form-input" required>
                        </div>

                        <div class="form-group">
                            <label for="new_password">Nouveau mot de passe</label>
                            <input type="password" id="new_password" name="new_password" class="form-input" required>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">Confirmer le mot de passe</label>
                            <input type="password" id="confirm_password" name="confirm_password" class="form-input" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Changer le mot de passe</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    // Navigation
    document.querySelectorAll('.nav-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();

            const section = this.dataset.section;

            // Update nav
            document.querySelectorAll('.nav-item').forEach(nav => nav.classList.remove('active'));
            this.classList.add('active');

            // Update sections
            document.querySelectorAll('.section').forEach(sec => sec.classList.remove('active'));
            document.querySelector(`.section[data-section="${section}"]`).classList.add('active');
        });
    });

    // Profile form
    document.getElementById('profile-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        formData.append('action', 'shiftzoner_update_profile');

        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            const alert = document.getElementById('profile-alert');
            if (data.success) {
                alert.className = 'alert alert-success';
                alert.textContent = 'Profil mis à jour avec succès !';
            } else {
                alert.className = 'alert alert-error';
                alert.textContent = data.data || 'Erreur lors de la mise à jour';
            }
            alert.style.display = 'block';
            setTimeout(() => alert.style.display = 'none', 5000);
        });
    });

    // Password form
    document.getElementById('password-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const newPass = document.getElementById('new_password').value;
        const confirmPass = document.getElementById('confirm_password').value;

        if (newPass !== confirmPass) {
            const alert = document.getElementById('security-alert');
            alert.className = 'alert alert-error';
            alert.textContent = 'Les mots de passe ne correspondent pas';
            alert.style.display = 'block';
            return;
        }

        const formData = new FormData(this);
        formData.append('action', 'shiftzoner_update_password');

        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            const alert = document.getElementById('security-alert');
            if (data.success) {
                alert.className = 'alert alert-success';
                alert.textContent = 'Mot de passe changé avec succès !';
                document.getElementById('password-form').reset();
            } else {
                alert.className = 'alert alert-error';
                alert.textContent = data.data || 'Erreur lors du changement de mot de passe';
            }
            alert.style.display = 'block';
            setTimeout(() => alert.style.display = 'none', 5000);
        });
    });

    // Avatar upload
    document.getElementById('avatar-upload').addEventListener('change', function(e) {
        if (!e.target.files || !e.target.files[0]) return;

        const formData = new FormData();
        formData.append('action', 'shiftzoner_update_avatar');
        formData.append('avatar', e.target.files[0]);
        formData.append('nonce', '<?php echo wp_create_nonce('update_avatar'); ?>');

        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur lors de l\'upload de l\'avatar');
            }
        });
    });
})();
</script>

<?php get_footer(); ?>
