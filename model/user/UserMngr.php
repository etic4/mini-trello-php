<?php

require_once "UserDao.php";

class UserMngr {
    private $dao;

    public function __construct() {
        $this->dao = new UserDao();
    }

    public function new($email, $fullName, $clearPasswd): User {
        return new User(null, $email, $fullName, null, null, $clearPasswd);
    }

    public function get_by_email($email): ?User {
        return $this->dao->get_by_email($email);
    }

    public function get_boards($user): BoardMngr {
        return new BoardMngr($user);
    }

    public function add($user): User {
        return $this->dao->insert($user);
    }

    public function update($user) {
        $this->dao->update($user);
    }

    public function delete($user) {
        $this->dao->delete($user);
    }


}