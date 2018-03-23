<?php
    include "src/Models/Database.php";
    include "src/Services/RegistrationHandler.php";

    use src\Models\Database;
    use src\Services\RegistrationHandler;

    if(isset($_POST['submit_button']))
    {
        $registration = new RegistrationHandler(new Database());
        if($registration->confirmRegistration())
        {
            header('location: index.php');
        }
    }

    echo '
        <h1>Registration form</h1>
        <form method="post" action="#">
                <label for="register-username">Username:</label>
                <input type="text" id="register-username" name="username" placeholder="username"><br>
                <br>
                <label for="register-password">Password:</label>
                <input type="password" id="register-password" name="password" placeholder="password"><br>
                <br>
                <label for="register-password1">Confirm your password:</label>
                <input type="password" id="register-password1" name="password1" placeholder="password"><br>
                <br>
                <input type="submit" name="submit_button" value="Register">
         </form> 
         <a href="login.php">Log in</a>';
