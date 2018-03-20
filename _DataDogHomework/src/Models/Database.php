<?php
namespace src\Models;
include 'src/Assets/StringGenerator.php';
include 'src/Models/MySQLConnection.php';
use src\Assets\StringGenerator;

class Database
{
    private static $mySqlConnection;

    public static function connect($server = "localhost", $username = "root", $password = "", $database = "login")
    {
        Database::$mySqlConnection = new MySQLConnection($server, $username, $password, $database);
        # Shouldn't be here, leaving for convenience
        # since database might need frequent modifications.
        Database::setUpTables();
    }

    private static function setUpTables(){
        Database::createUsersTable();
        Database::createProfilesTable();
    }

    public static function query($query){
        return Database::$mySqlConnection->queryDB($query);
    }

    private static function createUsersTable()
    {
        $query = "CREATE TABLE IF NOT EXISTS users (
                  username VARCHAR(255) NOT NULL,
                  password VARCHAR(255) NOT NULL,
                  PRIMARY KEY (username)
                 )";
        Database::query($query);
    }

    private static function createProfilesTable(){
        $query = "CREATE TABLE IF NOT EXISTS profiles(
                  id VARCHAR (255),
                  user_id VARCHAR (255),
                  first_name VARCHAR (255),
                  last_name VARCHAR (255),
                  email VARCHAR (255),
                  member_since DATE,
                  PRIMARY KEY (id),
                  CONSTRAINT has FOREIGN KEY (user_id) REFERENCES users (username) ON UPDATE CASCADE
                  )";
        Database::query($query);
    }
    
    public static function addUser($username, $password)
    {
        $query = "INSERT INTO users (username, password)
                  VALUES ('$username', '$password')";

        Database::query($query);
    }

    public static function linkProfileToUser($username){
        $id = StringGenerator::generateRandomString();
        $today = date("Y/m/d");
        $query = "INSERT INTO profiles (id,
                                        user_id,
                                        first_name,
                                        last_name,
                                        email,
                                        member_since)
                  VALUES ('$id', '$username',NULL ,NULL ,NULL, '$today')";
        Database::query($query);

    }

    public static function userExists($username)
    {
        $query = "SELECT username FROM users 
                  WHERE username= BINARY '$username'";

        $result = Database::query($query);

        return mysqli_num_rows($result) == 1 ? true : false;
    }
}