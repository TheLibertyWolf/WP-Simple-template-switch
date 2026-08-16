=== WP Simple Template Switch ===
Contributors: jessysystem
Tags: theme, switcher, user, development, preview
Requires at least: 6.5
Tested up to: 7.0
Requires PHP: 8.1
Stable tag: 2.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Permet aux utilisateurs autorisés de choisir leur thème WordPress sans modifier le thème actif pour les autres visiteurs.

== Description ==

WP Simple Template Switch ajoute un sélecteur de thème à la page de profil et à la barre d’administration.

La fonctionnalité peut être ouverte à tous les utilisateurs connectés, à certains rôles ou à certains comptes. L’accès à la page de réglages est également délégable par rôle, par utilisateur ou avec l’aptitude `manage_wp_simple_template_switch`.

Les gestionnaires choisissent précisément les thèmes proposés dans le switch et peuvent consulter le thème utilisé par chaque compte. Lors d’une nouvelle activation, seuls les administrateurs peuvent choisir un thème.

Le choix reste personnel : le thème actif du site n’est jamais remplacé.

Langues incluses : français, anglais, allemand, italien, espagnol et portugais.

== Installation ==

1. Téléverser le dossier du plugin dans `/wp-content/plugins/`.
2. Activer WP Simple Template Switch.
3. Ouvrir Réglages → Template Switch.
4. Configurer les utilisateurs et les rôles autorisés.
5. Installer le thème alternatif sans l’activer globalement.
6. Cocher le thème dans la liste des thèmes disponibles dans le switch.

== Frequently Asked Questions ==

= Le plugin active-t-il le thème pour tout le site ? =

Non. Le choix est appliqué uniquement au compte connecté qui l’a sélectionné.

= Les visiteurs non connectés voient-ils un changement ? =

Non. Ils continuent de voir le thème actif du site.

= Un thème enfant est-il pris en charge ? =

Oui. Le plugin applique conjointement les valeurs `stylesheet` et `template` nécessaires.

= Quelle aptitude permet de déléguer les réglages ? =

`manage_wp_simple_template_switch`

== Changelog ==

= 2.0.0 =
* Liste blanche des thèmes disponibles dans le switch.
* Tableau indiquant le thème utilisé par chaque utilisateur.
* Détection des préférences indisponibles ou non autorisées.
* Accès initial limité aux administrateurs.
* Captures d’écran intégrées au README GitHub.

= 1.1.0 =
* Traductions complètes en français, anglais, allemand, italien, espagnol et portugais.
* Catalogues WordPress PO, MO et POT inclus.

= 1.0.0 =
* Première version publique.
* Sélecteur sur le profil et dans la barre d’administration.
* Accès configurable par rôle, utilisateur et aptitude.
* Prise en charge des thèmes parents et enfants.
* Documentation et fichiers communautaires GitHub.
