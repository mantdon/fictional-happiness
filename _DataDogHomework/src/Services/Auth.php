<?php

namespace src\Services;


use src\Models\Database;
use src\Models\User;

class Auth
{
    private $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function connect($username, $password)
    {
        return $this->authenticate($username, $password);
    }

    public function authenticate($username, $password)
    {
        $user = $this->database->verifyUser($username, $password);

        if(!isset($user))
        {
            unset($_SESSION['user']);
            throw new \Exception('Invalid login details');
        } else {
            $_SESSION['user'] = $user;
            return true;
        }
        return false;
    }

    public function isAuthorized()
    {
        $user = $_SESSION['user'] ?? null;
        if(!isset($user))
            return false;

        $this->authenticate($user['username'], $user['password']);
        return true;
    }
}