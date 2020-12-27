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

    public function __construct(string $body, User $author, Card $card, ?string $id=null, ?DateTime $createdAt=null,
                                ?DateTime $modifiedAt=null){
        $this->id=$id;
        $this->body=$body;
        $this->author=$author;
        $this->card=$card;
        $this->set_createdAt($createdAt);
        $this->set_modifiedAt($modifiedAt);
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
            $data["Author"],
            $data["Card"],
            $data["ID"],
            DBTools::php_date($data["CreatedAt"]),
            DBTools::php_date($data["ModifiedAt"])
        );
    }

    /*
     * insertion en db avec les valeurs d'instances.
     */
    public function insert() { 
        $sql=
            "INSERT INTO comment (Body, CreatedAt, ModifiedAt, Author, Card) 
             VALUES (:body, :createdAt, :modifiedAt, :author, :card)";
        $params=array(
            "body"=>$this->get_body(),
            "author"=>$this->get_author()->get_id(),
            "card"=>$this->get_card()->get_id(),
            "createdAt"=>DBTools::sql_date($this->get_createdAt()),
            "modifiedAt"=>DBTools::sql_date($this->get_modifiedAt())
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
             SET Body=:body, Author=:author, Card=:card , ModifiedAt=:modifiedAt
             WHERE ID=:id";
        $params = array(
            "id"=>$this->get_id(),
            "body"=>$this->get_body(), 
            "author"=>$this->get_author()->get_id(),
            "card"=>$this->get_card()->get_id(),
            "modifiedAt"=>$this->set_modifiedDate_and_get_sql()
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
