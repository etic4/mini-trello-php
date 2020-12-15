<?php

require_once "framework/Model.php";
require_once "DBTools.php";
require_once "model/Comment.php";

class Card extends Model {
    private $id;
    private $title;
    private $body;
    private $position;
    private $createdAt;
    private $modifiedAt;
    private $author;
    private $column;
    private $comments;

    public function __construct($title, $body, $position, $createdAt, $author, $column, $id = null, $modifiedAt = null) {
        $this->id = $id;
        $this->title = $title;
        $this->body = $body;
        $this->position = $position;
        $this->createdAt = $createdAt;
        $this->modifiedAt = $modifiedAt;
        $this->author = $author;
        $this->column = $column;
    }

    public static function create_new($title, $author, $column) {
        $body = "";
        $position = Card::get_last_position($column) + 1;
        $createdAt = new DateTime();
        return new Card($title, $body, $position, $createdAt, $author, $column, null, null);
    }


    //    GETTERS    //

    public function get_id() {
        return $this->id;
    }

    public function get_title() {
        return $this->title;
    }

    public function get_body() {
        return $this->body;
    }

    public function get_position() {
        return $this->position;
    }

    public function get_createdAt() {
        return $this->createdAt;
    }

    public function get_author() {
        return $this->author;
    }

    public function get_column() {
        return $this->column;
    }


    public function get_comments() {
        return $this->comments;
    }


    //    SETTERS    //


    //    QUERIES    //

    public static function get_by_id($id) {
        $sql = "SELECT * FROM card WHERE ID=:id";
        $query = self::execute($sql, array("id"=>$id));
        $data = $query->fetch();

        if ($query->rowCount() == 0) {
            return null;
        } else {
            $createdAt = DBTools::php_date($data["CreatedAt"]);
            $modifiedAt = DBTools::php_date_modified($data["ModifiedAt"], $data["CreatedAt"]);
            return new Card($data["Title"], $data["Body"], $data["Position"], $createdAt, $data["Author"], $data["Column"], $data["ID"], $modifiedAt);
        }
    }

    public static function get_all_cards_from_column($column): array {
        $sql = "SELECT * FROM card WHERE `Column`=:column ORDER BY `Column`, Position";
        $params = array("column"=>$column->get_id());
        $query = self::execute($sql, $params);
        $data = $query->fetchAll();

        $cards = [];
        foreach ($data as $rec) {
            $createdAt = DBTools::php_date($rec["CreatedAt"]);
            $modifiedAt = DBTools::php_date_modified($rec["ModifiedAt"], $rec["CreatedAt"]);
            $card = new Card($rec["Title"], $rec["Body"], $rec["Position"], $createdAt, User::get_by_id($rec["Author"]), $rec["Column"], $rec["ID"], $modifiedAt);
            $card->comments = Comment::get_all_comments($card);
            array_push($cards, $card);
        }
        return $cards;
    }

    //position de la dernière Card de la Column
    public static function get_last_position($column_id) {
        $sql = "SELECT MAX(Position) FROM card WHERE `Column`=:id";
        $params= array("id"=>$column_id);
        $query = self::execute($sql, $params);
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return -1;
        } else {
            return $data["MAX(Position)"];
        }
    }

    public function insert() {
        $sql = "INSERT INTO card(Title, Body, Position, CreatedAt, Author, `Column`) 
            VALUES(:title, :body, :position, :createdAt, :author, :column)";
        $params = array(
            "title"=>$this->get_title(),
            "body"=>$this->get_body(),
            "position"=>$this->get_position(),
            "createdAt"=>DBTools::sql_date($this->get_createdAt()),
            "author"=>$this->get_author(),
            "column"=>$this->get_column()
        );
        $this->execute($sql, $params);

        return $this->get_by_id($this->lastInsertId());
    }

}