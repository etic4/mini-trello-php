# Diagrammes de classe (V2)
Version avec DAO. On sort toutes les opérations
d'accès à la db des classes représentant les objets de base.

On a une classe basique par objet, qui contient essentiellement les attributs
de l'objet en DB et des getters et setters (User, Board, etc).

On a une classe Dao (pour "data access object") qui est chargé de faire
les requêtes et update de la DB.

Par exemple pour insérer un user depuis le dao on ferait: `UserDao.add(user)`.
Pour ajouter un Board par exemple, on ferait dans le contrôleur approprié:
```
$board = new Board("Un Tableau");
$boardDao = new BoardDao($user);
$boardDao->add($board)
```

À l'instanciation d'un classe Manager on instancie le DAO correspondant

Tout ça a l'avantage de ne pas mélanger les responsabilités, et surtout d'éviter que
les objets de base (ceux qui sont stockés en DB) ne deviennent des sortes de fourre-tout.

## Dao
Les Dao héritent de la  classe abstraite Dao qui elle-même hérite de Model

```plantuml
@startuml

abstract class Dao extends Model {
    + get_all(): List<?>
    + get_by_id(int id): <?>
    + add(<?>): int
    + update(<?>)
    + remove(<?>)
}

class UserDao extends Dao {
    + get_by_email(String email) 
}

class BoardDao extends Dao {
    - User user
    + __construct(User user)
    + get_user_boards(): BoardList
    + get_others_boards(): BoardList
}

class ColumnDao extends Dao {
- Board board
+ __construct(Board board)
}
    
class CardDao extends Dao {
    - Column column
    + __construct(Column column)
}

class CommentDao extends Dao {
    - Card card
    + __construct(Card card)
}

@enduml
```

## Les objets de base et les managers

```plantuml
@startuml
User -[hidden]r Board
Board -[hidden]r Column
Column -[hidden]r Card
Card -[hidden]r Comment

BoardMngr *-u- Board
ColumnMngr *-u- Column
CardMngr *-u- Card
CommentMngr *-u- Comment

User -[dotted]-> UserDao: <<use>>
BoardMngr -[dotted]-> BoardDao: <<use>>
ColumnMngr -[dotted]-> ColumnDao: <<use>>
CardMngr -[dotted]-> CardDao: <<use>>
CommentMngr -[dotted]-> CommentDao: <<use>>

User -[dotted]> validator: <<use>>
Board -[dotted]> validator: <<use>>
Column -[dotted]> validator: <<use>>
Card -[dotted]> validator: <<use>>
Comment -[dotted]> validator: <<use>>

package validator {
}

'note top of User : "Il faut passer le passwd\nen clair à validate()"

class User {
    - final id
    - String eMail
    - String fullName
    - String passwdHash
    - DateTime registeredAt
    
    + __construct(attrs..)
    + get_boards() : BoardMngr
    + check_password(String passw)
    + validate(String pass) : List<String>
}

class Board {
    - int id
    - String title
    - DateTime createdAt
    - DateTime modifiedAt
    
    + __construct(attrs..)
    + get_columns() : ColumnMngr
    + get_owner() : User
    + validate() : List<String>
}

class Column {
    - int id
    - String title
    - int position
    - DateTime createdAt
    - DateTime modifiedAt
    - Board board
    + __construct(attrs..)
    + get_cards() : CardMngr
    + validate() : List<String>
}

class Card {
    - int id
    - String title
    - String body
    - int position
    - DateTime createdAt
    - DateTime modifiedAt
    - Column column
    - User author
    
    + __construct(attrs..)
    + get_comments(): CommentMngr
    + get_author(): User
    + get_column(): Column
    + move_to(Column col, int pos)
    + validate() : List<String>
}

class Comment {
    - int id
    - String body
    - DateTime createdAt
    - DateTime modifiedAt
    - Card card
    - User author

    + __construct(attrs..)
    + get_author(): User
    + get_card(): Card
    + validate() : List<String>
}

class BoardMngr {
    - User user
    + __construct(User user)
    + get_own_board()
    + get_others_boards()
    + add(Board board)
    + update(Board board)
    + remove(Board board)
    + remove_all()
    + size() : int
}

class ColumnMngr {
    - Board board
    + __construct(Board board)
    + move_up(Column col)
    + move_down(Column col)
    + add(Column col)
    + update(Column col)
    + remove(Column col)
    + remove_all()
    + size() : int
    - set_position(Column col, int pos)
}

class CardMngr {
    - Card card
    + __construct(Column column)
    + move_up(Card card)
    + move_down(Card card)
    + move_left(Card card)
    + move_right(Card card)
    + add(Card card)
    + update(Card card)
    + remove(Card card)
    + remove_all()
    + size() : int
    - set_position(Card card, int pos)
    - set_column(Card card, Column col)
}

class CommentMngr {
    - Comment comment
    + __construct(Card card)
    + add(Comment comm)
    + update(Comment comm)
    + remove(Comment comm)
    + remove_all()
    + size() : int
}

@enduml
```

## Validation

```plantuml
@startuml
package validator {
abstract class Validator {
    - List<String> errors
    + is_string(Object o, String errMsg)
    + is_shorter_than(String str, int strLen, String errMsg)
    + is_longer_than(String str, int length, String errMsg)
    + is_length_equal_to(String str, int length, String errMsg)
    + is_valid_email(String email, String errMsg)
    + regex_has_match(String str, String regex, String errMsg)
    + is_date_before(DateTime date, DateTime base)
    + validate() : List<String>: List<String>
}

class UserValidator implements Validator {
    - final User user
    + __construct(User user)
    - validate_email()
    - validate_fullName()
    - validate_password()
    - validate_unicity()
}

class BoardValidator implements Validator {
    - final Board board
    + __construct(Board board)
    - validate_title()
}

class ColumnValidator implements Validator {
    - final Column column
    + __construct(Column column)
    - validate_title()
    - validate_position()
}

class CardValidator implements Validator {
    - final Card card
    + __construct(Card card)
    - validate_title()
    - validate_position()
}
}
@enduml
```

# Contrôleurs
```plantuml
@startuml
Controller <|-- ControllerMain
Controller <|-- ControllerBoard
Controller <|-- ControllerColumn
Controller <|-- ControllerCard
Controller <|-- ControllerComment

class ControllerMain {
    - UserDao userDao
    + index()
    + login()
    + signup()
    + logout()
}

class ControllerBoard {
    - BoardMngr boardMngr
    + index()
    + add()
    + edit()
    + remove()
}

class ControllerColumn {
    - ColumnMngr columnMngr
    + index()
    + add()
    + edit()
    + remove()
    + left()
    + right()
}

class ControllerCard {
    - CardMngr cardMngr
    + index()
    + add()
    + view()
    + edit()
    + remove()
    + nbComments()
    + down()
    + up()
    + left()
    + right()
}

class ControllerComment {
    - CommentMngr commentMngr
    + index()
    + add()
    + edit()
    + remove()
}

@enduml
```