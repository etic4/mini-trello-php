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

    public static function get_collaborating_users(Board $board): array {
        $sql = new SqlGenerator(static::tableName);

        list($sql, $params) = $sql->select(["u.*"])
            ->join([static::tableName . " c", "user u"])->on(["c.Collaborator" => "u.ID"])
            ->where(["Board" => $board->get_id()])->get();

        $constructor = function($data) {return UserDao::from_query($data);};

        return self::get_many($sql, $params, $constructor);
    }


    public static function remove(Board $board, User $collaborator) {
        $sql = new SqlGenerator(static::tableName);
        list($sql, $params) = $sql->delete()->where(["Board" => $board->get_id(), "Collaborator" => $collaborator->get_id()]);
        self::execute($sql, $params);

    }

    public static function from_query($data, $class=null): Collaboration {
        return new Collaboration(
            BoardDao::get_by_id($data["Board"]),
            UserDao::get_by_id($data["Collaborator"])
        );
    }

    protected static function get_object_map($object): array {
        return array(
            "Board" => $object->get_boardId(),
            "Collaborator" => $object->get_collaboratorId(),
        );
    }
}