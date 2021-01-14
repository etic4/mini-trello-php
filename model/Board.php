<?php

require_once "framework/Model.php";
require_once "User.php";
require_once "Column.php";


class Board extends Model {
    use DateTrait;

    private ?string $id;
    private string $title;
    private User $owner;
    private array $columns;


    public function __construct(string $title, User $owner, ?string $id=null, ?DateTime $createdAt=null,
                                ?DateTime $modifiedAt=null) {
        $this->id = $id;
        $this->title = $title;
        $this->owner = $owner;
        $this->createdAt = $createdAt;
        $this->modifiedAt = $modifiedAt;
    }


    //    GETTERS    //

    public function get_id(): ?string {
        return $this->id;
    }

    public function get_title(): string {
        return $this->title;
    }

    public function get_owner(): User {
        return $this->owner;
    }

    public function get_owner_id(): string {
        return $this->owner->get_id();
    }

    public function get_owner_fullName(): string {
        return $this->owner->get_fullName();
    }

    public function get_columns(): array {
        return Column::get_columns_for_board($this);
    }


    //    SETTERS    //

    public function set_id(string $id): void {
        $this->id = $id;
    }

    public function set_title(string $title): void {
        $this->title = $title;
    }


    //    VALIDATION    //

    public function validate(): array {
        $errors = [];
        if (!Validation::str_longer_than($this->title, 2)) {
            $errors[] =  "Title must be at least 3 characters long";
            
        }
        if (!Validation::is_unique_title($this->title)) {
            $errors[] = "A board with the same title already exists";
        }

        return $errors;
    }


    //    QUERIES    //

    protected static function get_instance($data): Board {
        list($createdAt, $modifiedAt) = self::get_dates_from_sql($data["CreatedAt"], $data["ModifiedAt"]);
        return new Board(
            $data["Title"],
            User::get_by_id($data["Owner"]),
            $data["ID"],
            $createdAt,
            $modifiedAt
        );
    }

    public static function get_by_id(string $board_id): ?Board {
        $sql = 
            "SELECT * 
             FROM board 
             WHERE ID=:id";
        $params = array("id" => $board_id);
        $query = self::execute($sql, $params);
        $data = $query->fetch();

        if ($query->rowCount() == 0) {
            return null;
        } 

        else {
            return self::get_instance($data);
        }
    }

    public static function get_by_title(string $title): ?Board {
        $sql = 
            "SELECT * 
             FROM board 
             WHERE Title = :title";
        $params = array("title" => $title);
        $query = self::execute($sql, $params);
        $data = $query->fetch();

        if ($query->rowCount() == 0) {
            return null;
        } 

        else {
            $board = self::get_instance($data);
            return $board;
        }
    }

    public static function get_users_boards(User $user): array {
        $sql = 
            "SELECT * 
             FROM board 
             WHERE Owner=:id";
        $params= array("id"=>$user->get_id());
        $query = self::execute($sql, $params);
        $data = $query->fetchAll();

        $boards = array();
        foreach ($data as $rec) {
            array_push($boards, self::get_instance($rec));
        }

        return $boards;
    }
    
    public static function get_others_boards(User $user): array {
        $sql = 
            "SELECT * 
             FROM board 
             WHERE Owner!=:id";
        $params= array("id"=>$user->get_id());
        $query = self::execute($sql, $params);
        $data = $query->fetchAll();

        $boards = array();
        foreach ($data as $rec) {
            array_push($boards, self::get_instance($rec));
        }

        return $boards;
    }
    
    public function insert() {
        $sql = 
            "INSERT INTO board(Title, Owner) 
             VALUES(:title, :owner)";
        $params = array(
            "title"=>$this->get_title(),
            "owner"=>$this->get_owner_id(),
            );
        $this->execute($sql, $params);
        $id = $this->lastInsertId();
        $this->set_id($id);
        $this->set_dates_from_db();
    }

    public function update(): void {
        $sql = 
            "UPDATE board 
             SET Title=:title, Owner=:owner, ModifiedAt=NOW() 
             WHERE ID=:id";
        $params = array(
            "id"=>$this->get_id(), 
            "title"=>$this->get_title(), 
            "owner"=>$this->get_owner_id(),
        );
        
        $this->execute($sql, $params);
        $this->set_dates_from_db();
    }
    
    public function delete(): void {
        foreach ($this->get_columns() as $col) {
            $col->delete();
        }
        $sql = "DELETE FROM board 
                WHERE ID = :id";
        $params = array("id"=>$this->get_id());
        $this->execute($sql, $params);
    }

}