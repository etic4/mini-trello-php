<?php

require_once "autoload.php";

class ColumnDao extends BaseDao {
    protected const tableName = "`column`";

    public static function delete(Column $column) {
        foreach ($column->get_cards() as $card) {
            CardDao::delete($card);
        }
        ColumnDao::delete_one($column);
    }

    public static function get_columns(Board $board): array {
        $sql = new SqlGenerator(static::tableName);
        list($sql, $params) =
            $sql->select()
            ->where(["Board" => $board->get_id()])->order_by(["Position" => "ASC"])->sql();

        return self::get_many($sql, $params);
    }

    public static function decrement_following_columns_position(Column $column): void {
        $sql = new SqlGenerator(self::tableName);
        list($sql, $params) =
            $sql->update()
            ->set([], ["Position" => "Position -1"])
            ->where(["`Board`" => $column->get_board_id(), "Position" => $column->get_position()])->sql();

    }

    // TODO: implémenter batch update
    public static function update_columns_position(array $columns_id) {
        foreach ($columns_id as $data) {
            $sql = new SqlGenerator(self::tableName);
            list($sql, $params) =
                $sql->update()
                ->set(["Position" => $data["column_position"]])
                ->where(["ID" => $data["column_id"]])->sql();
            self::execute($sql, $params);
        }
    }

    public static function from_query($data) :Column {
        return new Column(
            $data["Title"],
            $data["Position"],
            BoardDao::get_by_id($data["Board"]),
            $data["ID"],
            DateUtils::php_date($data["CreatedAt"]),
            DateUtils::php_date($data["ModifiedAt"])
        );
    }

    protected static function get_object_map($object): array {
        return array(
            "Title" => $object->get_title(),
            "Position" => $object->get_position(),
            "Board" => $object->get_board_id(),
            "ModifiedAt" => DateUtils::sql_date($object->get_modifiedAt()),
        );
    }

    public static function is_title_unique(string $title, Board $board): bool {
        $sql = new SqlGenerator(self::tableName);
        list($sql, $params) =
            $sql->select()
            ->where(["Title" => $title, "Board" => $board->get_id()])
            ->count()->sql();
        return self::count($sql, $params) == 0;
    }
}