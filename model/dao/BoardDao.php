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
                ->where(["c.Collaborator" => $user->get_id(), "b.Owner" => $user->get_id()], ["!=", "!="])->sql();

        return self::get_many($sql, $params);
    }

    public static function from_query($data): Board {
        return new Board(
            $data["Title"],
            UserDao::get_by_id($data["Owner"]),
            $data["ID"],
            DateUtils::php_date($data["CreatedAt"]),
            DateUtils::php_date($data["ModifiedAt"])
        );
    }

    protected static function get_object_map(Board $board): array {
        return array (
            "Title" => $board->get_title(),
            "Owner" => $board->get_owner_id(),
            "ModifiedAt" => DateUtils::sql_date($board->get_modifiedAt())
        );
    }

    public static function is_title_unique(Board $board): bool {
        $sql = new SqlGenerator(self::tableName);
        list($sql, $params) = $sql->select()
            ->where(["Title" => $board->get_title()])
            ->count()->sql();
        return self::count($sql, $params) == 0;

    }

    public static function validate(Board $board, $update=false): array {
        return self::validate_title($board, $update);
    }
}