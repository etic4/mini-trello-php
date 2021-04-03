<?php

require_once "autoload.php";

class CommentDao extends BaseDao {
    protected const tableName = "`comment`";

    // renvoie un tableau de comment associé à la carte $card
    public static function get_comments(Card $card): array {
        $sql = new SqlGenerator(self::tableName);

        list($sql, $params) = $sql->select()->where(["Card" => $card->get_id()])
            ->order_by(["ModifiedAt DESC", "CreatedAt DESC"])->get();

        return self::get_many($sql, $params);
    }

    public static function comments_count($card): int {
        $sql = new SqlGenerator(self::tableName);

        list($sql, $params) = $sql->select()->where(["Card"  => $card->get_id()])->count()->get();

        return self::count($sql, $params);
    }

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