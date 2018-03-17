<?php
namespace src\Models;

class Connection
{
    private $server = "localhost";
    private $username = "root";
    private $password = "";
    private $db = "login";
    private $conn;

    public function __construct()
    {
        // Create a connection
        $this->conn = mysqli_connect($this->server, $this->username, $this->password, $this->db);

        // Check connection
        if(!$this->conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
    }
    public function getConnection()
    {
        return $this->conn;
    }

}

