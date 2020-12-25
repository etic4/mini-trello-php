<?php

require_once "framework/Model.php";
require_once "model/DBTools.php";
require_once "model/Card.php";
require_once "model/User.php";
//require_once "model/Date.php";

class Comment extends Model{
    use Date;

    private $id;
    private $body;
    private $author;
    private $card;

    public function __construct($body,$author,$card,$id=null,$createdAt=null,$modifiedAt=null){

        $this->id=$id;
        $this->body=$body;
        $this->createdAt=$createdAt;
        $this->modifiedAt=$modifiedAt;
        $this->author=$author;
        $this->card=$card;
    }

    // GETTERS

    public function get_id(){
        return $this->id;
    }

    public function get_body(){
        return $this->body;
    }

    public function get_author(){
        return $this->author;
    }

    public function get_card(){
        return $this->card;
    }

    /*
        renvoie un comment avec comme attributs les donnee de $data
    */
    protected static function get_instance($data) :Comment{

        $ca = DBTools::php_date($data["CreatedAt"]);
        $ma = DBTools::php_date_modified($data["ModifiedAt"],$data["CreatedAt"]);
        return new Comment(
            $data["Body"], 
            $data["Author"], 
            $data["Card"],
            $data["ID"], 
            $ca, 
            $ma
        );
        
    }


    //   SETTERS

    public function set_id($id){
        $this->id=$id;
    }

    public function set_body($body){
        $this->body=$body;
    }

    public function set_author($author){
        $this->author=$author;
    }

    public function set_card($card){
        $this->card=$card;
    }


    //   QUERIES

    public function get_author_name(){
        $sql = 
            "SELECT FullName 
             FROM User 
             WHERE ID=:id";
        $query = self::execute($sql, array("id"=>$this->author));
        $name=$query->fetch();
        return $name["FullName"];
    }

    /*
     * insertion en db avec les valeurs d'instances.
     */
    public function insert() { 
        $sql=
            "INSERT INTO Comment (Body, CreatedAt, ModifiedAt, Author, Card) 
             VALUES (:body, :createdAt, :modifiedAt, :author, :card)";
        $params=array(
            "body"=>$this->get_body(),
            "createdAt"=>$this->get_createdAt(),
            "modifiedAt"=>$this->get_modifiedAt(),
            "author"=>$this->get_author(),
            "card"=>$this->get_card()
        );
        $this->execute($sql, $params);

        return $this->get_by_id($this->lastInsertId());
    }

    /*
        renvoie un objet comment dont l'id est $id
    */
    public static function get_by_id($id) {
        $sql = 
            "SELECT * 
             FROM Comment 
             WHERE ID=:id";
        $query = self::execute($sql, array("id"=>$id));

        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return null;
        } else {
            return static::get_instance($data);
        }
    }
    
    /*
        mets a jour la db avec les valeurs de l'instance
    */
    public function update() {
        
        $this->set_modifiedAt(new DateTime("now"));
        $ma = DBTools::sql_date($this->get_modifiedAt());
        $ca=DBTools::sql_date($this->get_createdAt());

        $sql = 
            "UPDATE Comment 
             SET Body=:body, CreatedAt=:ca, ModifiedAt=:ma, Author=:author, Card=:card 
             WHERE ID=:id";
        $params = array(
            "id"=>$this->get_id(),
            "body"=>$this->get_body(), 
            "ca"=>$ca,"ma"=>$ma, 
            "author"=>$this->get_author(), 
            "card"=>$this->get_card()
        );

        $this->execute($sql, $params);
    }

    /*
        renvoie un tab de comment dont la carte est $card_id
    */
    public static function get_comments_from_card($card_id) {
        $sql = 
            "SELECT * 
             FROM comment 
             WHERE Card=:card";
        $param = array("card" => $card_id);
        $query = self::execute($sql, $param);
        $data = $query->fetchAll();
        $comments = array();
        foreach ($data as $rec) {
            $comment = self::get_instance($rec);
            array_push($comments, $comment);
        }
        return $comments;
    }

    public function delete() {
        $sql = "DELETE FROM comment 
                WHERE ID = :id";
        $param = array('id' => $this->id);
        self::execute($sql, $param);
    }

    public static function delete_all($card_id) {
        foreach(Comment::get_comments_from_card($card_id) as $comment) {
            $comment->delete();
        }
    }

}


?>