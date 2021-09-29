<?php
class NotificationMethods{
 
    // database connection 
    private $conn;
 
    // object properties
    public $id;
    public $isread;
    public $date;
    public $description;
    public $fromclientid;
    //Notification

 
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }



// read GetNotification
public function GetNotification($livreur_id,$tocken){
 
    // select query
    $query = "SELECT tblnotifications.id,tblnotifications.isread,DATE_FORMAT(tblnotifications.date,'%d-%m-%Y') as date,tblnotifications.description FROM tblnotifications,tblstaff
    where tblnotifications.touserid=?
    and tblnotifications.touserid=tblstaff.staffid
    AND tblstaff.password=?
     order by tblnotifications.id DESC LIMIT 20 ";
    // prepare query statementss
    $stmt = $this->conn->prepare( $query );

    // bind variable values
    $stmt->bindParam(1,$livreur_id);
    $stmt->bindParam(2,$tocken);
    // execute query
    $stmt->execute();
 
    // return values from database
    return $stmt;
}



}