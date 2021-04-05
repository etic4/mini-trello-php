<?php

require_once "autoload.php";

class ColumnDao extends BaseDao {
    protected const tableName = "`column`";

    public static function get_columns(Board $board): array {
        $sql = new SqlGenerator(static::tableName);
        list($sql, $params) = $sql->select()->where(["Board" => $board->get_id()])->order_by(["Position" => "ASC"])->get();

        return self::get_many($sql, $params);
    }

    public static function decrement_following_columns_position(Column $column): void {
        $sql = "UPDATE `column` 
                SET Position = Position - 1
                WHERE Board=:board 
                AND Position >:pos";
        $params = array( "board" => $column->get_board_id(), "pos" => $column->get_position());
        self::execute($sql,$params);
    }



    public static function delete(Column $column) {
        foreach ($column->get_cards() as $card) {
            CardDao::delete($card);
        }
        ColumnDao::delete_one($column);
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
            "ModifiedAt" => DateUtils::sql_date($object->get_createdAt()),
        );
    }

    public static function validate(Column $column, $update=false): array {
        return self::validate_title($column, $update);
    }
}