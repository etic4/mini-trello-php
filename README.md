# Projet PRWB 2021 - Trello

## Notes de livraison itération 1

	- Toutes les fonctionnalités demandées dans le cahier des charges ont été réalisées et fonctionnent sans bug connu. A l'exception d'un léger bug d'affichage du header dans 
la vue d'un tableau lorsque le nombre de colonnes de ce tableau devient trop important.

	- Aucune fonctionnalité supplémentaire n'a été implémentée.

 	- La base de donnée rendue est la même que celle fournie, car nous estimons qu'elle permet de naviguer a travers l'application et de montrer le bon fonctionnement de toutes les 
fonctionnalités de l'application. Nous vous conseillons de vous connectez lors de votre première visite en tant que `boverhaegen@epfc.eu` et de visualiser le tableau 
`Projet PRWB`, car c'est là que vous pourrez voir au mieux toutes les différentes fonctionnalités.

	- Dans un souci de ne pas mélanger les langues lors de la phase de programation, ainsi que pour rester le plus universel possible, les menus et messages d'erreur du site sont 
affichés en anglais.

	- Nous avons considéré qu'ajouter un tableau/colonne/carte/commentaire vide n'était pas permis, mais que cela ne devait pas afficher d'erreur. Si l'utilisateur clique sur le bouton
'ajouter' sans remplir le corps, la page est simplement rechargée à l'identique.

## Notes de livraison itération 2

## Notes de livraison itération 3


## Installation

- Déplacez le dossier à la racine de votre serveur web (dossier `projects` ou `htdocs` en fonction de votre installation)
- Accédez à l'url [http://localhost/prwb_2021_a02/setup/install](http://localhost/prwb_2021_a02/setup/install)

## Utilisateurs

Tous les utilisateurs (`boverhaegen@epfc.eu`, `bepenelle@epfc.eu`, `brlacroix@epfc.eu` et `xapigeolet@epfc.eu`) ont le mot de passe `Password1,` (remarquez qu'il se termine par une virgule).

## Sauvegarde de la base de données

- Vérifiez le chemin de `mysql dump` dans le fichier de configuration
- Accédez à l'url [http://localhost/prwb_2021_a02/setup/export](http://localhost/prwb_2021_a02/setup/export) 
    - `database/prwb_2021_a02.sql` contient le schéma de la base de données
    - `database/prwb_2021_a02_dump.sql` contient le dump de la base de données
- Pour la restaurer, accédez à l'url [http://localhost/prwb_2021_a02/setup/install](http://localhost/prwb_2021_a02/setup/install)


