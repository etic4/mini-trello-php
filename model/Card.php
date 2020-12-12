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

    public function __construct($id = null, $title, $body, $position, $createdAt, $modifiedAt = null, $author, $column) {
        $this->id = $id;
        $this->title = $title;
        $this->body = $body;
        $this->position = $position;
        $this->createdAt = $createdAt;
        $this->modifiedAt = $modifiedAt;
        $this->author = $author;
        $this->column = $column;
    }


    //    GETTERS    //

    public function get_id() {
        return $this->id;
    }

    public function get_title() {
        return $this->title;
    }

    public function get_position() {
        return $this->position;
    }

    public function get_comments() {
        return $this->comments;
    }


    //    SETTERS    //


    //    QUERIES    //

    public static function get_all_cards_from_column($column): array {
        $sql = "SELECT * FROM card WHERE `Column`=:column ORDER BY `Column`, Position";
        $params = array("column"=>$column->get_id());
        $query = self::execute($sql, $params);
        $data = $query->fetchAll();

        $cards = [];
        foreach ($data as $rec) {
            $createdAt = DBTools::php_date($rec["CreatedAt"]);
            $modifiedAt = DBTools::php_date($rec["ModifiedAt"]);
            $card = new Card($rec["ID"], $rec["Title"], $rec["Body"], $rec["Position"], $createdAt, $modifiedAt, User::get_by_id($rec["Author"]), $rec["Column"]);
            $card->comments = Comment::get_all_comments($card);
            array_push($cards, $card);
        }
        return $cards;
    }

}