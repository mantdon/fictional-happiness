<?php
    include "src/Services/Authenticator.php";
    include "src/Models/Database.php";

    use src\Services\Authenticator;

    session_start();

    $auth = new Authenticator();

    if ($auth->isAuthorized()) {
        echo '<h1>Hello, ' . $_SESSION['user']['username'] .'!</h1>
              <a href="profile.php">Profile</a><br><br>
              <a href="logout.php">Logout</a>';
        return;
    }else
        echo '<a href="login.php">Log in</a>
              <a href="register.php">Register</a><br><br>';


