<?php
class NotificationMethods{
 
    // database connection 
    private $conn;
 
    // object properties
    public $id;
    public $notification_id;
    public $isread;
    public $date;
    public $description;
    public $fromclientid;
    //Notification

 
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

// read GetStatusColisByCodeEnvoi
public function GetNotificationById($id){
 
    // select query
    $query = "SELECT tblnotificationscustomer.`id`,tblnotificationscustomer.`isread`,
    DATE_FORMAT(tblnotificationscustomer.`date`,'%d-%m-%Y') as date,tblnotificationscustomer.`description` 
    FROM `tblnotificationscustomer` 
    WHERE  toclientid= ?
       order by tblnotificationscustomer.id DESC LIMIT 20";
    // prepare query statementss
    $stmt = $this->conn->prepare( $query );

    // bind variable values
    $stmt->bindParam(1,$id);
    // execute query
    $stmt->execute();
 
    // return values from database
    return $stmt;
}

// read GetNotification
public function GetNotification($client_id,$tocken){
 
    // select query
    $query = "SELECT tblnotificationscustomer.`id`,tblnotificationscustomer.`isread`,
    DATE_FORMAT(tblnotificationscustomer.`date`,'%d-%m-%Y') as date,tblnotificationscustomer.`description` 
    FROM `tblnotificationscustomer` ,tblexpediteurs
    WHERE  toclientid= ?
    and tblexpediteurs.id=tblnotificationscustomer.toclientid
    and tblexpediteurs.password=?
       order by tblnotificationscustomer.id DESC LIMIT 20";
    // prepare query statementss
    $stmt = $this->conn->prepare( $query );

    // bind variable values
    $stmt->bindParam(1,$client_id);
    $stmt->bindParam(2,$tocken);
    // execute query
    $stmt->execute();
 
    // return values from database
    return $stmt;
}


// getNotifPopup
function getNotifPopup($client_id,$tocken){

    $query = "SELECT tblnotificationscustomer.`id` as notification_id ,tblnotificationscustomer.`isread`,
    DATE_FORMAT(tblnotificationscustomer.`date`,'%d-%m-%Y') as date,tblnotificationscustomer.`description` 
    FROM `tblnotificationscustomer` ,tblexpediteurs
    WHERE  toclientid= ?
    and tblexpediteurs.id=tblnotificationscustomer.toclientid
    and tblexpediteurs.password=?
    and tblnotificationscustomer.isread=0
       order by tblnotificationscustomer.id desc
             LIMIT 0, 1";
    // prepare query statement
    $stmt = $this->conn->prepare( $query );
 
    // bind id of product to be updated
   $stmt->bindParam(1,$client_id);
   $stmt->bindParam(2,$tocken);
 
    // execute query
    $stmt->execute();
 
    // get retrieved row
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
 
    // set values to object properties
    $this->notification_id = $row['notification_id'];
    $this->isread = $row['isread'];
    $this->date = $row['date'];
    $this->description = $row['description'];
}






}