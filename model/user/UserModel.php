<?php

require_once "model/DBTools.php";
require_once "User.php";

abstract class UserModel extends Model {

    public static function get_by_id($id) {
        $sql = "SELECT * FROM user WHERE ID=:id";
        $query = self::execute($sql, array("id"=>$id));

        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return null;
        } else {
            $registeredAt = DBTools::php_date($data["RegisteredAt"]);
            return new User($data["Mail"], $data["FullName"], $data["Password"], $data["ID"],  $registeredAt);
        }
    }

    public static function get_by_email($email): ?User {
        $sql = "SELECT * FROM user WHERE Mail=:email";
        $query = self::execute($sql, array("email"=>$email));

        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return null;
        } else {
            $registeredAt = DBTools::php_date($data["RegisteredAt"]);
            return new User($data["Mail"], $data["FullName"], $data["Password"], $data["ID"],  $registeredAt);
        }
    }

    public function insert() {
        $sql = "INSERT INTO user(Mail, FullName, Password) VALUES(:email, :fullName, :passwdHash)";
        $params = array("email"=>$this->get_email(), "fullName"=>$this->get_fullName(),"passwdHash"=>$this->get_passwdHash());
        $this->execute($sql, $params);

        return $this->get_by_id($this->lastInsertId());
    }

    public function update() {
        $sql = "UPDATE user SET Mail=:email, FullName=:fullName, Password=:passwdHash WHERE ID=:id";
        $params = array("id"=>$this->get_id(), "email"=>$this->get_email(), "fullName"=>$this->get_fullName(),
            "passwdHash"=>$this->get_passwdHash());
        $this->execute($sql, $params);
    }

    public function delete() {
        $sql = "DELETE FROM user WHERE ID = :id";
        $params = array("id"=>$this->get_id());
        $this->execute($sql, $params);
    }
}