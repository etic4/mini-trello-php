<?php

require_once "autoload.php";

class CardDao extends BaseDao {
    protected const tableName = "`card`";


    //renvoie un tableau de cartes triées par leur position dans la colonne dont la colonne est $column;
    public static function get_cards(Column $column): array {
        $sql = new SqlGenerator(self::tableName);
        list($sql, $params) = $sql->select()->where(["`Column`" => $column->get_id()])->order_by(["Position"]);
        return self::get_many($sql, $params);
    }

    public static function cascade_delete(Card $card) {
        ParticipationDao::delete_all(["Card" => $card->get_id()]);

        foreach ($card->get_comments() as $comment) {
            CommentDao::delete($comment);
        }

        CardDao::delete($card);
    }

    // En cas d'update, faut récupérer la ligne sans la mettre en cache
    public static function title_has_changed(Card $card): bool {
        $sql = new SqlGenerator(self::tableName);

        list($sql, $params) = $sql->select() ->where(["ID" => $card->get_id()])->get();
        $stored = self::get_one($sql, $params, $cache=false);

        return $stored->get_title() != $card->get_title();
    }

    public static function get_participating_cards(User $user, Board $board): array {
        $sql = new SqlGenerator();

        list($sql, $params) =
            $sql->select(["ca.*"])->join(["participate pa", "user us", "card ca, `column` co", "board bo"])
            ->on(["pa.Participant" => "us.ID", "pa.Card" => "ca.ID", "ca.Column" => "co.ID", "co.Board" => "bo.ID" ])
            ->where(["pa.Participant" => $user->get_id(), "bo.ID" => $board->get_id()])->get();

        return self::get_many($sql, $params);
    }


    // attribue les cartes de cet utilisateur à utilisateur anonyme
    // utilisé lors de la suppression d'un utilisateur
    public static function to_anonymous(User $user) {
        $sql = new SqlGenerator(self::tableName);
        list($sql, $params) = $sql->update(["ID" => "6"])->where(["ID" => $user->get_id()])->get();
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
            self::php_date($data["DueDate"]),
            $data["ID"],
            self::php_date($data["CreatedAt"]),
            self::php_date($data["ModifiedAt"])
        );
    }

    protected static function get_object_map($object): array {
        return array(
            "Title" => $object->get_title(),
            "Body" => $object->get_body(),
            "Position" => $object->get_position(),
            "Author" => $object->get_author()->get_id(),
            "`Column`" => $object->get_column()->get_id(),
            "DueDate" => $object->get_dueDate(),
            "ID" => $object->get_id(),
            "ModifiedAt" => self::sql_date($object->get_createdAt()),
        );
    }
}