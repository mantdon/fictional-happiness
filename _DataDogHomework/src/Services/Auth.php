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
        $this->authenticate($username, $password);

        $sessionUser = new User();
        $_SESSION['user'] = serialize($sessionUser->setUsername($username)->setPlainPassword($password));
    }

    public function authenticate($username, $password)
    {
        /**
         * @var $user User
         */
        $user = $this->database->findByUsername($username);

        if(!isset($user) || !$user->verifyPassword($password))
        {
            unset($_SESSION['user']);
            throw new \Exception('Invalid login details');
        }
    }

    public function isAuthorized()
    {
        /**
         * @var $user User
         */
        $user = $_SESSION['user'] ?? null;
        if(!isset($user))
            return false;

        $user = unserialize($user);
        $this->authenticate($user->getUsername(), $user->getPassword());
        return true;
    }
}