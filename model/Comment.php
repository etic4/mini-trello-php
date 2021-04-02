<?php

require_once "autoload.php";


class Comment {
    use DateTrait;

    private ?String $id;
    private String $body;
    private User $author;
    private Card $card;


    public static function get_tableName(): string {
        return "`comment`";
    }

    public static function get_FKName(): string {
        return "`Comment`";
    }



    public static function create_new(String $body, User $author, Card $card): Comment{
        return new Comment($body, $author, $card, null);
    }

    public function __construct(string $body, User $author, Card $card, ?string $id=null, ?DateTime $createdAt=null,
                                ?DateTime $modifiedAt=null){
        $this->id=$id;
        $this->body=$body;
        $this->author=$author;
        $this->card=$card;
        $this->createdAt = $createdAt;
        $this->modifiedAt = $modifiedAt;
    }


    // GETTERS

    public function get_id(): ?string {
        return $this->id;
    }


    public function set_id($id){
        $this->id=$id;
    }

    public function get_body(): string {
        return $this->body;
    }


    public function set_body($body){
        $this->body=$body;
    }

    public function get_author(): User {
        return $this->author;
    }


    public function set_author($author){
        $this->author=$author;
    }

    public function get_author_fullName(): string {
        return $this->author->get_fullName();
    }

    public function get_author_id(): string {
        return $this->get_author()->get_id();
    }
 
    public function get_card(): Card {
        return $this->card;
    }

    public function set_card($card){
        $this->card=$card;
    }

    public function get_card_id(): String {
        return $this->card->get_id();
    }

    public function get_board() {
        return $this->card->get_board();
    }





    // --- sql ---


    public function get_object_map(): array {
        return array(
            "Body" => $this->get_body(),
            "Author" => $this->get_author()->get_id(),
            "Card" => $this->get_card()->get_id(),
            "ID" => $this->get_id(),
            "ModifiedAt" => self::sql_date($this->get_createdAt()),
        );
    }




    public static function get_by_id(string $id) {
        return self::sql_select("ID", $id);
    }

    // insertion en db avec les valeurs d'instances.
    public function insert() { 
        self::sql_insert();
    }
    
    // rencoie true si l'utilisateur $user a le droit d'éditer le commentaire $id 
    public static function can_edit(int $id, User $user): bool{
        $comment = Comment::get_by_id($id);
        return !(is_null($comment) || $comment->get_author_id()!==$user->get_id());
    }
    
    //mets a jour la db avec les valeurs de l'instance
    public function update() {
        self::sql_update();
    }

    protected function cascade_delete() {
        return [];
    }

    public function delete() {
        self::sql_delete();
    }

    // renvoie un tableau de comment associé à la carte $card
    public static function get_comments_for_card(Card $card): array {
        $comments = self::sql_select_all("Card", $card->get_id());
        usort($comments, function (Comment $c1, Comment $c2) {
            return $c2->get_modifiedAt()->getTimestamp() -  $c1->get_modifiedAt()->getTimestamp();});
        usort($comments, function (Comment $c1, Comment $c2) {
            return $c2->get_createdAt()->getTimestamp() -  $c1->get_createdAt()->getTimestamp();});

        return $comments;
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
            array_push($comments, self::from_query($rec));
        }
        return $comments;
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

    //renvoie true si le commentaire peut être montré sur la page, false sinon
    public function can_be_show($show_comment): bool{
        return $show_comment == $this->get_id();
    }

}
