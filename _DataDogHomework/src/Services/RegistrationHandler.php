<?php

namespace src\Services;

use src\Models\Database;

class RegistrationHandler
{
    public function __construct(Database $database)
    {
        Database::connect();
    }
    public function confirmRegistration()
    {
        if($this->checkForEmptyFields())
        {
            echo "All fields are required.";
        }
        elseif(Database::userExists($_POST['username']))
        {
            echo "User with this username already exists.";
        }
        elseif(!$this->checkIfPasswordsMatch($_POST['password'], $_POST['password1']))
        {
            echo "Passwords should match.";
        }
        else
        {
            Database::addUser($_POST['username'], $this->hashPassword($_POST['password']));
            Database::linkProfileToUser($_POST['username']);
            return true;
        }
        return false;
    }
    public function checkForEmptyFields()
    {
        $required = array('username', 'password', 'password1');
        foreach($required as $field)
        {
            if(empty($_POST[$field]))
            {
                return true;
            }
        }
        return false;
    }
    public function checkIfPasswordsMatch($password1, $password2)
    {
        return $password1 === $password2;
    }
    public function hashPassword($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }


}