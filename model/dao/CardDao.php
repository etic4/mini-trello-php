<?php

require_once "autoload.php";

class CardDao extends BaseDao {
    protected const tableName = "`card`";


    //renvoie un tableau de cartes triées par leur position dans la colonne dont la colonne est $column;
    public static function get_cards(Column $column): array {
        $sql = new SqlGenerator(self::tableName);
        list($sql, $params) = $sql->select()->where(["`Column`" => $column->get_id()])->order_by(["Position" => "ASC"])->get();
        return self::get_many($sql, $params);
    }

    public static function delete(Card $card) {
        ParticipationDao::delete_all(["Card" => $card->get_id()]);

        foreach ($card->get_comments() as $comment) {
            CommentDao::delete($comment);
        }

        CardDao::delete_one($card);
    }


    public static function get_participating_cards(User $user, Board $board): array {
        $sql = new SqlGenerator();

        list($sql, $params) =
            $sql->select(["ca.*"])->join(["participate pa", "user us", "card ca, `column` co", "board bo"])
            ->on(["pa.Participant" => "us.ID", "pa.Card" => "ca.ID", "ca.Column" => "co.ID", "co.Board" => "bo.ID" ])
            ->where(["pa.Participant" => $user->get_id(), "bo.ID" => $board->get_id()])->get();

        return self::get_many($sql, $params);
    }


    // attribue les cartes de cet utilisateur à utilisateur 'Anonyme' dont l'ID est '6'
    // utilisé lors de la suppression d'un utilisateur
    public static function to_anonymous(User $user, string $anonID="6") {
        $sql = new SqlGenerator(self::tableName);
        list($sql, $params) =
            $sql->update()->set(["NewAuthor" => $anonID], ["Author" => "NewAuthor"])
                ->where(["Author" => $user->get_id()])->get();
        self::execute($sql, $params);
    }


//        Mets a jour la position des autres cartes de la colonne.
//        on n'utilise pas update pour ne pas mettre a jour 'modified at', vu qu'il ne s'agit pas d'une modif de la carte voulue par
//        l'utilisateur, mais juste une conséquence d'une autre action
    public static function decrement_following_cards_position($card){

        $sql = "UPDATE card 
                SET Position = Position - 1
                WHERE `Column`=:column 
                AND Position>:pos";
        $params = array( "column" => $card->get_column_id(), "pos" => $card->get_position());
        self::execute($sql,$params);
    }


    public static function from_query($data): Card {
        return new Card(
            $data["Title"],
            $data["Body"],
            $data["Position"],
            UserDao::get_by_id($data["Author"]),
            ColumnDao::get_by_id($data["Column"]),
            DateUtils::php_date($data["DueDate"]),
            $data["ID"],
            DateUtils::php_date($data["CreatedAt"]),
            DateUtils::php_date($data["ModifiedAt"])
        );
    }

    protected static function get_object_map($object): array {
        return array(
            "Title" => $object->get_title(),
            "Body" => $object->get_body(),
            "Position" => $object->get_position(),
            "Author" => $object->get_author()->get_id(),
            "`Column`" => $object->get_column()->get_id(),
            "DueDate" => DateUtils::sql_date($object->get_dueDate()),
            "ModifiedAt" => DateUtils::sql_date($object->get_modifiedAt()),
        );
    }

    public static function is_title_unique(Card $card): bool {
        $sql = new SqlGenerator(self::tableName);
        list($sql, $params) = $sql->select()
            ->join(["card ca", "`column` co"])
            ->on(["ca.Column" => "co.ID"])
            ->where(["ca.Title" => $card->get_title(), "co.Board" => $card->get_board_id()])
            ->count()->get();
        return self::count($sql, $params) == 0;

    }

    public static function validate(Card $card, $update=false): array {
        $valid = (new CardValidation(static::class))->validate($card, $update);
        return $valid->get_errors();
    }
}