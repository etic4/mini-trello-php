<?php

//require_once "BoardModel.php";
//require_once "BoardValidator.php";
require_once "framework/Model.php";
require_once "DBTools.php";
require_once "model/User.php";
require_once "model/Column.php";

class Board extends Model {
    private ?string $id;
    private string $title;
    private User $owner;
    private DateTime $createdAt;
    private ?DateTime $modifiedAt;
    private array $columns;

    public function __construct(string $title, User $owner, ?string $id=null, DateTime $createdAt, ?DateTime $modifiedAt=null) {
        $this->id = $id;
        $this->title = $title;
        $this->owner = $owner;
        $this->createdAt = $createdAt;
        $this->modifiedAt = $modifiedAt;
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
    
    public function get_createdAt(): DateTime {
        return $this->createdAt;
    }

    public function get_modifiedAt(): DateTime {
        return $this->modifiedAt;
    }

    public function get_columns(): array {
        return Column::get_columns_from_board($this);
    }

    protected static function get_instance($data): Board {
        return new Board(
            $data["Title"],
            User::get_by_id($data["Owner"]),
            $data["ID"],
            DBTools::php_date($data["CreatedAt"]), 
            DBTools::php_date_modified($data["ModifiedAt"], $data["CreatedAt"])
        );
    }


    //    SETTERS    //

    public function set_id(string $id): void {
        $this->id = $id;
    }

    public function set_title(string $title): void {
        $this->title = $title;
    }

    public function set_modifiedDate(): void {
        $this->modifiedAt = new DateTime("now");
    }

    public function set_columns(): void {
        $this->columns = $this->get_columns();
    }
 

    //    VALIDATION    //

    public function validate(): array {
        $errors = [];
        if (!Validation::str_longer_than($this->title, 2)) {
            $errors[] = "Title must be at least 3 characters long";
        }
        if (!Validation::is_unique_title($this)) {
            $errors[] = "A board by this title already exists";
        }
        return $errors;
    }


    //    QUERIES    //

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
            $board = self::get_instance($rec);
            $board->set_columns();

            array_push($boards, $board);
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
            $board = self::get_instance($rec);
            array_push($boards, $board);
        }

        return $boards;
    }

    public function get_by_title(): int {
        $sql = 
            "SELECT * 
             FROM board 
             WHERE Title = :title";
        $params= array("title" => $this->get_title());
        $query = self::execute($sql, $params);
        $data = $query->fetchAll();

        return count($data);
    }
    
    public function insert(): Board {
        $sql = 
            "INSERT INTO board(Title, Owner) 
             VALUES(:title, :owner)";
        $params = array(
            "title"=>$this->get_title(),
            "owner"=>$this->get_owner_id()
            );
        $this->execute($sql, $params);

        return $this->get_by_id($this->lastInsertId());
    }

    public function update(): void {
        $this->set_modifiedDate();
        $modifiedAt = DBTools::sql_date($this->get_modifiedAt());

        $sql = 
            "UPDATE board 
             SET Title=:title, Owner=:owner, ModifiedAt=:modifiedAt 
             WHERE ID=:id";
        $params = array(
            "id"=>$this->get_id(), 
            "title"=>$this->get_title(), 
            "owner"=>$this->get_owner_id(),
            "modifiedAt"=>$modifiedAt
        );
        
        $this->execute($sql, $params);
    }
    
    public function delete(): void {
        Column::delete_all($this);
        $sql = 
            "DELETE FROM board 
             WHERE ID = :id";
        $params = array("id"=>$this->get_id());
        $this->execute($sql, $params);
    }

    public static function delete_all(User $user): void {
        foreach (Board::get_users_boards($user) as $board) {
            $board->delete();
        }
    }

    /*  
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

    
    //    TOOLBOX    //

    public function move_left(Column $col): void {
        $pos = $col->get_position();

        if ($pos > 0) {
            $target = $this->get_columns()[$pos - 1];
            $col->set_position($pos - 1);
            $target->set_position($pos);

            $col->update();
            $target->update();
        }
    }

    public function move_right(Column $col): void {
        $pos = $col->get_position();
        $columns = $this->get_columns();

        if ($pos < sizeof($columns) - 1) {
            $target = $columns[$pos + 1];
            $col->set_position($pos + 1);
            $target->set_position($pos);

            $col->update();
            $target->update();;
        }
    }

}