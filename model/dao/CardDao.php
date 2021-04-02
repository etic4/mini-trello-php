<?php

require_once "autoload.php";

class CardDao extends BaseDao {
    protected const tableName = "`card`";
    protected const FKName = "`Author`";


    public static function cascade_delete($card) {
        ColumnDao::delete_all([self::FKName => $card->get_id()]);
        CardDao::delete($card);
    }

    public static function title_count(Card $card): int {
        $sql = new SqlGenerator(self::tableName);
        list($sql, $params) = $sql->select()
                                  ->where(["Title" => $card->get_title(), "Board" => $card->get_board_id()])
                                  ->count()->get();
        return self::count($sql, $params);
    }

    public static function get_participating_cards(User $user, Board $board): array {
        $sql = new SqlGenerator();

        list($sql, $params) =
            $sql->select(["ca.*"])->join(["participate pa", "user us", "card ca, `column` co", "board bo"])
            ->on(["pa.Participant" => "us.ID", "pa.Card" => "ca.ID", "ca.Column" => "co.ID", "co.Board" => "bo.ID" ])
            ->where(["pa.Participant" => $user->get_id(), "bo.ID" => $board->get_id()])->get();

        $constructor = function($data) {return CardDao::from_query($data);};

        return self::get_many($sql, $params, $constructor);
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