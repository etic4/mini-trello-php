<?php

require_once "framework/Model.php";
require_once "DBTools.php";
require_once "User.php";
require_once "Column.php";


class Board extends Model {
    use Date;

    private ?string $id;
    private string $title;
    private User $owner;
    private array $columns;


    public function __construct(string $title, User $owner, ?string $id=null, ?DateTime $createdAt=null, ?DateTime $modifiedAt=null) {
        $this->id = $id;
        $this->title = $title;
        $this->owner = $owner;
        $this->set_createdAt($createdAt);
        $this->set_modifiedAt($modifiedAt);
    }


    //    GETTERS    //

    public function get_id(): string {
        return $this->id;
    }

    public function get_title(): string {
        return $this->title;
    }

    public function get_owner(): User {
        return $this->owner;
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
            $errors = "Le titre doit comporter au moins 3 caractères";
        }
        return $errors;
    }


    //    QUERIES    //


    protected static function get_instance($data): Board {
        return new Board(
            $data["Title"],
            User::get_by_id($data["Owner"]),
            $data["ID"],
            DBTools::php_date($data["CreatedAt"]),
            DBTools::php_date($data["ModifiedAt"])
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
    
    public function insert(): Board {
        $sql = 
            "INSERT INTO board(Title, Owner, CreatedAt, ModifiedAt) 
             VALUES(:title, :owner, :createdAt, :modifiedAt)";
        $params = array(
            "title"=>$this->get_title(),
            "owner"=>$this->get_owner()->get_id(),
            "createdAt" => DBTools::sql_date($this->get_createdAt()),
            "modifiedAt" => DBTools::sql_date($this->get_modifiedAt())
            );
        $this->execute($sql, $params);
        $this->set_id($this->lastInsertId());
    }

    public function update(): void {
        $sql = "UPDATE board 
                SET Title=:title, Owner=:owner, ModifiedAt=:modifiedAt 
                WHERE ID=:id";
        $params = array(
            "id"=>$this->get_id(), 
            "title"=>$this->get_title(), 
            "owner"=>$this->get_owner()->get_id(),
            "modifiedAt"=>$this->set_modifiedDate_and_get_sql()
        );
        
        $this->execute($sql, $params);
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

    //    TOOLBOX    //


}