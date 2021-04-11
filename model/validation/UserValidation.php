<?php


class UserValidation extends Validation {
    private string $dao;

    public function __construct($dao) {
        $this->dao = $dao;
    }

    public function validate_datas(User $user, $update=false): UserValidation {
        if (!self::valid_email($user->get_email())) {
            $this->errors[] = "Invalid email";
        }

        if (!$update || $this->dao::email_has_changed($user)) {
            if (!$this->dao::is_email_unique($user->get_email())){
                $this->errors[] = "A user with the same email already exists";
            }
        }

        if (self::str_lower_than($user->get_fullName(), 3)) {
            $this->errors[] = "Name must be at least 3 characters long";
        }

        return $this;
    }

    public function validate_password(string $password, string $password_confirm): UserValidation {
        if (self::str_lower_than($password, 8)) {
            $this->errors[] = "Password must be at least 8 characters long";
        }

        if (self::doesnt_contains_capitals($password)) {
            $this->errors[] = "Password must contain at least 1 uppercase letter";
        }

        if (self::doesnt_contains_digits($password)) {
            $this->errors[] = "Password must contain at least 1 number";
        }

        if (self::doesnt_contains_non_alpha($password)) {
            $this->errors[] = "Password must contain at least one special character";
        }

        if (self::strings_not_equals($password, $password_confirm)) {
            $this->errors[] = "Passwords don't match";
        }

        return $this;
    }


    public function validate_login(string $email, string $password): UserValidation {
        if (empty($email) || $email == Configuration::get("anonyme")) {
            $this->errors[] = "Invalid username or password";
        } else {
            $user = UserDao::get_by_email($email);
            if ($user == null || $this->wrong_password( $user, $password)) {
                $this->errors[] = "Invalid username or password";
            }
        }

        return $this;
    }

    private function wrong_password(User $user, string $clearPasswd): bool {
        return $user->get_passwdHash() != Tools::my_hash($clearPasswd);
    }
}