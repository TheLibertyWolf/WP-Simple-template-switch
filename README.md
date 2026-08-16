# WP Simple Template Switch

WP Simple Template Switch permet à des utilisateurs WordPress autorisés de choisir le thème qu’ils voient sur le site public, sans modifier le thème actif pour les autres visiteurs.

[![Version](https://img.shields.io/badge/version-1.0.0-2271b1)](https://github.com/TheLibertyWolf/WP-Simple-template-switch/releases)
[![PHP](https://img.shields.io/badge/PHP-%3E%3D%208.1-777bb4)](https://www.php.net/)
[![WordPress](https://img.shields.io/badge/WordPress-%3E%3D%206.5-21759b)](https://wordpress.org/)
[![License](https://img.shields.io/badge/license-GPL--2.0--or--later-46b450)](LICENSE)

## Fonctionnalités

- choix personnel du thème depuis la page de profil ;
- changement rapide depuis la barre d’administration WordPress ;
- retour immédiat au thème par défaut du site ;
- activation ou désactivation globale de la fonctionnalité ;
- autorisation pour tous les comptes connectés, certains rôles ou certains utilisateurs ;
- accès aux réglages configurable par rôle et par utilisateur ;
- aptitude dédiée `manage_wp_simple_template_switch` compatible avec les gestionnaires de rôles ;
- détection automatique des thèmes installés, y compris les thèmes enfants ;
- prise en charge correcte de la relation parent/enfant via les filtres `template` et `stylesheet` ;
- préférence enregistrée dans le profil WordPress, sans cookie personnalisé ;
- interface de réglages inspirée des cartes natives WordPress ;
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

Le nouveau thème apparaît automatiquement sur la page de profil et dans la barre d’administration des utilisateurs autorisés.

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

## Contribution et support

- [Guide de contribution](CONTRIBUTING.md)
- [Support](SUPPORT.md)
- [Code de conduite](CODE_OF_CONDUCT.md)
- [Historique des versions](CHANGELOG.md)

## Auteur

SAS Jessy System — [https://jessysystem.com](https://jessysystem.com)

## Licence

GPL-2.0-or-later. Voir [LICENSE](LICENSE).
