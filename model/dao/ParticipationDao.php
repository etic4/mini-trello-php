<?php


class ParticipationDao extends BaseDao {
    protected const PkName = null;
    protected const tableName = "`participate`";
    protected const FkName = "`ID`";


    public static function get_participating_card(User $user): array {
        $sql = new SqlGenerator(static::tableName);

        list($sql, $params) = $sql->select(["c.*"])
            ->join([static::tableName . " p", "card c"])->on(["p.Card" => "c.ID"])
            ->where(["Participant" => $user->get_id()])->get();

        $constructor = function($data) {return CardDao::from_query($data);};

        return self::get_many($sql, $params, $constructor);
    }

    public static function get_participating_users(Card $card): array {
        $sql = new SqlGenerator(static::tableName);

        list($sql, $params) = $sql->select(["u.*"])
            ->join([static::tableName . " p", "user u"])->on(["p.Participant" => "u.ID"])
            ->where(["Card" => $card->get_id()])->get();

        $constructor = function($data) {return UserDao::from_query($data);};

        return self::get_many($sql, $params, $constructor);
    }

    public static function remove(Card $card, User $participant) {
        $sql = new SqlGenerator(static::tableName);
        list($sql, $params) = $sql->delete()
            ->where(["Card" => $card->get_id(), "Participant" => $participant->get_id()])->get();
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