<?php

require_once "autoload.php";

class CommentDao extends BaseDao {
    protected const tableName = "`comment`";
    protected const FKName = "`Comment`";

    public static function from_query($data): Comment {
        return new Comment(
            $data["Body"],
            UserDao::get_by_id($data["Author"]),
            CardDao::get_by_id($data["Card"]),
            $data["ID"],
            self::php_date($data["CreatedAt"]),
            self::php_date($data["ModifiedAt"])
        );
    }

    protected static function get_object_map($object): array {
        return array(
            "Body" => $object->get_body(),
            "Author" => $object->get_author()->get_id(),
            "Card" => $object->get_card()->get_id(),
            "ID" => $object->get_id(),
            "ModifiedAt" => self::sql_date($object->get_createdAt()),
        );
    }

}