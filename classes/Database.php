<?php

class Database
{
  private $server_name = "localhost";
  private $username = "root";
  private $password = "root"; // if windows OS for XAMPP users ""
  private $db_name = "the_company";
  protected $conn;

  public function __construct()
  {
    $this->conn = new mysqli($this->server_name, $this->username, $this->password, $this->db_name);
    // msqli = Represents a connection between PHP and a MySQL database.
    // $this-conn is now the object the class msqli
    // $this->conn holds the connection to the DB

    if ($this->conn->connect_error) {
      die("unable to connect to the database: " . $this->conn->connect_error);
    }
  }
}

?>
