<?php


namespace Models;

class Users implements UsersInterface {

    public static function logIn(string $email, string $password)
    {
        $user = Table::select('users', ['*'], ['email' => $email]);
        if(!empty($user)) {
            if (password_verify($password, $user->password)) {
                unset($user->password);
                $key = hash('sha256', bin2hex(random_bytes(32)));
                Table::insert('session', [
                    'user_id' => $user->id,
                    'ip' => $_SERVER['REMOTE_ADDR'],
                    'useragent' => $_SERVER['HTTP_USER_AGENT'],
                    'token' => $key]);
                $_SESSION['user'] = $user;
                setcookie('hsk-online', $key, time() + (60 * 60 * 24 * 7), '/', null, false, true);
                return true;
            }
        }
        return false;
    }

    public static function logOut()
    {
        unset($_SESSION['user']);
        session_destroy();
        return true;
    }

    public static function register(string $email, string $password, string $password2, string $name)
    {
        if ($password === $password2) {
           $hashed = password_hash($password, PASSWORD_DEFAULT);
           return Table::insert('users', ['email' => $email, 'password' => $hashed, 'name' => $name]);
        }
        return false;
    }

    public static function isAuthenticated()
    {
        return session_id() !== '' && !empty($_SESSION['user']);
    }

    public static function isAdmin()
    {
        if ($_SESSION['user']->type == 2) {
            return true;
        } else {
            return false;
        }

    }


}
