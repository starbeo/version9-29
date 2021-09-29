<?php
class LivreurMethods{
 
    // database connection 
    private $conn;
 
    // object properties
    public $staffid;
    public $firstname;
    public $lastname;
    public $email;
    public $telephone;
    public $latitude;
    public $longitude;
    public $ville;
 
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    public function getInformationLivreur($id,$tocken){

        $query = "SELECT staffid,firstname,lastname,email,phonenumber as telephone,latitude,longitude,tblvilles.name as ville
        FROM `tblstaff`,tblvilles where staffid=? and password=?
        and tblvilles.id=tblstaff.city
                 LIMIT 0,1";
        // prepare query statement
        $stmt = $this->conn->prepare( $query );
     
        // bind id of product to be updated
       $stmt->bindParam(1,$id);
       $stmt->bindParam(2,$tocken);
     
        // execute query
        $stmt->execute();
     
        // get retrieved row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
     
        // set values to object properties
        $this->staffid = $row['staffid'];
        $this->firstname = $row['firstname'];
        $this->lastname = $row['lastname'];
        $this->email = $row['email'];
        $this->telephone = $row['telephone'];
        $this->latitude = $row['latitude'];
        $this->longitude = $row['longitude'];
        $this->ville = $row['ville'];
    }
}