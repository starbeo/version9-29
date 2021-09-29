<?php
class StatutsMethods{
 
    // database connection 
    private $conn;
 
    // object properties
    public $code_envoi;
    public $statut;
    public $emplacement;
    public $color;
    public $date_reporte;
    public $date_created;
    //Notification
    

 
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

// read GetStatusColisByCodeEnvoi
public function GetStatusColisByCodeEnvoi($id){
 
    // select query
    $query = "SELECT  `tblstatus`.`code_barre` as 'code_envoi', `tblstatuscolis`.`name` as 'statut',
             `tbllocations`.`name` as 'emplacement', tblstatuscolis.`color`,DATE_FORMAT(date_reporte,'%d-%m-%Y') as date_reporte,DATE_FORMAT(tblstatus.date_created,'%d-%m-%Y') as date_created
               FROM `tblstatus`,tblstatuscolis,tbllocations,tblcolis,tblexpediteurs 
               WHERE tblstatus.type=tblstatuscolis.id AND tbllocations.id=tblstatus.emplacement_id 
               AND tblcolis.code_barre=tblstatus.code_barre AND tblexpediteurs.id=tblcolis.id_expediteur 
               AND tblcolis.code_barre = ? 
               order by tblstatus.id DESC";
    // prepare query statementss
    $stmt = $this->conn->prepare( $query );

    // bind variable values
    $stmt->bindParam(1,$id);
    // execute query
    $stmt->execute();
 
    // return values from database
    return $stmt;
}
}