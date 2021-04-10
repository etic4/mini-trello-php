<?php


class CollaborationDao extends BaseDao {
    protected const PkName = null;
    protected const tableName = "`collaborate`";
    protected const FkName = "`ID`";


    public static function get_collaborating_boards(User $user): array {
        $sql = new SqlGenerator(static::tableName);
        list($sql, $params) = $sql->select(["b.*"])
                                ->join([static::tableName . " c", "board b"])->on(["c.Board" => "b.ID"])
                                ->where(["Collaborator" => $user->get_id()])->sql();

        return self::get_many($sql, $params, fn($data) => BoardDao::from_query($data));
    }

    public static function get_collaborating_users(Board $board): array {
        $sql = new SqlGenerator(static::tableName);

        list($sql, $params) = $sql->select(["u.*"])
            ->join([static::tableName . " c", "user u"])->on(["c.Collaborator" => "u.ID"])
            ->where(["Board" => $board->get_id()])->sql();

        return self::get_many($sql, $params, fn($data) => UserDao::from_query($data));
    }


    public static function remove(Board $board, User $collaborator) {
        $sql = new SqlGenerator(static::tableName);
        list($sql, $params) = $sql->delete()
            ->where(["Board" => $board->get_id(), "Collaborator" => $collaborator->get_id()])->sql();
        self::execute($sql, $params);

    }

    public static function from_query($data, $class=null): Collaboration {
        return new Collaboration(
            BoardDao::get_by_id($data["Board"]),
            UserDao::get_by_id($data["Collaborator"])
        );
    }

    public static function has_collaborating_boards(User $user): bool {
        $sql = new SqlGenerator(static::tableName);
        list($sql, $params) = $sql->select()->where(["Collaborator" => $user->get_id()])->count()->sql();
        return self::count($sql, $params) > 0;

    }

    protected static function get_object_map($object): array {
        return array(
            "Board" => $object->get_boardId(),
            "Collaborator" => $object->get_collaboratorId(),
        );
    }
}