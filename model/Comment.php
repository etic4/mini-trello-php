<?php

require_once "framework/Model.php";
require_once "model/DBTools.php";
require_once "model/Card.php";
require_once "model/User.php";

class Comment extends Model {
    private $id;
    private $body;
    private $createdAt;
    private $modifiedAt;
    private $author;
    private $card; 

    public function __construct($id=null, $body, $createdAt, $modifiedAt=null, $author, $card) {
        $this->id = $id;
        $this->body = $body;
        $this->createdAt = $createdAt;
        $this->modifiedAt = $modifiedAt;
        $this->author = $author;
        $this->card = $card;
    }


    public static function get_all_comments($card) {
        $sql = "SELECT * FROM comment WHERE Card=:card";
        $param = array("card"=>$card->get_id());
        $query = self::execute($sql, $param);
        $data = $query->fetchAll();
        $comments = array();
        foreach ($data as $rec) {
            $comment = new Comment($rec["ID"], $rec["Body"], $rec["CreatedAt"], $rec["ModifiedAt"], $rec["Author"], $rec["Card"]);
            array_push($comments, $comment);
        }
        return $comments;
    }


}

