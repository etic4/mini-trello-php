<?php


class UserValidation extends Validation {
    const UserDao = "UserDao";

    public static function get_inst() {
        return new UserValidation();
    }

    public function validate_signup($email, $fullName, $password, $password_confirm): array {
        $this->validate_datas($fullName, $email);
        $this->validate_password($password, $password_confirm);
        return $this->get_errors();
    }


    public function validate_add(string $fullName, string $email): array {
        $this->validate_datas($fullName, $email);
        return $this->get_errors();
    }

    public function validate_edit(string $fullName, string $email, $user): array {
        $this->validate_datas($fullName, $email, $user);
        return $this->get_errors();
    }

    public function validate_login(string $email, string $password): array {
        if (empty($email) || $email == Configuration::get("anonyme")) {
            $this->errors[] = "Invalid username or password";
        } else {
            $user = UserDao::get_by_email($email);
            if ($user == null || $this->wrong_password( $user, $password)) {
                $this->errors[] = "Invalid username or password";
            }
        }

        return $this->get_errors();
    }

    public function validate_password(string $password, string $password_confirm): array {
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

    }

    private function wrong_password(User $user, string $clearPasswd): bool {
        return $user->get_passwdHash() != Tools::my_hash($clearPasswd);
    }

    private function validate_datas(string $fullName, string $email, $user=null) {
        if (!self::valid_email($email)) {
            $this->errors[] = "Invalid email";
        }

        if (is_null($user) || $user->get_email() != $email) {
            if (!UserDao::is_email_unique($email)){
                $this->errors[] = "A user with the same email already exists";
            }
        }

        if (self::str_lower_than($fullName, 3)) {
            $this->errors[] = "Name must be at least 3 characters long";
        }
    }
}