<?php

require_once "framework/Model.php";
require_once "model/Comment.php";


class Card extends Model {
    use DateTrait;

    private ?string $id;
    private string $title;
    private string $body;
    private string $position;
    private User $author;
    private Column $column;

    public static function create_new(string $title, User $author, Column $column): Card {
        return new Card(
            $title,
            "",
            self::get_cards_count($column),
            $author,
            $column
        );
    }

    public function __construct(string $title, 
                                string $body, 
                                int $position,
                                User $author, 
                                Column $column,
                                ?string $id = null,
                                ?DateTime $createdAt=null,
                                ?DateTime $modifiedAt=null) {

        $this->id = $id;
        $this->title = $title;
        $this->body = $body;
        $this->position = $position;
        $this->author = $author;
        $this->column = $column;
        $this->createdAt = $createdAt;
        $this->modifiedAt = $modifiedAt;
    }


    //    GETTERS    //

    public function get_id(): ?string {
        return $this->id;
    }

    public function get_title(): string {
        return $this->title;
    }

    public function get_body(): string {
        return $this->body;
    }

    public function get_position(): string {

        return $this->position;
    }

    public function get_author(): User {
        return $this->author;
    }

    public function get_author_name(): string {
        return $this->author->get_fullName();
    }

    public function get_author_id(): string {
        return $this->author->get_id();
    }

    public function get_column(): Column {
        return $this->column;
    }

    public function get_column_cards(): array {
        return $this->column->get_cards();
    }

    public function get_column_id(): string {
        return $this->column->get_id();
    }

    public function get_column_position(): string {
        return $this->column->get_position();
    }

    public function get_all_columns(): array {
        return $this->column->get_board_columns();
    }

    public function get_board_id(): string {
        return $this->column->get_board_id();
    }

    public function get_board() {
        return $this->column->get_board();
    }

    public function get_board_owner(): User{
        return $this->column->get_board_owner();
    }

    public function get_comments(): array {
        return Comment::get_comments_for_card($this);
    }

    

    // SETTERS //

    public function set_id(string $id) {
        $this->id = $id;
    }

    public function set_title(string $title) {
        $this->title = $title;
    }

    public function set_body(string $body) {
        $this->body = $body;
    }

    public function set_column(Column $column) {
        $this->column = $column;
    }

    public function set_position(string $position) {
        $this->position = $position;
    }

    public function validate(): array {
        $errors = [];
        if (!Validation::str_longer_than($this->get_title(), 2)) {
            $errors[] = "Title must be at least 3 characters long";
        }
        if(!$this->title_is_unique()){
            $errors[] = "Title already exists in this column";
        }
        return $errors;
    }
    public function validate_update(): array{
        $errors = [];
        if (!Validation::str_longer_than($this->get_title(), 2)) {
            $errors[] = "Title must be at least 3 characters long";
        }
        if(!$this->title_is_unique_update()){
            $errors[] = "Title already exists in this column";
        }
        return $errors;
    }

    public function has_comments(): bool {
        return $this->get_comments_count() > 0;
    }

    public function get_comments_count(): string {
        return Comment::get_comments_count($this);
    }

    public function is_first(): bool {
        return $this->get_position() == 0;
    }

    public function is_last(): bool {
        return $this->get_position() == self::get_cards_count($this->get_column()) - 1;
    }

    //    QUERIES    //

    // renvoie true si le titre de la carte $card est unique pour la colonne de la carte $card
    public function title_is_unique(){
        $sql = 
        "SELECT * 
         FROM card 
         WHERE Title=:title AND `Column`=:column";
         $params = array("title"=>$this->get_title(), "column"=>$this->get_column_id());
         $query = self::execute($sql, $params);
         $data=$query->fetch();
         return $query->rowCount()==0 ;
    }
    //renvoie true si le titre de la carte $card pour la colonne de la carte $card est unique
    // update version
    public function title_is_unique_update(){
        $sql = 
        "SELECT * 
         FROM card 
         WHERE Title=:title AND `Column`=:column AND ID <>:id ";
         $params = array(
             "title"=>$this->get_title(), 
             "column"=>$this->get_column_id(),
             "id"=>$this->get_id()
            );
         $query = self::execute($sql, $params);
         $data=$query->fetch();
         return $query->rowCount()==0 ;
    }
    //renvoie un objet Card dont les attributs ont pour valeur les données $data
    protected static function get_instance($data) :Card {
        list($createdAt, $modifiedAt) = self::get_dates_from_sql($data["CreatedAt"], $data["ModifiedAt"]);
        return new Card(
            $data["Title"],
            $data["Body"],
            $data["Position"],
            User::get_by_id($data["Author"]),
            Column::get_by_id($data["Column"]),
            $data["ID"],
            $createdAt,
            $modifiedAt
        );
    }
    //renvoie un objet Card dont l'id est $id
    public static function get_by_id($card_id): ?Card {
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

    //renvoie un tableau de cartes triées dont la colonne est $column;
    public static function get_cards_for_column(Column $column): array {
        $sql = 
            "SELECT * 
             FROM card 
             WHERE `Column`=:column 
             ORDER BY Position";
        $params = array("column"=>$column->get_id());
        $query = self::execute($sql, $params);
        $data = $query->fetchAll();

        $cards = [];
        foreach ($data as $rec) {
            array_push($cards, self::get_instance($rec));
        }
        return $cards;
    }

    //nombre de cartes dans la colonne
    public static function get_cards_count(Column $column) {
        $sql =
            "SELECT COUNT(Position)  as nbr
             FROM card 
             WHERE `Column`=:id";
        $params = array("id"=>$column->get_id());
        $query = self::execute($sql, $params);
        $data = $query->fetch();
        return $data["nbr"];
    }

    //insère la carte dans la db, la carte reçoit un nouvel id.
    public function insert() {
        $sql = 
            "INSERT INTO card(Title, Body, Position, Author, `Column`) 
             VALUES(:title, :body, :position, :author, :column)";
        $params = array(
            "title" => $this->get_title(),
            "body" => $this->get_body(),
            "position" => $this->get_position(),
            "author" => $this->get_author_id(),
            "column" => $this->get_column_id()
        );

        $this->execute($sql, $params);
        $id = $this->lastInsertId();
        $this->set_id($id);
        $this->set_dates_from_instance(self::get_by_id($id));
    }

    //met à jour la db avec les valeurs des attributs actuels de l'objet Card
    public function update() {
        $sql = "UPDATE card SET Title=:title, Body=:body, Position=:position, ModifiedAt=NOW(), Author=:author, 
                `Column`=:column WHERE ID=:id";
        $params = array(
            "id" => $this->get_id(), 
            "title" => $this->get_title(),
            "body" => $this->get_body(), 
            "position" => $this->get_position(),
            "author" => $this->get_author_id(),
            "column" => $this->get_column_id()
        );

        $this->execute($sql, $params);
        $this->set_dates_from_instance(self::get_by_id($this->get_id()));
    }

    /*
        supprime la carte de la db, ainsi que tous les commentaires liés a cette carte
    */
    public function delete() {
        foreach ($this->get_comments() as $comment) {
            $comment->delete();
        }
        $sql = "DELETE FROM card 
                WHERE ID = :id";
        $param = array('id' => $this->get_id());
        self::execute($sql, $param);
    }


    //    MOVE CARD    //

    public function move_up(): void {
        $pos = $this->get_position();

        if ($pos > 0) {
            $target = $this->get_column_cards()[$pos-1];
            $this->set_position($target->get_position());
            $target->set_position($pos);

            $this->update();
            $target->update();
        }
    }

    public function move_down(): void {
        $pos = $this->get_position();
        $cards = $this->get_column_cards();

        if ($pos < sizeof($cards)-1) {
            $target = $cards[(int)$pos + 1];
            $this->set_position($target->get_position());
            $target->set_position($pos);

            $this->update();
            $target->update();;
        }
    }

    public function move_left(): void {
        $pos = $this->get_column_position();

        if ($pos > 0) {
            $target = $this->get_all_columns()[$pos-1];

            /*Faut décrémenter les suivantes avant de changer de colonne*/
            Card::decrement_following_cards_position($this);

            $this->set_column($target);
            $this->set_position(Card::get_cards_count($target));
            $this->update();
        }
    }

    public function move_right(): void {
        $pos = $this->get_column_position();
        $colList = $this->get_all_columns();

        if ($pos < sizeof($colList)-1) {
            $target = $colList[$pos+1];

            /*Faut décrémenter les suivantes avant de changer de colonne*/
            Card::decrement_following_cards_position($this);

            $this->set_column($target);
            $this->set_position(Card::get_cards_count($target));
            $this->update();

        }
    }

    /*
        fonction utilisée lors de la suppression d'une carte. mets a jour la position des autres cartes de la colonne.
        on n'utilise pas update pour ne pas mettre a jour 'modified at', vu qu'il ne s'agit pas d'une modif de la carte voulue par 
        l'utilisateur, mais juste une conséquence d'une autre action
    */
    public static function decrement_following_cards_position($card){
        $sql = "UPDATE card 
                SET Position = Position - 1
                WHERE `Column`=:column 
                AND Position>:pos";
        $params = array(
            "column" => $card->get_column_id(),
            "pos" => $card->get_position()
        );
        self::execute($sql,$params);
    }
}