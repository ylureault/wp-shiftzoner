<?php
/**
 * Template Name: Groupes de Modèles
 * Description: Page listant tous les groupes de modèles automobiles
 *
 * @package ShiftZoneR
 */

get_header();

// Get all groups
$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
$groups_query = new WP_Query( array(
    'post_type' => 'car_group',
    'posts_per_page' => 12,
    'paged' => $paged,
    'post_status' => 'publish',
    'orderby' => 'date',
    'order' => 'DESC'
) );

// Get brands for filter
$brands = get_terms( array(
    'taxonomy' => 'car_brand',
    'hide_empty' => false,
) );
?>

<div class="groups-page">
    <!-- Header -->
    <div class="groups-header">
        <div class="container">
            <h1 class="groups-title">Groupes de Modèles</h1>
            <p class="groups-subtitle">Rejoignez des groupes de passionnés de modèles spécifiques</p>

            <?php if ( is_user_logged_in() ) : ?>
                <a href="#" class="create-group-btn" id="create-group-trigger">
                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                    </svg>
                    Créer un groupe
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Filters -->
    <div class="groups-filters">
        <div class="container">
            <div class="filters-row">
                <div class="filter-item">
                    <input type="text" id="search-groups" placeholder="Rechercher un groupe..." class="search-input">
                </div>

                <div class="filter-item">
                    <select id="brand-filter" class="filter-select">
                        <option value="">Toutes les marques</option>
                        <?php foreach ( $brands as $brand ) : ?>
                            <option value="<?php echo esc_attr( $brand->term_id ); ?>">
                                <?php echo esc_html( $brand->name ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-item">
                    <select id="sort-groups" class="filter-select">
                        <option value="recent">Plus récents</option>
                        <option value="members">Plus de membres</option>
                        <option value="active">Plus actifs</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Groups Grid -->
    <div class="groups-content">
        <div class="container">
            <div class="groups-grid" id="groups-grid">
                <?php if ( $groups_query->have_posts() ) : ?>
                    <?php while ( $groups_query->have_posts() ) : $groups_query->the_post(); ?>
                        <?php
                        $group_id = get_the_ID();
                        $brand_id = get_post_meta( $group_id, '_szr_group_brand_id', true );
                        $model_id = get_post_meta( $group_id, '_szr_group_model_id', true );
                        $members = get_post_meta( $group_id, '_szr_group_members', true ) ?: array();
                        $member_count = count( $members );
                        $is_member = is_user_logged_in() && in_array( get_current_user_id(), $members );

                        $brand = $brand_id ? get_term( $brand_id, 'car_brand' ) : null;
                        $model = $model_id ? get_term( $model_id, 'car_model' ) : null;
                        ?>

                        <div class="group-card" data-brand="<?php echo esc_attr( $brand_id ); ?>" data-members="<?php echo esc_attr( $member_count ); ?>">
                            <a href="<?php the_permalink(); ?>" class="group-card-link">
                                <?php if ( has_post_thumbnail() ) : ?>
                                    <div class="group-thumbnail">
                                        <?php the_post_thumbnail( 'medium' ); ?>
                                    </div>
                                <?php else : ?>
                                    <div class="group-thumbnail placeholder">
                                        <svg width="64" height="64" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>
                                        </svg>
                                    </div>
                                <?php endif; ?>

                                <div class="group-content">
                                    <div class="group-badge">
                                        <?php if ( $brand && $model ) : ?>
                                            <span class="badge-brand"><?php echo esc_html( $brand->name ); ?></span>
                                            <span class="badge-separator">•</span>
                                            <span class="badge-model"><?php echo esc_html( $model->name ); ?></span>
                                        <?php else : ?>
                                            <span class="badge-general">Groupe général</span>
                                        <?php endif; ?>
                                    </div>

                                    <h3 class="group-title"><?php the_title(); ?></h3>

                                    <?php if ( has_excerpt() ) : ?>
                                        <p class="group-excerpt"><?php echo wp_trim_words( get_the_excerpt(), 20 ); ?></p>
                                    <?php endif; ?>

                                    <div class="group-meta">
                                        <div class="meta-item">
                                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                                            </svg>
                                            <span><?php echo $member_count; ?> membre<?php echo $member_count > 1 ? 's' : ''; ?></span>
                                        </div>

                                        <div class="meta-item">
                                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                                            </svg>
                                            <span>Par <?php echo esc_html( get_the_author() ); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </a>

                            <div class="group-actions">
                                <?php if ( is_user_logged_in() ) : ?>
                                    <button
                                        class="join-group-btn <?php echo $is_member ? 'is-member' : ''; ?>"
                                        data-group-id="<?php echo esc_attr( $group_id ); ?>"
                                        data-is-member="<?php echo $is_member ? '1' : '0'; ?>">
                                        <span class="btn-join">Rejoindre</span>
                                        <span class="btn-leave">Quitter</span>
                                    </button>
                                <?php else : ?>
                                    <a href="<?php echo esc_url( wp_login_url( get_permalink() ) ); ?>" class="login-to-join">
                                        Connexion pour rejoindre
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                    <?php wp_reset_postdata(); ?>
                <?php else : ?>
                    <div class="no-groups">
                        <svg width="80" height="80" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>
                        </svg>
                        <h3>Aucun groupe pour le moment</h3>
                        <p>Soyez le premier à créer un groupe de passionnés !</p>
                        <?php if ( is_user_logged_in() ) : ?>
                            <button class="cta-button" id="create-first-group">Créer le premier groupe</button>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if ( $groups_query->max_num_pages > 1 ) : ?>
                <div class="groups-pagination">
                    <?php
                    echo paginate_links( array(
                        'total' => $groups_query->max_num_pages,
                        'current' => $paged,
                        'prev_text' => '← Précédent',
                        'next_text' => 'Suivant →',
                    ) );
                    ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Create Group Modal -->
<div class="modal" id="create-group-modal">
    <div class="modal-overlay"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h2>Créer un nouveau groupe</h2>
            <button class="modal-close">×</button>
        </div>

        <form id="create-group-form" class="modal-form">
            <div class="form-note">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <p>Pour créer un groupe, merci de contacter un administrateur ou d'utiliser l'interface d'administration WordPress (Groupes → Ajouter).</p>
            </div>

            <div class="modal-footer">
                <a href="<?php echo admin_url( 'post-new.php?post_type=car_group' ); ?>" class="cta-button">
                    Aller à l'admin
                </a>
                <button type="button" class="secondary-button modal-close">Annuler</button>
            </div>
        </form>
    </div>
</div>

<style>
<?php include( get_template_directory() . '/styles/page-groupes-styles.css' ); ?>
</style>

<script>
(function() {
    // Search functionality
    const searchInput = document.getElementById('search-groups');
    const brandFilter = document.getElementById('brand-filter');
    const sortFilter = document.getElementById('sort-groups');
    const groupCards = document.querySelectorAll('.group-card');

    function filterGroups() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        const selectedBrand = brandFilter ? brandFilter.value : '';

        groupCards.forEach(card => {
            const title = card.querySelector('.group-title').textContent.toLowerCase();
            const brandId = card.dataset.brand;

            const matchesSearch = title.includes(searchTerm);
            const matchesBrand = !selectedBrand || brandId === selectedBrand;

            card.style.display = (matchesSearch && matchesBrand) ? 'block' : 'none';
        });
    }

    if (searchInput) searchInput.addEventListener('input', filterGroups);
    if (brandFilter) brandFilter.addEventListener('change', filterGroups);

    // Join/Leave group
    document.querySelectorAll('.join-group-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const groupId = this.dataset.groupId;
            const isMember = this.dataset.isMember === '1';

            this.disabled = true;

            fetch('<?php echo admin_url( 'admin-ajax.php' ); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=szr_toggle_group_membership&group_id=${groupId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.dataset.isMember = data.data.is_member ? '1' : '0';
                    this.classList.toggle('is-member');

                    // Update member count
                    const memberCountEl = this.closest('.group-card').querySelector('.meta-item span');
                    if (memberCountEl) {
                        memberCountEl.textContent = `${data.data.member_count} membre${data.data.member_count > 1 ? 's' : ''}`;
                    }
                } else {
                    alert(data.data?.message || 'Une erreur est survenue');
                }
                this.disabled = false;
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Une erreur est survenue');
                this.disabled = false;
            });
        });
    });

    // Modal functionality
    const modal = document.getElementById('create-group-modal');
    const modalTriggers = document.querySelectorAll('#create-group-trigger, #create-first-group');
    const modalCloses = document.querySelectorAll('.modal-close, .modal-overlay');

    modalTriggers.forEach(trigger => {
        trigger?.addEventListener('click', (e) => {
            e.preventDefault();
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        });
    });

    modalCloses.forEach(close => {
        close.addEventListener('click', () => {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        });
    });
})();
</script>

<?php
get_footer();
