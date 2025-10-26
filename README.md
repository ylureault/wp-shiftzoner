# ShiftZoneR - Thème WordPress

Thème WordPress moderne pour la plateforme communautaire ShiftZoneR dédiée aux passionnés d'automobile et de photographie.

## Description

ShiftZoneR est une plateforme permettant de publier, explorer et commenter des photos de voitures classées par marque, modèle et année, avec un espace communautaire type forum pour discuter, échanger et voter sur les contenus.

## Fonctionnalités

### 🚗 Gestion des Photos
- **Custom Post Type** `car_photo` avec taxonomies (marque, modèle, année, tags)
- **Upload frontal** avec extraction automatique des données EXIF (GPS, date)
- **Système de votes** (upvote/downvote) avec calcul du karma utilisateur
- **Géolocalisation** automatique depuis les métadonnées EXIF
- **Watermark** automatique sur les images uploadées
- **Filtrage dynamique** par marque, modèle, année, tags

### 👥 Communauté
- **Système de karma** pour récompenser les contributions
- **Couleur personnalisée** par utilisateur (affichée partout)
- **Badge propriétaire** pour Rafael (créateur)
- **Profils publics** avec statistiques et galerie personnelle
- **Commentaires imbriqués** avec modération
- **Signalement de contenu** avec seuil de masquage automatique

### 🗺️ Carte Interactive
- **Affichage géolocalisé** des lieux de prise de vue
- **Filtres** par marque, modèle, année, contributeur
- **Clustering** des marqueurs
- **Popup** avec miniature et lien vers la photo

### 🎨 Design Moderne
- **Interface dark mode** avec couleurs vives (rouge #E50914, cyan #00AEEF)
- **Animations fluides** et effets au scroll
- **Responsive** mobile-first
- **Grilles masonry** pour les galeries
- **Lightbox** pour affichage plein écran

## Structure du Thème

```
wp-shiftzoner/
├── assets/
│   ├── css/          # Fichiers CSS additionnels
│   ├── js/           # Fichiers JavaScript additionnels
│   └── images/       # Images du thème
├── template-parts/
│   └── content-photo-card.php    # Template pour les cartes photos
├── style.css                     # Feuille de style principale (requis)
├── functions.php                 # Fonctions et hooks WordPress
├── header.php                    # En-tête du site
├── footer.php                    # Pied de page du site
├── index.php                     # Template par défaut
├── front-page.php                # Page d'accueil
├── single-car_photo.php          # Affichage d'une photo
├── archive-car_photo.php         # Galerie de photos
├── page-marques.php              # Page des marques
├── page-soumettre-photo.php      # Formulaire d'upload
├── page-carte.php                # Carte interactive
└── README.md                     # Documentation
```

## Installation

### Prérequis
- WordPress 5.0+
- PHP 7.4+
- Plugins recommandés :
  - **BuddyPress** : Profils et réseau social ✅
  - **bbPress** : Forum de discussion
  - **Advanced Custom Fields (ACF)** : Champs personnalisés
  - **Easy Watermark** : Filigrane automatique
  - **Leaflet Maps** : Carte interactive

### Installation du thème

1. Téléchargez le thème
2. Uploadez le dossier dans `wp-content/themes/`
3. Activez le thème depuis l'admin WordPress
4. Installez et activez les plugins recommandés
5. Créez les pages nécessaires :
   - Galerie (avec template "Archive")
   - Carte (avec template "Carte Interactive")
   - Marques (avec template "Page Marques")
   - Soumettre une photo (avec template "Soumettre une photo")

### Configuration du logo

1. Allez dans **Apparence > Personnaliser > Identité du site**
2. Uploadez votre logo (recommandé : 400x100px, PNG transparent)
3. Le logo remplacera automatiquement le texte "SHIFTZONER"

### Personnalisation des textes

1. Allez dans **Apparence > Personnaliser**
2. Ouvrez la section **Page d'accueil ShiftZoneR**
3. Modifiez les textes :
   - Titre et sous-titre Hero
   - Titre et sous-titre Section Rafael
   - Titre et sous-titre Section Communauté
   - Titre et sous-titre CTA
4. Ouvrez la section **Réseaux Sociaux**
5. Ajoutez vos liens Instagram, Facebook, Twitter

## Configuration

### Custom Post Type et Taxonomies

Le thème crée automatiquement :

- **Post Type** : `car_photo`
- **Taxonomies** :
  - `car_brand` (Marque) - hiérarchique
  - `car_model` (Modèle) - hiérarchique
  - `car_year` (Année)
  - `photo_tag` (Tags)

### Métadonnées Photos

Chaque photo stocke :
- `_szr_vote_score` : Score des votes
- `_szr_gps_lat` : Latitude GPS
- `_szr_gps_lng` : Longitude GPS
- `_szr_gps_alt` : Altitude
- `_szr_taken_at` : Date de prise de vue
- `_szr_views` : Nombre de vues

### Métadonnées Utilisateurs

- `_szr_user_color` : Couleur personnalisée (hex)
- `_szr_karma` : Points de karma
- `_szr_level` : Niveau basé sur le karma

## Fonctions AJAX

Le thème utilise AJAX pour :

### Votes
```javascript
Action: szr_vote
Params: post_id, vote (up/down)
```

### Filtrage Photos
```javascript
Action: szr_filter_photos
Params: search, brand, model, year, sort, page
```

### Signalement
```javascript
Action: szr_report
Params: post_id, reason
```

### Carte - Photos GPS
```javascript
Action: szr_map_photos
Params: brand, model, author
```

### Modèles par Marque
```javascript
Action: szr_get_models
Params: brand_id
```

## Shortcodes

### Galerie
```php
[shiftzoner_gallery brand="ferrari" model="458" limit="12"]
```

### Carte
```php
[shiftzoner_map height="600px" brand="porsche"]
```

### Sélecteur de véhicule
```php
[car_selector]
```

### Notifications
```php
[shiftzoner_notifications]
```

## Personnalisation

### Couleurs

Les couleurs sont définies en CSS variables dans `style.css` :

```css
:root {
    --primary: #E50914;      /* Rouge principal */
    --secondary: #00AEEF;    /* Cyan */
    --dark: #0a0a0a;         /* Fond sombre */
    --dark-gray: #1a1a1a;    /* Gris foncé */
    --light-gray: #2a2a2a;   /* Gris clair */
    --text: #ffffff;         /* Texte blanc */
    --text-muted: #a0a0a0;   /* Texte grisé */
}
```

### Typographie

- **Titres** : Bebas Neue, Anton
- **Texte** : Inter, Lato, Segoe UI

### Hooks Disponibles

```php
// Avant l'affichage d'une photo
do_action( 'szr_before_photo', $post_id );

// Après un vote
do_action( 'szr_after_vote', $post_id, $user_id, $vote_type );

// Après un upload
do_action( 'szr_after_upload', $post_id, $attachment_id );

// Avant la modération
do_action( 'szr_before_moderation', $post_id, $report_count );
```

## Sécurité

- **Nonce** pour tous les formulaires
- **Sanitization** des données entrantes
- **Escape** des données sortantes
- **Rate limiting** sur les uploads (100 par jour)
- **Captcha** après 5 uploads
- **Modération** a posteriori avec signalement

## SEO

Le thème intègre un SEO optimisé automatiquement :

### Meta Tags
- Description et keywords automatiques basés sur le contenu
- Balises author et dates (published/modified)
- Titre optimisé : `[Marque] [Modèle] [Année] - ShiftZoneR`

### Open Graph
- Partage optimisé sur Facebook, LinkedIn
- og:title, og:description, og:image, og:url
- og:type = "article" pour les photos
- Dimensions image : 1200x630

### Twitter Card
- Type : `summary_large_image`
- Image, titre et description automatiques

### Schema.org (Structured Data)
- **ImageObject** pour chaque photo avec :
  - Métadonnées complètes (titre, description, dates)
  - Auteur et publisher
  - Géolocalisation GPS (si disponible)
- **WebSite** pour la homepage avec :
  - SearchAction pour la recherche

### Optimisation
- Sitemap XML automatique (via WordPress)
- URLs propres : `/photo/marque-modele-annee/`
- Images avec attributs alt automatiques
- Balises H1, H2, H3 structurées
- Meta viewport pour mobile

## Performance

- **Lazy loading** natif pour les images
- **AJAX** pour le chargement progressif
- **Caching** des requêtes lourdes
- **Optimisation** des images (WebP)
- **Minification** recommandée pour production
- **Compteur de vues** optimisé

## Support Navigateurs

- Chrome/Edge (dernières versions)
- Firefox (dernières versions)
- Safari (dernières versions)
- Mobile (iOS Safari, Chrome Android)

## Changelog

### Version 1.1.0 (2025-01-26)
- ✨ **Logo personnalisé** : Support du logo WordPress dans le header
- ⚙️ **Customizer** : Personnalisation des textes via Apparence > Personnaliser
- 🚀 **SEO optimisé** : Meta tags, Open Graph, Twitter Card, Schema.org
- 📸 **Dernières photos** : Section dédiée en haut de la page d'accueil
- 👥 **BuddyPress** : Intégration complète avec redirection profil
- 🔧 **AJAX** : Filtrage photos, modèles par marque, carte interactive
- 📊 **Compteur vues** : Incrémentation automatique des vues
- 🎨 **Tailles images** : Formats optimisés (large, medium, thumb)

### Version 1.0.0 (2025-01-26)
- Version initiale
- Design moderne avec animations
- Système de votes et karma
- Carte interactive
- Upload avec EXIF
- Filtres dynamiques
- Templates complets

## Crédits

- **Développement** : Claude (Anthropic)
- **Design** : Inspiré du template HTML moderne
- **Créateur** : Rafael
- **Plateforme** : ShiftZoneR

## Support

Pour toute question ou problème, veuillez :
1. Vérifier la documentation
2. Consulter les issues GitHub
3. Contacter l'équipe ShiftZoneR

## Licence

Tous droits réservés - ShiftZoneR © 2025
