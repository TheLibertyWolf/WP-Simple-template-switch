# Contribuer

Merci de contribuer à WP Simple Template Switch.

## Avant de proposer un changement

1. Vérifiez qu’une issue similaire n’existe pas déjà.
2. Créez une branche depuis `main`.
3. Respectez les conventions de code WordPress et la compatibilité PHP 8.1.
4. Validez les nonces, les capacités, les entrées et l’échappement des sorties.
5. Testez le thème actif, un thème alternatif et, si possible, un thème enfant.
6. Mettez à jour la documentation et le changelog lorsque le comportement public change.

## Validation locale

```bash
composer install
composer lint
```

## Pull requests

Décrivez le besoin, le comportement avant/après et les vérifications effectuées. Une pull request doit rester ciblée et ne contenir aucun secret ni donnée issue d’un site de production.
