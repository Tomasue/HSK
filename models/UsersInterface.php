<?php

namespace Models;

interface UsersInterface {

    public static function logIn(string $email, string $password);
    public static function logOut();
    public static function register(string $email, string $password, string $password2, string $name);
    public static function isAuthenticated();
    public static function isAdmin();
}