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
        return new Card(
            $title, 
            "", 
            self::get_card_count($column), 
            new DateTime(), 
            $author, 
            $column, 
            null, 
            null
        );
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

    public function get_created_at() {
        return $this->createdAt;
    }

    public function get_modified_at(){
        return $this->modifiedAt;
    }

    public function get_author() {
        return $this->author;
    }

    public function get_column() {
        return $this->column;
    }

    public function get_column_inst(): Column {
        $col = $this->get_column();
        if (is_int(intval($col)) || is_null($col)) {
            return Column::get_by_id($col);
        }
        return $col;
    }

    public function get_comments() {
        return $this->comments;
    }

    public function get_comments_inst() {
        return Comment::get_comments_from_card($this->id);
    }

    public function get_board_id() {
        $column = $this->get_column_inst();
        return $column->get_board_inst()->get_id();
    }

    //renvoie un objet Card dont les attributs ont pour valeur les données $data
    protected static function get_instance($data) :Card {
        return new Card(
            $data["Title"],
            $data["Body"],
            $data["Position"],
            DBTools::php_date($data["CreatedAt"]),
            User::get_by_id($data["Author"]), 
            $data["Column"],
            $data["ID"],
            DBTools::php_date($data["ModifiedAt"])
        );
    }


    //    SETTERS    //

    public function set_id($id){
        $this->id=$id;
    }

    public function set_title($title){
        $this->title=$title;
    }

    public function set_body($body){
        $this->body=$body;
    }

    public function set_position($position){
        $this->position=$position;
    }

    public function set_created_at($createdAt){
        $this->createdAt=$createdAt;
    }

    public function set_modified_at($modifiedAt){
        $this->modifiedAt = new DateTime("now");
    }

    public function set_author($author){
        $this->author=$author;
    }

    public function set_column($column){
        $this->column=$column;
    }

    public function set_comments($comments){
        $this->comments=$comments;
    }



    //    QUERIES    //

    //renvoie un objet Card dont l'id est $id
    public static function get_by_id($card_id) {
        $sql = 
            "SELECT * 
             FROM card 
             WHERE ID=:id";
        $query = self::execute($sql, array("id"=>$card_id));
        $data = $query->fetch();

        if ($query->rowCount() == 0) {
            return null;
        } else {
            return self::get_instance($data);
        }
    }

    //renvoie un tableau de cartes triées dont la colonne est $column_id; chaque carte a son tableau de Comment associé.
    public static function get_cards_from_column($column): array {
        $sql = 
            "SELECT * 
             FROM card 
             WHERE `Column`=:column 
             ORDER BY `Column`, Position";
        $params = array("column"=>$column->get_id());
        $query = self::execute($sql, $params);
        $data = $query->fetchAll();

        $cards = [];
        foreach ($data as $rec) {
            $createdAt = DBTools::php_date($rec["CreatedAt"]);
            $modifiedAt = DBTools::php_date_modified($rec["ModifiedAt"], $rec["CreatedAt"]);
            $card = new Card(
                $rec["Title"], 
                $rec["Body"], 
                $rec["Position"], 
                $createdAt, 
                User::get_by_id($rec["Author"]), 
                $rec["Column"], 
                $rec["ID"], 
                $modifiedAt
            );
            $card->comments = Comment::get_comments_from_card($card->id);
            array_push($cards, $card);
        }
        return $cards;
    }

    //renvoie un tableau de cartes dont la colonne est column_id
    public static function get_cards_by_column($column){
        $sql =
            "SELECT * 
             FROM card WHERE `Column`=:id 
             ORDER BY Position";
        $params = array("id" => $column->get_id());
        $query = self::execute($sql, $params);
        $data = $query->fetchAll();

        $objects = array();
        foreach ($data as $rec) {
            array_push($objects, static::get_instance($rec));
        }
        return $objects;
    }

    //position de la dernière Card de la Column
    public static function get_last_position($column_id) {
        $sql = 
            "SELECT MAX(Position) 
             FROM card 
             WHERE `Column`=:id";
        $params = array("id"=>$column_id);
        $query = self::execute($sql, $params);
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return -1;
        } else {
            return $data["MAX(Position)"];
        }
    }

    //nombre de cartes dans la colonne
    public static function get_card_count($column_id) {
        $sql =
            "SELECT count(Position) 
             FROM card 
             WHERE `Column`=:id";
        $params = array("id"=>$column_id);
        $query = self::execute($sql, $params);
        $data = $query->fetch();
        return $data["count(Position)"];
    }

    //insère la carte dans la db, la carte reçoit un nouvel id. renvoie un objet Card avec l'id maj.
    public function insert() {
        $sql = 
            "INSERT INTO card(Title, Body, Position, CreatedAt, ModifiedAt, Author, `Column`) 
             VALUES(:title, :body, :position, :createdAt, :modifiedAt, :author, :column)";
        $params = array(
            "title" => $this->get_title(),
            "body" => $this->get_body(),
            "position" => $this->get_position(),
            "createdAt" => DBTools::sql_date($this->get_created_at()),
            "modifiedAt" => $this->get_modified_at(),
            "author" => $this->get_author(),
            "column" => $this->get_column()
        );

        $this->execute($sql, $params);

        return $this->get_by_id($this->lastInsertId());
    }

    //met à jour la db avec les valeurs des attibuts actuels de l'objet Card
    public function update() {
        $this->set_modified_at(date('Y-m-d H:i:s'));
        $modifiedAt = DBTools::sql_date($this->get_modified_at());

        /*Obligé de faire ça pour le moment parce que c'est le bordel et qu'on sait pas si les attributs qui représentent
        les clés étrangère en DB stockent une instance ou un string (qui représente un entier)*/
        $author = $this->get_author();
        if ($author instanceof User) {
            $author = $author->get_id();
        }

        $sql = "UPDATE card SET Title=:title, Body=:body, Position=:position, ModifiedAt=:ma, Author=:author, `Column`=:column WHERE ID=:id";
        $params = array(
            "id" => $this->get_id(), 
            "title" => $this->get_title(),
            "body" => $this->get_body(), 
            "position" => $this->get_position(),
            "ma" => $modifiedAt, 
            "author" => $author, 
            "column" => $this->get_column()
        );

        $this->execute($sql, $params);
    }

    /*
        supprime la carte de la db, ainsi que tous les commentaires liés a cette carte
    */
      
    public function delete() {
        Comment::delete_all($this->id);
        $sql = "DELETE FROM card 
                WHERE ID = :id";
        $param = array('id' => $this->id);
        self::execute($sql, $param);
    }

    public static function delete_all($column) {
        foreach($column->get_cards() as $card) {
            $card->delete();
        }
    }


    /*  
        renvoie un string qui est le nom complet de l'auteur de la carte
    */
    public function get_author_name(){
        $sql = 
            "SELECT FullName 
             FROM User 
             WHERE ID=:id";
        $query = self::execute($sql, array("id"=>$this->author));
        $name = $query->fetch();
        return $name["FullName"];
    }

    /*
        fonction utilisée lors de la suppression d'une carte. mets a jour la position des autres cartes de la colonne.
        on n'utilise pas update pour ne pas mettre a jour 'modified at', vu qu'il ne s'agit pas d'une modif de la carte voulue par 
        l'utilisateur, mais juste une conqéquence d'une autre action
    */
    public static function update_card_position(Card $card){

        $sql =
            "SELECT * 
             FROM Card 
             WHERE `Column`=:column 
             AND Position>=:pos 
             ORDER BY Position";
        $params = array(
            "column" => $card->get_column(), 
            "pos" => $card->get_position() + 1
        );
        $querry = self::execute($sql,$params);
        $data = $querry->fetchall();
        foreach($data as $d){
            $c = Card::get_instance($d);
            $pos = $c->get_position() - 1;
            self::execute(
                "UPDATE Card 
                 SET Position=:pos 
                 WHERE id=:id",
                 array(
                     "pos"=>$pos,
                     "id"=>$c->get_id()
                     )
                 );
        }
    }
}