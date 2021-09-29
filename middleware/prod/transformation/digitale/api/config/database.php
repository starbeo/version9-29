<?php
class Database{
 

    private $host = "161.35.115.15";
    private $db_name = "myColisprod";
    private $username = "mycolisprod";
    private $password = "O26T70egWQ2ULxuE";
    public $conn;
 
    // get the database connection
    public function getConnection(){
 
        $this->conn = null;
 
        try{
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        }catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        }
 
        return $this->conn;
    }
}
?>
