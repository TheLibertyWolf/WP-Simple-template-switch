# Changelog

Toutes les modifications notables de WP Simple Template Switch sont documentées ici.

Le format suit [Keep a Changelog](https://keepachangelog.com/fr/1.1.0/) et le projet utilise le [versionnage sémantique](https://semver.org/lang/fr/).

## [2.1.1] - 2026-08-16

### Corrigé

- le mode Sélecteur affiche désormais un véritable champ déroulant directement dans la barre d’administration ;
- le thème actif et les thèmes autorisés sont disponibles sans dépendre du sous-menu au survol.

## [2.1.0] - 2026-08-16

### Ajouté

- choix entre un sélecteur de thèmes et un bouton switch dans la barre d’administration ;
- réglage dédié du mode d’interaction dans la page d’administration.

### Modifié

- le sélecteur de thèmes devient le mode par défaut, y compris lors de la migration depuis une version antérieure.

## [2.0.1] - 2026-08-16

### Corrigé

- le bouton principal de la barre d’administration bascule désormais directement entre le thème du site et le premier thème alternatif ;
- un changement lancé depuis `wp-admin` redirige vers le site public afin de rendre immédiatement le nouveau thème visible.

## [2.0.0] - 2026-08-16

### Ajouté

- liste blanche des thèmes disponibles dans le profil et la barre d’administration ;
- tableau de suivi indiquant le thème utilisé par chaque compte ;
- détection des thèmes supprimés, des thèmes non proposés et des accès retirés ;
- galerie de captures d’écran dans le README GitHub.
- tableau de couverture linguistique et documentation dédiée des traductions ;
- détection des catalogues Gettext dans GitHub Languages/Insights.

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
[2.0.1]: https://github.com/TheLibertyWolf/WP-Simple-template-switch/releases/tag/v2.0.1
[2.1.0]: https://github.com/TheLibertyWolf/WP-Simple-template-switch/releases/tag/v2.1.0
[2.1.1]: https://github.com/TheLibertyWolf/WP-Simple-template-switch/releases/tag/v2.1.1
