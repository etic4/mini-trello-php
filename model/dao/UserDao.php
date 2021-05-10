<?php

require_once "autoload.php";

class UserDao extends BaseDao {
    protected const tableName = "`user`";

    public static function delete(User $user) {
        CollaborationDao::delete_all(["Collaborator" => $user->get_id()]);
        ParticipationDao::delete_all(["Participant" => $user->get_id()]);

        foreach ($user->get_own_boards() as $board) {
            BoardDao::delete($board);
        }

        // attribue toutes les cartes créées par user à "Anonyme"
        CardDao::to_anonymous($user);
        CommentDao::to_anonymous($user);

        UserDao::delete_one($user);
    }

    public static function get_all_users() {
        $all_users = self::get_all(null, ["FullName" => "ASC"]);
        return array_filter($all_users, fn($user) => $user->get_id() != "6");
    }

    public static function get_by_email(string $email) {
        return self::get_by(["Mail" => $email]);
    }

    public static function email_has_changed(User $user): bool {
        $sql = new SqlGenerator(static::tableName);

        list($sql, $params) = $sql->select() ->where([self::PkName => $user->get_id()])->sql();
        $stored = self::get_one($sql, $params, $cache=false);

        return $stored->get_email() != $user->get_email();
    }

    public static function is_email_unique(string $email): bool {
        return self::is_unique("Mail", $email);
    }

    public static function from_query($data): User {
        return new User(
            $data["Mail"],
            $data["FullName"],
            $data["Role"],
            null,
            $data["ID"],
            $data["Password"],
            new DateTime($data["RegisteredAt"])
        );
    }

    protected static function get_object_map($object): array {
        return array(
            "Mail" => $object->get_email(),
            "FullName" => $object->get_fullName(),
            "Role" => $object->get_role(),
            "Password" => $object->get_passwdHash()
        );
    }

    // --- Validation ---

}