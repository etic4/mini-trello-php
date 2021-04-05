<?php

require_once "autoload.php";

class CommentDao extends BaseDao {
    protected const tableName = "`comment`";

    public static function delete(Comment $comment) {
        self::delete_one($comment);
    }

    // renvoie un tableau de comment associé à la carte $card
    public static function get_comments(Card $card): array {
        $sql = new SqlGenerator(self::tableName);

        list($sql, $params) = $sql->select()->where(["Card" => $card->get_id()])
            ->order_by(["ModifiedAt" => "DESC", "CreatedAt" => "DESC"])->get();

        return self::get_many($sql, $params);
    }

    public static function comments_count($card): int {
        $sql = new SqlGenerator(self::tableName);

        list($sql, $params) = $sql->select()->where(["Card"  => $card->get_id()])->count()->get();

        return self::count($sql, $params);
    }

    // attribue les commentaires de cet utilisateur à utilisateur 'Anonyme' dont l'ID est '6'
    // utilisé lors de la suppression d'un utilisateur
    public static function to_anonymous(User $user, string $anonID="6") {
        $sql = new SqlGenerator(self::tableName);
        list($sql, $params) =
            $sql->update()->set(["NewAuthor" => $anonID], ["Author" => "NewAuthor"])
                ->where(["Author" => $user->get_id()])->get();
        self::execute($sql, $params);
    }


    public static function from_query($data): Comment {
        return new Comment(
            $data["Body"],
            UserDao::get_by_id($data["Author"]),
            CardDao::get_by_id($data["Card"]),
            $data["ID"],
            DateUtils::php_date($data["CreatedAt"]),
            DateUtils::php_date($data["ModifiedAt"])
        );
    }

    protected static function get_object_map($object): array {
        return array(
            "Body" => $object->get_body(),
            "Author" => $object->get_author()->get_id(),
            "Card" => $object->get_card()->get_id(),
            "ModifiedAt" => DateUtils::sql_date($object->get_createdAt()),
        );
    }

}