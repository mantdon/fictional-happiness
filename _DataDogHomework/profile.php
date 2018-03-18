<?php
    include "src\Models\Database.php";
    include "src/Services/Authenticator.php";

    use src\Models\Database;
    use src\Services\Authenticator;

    session_start();

    $auth = new Authenticator();

    if ($auth->isAuthorized()){
        Database::connect();

        $currentUsername = $_SESSION['user']['username'];
        $currentUser = Database::query("SELECT first_name, last_name, email, member_since
                                               FROM profiles 
                                               WHERE user_id = '$currentUsername'");
        $currentUser = $currentUser->fetch_assoc();

        $firstname = $currentUser['first_name'] === NULL ? "Unset" : $currentUser['first_name'];
        $lastname = $currentUser['last_name'] === NULL ? "Unset" : $currentUser['last_name'];
        $email = $currentUser['email'] === NULL ? "Unset" : $currentUser['email'];
        $member_since = $currentUser['member_since'] === NULL ? "Unset" : $currentUser['member_since'];

        echo '<b>', $_SESSION['profile-updated-message'] ?? NULL, '</b>';
        unset($_SESSION['profile-updated-message']);
        echo '<h1>Profile</h1>
             <table>
                  <tr><td>Username:    </td><td>' . $currentUsername . '</td></tr>
                  <tr><td>First name:  </td><td>' . $firstname . '</td></tr>
                  <tr><td>Last name:   </td><td>' . $lastname . '</td></tr>
                  <tr><td>E-mail:      </td><td>' . $email . '</td></tr>
                  <tr><td>Member since:</td><td>' . $member_since . '</td></tr>
              </table><br>
              <a href="profile_edit.php">Edit profile</a><br><br>
              <a href="index.php">Home</a>';

    }else
        header("location: login.php");