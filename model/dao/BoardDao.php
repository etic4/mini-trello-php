<?php

require_once "autoload.php";

class BoardDao extends BaseDao {
    protected const tableName = "`board`";
    protected const FKName = "`Board`";

    public static function delete(Board $board) {
        CollaborationDao::delete_all([self::FKName => $board->get_id()]);

        foreach ($board->get_columns() as $col) {
            ColumnDao::delete($col);
        }

        BoardDao::delete_one($board);
    }

    public static function get_by_title(string $title): ?Board {
        return BoardDao::get_by(["Title" => $title]);
    }

    public static function get_users_boards(User $user): array {
        return BoardDao::get_all(["Owner" => $user->get_id()]);
    }

    public static function get_admin_visible_boards(User $user): array {
        $sql = new SqlGenerator();
        list($sql, $params) =
            $sql->select(["b.*"], $distinct=true)->join(["board b", "collaborate c"])
                ->where(["c.Collaborator" => $user->get_id(), "b.Owner" => $user->get_id()], ["!=", "!="])->get();

        return self::get_many($sql, $params);
    }

    public static function from_query($data): Board {
        return new Board(
            $data["Title"],
            UserDao::get_by_id($data["Owner"]),
            $data["ID"],
            self::php_date($data["CreatedAt"]),
            self::php_date($data["ModifiedAt"])
        );
    }

    protected static function get_object_map(Board $board): array {
        return array (
            "Title" => $board->get_title(),
            "Owner" => $board->get_owner_id(),
            "ID" => $board->get_id(),
            "ModifiedAt" => self::sql_date($board->get_modifiedAt())
        );
    }
}