# WP Simple Template Switch

WP Simple Template Switch permet à des utilisateurs WordPress autorisés de choisir le thème qu’ils voient sur le site public, sans modifier le thème actif pour les autres visiteurs.

[![Version](https://img.shields.io/badge/version-2.1.1-2271b1)](https://github.com/TheLibertyWolf/WP-Simple-template-switch/releases)
[![PHP](https://img.shields.io/badge/PHP-%3E%3D%208.1-777bb4)](https://www.php.net/)
[![WordPress](https://img.shields.io/badge/WordPress-%3E%3D%206.5-21759b)](https://wordpress.org/)
[![License](https://img.shields.io/badge/license-GPL--2.0--or--later-46b450)](LICENSE)
[![Languages](https://img.shields.io/badge/languages-FR%20%7C%20EN%20%7C%20DE%20%7C%20IT%20%7C%20ES%20%7C%20PT-f48120)](#langues)

## Fonctionnalités

- choix personnel du thème depuis la page de profil ;
- choix entre un sélecteur de thèmes et une bascule directe dans la barre d’administration WordPress ;
- retour immédiat au thème par défaut du site ;
- activation ou désactivation globale de la fonctionnalité ;
- autorisation pour tous les comptes connectés, certains rôles ou certains utilisateurs ;
- accès initial limité aux administrateurs lors d’une nouvelle activation ;
- liste blanche des thèmes proposés dans les sélecteurs ;
- tableau indiquant le thème employé par chaque utilisateur ;
- accès aux réglages configurable par rôle et par utilisateur ;
- aptitude dédiée `manage_wp_simple_template_switch` compatible avec les gestionnaires de rôles ;
- détection automatique des thèmes installés, y compris les thèmes enfants ;
- prise en charge correcte de la relation parent/enfant via les filtres `template` et `stylesheet` ;
- préférence enregistrée dans le profil WordPress, sans cookie personnalisé ;
- interface de réglages inspirée des cartes natives WordPress ;
- interface traduite en français, anglais, allemand, italien, espagnol et portugais ;
- suppression complète des options, préférences et aptitudes lors de la désinstallation.

## Cas d’usage

Le plugin est particulièrement utile pour :

- développer un nouveau thème en parallèle du site public ;
- faire valider une refonte à une équipe éditoriale ;
- comparer un thème parent et un thème enfant ;
- limiter une prévisualisation à des comptes ou rôles précis.

## Installation

1. Copier le dossier `wp-simple-template-switch` dans `wp-content/plugins/`.
2. Activer **WP Simple Template Switch** dans WordPress.
3. Ouvrir **Réglages → Template Switch**.
4. Choisir qui peut sélectionner un thème et qui peut administrer les réglages.
5. Installer le thème à tester sans l’activer globalement.
6. Cocher ce thème dans la liste des thèmes disponibles dans le switch.

Une fois coché dans les réglages, le nouveau thème apparaît sur la page de profil et dans la barre d’administration des utilisateurs autorisés.

## Captures d’écran

### Réglages et gestion des accès

<a href="https://i.postimg.cc/wjY3PBNf/Capture-d-e-cran-2026-08-16-a-14-11-04.png"><img src="https://i.postimg.cc/wjY3PBNf/Capture-d-e-cran-2026-08-16-a-14-11-04.png" alt="Réglages de WP Simple Template Switch avec gestion des rôles et utilisateurs"></a>

### Sélecteur sur la page de profil

<a href="https://i.postimg.cc/sgC182W0/Capture-d-e-cran-2026-08-16-a-14-11-26.png"><img src="https://i.postimg.cc/sgC182W0/Capture-d-e-cran-2026-08-16-a-14-11-26.png" alt="Sélecteur de thème personnel sur le profil WordPress"></a>

### Sélecteur ou switch dans la barre d’administration

<a href="https://i.postimg.cc/QdZVPM7y/Capture-d-e-cran-2026-08-16-a-14-12-21.png"><img src="https://i.postimg.cc/QdZVPM7y/Capture-d-e-cran-2026-08-16-a-14-12-21.png" alt="Sélecteur de thème dans la barre d’administration WordPress"></a>

Le mode **Sélecteur de thèmes** affiche un véritable champ déroulant directement dans la barre. Le mode **Bouton switch** bascule en un clic entre le thème du site et le premier thème alternatif. Le mode se choisit dans **Réglages → Template Switch**.

## Fonctionnement

La préférence est stockée dans la métadonnée utilisateur `wp_simple_template_switch_theme`. Sur le site public, les filtres WordPress `stylesheet` et `template` chargent le thème choisi pour cet utilisateur uniquement. Le thème actif du site et les visiteurs non connectés ne sont pas affectés.

Le sélecteur ne remplace pas l’outil de prévisualisation WordPress et n’active jamais un thème globalement.

## Gestion des accès

Deux politiques sont indépendantes :

1. **Choix du thème** : tous les utilisateurs connectés, une sélection de rôles ou une sélection de comptes.
2. **Administration du plugin** : rôles et comptes sélectionnés dans la page de réglages, super-administrateurs Multisite et détenteurs de l’aptitude dédiée.

Pour déléguer l’administration avec Members, User Role Editor ou un outil équivalent, attribuez :

```text
manage_wp_simple_template_switch
```

Cette aptitude est accordée au rôle Administrateur lors de l’activation.

Les thèmes proposés forment une troisième politique indépendante : seuls les thèmes cochés dans **Réglages → Template Switch** apparaissent dans le profil et la barre d’administration. Le thème par défaut du site reste toujours disponible.

## Sécurité

- chaque changement de thème est protégé par une nonce WordPress ;
- toutes les autorisations sont revérifiées côté serveur ;
- seuls les thèmes réellement installés peuvent être sélectionnés ;
- les redirections utilisent les fonctions sécurisées de WordPress ;
- les entrées sont validées et toutes les sorties d’administration sont échappées.

Consultez [SECURITY.md](SECURITY.md) avant de signaler une vulnérabilité.

## Compatibilité

- WordPress 6.5 ou version ultérieure ;
- PHP 8.1 ou version ultérieure ;
- installation WordPress simple ou Multisite ;
- thèmes classiques, thèmes blocs, thèmes parents et enfants.

## Langues

Le plugin utilise automatiquement la langue configurée dans WordPress ou dans le profil utilisateur. Le français est la langue source. Des catalogues sont fournis pour l’anglais (`en_US`), l’allemand (`de_DE`), l’italien (`it_IT`), l’espagnol (`es_ES`) et le portugais (`pt_PT`).

| Langue | Locale WordPress | Couverture | État |
|---|---|---:|---|
| 🇫🇷 Français | `fr_FR` | 73/73 — 100 % | Langue source |
| 🇬🇧 Anglais | `en_US` | 73/73 — 100 % | Complet |
| 🇩🇪 Allemand | `de_DE` | 73/73 — 100 % | Complet |
| 🇮🇹 Italien | `it_IT` | 73/73 — 100 % | Complet |
| 🇪🇸 Espagnol | `es_ES` | 73/73 — 100 % | Complet |
| 🇵🇹 Portugais | `pt_PT` | 73/73 — 100 % | Complet |

Les informations techniques et les règles de contribution sont détaillées dans [TRANSLATIONS.md](TRANSLATIONS.md).

## Contribution et support

- [Guide de contribution](CONTRIBUTING.md)
- [Support](SUPPORT.md)
- [Code de conduite](CODE_OF_CONDUCT.md)
- [Historique des versions](CHANGELOG.md)

## Auteur

SAS Jessy System — [https://jessysystem.com](https://jessysystem.com)

## Licence

GPL-2.0-or-later. Voir [LICENSE](LICENSE).
