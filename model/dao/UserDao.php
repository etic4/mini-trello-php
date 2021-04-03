<?php

require_once "autoload.php";

class UserDao extends BaseDao {
    protected const tableName = "`user`";
    protected const FKName = "`Owner`";

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

    public static function get_by_email(string $email) {
        return self::get_by(["Mail" => $email]);
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
    
}