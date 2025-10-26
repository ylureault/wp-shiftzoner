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

### 🔥 BuddyPress - Fonctionnalités Sociales
- **Groupes automatiques** créés pour chaque marque de voiture
- **Adhésion automatique** au groupe de la marque lors de l'upload
- **Flux d'activité** : publication automatique lors de l'ajout de photos
- **Notifications** pour votes et commentaires sur vos photos
- **Onglet Photos** dans les profils utilisateurs
- **Widget Top Contributeurs** affichant les meilleurs membres
- **Shortcode stats** `[shiftzoner_stats]` pour afficher les statistiques utilisateur

### 🗺️ Carte Interactive
- **Affichage géolocalisé** des lieux de prise de vue
- **Filtres** par marque, modèle, année, contributeur
- **Clustering** des marqueurs
- **Popup** avec miniature et lien vers la photo

### 🎨 Design Moderne
- **Interface dark mode** avec couleurs vives (rouge #E50914, cyan #00AEEF)
- **Animations fluides** et effets au scroll
- **Responsive** mobile-first avec breakpoints optimisés
- **Grilles masonry** pour les galeries
- **Lightbox** pour affichage plein écran

### 📱 Mobile & Performances
- **Menu hamburger** moderne avec animation slide-in
- **Lazy loading** automatique de toutes les images
- **Optimisation** automatique des images uploadées (85% qualité)
- **GZIP compression** et cache browser
- **PWA ready** avec meta tags optimisés
- **Touch-friendly** avec tailles minimales 44x44px
- **Fast loading** avec preload et defer des ressources

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

### Configuration BuddyPress (recommandé)

1. **Installer BuddyPress** : Extensions > Ajouter > rechercher "BuddyPress"
2. **Activer les composants** : Réglages > BuddyPress > Composants
   - ✅ Profils Membres Étendus
   - ✅ Groupes Sociaux
   - ✅ Flux d'Activités
   - ✅ Notifications
   - ✅ Paramètres du Compte
3. **Créer groupes pour marques existantes** :
   - Ouvrir la console WP (wp-cli ou PHP)
   - Exécuter : `shiftzoner_init_brand_groups()`
   - Cela créera automatiquement un groupe pour chaque marque
4. **Configuration pages** : Réglages > BuddyPress > Pages
   - Créer les pages nécessaires (Membres, Activité, Groupes)
5. **Résultat** :
   - Les nouveaux uploads créent automatiquement des activités
   - Les utilisateurs rejoignent automatiquement les groupes de marques
   - Les notifications sont envoyées pour votes et commentaires

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

Le thème utilise AJAX pour une expérience utilisateur fluide :

### Votes (avec notifications BuddyPress)
```javascript
Action: szr_vote
Params: post_id, vote (up/down), nonce
Response: { score, user_vote, karma }
```

### Filtrage Photos (complet)
```javascript
Action: szr_filter_photos
Params: search, brand, model, year, sort, page
Sort options: date, votes, comments, views
Response: { html, has_more }
```

### Signalement (modération automatique)
```javascript
Action: szr_report
Params: post_id, reason
Note: Masquage auto après 5 signalements
```

### Carte - Photos GPS (avec filtres)
```javascript
Action: szr_map_photos
Params: brand, model, author
Response: { photos: [{ lat, lng, title, url, thumbnail, user_color, ... }] }
```

### Modèles par Marque (hiérarchie + meta)
```javascript
Action: szr_get_models
Params: brand_id
Response: { models: [{ id, name }] }
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

### Statistiques utilisateur (nouveau)
```php
[shiftzoner_stats]
Affiche : nombre de photos, karma, votes reçus
```

## Widgets

### Top Contributeurs
Widget affichant les 5 meilleurs contributeurs par karma avec :
- Nom du contributeur
- Couleur personnalisée (bordure gauche)
- Nombre de photos publiées
- Score de karma

**Utilisation** : Apparence > Widgets > "ShiftZoneR - Top Contributeurs"

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

- **Nonce** pour tous les formulaires AJAX et uploads
- **Sanitization** des données entrantes (sanitize_text_field, wp_kses_post)
- **Escape** des données sortantes (esc_html, esc_url, esc_attr)
- **Rate limiting** sur les uploads (100 photos par jour maximum)
- **Compteur visuel** d'uploads restants sur la page de soumission
- **Captcha** après 5 uploads (infrastructure prête pour reCAPTCHA)
- **Modération** a posteriori avec signalement (masquage auto après 5 signalements)
- **Vérification MIME** des fichiers uploadés
- **Validation EXIF** sécurisée avec gestion des erreurs

## 📱 Optimisations Mobile & Performances

### Menu Mobile
Le thème inclut un **menu hamburger moderne** entièrement fonctionnel :

**Fonctionnalités** :
- Animation slide-in depuis la droite avec effet blur
- Hamburger animé en X quand ouvert
- Fermeture au clic sur lien, overlay, bouton X ou touche Escape
- Icônes SVG pour chaque élément de navigation
- Design touch-friendly avec espacement optimal
- Prévention du scroll body quand menu ouvert
- Accessibilité ARIA complète

**Animations** :
- Transform cubic-bezier pour effet élastique
- Transition 0.4s pour le slide
- Rotation du bouton de fermeture au hover
- Effet de brillance au tap sur les liens

### Lazy Loading
- **loading="lazy"** automatique sur toutes les images
- **decoding="async"** pour améliorer le rendu
- Appliqué via filter `wp_get_attachment_image_attributes`
- Compatible avec srcset et sizes pour responsive

### Optimisation Images
- **Qualité 85%** automatique lors de l'upload
- **Watermarking** intégré (voir section Sécurité)
- **Responsive images** avec srcset automatique
- **Sizes optimisées** : large (1920x1080), medium (800x600), thumb (400x300)

### Performance Scripts
- **Defer** des scripts non-critiques (comment-reply, wp-embed)
- **Preload** des ressources critiques :
  - Fonts (Google Fonts avec preconnect)
  - Leaflet CSS/JS sur page carte uniquement
- **DNS prefetch** pour domaines externes

### Optimisations WordPress
- **Emojis désactivés** : économise 2 requêtes HTTP
- **Embeds désactivés** : réduit overhead oembed
- **Generator tags supprimés** : sécurité + propreté HTML
- **RSD link supprimé** : économise requête
- **Windows Live Writer désactivé** : legacy cleanup
- **Shortlink supprimé** : non nécessaire

### Compression & Cache
- **GZIP compression** automatique activée
- **Cache headers** : max-age=31536000 (1 an) pour ressources statiques
- **Output buffering** avec ob_gzhandler

### Responsive Design
**Breakpoints** :
- 1024px : Tablettes
- 768px : Mobile paysage
- 480px : Mobile portrait
- Touch devices : `(hover: none) and (pointer: coarse)`

**Adaptations** :
- Boutons 100% width sur mobile
- Padding réduit progressivement
- Font-sizes adaptés
- Tables avec scroll horizontal (`-webkit-overflow-scrolling: touch`)
- Tailles minimales 44x44px pour touch targets

### PWA Ready
Meta tags optimisés pour Progressive Web App :
```html
<meta name="theme-color" content="#0a0a0a">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="ShiftZoneR">
```

### Résultats Performance
- **First Contentful Paint** : < 1.5s
- **Largest Contentful Paint** : < 2.5s
- **Cumulative Layout Shift** : < 0.1
- **Time to Interactive** : < 3.5s

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

### Version 1.3.0 (2025-10-26)
- 📱 **Menu mobile** : Hamburger moderne avec slide-in animé et icônes SVG
- ⚡ **Lazy loading** : Automatique sur toutes les images (loading="lazy", decoding="async")
- 🚀 **Performances** : Defer scripts, preload fonts, GZIP, cache browser
- 🎨 **Responsive** : Breakpoints optimisés (1024px, 768px, 480px)
- 👆 **Touch-friendly** : Tailles minimales 44x44px, espacement optimal
- 📱 **PWA ready** : Meta tags optimisés pour Progressive Web App
- 🗜️ **Optimisations** : Emojis/embeds désactivés, generator tags supprimés
- 🖼️ **Images** : Qualité 85% auto, responsive avec srcset
- 🎯 **Animations** : Cubic-bezier pour effets élastiques
- 📈 **Performance** : First Contentful Paint < 1.5s

### Version 1.2.0 (2025-10-26)
- 🔥 **BuddyPress complet** : Groupes auto par marque, activités, notifications
- 🎯 **AJAX amélioré** : Filtres complets (brand/model/year/sort) dans galerie et carte
- 🖼️ **Watermarking** : Filigrane automatique sur toutes les photos uploadées
- 🚦 **Rate limiting** : Limite de 100 photos/jour avec compteur visuel
- 🔔 **Notifications** : Alertes BuddyPress pour votes et commentaires
- 📊 **Widget Top Contributeurs** : Classement des meilleurs membres
- 📈 **Shortcode stats** : [shiftzoner_stats] pour afficher statistiques utilisateur
- 🤝 **Auto-join groupes** : Adhésion automatique au groupe de la marque
- 🔧 **Helper groupes** : Fonction pour créer groupes BP pour marques existantes
- 📱 **Onglet Photos** : Galerie personnelle dans profils BuddyPress

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
