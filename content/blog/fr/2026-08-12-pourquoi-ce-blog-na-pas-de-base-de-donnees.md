---
title: "Pourquoi ce blog n'a pas de base de données"
description: 'Les articles sont des fichiers markdown versionnés dans le dépôt : publier revient à pousser un commit, relire revient à ouvrir une pull request.'
tags: ['symfony', 'php', 'markdown']
category: 'architecture'
translation_key: 'blog-without-a-database'
draft: false
---

Ce site parle déjà à Postgres pour les statistiques et les réglages : ajouter une table `post` aurait été la solution de facilité. J'ai fait le choix inverse : chaque article est un fichier markdown versionné à côté du code.

## L'organisation

Le contenu vit sous `content/blog/`, avec un répertoire par langue :

```
content/blog/
├── en/
│   └── 2026-08-12-why-this-blog-has-no-database.md
└── fr/
    └── 2026-08-12-pourquoi-ce-blog-na-pas-de-base-de-donnees.md
```

Deux informations sont portées par le chemin lui-même. La langue est le répertoire : elle est visible sans ouvrir le fichier, et deux langues ne peuvent jamais entrer en collision sur un même slug. La date de publication et le slug forment le nom du fichier : ils ne sont donc jamais dupliqués dans le front matter, et ne peuvent pas s'en désynchroniser.

Il ne reste dans le front matter que ce que le nom de fichier ne peut pas porter :

```yaml
title: "Pourquoi ce blog n'a pas de base de données"
description: 'Utilisée comme meta description et comme og:description.'
tags: ['symfony', 'php', 'markdown']
category: 'architecture'
translation_key: 'blog-without-a-database'
```

Ce `translation_key` est le seul champ qui justifie vraiment sa présence. Il relie un article à son équivalent dans l'autre langue, ce qui permet aux alternates `hreflang` de pointer vers le slug traduit plutôt que de réutiliser aveuglément celui en anglais.

## Ce que ça apporte

L'écriture se fait dans un éditeur, la relecture dans une pull request, et le déploiement est un `git push`. Aucun écran d'administration à construire, aucun CRUD à maintenir, aucune migration à écrire, et rien à sauvegarder que git ne conserve déjà. Quand un article est faux, le rollback s'appelle `git revert`.

## Ce que ça coûte

La recherche plein texte n'est plus à un `LIKE` de distance, et la publication différée demanderait une tâche capable de committer à ma place. Ni l'une ni l'autre ne valent une table aujourd'hui, et les deux restent possibles plus tard : le modèle de lecture est un value object, pas un descripteur de fichier. Changer la provenance des articles ne touchera que le repository.
