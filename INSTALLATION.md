# Guide d'Installation ShiftZoneR

## 🚀 Activation des Pages avec Templates

### Pages Existantes à Configurer

Vous avez déjà créé ces pages dans WordPress. Il faut maintenant leur assigner les bons templates.

#### 1. Page Soumettre Photo (ID: 53)

1. Allez dans **Pages > Toutes les pages**
2. Cliquez sur **Modifier** pour la page ID 53
3. Dans le panneau de droite, section **Attributs de page**
4. Dans le menu déroulant **Modèle**, sélectionnez : **soumettre une photo (marque -> modèle + logo + exif)**
5. Cliquez sur **Mettre à jour**

**Slug recommandé** : `/soumettre-photo/`

#### 2. Page Marques (ID: 177)

1. Allez dans **Pages > Toutes les pages**
2. Cliquez sur **Modifier** pour la page ID 177
3. Dans le panneau de droite, section **Attributs de page**
4. Dans le menu déroulant **Modèle**, sélectionnez : **Page Marques**
5. Cliquez sur **Mettre à jour**

**Slug recommandé** : `/marques/`

#### 3. Page Discussion (ID: 11)

Cette page utilise bbPress. Assurez-vous que :
1. bbPress est activé
2. La page est définie comme "Page Forums" dans **Réglages > Forums > Pages**

**Slug recommandé** : `/discussion/` ou `/forums/`

---

## 📋 Création des Pages Supplémentaires

### Page Galerie

1. **Pages > Ajouter**
2. Titre : **Galerie** ou **Explorer**
3. Slug : `galerie`
4. Template : **Archive Car Photo Template - Galerie**
5. Publier

### Page Carte Interactive

1. **Pages > Ajouter**
2. Titre : **Carte** ou **Carte Interactive**
3. Slug : `carte`
4. Template : **Carte Interactive**
5. Publier

---

## 🔧 Configuration BuddyPress

### 1. Activer les Composants

Allez dans **Réglages > BuddyPress > Composants** et activez :
- ✅ Profils Membres Étendus
- ✅ Groupes Sociaux
- ✅ Flux d'Activités
- ✅ Notifications
- ✅ Paramètres du Compte
- ✅ Messages Privés (optionnel)

### 2. Créer les Pages BuddyPress

BuddyPress devrait créer automatiquement :
- Page Membres
- Page Activité
- Page Groupes

Vérifiez dans **Réglages > BuddyPress > Pages**

### 3. Créer les Groupes pour les Marques Existantes

Ouvrez **Outils > PHP** (via plugin Code Snippets ou Theme Functions) et exécutez :

```php
shiftzoner_init_brand_groups();
```

Cela créera automatiquement un groupe BuddyPress pour chaque marque de voiture dans votre taxonomie.

---

## 🎨 Configuration Easy Watermark

Easy Watermark est déjà installé. Pour éviter les conflits avec notre watermark intégré :

### Option 1 : Utiliser Easy Watermark (recommandé)

1. Allez dans **Réglages > Easy Watermark**
2. Configurez votre filigrane (texte "© ShiftZoneR" ou logo)
3. Cochez **Appliquer automatiquement**
4. Sélectionnez les types de fichiers : **car_photo**

**Dans functions.php, commentez notre watermark** :

```php
// 13. WATERMARKING
// Désactivé car Easy Watermark est utilisé
// add_filter( 'wp_generate_attachment_metadata', 'shiftzoner_apply_watermark', 10, 2 );
```

### Option 2 : Utiliser notre watermark intégré

Désactivez Easy Watermark et notre code intégré fonctionnera automatiquement.

---

## 🛡️ Configuration CAPTCHA 4WP

CAPTCHA 4WP est installé. Pour l'activer sur le formulaire d'upload :

1. Allez dans **CAPTCHA 4WP > Settings**
2. Sélectionnez **Google reCAPTCHA v3** (invisible) ou **v2**
3. Entrez vos clés Google reCAPTCHA
4. Dans **Forms**, cochez : **Comments**, **Login**, **Registration**

**Pour l'ajouter au formulaire de soumission photo**, ajoutez dans `page-soumettre-photo.php` ligne 341 (avant le bouton submit) :

```php
<?php
if ( function_exists( 'c4wp_nocaptcha_form_field' ) ) {
    c4wp_nocaptcha_form_field();
}
?>
```

---

## 🏷️ Création des Taxonomies

### 1. Créer les Marques

Allez dans **Photos Auto > Marques** et créez :
- Ferrari
- Porsche
- Lamborghini
- Mercedes
- BMW
- Audi
- etc.

**Pour chaque marque, uploadez un logo** :
1. Cliquez sur **Modifier** la marque
2. Dans la section personnalisée ACF ou meta, uploadez le logo
3. Sauvegardez

### 2. Créer les Modèles

Allez dans **Photos Auto > Modèles**

**IMPORTANT** : Les modèles doivent être liés aux marques via hiérarchie :

**Méthode 1 : Hiérarchie Parent**
1. Créez d'abord un terme parent avec le nom de la marque (ex: "Ferrari")
2. Puis créez les modèles enfants (ex: "458", "488", "F8")

**Exemple** :
```
Ferrari (parent)
  ├─ 458 Italia
  ├─ 488 GTB
  └─ F8 Tributo

Porsche (parent)
  ├─ 911
  ├─ Cayman
  └─ Taycan
```

**Méthode 2 : Via Meta**
Si vous utilisez ACF, ajoutez un champ "Marque associée" à la taxonomie car_model.

---

## 📝 Configuration Menu (IMPORTANT)

### Menu Principal

Le thème utilise maintenant le système de menus WordPress pour une flexibilité maximale.

1. **Apparence > Menus**
2. Cliquez sur **Créer un nouveau menu**
3. Nom du menu : **Menu Principal**
4. Cliquez sur **Créer le menu**

**Ajoutez vos pages** :

Dans la colonne de gauche, cochez les pages que vous voulez ajouter :

**Pages recommandées** :
- ☑ Accueil (page d'accueil)
- ☑ Page Galerie (ID créée précédemment)
- ☑ Page Marques (ID 177)
- ☑ Page Carte (ID créée précédemment)
- ☑ Page Discussion (ID 11) - si bbPress activé

**Cliquez sur "Ajouter au menu"**

**Pour BuddyPress** (si activé) :

1. Dans la colonne de gauche, cliquez sur **Liens personnalisés**
2. Ajoutez ces liens :
   - URL : `/membres/` - Texte : **Membres**
   - URL : `/groupes/` - Texte : **Groupes** ou **Communauté**
   - URL : `/activite/` - Texte : **Activité**
3. Cliquez sur **Ajouter au menu** pour chacun

**Assignez le menu** :

En bas de la page, dans **Réglages du menu** :
- ☑ Cochez **Menu Principal** dans "Emplacement du thème"
- Cliquez sur **Enregistrer le menu**

### 🎯 Avantages du Menu WordPress

**✅ Le menu apparaîtra automatiquement** :
- Dans le header desktop
- Dans le menu mobile hamburger (avec icônes automatiques !)
- Les icônes sont détectées automatiquement selon le nom (Accueil, Explorer, Carte, etc.)

**✅ Flexibilité totale** :
- Réorganisez l'ordre en drag & drop
- Ajoutez/supprimez des liens facilement
- Créez des sous-menus si besoin
- Pas besoin de modifier le code !

### Menu Footer (optionnel)

1. Créez un menu "Footer"
2. Ajoutez :
   - À propos
   - Conditions d'utilisation
   - Politique de confidentialité
   - Contact
3. Assignez à **Menu Footer**

### 🎨 Icônes Automatiques Menu Mobile

Le thème détecte automatiquement le type de lien et ajoute l'icône correspondante :

- **Accueil** → 🏠 Icône maison
- **Explorer / Galerie** → 🖼️ Icône photos
- **Communauté / Groupes** → 👥 Icône personnes
- **Carte** → 📍 Icône map pin
- **Discussion / Forums** → 💬 Icône chat
- **Profil** → 👤 Icône utilisateur

**Astuce** : Nommez vos éléments de menu avec ces mots-clés pour obtenir les bonnes icônes !

---

## 🎯 Configuration Permaliens

**IMPORTANT** : Pour que les URLs fonctionnent correctement :

1. Allez dans **Réglages > Permaliens**
2. Sélectionnez **Nom de l'article** ou **Structure personnalisée** : `/%postname%/`
3. Cliquez sur **Enregistrer**

Cela activera les URLs propres :
- `/photo/ferrari-458-italia/`
- `/marque/ferrari/`
- `/modele/458-italia/`

---

## 🖼️ Premier Upload Test

1. Connectez-vous à WordPress
2. Allez sur `/soumettre-photo/`
3. Vérifiez que :
   - Le formulaire s'affiche
   - Les marques apparaissent dans le select
   - En choisissant une marque, les modèles se chargent
   - Le compteur "X photos restantes aujourd'hui" s'affiche
   - Le drag & drop fonctionne

4. Uploadez une photo avec EXIF GPS (prenez une photo avec votre téléphone avec géolocalisation activée)

5. Vérifiez après upload :
   - La photo apparaît sur la page d'accueil (section "Dernières Photos")
   - Elle apparaît dans la galerie `/galerie/`
   - Elle apparaît sur la carte `/carte/` (si GPS)
   - Une activité BuddyPress est créée
   - Le watermark est appliqué

---

## 🔍 Vérifications Post-Installation

### ✅ Checklist

- [ ] Template "soumettre une photo" assigné à page ID 53
- [ ] Template "Page Marques" assigné à page ID 177
- [ ] Page Galerie créée avec template Archive
- [ ] Page Carte créée avec template Carte Interactive
- [ ] BuddyPress activé avec composants
- [ ] Groupes créés pour marques (via `shiftzoner_init_brand_groups()`)
- [ ] Au moins 3 marques créées avec logos
- [ ] Modèles créés avec hiérarchie parent correcte
- [ ] Menu principal créé et assigné
- [ ] Permaliens en "Nom de l'article"
- [ ] Easy Watermark ou notre watermark configuré
- [ ] CAPTCHA 4WP configuré (optionnel)
- [ ] Premier upload test réussi

---

## 🎨 Personnalisation Customizer

1. Allez dans **Apparence > Personnaliser**
2. Section **Identité du site** :
   - Uploadez votre logo ShiftZoneR
3. Section **Page d'accueil ShiftZoneR** :
   - Personnalisez les textes hero, Rafael, communauté, CTA
4. Section **Réseaux Sociaux** :
   - Ajoutez vos URLs Instagram, Facebook, Twitter

---

## 🐛 Dépannage

### Le formulaire d'upload ne s'affiche pas
- Vérifiez que le template est bien assigné à la page
- Vérifiez que vous êtes connecté
- Vérifiez les erreurs PHP dans **Outils > Santé du site > Informations > Journaux**

### Les modèles ne se chargent pas quand je sélectionne une marque
- Vérifiez que les modèles ont bien un parent correspondant à la marque
- Vérifiez dans la console navigateur (F12) s'il y a des erreurs AJAX
- L'URL AJAX devrait être `/wp-admin/admin-ajax.php`

### Les photos n'apparaissent pas sur la carte
- Vérifiez que les photos ont bien des données GPS EXIF
- Vérifiez que Leaflet se charge (console navigateur F12)
- Testez avec une photo prise au téléphone avec GPS activé

### Le watermark ne s'applique pas
- Si Easy Watermark est activé, configurez-le dans **Réglages > Easy Watermark**
- Si vous utilisez notre code, vérifiez que la fonction GD est activée : `phpinfo()` > chercher "GD Support"

### Erreur 404 sur les pages
- Allez dans **Réglages > Permaliens** et cliquez sur **Enregistrer** pour rafraîchir les règles de réécriture

---

## 📞 Support

Pour toute question :
1. Vérifiez ce guide d'installation
2. Consultez le README.md principal
3. Vérifiez les logs dans **Outils > Santé du site**
4. Testez avec les thèmes par défaut WordPress pour isoler le problème

---

## 🚀 Prêt à Démarrer !

Une fois toutes ces étapes complétées, votre plateforme ShiftZoneR est opérationnelle !

Bon lancement ! 🏎️📸
