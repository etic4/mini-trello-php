<?php

require_once "autoload.php";

class ColumnDao extends BaseDao {
    protected const tableName = "`column`";
    protected const FKName = "`Column`";


    public static function get_columns_for_board(Board $board): array {
        $sql = new SqlGenerator(static::tableName);
        list($sql, $params) = $sql->select()->where(["Board" => $board->get_id()])->order_by(["Position ASC"])->get();

        return self::get_many($sql, $params);
    }

    public static function cascade_delete($column) {
        CardDao::delete_all([self::FKName => $column->get_id()]);
        ColumnDao::delete($column);
    }

    public static function from_query($data) :Column {
        return new Column(
            $data["Title"],
            $data["Position"],
            BoardDao::get_by_id($data["Board"]),
            $data["ID"],
            self::php_date($data["CreatedAt"]),
            self::php_date($data["ModifiedAt"])
        );
    }

    protected static function get_object_map($object): array {
        return array(
            "Title" => $object->get_title(),
            "Position" => $object->get_position(),
            "Board" => $object->get_board_id(),
            "ID" => $object->get_id(),
            "ModifiedAt" => self::sql_date($object->get_createdAt()),
        );
    }

}