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


    public function __construct(string $title, User $owner, ?string $id=null, ?string $createdAt=null, ?string $modifiedAt=null) {
        $this->id = $id;
        $this->title = $title;
        $this->owner = $owner;
        $this->set_createdAt_from_sql($createdAt);
        $this->set_modifiedAt_from_sql($modifiedAt, $createdAt);
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
            $errors[] = "Title must be at least 3 characters long";
        }
        if (!Validation::is_unique_title($this->title)) {
            $errors[] = "A board with the same title already exists";
        }
        return $errors;
    }


    //    QUERIES    //

    protected static function get_instance($data): Board {
        return new Board(
            $data["Title"],
            User::get_by_id($data["Owner"]),
            $data["ID"],
            $data["CreatedAt"],
            $data["ModifiedAt"]
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
            "INSERT INTO board(Title, Owner, CreatedAt, ModifiedAt) 
             VALUES(:title, :owner, NOW(), null)";
        $params = array(
            "title"=>$this->get_title(),
            "owner"=>$this->get_owner_id(),

            );
        $this->execute($sql, $params);
        $this->set_id($this->lastInsertId());
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

    public static function delete_all(User $user): void {
        foreach (Board::get_users_boards($user) as $board) {
            $board->delete();
        }
    }

    /*  TODO: si on a des instances partout, on peut juste faire: $this->get_column()->get_board()->get_owner() et pas de fetch en DB
        renvoie le propriétaire du board contenant la carte id_card
    */
    public static function get_board_owner(Card $card): User{
        $sql=
            "SELECT Owner 
             FROM Board b, `Column` co, Card ca 
             WHERE ca.id=:id_card 
             AND co.id=ca.column 
             AND co.Board=b.id";
        $params=array("id_card" => $card->get_id());
        $query = self::execute($sql, $params);
        $data = $query->fetch();

        return User::get_by_id($data["Owner"]);
    }


}