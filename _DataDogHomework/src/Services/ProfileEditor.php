<?php

namespace src\Services;

include("src/Services/FieldValidator.php");
use src\Models\Database;

class ProfileEditor{
    private $currentUsername;
    private $editedUsername;
    private $editedFirstName;
    private $editedLastName;
    private $editedEmail;
    private $fieldValidator;

    public function __construct($postData, $currentUsername){
        $this->currentUsername = $currentUsername;
        if(!empty($postData))
            $this->parsePostData($postData);
        $this->fieldValidator = new FieldValidator();
        Database::connect();
    }

    public function updateProfile(){
        if(!$this->allFieldsAreValid())
            return;
        $this->updateUsername();
        $this->updateFirstName();
        $this->updateLastName();
        $this->updateEmail();
    }

    public function parsePostData($postData){
        $this->editedUsername  =  $postData['edited-username'];
        $this->editedFirstName =  $postData['edited-firstname'];
        $this->editedLastName  =  $postData['edited-lastname'];
        $this->editedEmail     =  $postData['edited-email'];
    }

    private function allFieldsAreValid(){
        $this->fieldValidator->validateUsername($this->editedUsername);
        $this->fieldValidator->validateFirstName($this->editedFirstName);
        $this->fieldValidator->validateLastName($this->editedLastName);
        $this->fieldValidator->validateEmail($this->editedEmail);
        return !$this->sessionContainsEditErrors();
    }

    public function sessionContainsEditErrors(){
        return isset($_SESSION['username-edit-error']) ||
            isset($_SESSION['firstname-edit-error'])||
            isset($_SESSION['lastname-edit-error']) ||
            isset($_SESSION['email-edit-error']);
    }

    public function updateUsername(){
        if(empty($this->editedUsername))
            return;
        $usernameUpdateQuery = "UPDATE users
                                SET username = '$this->editedUsername' 
                                WHERE username = BINARY '$this->currentUsername'";
        Database::query($usernameUpdateQuery);
        $this->currentUsername = $this->editedUsername;
        # Temporary workaround to not require relogging.
        $_SESSION['user']['username'] = $this->currentUsername;
    }

    public function updateFirstName(){
        if(empty($this->editedFirstName))
            return;
        $firstNameUpdateQuery = "UPDATE profiles 
                                 SET first_name = '$this->editedFirstName' 
                                 WHERE BINARY user_id = BINARY '$this->currentUsername'";
        Database::query($firstNameUpdateQuery);
    }

    public function updateLastName(){
        if(empty($this->editedLastName))
            return;
        $lastNameUpdateQuery = "UPDATE profiles 
                                SET last_name = '$this->editedLastName' 
                                WHERE user_id = BINARY '$this->currentUsername'";
        Database::query($lastNameUpdateQuery);
    }

    public function updateEmail(){
        if(empty($this->editedEmail))
            return;
        $emailUpdateQuery = "UPDATE profiles 
                             SET email = '$this->editedEmail' 
                             WHERE user_id = BINARY '$this->currentUsername'";
        Database::query($emailUpdateQuery);
    }

    public function clearErrorFromSession(){
        unset($_SESSION['username-edit-error']);
        unset($_SESSION['firstname-edit-error']);
        unset($_SESSION['lastname-edit-error']);
        unset($_SESSION['email-edit-error']);
    }
}