<?php

namespace src\Services;

use src\Models\Database;

class FieldValidator{
    private static $namePattern = "/^[a-žA-Ž]+$/";
    private static $emailPattern = "/^[a-žA-Ž0-9_.+-]+@[a-žA-Ž0-9-]+\.[a-žA-Ž0-9-.]+$/";

    public function __construct(){
        Database::connect();
    }

    public function validateUsername($username){
        if((!$this->usernameIsOfCorrectFormat($username) || Database::userExists($username)) && !empty($username)) {
            $_SESSION['username-edit-error'] = "<- Selected username is not valid";
            return false;
        }
        return true;
    }

    public function validateFirstName($firstname){
        if(!FieldValidator::nameIsOfCorrectFormat($firstname) && !empty($firstname)) {
            $_SESSION['firstname-edit-error'] = "<- Selected first name is not valid";
            return false;
        }
        return true;
    }

    public function validateLastName($lastname){
        if(!FieldValidator::nameIsOfCorrectFormat($lastname) && !empty($lastname)) {
            $_SESSION['lastname-edit-error'] = "<- Selected last name is not valid";
            return false;
        }
        return true;
    }

    public function validateEmail($email){
        if(!FieldValidator::emailIsOfCorrectFormat($email) && !empty($email)) {
            $_SESSION['email-edit-error'] = "<- Selected email is not valid";
            return false;
        }
        return true;
    }

    private  function usernameIsOfCorrectFormat($username){
        return preg_match(FieldValidator::$namePattern, $username);
    }

    private  function nameIsOfCorrectFormat($name){
        return preg_match(FieldValidator::$namePattern, $name);
    }

    private  function emailIsOfCorrectFormat($email){
        return preg_match(FieldValidator::$emailPattern, $email);
    }
}