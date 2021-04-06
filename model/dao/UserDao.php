<?php

require_once "autoload.php";

class UserDao extends BaseDao {
    protected const tableName = "`user`";

    public static function get_all_users() {
        $all_users = self::get_all();
        return array_filter($all_users, fn($user) => $user->get_id() != "6");
    }

    public static function get_by_email(string $email) {
        return self::get_by(["Mail" => $email]);
    }

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

    public static function email_has_changed(User $user): bool {
        $sql = new SqlGenerator(static::tableName);

        list($sql, $params) = $sql->select() ->where([self::PkName => $user->get_id()])->get();
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

    // --- AbstractValidation ---

    public static function validate_signup(User $user, $password, $password_confirm): array {
        $valid = (new UserValidation(static::class))->validate_datas($user);
        $valid->validate_password($password, $password_confirm);
        return $valid->get_errors();
    }

    public static function validate_add(User $user): array {
        $valid = (new UserValidation(static::class))->validate_datas($user);
        return $valid->get_errors();
    }

    public static function validate_edit(User $user): array {
        $valid = (new UserValidation(static::class))->validate_datas($user, $update=true);
        return $valid->get_errors();
    }

    public static function validate_login(string $email, string $password): array {
        $valid = (new UserValidation(static::class))->validate_login($email, $password);
        return $valid->get_errors();
    }
    
}