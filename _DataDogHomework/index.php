<?php

    include "src/Services/Auth.php";
    include "src/Models/Database.php";

    use src\Services\Auth;
    use src\Models\Database;

    session_start();

    $auth = new Auth(new Database());

    if ($auth->isAuthorized()) {
        echo '<h1>Hello, ' . $_SESSION['user']['username'] .'!</h1>';
        echo '<a href="logout.php"><button>Logout</button></a>';
        return;
    } else {
        echo '
            <a href="login.php"><button>Login</button></a>
            <a href="register.php"><button>Register</button></a>
        ';
    }

