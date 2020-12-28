<?php

require_once "framework/Model.php";
require_once "model/DBTools.php";
require_once "model/Card.php";
require_once "model/User.php";

class Comment extends Model{
    use Date;

    private ?string $id;
    private string $body;
    private User $author;
    private Card $card;

    public function __construct(string $body, User $author, Card $card, ?string $id=null, ?string $createdAt=null,
                                ?string $modifiedAt=null){
        $this->id=$id;
        $this->body=$body;
        $this->author=$author;
        $this->card=$card;
        $this->set_createdAt_from_sql($createdAt);
        $this->set_modifiedAt_from_sql($modifiedAt);
    }

    // GETTERS

    public function get_id(): string {
        return $this->id;
    }

    public function get_body(): string {
        return $this->body;
    }

    public function get_author(): User {
        return $this->author;
    }

    public function get_card(): Card {
        return $this->card;
    }

    //   SETTERS

    public function set_id($id){
        $this->id=$id;
    }


    //   QUERIES

    /*
        renvoie un comment avec comme attributs les donnee de $data
    */
    protected static function get_instance($data): Comment {
        return new Comment(
            $data["Body"],
            User::get_by_id($data["Author"]),
            Card::get_by_id($data["Card"]),
            $data["ID"],
            $data["CreatedAt"],
            $data["ModifiedAt"]
        );
    }

    /*
     * insertion en db avec les valeurs d'instances.
     */
    public function insert() { 
        $sql=
            "INSERT INTO comment (Body, Author, Card) 
             VALUES (:body, :author, :card)";
        $params=array(
            "body"=>$this->get_body(),
            "author"=>$this->get_author()->get_id(),
            "card"=>$this->get_card()->get_id()
        );
        $this->execute($sql, $params);
        $this->set_id($this->lastInsertId());
    }

    /*
        renvoie un objet comment dont l'id est $id
    */
    public static function get_by_id($id): ?Comment {
        $sql = 
            "SELECT * 
             FROM comment 
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
        $sql = 
            "UPDATE comment 
             SET Body=:body, Author=:author, Card=:card , ModifiedAt=NOW()
             WHERE ID=:id";
        $params = array(
            "id"=>$this->get_id(),
            "body"=>$this->get_body(), 
            "author"=>$this->get_author()->get_id(),
            "card"=>$this->get_card()->get_id()
        );
        $this->execute($sql, $params);
    }

    /*
        renvoie un tab de comment dont la carte est $card
    */
    public static function get_comments_for_card(Card $card): array {
        $sql = 
            "SELECT * 
             FROM comment 
             WHERE Card=:id";
        $param = array("id" => $card->get_id());
        $query = self::execute($sql, $param);
        $data = $query->fetchAll();

        $comments = array();
        foreach ($data as $rec) {
            array_push($comments, self::get_instance($rec));
        }
        return $comments;
    }

    public function delete() {
        $sql = "DELETE FROM comment 
                WHERE ID = :id";
        $param = array('id' => $this->id);
        self::execute($sql, $param);
    }
}
