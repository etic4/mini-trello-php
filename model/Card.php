<?php

require_once "framework/Model.php";
require_once "DBTools.php";
require_once "model/Comment.php";

class Card extends Model{

    private $id;
    private $title;
    private $body;
    private $position;
    private $createdAt;
    private $modifiedAt;
    private $author;
    private $column;
    private $comments;

    public function __construct($title, $body, $position, $author, $column, $id=null, $createdAt=null, $modifiedAt=null){

        $this->id=$id;
        $this->title=$title;
        $this->position=$position;
        $this->body=$body;
        $this->createdAt=$createdAt;
        $this->modifiedAt=$modifiedAt;
        $this->author=$author;
        $this->column=$column;
        $this->comments=$this->get_comments();
    }


    /*
        "constructeur" de Card avec moins de paramètres
    */
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
    public function get_title(){
        return $this->title;
    }

    public function get_body(){
        return $this->body;
    }
    public function get_position(){
        return $this->position;
    }
    public function get_created_at(){
        return $this->createdAt;
    }
    public function get_modified_at(){
        return $this->modifiedAt;
    }
    public function get_author(){
        return $this->author;
    }
    public function get_column(){
        return $this->column;
    }

    public function get_column_inst(): Column {
        $col = $this->get_column();
        if (is_int(intval($col)) || is_null($col)) {
            return Column::get_by_id($col);
        }
        return $col;
    }

    public function get_comments(){
        return Comment::get_comments_by_card($this);
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
        $this->modifiedAt=$modifiedAt;
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

    /*
        insère la carte dans la db, la carte recoit un nouvel id. renvoie un objet Card avec l'id maj.
    */
    public function insert() {
        $sql="INSERT INTO card (Title, Body, Position, CreatedAt, ModifiedAt, Author, `Column`) 
        VALUES (:title, :body, :position, :createdAt, :modifiedAt, :author, :column)";

        $params=array(
            "title"=>$this->get_title(),
            "body"=>$this->get_body(),
            "position"=>$this->get_position(),
            "createdAt"=>DBTools::sql_date($this->get_created_at()),
            "modifiedAt"=>$this->get_modified_at(),
            "author"=>$this->get_author(),
            "column"=>$this->get_column()
        );

        $this->execute($sql, $params);

        return $this->get_by_id($this->lastInsertId());
    }


    /*
        renvoie un objet Card dont l'id est $id
    */
    public static function get_by_id($id) {
        $sql = "SELECT * FROM card WHERE ID=:id";
        $query = self::execute($sql, array("id"=>$id));

        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return null;
        } else {
            return static::get_instance($data);
        }
    }


    /*
        renvoie un objet Card dont les attributs ont pour valeur les données $data
    */
    protected static function get_instance($data) :Card{
        return new Card($data["Title"],$data["Body"],$data["Position"],$data["Author"], $data["Column"],$data["ID"],
            $data["CreatedAt"], $data["ModifiedAt"]);

    }


    /*
        mets a jour la db avec les valeurs des attibuts actuels de l'objet card
    */
    public function update() {
        $this->set_modified_at(date('Y-m-d H:i:s'));

        $sql = "UPDATE card SET Title=:title, Body=:body, Position=:position, CreatedAt=:ca, ModifiedAt=:ma, Author=:author, `Column`=:column WHERE ID=:id";
        $params = array("id"=>$this->get_id(), "title"=>$this->get_title(),"body"=>$this->get_body(), "position"=>$this->get_position(), "ca"=>$this->get_created_at(),
            "ma"=>$this->get_modified_at(), "author"=>$this->get_author(), "column"=>$this->get_column());

        $this->execute($sql, $params);
    }


    /*
        renvoie un tableau de carte dont la colonne est idcolumn
    */
    public static function get_cards_by_column($idcolumn){
        $sql="SELECT * FROM card WHERE `Column`=:id ORDER BY Position";
        $params=array("id"=>$idcolumn);
        $query = self::execute($sql, $params);
        $data = $query->fetchAll();

        $objects = array();
        foreach ($data as $rec) {
            array_push($objects, static::get_instance($rec));
        }
        return $objects;
    }


    /*
        renvoie un tableau de cartes triées dont la colonne est $column. chaque carte a son tableau de Comment associé.
    */
    public static function get_all_cards_from_column($column): array {
        $sql = "SELECT * FROM card WHERE `Column`=:column ORDER BY Position";
        $params = array("column"=>$column->get_id());
        $query = self::execute($sql, $params);
        $data = $query->fetchAll();

        $cards = [];
        foreach ($data as $rec) {
            $createdAt = DBTools::php_date($rec["CreatedAt"]);
            $modifiedAt = DBTools::php_date_modified($rec["ModifiedAt"], $rec["CreatedAt"]);
            $card = new Card($rec["Title"], $rec["Body"], $rec["Position"], User::get_by_id($rec["Author"]) ,$rec["Column"], $rec["ID"],  $createdAt, $modifiedAt, );
            $card->comments = Comment::get_comments_by_card($card);
            array_push($cards, $card);
        }
        return $cards;
    }


    /*
        renvoie la position de la dernière Card de la Column.
    */
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

}

