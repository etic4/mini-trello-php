<?php

require_once "framework/Model.php";
require_once "model/Card.php";
require_once "model/User.php";
//require_once "model/Date.php";

class Comment extends Model{
    use DateTrait;

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
        $this->createdAt = $createdAt;
        $this->modifiedAt = $modifiedAt;
    }
    public static function create_new(String $body, User $author, Card $card): Comment{
        return new Comment($body, $author, $card, null);
    }

    // GETTERS

    public function get_id(): ?string {
        return $this->id;
    }

    public function get_body(): string {
        return $this->body;
    }


    public function get_author(): User {
        return $this->author;
    }

    public function get_author_fullName() :string {
        return $this->author->get_fullName();
    }
 
    public function get_card(): Card {
        return $this->card;
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

    /*
        renvoie un comment avec comme attributs les donnee de $data
    */
    protected static function get_instance($data): Comment {
        list($createdAt, $modifiedAt) = self::get_dates_from_sql($data["CreatedAt"], $data["ModifiedAt"]);
        return new Comment(
            $data["Body"],
            User::get_by_id($data["Author"]),
            Card::get_by_id($data["Card"]),
            $data["ID"],
            $createdAt,
            $modifiedAt
        );
    }

    /*
         insertion en db avec les valeurs d'instances.
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
        $id = $this->lastInsertId();
        $this->set_id($id);
        $this->set_dates_from_instance(self::get_by_id($id));
    }

    /*
        renvoie un objet comment dont l'id est $id
    */
    public static function get_by_id($id): ?Comment {
        $sql = 
            "SELECT * 
             FROM comment 
             WHERE ID=:id
             ORDER BY ModifiedAt DESC, CreatedAt DESC";
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
        $this->set_dates_from_instance(self::get_by_id($this->get_id()));
    }

    public function delete() {
        $sql = "DELETE FROM comment 
                WHERE ID = :id";
        $param = array('id' => $this->id);
        self::execute($sql, $param);
    }

    /*
        renvoie un tab de comment dont la carte est $card
    */
    public static function get_comments_for_card(Card $card): array {
        $sql = 
            "SELECT * 
             FROM comment 
             WHERE Card=:id
             ORDER BY ModifiedAt DESC, CreatedAt DESC";
        $param = array("id" => $card->get_id());
        $query = self::execute($sql, $param);
        $data = $query->fetchAll();

        $comments = array();
        foreach ($data as $rec) {
            array_push($comments, self::get_instance($rec));
        }
        return $comments;
    }

    public static function get_comments_count(Card $card): string {
        $sql = "SELECT COUNT(*) as nbr FROM comment WHERE Card=:cardId";
        $params = array("cardId" => $card->get_id());
        $query = self::execute($sql, $params);
        $data = $query->fetch();

        return $data["nbr"];
    }

    // fonction utilitaires
    public function get_author_name(): String{
        return $this->get_author()->get_fullName();
    }

    public function get_time_string(): String{
        $created=$this->get_created_intvl();
        $ma=$this->get_modified_intvl();
        return "Created ".$created.". ".$ma.".";
    }
}
