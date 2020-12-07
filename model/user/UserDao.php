<?php

require_once "../Dao.php";

class UserDao extends Dao {
    protected $tableName = "user";

    public function get_by_email($email): ?User {
        $sql = "SELECT * FROM user WHERE Mail=:email";
        $query = $this->execute($sql, array("email"=>$email));

        return $this->fetch_one_and_get_instance($query);
    }

    protected function prepare_insert($object): array {
        $sql = "INSERT INTO user(Mail, FullName, Password, RegisteredAt) VALUES(:email,:fullName,:passwdHash,:registeredAt)";
        $datetime = $this->sql_date($object->registeredAt);
        $params = array("email"=>$object->email, "fullName"=>$object->fullName,"passwdHash"=>$object->passwdHash,
            "registeredAt"=>$datetime);
        return array("sql"=>$sql, "params"=>$params);
    }

    protected  function prepare_update($object): array {
        $registeredAt = $this->sql_date($object->RegisteredAt);
        $sql = "UPDATE user SET Mail=:email, FullName=:fullName, Password=:passwdHash, RegisteredAt=:registeredAt WHERE ID=:id";
        $params = array("id"=>$object->id, "email"=>$object->email, "fullName"=>$object->fullName,
            "passwdHash"=>$object->passwdHash, "$registeredAt"=>$registeredAt);
        return array("sql"=>$sql, "params"=>$params);
    }

    protected function get_instance($data): User {
        $registeredAt = $this->php_date($data["RegisteredAt"]);
        return new User($data["Mail"], $data["FullName"], $data["Password"], $data["ID"],  $registeredAt);
    }

}