# Diagrammes de classe (V1)
Manière "comme au cours", où les classes représentant les objets de base sont chargées
des requêtes en db.

## Classes de Base et managers
Les managers sont chargés de toutes les opération sur les instances d'objets de base, à part les accès en db

```plantuml
@startuml

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
    + get_all(): List<?>
    + get_by_id(int id): <?>
    + add(<?>): int
    + update(<?>)
    + remove(<?>)
}

class User {
    - final id
    - String eMail
    - String fullName
    - String passwdHash
    - DateTime registeredAt
    + get_by_email(String email)
    
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