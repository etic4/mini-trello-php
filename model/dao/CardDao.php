<?php

require_once "autoload.php";

class CardDao extends BaseDao {
    protected const tableName = "`card`";

    public static function delete(Card $card) {
        ParticipationDao::delete_all(["Card" => $card->get_id()]);

        foreach ($card->get_comments() as $comment) {
            CommentDao::delete($comment);
        }

        CardDao::delete_one($card);
    }

    //renvoie un tableau de cartes triées par leur position dans la colonne dont la colonne est $column;
    public static function get_cards(Column $column): array {
        $sql = new SqlGenerator(self::tableName);
        list($sql, $params) =
            $sql->select()
            ->where(["`Column`" => $column->get_id()])->order_by(["Position" => "ASC"])->sql();
        return self::get_many($sql, $params);
    }

    // attribue les cartes de cet utilisateur à utilisateur 'Anonyme', dont l'ID est '6'
    // utilisé lors de la suppression d'un utilisateur
    public static function to_anonymous(User $user, string $anonID="6") {
        $sql = new SqlGenerator(self::tableName);
        list($sql, $params) =
            $sql->update()
            ->set(["NewAuthor" => $anonID], ["Author" => "NewAuthor"])
            ->where(["Author" => $user->get_id()])->sql();
        self::execute($sql, $params);
    }

    // Mets a jour la position des autres cartes de la colonne après suppression ou déplacement
    public static function decrement_following_cards_position($card){
        // TODO: Debug ça. Je ne comprend pas bien ce qu'il se passe
        // Hypothèse pour l'instant: problème d'interpolation -> tente de modifier toutes les positions après column.id

/*        $sql = new SqlGenerator(self::tableName);
        list($sql, $params) =
            $sql->update()
            ->set(["new_position" => "Position - 1"], ["Position" => "new_position"])
            ->where([
                "`Column`" => $card->get_column_id(),
                "Position" => $card->get_position()
            ],
            ["=", ">"])->sql();
        self::execute($sql, $params);*/

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

    // TODO: implémenter batch update
    public static function update_cards_position(array $cards_positions) {
        foreach ($cards_positions as $data) {
            $sql = new SqlGenerator(self::tableName);
            list($sql, $params) =
                $sql->update()
                    ->set(["Position" => $data["card_position"], "`Column`" => $data["column_id"]])
                    ->where(["ID" => $data["card_id"]])->sql();
            try {
                self::execute($sql, $params);
            } catch (Exception $e) {
                echo "foiré";
            }

        }
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

    public static function is_title_unique(string $title, Board $board): bool {
        $sql = new SqlGenerator(self::tableName);
        list($sql, $params) = $sql->select()
            ->join(["card ca", "`column` co"])
            ->on(["ca.Column" => "co.ID"])
            ->where(["ca.Title" => $title, "co.Board" => $board->get_id()])
            ->count()->sql();

        return self::count($sql, $params) == 0;
    }
}