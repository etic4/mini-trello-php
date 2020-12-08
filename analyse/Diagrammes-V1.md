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


package validator {
}

'note top of User : "Il faut passer le password\nen clair à validate()"

abstract class BaseModel extends Model {
    + {static} get_all(): List<?>
    + {static} get_by_id(int id): <?>
    + {static} fetch_one_and_get_instance(query): <?>
    + {static} get_many(sql, params): List<?>
    + {static} sql_date(datetime): str
    + {static} php_date(sqldate): Datetime
    + {static} get_tableName()
    + insert(): int
    + update()
    + delete()
    + delete_all()
}

class User {
    - id
    - String email
    - String fullName
    - String passwdHash
    - String clearPasswd
    - DateTime registeredAt
    
    + {static} get_by_email(String email)
    + {static} validate_login(email, passwd)
    
    + __construct(attrs..)
    + getters()
    + setters()
    + get_own_board(): List<Board>
    + get_others_boards(): List<Board>
    + check_password(passw)
    + validate() : List<String>
}

class Board {
    - int id
    - String title
    - DateTime createdAt
    - DateTime modifiedAt
    - int Owner
    
    + __construct(attrs..)
    + getters()
    + setters()
    + get_columns() : List<Column>
    + get_owner_inst() : User
    + set_modifiedDate()
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
    + get_board_inst(): Board
    + get_cards() : List<Card>
    + move_up()
    + move_down()
    + set_modifiedDate()
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
    + get_comments(): List<Comment>
    + get_author_inst(): User
    + get_column(): Column
    + move_up()
    + move_down()
    + move_left()
    + move_right()
    + set_modifiedDate()
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
    + set_modifiedDate()
    + validate() : List<String>
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