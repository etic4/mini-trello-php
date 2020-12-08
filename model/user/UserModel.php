<?php

require_once "model/BaseModel.php";
require_once "User.php";

abstract class UserModel extends BaseModel {
    public static function get_by_email($email): ?User {
        $sql = "SELECT * FROM user WHERE Mail=:email";
        $query = self::execute($sql, array("email"=>$email));

        return self::fetch_one_and_get_instance($query);
    }

    protected function prepare_insert(): array {
        $sql = "INSERT INTO user(Mail, FullName, Password) VALUES(:email, :fullName, :passwdHash)";
        $params = array("email"=>$this->get_email(), "fullName"=>$this->get_fullName(),"passwdHash"=>$this->get_passwdHash());
        return array("sql"=>$sql, "params"=>$params);
    }

    protected function prepare_update(): array {
        $sql = "UPDATE user SET Mail=:email, FullName=:fullName, Password=:passwdHash WHERE ID=:id";
        $params = array("id"=>$this->get_id(), "email"=>$this->get_email(), "fullName"=>$this->get_fullName(),
            "passwdHash"=>$this->get_passwdHash());
        return array("sql"=>$sql, "params"=>$params);
    }

    protected static function get_instance($data): User {
        $registeredAt = self::php_date($data["RegisteredAt"]);
        return new User($data["Mail"], $data["FullName"], $data["Password"], $data["ID"],  $registeredAt);
    }

}