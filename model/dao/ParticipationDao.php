<?php


class ParticipationDao extends BaseDao {
    protected const PkName = null;
    protected const tableName = "`participate`";
    protected const FkName = "`ID`";




    public static function from_query($data): Collaboration {
        return new Collaboration(
            $data["Card"],
            $data["Participant"]
        );
    }

    protected static function get_object_map($object): array {
        return array(
            "Card" => $object->get_cardId(),
            "Participant" => $object->get_participantId(),
        );
    }
}