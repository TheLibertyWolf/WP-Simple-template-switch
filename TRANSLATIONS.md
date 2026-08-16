# Traductions

WP Simple Template Switch utilise le système d’internationalisation natif de WordPress et sélectionne automatiquement la traduction correspondant à la langue du site ou du profil utilisateur.

| Langue | Locale WordPress | Couverture | État |
|---|---|---:|---|
| Français | `fr_FR` | 72/72 — 100 % | Langue source |
| Anglais | `en_US` | 72/72 — 100 % | Complet |
| Allemand | `de_DE` | 72/72 — 100 % | Complet |
| Italien | `it_IT` | 72/72 — 100 % | Complet |
| Espagnol | `es_ES` | 72/72 — 100 % | Complet |
| Portugais | `pt_PT` | 72/72 — 100 % | Complet |

## Fichiers

- `languages/wp-simple-template-switch.pot` : catalogue de référence ;
- fichiers `.po` : traductions modifiables ;
- fichiers `.mo` : catalogues compilés chargés par WordPress ;
- `tools/build-translations.php` : source reproductible des six catalogues.

Les catalogues sont validés avec `msgfmt` et comparés au fichier POT avec `msgcmp`. Chaque langue contient l’intégralité des 72 messages, y compris les formes plurielles et la description de l’extension.

## Mettre à jour une traduction

1. Ajouter ou modifier les chaînes dans `tools/build-translations.php`.
2. Mettre à jour le catalogue POT depuis les fichiers PHP.
3. Exécuter le générateur de traductions.
4. Compiler les fichiers MO avec `msgfmt`.
5. Vérifier chaque fichier PO avec `msgcmp` et confirmer qu’aucune chaîne n’est manquante.

Les variables telles que `%s` et `%d` doivent être conservées à l’identique dans chaque traduction.

## GitHub Languages

GitHub Linguist classe les fichiers `.po` et `.pot` comme **Gettext Catalog**. Ils sont explicitement rendus détectables dans `.gitattributes`. Cette statistique mesure les types de fichiers du dépôt ; elle ne peut pas distinguer les langues humaines française, anglaise, allemande, italienne, espagnole et portugaise. Le tableau ci-dessus constitue donc la référence de couverture linguistique du plugin.
