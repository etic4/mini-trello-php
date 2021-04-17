# Projet PRWB 2021 - Trello

## Notes de livraison itération 1

Toutes les fonctionnalités demandées dans le cahier des charges ont été réalisées et fonctionnent sans bug connu.

Aucune fonctionnalité supplémentaire n'a été implémentée.

La base de donnée rendue est la même que celle fournie, car nous estimons qu'elle permet de naviguer a travers l'application et de montrer le bon fonctionnement de toutes les fonctionnalités de l'application.

Dans un souci de ne pas mélanger les langues lors de la phase de programation, ainsi que pour rester le plus universel possible, les menus et messages d'erreur du site sont affichés en anglais.

Nous avons considéré qu'ajouter un tableau/colonne/carte/commentaire vide n'était pas permis, mais que cela ne devait pas afficher d'erreur. Si l'utilisateur clique sur le bouton 'ajouter' sans remplir le corps, la page est simplement rechargée à l'identique.

## Notes de livraison itération 2
simplification de la gestion des dates
cache des requêtes étendu et exécuté au moment de l'exécution de la requête;

problèmes:
A rectifier: lors des edit les champs ne sont restitués dans l'état de la carte en DB
cela dit, je ne vois pas comment passer le contenu de plus de deux champs quand on n'a que deux paramètres à notre disposition
et que l'expressions régulière de .htaccess explose la requête en mots ('w')
à moins d'utiliser $_SESSION, et tu as incité à ne pas en abuser lors de la dernière éval.

## Notes de livraison itération 3

## Utilisateurs

Tous les utilisateurs (`boverhaegen@epfc.eu`, `bepenelle@epfc.eu`, `brlacroix@epfc.eu` et `xapigeolet@epfc.eu`) ont le mot de passe `Password1,` (remarquez qu'il se termine par une virgule).


