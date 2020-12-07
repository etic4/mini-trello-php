<?php

require_once "model/Dao.php";
require_once "User.php";

class UserDao extends Dao {
    protected $tableName = "user";

    public function get_by_email($email): ?User {
        $sql = "SELECT * FROM user WHERE Mail=:email";
        $query = $this->execute($sql, array("email"=>$email));

        return $this->fetch_one_and_get_instance($query);
    }

    protected function prepare_insert($object): array {
        $sql = "INSERT INTO user(Mail, FullName, Password) VALUES(:email, :fullName, :passwdHash)";
        $params = array("email"=>$object->get_email(), "fullName"=>$object->get_fullName(),"passwdHash"=>$object->get_passwdHash());
        return array("sql"=>$sql, "params"=>$params);
    }

    protected  function prepare_update($object): array {
        $sql = "UPDATE user SET Mail=:email, FullName=:fullName, Password=:passwdHash WHERE ID=:id";
        $params = array("id"=>$object->get_id(), "email"=>$object->get_email(), "fullName"=>$object->get_fullName(),
            "passwdHash"=>$object->get_passwdHash());
        return array("sql"=>$sql, "params"=>$params);
    }

    protected function get_instance($data): User {
        $registeredAt = $this->php_date($data["RegisteredAt"]);
        return new User($data["Mail"], $data["FullName"], $data["Password"], $data["ID"],  $registeredAt);
    }

}