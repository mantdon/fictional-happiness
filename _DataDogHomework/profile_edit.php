<?php
    include "src/Models/Database.php";
    include "src/Services/ProfileEditor.php";
    include "src/Services/Authenticator.php";

    use src\Services\Authenticator;
    use src\Services\ProfileEditor;

    session_start();

    $auth = new Authenticator();

    if ($auth->isAuthorized()) {

        $currentUsername = $_SESSION['user']['username'];
        $profileEditor = new ProfileEditor($_POST, $currentUsername);

        $profileEditor->updateProfile();

        if (!$profileEditor->sessionContainsEditErrors() && !empty($_POST)) {
            $_SESSION['profile-updated-message'] = "Profile updated!";
            $profileEditor->clearErrorFromSession();
            header("location: profile.php");
        } else {
            include("Forms/profile_edit.html");
            $profileEditor->clearErrorFromSession();
        }
    }else
        header("location: login.php");
