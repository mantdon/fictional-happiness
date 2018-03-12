<?php

include "src/Models/User.php";
include "src/Models/Database.php";
include "src/Services/Auth.php";

use src\Models\User;
use src\Models\Database;
use src\Services\Auth;

session_start();

$database = new Database();

$testUser = new User();
$database->addUser($testUser->setUsername('test')->setPassword('pass'));

$testUser = new User();
$database->addUser($testUser->setUsername('useris')->setPassword('secret'));

$auth = new Auth($database);

$username = $_POST['username'] ?? null;
$password = $_POST['password'] ?? null;

try {
    if (isset($username) && isset($password))
        $auth->connect($username, $password);
}
catch (Exception $exception)
{
    echo $exception->getMessage();
}

if ($auth->isAuthorized()) {
    echo 'logged in <br>';
    echo '<a href="logout.php">logout</a>';
    return;
}

echo '<form method="post" action="index.php">
        username: <input type="text" name="username">
        password: <input type="password" name="password">
        <input type="submit" value="Submit">
        </form> ';
