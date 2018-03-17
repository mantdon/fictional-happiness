<?php
    include "src/Models/Database.php";
    include "src/Services/Auth.php";

    use src\Models\Database;
    use src\Services\Auth;

    session_start();

    $auth = new Auth(new Database());

    $username = $_POST['username'] ?? null;
    $password = $_POST['password'] ?? null;

    try {
        if (isset($username) && isset($password) && $auth->connect($username, $password))
                header('location: index.php');
    }
    catch (Exception $exception)
    {
        echo $exception->getMessage();
    }

    echo '
    <h1>Login</h1>
    <form method="post" action="login.php">
            <label for="login-username">Username:</label>
            <input type="text" id="login-username" name="username" placeholder="username"><br>
            <br>
            <label for="login-password">Password:</label>
            <input type="password" id="login-password" name="password" placeholder="password"><br>
            <br>
            <input type="submit" value="Submit">
     </form> ';
