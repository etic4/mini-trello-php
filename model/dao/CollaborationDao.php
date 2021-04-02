<?php


class CollaborationDao extends BaseDao {
    protected const PkName = null;
    protected const tableName = "`collaborate`";
    protected const FkName = "`ID`";


    public static function get_collaborating_boards(User $user): array {
        $sql = new SqlGenerator(static::tableName);
        list($sql, $params) = $sql->select(["b.*"])
                                ->join([static::tableName . " c", "board b"])->on(["c.Board" => "b.ID"])
                                ->where(["Collaborator" => $user->get_id()])->get();

        $constructor = function($data) {return BoardDao::from_query($data);};

        return self::get_many($sql, $params, $constructor);
    }


    public static function from_query($data, $class=null): Collaboration {
        return new Collaboration(
            $data["Board"],
            $data["Collaborator"]
        );
    }

    protected static function get_object_map($object): array {
        return array(
            "Board" => $object->get_boardId(),
            "Collaborator" => $object->get_collaboratorId(),
        );
    }
}