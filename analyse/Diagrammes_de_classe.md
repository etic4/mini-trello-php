# Diagrammes de classe
Test diagramme de classe

<<<<<<< HEAD

```plantuml
@startuml

abstract class Model {
    int: insert()
    void: update()
    void: delete()
    boolean: validate()
}

class User implements Model {
    list<User>: public static getAll()
    User: public static getById()
    list<Board>: public getBoards()
}

abstract class Validator {
    boolean : isStringShorterThan(String str, int strLen)
    boolean: isStringLongerThan(String, int length)
    boolean: regexHasMatches(String str, String regex)
    boolean: isDateBefore(Date date)
}

class UserValidator {

}
 
=======
```plantuml
@startuml
abstract Class Validator
boolean : isShorterThan(String str, int strLen)
>>>>>>> dev

@enduml
```