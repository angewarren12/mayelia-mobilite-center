# Plan de Refonte Graphique - Mayelia Mobilité

Ce document détaille le plan de refonte des couleurs et de l'identité visuelle de l'application, en se basant sur la nouvelle charte graphique (Vert et Blanc).

## 1. Nouvelle Palette de Couleurs

Basée sur les informations fournies (Pantone / RGB 2, 145, 63) :

- **Couleur Principale (Vert Mayelia)** : `#02913F` (RGB: 2, 145, 63)
- **Couleur Secondaire (Blanc)** : `#FFFFFF`
- **Dégradé (Gradient)** : Un dégradé linéaire allant du Vert Mayelia vers un vert plus clair ou vers le blanc, selon les zones.

### Configuration Tailwind proposée
Nous allons étendre la configuration Tailwind pour inclure ces couleurs personnalisées.

```javascript
// tailwind.config.js
export default {
    theme: {
        extend: {
            colors: {
                mayelia: {
                    DEFAULT: '#02913F', // Vert principal
                    50: '#f0fdf4',
                    100: '#dcfce7',
                    200: '#bbf7d0',
                    300: '#86efac',
                    400: '#4ade80',
                    500: '#02913F', // Notre vert cible (approx)
                    600: '#16a34a',
                    700: '#15803d',
                    800: '#166534',
                    900: '#14532d',
                }
            }
        }
    }
}
```

## 2. Modifications de la Sidebar (Barre latérale)

La sidebar sera entièrement revue pour correspondre à la nouvelle identité.

- **Fond (Background)** : Dégradé vertical Vert (#02913F) vers un vert légèrement plus foncé ou plus clair pour donner de la profondeur, ou Vert vers Blanc si demandé explicitement (bien que vert vers blanc puisse rendre le texte difficile à lire au milieu, nous privilégierons un fond vert riche avec texte blanc, ou un fond blanc avec accents verts selon la préférence "dégradé avec vert et blanc").
    - *Proposition* : Fond principal en dégradé de vert (`bg-gradient-to-b from-mayelia-600 to-mayelia-800`) pour un look premium.
- **Logo** : Remplacement du "M" générique par le fichier `LogoMobilité.svg`.
    - Le fichier devra être déplacé de `resources/img/` vers `public/img/` pour être accessible.
- **Navigation** :
    - Liens inactifs : Texte blanc avec transparence (ex: `text-white/80`).
    - Liens actifs : Fond blanc avec texte vert (`bg-white text-mayelia-600`) ou fond vert plus foncé.
    - Icônes : Assorties au texte.

## 3. Modifications Globales (UI Kit)

Remplacement des accents bleus (`blue-600`, `blue-50`, etc.) par les nouvelles couleurs `mayelia`.

- **Boutons** :
    - Primaire : `bg-mayelia-500 hover:bg-mayelia-600 text-white`.
    - Secondaire : `bg-white text-mayelia-500 border border-mayelia-500`.
- **Badges / Étiquettes** :
    - Succès/Actif : `bg-green-100 text-green-800` (déjà proche).
    - Info/Primaire : `bg-mayelia-100 text-mayelia-800`.
- **Liens** : `text-mayelia-600 hover:text-mayelia-800`.
- **Focus rings** : `focus:ring-mayelia-500`.

## 4. Étapes de Mise en Œuvre

1.  **Préparation des Assets** :
    - Copier `resources/img/LogoMobilité.svg` vers `public/img/LogoMobilité.svg`.
2.  **Configuration CSS** :
    - Mettre à jour `tailwind.config.js` avec la palette `mayelia`.
3.  **Refonte du Layout (`layouts/dashboard.blade.php`)** :
    - Appliquer le dégradé sur la sidebar.
    - Intégrer le logo SVG.
    - Mettre à jour les classes de couleurs des liens de navigation.
4.  **Nettoyage** :
    - Vérifier les autres vues pour remplacer les occurrences de `blue-600` codées en dur par les classes utilitaires `mayelia` ou `primary` si on refactorise.

## 5. Exemple de Code pour la Sidebar

```html
<div class="w-64 bg-gradient-to-b from-mayelia-600 to-mayelia-800 shadow-lg text-white">
    <div class="p-6 flex justify-center">
        <img src="{{ asset('img/LogoMobilité.svg') }}" alt="Mayelia Mobilité" class="h-12 w-auto">
    </div>
    
    <nav class="mt-6">
        <a href="#" class="flex items-center px-6 py-3 text-white/90 hover:bg-white/10 hover:text-white">
            <!-- ... -->
        </a>
        <!-- Lien Actif -->
        <a href="#" class="flex items-center px-6 py-3 bg-white text-mayelia-700 border-r-4 border-mayelia-900">
            <!-- ... -->
        </a>
    </nav>
</div>
```
