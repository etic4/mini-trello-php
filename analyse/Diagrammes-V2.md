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
    + get_by_id(int id): Object

    + insert(Object): Object
    + update(Object)
    + remove(Object)
    
    # get_many(sql, params)
    # fetch_one_and_get_instance(query)
    # sql_date(datetime)
    # php_date(sqlDate)
    # get_tableName()

    # {abstract} prepare_insert()
    # {abstract} prepare_update()
    # {abstract} get_instance()
}

class UserDao extends Dao {
    + get_by_email(String email) 
}

class BoardDao extends Dao {
    - User user
    + __construct(User)
    + get_owners_boards(): List<Board>
    + get_others_boards(): List<Board>
}

class ColumnDao extends Dao {
    - Board board
    + __construct(Column)
    + get_all(): List<Column>
}
    
class CardDao extends Dao {
    - Card card
    + __construct(Card)
    + get_all(): List<Card>
}

class CommentDao extends Dao {
    - Comment comment
    + __construct(Comment)
    + get_all(): List<Comment>
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

UserMngr *-u- User
BoardMngr *-u- Board
ColumnMngr *-u- Column
CardMngr *-u- Card
CommentMngr *-u- Comment

UserMngr -[dotted]-> UserDao: <<use>>
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
    - int id
    - String eMail
    - String fullName
    - String passwdHash
    - String clearPasswd
    - DateTime registeredAt
    
    + __construct(attrs..)
    + getters()
    + setters()
    + check_password(passw)
    + validate() : List<String>
}

class Board {
    - int id
    - String title
    - DateTime createdAt
    - DateTime modifiedAt
    - int owner
    
    + __construct(attrs..)
    + getters()
    + setters()
    + validate() : List<String>
}

class Column {
    - int id
    - String title
    - int position
    - DateTime createdAt
    - DateTime modifiedAt
    - int board

    + __construct(attrs..)
    + getters()
    + setters()
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
    - int author
    
    + __construct(attrs..)
    + getters()
    + setters()
    + validate() : List<String>
}

class Comment {
    - int id
    - String body
    - DateTime createdAt
    - DateTime modifiedAt
    - int card
    - int author

    + __construct(attrs..)
    + getters()
    + setters()
    + validate() : List<String>
}

class UserMngr {
    - UserDao dao
    + new(): User
    + get_by_email(email) : User
    + validate_login(email, passwd): List<String>
    + get_boards(User): BoardMngr
}

class BoardMngr {
    - User user
    - Board dao

    + __construct(User user)
    + new(): Board
    + get_own_board(): List<Board>
    + get_others_boards(): List<Board>
    + get_columns(Board): ColumnMngr
    + get_owner(Board): User
}

class ColumnMngr {
    - Board board
    - ColumnDao dao

    + __construct(Board)
    + new(): Board
    + get_board(): Board
    + get_cards(Column): CardMngr

    + move_up(Column)
    + move_down(Column)
    - set_position(Column, pos)
}

class CardMngr {
    - Column column
    - CardDao dao

    + __construct(Column)
    + new(title, body, author, column): Card
    + get_author(Card): User
    + get_column(): Column
    + get_comments(): CommentMngr
    + get_author(Card): User

    + move_up(Card)
    + move_down(Card)
    + move_left(Card)
    + move_right(Card)
    - set_position(Card, pos)
    - set_column(Card, Column)
}

class CommentMngr {
    - Card card
    - CommenDao dao

    + __construct(Card)
    + new(title, body): Comment
    + get_by_id(id): Comment
    + get_card(): Card
    + get_author(Comment): User
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
    + str_longer_than(str, length)
    + contains_capitals(str)
    + contains_digits(str)
    + contains_non_alpha(str)
    + valid_email(email)
    + add_error(errMsg)
    + get_errors()
    + validate() : List<String>
}

class UserValidator implements Validator {
    - User user
    + __construct(User user)
}

class BoardValidator implements Validator {
    - Board board
    + __construct(Board board)
}

class ColumnValidator implements Validator {
    - Column column
    + __construct(Column column))
}

class CardValidator implements Validator {
    - Card card
    + __construct(Card card)
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