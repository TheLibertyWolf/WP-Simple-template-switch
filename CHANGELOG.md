# Changelog

Toutes les modifications notables de WP Simple Template Switch sont documentées ici.

Le format suit [Keep a Changelog](https://keepachangelog.com/fr/1.1.0/) et le projet utilise le [versionnage sémantique](https://semver.org/lang/fr/).

## [2.0.0] - 2026-08-16

### Ajouté

- liste blanche des thèmes disponibles dans le profil et la barre d’administration ;
- tableau de suivi indiquant le thème utilisé par chaque compte ;
- détection des thèmes supprimés, des thèmes non proposés et des accès retirés ;
- galerie de captures d’écran dans le README GitHub.

### Modifié

- les nouvelles activations autorisent uniquement le rôle Administrateur à choisir un thème ;
- la migration depuis la version 1.x conserve les règles d’accès existantes et autorise initialement le thème actif du site.

## [1.1.0] - 2026-08-16

### Ajouté

- traductions complètes de l’interface en français, anglais, allemand, italien, espagnol et portugais ;
- catalogues WordPress `.po` et `.mo` pour chaque langue ;
- catalogue source `.pot` et outil reproductible de génération des traductions.

## [1.0.0] - 2026-08-16

### Ajouté

- sélection personnelle du thème sur la page de profil ;
- menu de changement rapide dans la barre d’administration ;
- retour au thème par défaut du site ;
- ciblage de tous les utilisateurs connectés, de rôles ou de comptes précis ;
- délégation du menu de réglages par rôle, utilisateur ou aptitude WordPress ;
- aptitude `manage_wp_simple_template_switch` attribuée aux administrateurs ;
- prise en charge des thèmes parents et enfants ;
- cartes latérales WordPress pour l’état, l’aide, l’aptitude avancée et l’auteur ;
- nettoyage complet à la désinstallation ;
- documentation et fichiers communautaires GitHub.

[1.0.0]: https://github.com/TheLibertyWolf/WP-Simple-template-switch/releases/tag/v1.0.0
[1.1.0]: https://github.com/TheLibertyWolf/WP-Simple-template-switch/releases/tag/v1.1.0
[2.0.0]: https://github.com/TheLibertyWolf/WP-Simple-template-switch/releases/tag/v2.0.0
