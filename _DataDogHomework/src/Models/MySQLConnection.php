<?php
namespace src\Models;

# For use in Database.php only
class MySQLConnection
{
    private $mySqlConnection;

    public function __construct($server = "localhost", $username = "root", $password = "", $database = "login")
    {
        // Create a connection
        $this->mySqlConnection = mysqli_connect($server, $username, $password, $database);

        // Check connection
        if(!$this->mySqlConnection) {
            die("Connection failed: " . mysqli_connect_error());
        }
    }

    public function queryDB($query){
        return mysqli_query($this->mySqlConnection, $query);
    }

}

