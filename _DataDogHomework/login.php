<?php
    include "src/Models/Database.php";
    include "src/Services/Authenticator.php";

    use src\Services\Authenticator;

    session_start();

    $auth = new Authenticator();

    $username = $_POST['username'] ?? null;
    $password = $_POST['password'] ?? null;

    try {
        if (isset($username) && isset($password) && $auth->authenticate($username, $password))
                header('location: index.php');
    }
    catch (Exception $exception)
    {
        echo $exception->getMessage();
    }

     echo '<h1>Login</h1>
          <form method="post" action="login.php">
            <label for="login-username">Username:</label>
            <input type="text" id="login-username" name="username" placeholder="username"><br>
            <br>
            <label for="login-password">Password:</label>
            <input type="password" id="login-password" name="password" placeholder="password"><br>
            <br>
            <input type="submit" value="Log in"><br><br>
         </form> 
         <a href="register.php">Register</a>';
