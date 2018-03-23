<?php

namespace src\Services;

use src\Models\Database;

class Authenticator
{
    public function __construct()
    {
        Database::connect();
    }

    public function authenticate($username, $password)
    {
        $user = $this->verifyUser($username, $password);

        if(!isset($user)){
            unset($_SESSION['user']);
            throw new \Exception('Invalid login details');
        }else{
            $_SESSION['user'] = $user;
            return true;
        }
    }

    public function verifyUser($username, $password)
    {
        $query = "SELECT username, password FROM users 
                  WHERE username= BINARY '$username'";

        $result = Database::query($query);

        if(mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);
            return $this->verifyPassword($row['password'], $password) == true ? $row : null;
        }
        return null;

    }

    public function verifyPassword($db_password, $password)
    {
        return password_verify($password, $db_password) || $db_password === $password;
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