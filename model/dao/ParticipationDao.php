<?php


class ParticipationDao extends BaseDao {
    protected const PkName = null;
    protected const tableName = "`participate`";
    protected const FkName = "`ID`";

    public static function get_participating_card(User $user): array {
        $sql = new SqlGenerator(static::tableName);

        list($sql, $params) =
            $sql->select(["c.*"])
            ->join([static::tableName . " p", "card c"])->on(["p.Card" => "c.ID"])
            ->where(["Participant" => $user->get_id()])->sql();

        return self::get_many($sql, $params, fn($data) => CardDao::from_query($data));
    }

    public static function get_participations_count_in_board(User $user, Board $board): int {
        $sql = new SqlGenerator(static::tableName);

        list($sql, $params) =
            $sql->select(["p.*"])
            ->join([static::tableName . " p", "card ca", "`column` co"])
            ->on(["p.Card" => "ca.ID", "ca.Column" => "co.ID"])
            ->where(["p.Participant" => $user->get_id(), "co.Board" => $board->get_id()])->count()->sql();

        return self::count($sql, $params);
    }

    public static function get_participating_users(Card $card): array {
        $sql = new SqlGenerator(static::tableName);

        list($sql, $params) =
            $sql->select(["u.*"])
            ->join([static::tableName . " p", "user u"])->on(["p.Participant" => "u.ID"])
            ->where(["Card" => $card->get_id()])->sql();

        return self::get_many($sql, $params, fn($data) => UserDao::from_query($data));
    }

    public static function remove(Card $card, User $participant) {
        $sql = new SqlGenerator(static::tableName);
        list($sql, $params) =
            $sql->delete()
            ->where(["Card" => $card->get_id(), "Participant" => $participant->get_id()])->sql();
        self::execute($sql, $params);
    }

    public static function remove_all(User $user, Board $board) {
        $sql = new SqlGenerator(static::tableName);
        $table = static::tableName;
        list($sql, $params) =
            $sql->delete("participate")
            ->join([static::tableName, "card ca", "`column` co"])
            ->on(["$table.Card" => "ca.ID", "ca.Column" => "co.ID"])
            ->where(["$table.Participant" => $user->get_id(), "co.Board" => $board->get_id()])->sql();

        self::execute($sql, $params);
    }

    public static function from_query($data): Collaboration {
        return new Collaboration(
            CardDao::get_by_id($data["Card"]),
            UserDao::get_by_id($data["Participant"])
        );
    }

    protected static function get_object_map($object): array {
        return array(
            "Card" => $object->get_cardId(),
            "Participant" => $object->get_participantId(),
        );
    }
}