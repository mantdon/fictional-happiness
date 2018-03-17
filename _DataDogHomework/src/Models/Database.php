<?php
namespace src\Models;

include("src/Models/Connection.php");

class Database
{
    private $conn;

    public function __construct()
    {
        $this->conn = new Connection();
    }

    public function addUser($username, $password)
    {
        $query = "INSERT INTO users (username, password) VALUES ('$username', '$password')";

        makeRequest($query);
    }

    public function makeRequest($query)
    {
        return mysqli_query($this->conn->getConnection(), $query);
    }

    public function checkIfUserExists($username)
    {
        $query = "SELECT username FROM users WHERE username='$username'";

        $result = $this->makeRequest($query);

        return mysqli_num_rows($result) == 1 ? true : false;
    }

    public function verifyUser($username, $password)
    {
        $query = "SELECT username, password FROM users WHERE username='$username'";

        $result = $this->makeRequest($query);

        if(mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);
            return $this->verifyPassword($row['password'], $password) == true ? $row : null;
        }
        return null;

    }

    public function verifyPassword($db_password, $password)
    {
        return password_verify($password, $db_password) || $db_password === $password;
    }

}