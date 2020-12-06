# Diagrammes de classe (V1)
Manière "comme au cours", où les classes représentant les objets de base sont chargées
des requêtes en db.

## Classes de Base et managers
Les managers sont chargés de toutes les opération sur les instances d'objets de base, à part les accès en db

```plantuml
@startuml

User -[hidden]r- Board
Board -[hidden]r- Column
Column -[hidden]r- Card
Card -[hidden]r- Comment

BaseModel -[dotted]r-> validator : <<use>
User -u-|> BaseModel
Board -u-|> BaseModel
Column -u-|> BaseModel
Card -u-|> BaseModel
Comment -u-|> BaseModel

BoardMngr *-u- Board
ColumnMngr *-u- Column
CardMngr *-u- Card
CommentMngr *-u- Comment

package validator {
}

'note top of User : "Il faut passer le password\nen clair à validate()"

abstract class BaseModel extends Model {
    + {static} get_all(): List<?>
    + {static} get_by_id(int id): <?>
    + add(<?>): int
    + update(<?>)
    + delete(<?>)
}

class User {
    - final id
    - String email
    - String fullName
    - String passwdHash
    - DateTime registeredAt
    + {static} get_by_email(String email)
    
    + __construct(attrs..)
    + getters()
    + setters()
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
    + getters()
    + setters()
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
    + getters()
    + setters()
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
    + getters()
    + setters()
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
    + getters()
    + setters()
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
    + delete(Board board)
    + delete_all()
    + size() : int
}

class ColumnMngr {
    - Board board
    + __construct(Board board)
    + move_up(Column col)
    + move_down(Column col)
    + add(Column col)
    + update(Column col)
    + delete(Column col)
    + delete_all()
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
    + delete(Card card)
    + delete_all()
    + size() : int
    - set_position(Card card, int pos)
    - set_column(Card card, Column col)
}

class CommentMngr {
    - Comment comment
    + __construct(Card card)
    + add(Comment comm)
    + update(Comment comm)
    + delete(Comment comm)
    + delete_all()
    + size() : int
}

@enduml
```


## Validation
Les classes chargées de la validation

```plantuml
@startuml
package validator {
abstract class Validator {
    - List<String> errors
    + {static} is_string(Object o, String errMsg)
    + {static} is_shorter_than(String str, int strLen, String errMsg)
    + {static} is_longer_than(String str, int length, String errMsg)
    + {static} is_length_equal_to(String str, int length, String errMsg)
    + {static} is_valid_email(String email, String errMsg)
    + {static} regex_has_match(String str, String regex, String errMsg)
    + {static} is_date_before(DateTime date, DateTime base)
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
    + delete()
}

class ControllerColumn {
    - ColumnMngr columnMngr
    + index()
    + add()
    + edit()
    + delete()
    + left()
    + right()
}

class ControllerCard {
    - CardMngr cardMngr
    + index()
    + add()
    + view()
    + edit()
    + delete()
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
    + delete()
}

@enduml
```