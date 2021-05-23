# Projet PRWB 2021 - Trello

## Notes de livraison itération 1

Toutes les fonctionnalités demandées dans le cahier des charges ont été réalisées et fonctionnent sans bug connu.

Aucune fonctionnalité supplémentaire n'a été implémentée.

La base de donnée rendue est la même que celle fournie, car nous estimons qu'elle permet de naviguer a travers l'application et de montrer le bon fonctionnement de toutes les fonctionnalités de l'application.

Dans un souci de ne pas mélanger les langues lors de la phase de programation, ainsi que pour rester le plus universel possible, les menus et messages d'erreur du site sont affichés en anglais.

Nous avons considéré qu'ajouter un tableau/colonne/carte/commentaire vide n'était pas permis, mais que cela ne devait pas afficher d'erreur. Si l'utilisateur clique sur le bouton 'ajouter' sans remplir le corps, la page est simplement rechargée à l'identique.

## Notes de livraison itération 2
### Concernant les fonctionnalités pour l'itération 2
Toutes les fonctionnalités ont été implémentées, y compris celles pour les groupes de 3 dans la mesure où elles étaient déjà en chantier.

En ce qui concerne les permissions (rôles, permissions des collaborateurs, management des users), elle ont été implémentées à l'aide de classes
`Permissions` (`BoardPermissions`, etc...) dont les méthodes (add, view, update, delete) sont associées aux fonctions publiques correspondantes de chaque contrôleur. `Permission::action($object)` retourne un booléen
qui permet de déterminer si l'utilisateur a le droit de les exécuter. Dans le cas contraire, il est redirigé. On trouve ces classes dans le dossier _controller/permissions_

La classe controller du framework a été étendue (classe `ExtendedController` dans le dossier _controller_) pour inclure deux types de méthodes:
  
* deux méthodes qui retournent une instance d'un objet dont l'ID est passé dans la requête, qu'elle soit POST ou GET, et qui redirige si l'objet n'est pas trouvé. 
    
    * une version reçoit 2 arguements, le premier le type de l'objet à retourner, le second la clé de $_POST ou $_GET qui retourne l'ID l'instance à construire,

    * une seconde version, utilisée dans la plupart des cas, ne prend aucun argument et retourne une instance correspondant au controlleur (ControllerCard -> Card) dont elle trouve la clé sous `id` si c'est une requête POST et sous `param1` si c'est une requête GET
    
* une qui reçoit le résultat d'un appel à `Permission` et redirige si aucun utilisateur n'est connecté ou si l'appel a `Permission` retourne false

### Plus généralement
L'ensemble du code a été profondément retravaillé avec comme objectif de mieux l'organiser (éviter en particulier l'encombrement des classes du modèle) et
de factoriser ce qui peut l'être en restant facilement lisible et maintenable que possible et, pas accessoirement, de me réapproprier le code.

#### Concernant le modèle
* implémentation du pattern DAO (cf. classes `BoardDao` etc. dans le dossier _model/dao_). La classe BaseDao rassemble les méthodes 
  génériques d'accès à la DB. Les autres en hérite et implémentent les comportements spécifiques
  
* implémentation d'une classe `SqlGenerator` (dossier _model/dao)_, qui génère et retourne une requête sql et les paramètres associé par des appels
  successifs de méthode. Cette dernière est l'aboutissement d'un travail de factorisation du sql qui s'est avéré très utile dans certains cas mais au final moins que prévu
  et, dans beaucoup de cas,  d'un usage équivalent à du pure SQL. Cela dit, en ce qui me concerne, je trouve que le sql gagne a être repoussé$
  à la périphérie du reste du code.
  
* réimplémentation de la logique de validation (cf les classes pour chaque objet dans le dossier _model/validation_) pour la clarifier et la sortir des classes du modèle

#### Concernant la vue

Utilisation du framework css _Bulma_ et donc portage et réorganisation importante du html.

Les titre sont maintenant édités dans des pages séparées.

Une classe `ViewUtils` a été ajouté (dossier _view_) qui rassemble les méthodes que j'ai jugées "de présentation" et qui ne sont utilisées
que dans le cadre des vues.

### Divers

* clarification de la gestion des erreurs. Une instance de la classe `DisplayableErrors` (dans le dossier _model/validation_) 
  se contente de gérer la liste des erreurs
  reçues par les classes de validation, cette instance est ensuite set sur $_SESSION, qui est reset lorsque les 
  erreurs sont passsés à la vue.

* simplification de la gestion des dates et relocalisation dans une classe `DateUtils` dans le dossier _utils_

* suppression des traits dès lors inutiles et qui, je trouve, diminuaient la lisibilité

* simplification et clarification du rôle de la classe `CachedGet` (dans _model/dao_), qui se contente maintenant de mettre en cache et
    de retourner ce qui s'y trouve

* la mise en cache du résultat des requêtes est maintenant faite au moment de l'exécution
  de la requête dans `BaseDao`. Elle a été étendue à quelques cas spéciaux.

* la classe `BreadCrumb` (dossier _controller_) a été améliorée pour plus de généralité, mais reste perfectible et un peu obscure à l'usage 

* création de wrapper pour $_POST, $_GET et $_SESSION avec diverses méthodes utiles comme (cf le dossier _controller/utils_). Seuls ces wrapper sont utilisés

### Remarques
#### PRG
Je suis un peu troublé par la question du PRG. Je crois en comprendre le principe et l'avoir correctement implémenté 
   (mais peut-être suis-je à côté).
   
La question est comment faire si on souhaite que le contenu des champs soumis soient préremplis après un post qui retourne une erreur ?
Il faut pour ça que la valeur de ces champs soit passée à la vue. Je ne vois que trois manières de faire:

* c'est la fonction qui traite le POST qui affiche la vue, mais dans ce cas ce n'est pas PRG. C'est qui se passe dans la (votre) méthode
  User::signup
* le contenu des champs est passé lors du redirect en paramètre de la requête, éventuellement encodés. Ça ne me paraît 
  pas possible avec le framework
  (sauf dans des cas particuliers) parce que nous n'avons que trois arguments à disposition, et que ces arguments sont
  parsés dans .htaccess comme "mots" ('w') par l'expression régulière, ce qui interdit notamment l'urlencode. Il y aurait peut-être
  moyen de bidouiller un truc avec des "_" et des "-", mais ça serait du bidouillage...
* utiliser $_SESSION pour passer les arguments lors d'un redirect, mais tu as invité à "limiter au max l'usage de SESSION"

J'ai choisi de laisser signup en l'état et de me contenter de simples redirects dans les autres cas (édition des cartes notamment).

#### Suppressions
Pour la suppression d'un utilisateur, 
j'ai choisi de supprimer ses propres tableaux et tout ce qu'ils contiennent ainsi que ses participations et collaborations, 
mais pour le reste (ce dont l'utilisateur supprimé est l'auteur dans des tableaux dont il n'est pas auteur) j'ai choisi d'attribuer
les cartes qu'il a créé ainsi que ses commentaires 
à un utilisateur spécial "Anonyme" qui se trouve en base de donnée et ne peut pas se logger. Ce comportement me paraît réaliste.

## Notes de livraison itération 3
Toutes les fonctionnalités pour les groupes de 2 ont été implémentées. Les problèmes de l'itération précédente ont été réglés.

Concernant ta remarques sur l'accessibilité des classes DAO depuis les contrôleurs, je n'ai pas eu le temps de rectifier ça. Implémenter un facade n'aurait
pas demandé beaucoup de temps, mais il m'est compté...

Les fichier javascript se trouvent dans `lib/js`, un par fonctionnalité demandée.

J'ai dans cette itération fait le service minimum, vite fait bien fait comme on dit, assez proprement quand-même, je crois, même si quelques trucs
mériteraient d'être un peu factorisés, la validation par exemple, par quoi j'ai commencé ....

J'espère que je réussirai quand-même ;)

PS:
Je n'ai pas eu l'occasion de tester sur Windows... je croise les doigts.

## Utilisateurs

Tous les utilisateurs (`boverhaegen@epfc.eu`, `bepenelle@epfc.eu`, `brlacroix@epfc.eu` et `xapigeolet@epfc.eu`) ont le mot de passe `Password1,` (remarquez qu'il se termine par une virgule).


